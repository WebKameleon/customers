<?php
	$taniec=isset($_REQUEST['taniec'])?$_REQUEST['taniec']:'';

	$taniec=addslashes(stripslashes($taniec));

	$query="SELECT * FROM kursy 
		WHERE taniec='$taniec' AND rok=$C_ROK
		ORDER BY cykl,godz_od,obiekt";

	$taniec=stripslashes($taniec);

	$res=pg_Exec($db,$query);

	if (pg_NumRows($res)) 
	{
		echo "<h2>".sysmsg($taniec)."</h2>";
		echo "<table class=\"tabelka table table-responsive table-striped\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";

		echo "<thead><tr>
			<td>".sysmsg("Poziom")."</td>
			<td>".sysmsg("Godziny")."</td>
			<td>".sysmsg("Prowadzący")."</td>
			<td align=right>".sysmsg("Cena")."</td>
			<td>".sysmsg("Miejsc")."</td>
			<td>".sysmsg("Zapis")."</td>
			</tr></thead>
			<tbody>";
	}


	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));

		$prowadzacy=ereg_replace(",","<br>",$prowadzacy);


		$query="SELECT sum(ilosc) AS zapisani FROM zapisy WHERE kurs_id=$id";
		parse_str(query2url($query));

		$wolne=$miejsc-$zapisani;

		if ($wolne<0 && !is_pttAdmin()) $wolne=0;


		$p=urlencode($prowadzacy);
		$href="$next${znak}kurs=$id";
		$prow="$more${znak}prow=$p";

		//if ($lang!='i') $prowadzacy=unpolish($prowadzacy);
		
		if ($old_cykl!=$cykl)
		{
			echo "<tr>";
			echo "<td class='c2 head-title' colspan=6 align=center>";
			//W 2002 było tak
			//echo "<b>CYKL $cykl</b> - kursy odbywające się w terminie $CYKLE[$cykl]";
			echo "<b>".sysmsg("termin")." ".$CYKLE[$cykl]."</b>";
			echo "</td>";

			echo "</tr>";
			$old_cykl=$cykl;
		}

		echo "<tr>";

		echo "<td class='c3'>";
		echo "<a href='$href'>";
		echo sysmsg($zaawansowanie);
		echo "</a>";
		echo "</td>";



		echo "<td class='c3'>";
		echo "<a href='$href'>";
		echo substr($godz_od,0,5)."-".substr($godz_do,0,5);
		echo "</a>";
		echo "</td>";


		echo "<td class='c3'>";
		echo "<a href='$prow'>";
		echo "$prowadzacy";
		echo "</a>";
		echo "</td>";


		echo "<td class='c3' align=right>";
		echo "<a href='$href'>";
		echo u_cena($cena);
		echo "</a>";
		echo "</td>";

		echo "<td class='c3'>";
		echo "<a href='$href'>";
		echo "$wolne";
		echo "</a>";
		echo "</td>";

		echo "<td class='c3'>";
		echo "<a href='$href' style='letter-spacing:-1'>";
		echo sysmsg("chce sie zapisac");
		//echo "<img src='$UIMAGES/b-zapis.gif' border=0>";
		echo "</a>";
		echo "</td>";

		echo "</tr>";
	}


	if (pg_NumRows($res)) 
	{
		echo "</tbody></table>\n";
	}


