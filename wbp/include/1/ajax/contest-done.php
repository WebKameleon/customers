<?php

	require_once __DIR__.'/../kameleon/Google.php';
	require_once __DIR__.'/../kameleon/Spreadsheet.php';
	include_once __DIR__.'/../system/fun.php';


	if (!isset($_REQUEST['id']) || strlen($_REQUEST['id'])!=64 ) return;
	$sid=substr($_REQUEST['id'],0,32);
	$photo_id=substr($_REQUEST['id'],32);
	
	$td_data=WBP::get_data($sid);
	
        Spreadsheet::setToken(null);
        if (!isset($_SESSION['spreadsheets_access_token'])) $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
        $token=Spreadsheet::setToken($_SESSION['spreadsheets_access_token']);    
        foreach($token AS $k=>$v) $td_data['tokens']['spreadsheets']->$k=$v;
        $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
	
	session_write_close();

	$sheets=Spreadsheet::listWorksheets($td_data['drive']['id']);
		
	$worksheet_id=null;
	foreach ($sheets AS $id=>$contents)
	{
	    if ($contents['title']==$td_data['title'])
	    {
		$worksheet_id=$id;
		break;
	    }
	}	
	
	$data=Spreadsheet::getWorksheet($td_data['drive']['id'], $worksheet_id );

	$header=$data[0];
	$idx_id=-1;
	foreach($header AS $i=>$h)
	{
	    if ($h=='id') $idx_id=$i;
	}
	
	$foto_ul='';
	
	$from=$td_data['drive']['email'];
	$to=$from;

	for ($i=1; $i<count($data); $i++)
	{
	    if ($data[$i][$idx_id]==$photo_id)
	    {
		foreach ($data[$i] AS $k=>$v) $data[$i][$header[$k]]=$v;
		if (strstr($data[$i]['email'],'@')) $to=trim($data[$i]['email']);
		$foto_ul.='<li><img src="'.$data[$i]['thumbnail'].'"/> '.$data[$i]['title'].'</li>';
	    }
	}
	
	
	$mail='Dziękujemy za przesłanie następujących zdjęć <ul style="list-style: none">'.$foto_ul.'</ul>';
	
	$mail.='<p>Po weryfikacji zdjęcia wezmą udział w konkursie <b>'.$td_data['title'].'</b></p>';
	$mail.='<p>WBPiCAK - Dział FOTOGRAFIA</p>';
	
	$header="From: $from\r\nBcc: $from\r\nContent-type: text/html; charset=utf-8\r\nContent-transfer-encoding: base64";
	$title='=?UTF-8?B?'.base64_encode($td_data['title']).'?=';
	
	mail($to,$title,base64_encode($mail),$header);
	