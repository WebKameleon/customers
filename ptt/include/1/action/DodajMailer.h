<?
	global $nazwa,$mailer_id;

	if (!strlen($nazwa)) return;

	$query="INSERT INTO mailer (action,mailfrom) VALUES ('$nazwa','$C_EMAIL')";


	//echo nl2br($query);
	pg_Exec($db,$query) ;

	$query="SELECT max(id) AS mailer_id FROM mailer";
	parse_str(query2url($query));
	
?>

