<?php

	if (!isset($kurs)) $kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:0;

	$kurs+=0;
	if (!$kurs) return;

	$query="SELECT * FROM kursy 
		WHERE id=$kurs AND rok=$C_ROK";
	parse_str(query2url($query));

	$taniec=stripslashes($taniec);

	$query="SELECT sum(ilosc) AS zapisani FROM zapisy WHERE kurs_id=$id";
	parse_str(query2url($query));

	$wolne=$miejsc-$zapisani;

	if ($wolne<=0 && !$showonly) echo sysmsg("Brak wolnych miejsc, przepraszamy")."!";
	if ($wolne<=0 && !$showonly && !is_pttAdmin() ) return;



	echo "<table class=\"table table-responsive table-striped\" cellspacing=0 cellpadding=3 border=0 >";

	echo "<tr>";
	echo "<td class='c2' valign=top>";
	echo "<b>".sysmsg("Wybrany taniec").":</b>";
	echo "</td>";
	echo "<td class='c3'>";
	echo "<b>".sysmsg($taniec)."</b>";
	if (strstr(strtolower($taniec),"tango") && !strstr(strtolower($taniec),"tangos"))
	{
		echo "<br>".sysmsg("UWAGA")."!<br>".sysmsg("Zapisy wylacznie parami (na kurs musisz zglosic sie z partnerem/partnerka)")." !";
	}
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td class='c2'>";
	echo sysmsg("Poziom").":";
	echo "</td>";
	echo "<td class='c3'>";
	echo sysmsg("$zaawansowanie");
	echo "</td>";
	echo "</tr>";


	echo "<tr>";
	echo "<td class='c2'>";
	echo sysmsg("Termin").":";
	echo "</td>";
	echo "<td class='c3'>";
	echo "$CYKLE[$cykl]";
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td class='c2'>";
	echo sysmsg("Godziny").":";
	echo "</td>";
	echo "<td class='c3'>";
	echo substr($godz_od,0,5)."-".substr($godz_do,0,5);
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td class='c2'>";
	echo sysmsg("Prowadzący").":";
	echo "</td>";
	echo "<td class='c3'>";
	//if ($lang!='i') $prowadzacy=unpolish($prowadzacy);
	echo "$prowadzacy";
	echo "</td>";
	echo "</tr>";


	echo "<tr>";
	echo "<td class='c2'>";
	echo sysmsg("Cena").":";
	echo "</td>";
	echo "<td class='c3'>";
	echo u_cena($cena);
	echo "</td>";
	echo "</tr>";

	$query="SELECT * FROM obiekty 
		WHERE kod='$obiekt'";
	parse_str(query2url($query));

	echo "<tr>";
	echo "<td valign=top class='c2'>";
	echo sysmsg("Adres").":";
	echo "</td>";
	echo "<td class='c3'>";
	echo "$nazwa<br>$adres<br>".sysmsg("Sala").": $pomieszczenie";
	echo "</td>";
	echo "</tr>";

	echo "</table>";


