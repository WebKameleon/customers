<?php

    require_once __DIR__.'/kameleon/Google.php';
    include_once __DIR__.'/../system/fun.php';

    $data=$_GET;
    
  
    
    @session_start();
    
    // Drive part
    Google::setToken($_SESSION['drive_access_token']);
    session_write_close();
    
    $f=Google::getFile($data['id']);
    
    if (!$f['id']) return;
    
    $img=Google::req($f['downloadUrl']);
    
    Header('Content-type: '.$f['mimeType']);
    
    
    die($img);