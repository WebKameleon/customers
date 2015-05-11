<?
include_once ("$INCLUDE_PATH/sendmail2.h");

	global $mailing_bcc;
	global $sendmail_action;


	if (!strlen($sendmail_action)) $error=sysmsg("alert_choose_mail_action");
	if (!strlen($sendmail_action)) return;

	$mailing_bcc = trim($mailing_bcc);
	$mailbcc=explode("\n",$mailing_bcc);
	$ile=count($mailbcc);
	if (!is_array($mailbcc)) $ile=0;

	if ($ile) 
	{	
		
		$mailbcc_tmp = $mailbcc;
		unset($mailbcc);
		$obj=new sendmail_obj;
		$obj->wait4flush=1;
		for ($i=0; $i < $ile; $i++)
		{
			if ( ($i && !($i%10000)) || $i==$ile-1) $obj->flush=1;
			$action="";
			$tmp_string = "";
			$first_char = strpos(trim($mailbcc_tmp[$i]),"<"); 
			$last_char = strpos(trim($mailbcc_tmp[$i]),">"); 
			$first_char++;
			$tmp_string = strtolower(substr(trim($mailbcc_tmp[$i]),$first_char,$last_char-$first_char));
			$mailto = $mailbcc_tmp[$i];
			$pure_mail = $tmp_string;
			include("$INCLUDE_PATH/action/SendMail.h");
		}
		//$action="SendMail";
		//$mailto="\$mailfrom";
		$sysinfo=sysmsg("info_messages_sent_count").": $ile";
	}
	

?>
