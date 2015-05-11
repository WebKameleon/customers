<?php
    
    $sql="SELECT * FROM wbp_Katalogi LEFT JOIN wbp_KatalogiKategorie ON Katalogi_KategoriaId=KatalogiKategorieId WHERE 1=1";
    if (!$wbp_id) $sql.=" AND KatalogiStatus='1'";
    if ($wbp_id) $sql.=" AND KatalogiId=$wbp_id";
    if ($wbp_g_id) $sql.=" AND KatalogiId>=$wbp_g_id";

    $sql.=" ORDER BY KatalogiId";
    if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
    if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
    
    
    
    
    $id_plus=$_SERVER['plus']['katalogi'];
    
    echo 'Main query ... ';
    flush();
    
    $q=$src->query($sql);
    
    
    echo "ok \n";
    
    
    if ($q) foreach ($q AS $row ){
        
        $id=$row['KatalogiId'];
        $page_id=$id+$id_plus;
     
        //print_r($row);
        
        $page_exists=kameleon_page_exists($page_id);
        if (!$force_rewrite && $page_exists)
        {
                echo sprintf("[%05d]",$id)."\r";
                flush();
                continue;	
        }
        

        $title=trim($row['KatalogiTytul']);
        $title=str_replace('"','&quot;',$title);        
        
        $costxt=trim($row['KatalogiAutor']);
        $costxt=str_replace('"','&quot;',$costxt);
        
        kameleon_page($page_id,$title,158,1);
        
        
        $html=$row['KatalogiOpisHtml'];
        
        if ($row['KatalogiGrafika'])
        {
            $html='<a href="files/pliki/'.$row['KatalogiGrafika'].'"><img src="nie_ma_znaczenia"/></a>'.$html;
        }
        
        $kat=mb_substr('Plik/'.$row['KatalogiKategorieNazwa'],0,30,'utf8');
        
        
        kameleon_article($page_id,$title,$html,array($kat),$row['KatalogiAddData'],'0000-00-00',1,null,$costxt,null,'','',$row['KatalogiPlik']);
        
        echo sprintf("[%05d]",$id)." $title ... $page_id\n";
    }        
        