<?
$sendmail_action=$action;
$action="";


$query="SELECT * FROM mailer WHERE action='$akcja' "; 
$result=pg_Exec($db,$query);
parse_str(query2url($query));
$mailto=$mailfrom;

$query="SELECT email FROM klub WHERE mailer ~ ':$grupa:'";
$mailer=pg_ObjectArray($db,$query);

if (is_array($mailer))
{
	$action="SendMail";
	for ($i=0;$i<count($mailer);$i++)
		$bcc[]=$mailer[$i]->email;
}
?>