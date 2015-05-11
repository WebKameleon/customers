<?php

    WBP::kameleon_require_static_include($this);
    

    
    if (isset($_POST['data'][$sid]))
    {       
        $cos=$_POST['data'][$sid]['obj'];
        $costxt=$_POST['data'][$sid]['table'];
        
        $webtd=new webtdModel($sid);
        $webtd->cos=$cos;
        $webtd->costxt=$costxt;
        $webtd->ob=3;
        $webtd->save();
    }
    
    
    $table=$costxt?:'objects';
    
    $sql="SELECT tablename FROM pg_tables WHERE tablename LIKE 'wbp_%' AND tablename <> 'wbp_imports' AND tablename <> 'wbp_db'";
    $q=$dbh->query($sql);
    $tables='';
    

    foreach ($q AS $row)
    {
        $tables.='<option '.($table==substr($row['tablename'],4)?'selected':'').'>'.substr($row['tablename'],4).'</option>';
    }
    
    $objects=WBP::get_file_db($table);
    
    $obiekty=array();
    foreach($objects AS $object)
    {
        $obiekty[mb_strtolower($object['miasto'].'-'.$object['nazwa'],'utf8')] = $object;
    }
    ksort($obiekty);
    
    $obj='<option value="0">Wybierz</option>';
    foreach ($obiekty AS $o)
    {
        $s=$cos==$o['id']?'selected':'';
        $obj.='<option '.$s.' value="'.$o['id'].'">'.$o['miasto'].' - '.$o['nazwa'].'</option>';
    }
    
?>
<form method="post" action="<?php echo $self?>">
    <select name="data[<?php echo $sid;?>][table]"><?php echo $tables;?></select>
    <select name="data[<?php echo $sid;?>][obj]"><?php echo $obj;?></select>
    
    <br/>
    <input type="submit" value="zapisz"/>
</form>