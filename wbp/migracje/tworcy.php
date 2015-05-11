<?php


    $sql="SELECT * FROM wbp_Tworcy
	    LEFT JOIN wbp_Tworcy_TworcaKategoria ON TworcyId=Tworcy_TworcaId
	    LEFT JOIN wbp_TworcyKategorie ON Tworcy_KategoriaId=TworcyKategorieId
	    WHERE TworcyStatus='1'
	    AND TworcyKategorieNazwa='fotograficy'";
    
    
    if ($wbp_id) $sql.=" AND TworcyId=$wbp_id";
    if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
    if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
    
    $sql.=" ORDER BY TworcyId";
    
    
    $id_plus=$_SERVER['plus']['tworcy'];
    
    $parent=46;
    $type=1;
    

    $q=$src->query($sql);
    if ($q) foreach ($q AS $row ){
        
        $id=$row['TworcyId'];
        $page_id=$id+$id_plus;
 
 
	if (!$force_rewrite && kameleon_page_exists($page_id))
	{
		echo sprintf("[%05d]",$id)."\r";
		flush();
		continue;	
	}
        
        $title=$row['TworcyNazwisko'].' '.$row['TworcyImie'];
        
        
	$img='<a href="http://www.wbp.poznan.pl/files/tworcy/'.$row['TworcyFoto'].'"><img src=""></a>';
	
	kameleon_page($page_id,$title,$parent,$type);
	kameleon_article($page_id,$title,$img.$row['TworcyHtml'],array('Twórcy foto'),'0000','0000',1,null,'fotograficy',null,'photographers');

	
        
        //print_r($row);
        
        echo sprintf("[%05d]",$id)." $title ... $page_id\n";
    }