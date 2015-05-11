<?
	global $msg_msg,$msg_label,$MsgLang;

	$action="";

	if ( !strlen($msg_label) || !strlen(trim($msg_msg)) ) return;

	$msg_msg=addslashes(stripslashes($msg_msg));
	$msg_label=addslashes(stripslashes($msg_label));

	$sql="SELECT count(*) AS c FROM messages WHERE msg_label='$msg_label' AND msg_lang='$MsgLang'";
	parse_str(sql2url($sql));

	if ($c)
		$query="UPDATE messages SET msg_msg='$msg_msg' 
			 WHERE msg_label='$msg_label' AND msg_lang='$MsgLang'";
	else
		$query="INSERT INTO messages (msg_lang,msg_label,msg_msg)
			 VALUES ('$MsgLang','$msg_label','$msg_msg')";
	
	
	
		
	//echo nl2br($query);return;
	if (db_Exec($db,$query)) {}


?>

