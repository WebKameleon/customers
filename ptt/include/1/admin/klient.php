<?php
	$klient+=0;
	if (!$klient) return;

	$kli_id_pos=strpos($REQUEST_URI,"klient=");
	$kli_id_end=strpos(substr($REQUEST_URI,$kli_id_pos),"&");
	$all_link=substr($REQUEST_URI,0,$kli_id_pos+7);
	$all_link.="0";
	if ($kli_id_end) $all_link.=substr($REQUEST_URI,$kli_id_end+$kli_id_pos);

	echo "<hr size=1 color=$COLORS[0]>";
	echo "<a href='$all_link'>WSZYSCY</a>";
	echo "<hr size=1 color=$COLORS[1]>";


	$k_sql="SELECT * FROM klienci WHERE id=$klient";
	
	parse_str(query2url($k_sql));
	$_data_p = $d_przyjazdu;
	
	echo nl2br("<u><b>Dane klienta:</b></u> [login:<b>$login/$pass</b>]
			&nbsp; <b>$imie $nazwisko</b>
			&nbsp; $kod $miasto; $adres
			&nbsp; tel.: $telefon, $gsm
			&nbsp; e-mail: <a href='mailto:$email'>$email</a>
			");


	$k_sql="SELECT *, zapisy.cena AS z_cena FROM zapisy,kursy 
		WHERE klient_id=$klient 
		AND kursy.id=zapisy.kurs_id
		ORDER BY cykl,taniec";
	$res=pg_Exec($db,$k_sql);

	if (pg_NumRows($res)) 
		echo "<hr size=1 color=$COLORS[1]><u><b>Zapisał(a) się na następujące techniki</b>:</u><br>";

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$k_sql="SELECT nazwa,adres FROM obiekty WHERE kod='$obiekt'";
		parse_str(query2url($k_sql));
		$godz=substr($godz_od,0,5);
		$do=substr($godz_do,0,5);
		if ($i) echo "<br>";
		if (!$ilosc) echo "<span title='Rezygnacja ".formatujdate($d_rezygnacji)."' style='text-decoration:line-through'>";
		
		echo "&nbsp; * $taniec $zaawansowanie ($prowadzacy), $CYKLE[$cykl]($cykl),$godz-$do 
			<br>&nbsp;&nbsp;&nbsp;&nbsp; $nazwa, $adres (sala $pomieszczenie)";

		if (!$ilosc) echo "</span>";

		if ($ilosc)
		echo "<FORM METHOD=POST ACTION=\"$more\">
				<INPUT TYPE=\"hidden\" name=\"kurs[nazwa]\" value=\"$taniec $zaawansowanie\">
				<INPUT TYPE=\"hidden\" name=\"kurs[cena]\" value=\"$z_cena\">
				<INPUT TYPE=\"hidden\" name=\"kurs[k_id]\" value=\"$klient\">
				<INPUT TYPE=\"hidden\" name=\"kurs[id]\" value=\"$kurs_id\">
				<INPUT TYPE=\"submit\" value=\"Zmień cenę\">
				</FORM>";
	}

	
	$k_sql="SELECT * FROM zapisy_a,akademiki 
		WHERE klient_id=$klient AND ilosc>0
		AND akademiki.id=zapisy_a.akademik_id";
	$res=pg_Exec($db,$k_sql);

	

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$d_przyjazdu=FormatujDate($d_przyjazdu);
		echo "<br>";
		echo "&nbsp; > Akademik $nazwa, od $d_przyjazdu na $ilosc dni.";
		
	}

	$k_sql="SELECT * FROM wplaty WHERE klient_id=$klient 
		ORDER BY d_wplaty,id";
	$res=pg_Exec($db,$k_sql);

	if (pg_NumRows($res)) 
		echo "<hr size=1 color=$COLORS[1]><u><b>Wpłaty</b>:</u><br>";
	

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		if ($i) echo "<br>";
		echo "&nbsp; * <b>".u_cena($kwota)."</b>	$uwagi";

	}

	echo "<hr size=1 color=$COLORS[0]>";
	if (strlen($_data_p)) $d_przyjazdu = FormatujDate($_data_p);
	echo "<U><B>Data przyjazdu klienta:</B></U> : $d_przyjazdu";
	$dzisiaj = date("d-m-Y");
	echo "<br>
	<FORM METHOD=\"POST\" ACTION=\"$self\" name=\"dataform\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"admin/KlientZapiszDate\">
	<INPUT TYPE=\"hidden\" name=\"klient\" value=\"$klient\">
	Nowa data przyjazdu<br> <INPUT TYPE=\"text\" name=\"data_p\" value=\"$dzisiaj\">
	<INPUT TYPE=\"submit\" value=\"Zapisz datę\" class=\"button\">
	</FORM>
	";
	
