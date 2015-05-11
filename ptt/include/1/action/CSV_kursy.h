<?
	include("$INCLUDE_PATH/sendmail2.h");
	include_once("$INCLUDE_PATH/winiso.h");
	global $plik,$DeleteBefore;

	if (!file_exists($plik)) $error="Nie wprowadzono pliku";
	if (!file_exists($plik)) return;

	
	$plik=file($plik);
	$new=0;
	$upd=0;
	$sql="";
	if ($DeleteBefore) 
	{
		$sql="DELETE FROM kursy WHERE rok=$C_ROK; DELETE FROM zapisy;\n";
		pg_Exec($db,$sql);
	}

	for ($l=1;$l<count($plik);$l++)
	{
		$linia=ereg_replace(";\"([^;]+)\";",";\\1;",$plik[$l]);
		$linia=ereg_replace("[\"]+","\"",$linia);
		$linia=ereg_replace("[\n\r]","",win2iso(addslashes(stripslashes(trim($linia)))));
		$k=explode(";",$linia);
		if ($k[0]=="") continue;

		$k[9]=ereg_replace(",",".",$k[9]); // cena
		$k[9]+=0;

		$cykl="";
		$where="cykl='$k[0]' AND obiekt='$k[3]' 
			 AND godz_od='$k[1]' AND godz_do='$k[2]'
			 AND pomieszczenie='$k[4]' AND rok=$C_ROK";

		$query="SELECT * FROM kursy 
			 WHERE $where LIMIT 1";
		parse_str(query2url($query));
		//echo nl2br($query);


		$query="SELECT count(*) AS c FROM obiekty WHERE kod='$k[3]'";
		parse_str(query2url($query));
		if (!$c) $error="Brak kodu obiektu ($k[3])";

		$k[5]=trim($k[5]);
		$k[6]=trim($k[6]);
		$k[7]=trim($k[7]);
		
		$sql="";

		if (!strlen($cykl) || $DeleteBefore)
		{
			$sql.="INSERT INTO kursy (cykl,godz_od,godz_do,obiekt,pomieszczenie,
						    taniec,zaawansowanie,prowadzacy,miejsc,cena,rok)
				VALUES ('$k[0]','$k[1]','$k[2]','$k[3]','$k[4]',
					 '$k[5]','$k[6]','$k[7]',$k[8],$k[9],$C_ROK);\n";
			$new++;
		}
		else
		{
			$changes="";
			if (toText($taniec)!=$k[5]) 
			{
				$changes.=",taniec='$k[5]'";
			}
			if (toText($zaawansowanie)!=$k[6]) 
			{
				$changes.=",zaawansowanie='$k[6]'";
			}
			if (toText($prowadzacy)!=$k[7]) 
			{
				$changes.=",prowadzacy='$k[7]'";
			}
			if ($miejsc!=$k[8]) 
			{
				$changes.=",miejsc=$k[8]";
			}
			if ($cena!=$k[9]) 
			{
				$changes.=",cena=$k[9]";
			}
			

			if (strlen($changes))
			{
				$sql.="UPDATE kursy SET cykl='$k[0]' $changes WHERE $where;\n";
				$upd++;
			}
		}
		$rek++;

		if (strlen($sql) && !strlen($error)) 
		{
			if (!@pg_Exec($db,$sql)) 
			{
				echo "ERROR ".($l+1)."<br>";
				echo nl2br($sql);
			}
		}


		//echo stripslashes($k[5])."<br>";
	}

	//echo nl2br($sql); return;


	if ($DeleteBefore) 
	{
		$new=$rek;
		$upd=0;
	}
	$sysinfo="Wprowadzono $new terminów, zmieniono $upd.";


?>
