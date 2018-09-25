<?php
    include_once __DIR__.'/../system/fun.php';
    
    Header('Content-type: image');
    
    $img = new Image($_GET['img']);
    $dst = $_GET['img'].'.'.$_GET['size'].'.jpg';
    $img->min($dst,$_GET['size'],$_GET['crop']?$_GET['size']:0,true,$_GET['crop']);
    die(file_get_contents($dst));
