<?php
    require_once __DIR__.'/../system/crypt.php';
    require_once __DIR__.'/../system/cache.php';
    require_once __DIR__.'/../system/calendar.php';
    

    if (isset($_GET['id']) && $_GET['id'])
    {

        
        $month = isset($_GET['month']) ? $_GET['month'] : date('n');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $next_month=$month+1;
        $next_year=$year;
        if ($next_month==13) {
            $next_month=1;
            $next_year++;
        }
        
        $ics=decrypt($_GET['id']);
        
        $key='cal:'.md5($ics."-$month-$year");
        
        $a=folklor_cache($key);
        
        if ($a) {
            $array=$a;
            
        } else {
                        
            $ical=new calendar();    
            
            $events = $ical->events($ics,strtotime("01-$month-$year"),strtotime("01-$next_month-$next_year")-1);
    
            $events2=[];
            foreach ($events AS $event)
            {
                $date=date('Y-m-d',$event['start']);
                
                if (isset($events2[$date])) {
                    $events2[$date][1].="\n\n".stripslashes($event['SUMMARY']);
                    
                } else {
                    $events2[$date]=[
                        date('j/n/Y',$event['start']),
                        stripslashes($event['SUMMARY']),
                        $_GET['next']?$_GET['next'].'?date='.$date:null,
                        'red'
                    ];                
                }
    
            }
    
            foreach ($events2 AS $e) $array[]=$e;
            folklor_cache($key,$array);
        }
    }
    


    
header('Content-Type: application/json; charset=utf8');
die(json_encode($array));
