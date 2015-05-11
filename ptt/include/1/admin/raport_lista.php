<?php
	$kurs=$_REQUEST['kurs'];

	include("$INCLUDE_PATH/admin/lista_kursow.php");
	if (!$kurs) return;


	
	$query="SELECT * FROM kursy,obiekty 
		 WHERE kursy.id=$kurs
		 AND kursy.obiekt=obiekty.kod";

	parse_str(query2url($query));

	echo "$nazwa / sala $pomieszczenie <br>";
	echo "Cykl $cykl ($CYKLE[$cykl]), godz: ".substr($godz_od,0,5)." - ".substr($godz_do,0,5)."<br>";
	echo stripslashes("$taniec - $zaawansowanie<br>");
	echo "Prowadzacy: $prowadzacy<br>";



	$query="SELECT * FROM zapisy,klienci 
		WHERE ilosc>0 AND kurs_id=$kurs
		AND zapisy.klient_id=klienci.id
		ORDER BY nazwisko,imie,miasto";

	$res=pg_Exec($db,$query);

	$more_href="&kurs=$kurs";

	include("$INCLUDE_PATH/admin/raport_lista_query_wymagane.php");
	
