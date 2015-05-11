<?php
	
	$kurs=$_REQUEST['kurs'];
	$klient=$_REQUEST['klient'];
	$nowa_cena=$_REQUEST['nowa_cena'];
	$nazwa_tanca=$_REQUEST['nazwa_tanca'];

	

	if (!strlen($klient) || !strlen($nowa_cena) || !strlen($kurs)) return;

	$sql = "SELECT cena AS stara_cena FROM zapisy 
			WHERE klient_id = $klient AND kurs_id = $kurs";
	parse_str(query2url($sql));


	$sql = "UPDATE zapisy SET cena = $nowa_cena 
			WHERE klient_id = $klient AND kurs_id = $kurs";

	if (pg_Exec($db,$sql))
	{
		$sql = "SELECT imie, nazwisko FROM klienci WHERE id = $klient";
		parse_str(query2url($sql));
		$undo ="UPDATE zapisy SET cena = $stara_cena 
				WHERE klient_id = $klient AND kurs_id = $kurs";

 		$opis = "Przywraca cenę dla $imie $nazwisko za $nazwa_tanca na poziom $stara_cena";
		$sql = "INSERT INTO undo (opis, undo, d_wykonania, username)
				VALUES ('$opis ','$undo',CURRENT_DATE,'$username')";
		pg_Exec($db,$sql);

	}
