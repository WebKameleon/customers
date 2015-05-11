<?php
	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;



	$query="SELECT * FROM zapisy
		WHERE klient_id=$AUTH_ID
		ORDER BY d_zgloszenia,id";
	$res=pg_Exec($db,$query);

	echo "<table class=\"tabelka table table-responsive table-striped\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
	echo "<thead><tr>
		<td>".sysmsg("Nazwa / Poziom")."</td>
		<td>".sysmsg("Termin")."</td>
		<td>".sysmsg("Godziny")."</td>
		<td>".sysmsg("Prowadzący")."</td>
		<td align=right>".sysmsg("Cena")."</td>
		<td>&nbsp;</td>
		</tr>
		</thead>
		<tbody>";



	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$zapisy_id=$id;
		$cena_zap=$cena;

		$style="";
		$cla='c2';
		if ($ilosc==0) 
		{
			$cla="c3";
			$style="style='text-decoration:line-through'";
		}

		$query="SELECT * FROM kursy WHERE id=$kurs_id";
		parse_str(query2url($query));
	
	
		$taniec=stripslashes($taniec);
		$p=urlencode($prowadzacy);
		//if ($lang!='i') $prowadzacy=unpolish($prowadzacy);
		$href="$next${znak}kurs=$id#explore";
		$prow="$more${znak}prow=$p";

		echo "<tr>";

		echo "<td class='$cla'>";
		echo "<a href='$href' $style>";
		echo sysmsg($taniec).'<br>'.sysmsg($zaawansowanie);
		echo "</a>";
		echo "</td>";


		echo "<td class='$cla'>";
		echo "<a href='$href' $style>";
		echo "$CYKLE[$cykl]";
		echo "</a>";
		echo "</td>";

		echo "<td nowrap class='$cla'>";
		echo "<a href='$href' $style>";
		echo substr($godz_od,0,5)."-".substr($godz_do,0,5);
		echo "</a>";
		echo "</td>";


		echo "<td class='$cla'>";
		echo "$prowadzacy";
		echo "</td>";


		echo "<td nowrap class='$cla' align=right>";
		echo "<a href='$href' $style>";
		echo u_cena($cena_zap);
		echo "</a>";
		echo "</td>";


		echo "<td class='$cla' nowrap>";
		$alt = sysmsg("Wypisz się");
		if ($ilosc) 
		{
			echo "<a href='$href' $style>".sysmsg("information")."</a>";	
			echo "<br><a href='javascript:usun($zapisy_id)'>$alt</a>";
		}
		if (!$ilosc) echo sysmsg("$p_rezygnacji");
		echo "</td>";

		echo "</tr>";
	}


	$query="SELECT nazwa,adres,zapisy_a.cena,ilosc,d_przyjazdu
		 FROM zapisy_a,akademiki
		 WHERE klient_id=$AUTH_ID AND ilosc>0
		 AND zapisy_a.akademik_id=akademiki.id";
	$res=pg_Exec($db,$query);

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		echo "<tr><td colspan=5 class='c2'>";
		parse_str(pg_ExplodeName($res,$i));
		echo "<b>".sysmsg("Zakwaterowanie w akademiku")."</b>: $nazwa";
		echo "<br>".sysmsg("Adres").": $adres";
		$d_przyjazdu=FormatujDate($d_przyjazdu);

		echo "<br>".sysmsg("Od")." $d_przyjazdu, ".sysmsg("ilosc dni").": $ilosc"; 
		echo "<br>".sysmsg("Cena jednej doby").": ".u_cena($cena)."; ".sysmsg("razem").": ".u_cena($cena*$ilosc);
	
		echo "</td>";
		echo "<td class='c2'>
			<a href='javascript:usunAkademik()'>
			".sysmsg("REZYGNUJE")."
			</td></tr>";

	}


	echo "</table>\n";

	$uwaga="";
	for ($c=0;$c<count($CYKLE_NAKLAD);$c++)
	{
		$cykl=explode(":",$CYKLE_NAKLAD[$c]);
		$query="SELECT 
				date_part('epoch',godz_od) AS od,
				date_part('epoch',godz_do) AS do,
				obiekty.grupa,taniec,kurs_id
			FROM zapisy,kursy,obiekty
			WHERE klient_id=$AUTH_ID
			AND ilosc>0
			AND kurs_id=kursy.id AND kursy.obiekt=obiekty.kod
			AND (cykl='$cykl[0]' OR cykl='$cykl[1]')
			ORDER BY godz_od";
		$res=pg_Exec($db,$query);

		$grupa_poprzednia=0;
		$koniec_poprzedni=0;
		$kurs_poprzedni=0;
		for ($i=0;$i<pg_NumRows($res);$i++)
		{
			parse_str(pg_ExplodeName($res,$i));
			
			
			$przerwa=($od-$koniec_poprzedni)/60;
			
			if ($taniec==$taniec_poprzedni || $overlaping[$kurs_id]==$kurs_poprzedni) $przerwa=100;

			if ($przerwa<0) 
			{
				$alert_content = sysmsg("Zajęcia").": '$taniec' ".sysmsg("i")." '$taniec_poprzedni' ".sysmsg("się czasowo pokrywają")." !";
				$uwaga.="<br>$alert_content
					<script>alert(\"$alert_content\");</script>";
				$overlaping[$kurs_id]=$kurs_poprzedni;
				$overlaping[$kurs_poprzedni]=$kurs_id;

			}
			if ($przerwa>0 && $przerwa<$czas_przejscia+1 && $grupa!=$grupa_poprzednia)
			{
				$uwaga.="<br>".sysmsg("Czas przejscia między obiektami, w których odbywaja się")." '$taniec' ".sysmsg("i")." '$taniec_poprzedni' ".sysmsg("wynosi ponad $czas_przejscia minut. Jesteś zapisany(a) na własna odpowiedzialność.");
				$overlaping[$kurs_id]=$kurs_poprzedni;
				$overlaping[$kurs_poprzedni]=$kurs_id;

			}

			$grupa_poprzednia=$grupa;
			$koniec_poprzedni=$do;
			$taniec_poprzedni=$taniec;
			$kurs_poprzedni=$kurs_id;
			//echo "$cykl[0],$cykl[1]: $od -> $do ($grupa) $przerwa<br>";
		}	
	}
	
	if (strlen($uwaga)) echo "<u>".sysmsg("Uwaga").":</u>$uwaga<br><br>";


	if ($kurs) 
	{
		echo "<hr size=1><a name='explore'>";
		$showonly=1;
		include ("$INCLUDE_PATH/termin.php");
	}


?>
<form method="post" name="usunakademik" action="<?php echo $self?>">
	<input type=hidden name=ilosc value="0">
	<input type=hidden name=action value="ZapiszAkademik">
</form>

<form method="post" name="usuwanie" action="<?php echo $self?>">
	<input type=hidden name=zapis value="0">
	<input type=hidden name=action value="UsunZapis">
</form>

<script>
  function usun(id)
  {
	if (confirm('<?php echo sysmsg("Czy chcesz wypisać się z kursu") ?> ?'))
	{
		document.usuwanie.zapis.value=id;
		document.usuwanie.submit();
	}
  }

  function usunAkademik(id)
  {
	if (confirm('<?php echo sysmsg("Czy chcesz zrezygnować z akademika") ?> ?'))
		document.usunakademik.submit();
  }

</script>
