<?php

	
	$sql="SELECT * FROM folklor_GaleriaKategorie WHERE GaleriaKategorieLang='".strtoupper($kameleon_lang)."' AND GaleriaKategorieId>1";
	if (!$wbp_id && !$wbp_kat) $sql.=" AND GaleriaKategorieStatus='1'";
	if ($wbp_id) $sql.=" AND GaleriaKategorieId=$wbp_id";
	if ($wbp_g_id) $sql.=" AND GaleriaKategorieId>=$wbp_g_id";
	if ($wbp_like) $sql.=" AND GaleriaKategorieNazwa LIKE '$wbp_like'";
	
	
	$sql.=" ORDER BY GaleriaKategorieId";
	if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
	if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
	
	//die("$sql\n");
	
	$id_plus=$_SERVER['plus']['galeria'];

	echo 'Main query ... ';
	flush();

	$q=$src->query($sql);
	
	echo "ok \n";

	if ($q) foreach ($q AS $row ){
		

		$id=$row['GaleriaKategorieId'];
		$page_id=$id+$id_plus;

		$page_exists=kameleon_page_exists($page_id);
	
		if (!$force_rewrite && $page_exists)
		{
			echo sprintf("[%05d]",$id)." ... $page_id\n";
			flush();
			continue;	
		}
		

		$parent=5;

		$title=trim($row['GaleriaKategorieNazwa']);
		$title_short=null;
		$title=str_replace('"','&quot;',$title);		
		
		if ($page_exists) 
		{
			$pg=kameleon_get_page($page_id);
			$type=$pg['type'];
			$parent=$pg['prev'];
			$title=$pg['title'];
			$title_short=$pg['title_short'];
		}		
		
		$type=0; 
	
		kameleon_page($page_id,$title,$parent,$type,$title_short);
		
		
		$sql="SELECT * FROM folklor_Galeria WHERE Galeria_GaleriaKategorieId=".$id." AND GaleriaLang='".$row['GaleriaKategorieLang']."' ORDER BY GaleriaPozycja DESC,GaleriaId";
		
		$menu=array();
		
		$q2=$src->query($sql);
		
		if ($q2) foreach ($q2 AS $m ){
			$m['name']=$row['GaleriaKategorieNazwa'];
			$m['menu_id']=$page_id;
			
			$menu[]=$m;
		}
					
		kameleon_galery($menu,$page_id,$row['GaleriaKategorieNazwa']);
		
		echo sprintf("[%05d]",$id)." $title ... $page_id\n";

		break;	
	}
 
	
