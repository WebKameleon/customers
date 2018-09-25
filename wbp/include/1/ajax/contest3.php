<?php
    ini_set('display_errors',true);
    //error_reporting(15);
    
    //if (rand(0,2)+0==0) myd988die();
    
    if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
        require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
    }
    use google\appengine\api\cloud_storage\CloudStorageTools;


    require_once __DIR__.'/../kameleon/Google.php';
    require_once __DIR__.'/../kameleon/Spreadsheet.php';
    include_once __DIR__.'/../system/fun.php';
    
    
    WBP::imap_utf8($_REQUEST);
    WBP::imap_utf8($_FILES);
    WBP::dumpInput();

    $debug=array();
    $debug=false;

    //mydie([$_FILES,$_POST,$_SERVER]);
    
    function contest_ret($resp)
    {
        header('Content-type: application/json; charset=utf8');
        die(json_encode($resp));     
    }

    $data=$_REQUEST;
    
    $teraz=date('Y-m-d,H:i');
        
    if (!isset($data['id'])) 
        contest_ret(['error'=>true,'message'=>'no id sent']);        
    
    $client_data = WBP::getContestDir($data['id']);
    
    if (!isset($client_data['contents']['data.json']))
        $client_data['contents']['data.json'] = [];
        
    $saveRequired=false;
    foreach ($data AS $k=>$v) {
        if (!isset($client_data['contents']['data.json'][$k]) || $client_data['contents']['data.json'][$k]!=$v) {
            $client_data['contents']['data.json'][$k] = $v;
            $saveRequired = true;
        }
    }
    if ($saveRequired) {
        if ($debug) $debug['save'] = 1;
        file_put_contents($client_data['dir'].'/data.json',json_encode($client_data['contents']['data.json']));
    }
    
    $data['payment']='';
    
    if (isset($data['sid'])) {
        $sid=$data['sid'];
        unset($data['sid']);
        $td_data=WBP::get_data($sid);
    } else contest_ret(['error'=>true,'message'=>'no sid sent']); 
    

    $data_to_write_to_spreadsheet=array();
    $response_images=array();
 

    
    if ($debug) $debug['data']=$data;
    if ($debug) $debug['td_data']=$td_data;
    if ($debug) $debug['sid']=$sid;
    
    
    $done_uri=$_SERVER['REQUEST_URI'];
    if ($pos=strpos($done_uri,'?')) $done_uri=substr($done_uri,0,$pos);
    $done_uri=dirname($done_uri).'/contest-done.php';
    
    
    @session_start();
    
   
    
    //$_REQUEST['files']
    foreach ($_FILES AS $k=>$f) {
        $contest['files'][$k]=$f;
    }
   

    Google::setToken(null);
    if (!isset($_SESSION['drive_access_token'])) $_SESSION['drive_access_token']=$td_data['tokens']['drive'];
    $token=Google::setToken($_SESSION['drive_access_token']);    
    foreach($token AS $k=>$v) $td_data['tokens']['drive']->$k=$v;
    $_SESSION['drive_access_token']=$td_data['tokens']['drive'];
        
    
    Spreadsheet::setToken(null);
    if (!isset($_SESSION['spreadsheets_access_token'])) $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
    $token=Spreadsheet::setToken($_SESSION['spreadsheets_access_token']);    
    foreach($token AS $k=>$v) $td_data['tokens']['spreadsheets']->$k=$v;
    $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
    
    if ($debug) $debug['session']=$_SESSION;
    
    
    if (!$td_data['drive']['id']) contest_ret(array('files'=>array(),'error'=>'Brak arkusza'));
    
    
    // Drive part
    Google::setToken($_SESSION['drive_access_token']);
    $file=Google::getFile($td_data['drive']['id']);  
     
    
    if (!isset($file['parents'])) contest_ret(array('files'=>array(),'debug'=>$debug,'file'=>$file));
    
    foreach($file['parents'] AS $parent)
    {
        if (!$parent['isRoot'])
        {
            $parent_id=$parent['id'];
            break;
        }
    }
    $parent_is_root=$parent['isRoot'];
    
    $lp=0;
    
    if ($debug) $debug['f']=$_FILES;
    
    $dir_suffix='/wbp_img_upload/';
    if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
        $dir_prefix='gs://'.CloudStorageTools::getDefaultGoogleStorageBucketName().$dir_suffix;
    } else {
        $dir_prefix=sys_get_temp_dir().$dir_suffix;
        if (!file_exists($dir_prefix)) mkdir($dir_prefix,0755);
    }

    $storage_chunk_files = [];

    foreach($_FILES AS $f)
    {
        if ($f['name'][$lp]=='blob' && isset($_SERVER['HTTP_CONTENT_DISPOSITION']) ) {
            $a=[];
            if (preg_match('/filename="([^"]+)"/',$_SERVER['HTTP_CONTENT_DISPOSITION'],$a)) {
                $f['name'][$lp]=urldecode($a[1]);
            }
        }
        
        if (isset($_SERVER['HTTP_CONTENT_RANGE'])) {
            $max_chunks=30;
		    $range=str_replace('bytes ', '', $_SERVER['HTTP_CONTENT_RANGE']);
            $range=explode('/',$range);
		    $range[0]=explode('-',$range[0]);
		    
		    $token='chunk.'.md5($f['name'][$lp]).'.'.$range[1].'.'.$data['ip'];
            $total_size=$range[1];
            $chunk_size=$range[0][1]-$range[0][0]+1;
            $chunk=1+floor($range[0][0]/$chunk_size);
            //chunk 0 => last chunk
            if ($total_size-1==$range[0][1]) $chunk=0;
            
            $storage_copy = $dir_prefix.$token.'.bin';
             
            $fi = $dir_prefix.$token.'.'.$chunk;
            if (file_exists($fi)) unlink($fi);
            move_uploaded_file($f['tmp_name'][$lp],$fi);
            
            
            $size=0;
            $last_chunk_size=0;
            $allChunks=['total'=>$total_size,'chunks'=>[]];
            for ($i=0;$i<=$max_chunks;$i++) {
                $fi=$dir_prefix.$token.'.'.$i;
                if (file_exists($fi)) {
                    if ($i==0) $last_chunk_size=filesize($fi);
                    $size+=filesize($fi);
                    $allChunks['chunks'][$i]=filesize($fi);
                    $storage_chunk_files[]=$fi;
                } 
                if ($total_size==$size) {
                    $chunk=$i;
                    break;
                }
            }
            $allChunks['size'] = $size;
            
            if (file_exists($storage_copy)) {
                $blob = file_get_contents($storage_copy);
            } else {
                if ($total_size==$size) {
                    $blob='';
                    for ($i=1;$i<=$chunk;$i++) {
                        $fi=$dir_prefix.$token.'.'.$i;
                        if (file_exists($fi)) {
                            $blob.=file_get_contents($fi);
                            unlink($fi);
                        }                        
                    }
                    $fi=$dir_prefix.$token.'.0';
                    if (file_exists($fi)) {
                        $blob.=file_get_contents($fi);
                        unlink($fi);
                    }
                    file_put_contents($storage_copy,$blob);
                    
                } else {
                    contest_ret(array('files'=>array(),'chunk'=>$chunk.'/'.ceil($total_size/$chunk_size), 'range'=>$_SERVER['HTTP_CONTENT_RANGE'], 'debug'=>$allChunks));
                }                
            }
 
        } else {
            $tmp=$f['tmp_name'][$lp];
            $storage_copy=$tmp;
            $blob=file_get_contents($tmp);
            $size=$f['size'][$lp];
            $token='nochunk.'.md5($f['name'][$lp]).'.'.$size.'.'.$data['ip'];
        }
        
        
        $index=$f['name'][$lp];
        $type=$f['type'][$lp];
        
        
        //if (strstr($f['name'][$lp],'canon2')) mydd3ie($f);
        

        
        file_put_contents($client_data['dir'].'/'.$f['name'][$lp],$blob);
        
        
        $json = [
            'type' => 'file',
            'name' => $index,
            'fileType' => $type,
            'size' => $size,
            'thumbnailUrl' => WBP::getContestPreview($client_data['dir'].'/'.$f['name'][$lp],dirname($_SERVER['REQUEST_URI']),100,true),
            'url' => WBP::getContestPreview($client_data['dir'].'/'.$f['name'][$lp],dirname($_SERVER['REQUEST_URI']),1000,false),
            
        ];
        
        file_put_contents($client_data['dir'].'/'.$f['name'][$lp].'.json',json_encode($json));
        
        $response_images[]=$json;
        
        $lp++;
        
    }
    
    /*
    
    WBP::dumpJson($sid.$data['id'].'-'.$token.'.json',[
        'response_images' => $response_images,
        'td_data' => $td_data,
        'data_to_write_to_spreadsheet' => $data_to_write_to_spreadsheet,
        'storage_copy' => $storage_copy,
        'storage_chunk_files' => $storage_chunk_files
    ]);
    
    */
    
    
    contest_ret(array('files'=>$response_images,'debug'=>$debug,'get'=>$_GET,'ssid'=>session_id()));
