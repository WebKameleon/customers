<?php
	if (headers_sent())
		echo "<script> document.cookie = \"AUTH_PASS=; path=/\"; </script>";
	else
		SetCookie("AUTH_PASS","",0,'/');
	
	if (isset($AUTH_PASS)) unset($AUTH_PASS);
	if (isset($_REQUEST['AUTH_PASS'])) unset($_REQUEST['AUTH_PASS']);
