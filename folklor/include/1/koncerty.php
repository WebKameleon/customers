<?php
	require_once __DIR__.'/system/calendar.php';

	
	$ics=$costxt;
	
	if (!$ics) return;
	
	$months=[
		 'pl'=>['styczeń','luty','marzec','kwiecień','maj','czerwiec','lipiec','sierpień','wrzesień','październik','listopad','grudzień'],
		 'en'=>['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
		];	
	
	$ical=new calendar();
	if (isset($_GET['date'])) {
		$events=$ical->events($ics,strtotime($_GET['date']));
		$display_date=date('d-m-Y',strtotime($_GET['date']));
	}
	else {
		$year=date('Y');
		$month=date('m');
        $next_month=$month+1;
        $next_year=$year;
        if ($next_month==13) {
            $next_month=1;
            $next_year++;
        }
		$events=$ical->events($ics,strtotime("01-$month-$year"),strtotime("01-$next_month-$next_year")-1);
	
		$display_date=$months[$lang][$month-1].' '.$year;	
	}
	
        
	$months=[
		 'pl'=>['stycznia','lutego','marca','kwietnia','maja','czerwca','lipca','sierpnia','września','października','listopada','grudnia'],
		 'en'=>['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
		];
        
	
	$events2=[];
	foreach($events AS $e)
	{
		$t=$e['start'];
		while (isset($events2[$t])) $t++;
		$events2[$t]=$e;
	}
	$events=$events2;
	//krsort($events); //desc order
    ksort($events); //asc order
	
	
	foreach ($events AS &$event)
	{

		$place=$address=$event['LOCATION'];
		$event['place']=$place;	
 
		$start=strtotime($event['DTSTART']);
		
		$event['date']=date('j',$start).' '.$months[$lang][date('n',$start)-1];
		$event['time']=date('H:i',$start);
		
		if ($event['time']=='00:00' || $event['time']=='01:00') $event['time']='';

		
    }
	

	//mydie($events);
        
	
