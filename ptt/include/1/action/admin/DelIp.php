<?php

	$ip=$_REQUEST['ip'];
	if (!strlen($ip)) $ip=$_SERVER['REMOTE_ADDR'];

	if (!strlen($ip)) $ip=$REMOTE_ADDR;
	$query="DELETE FROM admin WHERE ip='$ip'";
	db_Exec($db,$query);
