<?php

    $get=array();
    foreach($_GET AS $k=>$v) $get[]=urlencode($k).'='.urlencode($v);

    $url='http://maps.googleapis.com/maps/api/geocode/json?'.implode('&',$get);
    
    Header('Content-type: application/json');
    readfile($url);