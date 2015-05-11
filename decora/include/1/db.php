<?php

    $current_version=0;
    $sql="SELECT max(ver) FROM decora_db";
    try{
    	$q=$dbh->query($sql);
    	if ($q) foreach ($q AS $row ){
        	$current_version=$row[0];
    	}
    }
    catch (Exception $e) {

    }


    $dir=__DIR__.'/db/'.$dbh->getAttribute(PDO::ATTR_DRIVER_NAME);
    $versions=array();
    foreach (scandir($dir) AS $file) {
        if ($file[0]=='.' || is_dir("$dir/$file")) continue;

        $a=explode('_',$file);

        $versions[$a[0]+0]=$file;
    }

    ksort($versions);

    foreach($versions AS $v=>$file) {
        if ($v>$current_version) {
	    
	    $e=$dbh->import(__DIR__.'/db',$file);
	    if ($e)
	    {
		//mydie($e['errorInfo'],$e['sql']);
		break;
	    }
	    else
	    {
		$sql="INSERT INTO decora_db (ver, file) VALUES ($v,'$file')";
		$dbh->exec($sql);
		$current_version=$v;		
	    }
        }
    }
    
