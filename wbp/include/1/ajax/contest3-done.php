<?php
    ini_set('display_errors',true);
    
    if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
        require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
    }
    use google\appengine\api\cloud_storage\CloudStorageTools;

 
    include_once __DIR__.'/../system/fun.php';
    
    
    WBP::imap_utf8($_REQUEST);
    WBP::dumpInput();

    $debug=array();
    $debug=false;

    
    function contest_ret($resp)
    {
        header('Content-type: application/json; charset=utf8');
        die(json_encode($resp));     
    }

    $data=$_REQUEST;
    
        
    if (!isset($data['id'])) 
        contest_ret(['error'=>true,'message'=>'no id sent']);        
    
    $client_data = WBP::getContestDir($data['id']);
    
    if (!isset($client_data['contents']['data.json']))
        $client_data['contents']['data.json'] = [];
        

    foreach ($data AS $k=>$v) {
        if (!isset($client_data['contents']['data.json'][$k]) || json_encode($client_data['contents']['data.json'][$k])!=json_encode($v)) {
            $client_data['contents']['data.json'][$k] = $v;
        }
    }
    $client_data['contents']['data.json']['finished'] = date('c'); 
  
    if ($debug) $debug['save'] = 1;
    file_put_contents($client_data['dir'].'/data.json',json_encode($client_data['contents']['data.json']));

    
    die("OK");
    
   