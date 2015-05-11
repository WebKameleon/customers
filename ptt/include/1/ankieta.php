<?php
	$zapis=isset($_REQUEST['zapis'])?$_REQUEST['zapis']:0;
	
	$zapis+=0;
	$klient_id=0;

	$query="SELECT *,date_part('Year',d_zgloszenia) AS ro 
			FROM zapisy_all WHERE id=$zapis";
	parse_str(query2url($query));
	
	if ($klient_id!=$AUTH_ID) return;
	if ($ilosc<=0) return;

	$ocena=0;
	$query="SELECT avg(o_wart) AS ocena FROM odpowiedzi WHERE o_zapis=$zapis AND o_wart>0";
	parse_str(query2url($query));


	$query="SELECT * FROM kursy WHERE id=$kurs_id";
	parse_str(query2url($query));
	
	$taniec=stripslashes($taniec);
	$prowadzacy=stripslashes($prowadzacy);

	$g=substr($godz_od,0,5);

	echo "<table cellspacing=0 cellpadding=3 border=1>";
	echo "
			<tr><td class='c1'><b>".sysmsg("Nazwa / Poziom")."</b></td>
				<td class='c2'>$taniec / $zaawansowanie </td></tr>
			<tr><td class='c1'><b>".sysmsg("Prowadzący")."</b></td>
				<td class='c2'>$prowadzacy</td></tr>
			<tr><td class='c1'><b>".sysmsg("Rok")."</b></td>
				<td class='c2'>$ro ($g)</td></tr>
			";

	if ($ocena)
	{
		$ocena+=0;
		echo "
			<tr><td class='c1' align=right><b>".sysmsg("Ocena")."</b></td>
			<td class='c2'>$ocena</td></tr>";
	}

	echo "</table>";

	if ($ocena) return;

	$query="SELECT max(p_skala_do) AS s_do, min(p_skala_od) AS s_od FROM pytania WHERE p_r_od<=$ro AND p_r_do>=$ro ";
	parse_str(query2url($query));
	$query="SELECT * FROM pytania WHERE p_r_od<=$ro AND p_r_do>=$ro ORDER BY p_pri,p_id";
	$res=pg_Exec($db,$query);


	$skala=$s_do-$s_od+1;

?>
<form method="post" action="<?php echo $next?>">
<input type=hidden name=action value="Ankieta">
<input type=hidden name=zapis value="<?php echo $zapis?>">
<input type=hidden name=zapisy value="<?php echo pg_NumRows($res)?>">
<table cellspacing=0 cellpadding=3 border=1 width=100%>

<?php
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
			
		echo "
		<tr>
		<td class='c1' colspan=$skala><b>".stripslashes($p_pytanie)."</b></td>
		</tr>
		<tr>";

		for ($s=$s_od;$s<=$s_do ;$s++ )
		{
			echo "<td class='c2' align='center'>";
			if ($p_skala_do>=$s && $p_skala_od<=$s)
			{
				echo $s;
				echo "<input type=radio name='ankieta[$p_id]' value=$s style='border:0px'>";
			}
			else echo '&nbsp;';
		}
	}
?>

</table>

<input type=submit value="<?php echo sysmsg('Oceniam')?>" style="margin: 20 2" class=button>
</form>

