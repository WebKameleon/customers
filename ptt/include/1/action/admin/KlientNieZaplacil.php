<?php

	$zapis=$_REQUEST['zapis'];
	if (!$zapis) return;

	$query="SELECT ilosc,klient_id,kurs_id,d_zgloszenia 
			FROM zapisy
			WHERE id=$zapis;";
	parse_str(query2url($query));

	$undo_q="UPDATE zapisy SET ilosc=$ilosc, 
				d_rezygnacji=NULL ,
				p_rezygnacji=NULL 
			WHERE id=$zapis";


	$query="UPDATE zapisy SET ilosc=0, 
				d_rezygnacji=CURRENT_DATE ,
				p_rezygnacji='brak wpłaty' 
			WHERE id=$zapis";
	

	//echo nl2br($query); return;
	if (pg_Exec($db,$query)) 
	{
		$sysinfo="Anulowano zapis";
		$query="SELECT * FROM klienci WHERE id=$klient_id";
		parse_str(query2url($query));
		$query="SELECT taniec,cykl FROM kursy WHERE id=$kurs_id";
		parse_str(query2url($query));
		$zdnia=FormatujDate($d_zgloszenia);
		undo($undo_q,"Anulowano zgłoszenie $imie $nazwisko ($miasto) z dnia $zdnia na $taniec $cykl");

		$mailto=$email;
		$action="SendMail";
		$sendmail_action="AnulowanoZapis";
	}

