<form method=post action=<?php echo $next?>>
<div class="df">
<?php	


	$daty=array( 
		'15-08-2015',
		'16-08-2015',
		'17-08-2015',
		'18-08-2015',
		'19-08-2015',
		'20-08-2015',
		'21-08-2015',
		'22-08-2015',
		'23-08-2015',
		'24-08-2015',
		);



	$query="SELECT * FROM akademiki ORDER BY nazwa";


	$query="SELECT * FROM zapisy_a
		WHERE klient_id=$AUTH_ID";
	$res=pg_Exec($db,$query);


	$akademik_id=0;	
	parse_str(query2url($query));
	$zapisy_id=$id;
	$cena_zap=$cena;

	$style="";
	if ($ilosc==0) $style="style='text-decoration:line-through'";


	$query="SELECT * FROM akademiki ORDER BY nazwa";
	$res=pg_Exec($db,$query);
	
	echo "<div class=\"col-xs-3\">";
	echo sysmsg("wybierz").": <select  class=\"form-control input-sm\" name=akademik_id><option value='0'>".sysmsg("Wybierz akademik")."</option>";
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$sel=($akademik_id==$id)?" selected":"";
		echo "<option value='$id'$sel>$nazwa, ".sysmsg("cena").": $cena/".sysmsg("dobę")."</option>";
	}
	echo "</select></div>,\n";
	
	echo "<div class=\"col-xs-3\">";
	echo sysmsg("od").": <select class=\"form-control input-sm\" name=d_przyjazdu><option value=''>".sysmsg("Data przyjazdu")."</option>";
	for ($i=0;$i<count($daty);$i++)
	{
		$data=$daty[$i];
		$data_sql=FormatujDateSQL($data);
		$sel=($data_sql==$d_przyjazdu)?" selected":"";
		echo "<option value='$data_sql'$sel>$data</option>";
	}
	echo "</select></div>,\n";
	
	echo "<div class=\"col-xs-3\">";
	echo sysmsg("ile dni").": <input class=\"form-control input-sm\" name=ilosc size=2 value='$ilosc'></div>";
	$zapisz_button = sysmsg("Zapisz");
?>
</div>
<p align=right>
<input type=submit value="<?php echo $zapisz_button ?>" class=btn>
<?php
	$button_text = sysmsg("Rezygnacja");
	if ($akademik_id)
		echo "<input type=btn class=button value=\"$button_text\"
			onClick=\"ilosc.value=0; submit()\">";
?>
</p>
<input type="hidden" name="action" value="ZapiszAkademik">
</form>
