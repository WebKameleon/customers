<?php

use google\appengine\api\cloud_storage\CloudStorageTools;

require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
$base='gs://'.CloudStorageTools::getDefaultGoogleStorageBucketName().'/posts';

foreach (scandir($base) AS $component)
{
    echo "Starting $component<br/>";
    if ($component[0]=='.') continue;
    $dir="$base/$component";
    
    
    $log=[];
    foreach(scandir($dir) AS $f)
    {
        if ($f[0]=='.') continue;
	if (substr($f,0,10)==date('Y-m-d') continue;
	if (strlen($f)==14) continue;

	$log[substr($f,0,10)].=$f."\n".file_get_contents("$dir/$f")."\n\n";
	
	
        //unlink("$dir/$f");
    }

	foreach ($log AS $f=>$c)
	{
		file_put_contents($dir.'/'.$f,$c);
	}
	
}
