<?php
    include_once __DIR__.'/../system/fun.php';
    
    $COOKIENAME= 'fcontest';
    
    function contest_ret($resp)
    {
        header('Content-type: application/json; charset=utf8');
        die(json_encode($resp));     
    }
    
    if ( !isset($_COOKIE[$COOKIENAME]))
        contest_ret(['error'=>1,'message'=>'no data']);
    
    
    $client_data = WBP::getContestDir($_COOKIE[$COOKIENAME]);
    
	
    foreach($client_data['contents'] AS $k=>$v) {
		unlink($client_data['dir'].'/'.$k);
	}
    
    die('OK');
