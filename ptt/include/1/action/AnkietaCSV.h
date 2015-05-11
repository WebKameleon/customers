<?
	global $pytanie;
	include_once("$INCLUDE_PATH/winiso.h");

	$wynik="pytanie;kurs;obiekt;rok;miejsc;pedagog;odpowiedzi;średnia ocena\n";

	$where=$pytanie?"WHERE o_p_id=$pytanie":"";

	$sql="
			SELECT p_symbol,taniec,zaawansowanie,obiekt,pomieszczenie,rok,prowadzacy,
					sum(miejsc) AS m,count(p_id) AS o, avg(o_wart) AS s
			FROM odpowiedzi
			LEFT JOIN pytania ON o_p_id=p_id
			LEFT JOIN zapisy_all ON o_zapis=zapisy_all.id
			LEFT JOIN kursy ON kurs_id=kursy.id
			$where
			GROUP BY p_symbol,taniec,zaawansowanie,obiekt,pomieszczenie,rok,prowadzacy
			ORDER BY rok,p_symbol,taniec,prowadzacy
	";


	$res=pg_Exec($db,$sql);
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		
		$prowadzacy=stripslashes($prowadzacy);
		$taniec=stripslashes($taniec);

		$prowadzacy=str_replace(';',',',$prowadzacy);
		$taniec=str_replace(';',',',$taniec);

		$s=round($s,3);
		$s=str_replace('.',',',$s);

		$wynik.="$p_symbol;$taniec ($zaawansowanie);$obiekt ($pomieszczenie);$rok;$m;$prowadzacy;$o;$s\n";
	}

	if ($KAMELEON_MODE) 
	{
		echo "<pre>$wynik</pre>";
	}
	else
	{
		if ($pytanie)
		{
			$query="SELECT * FROM pytania WHERE p_id=$pytanie";
			parse_str(query2url($query));
			$p_symbol="-$p_symbol";
		}

		$wynik=iso2win($wynik);
		Header('Content-Type: application/csv');
		Header('Content-Length: '.strlen($wynik));
		Header("Content-disposition: attachment; filename=ankieta$p_symbol.csv");
		echo $wynik;
		exit();


	}

?>