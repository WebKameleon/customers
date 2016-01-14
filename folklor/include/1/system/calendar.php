<?php
require_once __DIR__.'/class.iCalReader.php';

date_default_timezone_set ('Europe/Warsaw');

class calendar {
    protected $ical;

    
    protected function strtotime($t,$tz=null)
    {
        if (!$tz || substr($t,-1)=='Z') return strtotime($t);
        $date=new DateTime($t, new DateTimeZone($tz));
        return $date->getTimestamp();
     
    }

    protected function timetostr($t)
    {
        return date('Y-m-d H:i',$t);
    }
    
    protected function processEvent($event,$start,$end,$update_allowed=true)
    {
        
        
        $e=$this->event()->find_one_by_ical_id($event['UID']);
        if ($e) {
            if (!$e['active'] && $update_allowed) {
                $this->data=[
                    'name'=>$event['SUMMARY'],
                    'event_start'=>$this->timetostr($start),
                    'event_end'=>$this->timetostr($end),
                    'about'=>$event['DESCRIPTION'],
                ];
                $this->id=$this->event()->id;
                $this->put();
            }
            
        } else {
            $this->data=[
                'name'=>$event['SUMMARY'],
                'event_start'=>$this->timetostr($start),
                'event_end'=>$this->timetostr($end),
                'about'=>$event['DESCRIPTION'],
                'ical_id'=>$event['UID']
            ];
            $this->post();
        }
        return $this->event()->data();
    }
    
    public function events($ics,$d_start,$d_end=null)
    {
        if (is_null($d_end)) $d_end=$d_start+3600*24;
        
        $key='ev-'.md5($ics);
        $events=false;
        if (function_exists('calendar_cache')) $events=calendar_cache($key);
        if (!$events) {
            $this->ical = new ICal($ics);
            $events = $this->ical->events();
            if (function_exists('calendar_cache')) calendar_cache($key,$events);
        }
        $result=[];
        if (!is_array($events)) return $result;


        foreach ($events AS &$event)
        {
            $tz=null;
            foreach (array_keys($event) AS $k)
            {
                if ($pos=strpos($k,'TZID='))
                {
                    $tz=substr($k,$pos+5);
                    if ($pos=strpos($tz,';')) $tz=substr($tz,0,$pos);
                    break;
                }
            }
            $start=$this->strtotime($event['DTSTART'],$tz);
            $end=isset($event['DTEND'])?$this->strtotime($event['DTEND'],$tz):$start;
            $duration=$end-$start;
            
            
            if (isset($event['RRULE'])) {
                $dates=$this->parseRrule($start,$event['RRULE'],$tz);
    
                if (is_array($dates)) foreach ($dates AS $start)
                {
                    if ($start<$d_end && $start>=$d_start) {
                        $event['start']=$start;
                        $event['end']=$start+$duration;
                        $result[]=$event;
                    }
                }
                
            } else {
                if ($start<$d_end && $start>=$d_start) {
                    $event['start']=$start;
                    $event['end']=$start+$duration;
                    $result[]=$event;
                }
            }
            

        }
        
        foreach ($result AS &$e)
        {
            foreach (array('SUMMARY','LOCATION','DESCRIPTION') AS $f)
            {
                $e[$f]=str_replace('\n',"\n",$e[$f]);
                $e[$f]=stripslashes($e[$f]);
            }
        
        }
        
        return $result;
        
    }
    
    
    protected function parseRrule($start,$rule,$tz)
    {
        $params = explode(';', $rule);
        $data=['interval'=>1,'count'=>9999999];
        foreach ($params as $param) {
            list($name, $value) = explode('=', $param);
            switch ($name) {
                case 'UNTIL':
                    $data['until'] = $this->strtotime($value,$tz);
                    break;
                case 'FREQ':
                    $data['freq'] = $this->translateFrequency($value);
                    break;
                case 'INTERVAL':
                    $data['interval'] = $value;
                    break;
                case 'COUNT':
                    $data['count'] = intval($value);
                    break;
                case 'WKST':
                    //$data['wkst'] = $data['translateWeekday($value);
                    break;
                case 'BYSECOND':
                    $data['bysecond'] = explode(',', $value);
                    break;
                case 'BYMINUTE':
                    $data['byminute'] = explode(',', $value);
                    break;
                case 'BYHOUR':
                    $data['byhour'] = explode(',', $value);
                    break;
                case 'BYDAY':
                    $data['byday'] = $this->translateDay(explode(',', $value));
                    break;
                case 'BYMONTHDAY':
                    $data['bymonthday'] = explode(',', $value);
                    break;
                case 'BYYEARDAY':
                    $data['byyearday'] = explode(',', $value);
                    break;
                case 'BYWEEKNO':
                    $data['byweekno'] = explode(',', $value);
                    break;
                case 'BYMONTH':
                    $data['bymonth'] = explode(',', $value);
                    break;
                case 'BYSETPOS':
                    $data['bysetpos'] = explode(',', $value);
                    break;
            }
        }
        $dates=[];
        $ts=$start;
        $future=time()+365*24*3600;
        if (isset($data['until']) && $data['until']<$future) $future=$data['until'];

        while ($ts<$future)
        {
            if (isset($data['byday']) && is_array($data['byday'])) {
                $ts_dow=date('w',$ts);
                foreach($data['byday'] AS $dow)
                {
                    $delta=$dow-$ts_dow;
                    if ($delta) {
                        if ($delta>0) $delta="+$delta";
                        $ts2=strtotime("$delta day",$ts);
                        if ($ts2<=$future && $ts2>$start) $dates[]=$ts2;
                    } else {
                        if ($ts<=$future) $dates[]=$ts;
                    }
                    if (count($dates)>=$data['count']) break 2;
                }
            } else {
                $dates[]=$ts;
                if (count($dates)>=$data['count']) break;
            }
            
            $plus='+'.$data['interval'].' '.$data['freq'];
            $ts=strtotime($plus,$ts);            
            
            
        }
        
        sort($dates);

        return $dates;
    }
    
    protected function translateDay($value) {
        if (is_array($value)) {
            $days=[];
            foreach ($value AS $day) $days[]=$this->translateDay($day);
            return $days;
        }
        $days=['SU','MO','TU','WE','TH','FR','SA'];
        return array_search($value,$days);
    }

    protected function translateFrequency($value) {
        switch ($value) {
            case 'YEARLY': return 'year'; break;
            case 'MONTHLY': return 'month'; break;
            case 'WEEKLY': return 'week'; break;
            case 'DAILY': return 'day'; break;
            case 'HOURLY': return 'hour'; break;
            case 'MINUTELY': return 'minute'; break;
            case 'SECONDLY': return 'second'; break;
        }
    }
    
    
    protected function error($id=0,$ctx=null)
    {
        require_once __DIR__.'/../../rest/class/Error.php';
        $result['error']=Error::e($id);
        if (!is_null($ctx)) $result['ctx']=$ctx;
        
        echo 'Error: '.json_encode($result).'<br/>';
        Tools::log('calendar',$result);
        $this->userstatus=false;
    }
    
}



