<?php
	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;
	if (!$kurs) return;

	$query="SELECT * FROM kursy WHERE id=$kurs;";
	parse_str(query2url($query));
	$cena+=0;
	$miejsc+=0;

	$undo_q="INSERT INTO kursy (id,cykl,godz_od,godz_do,obiekt,pomieszczenie,
						    taniec,zaawansowanie,prowadzacy,miejsc,cena)
				VALUES ($kurs,'$cykl','$godz_od','$godz_do','$obiekt','$pomieszczenie',
						    '$taniec','$zaawansowanie','$prowadzacy',$miejsc,$cena);\n";


	$query="DELETE FROM kursy WHERE id=$kurs;";
	

	//echo nl2br($query)."<br><b>UNDO:</b> ".nl2br($undo_q); return;

	if (pg_Exec($db,$query)) 
	{
		$sysinfo="Usunięto kurs";
		$godz=substr($godz_od,0,5);
		undo($undo_q,"Usunięto <b>$taniec</b> ($obiekt / $cykl,$godz)");
		$kurs=0;
	}
