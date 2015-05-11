<?php
	$szukaj=$_REQUEST['szukaj'];
?>
<form action="<?php echo $self?>" method=post>
<input name=szukaj value="<?php echo $szukaj?>">
<input type=submit value="Szukaj" class=button>
</form>

<?php

	$query="SELECT *,id AS klient_id FROM klienci 
		WHERE id IN (SELECT klient_id FROM zapisy WHERE klient_id=klienci.id
				AND ilosc>0)
		";

	if (strlen($szukaj)) $query.="AND (nazwisko ~* '$szukaj' OR email ~* '$szukaj' OR login ~* '$szukaj') ";

	$query.="\nORDER BY nazwisko,imie,miasto";

	include("$INCLUDE_PATH/admin/raport_lista_query_wymagane.php");
	
