<?php

    $redirect='';
    $prefix='beta';
    $desired_host='';
    $lang_prefixes=array('en'=>'vidella.eu','ru'=>'vidella.ru','de'=>'vidella.ru');
    $redirects=array('vidella.cz'=>'vidella.eu','vidella.pl'=>'vidella.com');
    
    foreach($redirects AS $co=>$naco)
    {
        if ($host==$prefix.'.'.$co)
        {
            $desired_host=$prefix.'.'.$naco;
        }
    }
    
    foreach($lang_prefixes AS $l=>$d)
    {
        if (substr($_SERVER['REQUEST_URI'],0,3)==$l.'/')
        {
            $desired_host=$prefix.'.'.$d;
            break;
        }
    }
    if (!$desired_host) $desired_host=$prefix.'.vidella.com';
    
    
    if ($desired_host != $host) $redirect='http://'.$desired_host.$_SERVER['REQUEST_URI'];