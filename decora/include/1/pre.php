<?php
    /*
    if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine'))
    {
        $host=strtolower($_SERVER['HTTP_HOST']);
        if (strstr($host,'vidella')) include(__DIR__.'/vidella.php');
        
        echo "R:$redirect";
    }
    */


    $appengine_pdo = array('dsn'=>'mysql:unix_socket=/cloudsql/gammanet-general:decora;charset=utf8;dbname=decora','user'=>'decora','pass'=>'JgierZ');
    $google_remote_pdo = array('dsn'=>'mysql:host=173.194.109.181;charset=utf8;dbname=decora','user'=>'decora','pass'=>'JgierZ');
    $local_pdo = array('dsn'=>'pgsql:host=dbpremium;dbname=cmspremium','user'=>'cmsdecora','pass'=>'j4th84it44h8rg');

    
    if (!isset($lang) && isset($session)) $lang=$session['lang'];


    if (!isset($_SERVER['dbh']) || !$_SERVER['dbh']) {
        try {
            
            if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine'))
            {
                $_SERVER['dbh']=new PDO($appengine_pdo['dsn'],$appengine_pdo['user'],$appengine_pdo['pass']);
            }
            elseif ($_SERVER['SERVER_ADDR']=='92.222.171.33')
            {
                require_once(__DIR__.'/pdo.php');
                //mydie(Bootstrap::$main->getConn());
                $_SERVER['dbh']=new PDO2(Bootstrap::$main->getConn());
            }
            else
            {
                require_once(__DIR__.'/pdo.php');                
                $_SERVER['dbh']=new PDO2(array($local_pdo,$google_remote_pdo));
                //$_SERVER['dbh']=new PDO2(array($google_remote_pdo));

            }
        } catch (Exception $e)
        {
            die(print_r($e,1));
        }
    }  
    
    $dbh=&$_SERVER['dbh']; 
    include_once(__DIR__.'/dict.php');
    include_once(__DIR__.'/row.php');
    $translate=new dict($dbh,$lang);
