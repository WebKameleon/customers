<?php

use google\appengine\api\cloud_storage\CloudStorageTools;

require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
$base='gs://'.CloudStorageTools::getDefaultGoogleStorageBucketName().'/post';

echo '<pre>';
echo "$base\n";

foreach (scandir($base) AS $component)
{
    echo "Starting $component\n";
    if ($component[0]=='.') continue;
    $dir="$base/$component";
    
    
    $log=[];
    foreach(scandir($dir) AS $f)
    {
        if ($f[0]=='.') continue;
	if (substr($f,0,10)==date('Y-m-d')) continue;
	if (strlen($f)<15) continue;

	if (!isset($log[substr($f,0,10)])) $log[substr($f,0,10)]=''; 
	$log[substr($f,0,10)].=$f."\n".file_get_contents("$dir$f")."\n\n";
	
        unlink("$dir$f");
    }


	foreach ($log AS $f=>$c)
	{
		if (strlen($f)<10) continue;
		file_put_contents($dir.$f.'.txt',$c);
	}


	
}
