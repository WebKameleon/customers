<?php

    //mail('piotr@gammanet.pl','dotpay',print_r(array('req'=>$_REQUEST,'ser'=>$_SERVER),1));
    
    header('Content-Type: text/plain; charset=utf-8');
    
    if (!isset($_REQUEST['id']) || !isset($_REQUEST['t_status']) || !isset($_REQUEST['t_date']) || !isset($_REQUEST['orginal_amount'])) return;
    
    @session_start();
    
    require_once __DIR__.'/../kameleon/Google.php';
    require_once __DIR__.'/../kameleon/Spreadsheet.php';
    include_once __DIR__.'/../system/fun.php';
    
    if (in_array($_SERVER['REMOTE_ADDR'],array('195.150.9.37')) && $_REQUEST['t_status']==2 && strlen($_REQUEST['id'])==64)
    {
        $sid=substr($_REQUEST['id'],0,32);
        $id=substr($_REQUEST['id'],32);

        
        WBP::contest_paied($sid,$id,$_REQUEST['t_date'],$_REQUEST['orginal_amount']);

    }

    
    die('OK');