<?php

use google\appengine\api\cloud_storage\CloudStorageTools;


require_once __DIR__.'/../kameleon/Google.php';
require_once __DIR__.'/../kameleon/Spreadsheet.php';
include_once __DIR__.'/../system/fun.php';



function toSpreadsheet($data) {

    $td_data=$data['td_data'];
    $data_to_write_to_spreadsheet=$data['data_to_write_to_spreadsheet'];

    Spreadsheet::setToken(null);
    if (!isset($_SESSION['spreadsheets_access_token'])) $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
    $token=Spreadsheet::setToken($_SESSION['spreadsheets_access_token']);
    
    $td_data['tokens']['spreadsheets']=new stdClass();
    foreach($token AS $k=>$v) $td_data['tokens']['spreadsheets']->$k=$v;
    $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
    Spreadsheet::setToken($_SESSION['spreadsheets_access_token']);

    
    $sheets=Spreadsheet::listWorksheets($td_data['drive']['id']);
    
    
    if (!is_array($sheets)) return false;
    
    
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
    
    foreach ($data_to_write_to_spreadsheet AS $row)
    {
        
        $header=Spreadsheet::getWorksheet($td_data['drive']['id'],$worksheet_id,'max-row=1');
        if (isset($header[0])) $header=$header[0];
        

        
        foreach ($row AS $k=>$v)
        {
            if (!in_array($k,$header))
            {
                @Spreadsheet::update_cell($td_data['drive']['id'],$worksheet_id,0,count($header),$k);
                $header[]=$k;
            }
        }
        
        
        $row=@Spreadsheet::addListRow($td_data['drive']['id'],$worksheet_id,$row);


    }

    return true;
}


$photos=WBP::getJsonFiles();
foreach ($photos AS $f=>$photo) {
    if(toSpreadsheet($photo)) WBP::rmJsonFile($f);
}