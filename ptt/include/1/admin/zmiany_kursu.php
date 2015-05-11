<?php


	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:false;
	
	include("$INCLUDE_PATH/admin/lista_kursow.php");

	if (!$kurs) return;

	$query="SELECT sum(ilosc) AS ile FROM zapisy 
		 WHERE kurs_id=$kurs";


	parse_str(query2url($query));
	
	
	$query="SELECT * FROM kursy,obiekty 
		 WHERE kursy.id=$kurs
		 AND kursy.obiekt=obiekty.kod";


	parse_str(query2url($query));

	echo "$nazwa / sala $pomieszczenie <br>";
	echo "Cykl $cykl ($CYKLE[$cykl]), godz: ".substr($godz_od,0,5)." - ".substr($godz_do,0,5)."<br>";
	echo "$taniec<br>";
	echo "Prowadzacy: $prowadzacy<br>";


?>
<form action="<?php echo $next?>" method=post>
 <input type=hidden name=action value="admin/ZmianyWKursie">
 <input type=hidden name=kurs value="<?php echo $kurs?>">
<br>
 Obiekt: <select class="form-control" name=new_obiekt>
 <?php
	$query="SELECT kod,nazwa FROM obiekty ORDER BY nazwa";
	$res=pg_Exec($db,$query);
	for ($i=0;$i<pg_NumRows($res);$i++)
	{

		parse_str(pg_ExplodeName($res,$i));
		$sel=($kod==$obiekt)?"selected":"";		
		echo "<option value='$kod' $sel>$nazwa</option>";
	}

 ?>
 </select> 
 <br>
 
   <div class="form-group">
    <label>sala</label>
    <input type=text name=new_pomieszczenie value="<?php echo $pomieszczenie?>">
  </div>
   
   <div class="form-group">
    <label>taniec: </label>
    <input name=new_taniec type=text value="<?php echo $taniec?>" size=60>
  </div>
   
   <div class="form-group">
    <label>poziom: </label>
    <input name=new_zaawansowanie type=text value="<?php echo $zaawansowanie?>" size=40>
  </div>
  
  <div class="form-group">
    <label>Prowadzacy:  </label>
    <input name=new_prowadzacy type=text value="<?php echo $prowadzacy?>" size=60>
  </div>
 
  <div class="form-inline">
  <div class="form-group">
    <label>Godziny:</label>
    <input type=text name=new_godz_od value="<?php echo substr($godz_od,0,5)?>">
  </div>
  <div class="form-group">
    <label>do</label>
    <input type=text name=new_godz_do value="<?php echo substr($godz_do,0,5)?>" size=4>
  </div>
  <div class="form-group">
    <label>cena:</label>
    <input name=new_cena type=text value="<?php echo $cena?>" size=10>
  </div>
  <div class="form-group">
    <label>il.miejsc:</label>
    <input name=new_miejsc type=text value="<?php echo $miejsc?>" size=10>
  </div>
</div>
 

 <br> <br>
 <input class="btn btn-default" type=submit value="Zastosuj zmiany [<?php echo 0+$ile?> kursantów]">
</form>

<?
	if ($ile) return;
?>

<form style="margin-top:15px;" action="<?php echo $next?>" method=post>
 <input class="btn btn-default" type=hidden name=action value="admin/UsunKurs">
 <input class="btn btn-default" type=hidden name=kurs value="<?php echo $kurs?>">
 <input class="btn btn-default" type=submit value="Usuń kurs">
</form>