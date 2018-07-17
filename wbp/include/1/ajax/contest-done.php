<?php

	if (!isset($_REQUEST['id']) || strlen($_REQUEST['id'])!=64 ) return;
	$sid=substr($_REQUEST['id'],0,32);
	$photo_id=substr($_REQUEST['id'],32);
	
	include __DIR__.'/contest-cron.php';

	

	