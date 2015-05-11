<hr size=1>Akademiki<br>
<?php

	$query="SELECT nazwa,sum(ilosc*zapisy_a.cena) AS wart,count(*) AS ile 
		FROM zapisy_a,akademiki 
		WHERE ilosc>0 AND akademik_id=akademiki.id
		GROUP BY nazwa ORDER BY nazwa";

	$suma=0;
	$ilosc=0;
	$res=pg_Exec($db,$query);
	echo "<table class=\"table table-responsive table-striped table-bordered \">";
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_explodeName($res,$i));
		echo "<tr>";
		echo "<td>$nazwa</td>";
		echo "<td>$ile osób</td>";
		echo "<td align=right>".u_Cena($wart)."</td>";
		echo "</tr>";

		$suma+=$wart;
		$ilosc+=$ile;
	}
	echo "<tr>";
	echo "<td><b>Razem:</b></td>";
	echo "<td><b>$ilosc</b></td>";
	echo "<td align=right><b>".u_Cena($suma)."</b></td>";
	echo "</tr>";


	echo "</table>\n";
?>
<hr size=1>Zapisy na techniki tańca<br>
<?php
	$query="SELECT taniec,sum(ilosc*zapisy.cena) AS wart,sum(ilosc) AS ile 
		FROM zapisy,kursy
		WHERE ilosc>0 AND kurs_id=kursy.id
		GROUP BY taniec ORDER BY taniec";

	$suma=0;
	$ilosc=0;
	$res=pg_Exec($db,$query);
	
	echo "<table class=\"table table-responsive table-striped table-bordered \">";
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_explodeName($res,$i));
		$taniec=stripslashes($taniec);
		echo "<tr>";
		echo "<td>$taniec</td>";
		echo "<td>$ile osób</td>";
		echo "<td align=right>".u_Cena($wart)."</td>";
		echo "</tr>";

		$suma+=$wart;
		$ilosc+=$ile;
	}


	echo "<tr>";
	echo "<td><b>Razem:</b></td>";
	echo "<td><b>$ilosc kursów</b></td>";
	echo "<td align=right><b>".u_Cena($suma)."</b></td>";
	echo "</tr>";

	$query="SELECT count(*) AS ile
		FROM klienci
		WHERE id IN (SELECT klient_id FROM zapisy WHERE klient_id=klienci.id AND ilosc>0 ) 
		";

	parse_str(query2url($query));

	echo "<tr>";
	echo "<td><b>Razem:</b></td>";
	echo "<td><b>$ile osób</b></td>";
	echo "<td align=right>&nbsp;</td>";
	echo "</tr>";
	

	$query="SELECT sum(miejsc) AS miejsc FROM kursy WHERE rok=$C_ROK";
	parse_str(query2url($query));
	echo "<tr>";
	echo "<td><b>Przygotowanych miejsc:</b></td>";
	echo "<td><b>$miejsc</b></td>";
	echo "<td align=right>&nbsp;</td>";
	echo "</tr>";

	echo "</table>\n";

	$total_zapisow=$ilosc;




?>


<hr size=1>Statystyki wiekowo-płciowe<br>
<?php
	$query="SELECT wiek,plec,count(*) AS ile, sum(cena*ilosc) AS wart
		FROM zapisy,klienci
		WHERE ilosc>0 AND klient_id=klienci.id
		GROUP BY wiek,plec ORDER BY wiek,plec";

	$suma=0;
	$ilosc=0;
	$res=pg_Exec($db,$query);

	echo "<table class=\"table table-responsive table-striped table-bordered \">";
	echo "<tr>";
	$_wiek="";
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_explodeName($res,$i));
		if (!strlen($plec)) $plec="?";	
		if (!strlen($wiek)) $wiek="?";	

		if ($i && $_wiek!=$wiek) 
		{
			echo "</tr><tr>";
			echo "<td>Wiek: <b>$wiek</b></td>";
		}
		if (!$i) echo "<td>Wiek: <b>$wiek</b></td>";
		$_wiek=$wiek;



		echo "<td>płeć <b>$plec</b>:";
		echo "$ile (".round(100*$ile/$total_zapisow)."%)";
		echo "=".u_Cena($wart)."</td>";

		$suma+=$wart;
		$ilosc+=$ile;
	}


	echo "</tr>";

	echo "</table>\n";


?>

<hr size=1>Wpłaty<br>
<?php
	$query="SELECT sum(kwota) AS wart
		FROM wplaty";

	parse_str(query2url($query));

	echo "<table class=\"table table-responsive table-striped table-bordered \">";
	echo "<tr>";

	echo "<td><b>Wpłaty:</b></td>";
	echo "<td align=right><b>".u_Cena($wart)."</b></td>";
	echo "</tr>";

	echo "</table>\n";


