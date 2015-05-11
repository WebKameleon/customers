<?php

    $products=0;
    if (isset($_GET['products'])) $products+=$_GET['products'];
    if (!$products) {
        if (!is_object($pagekey)) $pagekey=json_decode($pagekey);
        $products=0+$pagekey->pr;
    }
    
    
    include(__DIR__.'/recipients-'.$lang.'.html');
    
    
    $include=$KAMELEON_MODE?$session['uincludes_ajax']:$INCLUDE_PATH;
    
    $ajax_path=$include.'/recipients-backend.php';
    $ajax_gmap_path=$include.'/google_maps.php';
?>


<link rel="stylesheet" href="<?php echo $IMAGES?>/css/recipients.css" media=""/>


<script type="text/javascript">
    
    var products='';
    var default_products='<?php echo $products+0?>';
    var all_products='';
    
    var IMAGES='<?php echo $IMAGES?>';
    var ajax_gmap_path='<?php echo $ajax_gmap_path;?>';
    var ajax_path='<?php echo $ajax_path?>';
    var lang='<?php echo $lang?>';
    
    var last_province = null;
</script>    

<script type="text/javascript" src="<?php echo $IMAGES?>/js/decora/recipients-<?php echo $lang?>.js"></script>
<script type="text/javascript" src="<?php echo $IMAGES?>/js/decora/recipients.js"></script>

    
