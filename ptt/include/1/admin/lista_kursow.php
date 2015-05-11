<form action="<?php echo $next?>" method=post>
<select class="form-control" name=kurs onChange="submit()">
<option value="0"><?php echo sysmsg("Wybierz kurs tańca")?></option>
<?php
	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;


	$query="SELECT * FROM kursy WHERE rok=$C_ROK
		ORDER BY taniec,obiekt,cykl,godz_od";

	$res=pg_Exec($db,$query);

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$query="SELECT count(*) AS ile from zapisy WHERE kurs_id=$id AND ilosc>0";
		parse_str(query2url($query));

		$sel="";
		$godz=substr($godz_od,0,5);
		if ($kurs==$id) $sel="selected";
		echo " <option value='$id' $sel>";
		//echo "$obiekt,$pomieszczenie  |  $godz_od,$taniec,$cykl ($ile)";
		echo stripslashes("$taniec - $zaawansowanie | $obiekt,$pomieszczenie  |  $cykl,$godz [$ile]");

		echo "</option>\n";
	
		
	}


?>
</select>
</form>
<br>