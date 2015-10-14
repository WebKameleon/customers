<?php

    $data=json_decode(file_get_contents(__DIR__.'/../system/wyprawy.json'),true);
    
    $struct=[];
    $countries=[];
    
    foreach($data['struct'] AS $cont=>$c)
    {
        ksort($c);
        $countries=array_merge($countries,$c);
        $struct[$cont]=array('name'=>$cont,'countries'=>array_keys($c));
        
    }
    ksort($struct, SORT_LOCALE_STRING);
    ksort($countries, SORT_LOCALE_STRING);
    
    $out=array('struct'=>$struct,'countries'=>array_keys($countries));
    
    if (isset($_GET['debug'])) mydie($out);
    
    header('Content-type: application/json; charset=utf8');
    die(json_encode($out,JSON_NUMERIC_CHECK));
