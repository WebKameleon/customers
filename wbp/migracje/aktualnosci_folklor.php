<?php

	
	$sql="SELECT * FROM folklor_AktualnosciExtended WHERE AktualnoscExtendedLang='".strtoupper($kameleon_lang)."'";
	if (!$wbp_id && !$wbp_kat) $sql.=" AND AktualnoscExtendedStatus='1'";
	if ($wbp_id) $sql.=" AND AktualnoscExtendedId=$wbp_id";
	if ($wbp_g_id) $sql.=" AND AktualnoscExtendedId>=$wbp_g_id";
	if ($wbp_like) $sql.=" AND AktualnoscExtendedHtml LIKE '$wbp_like'";
	
	
	$sql.=" ORDER BY AktualnoscExtendedId";
	if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
	if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
	
	//die("$sql\n");
	
	$id_plus=$_SERVER['plus']['aktualnosci_folklor'];

	echo 'Main query ... ';
	flush();

	$q=$src->query($sql);
	
	echo "ok \n";

	if ($q) foreach ($q AS $row ){
		

		$id=$row['AktualnoscExtendedId'];
		$page_id=$id+$id_plus;
		
		

		$page_exists=kameleon_page_exists($page_id);
	
		if (!$force_rewrite && $page_exists)
		{
			echo sprintf("[%05d]",$id)." ... $page_id\n";
			flush();
			continue;	
		}
		

		$parent=23;
		$categories=array();

		
		$sql="select AktualnoscExtendedTypName from folklor_AktualnosciExtended_Typ
				left join folklor_AktualnosciExtendedTyp on AktualnoscExtended_TypId=AktualnoscExtendedTypId and AktualnoscExtendedTypLang=AktualnoscExtended_AktLang
				where AktualnoscExtended_AktId=$id and AktualnoscExtended_AktLang='".$row['AktualnoscExtendedLang']."'";
		$q2=$src->query($sql);

		if ($q2) foreach ($q2 AS $row2 ){
			//print_r($row2);
			
			$categories[]=$row2['AktualnoscExtendedTypName'];

		}

		$title=trim($row['AktualnoscExtendedTytul']);
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
		
		
		$html=$row['AktualnoscExtendedHtml'];
		
		if ($row['AktualnoscExtendedGraph2'])
		{
			$html='<a href="files/aktualnosci_extended/'.$row['AktualnoscExtendedGraph2'].'"><img src="nie_ma_znaczenia"/></a>'.$html;
		}
		
		$att='';

		
		kameleon_article($page_id,$title,$html,$categories,$row['AktualnoscExtendedDataBPublikacji'],$row['AktualnoscExtendedDataEPublikacji'],1,null,null,null,'',$row['AktualnoscExtendedZajawka'],$att);

	
		
		echo sprintf("[%05d]",$id)." $title ... $page_id\n";

		//break;	
	}
 
	
