<?php
    include_once __DIR__.'/inc/system/fun.php';
    
    $mydir=dirname($_SERVER['SCRIPT_NAME']);
    $mylenght=strlen($mydir);
    $file=substr($_SERVER['REQUEST_URI'],$mylenght);
    if ($file[0]=='/') $file=substr($file,1);



    $file=substr($_SERVER['REQUEST_URI'],$mylenght);
    while ($file[0]=='/') $file=substr($file,1);

    $token='lksdTlkjfaskfhEwjerFTwehkjTnskdcbaHskTdbcmabdIOcwefaf';
    
    if (substr($file,0,6)=='files/')
    {
        $file=substr($file,6);
        $file=urldecode($file);

        $file=str_replace('/',$token,$file);
        $file=WBP::str_to_url($file);
        $file=str_replace($token,'/',$file);
        $file="att/x-archiwum/$file";

        $dir=__DIR__;
        if (substr($dir,-1)!='/') $dir.='/';
        if (substr($mydir,-1)!='/') $mydir.='/';

        if (file_exists($dir.$file))
        {
            header("Location: ".$mydir.$file,TRUE,301);
            die();
        }
        else
        {
            die( "Serching for $dir$file ...");
        }
        
    }

    
    if (substr($file,0,9)=='index.php')
    {
        include __DIR__.'/inc/admin/redirect.php';
        
        $_SERVER['plus']['artykuly']=500;	
        $_SERVER['plus']['aktualnosci_wbp']=1000;
        $_SERVER['plus']['tworcy']=50000;
        $_SERVER['plus']['katalogi']=55000;
        
        
        $page=0;
        
        if (isset($_GET['id'])) $page=$_GET['id'];
        if (isset($_GET['mode']) && isset($_SERVER['plus'][$_GET['mode']]) ) $page+=$_SERVER['plus'][$_GET['mode']];
        
        if (isset($redirect[$page]))
        {
            header("Location: ".$redirect[$page],TRUE,301);
            die();
        }
        
        header("Location: /",TRUE,301);
        die();
        
    }


