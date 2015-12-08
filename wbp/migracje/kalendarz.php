<?php

	$sql="SELECT * FROM folklor_Imprezy WHERE ImprezyLang='".strtoupper($kameleon_lang)."' ";
	if (!$wbp_id && !$wbp_kat) $sql.=" AND ImprezyStatus='1'";
    
	$sql.=" ORDER BY ImprezyId DESC";
	if ($wbp_limit) $sql.=" LIMIT $wbp_limit";
	if ($wbp_offset) $sql.=" OFFSET $wbp_offset";
    

	flush();

	$q=$src->query($sql);
	

    echo "Subject,Start Date,Start Time,Location\n";
    
	if ($q) foreach ($q AS $row ){
    
        
        echo '"'.str_replace('"','',$row['ImprezyNazwa']).'",';
        echo '"'.date('m/d/Y',strtotime($row['ImprezyData'])).'",';
        echo '"'.($row['ImprezyCzas']=='00:00:00'?'01:00':substr($row['ImprezyCzas'],0,5)).'",';
        echo '"'.$row['ImprezyMiejsce'].'"';
        echo "\n";
        
        //break;
    }