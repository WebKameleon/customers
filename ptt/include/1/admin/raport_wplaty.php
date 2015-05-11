<?php
	$query="SELECT * 
		FROM klienci,wplaty
		WHERE wplaty.klient_id=klienci.id
		ORDER BY wplaty.id,nazwisko
		";

	include("$INCLUDE_PATH/admin/raport_lista_query_wymagane.php");
