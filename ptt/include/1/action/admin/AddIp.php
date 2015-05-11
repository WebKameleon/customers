<?php

	$ip=$_REQUEST['ip'];

	if (!strlen($ip)) $ip=$_SERVER['REMOTE_ADDR'];



	$query="INSERT INTO admin (ip,username) SELECT '$ip','".$_SERVER['PHP_AUTH_USER']."' 
			WHERE '$ip' NOT IN (SELECT ip FROM admin)";


	db_Exec($db,$query);
