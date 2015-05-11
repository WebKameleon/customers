<?php


    $sql="SELECT * FROM wbp_Artykuly WHERE ArtykulyStatus=1";
    if ($wbp_id) $sql.=" AND ArtykulyId=$wbp_id";
    if ($wbp_g_id) $sql.=" AND ArtykulyId>=$wbp_g_id";
    if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
    if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
    
    
    $id_plus=$_SERVER['plus']['artykuly'];
    
    $parent=18;
    $type=1;
    

    $q=$src->query($sql);
    if ($q) foreach ($q AS $row ){
        
        $id=$row['ArtykulyId'];
        $page_id=$id+$id_plus;
 
 
	$page_exists=kameleon_page_exists($page_id);
	
	if ($page_exists && !$force_rewrite)
	{
		echo sprintf("[%05d]",$id)." ... $page_id\n";
		flush();
		continue;	
	}
	
	
        $title=$row['ArtykulyOpis'];
	$prev=$parent;
	
	if ($page_exists) 
	{
	    $pg=kameleon_get_page($page_id);
	    $type=$pg['type'];
	    $prev=$pg['prev'];
	    $title=$pg['title'];
	}
        
        
	kameleon_page($page_id,$title,$prev,$type);
	kameleon_article($page_id,$title,$row['ArtykulyHtml'],array(),$row['ArtykulyAddData'],'0000',1,null,null,null,'wbp_article');

	if ($wbp_id && $wbp_gal)
	{
	    $sql="SELECT * FROM wbp_GaleriaKategorie WHERE GaleriaKategorieId=$wbp_gal";
		    
	    $q2=$src->query($sql);
	    if ($q2) foreach ($q2 AS $galeria ){
	    }
    
    
	    $sql="SELECT * FROM wbp_Galeria WHERE Galeria_GaleriaKategorieId=$wbp_gal ORDER BY GaleriaPozycja DESC,GaleriaId";
	    

	    
	    $menu=array();
	    $q2=$src->query($sql);
	    if ($q2) foreach ($q2 AS $m ){
		    $m['name']=$galeria['GaleriaKategorieNazwa'];
		    $m['menu_id']=$wbp_gal;
		    
		    $menu[]=$m;
	    }
	    //print_r($menu);   
	    kameleon_galery($menu,$page_id/*,$galeria['GaleriaKategorieNazwa']*/);	    
	}
	
        
        //print_r($row);
        
        echo $title. " ... $page_id\n";
    }