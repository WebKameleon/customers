<?php

	$kategorie=array();
	
	$kategorie[1] = array('cat'=>'Firmowe','prev'=>21,'type'=>1,'class'=>'company');
	$kategorie[2] = array('cat'=>'Polecamy','prev'=>24,'type'=>1,'class'=>'recommend');
	$kategorie[3] = array('cat'=>'Warsztaty','prev'=>25,'type'=>1,'class'=>'workshop');
	$kategorie[4] = array('cat'=>'Konkursy','prev'=>26,'type'=>1,'class'=>'competition');
	$kategorie[5] = array('cat'=>'Przetargi','prev'=>30,'type'=>1,'class'=>'przetargi');
	$kategorie[6] = array('cat'=>'WIK','prev'=>29,'type'=>1,'class'=>'wik');
	
	$kategorie[7] = array('cat'=>'Fotografia','prev'=>27,'type'=>1,'class'=>'photography');
	$kategorie[9] = array('cat'=>'Impr. krajoznawcze','prev'=>23,'type'=>1,'class'=>'siteseeing');
	$kategorie[10] = array('cat'=>'P. inf. eur.','prev'=>45,'type'=>1,'class'=>'siteseeing');
	
	$kategorie[11] = array('cat'=>'Muzyka','prev'=>33,'type'=>1,'class'=>'music');
	$kategorie[12] = array('cat'=>'Teatr','prev'=>35,'type'=>1,'class'=>'theather');
	$kategorie[13] = array('cat'=>'Literatura','prev'=>36,'type'=>1,'class'=>'literature');
	$kategorie[14] = array('cat'=>'Film i fotografia','prev'=>37,'type'=>1,'class'=>'film_photo');
	
	$kategorie[15] = array('cat'=>'Edukacja','prev'=>31,'type'=>1,'class'=>'edu');
	$kategorie[16] = array('cat'=>'Imprezy plenerowe','prev'=>38,'type'=>1,'class'=>'edu');
	$kategorie[17] = array('cat'=>'Wystawy','prev'=>34,'type'=>1,'class'=>'exibitions');
	
	$kategorie[18] = array('cat'=>'Folklor','prev'=>28,'type'=>1,'class'=>'folklor');
	$kategorie[19] = array('cat'=>'Autor na żądanie','prev'=>39,'type'=>1,'class'=>'author_on_demand');
	
	$kategorie[21] = array('cat'=>'Lista wystaw','prev'=>20,'type'=>1,'class'=>'exhib_list');

	
	$kategorie[23] = array('cat'=>'Ogłoszenia','prev'=>47,'type'=>1,'class'=>'anouncment');
	

	$kategorie[25] = array('cat'=>'Rej.zam.pub.','prev'=>41,'type'=>1,'class'=>'rzp');
	$kategorie[27] = array('cat'=>'Konkursy foto WBP','prev'=>22,'type'=>1,'class'=>'photo_competition');
	
	$kategorie[28] = array('cat'=>'Konkursy kraj i świat','prev'=>43,'type'=>1,'class'=>'world_competitions');
	$kategorie[29] = array('cat'=>'Konkursy wlkp','prev'=>44,'type'=>1,'class'=>'wlkp_competitions');
	$kategorie[30] = array('cat'=>'Zaproszenia','prev'=>42,'type'=>1,'class'=>'invitations');
	$kategorie[31] = array('cat'=>'Prom.czytelnictwa','prev'=>32,'type'=>1,'class'=>'readers');
	
	
	
	
	/*
|                         1 | Firmowe                            |
|                         2 | Polecamy                           |
|                         3 | Warsztaty                          |
|                         4 | Konkursy                           |
|                         5 | Przetargi                          |
|                         6 | Wielkopolski Informator Kulturalny |
|                         7 | Fotografia                         |
|                         8 | Folklor                            |
|                         9 | Imprezy krajoznawcze               |
|                        10 | Punkt Informacji Europejskiej      |
|                        11 | muzyka                             |
|                        12 | teatr                              |
|                        13 | literatura                         |
|                        14 | film i fotografia                  |
|                        15 | edukacja                           |
|                        16 | imprezy plenerowe                  |
|                        17 | wystawy                            |
|                        18 | folklor                            |
|                        19 | Autor na żądanie                   |
|                        21 | Lista wystaw                       |
|                        22 | Przetargi                          |
|                        23 | Ogłoszenia                         |
|                        24 | Korespondencja                     |
|                        25 | Rejestr zamówień publicznych       |
|                         8 | Folklor                            |
|                        26 | konkursy                           |
|                        27 | Konkursy fotograficzne WBP         |
|                        28 | Konkursy z kraju i ze świata       |
|                        29 | Konkursy w Wielkopolsce            |
|                        30 | Zaproszenia                        |
|                        31 | Prom.czytelnictwa - Konkursy       |
|                        32 | Księgarnia        	 */
	
	
	
	$sql="SELECT * FROM wbp_AktualnosciWbp WHERE 1=1";
	if (!$wbp_id && !$wbp_kat) $sql.=" AND AktualnosciWbpStatus='1'";
	if ($wbp_id) $sql.=" AND AktualnosciWbpId=$wbp_id";
	if ($wbp_kat) $sql.=" AND AktualnosciWbpId IN (SELECT AktualnosciWbp_AktualnosciWbpId FROM wbp_AktualnosciWbp_AktualnosciWbpKategorie WHERE AktualnosciWbp_KategorieId=$wbp_kat)";
	if ($wbp_g_id) $sql.=" AND AktualnosciWbpId>=$wbp_g_id";
	if ($wbp_like) $sql.=" AND AktualnosciWbpHtml LIKE '$wbp_like'";
	
	
	$sql.=" ORDER BY AktualnosciWbpId";
	if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
	if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
	
	//die("$sql\n");
	
	$id_plus=$_SERVER['plus']['aktualnosci_wbp'];

	echo 'Main query ... ';
	flush();

	$q=$src->query($sql);
	
	echo "ok \n";

	if ($q) foreach ($q AS $row ){
		

		$id=$row['AktualnosciWbpId'];
		$page_id=$id+$id_plus;
		
		
		if (strstr($row['AktualnosciWbpHtml'],'index.php?mode=towary'))
		{
			echo sprintf("[%05d]",$id)." link do sklepu ... kiedyś\n";
			continue;			
		}

		$page_exists=kameleon_page_exists($page_id);
	
		if (!$force_rewrite && $page_exists)
		{
			echo sprintf("[%05d]",$id)." ... $page_id\n";
			flush();
			continue;	
		}
		

		
		

		$parent=0;
		$categories=array();

		
		$sql="SELECT * FROM wbp_AktualnosciWbp_AktualnosciWbpKategorie WHERE AktualnosciWbp_AktualnosciWbpId=$id ORDER BY AktualnosciWbp_KategorieId DESC";
		$q2=$src->query($sql);

		if ($q2) foreach ($q2 AS $row2 ){
			//print_r($row2);
			$kat_id=$row2['AktualnosciWbp_KategorieId'];
			
			if ($kat_id==8) $kat_id=18;
			if ($kat_id==26) $kat_id=4;
			if ($kat_id==22) $kat_id=5;

			if (!isset($kategorie[$kat_id]))
			{
				echo "No entry for category $kat_id\n";
				break 2;
			}
			if (!in_array($kategorie[$kat_id]['cat'],$categories)) $categories[]=$kategorie[$kat_id]['cat'];
			if (!$parent) $parent=$kategorie[$kat_id]['prev'];
		}

		$title=trim($row['AktualnosciWbpTytul']);
		$title_short=null;
		$title=str_replace('"','&quot;',$title);



		
		if (strstr($title,'&quot;'))
		{
			$pos=strpos($title,'&quot;');
			$title_short=substr($title,$pos+6);
			$pos=strpos($title_short,'&quot;');
			$title_short=substr($title_short,0,$pos);
			
			$title_short=mb_substr($title_short,0,64,'utf8');
		}
		
		$type=$kategorie[$kat_id]['type'];
		if ($page_exists) 
		{
			$pg=kameleon_get_page($page_id);
			$type=$pg['type'];
			$parent=$pg['prev'];
			$title=$pg['title'];
			$title_short=$pg['title_short'];
		}		
		
		
		kameleon_page($page_id,$title,$parent?:40,$type,$title_short);
		
		
		$html=$row['AktualnosciWbpHtml'];
		
		if ($row['AktualnosciWbpFoto1'])
		{
			$html='<a href="files/aktualnosci_wbp/big/'.$row['AktualnosciWbpFoto1'].'"><img src="nie_ma_znaczenia"/></a>'.$html;
		}
		
		$att='';
		
		if ($row['AktualnosciWbpPlik'])
		{
			$att=$row['AktualnosciWbpPlik'];
		}
		
		kameleon_article($page_id,$title,$html,$categories,$row['AktualnosciWbpDataBPublikacji'],$row['AktualnosciWbpDataEPublikacji'],1,null,null,null,$kategorie[$kat_id]['class'],$row['AktualnosciWbpZajawka'],$att);

	
		if ($row['AktualnosciWbp_GaleriaId'])
		{
			$sql="SELECT * FROM wbp_GaleriaKategorie WHERE GaleriaKategorieId=".$row['AktualnosciWbp_GaleriaId'];
				
			$q2=$src->query($sql);
			if ($q2) foreach ($q2 AS $galeria ){
			}
		
		
			
			
			$sql="SELECT * FROM wbp_Galeria WHERE Galeria_GaleriaKategorieId=".$row['AktualnosciWbp_GaleriaId']." ORDER BY GaleriaPozycja DESC,GaleriaId";
			
			$menu=array();
			$q2=$src->query($sql);
			if ($q2) foreach ($q2 AS $m ){
				$m['name']=$galeria['GaleriaKategorieNazwa'];
				$m['menu_id']=$row['AktualnosciWbp_GaleriaId'];
				
				$menu[]=$m;
			}
						
			kameleon_galery($menu,$page_id/*,$galeria['GaleriaKategorieNazwa']*/);
		}
		
		if ($row['AktualnosciWbp_ObiektyId'])
		{
			kameleon_article($page_id,'','',array(),$row['AktualnosciWbpDataBPublikacji'],'0000',3,$row['AktualnosciWbp_ObiektyId'],null,'obiekty.php');
		}
		
		$kalendarium=array();
		$kalendarium_html='';
		
		$sql="SELECT * FROM wbp_Kalendarium
			LEFT JOIN wbp_Obiekty ON Kalendarium_ObiektyId=ObiektyId 
			WHERE Kalendarium_AktualnosciId=$id ORDER BY KalendariumDataOd";
		$q2=$src->query($sql);
		
		$pagekey=array();
		if ($q2) foreach ($q2 AS $row2 ){
			
			if (count($kalendarium)==0)
			{
				$kalendarium_html.='<h4>Inauguracja:</h4><ul>';
				$kalendarium_data=$row2['KalendariumDataOd'];
				
			}
			if (count($kalendarium)==1)
			{
				$kalendarium_html.='</ul><h4>Kolejne prezentacje:</h4><ul>';
			}
			
			$kalendarium[]=$row2;
			
			$kalendarium_html.='<li>';
			
			$kalendarium_html.='od: '.date('d-m-Y',strtotime($row2['KalendariumDataOd']));
			$kalendarium_html.=' do: '.date('d-m-Y',strtotime($row2['KalendariumDataDo']));
			
			$kalendarium_html.=' <b>'.$row2['ObiektyMiasto'].' - '.$row2['ObiektyNazwa'].'</b>';
			
			$kalendarium_html.='</li>';
			
			$pagekey[]=array('object'=>$row2['Kalendarium_ObiektyId'],'from'=>strtotime($row2['KalendariumDataOd']),'to'=>strtotime($row2['KalendariumDataDo']));
		}
		if ($kalendarium_html)
		{
			$kalendarium_html.='</ul>';
			
			//kameleon_article($page_id,'',$kalendarium_html,array(),$kalendarium_data,'0000',4);
		}
		
		if (count($pagekey))
		{
			kameleon_pagekey($page_id,base64_encode(serialize($pagekey)));
		}
		
		
		echo sprintf("[%05d]",$id)." $title ... $page_id\n";
	
	}
 
	
