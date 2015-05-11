<?php
    include (__DIR__.'/.produkty.php');
    
    include (__DIR__.'/produkty.php');
    
    
    
    
    $features=array();
    if (!is_array($products) || !count($products) ) return;

    foreach($products AS $p)
    {
        $features=array_merge($features,explode(',',$p['features']));
    }
    foreach ($features AS $i=>&$f)
    {
        $f=trim($f);
        if (!$f) unset($features[$i]);
    }
    $features=array_unique($features);
    
    
    if (isset($_GET['dest'][$sid])) {
        $costxt[3]=$_GET['dest'][$sid];
        $webtd=new webtdModel($sid);
        $webtd->costxt=implode(':',$costxt);
        $webtd->save();
    }
    
    
?>

<div id="przeznaczenieDiv_<?php echo $sid?>">
<div class="product-list-selector container">
<select onchange="location.href='<?php echo $self.$next_sign.'t='.time().'&dest['.$sid.']='?>'+this.value" >
    <option value="">Wybierz przeznaczenie</option>
    <?php
        foreach($features AS $f) echo '<option '.($f==$costxt[3]?'selected':'').' value="'.$f.'">Pod: '.$f.'</option>';
    ?>
</select>
</div>
</div>

<script type="text/javascript">
    child=$('#przeznaczenieDiv_<?php echo $sid?>').children().first();
    $('#productManagement_<?php echo $sid?>').append(child);
</script>

