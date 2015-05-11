<?php

	@session_start();
	
	require_once __DIR__.'/../kameleon/Google.php';
	require_once __DIR__.'/../kameleon/Spreadsheet.php';
	include_once __DIR__.'/../system/fun.php';

	
	$req = array();
	$req['cmd'] = '_notify-validate';
	foreach ($_REQUEST as $key => $value) {
	    $req[$key] = stripslashes($value);
	}	
	$req = http_build_query($req);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/x-www-form-urlencoded',
	    'Host: www.paypal.com',
	    'Connection: close'
	));
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	
	$res = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	
	if ($res=='VERIFIED')
	{
		//mail('piotr@gammanet.pl','paypal',print_r(array('req'=>$_REQUEST,'resp'=>$res),1));
		$sid=substr($_REQUEST['custom'],0,32);
		$id=substr($_REQUEST['custom'],32);
	
		WBP::contest_paied($sid,$id,date('d-m-Y, H:i', strtotime($_REQUEST['payment_date'])),$_REQUEST['mc_gross'].$_REQUEST['mc_currency']);	
	}
	
	
	header('Content-Type: text/plain; charset=utf-8');
	die('OK');
