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


    //mydie([$_FILES,$_POST,$_SERVER]);
    
    function contest_ret($resp)
    {
        header('Content-type: application/json; charset=utf8');
        die(json_encode($resp));     
    }

    $data=$_REQUEST;
    
    $teraz=date('Y-m-d,H:i');
    
    if (isset($data['id'])) $data['id'] = md5($data['id'].$data['surname']);
    $data['payment']='';
    
    if (isset($data['sid'])) {
        $sid=$data['sid'];
        unset($data['sid']);
        $td_data=WBP::get_data($sid);
    } else die();
    
    
    
    
    $data_to_write_to_spreadsheet=array();
    $response_images=array();
 
    $debug=array();
    $debug=false;
    
    if ($debug) $debug['data']=$data;
    if ($debug) $debug['td_data']=$td_data;
    if ($debug) $debug['sid']=$sid;
    
    
    $done_uri=$_SERVER['REQUEST_URI'];
    if ($pos=strpos($done_uri,'?')) $done_uri=substr($done_uri,0,$pos);
    $done_uri=dirname($done_uri).'/contest-done.php';
    
    
    @session_start();
    
    $img_info = isset($_SESSION['img_infos']) ? $_SESSION['img_infos'] : [];
    
    if (isset($data['files'])) {
        $img_info=$data['files'];
        foreach($data['files'] AS $k=>$v) $img_info[$k]=$v;
        unset($data['files']);
        $_SESSION['img_infos'] = $img_info;
    }
    

    if (isset($_SESSION['contest'])) $contest=$_SESSION['contest'];
    else {
        $contest=['files'=>[]];
    }
    $contest['data']=$_REQUEST;
    if (isset($contest['data']['files'])) unset($contest['data']['files']);
    
    //$_REQUEST['files']
    foreach ($_FILES AS $k=>$f) {
        $contest['files'][$k]=$f;
    }
    $_SESSION['contest']=$contest;





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
        
        $lp++;
        
        
        @session_write_close();
        
        if (isset($img_info[$index])) {
            
        
            $row=array('date'=>date('d-m-Y H:i:s'));
            $row=array_merge($row,$data,$img_info[$index]);
        
            $kat=isset($img_info[$index]['category']) ? $img_info[$index]['category'] : '';
    
            if ($kat)
            {
                $kategorie=Google::getFileChildren($parent_id,$kat);
        
                if (!count($kategorie['items']))
                {
                    sleep(1);
                    $kategorie=Google::getFileChildren($parent_id,$kat);
                    if (!count($kategorie['items'])) {
                        $kategoria=Google::createFolder($kat,$parent_id);
                        $parent_id=$kategoria['id'];                    
                    } else {
                        $parent_id=$kategorie['items'][0]['id'];
                    }
                    
    
                }
                else
                {
                    $parent_id=$kategorie['items'][0]['id'];
                }
            }
            
            
            $filename=WBP::str_to_url($data['surname']).'-'.WBP::str_to_url($data['name']).'-'.WBP::str_to_url($row['title']).WBP::str_to_url($row['setno']).'-'.$index;
            
            $row['filename']=$filename;
            
            $gfile=Google::uploadFile($filename,$type,$blob,$parent_id);
            $blob='';
        
            if (isset($gfile['id']))
            {
                $thumbnailUrl=preg_replace('/[0-9]+$/','80',$gfile['thumbnailLink']);
                $row['thumbnail']=$thumbnailUrl;
                $row['phone']=str_replace('+48','',$row['phone']);
                $row['phone']=str_replace('+','0',$row['phone']);
                $row['description']=str_replace("\r",'',$row['description']);
                $row['description']=str_replace("\n",' ',$row['description']);
                
                $data_to_write_to_spreadsheet[]=$row;
                $url=dirname($_SERVER['REQUEST_URI']).'/contest-bckd.php?sid='.$sid.'&id='.$gfile['id'];
                
                $response_images[]=array(
                    'name'=>$index,
                    'size'=>$size,
                    'type'=>$type,
                    'url'=>$url,
                    'thumbnailUrl'=>$thumbnailUrl,
                    'deleteUrl'=>$url,
                    'deleteType'=>'DELETE',
                    'id'=>$sid.$data['id'],
                    'done'=>$done_uri
                );
            }
            else
            {
                contest_ret(array('files'=>array(),'debug'=>$debug,'gfile'=>$gfile));    
            }
        }
    }
    
    
    
    WBP::dumpJson($sid.$data['id'].'-'.$token.'.json',[
        'response_images' => $response_images,
        'td_data' => $td_data,
        'data_to_write_to_spreadsheet' => $data_to_write_to_spreadsheet,
        'storage_copy' => $storage_copy,
        'storage_chunk_files' => $storage_chunk_files
    ]);
    
    
    contest_ret(array('files'=>$response_images,'debug'=>$debug,'get'=>$_GET,'ssid'=>session_id()));
