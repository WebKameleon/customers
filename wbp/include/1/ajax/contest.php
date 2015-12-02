<?php

    require_once __DIR__.'/../kameleon/Google.php';
    require_once __DIR__.'/../kameleon/Spreadsheet.php';
    include_once __DIR__.'/../system/fun.php';
    
    function contest_ret($resp)
    {
        header('Content-type: application/json; charset=utf8');
        die(json_encode($resp));     
    }

    $data=$_REQUEST;
    
    $teraz=date('Y-m-d,H:i');
    
    $data['id'] = md5($data['id'].$data['surname']);
    $data['payment']='';
    
    $sid=$data['sid'];
    unset($data['sid']);
    
    $td_data=WBP::get_data($sid);
    
    $img_info=$data['files'];
    unset($data['files']);

    
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
    session_write_close();
    
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

    foreach($_FILES AS $f)
    {
        $index=$f['name'][$lp];
        $type=$f['type'][$lp];
        $size=$f['size'][$lp];
        $tmp=$f['tmp_name'][$lp];
        $lp++;
        
        
        
        $row=array('date'=>date('d-m-Y H:i:s'));
        $row=array_merge($row,$data,$img_info[$index]);
    
        $kat=isset($img_info[$index]['category']) ? $img_info[$index]['category'] : '';

        if ($kat)
        {
            $kategorie=Google::getFileChildren($parent_id,$kat);
    
            if (!count($kategorie['items']))
            {
                $kategoria=Google::createFolder($kat,$parent_id);
                $parent_id=$kategoria['id'];
            }
            else
            {
                $parent_id=$kategorie['items'][0]['id'];
            }
        }
        
        
        $filename=WBP::str_to_url($data['surname']).'-'.WBP::str_to_url($data['name']).'-'.WBP::str_to_url($row['title']).WBP::str_to_url($row['setno']).'-'.$index;
        
        $row['filename']=$filename;
        
        
        $gfile=Google::uploadFile($filename,$type,file_get_contents($tmp),$parent_id);
    
    
        if (isset($gfile['id']))
        {
            $thumbnailUrl=preg_replace('/[0-9]+$/','80',$gfile['thumbnailLink']);
            $row['thumbnail']=$thumbnailUrl;
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
    
    
    
    
    // Spreadsheet part 
    
    Spreadsheet::setToken(null);
    Spreadsheet::setToken($_SESSION['spreadsheets_access_token']);

    //$debug['td_data']=$td_data;
    
    $sheets=Spreadsheet::listWorksheets($td_data['drive']['id']);
    
    if (!is_array($sheets)) contest_ret(array('files'=>array(),'debug'=>$debug,'sheets'=>$sheets));
    
    //$debug['sheets']=$sheets;
    
    $worksheet_id=null;
    foreach ($sheets AS $id=>$contents)
    {
        if ($contents['title']==$td_data['title'])
        {
            $worksheet_id=$id;
            break;
        }
    }
    
    if (!$worksheet_id)
    {
        $worksheet=Spreadsheet::addWorksheet($td_data['drive']['id'],$td_data['title']);
        $id=$worksheet->id;
        $worksheet_id=end(explode('/',$worksheet->id));
    }
    
    if ($debug) $debug['rows']=array();
    foreach ($data_to_write_to_spreadsheet AS $row)
    {
        
        $header=Spreadsheet::getWorksheet($td_data['drive']['id'],$worksheet_id,'max-row=1');
        if (isset($header[0])) $header=$header[0];
        
        if ($debug) $debug['rows'][]=$row;
        if ($debug) $debug['header']=$header;
        
        foreach ($row AS $k=>$v)
        {
            if (!in_array($k,$header))
            {
                Spreadsheet::update_cell($td_data['drive']['id'],$worksheet_id,0,count($header),$k);
                $header[]=$k;
            }
        }
        
        
        $row=Spreadsheet::addListRow($td_data['drive']['id'],$worksheet_id,$row);


    }

    contest_ret(array('files'=>$response_images,'debug'=>$debug));
