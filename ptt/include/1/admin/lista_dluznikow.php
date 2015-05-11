<?php

	$szukaj=$_REQUEST['szukaj'];
	$klient=$_REQUEST['klient'];	

?>

<form action="<?php echo $self?>" method=post>
<input name=szukaj value="<?php echo $szukaj?>">
<input type=submit value="Szukaj" class=button>
</form>


<?php 
	
	if ($klient) 
	{
		$and="AND klient_id=$klient";
	}

	$query="SELECT *, zapisy.cena AS zcena,
				d_zgloszenia+$ile_dajemy_czasu AS termin,
				d_zgloszenia+$ile_dajemy_czasu<CURRENT_DATE AS przeterm,
				zapisy.id AS zapis_id
		FROM zapisy,klienci,kursy WHERE ilosc>0 $and
		AND zapisy.klient_id=klienci.id
		AND zapisy.kurs_id=kursy.id
		";

	if (strlen($szukaj)) 
	{
		$_id=$szukaj+0;
		$query.="AND (nazwisko ~* '$szukaj' OR email ~* '$szukaj' OR login ~* '$szukaj' OR miasto ~* 'szukaj') ";
	}

	$query.="\nORDER BY d_zgloszenia,zapisy.id";



	include("$INCLUDE_PATH/admin/raport_lista_query_wymagane.php");


