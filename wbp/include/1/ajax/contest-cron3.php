<?php

use google\appengine\api\cloud_storage\CloudStorageTools;

require_once __DIR__.'/../kameleon/Google.php';
require_once __DIR__.'/../kameleon/Spreadsheet.php';
include_once __DIR__.'/../system/fun.php';


function toSpreadsheet($rows,$td_data) {
    
    Spreadsheet::setToken(null);
    $token=Spreadsheet::setToken($td_data['tokens']['spreadsheets']);
    $td_data['tokens']['spreadsheets']=new stdClass();
    foreach($token AS $k=>$v) $td_data['tokens']['spreadsheets']->$k=$v;
    Spreadsheet::setToken($td_data['tokens']['spreadsheets']);
    $sheets=Spreadsheet::listWorksheets($td_data['drive']['id']);
    
    if (!is_array($sheets)) {
        return false;
    }
    
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
    

    $header=Spreadsheet::getWorksheet($td_data['drive']['id'],$worksheet_id,'max-row=1');
    if (isset($header[0])) $header=$header[0];
    
    foreach($rows AS $row) {
        foreach ($row AS $k=>$v)
        {
            if (!in_array($k,$header))
            {
                @Spreadsheet::update_cell($td_data['drive']['id'],$worksheet_id,0,count($header),$k);
                $header[]=$k;
            }
        }
        
        $row=Spreadsheet::addListRow($td_data['drive']['id'],$worksheet_id,$row);
        if (!$row) return false;
    }
      

    
    return true;
}


$clients=WBP::getContestClients();



if (count($clients)==0) die();

for ($i=0; $i<count($clients); $i++) {
    $client_data = WBP::getContestDir(str_replace('/','',$clients[$i]));
    if (!isset($client_data['contents']) || !isset($client_data['contents']['data.json']))
        continue;
    $data = $client_data['contents']['data.json'];
    
    if (!isset($data['finished']))
        continue;
    
    $td_data=WBP::get_data($data['sid']);
    
    Google::setToken(null);
    $token=Google::setToken($td_data['tokens']['drive']);    
    foreach($token AS $k=>$v) $td_data['tokens']['drive']->$k=$v;
    $file=Google::getFile($td_data['drive']['id']);
        
    foreach($file['parents'] AS $parent)
    {
        $client_parent_id=$parent['id'];
        if (!$parent['isRoot'])
            break;
        
    }
    $parent_is_root=$parent['isRoot'];
    
    foreach($data['files'] AS $fname=>$f) {
        
        if (isset($client_data['contents'][$fname.'.json']['gid']))
            continue;
        
        $parent_id = $client_parent_id;
        $kat=isset($f['category']) ? $f['category'] : '';
        
        if ($kat) {
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
        
        $filename=WBP::str_to_url($data['surname']).'-'.WBP::str_to_url($data['name']).'-'.WBP::str_to_url($f['title']).WBP::str_to_url($f['setno']).'-'.WBP::str_to_url($fname);
        
        $blob=file_get_contents($client_data['dir'].'/'.$fname);
        $gfile=Google::uploadFile($filename,$client_data['contents'][$fname.'.json']['fileType'],$blob,$parent_id);
    
        if (isset($gfile['id'])) {
            $client_data['contents'][$fname.'.json']['gid'] = $gfile['id'];
            $client_data['contents'][$fname.'.json']['gtitle'] = $gfile['title'];
        }
        
        file_put_contents($client_data['dir'].'/'.$fname.'.json',json_encode($client_data['contents'][$fname.'.json']));
        
        if ($client_data['contents'][$fname.'.json']['scp'] && file_exists($client_data['contents'][$fname.'.json']['scp']))
            unlink($client_data['contents'][$fname.'.json']['scp']);
        
        
    }
    $rows=[];
    $teraz=date('Y-m-d,H:i');
    foreach($data['files'] AS $fname=>$f) {
        
        $row = $data;
        
        $row['date'] = $teraz;
        
        unset($row['sid']);
        unset($row['files']);
        unset($row['t']);
        unset($row['finished']);
        
        foreach ($data['files'][$fname] AS $k=>$v) {
            $row[$k]=$v;
        }
        
        $row['filename'] = $client_data['contents'][$fname.'.json']['gtitle'];
        $row['thumbnail'] = $client_data['contents'][$fname.'.json']['thumbnailUrl'];
        
        $rows[]=$row;
        
        
        
        
        
    }
    
    $ok=toSpreadsheet($rows,$td_data);
    

    if ($ok) {
        $mail='Dziękujemy za przesłanie następujących zdjęć / Thank you for submitting the following photographs<ul style="list-style: none">';
        
        foreach ($rows AS $row) {
            $mail.='<li style="padding:5px"><img src="'.$row['thumbnail'].'" align="absmiddle"/> <b>'.$row['title'].'</b> ('.$row['description'].')</li>';
        }
        $mail.='</ul>';
        
        $mail.='<p>Po weryfikacji zdjęcia wezmą udział w konkursie / After verification, the photographs will take part in the competition: <b>'.$td_data['title'].'</b></p>';
        $mail.='<p>WBPiCAK - Dział FOTOGRAFIA / WBPiCAK - Department of PHOTOGRAPHY</p>';
        
        WBP::mail($td_data['drive']['email'],$data['email'],$td_data['title'],$mail);
    
        WBP::removeContestFolder($data['id']);
    }

    
    
    break;
    
}


