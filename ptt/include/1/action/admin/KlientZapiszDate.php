<?php
	$klient=$_REQUEST['klient'];
	$data_p=$_REQUEST['data_p'];
	
	if (!strlen($klient)) return;
		
	if (strlen($data_p))
	{
		$data_p = FormatujDateSql($data_p);
		$sql = "UPDATE klienci SET d_przyjazdu = '$data_p' WHERE $klient = id";
	} else
	{
		$sql = "UPDATE klienci SET d_przyjazdu = NULL WHERE $klient = id";
	}
	pg_Exec($db,$sql);
	$sql = "SELECT imie, nazwisko FROM klienci WHERE id = $klient";
	parse_str(query2url($sql));

	$undo = "UPDATE klienci SET d_przyjazdu = NULL WHERE $klient = id";
	$opis = "Zeruje datę przyjazdu klienta : $imie $nazwisko";
	$sql = "INSERT INTO undo (opis, undo, d_wykonania) VALUES ('$opis','$undo',CURRENT_DATE)";
	pg_Exec($db,$sql);


