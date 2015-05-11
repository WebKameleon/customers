<?php
	$klient=$_REQUEST['klient'];
	$kwota=$_REQUEST['kwota'];
	$uwagi=$_REQUEST['uwagi'];
	$szukaj=$_REQUEST['szukaj'];


	$kwota=0+str_replace(",",".",$kwota);
	if (!$klient || !$kwota) return;

	//if ($kwota<=0) $sysinfo="System nie przewiduje wyplat";
	if (strlen($sysinfo)) return;

	$uwagi=toText($uwagi);

	$query="INSERT INTO wplaty (klient_id,d_wplaty,kwota,uwagi)
		 VALUES ($klient,CURRENT_DATE,$kwota,'$uwagi')";
	


	//echo nl2br($query); return;
	if (pg_Exec($db,$query)) 
	{
		$sysinfo="Zarejestrowano wpłatę";
		$query="SELECT max(id) AS maxid FROM wplaty WHERE klient_id=$klient";
		parse_str(query2url($query));

		$query="SELECT * FROM klienci WHERE id=$klient";
		parse_str(query2url($query));


		$undo_q="DELETE FROM wplaty WHERE id=$maxid";

		undo($undo_q,"Zaksięgowano wpłatę $kwota od $imie $nazwisko, $miasto ($uwagi)");
	}

