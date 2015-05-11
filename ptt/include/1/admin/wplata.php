<?php	
	$szukaj=$_REQUEST['szukaj'];
	$klient=$_REQUEST['klient'];
	
?>

<form  class="form-group" action="<?php echo $self?>" method=post>
<input class="form-control" style="width:80%;float:left;margin-right:5px;" name=szukaj value="<?php echo $szukaj?>">
<input type=submit value="Szukaj" class="btn btn-default">
</form>


<?php
if (!strlen($szukaj)) return;

$query="SELECT *,id AS klient_id FROM klienci WHERE
	(nazwisko ~* '$szukaj' OR email ~* '$szukaj' OR login ~* '$szukaj')
	ORDER BY nazwisko,imie,miasto ";

$more_href="&szukaj=".urlencode($szukaj)."#wplata";

include("$INCLUDE_PATH/admin/raport_lista_query_wymagane.php");

?>



<a name=wplata>
<hr size=1 color="<?php echo $COLORS[0]?>">
<?php

if (!strlen($klient)) return;

echo "<hr color=$COLORS[0] size=1><u>Wpłata:</u>";
echo "<form method=post action=$self>
	<input type=hidden name=action value=\"admin/PrzyszlaWplata\">
	<input type=hidden name=klient value='$klient'>
	<input type=hidden name=szukaj value='$szukaj'>";

echo "Uwagi:<br><textarea name=uwagi cols=50 rows=5></textarea><br>";

echo "<input size=5 name=kwota> zł
	<input type=submit class=button value='Zaksięguj'>";

echo "</form>";


