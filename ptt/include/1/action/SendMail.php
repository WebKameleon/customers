<?php

include_once ("$INCLUDE_PATH/system/sendmail2.php");

if (!is_object($obj)) $obj=new sendmail_obj;
$obj->to="$mailto";
$obj->from="$mailfrom";
$obj->subject="$subject";

if (isset($attachment) && file_exists($attachment))
{
	$plik=fopen($attachment,"r");
	$binadata=fread( $plik, $attachment_size );
	fclose($plik);
	$obj->att[]=array($binadata,$attachment_type,$attachment_name);	
}

if (strlen($sendmail_action))
{
	$query="SELECT * FROM mailer WHERE action='$sendmail_action'";
	parse_str(query2url($query));
	$action="";
}

$msg=addslashes($msg);
eval("\$msg_list=stripslashes(\"$msg\");");

$obj->from="$mailfrom";
eval("\$obj->to=\"$mailto\";");
eval("\$obj->subject=stripslashes(\"$subject\");");


if (is_array($mailcc)) $obj->cc=$mailcc;
if (is_array($mail)) $obj->bcc=$mailbcc;

$obj->type="$type";
$obj->msg="$msg_list";

if (is_array($bcc)) $obj->bcc=$bcc;

sendmail2($obj);



