<?php


class sendmail_obj 
{  
	var $from,$to,$cc;
	var $subject,$msg;
	var $type;
	var $bcc;
	var $att;
	var $precedence;
} 



function sendmail2_encode($str)
{
	return "=?UTF-8?B?".base64_encode($str)."?=";
}



function sendmail_full($obj)
{
	return sendmail2($obj);
}


function sendmail2(&$obj)
{

	$from=$obj->from;
	$to=$obj->to;
	$cc=$obj->cc;
	$subject=$obj->subject;
	$msg=$obj->msg;
	$type=$obj->type;
	$bcc=$obj->bcc;
	$att=$obj->att;
	$att_cid= $obj->att_cid;
	if ( !strlen($from) ) return 0;
	if ( !strlen($to) ) return 0;
	if ( !strlen($msg) ) return 0;

	if ( strtolower($type)!="html" ) $type="plain";

	$msg = chunk_split(base64_encode($msg));

	$subject = stripslashes($subject);
	$subject = sendmail2_encode($subject);

	$mailcc="";
	if (is_Array($cc))
	{
		for ($c=0;$c<count($cc);$c++)
		{
			$mailcc.="Cc: ".$cc[$c]."\n";
		}
	}
	else if (strlen($cc)) $mailcc ="Cc: ".$cc."\n";

	

	$msgheader="Received: from ".$_SERVER['REMOTE_HOST']." (".$_SERVER['$REMOTE_HOST']." [".$_SERVER['REMOTE_ADDR']."])\n";
	$msgheader.="From: $from\nTo: $to\n$mailcc";
	if (strlen($obj->precedence)) $msgheader.="Precedence: ".$obj->precedence."\n";

	$boundary = "0Ga-".time()."-".time()%2001;
	$msgbody.="Mime-Version: 1.0\n";
	$msgbody.= "Content-Type: MULTIPART/MIXED;";
	$msgbody.= " BOUNDARY=\"".$boundary."\"\n\n";

//	$msgbody.= "--".$boundary."\n";



	if (is_Array($att_cid))
	{

		$msgbody.= "--".$boundary."\n";
		$cid_boundary = "0Ga-".time()."-".time()%2002;
		$msgbody.="Content-Type: multipart/related;\n";
		$msgbody.= " BOUNDARY=\"".$cid_boundary."\"\n\n";

		$msgbody.= "--".$cid_boundary."\n";
		$msgbody.= "Content-Type: TEXT/$type; ";
		$msgbody.= "\n	charset=\"utf8\"\n";
		$msgbody.= "Content-Transfer-Encoding: base64\n\n";
		$msgbody.= "$msg\n";


		while( list($key,$val) = each ($att_cid) )
		{
			if (!file_exists($key)) continue;	
			$f=popen("file -bi $key","r");
			$type=fread($f,100);
			pclose($f);
			$type=trim($type);

			$f=fopen($key,"rb");
			$data=fread($f,filesize ($key));
			fclose($f);

			$name=basename($key);

			$at_file = chunk_split(base64_encode($data));

			$msgbody.= "\n--".$cid_boundary."\n";
			$msgbody.= "Content-Type: $type;";
			$msgbody.= "\n	name=\"$name\"\n";
			$msgbody.= "Content-Transfer-Encoding: base64\n";

			if (strlen($val)) 
				$msgbody.=$val;

			$msgbody.= "\n";

			$msgbody.= $at_file;			
		}

		$msgbody.= "\n--".$cid_boundary."--\n";

	} else 
	{
		$msgbody.= "--".$boundary."\n";
		$msgbody.= "Content-Type: TEXT/$type; ";
		$msgbody.= "\n	charset=\"utf8\"\n";
		$msgbody.= "Content-Transfer-Encoding: base64\n\n";
		$msgbody.= "$msg\n";
	}


	if (is_Array($att))
	{
	
		while( list($key,$val) = each ($att) )
		{
			if (!file_exists($key)) continue;	
			$f=popen("file -bi $key","r");
			$type=fread($f,100);
			pclose($f);
			$type=trim($type);

			$f=fopen($key,"rb");
			$data=fread($f,filesize ($key));
			fclose($f);

			$name=basename($key);

			$at_file = chunk_split(base64_encode($data));

			$msgbody.= "\n--".$boundary."\n";
			$msgbody.= "Content-Type: $type;";
			$msgbody.= "\n	name=\"$name\"\n";
			$msgbody.= "Content-Transfer-Encoding: base64\n";

			if (strlen($val)) 
				$msgbody.=$val;
			else
			{
				$msgbody.= "Content-Description: $name\n";
				$msgbody.= "Content-Disposition: attachment; filename=\"$name\"\n";
			}
			$msgbody.= "\n";

			$msgbody.= $at_file;
	
		}

		$msgbody.= "\n--".$boundary."--\n";
	}

	$msgheader.="Subject: $subject\n";

	if (!is_array($bcc)) $bcc=array("$to");

	$bccpole="";
	for ($i=0;$i<count($bcc);$i++)
	{
		$bccpole.="Bcc: ".$bcc[$i]."\n";
		if ( ($i && !($i%100)) || $i==count($bcc)-1 )
		{
			$_msg="$msgheader$bccpole$msgbody";

			$plik="/tmp/sendmail".time().uniqid("");
			$f=fopen($plik,"w");
			if (!$f) return 0;
			fputs($f,$_msg);
			fclose($f);

			
			if ( !strlen($obj->exec_file) ) $obj->exec_file="$plik.sh";

			
			$f=fopen($obj->exec_file,"a");
			fputs($f,"/usr/sbin/sendmail -t -f \"$from\" <$plik \n");
			fputs($f,"rm -f $plik\n"); 
			if (!$obj->wait4flush) fputs($f,"rm -f $obj->exec_file \n");
			fclose($f);

			if (!$obj->wait4flush) 
			{
				exec("/bin/sh $obj->exec_file >/dev/null 2>/dev/null &");
				$obj->exec_file="";
			}

			$bccpole="";
		}
	}

	if ($obj->wait4flush && $obj->flush && strlen($obj->exec_file) ) 
	{
		$f=fopen($obj->exec_file,"a");
		fputs($f,"\nrm -f $obj->exec_file\n");
		fclose($f);
		exec("/bin/sync");
		exec("/bin/sh $obj->exec_file >/dev/null 2>/dev/null &");
		$obj->exec_file="";
		$obj->flush=0;

	}

	return 1;
	
}

