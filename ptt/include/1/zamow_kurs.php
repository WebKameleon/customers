<?php
$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;

if (!$kurs) return;



if (strlen($AUTH_LOGIN))
{

	$query="SELECT date_part('epoch',godz_od) AS od ,
			date_part('epoch',godz_do) AS do,
			grupa,cykl
		 FROM kursy,obiekty 
		WHERE kursy.id=$kurs AND kursy.obiekt=obiekty.kod";

	parse_str(query2url($query));

	for ($c=0;$c<count($CYKLE_NAKLAD);$c++)
	{
		$_cykl=explode(":",$CYKLE_NAKLAD[$c]);
		if ($cykl==$_cykl[0] || $cykl==$_cykl[1])
			$zapytaj_o_cykle.=($cykl==$_cykl[0])?",'$_cykl[1]'":",'$_cykl[0]'";
	
	}
	if (strlen($zapytaj_o_cykle))
	{
		$AUTH_ID+=0;
		$zapytaj_o_cykle="'$cykl'$zapytaj_o_cykle";
		$query="SELECT date_part('epoch',godz_od) AS _od, 
				date_part('epoch',godz_do) AS _do,
				taniec AS _taniec, cykl AS _cykl, godz_od , godz_do,
				grupa AS _grupa
			 FROM zapisy,kursy,obiekty
			 WHERE klient_id=$AUTH_ID AND kursy.id=zapisy.kurs_id 
			 AND ilosc>0 AND cykl IN ($zapytaj_o_cykle)
			 AND  kursy.obiekt=obiekty.kod";


		
		$res=pg_Exec($db,$query);
		//if (!$res) echo nl2br($query);

		for ($i=0;$i<pg_NumRows($res);$i++)
		{
			parse_str(pg_ExplodeName($res,$i));
			$roznica1=round(abs($od-$_do)/60);
			$roznica2=round(abs($do-$_od)/60);

			$godz_od=substr($godz_od,0,5);
			$godz_do=substr($godz_do,0,5);
			if (($roznica1<=$czas_przejscia || $roznica2<=$czas_przejscia) && $grupa!=$_grupa)
			{
				$info="<br>".sysmsg("Czas przejscia pomiedzy wybranym kursem a")." <b>'".sysmsg($_taniec)."' ($CYKLE[$_cykl], $godz_od-$godz_do)</b> ".sysmsg("wynosi ponad $czas_przejscia minut. Zapisujesz się na własna odpowiedzialnosc")." !";
				$odpowiedzialnosc= sysmsg("na własna odpowiedzialnosc");
				break;
			}
			if (round(abs($od-$_od)/60)<20) 
				$info="<br>".sysmsg("Taniec pokrywa się czasowo z")." <b>'".sysmsg($_taniec)."' ($CYKLE[$_cykl], $godz_od-$godz_do)</b>";
		}
	}
	
	$button_1 = sysmsg("Zapisz się na kurs").' '.$odpowiedzialnosc;
	$button_2 = sysmsg("Anuluj");
	

	$form= "$info<form method=post action='$next'>
			<input type=submit value=\"$button_1\" class=button>
			<input type=button value=\"$button_2\" onClick='history.go(-1)' class=button>
			<input type=hidden name=action value=\"Zamawiam\">
			<input type=hidden name=kurs value='$kurs'>
			</form>";
}
else
{
	$button_3 = sysmsg("Wprowadź swoje dane i zapisz się na kurs");
	$form= "<form method=post action='$more'>
			<input type=submit value=\"$button_3\" class=button>
			<input type=hidden name=kurs value='$kurs'>
			</form>";
}


$query="SELECT cykl FROM kursy WHERE id=$kurs";
parse_str(query2url($query));


if (saleOff($cykl))
{
	echo "<p><b>";
	echo sysmsg("Sprzedaż w tym terminie została zakończona");
	echo "</b></p>";
	$button_2 = sysmsg("Anuluj");
	if (!is_pttAdmin()) $form= "<p><input class=\"btn btn-default\" type=button value=\"$button_2\" onClick='history.go(-1)' class=button></p>";
}



echo $form;
include ("$INCLUDE_PATH/termin.php");
echo $form;


