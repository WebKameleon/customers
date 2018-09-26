<?php
    include_once __DIR__.'/../system/fun.php';
    
    $COOKIENAME= 'fcontest';
    
    function contest_ret($resp)
    {
        header('Content-type: application/json; charset=utf8');
        die(json_encode($resp));     
    }
    
    if (!isset($_REQUEST['file']) || !isset($_COOKIE[$COOKIENAME]))
        contest_ret(['error'=>1,'message'=>'no data']);
    
    $file=$_REQUEST['file'];
    
    $client_data = WBP::getContestDir($_COOKIE[$COOKIENAME]);
        
    if (!isset($client_data['contents'][$file]))
        contest_ret(['error'=>1,'message'=>'no file','file'=>$file]);
    
    foreach ($client_data['contents'] AS $k=>$v) {
        if ($k=='data.json') continue;
        if (substr($k,0,strlen($file))==$file)    
            unlink($client_data['dir'].'/'.$k);
   
    }
    
    $client_data = WBP::getContestDir($_COOKIE[$COOKIENAME]);
    
    $contest = [
		'data' => [],
		'files' => []
	];
    
    foreach($client_data['contents'] AS $k=>$v) {
		if (substr($k,-5)=='.json') {
			if ($k=='data.json')
				$contest['data'] = $v;
			else
				$contest['files'][$k] = $v;
				
		}
	}
    
    contest_ret($contest);
