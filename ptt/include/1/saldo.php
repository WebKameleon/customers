<?php
	if (!$AUTH_ID) return;

	$query="SELECT cena,kurs_id FROM zapisy 
		WHERE klient_id=$AUTH_ID AND ilosc>0";
	$res=pg_Exec($db,$query);
	

	echo "<table class=\"tabelka table table-responsive table-striped\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tbody>";

	$saldo=0;
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$query="SELECT taniec AS nazwa_tanca ,cykl FROM kursy WHERE id=$kurs_id";
		parse_str(query2url($query));
		$nazwa_tanca=stripslashes($nazwa_tanca);

		$cena*=-1;
		$saldo+=$cena;

		echo "<tr>";

		echo "<td>";
		echo sysmsg($nazwa_tanca)."<br>$CYKLE[$cykl]";
		echo "</td>";

		echo "<td align=right valign=top nowrap>";
		echo u_Cena($cena);
		echo "</td>";
	
		echo "</tr>";
		
	}	


	$znizka=znizka($AUTH_ID);
	if ($znizka)
	{
		$saldo+=$znizka;
		echo "<tr>";
		echo "<td>";
		echo sysmsg("Zniżka");
		echo "</td>";
		echo "<td align=right valign=top nowrap>";
		echo u_Cena($znizka);
		echo "</td>";
		echo "</tr>";
	}

	$query="SELECT nazwa,zapisy_a.cena,ilosc FROM zapisy_a,akademiki
		 WHERE klient_id=$AUTH_ID AND ilosc>0
		 AND zapisy_a.akademik_id=akademiki.id";

	$res=pg_Exec($db,$query);
	
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));

		$cena*=-1;
		$saldo+=$cena*$ilosc;

		echo "<tr>";

		echo "<td>";
		echo sysmsg("Akademik")."<br>$nazwa<br>$ilosc dni";
		echo "</td>";

		echo "<td align=right valign=top nowrap>";
		echo u_Cena($cena*$ilosc);
		echo "</td>";
	
		echo "</tr>";
		
	}	



	$query="SELECT kwota,d_wplaty FROM wplaty
		WHERE klient_id=$AUTH_ID ";

	$res=pg_Exec($db,$query);


	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));

		$saldo+=$kwota;

		echo "<tr>";

		echo "<td>";
		echo sysmsg("wpłata")."<br>".FormatujDate($d_wplaty);
		echo "</td>";

		echo "<td align=right valign=top nowrap>";
		echo u_Cena($kwota);
		echo "</td>";
	
		echo "</tr>";
		
	}	



	echo "</tbody><tfoot><tr>";
	echo "<td>";
	echo sysmsg("RAZEM DO")." ";
	echo ($saldo>0)?sysmsg("ZWROTU"):sysmsg("ZAPŁATY");
	echo ":";
	echo "</td>";
	echo "<td align=right>";
	echo ($saldo>0)?u_Cena($saldo):u_Cena(-1*$saldo);
	echo "</td>";
	echo "</tr></tfoot>";

	echo "</table>\n";

