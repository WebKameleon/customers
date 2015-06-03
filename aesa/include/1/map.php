<?php
    if (!$costxt) return;
    
    $file='map/'.$costxt.'.json';
    
    $data=json_decode(file_get_contents(__DIR__.'/'.$file),1);
    
    $include=isset($KAMELEON_MODE) && $KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    
    //mydie($data['markers']);
?>
<ul class="map-markers">
    <?php foreach ($data['markers'] AS $m) if ($m['Icon']) {?>
    <li class="<?php echo $m['Type']?>"><img src="<?php echo $IMAGES.'/'.$m['Icon'];?>"/></li>
    <?php } ?>
</ul>
<script>
    var map_json='<?php echo $include.'/'.$file;?>';
</script>