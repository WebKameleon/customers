<?php
	foreach (array('new_godz_od','new_godz_do','new_obiekt','new_pomieszczenie','new_prowadzacy','new_miejsc','new_taniec','new_cena','new_zaawansowanie') AS $k ) $$k=$_POST[$k];

	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;
	if (!$kurs) return;

	$query="SELECT * FROM kursy,obiekty 
		 WHERE kursy.id=$kurs
		 AND kursy.obiekt=obiekty.kod";

	parse_str(query2url($query));

	$godz_od=substr($godz_od,0,5);
	$godz_do=substr($godz_do,0,5);

	$new_godz_od=trim($new_godz_od);
	$new_godz_do=trim($new_godz_do);

	if (strlen($new_godz_od)==4) $new_godz_od="0$new_godz_od";
	if (strlen($new_godz_do)==4) $new_godz_do="0$new_godz_do";

	if ($new_taniec!=$taniec)
	{
		$sql_zmiany.=",taniec='$new_taniec'";
		$zmiany.="Zajęcia: $taniec => $new_taniec - $new_zaawansowanie\n";
	}

	if ($new_zaawansowanie!=$zaawansowanie)
	{
		$sql_zmiany.=",zaawansowanie='$new_zaawansowanie'";
	}

	if ($new_cena!=$cena)
	{
		$sql_zmiany.=",cena=$new_cena";
		$zmiany.="Cena: $cena => $new_cena\n";
	}

	if ($new_miejsc!=$miejsc)
	{
		$sql_zmiany.=",miejsc=$new_miejsc";
	}

	if ($new_obiekt!=$obiekt)
	{
		$sql_zmiany.=",obiekt='$new_obiekt',pomieszczenie='$new_pomieszczenie'";
		$query="SELECT nazwa AS new_nazwa FROM obiekty WHERE kod='$new_obiekt'";
		parse_str(query2url($query));
		$zmiany.="Lokalizacja: $nazwa => $new_nazwa / sala $new_pomieszczenie\n";
	}

	if ($new_pomieszczenie!=$pomieszczenie && $new_obiekt==$obiekt)
	{
		$sql_zmiany.=",pomieszczenie='$new_pomieszczenie'";
		$zmiany.="Pomieszczenie sala $pomieszczenie => sala $new_pomieszczenie\n";
	}

	if ($new_godz_od!=$godz_od || $new_godz_do!=$godz_do)
	{
		$sql_zmiany.=",godz_od='$new_godz_od',godz_do='$new_godz_do'";
		$zmiany.="Godziny: $godz_od-$godz_do => $new_godz_od-$new_godz_do\n";
	}

	if ($new_prowadzacy!=$prowadzacy)
	{
		$sql_zmiany.=",prowadzacy='$new_prowadzacy'";
		$zmiany.="Prowadzšcy: $prowadzacy => $new_prowadzacy\n";
	}



	//echo nl2br($sql_zmiany);	return;


	if (strlen($sql_zmiany))
	{
		$query="UPDATE kursy SET id=$kurs $sql_zmiany
			 WHERE id=$kurs";

		if (pg_Exec($db,$query))
		{
			$sendmail_action="ZmianaKursu";
			$mailto="\$mailfrom";
	
			$query="SELECT email FROM zapisy,klienci 
				 WHERE ilosc>0 AND kurs_id=$kurs
				 AND klient_id=klienci.id
				 AND email IS NOT NULL
				 AND email<>''";

			$result=pg_Exec($db,$query);
			for ($i=0;$i<pg_NumRows($result);$i++)
			{
				parse_str(pg_ExplodeName($result,$i));
				$mailbcc[]=$email;
			}
			if (pg_NumRows($result)) 
			{
				$action="SendMail";
				$sysinfo="Wysłano ".pg_NumRows($result)." maili";
			}			
		}
	}

