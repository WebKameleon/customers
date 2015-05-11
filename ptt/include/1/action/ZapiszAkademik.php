<?php

	
	$akademik_id=$_REQUEST['akademik_id'];
	$d_przyjazdu=$_REQUEST['d_przyjazdu'];
	$ilosc=$_REQUEST['ilosc'];

	if (!$AUTH_ID ) return;

	$akademik_id+=0;
	$ilosc+=0;
	if (!$akademik_id) $ilosc=0;
	if (10!=strlen($d_przyjazdu)) $error=sysmsg("Podaj datę przyjazdu");

	
	$query="SELECT miejsc,cena FROM akademiki WHERE id=$akademik_id";
	parse_str(query2url($query));
	

	$query="SELECT count(*) AS c FROM zapisy_a WHERE klient_id=$AUTH_ID";
	parse_str(query2url($query));
	

	$query="SELECT count(*) AS zaj FROM zapisy_a WHERE ilosc>0 AND akademik_id=$akademik_id";
	parse_str(query2url($query));
	

	$query="SELECT count(*) AS zaj FROM zapisy_a 
			WHERE ilosc>0 
			AND akademik_id=$akademik_id
			AND klient_id<>$AUTH_ID
			AND (d_przyjazdu+ilosc < '$d_przyjazdu' OR
				'$d_przyjazdu' > d_przyjazdu-$ilosc)";
	
	if ($ilosc != 0)
	parse_str(query2url($query));

	//$error=$zaj; return;
	
	if ( $miejsc-$zaj<=0 ) $error=sysmsg("Brak wolnych miejsc");
	else
	{
		if ($c) $query="UPDATE zapisy_a 
				 SET ilosc=$ilosc,cena=$cena,akademik_id=$akademik_id,
				 d_przyjazdu='$d_przyjazdu'
				 WHERE klient_id=$AUTH_ID";
		else 	$query="INSERT INTO zapisy_a
				 (klient_id,akademik_id,cena,ilosc,
				  d_przyjazdu,d_zgloszenia)
				 VALUES
				 ($AUTH_ID,$akademik_id,$cena,$ilosc,
				  '$d_przyjazdu',CURRENT_DATE)";
	}

	if (!$ilosc) 
	{
		$query="DELETE FROM zapisy_a WHERE klient_id=$AUTH_ID";
		$error="";
	}
	
	

	//echo nl2br($query); return;
	if (!strlen($error)) if (pg_Exec($db,$query)) 
	{
		if ($ilosc) $sysinfo=sysmsg("Miejsce w akademiku zostało zarezerwowane");
	}


