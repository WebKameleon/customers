<?php
	use google\appengine\api\cloud_storage\CloudStorageTools;
	
	require_once __DIR__.'/../kameleon/Google.php';
	require_once __DIR__.'/../kameleon/Spreadsheet.php';
	include_once __DIR__.'/../system/fun.php';

	
	function newCoockieValue() {
		return md5(time().rand(20000,100000));
	}
	
	$CLIENTNAME = isset($_COOKIE[$COOKIENAME])?$_COOKIE[$COOKIENAME]:newCoockieValue();
	
	
	
	
	$data = WBP::getContestDir($CLIENTNAME);
		
	$contest = [
		'id' => $CLIENTNAME,
		'data' => [],
		'files' => []
	];
	
	foreach($data['contents'] AS $k=>$v) {
		if (substr($k,-5)=='.json') {
			if ($k=='data.json')
				$contest['data'] = $v;
			else
				$contest['files'][$k] = $v;
				
		}
	}
	
	if (false && isset($contest['data']['finished'])) {
		$CLIENTNAME = newCoockieValue();
		$data = WBP::getContestDir($CLIENTNAME);
		$contest = [
			'id' => $CLIENTNAME,
			'data' => [],
			'files' => []
		];
	}
	
	
	setcookie($COOKIENAME,$CLIENTNAME,time()+24*3600,'/');
	
	Header('Content-type: application/json');
	die(json_encode($contest));