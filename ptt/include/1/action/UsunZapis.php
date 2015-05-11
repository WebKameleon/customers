<?php
	$zapis=isset($_REQUEST['zapis'])?$_REQUEST['zapis']:0;
	
	if (saleOff($cykl) && !is_pttAdmin())
	{
		$error='Niestety po terminie';
		return;
	}

	if (!$AUTH_ID || !$zapis) return;


	$query="UPDATE zapisy SET ilosc=0, 
				d_rezygnacji=CURRENT_DATE ,
				p_rezygnacji='rezygnacja' 
			WHERE klient_id=$AUTH_ID AND id=$zapis";
	

	//echo nl2br($query); return;
	if (pg_Exec($db,$query)) $sysinfo="Rezygnacja przyjęta";
