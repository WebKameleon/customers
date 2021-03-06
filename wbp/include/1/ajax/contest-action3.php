<?php

    use google\appengine\api\cloud_storage\CloudStorageTools;
 
    $url=str_replace('contest-action3.php','contest3.php?t='.time(),$_SERVER['REQUEST_URI']);

    if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
		require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
        $url=CloudStorageTools::createUploadUrl($url.'&cookie='.$_COOKIE['PHPSESSID'], []);
	} 
    
	Header('Content-type: application/json');    
	die(json_encode(['url'=>$url,'ip'=>$_SERVER['REMOTE_ADDR']]));
    