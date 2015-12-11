<?php

    include __DIR__.'/inc/redirect.php';
    
	
    $_SERVER['plus']['aktualnosci_extended']=500;

    
    $url='/';
    $token='/';
    
    if (isset($_GET['mode'])) switch ($_GET['mode']) {
        case 'aktualnosci_extended':
            if (isset($_GET['id']) && isset($_GET['lang'])) {
                $token=strtolower($_GET['lang']).':'.($_GET['id']+500);
            } elseif (isset($_GET['lang'])) {
                if (!isset($_GET['d'])) {
                    $token=strtolower($_GET['lang']).':23';
                } elseif ($_GET['d']==2) {
                    if (strtolower($_GET['lang'])=='pl') $token='pl:1';
                    if (strtolower($_GET['lang'])=='en') $token='en:17';
                }
            }
            break;
        
        case 'galeria':
            if (isset($_GET['d']) && isset($_GET['lang'])) {
                $token=strtolower($_GET['lang']).':'.($_GET['d']-7000);
            } elseif (isset($_GET['id']) && isset($_GET['lang'])) {
                if ($_GET['id']=='8033') $token=strtolower($_GET['lang']).':3';
                if ($_GET['id']=='8030') $token=strtolower($_GET['lang']).':6';
            }
            break;
            
        case 'imprezy':
            if (isset($_GET['lang'])) {
                $token=strtolower($_GET['lang']).':3';
            }
            break;
        
        case 'artykuly':
            if (isset($_GET['id']) && isset($_GET['lang'])) {
                $arr=[
                    'pl:100'=>'pl:21',
                    'pl:26'=>'pl:2',
                    'pl:27'=>'pl:7',
                    'pl:31'=>'pl:8',
                    'en:26'=>'en:2',
                    'en:27'=>'en:7',
                    'en:31'=>'en:8',
                ];
                
                $token2=strtolower($_GET['lang']).':'.$_GET['id'];
                if (isset($arr[$token2])) $token=$arr[$token2]; 
            }
            break;
    }
    
    if (isset($redirect[$token])) $url=$redirect[$token];
    
    
    
    die('<a href="'.$url.'">'.$url.'</a>');
    
    
    header("Location: ".$url,TRUE,301);
    die();    

