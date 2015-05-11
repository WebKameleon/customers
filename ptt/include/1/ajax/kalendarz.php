<?php
    require_once __DIR__.'/../system/crypt.php';
    require_once __DIR__.'/../system/calendar.php';
    
    $array=[];
    if (isset($_GET['id']) && $_GET['id'])
    {
        $ics=decrypt($_GET['id']);
        $ical=new calendar();
        
        $month = isset($_GET['month']) ? $_GET['month'] : date('n');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $next_month=$month+1;
        $next_year=$year;
        if ($next_month==13) {
            $next_month=1;
            $next_year++;
        }
        
        $events = $ical->events($ics,strtotime("01-$month-$year"),strtotime("01-$next_month-$next_year")-1);

        
        foreach ($events AS $event)
        {
 
            $array[]=[
                date('j/n/Y',$event['start']),
                stripslashes($event['SUMMARY']).date(' H:i',$event['start']),
                $_GET['next']?:null,
                'red'
            ];

        }
        
        /*
        $array = array(
          array(
            "7/$month/$year", 
            'bootstrap logo popover!', 
            '#', 
            '#51a351', 
            '<img src="http://bit.ly/XRpKAE" />'
          ),
          array(
            "17/$month/$year", 
            'octocat!', 
            'https://github.com/logos', 
            'blue', 
            'new github logo <img src="http://git.io/Xmayvg" />'
          ),
          array(
            "27/$month/$year", 
            'github drinkup', 
            'https://github.com/blog/category/drinkup', 
            'red'
          )
        );
        $array = array(
          array(
            "22/$month/$year", 
            'github drinkup', 
            'https://github.com/blog/category/drinkup', 
            'red'
          ),
          array(
            "11/$month/$year", 
            'github drinkup', 
            'https://github.com/blog/category/drinkup', 
            'red'
          ),
          array(
            "5/$month/$year", 
            'github drinkup', 
            'https://github.com/blog/category/drinkup', 
            'red'
          )
        );
        */
        
    }
    
    
    
header('Content-Type: application/json; charset=utf8');
die(json_encode($array));
