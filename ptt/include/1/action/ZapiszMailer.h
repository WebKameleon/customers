<?
	global $nazwa,$mailer_id,$msg,$mailfrom,$subject,$type,$grupa,$mailer_id;

	$grupa+=0;

	$msg=addslashes(stripslashes($msg));
	$subject=addslashes(stripslashes($subject));

	$query="UPDATE mailer
		 SET 	action='$nazwa',mailfrom='$mailfrom',subject='$subject',
			type='$type',msg='$msg',grupa=$grupa
		 WHERE id=$mailer_id ";
	
	//echo nl2br($query);return;

	if (pg_Exec($db,$query));
	$akcja=$nazwa;
	//$mailer_id=$id;
?>

