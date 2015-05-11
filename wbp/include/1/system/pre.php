<?php
    require_once __DIR__.'/fun.php';

    $dotpay_id='67969';
    $paypal_id='foto@wbp.poznan.pl';
    $jotform_key='a74f1e494798fc82a94e96546ced5433';
    $jotform_subkey='14e3db9182026c6fa7a58c947263ad2e';
    
    $jotform_user='wbp';
    $jotform_pass='kaktusy';
    
    
    if (!isset($lang) && isset($session)) $lang=$session['lang'];

    if(isset($KAMELEON_MODE)) $_SERVER['dbh']=Bootstrap::$main->getConn()->getDbh();

    $dbh=&$_SERVER['dbh']; 

    if (isset($KAMELEON_MODE) && $KAMELEON_MODE && Bootstrap::$main->session('editmode')>1) echo '<div class="k_td_conf">';


    $contest_categories=array('events'=>'Wydarzenia','human_passion'=>'Człowiek i jego pasje','life'=>'Życie codzienne','sport'=>'Sport','environment'=>'Przyroda i ekeologia');
    
