<form method="post" action="<?php echo $self?>">
<ul>
<?php
    WBP::kameleon_require_static_include($this);

    $data=unserialize(base64_decode($costxt));


    $table=isset($data['table'])?$data['table']:'objects';    
    $sql="SELECT tablename FROM pg_tables WHERE tablename LIKE 'wbp_%' AND tablename <> 'wbp_imports' AND tablename <> 'wbp_db'";
    $q=$dbh->query($sql);
    $tables='';

    foreach ($q AS $row)
    {
        $tables.='<option '.($table==substr($row['tablename'],4)?'selected':'').'>'.substr($row['tablename'],4).'</option>';
    }
    
    if (isset($_POST['obj'][$sid]))
    {
        $data['kat']=$_POST['obj'][$sid];
    }
    
    if (isset($_POST['table'][$sid]))
    {
        $data['table']=$table=$_POST['table'][$sid];
    }
    
    $table='wbp_'.$table;
    
    if (isset($_POST['lazyload'][$sid]))
    {
        $cos=$_POST['lazyload'][$sid];
        $webtd=new webtdModel($sid);
        $webtd->cos=$cos;
        $webtd->save();
    }
    

    $sql="SELECT * FROM $table LIMIT 1";
    $q=$dbh->query($sql);
    
    $or=array();
        
    foreach ($q AS $row)
    {
        foreach ($row AS $k=>$v)
        {
            if (substr($k,0,2)=='x_')
            {
                echo '<li><input type="text" name="obj['.$sid.']['.$k.']" value="'.(isset($data['kat'][$k])?$data['kat'][$k]:'').'" placeholder="'.substr($k,2).'"/>';
                if (isset($data['kat'][$k]) && $data['kat'][$k])
                {
                    $or[]=$k.'=1';
                }
            }
        }
    }
    
    $where=count($or)?'WHERE '.implode(' OR ',$or):'';
    
    $sql="SELECT powiat FROM $table $where GROUP by powiat ORDER BY powiat";

    $q=$dbh->query($sql);
    
    $data['powiat']=array();
    foreach ($q AS $row)
    {
        if($row['powiat']) $data['powiat'][]=$row['powiat'];
    }  


    $sql="SELECT miasto FROM $table $where GROUP by miasto ORDER BY miasto";

    $q=$dbh->query($sql);
    $data['miasto']=array();
    foreach ($q AS $row)
    {
        if($row['miasto']) $data['miasto'][]=$row['miasto'];
    } 

    
    $newcostxt=base64_encode(serialize($data));
    
    if ($costxt!=$newcostxt)
    {
        $webtd=new webtdModel($sid);
        $webtd->costxt=$newcostxt;
        $webtd->save();
    }
    
?>
</ul>
<select name="table[<?php echo $sid;?>]"><?php echo $tables;?></select>
<p>
    <input type="hidden" name="lazyload[<?php echo $sid?>]" value="0"/>
    <input type="checkbox" name="lazyload[<?php echo $sid?>]" value="1" <?php if ($cos) echo 'checked'?>/> Lazy load
</p>
<input type="submit" value="zapisz"/>
</form>