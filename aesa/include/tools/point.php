<?php
    function get ($d,$a){
        foreach ($a AS $k) if (isset($d[$k])) return $d[$k];
    }
    $pointTypes = [ "oua", "ppo", "spo", "road", "parking", "hotel", "wc", "restaurant", "gas", "cafe" ];

    
    $data=json_decode(file_get_contents($argv[1]),1);
    
    foreach($data AS $d) 
    {
        $rec=[];
        
        $rec['name']=get($d,['namePl','name']);
        $cords=get($d,['coords','cordinates']);
        $rec['lat']=$cords[0];
        $rec['lng']=$cords[1];
        $desc=get($d,['description']);
        $rec['pl']=get($d,['contentPl']);
        $rec['en']=get($d,['contentEn']);
        if (!$rec['pl'] && is_array($desc)) {
            $rec['pl']=$desc[0];
            $rec['en']=$desc[1];
        }
        foreach ($pointTypes AS $t) $rec[$t]=false;
        $pattern=get($d,['pattern']);
        if (is_array($patern)) {
            foreach ($patern AS $k=>$b) if ($b) $rec[$k]=true;
        }
    }
    
    print_r($rec);