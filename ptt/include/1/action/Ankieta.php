<?php


	$zapis=$_REQUEST['zapis'];
	$zapisy=$_REQUEST['zapisy'];
	$ankieta=$_REQUEST['ankieta'];
	
	
	if (!is_array($ankieta)) $ankieta=array();

	if (count($ankieta)<$zapisy)
	{
		$error=sysmsg('Prosimy odpowiedzieć na wszystkie pytania');
		return;
	}

	if (!$zapis) return;
	
	$klient_id=0;

	$query="SELECT klient_id FROM zapisy_all WHERE id=$zapis";
	parse_str(query2url($query));
	if ($klient_id!=$AUTH_ID) return;

	$ocena=0;
	$query="SELECT avg(o_wart) AS ocena FROM odpowiedzi WHERE o_zapis=$zapis";
	parse_str(query2url($query));

	if ($ocena) return;

	
	foreach ($ankieta AS $p=>$o)
	{
		$query="INSERT INTO odpowiedzi (o_p_id,o_zapis,o_wart,o_data) VALUES ($p,$zapis,$o,CURRENT_DATE)";
		pg_exec($db,$query);
	}

	$sysinfo="Dziękujemy";


