<?php

    include __DIR__.'/inc/redirect.php';
    
	
    $_SERVER['plus']['aktualnosci_extended']=500;

    echo '<pre>';
    print_r($redirect);
    echo '</pre>';
    
    $url='/';
    $token='/';
    
    if (isset($_GET['mode'])) switch ($_GET['mode']) {
        case 'aktualnosci_extended':
            if (isset($_GET['id']) && isset($_GET['lang'])) {
                $token=strtolower($_GET['lang']).':'.$_GET['id'];
            }
            break;
            
    }
    
    if (isset($redirect[$token])) $url=$redirect[$token];
    
    
    
    die('<a href="'.$url.'">'.$url.'</a>');
    
    
    header("Location: ".$url,TRUE,301);
    die();    

