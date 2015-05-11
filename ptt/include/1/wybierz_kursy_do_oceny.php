<?php

	$znak="?";
	if (strstr($next,$znak)) $znak="&";

	$query="SELECT *,date_part('Year',d_zgloszenia) AS ro
			FROM zapisy_all
			WHERE klient_id=$AUTH_ID AND ilosc>0
			ORDER BY d_zgloszenia DESC,id";
	$res=pg_Exec($db,$query);

	echo "<table class=\"tabelka table table-responsive\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
	echo "<thead><tr>
		<td class=\"col-md-4 col-sm-4\">".sysmsg("Nazwa / Poziom")."</td>
		<td class=\"col-md-4 col-sm-4\">".sysmsg("Rok")."</td>
		<td class=\"col-md-4 col-sm-4\">".sysmsg("Prowadzący")."</td>
		<td class=\"col-md-4 col-sm-4\" align=right>".sysmsg("Ocena")."</td>
		</tr></thead>
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
			//$style="style='text-decoration:line-through'";
			//$cla="c3";
		}

		
		$href="$next${znak}zapis=$zapisy_id";

		$query="SELECT *,koniec<=CURRENT_DATE AS mozna FROM kursy WHERE id=$kurs_id";
		parse_str(query2url($query));

		$taniec=stripslashes($taniec);
		$prowadzacy=stripslashes($prowadzacy);
		$g=substr($godz_od,0,5);

		$dis=($mozna=='f')?'disabled':'';
		if (is_pttAdmin()) $dis='';


		echo "<tr $dis>";

		echo "<td class='$cla'>";
		echo "$taniec<br>$zaawansowanie ";
		echo "</td>";

		echo "<td nowrap class='$cla'>";
		echo $ro;
		echo "</td>";


		echo "<td class='$cla'>";
		echo "$prowadzacy ($g)";
		echo "</td>";

		echo "<td nowrap class='$cla' align=right>&nbsp;";

		$ocena=0;
		$query="SELECT avg(o_wart) AS ocena FROM odpowiedzi WHERE o_zapis=$zapisy_id AND o_wart>0";
		parse_str(query2url($query));
		$ocena=round($ocena,1);

		if ($ocena || strlen($dis)) echo $ocena;
		else
		{
			echo "<a href='$href' $style>".sysmsg("Oceń kurs")."</a>";
		}

		echo "</td>";

		echo "</tr>";
	}
	echo "</table>";


