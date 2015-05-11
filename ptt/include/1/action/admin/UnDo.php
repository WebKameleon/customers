<?php
	$undo=$_REQUEST['undo']+0;

	if (!$undo) return;

	$query="SELECT undo AS undo_query FROM undo WHERE id=$undo";
	parse_str(query2url($query));

	
	$undo_query=stripslashes($undo_query);
	$query="$undo_query ;
		 DELETE FROM undo WHERE id=$undo";
	

	//echo nl2br($query); return;
	if (pg_Exec($db,$query)) $sysinfo="Wycofanie operacji zakończone";
