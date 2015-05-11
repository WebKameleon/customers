<?php
	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;
	

	if (!$AUTH_ID || !$kurs) return;
	
	
	$query="SELECT *,date_part('epoch',godz_od) AS od 
		 FROM kursy WHERE id=$kurs";
	parse_str(query2url($query));

	if ($C_ROK!=$rok)
	{
		$error=sysmsg("Sprzedaż w tym terminie została zakończona");
		return;
	}

	$query="SELECT sum(ilosc) AS zap FROM zapisy WHERE kurs_id=$kurs";
	parse_str(query2url($query));

	$wolne=$miejsc - $zap;

	if (saleOff($cykl) && !is_pttAdmin()) $error=sysmsg("Sprzedaż w tym terminie została zakończona");
	

	for ($c=0;$c<count($CYKLE_NAKLAD);$c++)
	{
		$_cykl=explode(":",$CYKLE_NAKLAD[$c]);
		if ($cykl==$_cykl[0] || $cykl==$_cykl[1])
			$zapytaj_o_cykle.=($cykl==$_cykl[0])?",'$_cykl[1]'":",'$_cykl[0]'";
	
	}
	if (strlen($zapytaj_o_cykle))
	{
		$zapytaj_o_cykle="'$cykl'$zapytaj_o_cykle";
		$query="SELECT date_part('epoch',godz_od) AS _od, taniec AS _taniec, cykl AS _cykl 
			 FROM zapisy,kursy 
			 WHERE klient_id=$AUTH_ID AND kursy.id=zapisy.kurs_id 
			 AND ilosc>0 AND cykl IN ($zapytaj_o_cykle)";

		$res=pg_Exec($db,$query);
		for ($i=0;$i<pg_NumRows($res);$i++)
		{
			parse_str(pg_ExplodeName($res,$i));
			$roznica=round(abs($od-$_od)/60);

			if ($roznica<60)
			{
				$error=sysmsg("Taniec pokrywa się z")." \'$_taniec\' $CYKLE[$_cykl]";
				//$sysinfo=sysmsg("Taniec pokrywa się z")." \'$_taniec\' $CYKLE[$_cykl]";
				break;
			}
		}
	}


	if (strlen($error)) return;
	
	if ($wolne>0 || is_pttAdmin() )
	{
		$query="INSERT INTO zapisy (klient_id,kurs_id,d_zgloszenia,ilosc,cena,ip_zgloszenia)
			 VALUES ($AUTH_ID,$kurs,CURRENT_DATE,1,$cena,'".$_SERVER['REMOTE_ADDR']."')";
	}
	else $error=sysmsg("Brak wolnych miejsc");


	

	//echo nl2br($query); return;
	if (!strlen($error)) if (pg_Exec($db,$query)) 
	{
		$query="SELECT *,CURRENT_DATE+$ile_dajemy_czasu AS termin
			FROM klienci WHERE id=$AUTH_ID";
		parse_str(query2url($query));

		$query="SELECT * FROM obiekty WHERE kod='$obiekt'";
		parse_str(query2url($query));

		$godz=substr($godz_od,0,5);
		$pierwsza_rata=ceil(0.5*$cena);

		$termin=FormatujDate($termin);
		$mailto=$email;

		$sendmail_action="ZapisanoSie";
		$action="SendMail";
		$sysinfo=sysmsg("Zarejestrowano Cię na liście");
	}


