<?php

$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';

//if ($pagetype==3 || strlen($AUTH_PASS)) include ("$INCLUDE_PATH/action/login.php");
if ($pagetype==3 ) include ("$INCLUDE_PATH/action/login.php");



$previous_action="";
while( strlen($action) && !strlen($error) && $action!=$previous_action )
{
	$previous_action=$action;


	if (file_exists("$INCLUDE_PATH/action/$action.php"))
	{
		include("$INCLUDE_PATH/action/$action.php");
	}
	else $error="Brak pliku $action.php";
	
}
$_REQUEST['action']=$action="";



if (strlen($error))
{

	die("<html>
		<head>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
			<title>$error</title>
				<script>alert('$error');history.go(-1);</script>
		</head>
			</html>");


}

