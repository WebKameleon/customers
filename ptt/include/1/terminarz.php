<?php
	require_once __DIR__.'/system/calendar.php';
	
	$webtd=new webtdModel($sid);
	$adr=json_decode($webtd->xml,1);
	

	$ics=$costxt;
	$month=$year='';
        if ($cos)
        {
                $month=$cos%100;
                $year=2000+floor($cos/100);
        }
	if (!$ics || !$month || !$year) return;

        $ical=new calendar();
        
        $next_month=$month+1;
        $next_year=$year;
        if ($next_month==13) {
            $next_month=1;
            $next_year++;
        }
        
        $events = $ical->events($ics,strtotime("01-$month-$year"),strtotime("01-$next_month-$next_year")-1);
	$months=[
		 'pl'=>['stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia'],
		 'en'=>['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
		];
        
        foreach ($events AS &$event)
        {
		$pos=strpos($event['LOCATION'],'(');
		if ($pos)
		{
			$address=substr($event['LOCATION'],0,$pos);
			$place=substr($event['LOCATION'],$pos+1);
			$pos=strpos($place,')');
			if ($pos) $place=substr($place,0,$pos);
		}
		else
		{
			$place=$address=$event['LOCATION'];
		}
		
		$event['place']=$place;	
 
		$start=strtotime($event['DTSTART']);
		
		$event['date']=date('j',$start).' '.$months[$lang][date('n',$start)-1];
		$event['time']=date('H:i',$start);
		
		$description=explode("\n",trim($event['DESCRIPTION']));
		$ticket=end($description);
		
		$matches=[];
		if(preg_match_all('/"[^ "][^"]*"/',$event['SUMMARY'],$matches))
		{
	
			for ($i=0;$i<count($matches[0]);$i++)
			{
				if (isset($description[$i]) && $description[$i]+0>0)
				{
					$link=Bootstrap::$main->tokens->page_href($description[$i]);
					$event['SUMMARY']=str_replace($matches[0][$i],
						'<a href="'.$link.'">'.$matches[0][$i].'</a>',
						$event['SUMMARY']);
				}
			}
				
		}
		$event['ticket']='';
		if ($ticket+0==0 && strlen($ticket)) $event['ticket']=$ticket;
		
		
		$md5=md5($address);

		if (!isset($adr[$md5]))
		{
			$url='http://maps.google.com/maps/api/geocode/json?address='.urlencode("$address").'&sensor=false';
			$data=json_decode(file_get_contents($url),true);
			if (isset($data['results'][0])) $adr[$md5]=$data['results'][0];
		}
		
		if (isset($adr[$md5]))
		{
			$address_components=$adr[$md5]['address_components'];
			$country=end($address_components);
			$event['country']=$country['short_name'];
			$event['country_long']=$country['long_name'];
			
			foreach($address_components AS $ac)
			{
				if (in_array('locality',$ac['types']))
				{
					$event['city']=$ac['long_name'];
				}
			}
			$geo=$adr[$md5]['geometry']['location'];
			$event['map']='https://www.google.pl/maps/place/'.urlencode($adr[$md5]['formatted_address']).'/@'.$geo['lat'].','.$geo['lng'].',14z';
		}

        }
	
	$webtd->xml=json_encode($adr);
	$webtd->save();
        
	include(__DIR__.'/terminarz.html');
