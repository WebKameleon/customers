<?php

	$szukaj=$_REQUEST['szukaj'];
	$offset=$_REQUEST['offset'];
	$klient=$_REQUEST['klient'];


	if ($klient)
	{
		include("$INCLUDE_PATH/admin/klient.php");

		$query=str_replace("WHERE","WHERE klienci.id=$klient AND",$query);
		
	}


	$frompos=strpos(strtolower($query),"from");

	if ($frompos)
	{
		$len=strlen($query)-$orderpos;
		$q1="SELECT count(*) AS ile ".substr($query,$frompos);
		$orderpos=strpos(strtolower($q1),"order");
		if ($orderpos) $q1=substr($q1,0,$orderpos);
		parse_str(query2url($q1));
	}
	$offset+=0;
	if ($size && $ile>$size && $offset>=0)
	{
		$query.="\nLIMIT $size OFFSET $offset";
	}

	$res=pg_Exec($db,$query);

	//echo nl2br(htmlspecialchars($query));


	//echo $costxt;
	$head=explode(":",$costxt);

	echo "<div class=\"table-responsive\"><table style=\"font-size:10px;\" class=\"table table-responsive table-striped\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";

	
	echo "<thead><tr>";
	for ($h=0;$h<count($head);$h++) echo "<td>$head[$h]</td>";
	echo "</tr></thead><tbody>";

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));

		$query="SELECT sum(kwota) AS wplaty FROM wplaty 
			WHERE klient_id=$klient_id ";
		parse_str(query2url($query));

		$query="SELECT sum(cena*ilosc) AS zapisy FROM zapisy 
			WHERE klient_id=$klient_id ";
		parse_str(query2url($query));

		$query="SELECT sum(cena*ilosc) AS akademik FROM zapisy_a
			WHERE klient_id=$klient_id ";
		parse_str(query2url($query));

		$query="SELECT cykl,sum(zapisy.cena) AS ceny FROM zapisy,kursy
			 WHERE klient_id=$klient_id AND ilosc>0
			 AND kursy.id=zapisy.kurs_id
			 AND cykl IN ('A','B','A1')
			 GROUP BY cykl";
		$res1=pg_Exec($db,$query);

		$znizka=znizka($klient_id);

		$shit=($wplaty<0.5*($zapisy-$znizka))?1:0;
			
		echo "<tr>";
		for ($h=0;$h<count($head);$h++) 
		{	
			$wynik="";
			$align="left";
			$wrap="";
			$lp=$i+1;
			
			$pola=explode("/",$head[$h]);
			for ($p=0;$p<count($pola);$p++) 
			{		
				switch (strtolower($pola[$p]))
				{
					case "lp":
						$wynik.=($lp+$offset);
						break;
					case "imię":
						$wynik.= "<a href='$next${znak}klient=$klient_id$more_href'>$imie</a>";
						break;
					case "nazwisko":
						$wynik.= "<a href='$next${znak}klient=$klient_id$more_href'>$nazwisko</a>";
						break;
					case "miasto":
						$wynik.= $miasto;
						break;
					case "adres":
						$wynik.="$adres<br>$kod $miasto";
						break;
					case "telefon":
						$wynik.="$telefon, $gsm";
						break;
					case "technika":
						$wynik.="$taniec / $obiekt ($cykl ".substr($godz_od,0,5).")";
						break;
					case "termin":
						$wynik.=FormatujDate($termin);
						$wrap="nowrap";
						break;
					case "zapisy":
						$wynik.=u_Cena($zapisy);
						$align="right";
						$wrap="nowrap";
						break;
					case "wpłaty":
						$wynik.=u_Cena($wplaty);
						$align="right";
						$wrap="nowrap";
						break;
					case "zniżka":
						$wynik.=u_Cena($znizka);
						$align="right";
						$wrap="nowrap";
						break;
					case "kwota":
						$wynik.=u_Cena($kwota);
						$align="right";
						$wrap="nowrap";
						break;
					case "akademik":
						$wynik.=u_Cena($akademik);
						$align="right";
						$wrap="nowrap";
						break;
					case "przyjazd":
						$wynik.=substr(FormatujDate($d_przyjazdu),0,5);
						break;
					case "dni":
						$wynik.=$ilosc;
						break;
					case "saldo":
						if ($shit) $wynik.="<font color=Red>";
						$wynik.=u_Cena($wplaty+$znizka-$zapisy-$akademik);
						if ($shit) $wynik.="</font>";
						$align="right";
						$wrap="nowrap";
						break;
					case "cena":
						$wynik.=strlen($zcena)?u_Cena($zcena):u_Cena($cena);
						$align="right";
						$wrap="nowrap";
						break;
					case "email":
					case "e-mail":
						$wynik.="<a href=mailto:$email>$email</a>";
						break;
					case "uwagi":
						$wynik.=nl2br($uwagi);
						break;
					case "klient_id":
					case "id":
						$wynik.=$klient_id;
						break;
					case "usuń zapis":
						$wynik.="<a href='$next${znak}action=admin/KlientNieZaplacil&zapis=$zapis_id&szukaj=$szukaj'>
							<img width=12 src='$IMAGES/ikona-smietnik-b.gif' border=0 
								alt='Usuń $taniec'></a>";
					
					default:
						@eval("\$wynik=\$".strtolower($pola[$p]).';');
			
				}
				$wynik.= "<br>";
			}
			echo "<td valign=top align='$align' $wrap>$wynik</td>";
		}
		echo "</tr>";


	}

	echo "</tbody></table></div>";


	if ($size && $ile>$size)
	{
		
		echo "<hr><table cellspacing=0 cellpadding=3 border=0 width=100%><tr>";
		if ($offset) 
		{
			$offs=$offset-$size;
			if ($offs>=0) echo "<td width=25%><a href='$next${znak}offset=$offs$more_href'> <<< poprzednia strona</a>";
		}
		else echo "<td width=25%>&nbsp;</td>";

		echo "<td width=50% align=center>";
		for ($pages=0;$pages<ceil($ile/$size);$pages++)
		{
			$o=$pages*$size;
			$link="$next${znak}offset=$o$more_href&szukaj=$szukaj";
			echo " <a href='$link'>";
			if ($o==$offset) echo "<font color=Red>";
			echo $pages+1;
			if ($o==$offset) echo "</font>";
			echo "</a> ";
		}
		echo "</td>";

		$nxt=$offset+$size;
		if ($nxt<=$ile)
		{
			echo "<td align=right width=25%><a href='$next${znak}offset=$nxt$more_href'> następna strona>>> </a>";

		}
		else echo "<td width=25%>&nbsp;</td>";
		
		echo "</tr></table>";
	}

