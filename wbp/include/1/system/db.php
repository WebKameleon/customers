<?php

    $current_version=0;
    $sql="SELECT max(ver) FROM wbp_db";
    try{
    	$q=$dbh->query($sql);
    	if ($q) foreach ($q AS $row ){
        	$current_version=$row[0];
    	}
    }
    catch (Exception $e) {

    }

    $driver=$dbh->getAttribute(PDO::ATTR_DRIVER_NAME);
    $dir=__DIR__.'/../db/'.$driver;
    $versions=array();
    foreach (scandir($dir) AS $file) {
        if ($file[0]=='.' || is_dir("$dir/$file")) continue;

        $a=explode('_',$file);

        $versions[$a[0]+0]=$file;
    }

    ksort($versions);

    ob_start();
    foreach($versions AS $v=>$file) {
        if ($v>$current_version) {
	    
	    
	    
	    $sql=file_get_contents("$dir/$file");
	    echo "<pre><h1>$file</h1>$sql</pre>";
	    if ($dbh->exec($sql)===false)
	    {
		mydie($dbh->errorInfo());
	    }
	    
	    
	    $sql="INSERT INTO wbp_db (ver, file) VALUES ($v,'$file')";
	    $dbh->exec($sql);
	    $current_version=$v;		
	    
        }
    }
    ob_end_clean();
