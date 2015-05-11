<?
	global $mailer_id;
	if (!$mailer_id) return;

	$query="DELETE FROM mailer WHERE id=$mailer_id;";

	//echo nl2br($query); return;
	pg_Exec($db,$query) ;


?>

