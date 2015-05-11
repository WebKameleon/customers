<?php

    require_once __DIR__.'/../system/fun.php';
    
    $obj='objects';
    if (isset($_GET['obj']) && $_GET['obj']) $obj=$_GET['obj'];
    $objects=WBP::get_file_db($obj);
    
    $id=0;
    if (isset($_GET['id'])) $id=$_GET['id'];

    $ret=array();
    if (isset($objects[$id]))
    {
        $ret=$objects[$id];
    }
    
    if (isset($_GET['debug']))
    {
        header('Content-type: text/plain; charset=utf8');
        die(print_r($ret,1));
    }
    header('Content-type: application/json; charset=utf8');
    echo json_encode($ret);