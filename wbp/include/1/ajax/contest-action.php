<?php

    use google\appengine\api\cloud_storage\CloudStorageTools;
 
    $url=str_replace('contest-action.php','contest.php',$_SERVER['REQUEST_URI']);

    if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
		require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
        die(CloudStorageTools::createUploadUrl($url, []));
	} else {
        die($url);
    }