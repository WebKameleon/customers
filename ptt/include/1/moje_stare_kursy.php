<?php
	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;



	$query="SELECT *,date_part('Year',d_zgloszenia) AS ro
			FROM zapisy_arch
			WHERE klient_id=$AUTH_ID AND ilosc>0
			ORDER BY d_zgloszenia,id";
	$res=pg_Exec($db,$query);

	echo "<table class=\"table table-responsive table-striped\">";
	echo "<thead><tr>
		<td>".sysmsg("Nazwa / Poziom")."</td>
		<td>".sysmsg("Rok")."</td>
		<td>".sysmsg("Prowadzący")."</td>
		<td align=right>".sysmsg("Cena")."</td>
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
			//$style="style='text-decoration:line-through'";
			//$cla="c3";
		}

		$taniec=stripslashes($taniec);

		$query="SELECT * FROM kursy WHERE id=$kurs_id";
		parse_str(query2url($query));

		$p=urlencode($prowadzacy);
		$href="$next${znak}kurs=$id#explore";
		$prow="$more${znak}prow=$p";

		//if ($lang!='i') $prowadzacy=unpolish($prowadzacy);
		echo "<tr>";

		echo "<td class='$cla'>";
		echo sysmsg($taniec).'<br>'.sysmsg($zaawansowanie);
		echo "</td>";

		echo "<td nowrap class='$cla'>";
		echo $ro;
		echo "</td>";


		echo "<td class='$cla'>";
		echo "$prowadzacy";
		echo "</td>";

		echo "<td nowrap class='$cla' align=right>";
		echo u_cena($cena_zap);
		echo "</td>";

		echo "</tr>";
	}
	echo "</tbody></table>";


