<?php
    if (!$costxt) return;
    
    $file='map/'.$costxt.'.json';
    
    $data=json_decode(file_get_contents(__DIR__.'/'.$file),1);
    
    $include=isset($KAMELEON_MODE) && $KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    
    //mydie($data['markers']);
?>
<ul class="map-markers">
    <?php foreach ($data['markers'] AS $m) if ($m['Icon']) {?>
    <li class="<?php echo $m['Type']; if ($m['VisibleStart']) echo ' active'; ?>" title="<?php echo $m[$lang]?>"><img src="<?php echo $IMAGES.'/map/'.$m['Icon'];?>"/></li>
    <?php } ?>
</ul>
<script>
    var map_json='<?php echo $include.'/'.$file;?>';
    var map_icons='<?php echo $IMAGES.'/map/';?>';
</script>