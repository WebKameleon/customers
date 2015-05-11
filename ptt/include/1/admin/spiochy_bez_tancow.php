<?php
	$akademik=$_REQUEST['akademik'];

	$znak="?";
	if (strstr($next,$znak)) $znak="&";
	

	$query="SELECT *,nazwa AS akademik FROM zapisy_a
			LEFT JOIN klienci ON  zapisy_a.klient_id=klienci.id
			LEFT JOIN akademiki ON akademik_id=akademiki.id
		WHERE ilosc>0 AND (0 IN (SELECT sum(ilosc) FROM zapisy WHERE klient_id=zapisy_a.klient_id) 
							OR klient_id NOT IN (SELECT klient_id FROM zapisy WHERE klient_id=zapisy_a.klient_id)) 
		
		ORDER BY nazwisko,imie,miasto";


	include("$INCLUDE_PATH/admin/raport_lista_query_wymagane.php");
