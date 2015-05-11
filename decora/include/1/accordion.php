<?php
    static $accordion_loaded;
    
    if (!$accordion_loaded) {
    $accordion_loaded=true;
?>

<link type="text/css" rel="stylesheet" href="<?php echo $IMAGES ?>/css/jquery.qtip.css">
<script type="text/javascript" src="<?php echo $IMAGES ?>/js/jquery.qtip.min.js"></script>
<script type="text/javascript" src="<?php echo $IMAGES ?>/js/jquery.thumbnailScroller.js"></script>
<script type="text/javascript" src="<?php echo $IMAGES ?>/js/accordion.js"></script>
<script type="text/javascript" src="<?php echo $IMAGES ?>/js/simple_inspirations.js"></script>

<?php
}
?>
