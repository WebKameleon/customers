<?php
    include_once __DIR__.'/inc/system/fun.php';
    
    if (isset($_GET['file']))
    {
        $f=$_GET['file'];
        $f=WBP::str_to_url($f);
        $f="att/x-archiwum/$f";
        if (file_exists(__DIR__.'/'.$f))
        {
            header("Location: ".$f,TRUE,301);
            die();
        }
    }