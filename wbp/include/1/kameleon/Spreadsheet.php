<?php

class Spreadsheet extends Google {


    protected static function request($url,$method='GET',$data=null,$headers=array())
    {
        return parent::request($url,$method,$data,'spreadsheets','xml',null,$headers);
    }


    public static function listWorksheets($fileId) {
        $url='https://spreadsheets.google.com/feeds/worksheets/'.$fileId.'/private/full';
    
        $worksheets = self::request($url);
    
        
        
        $result=array();
        
        foreach ($worksheets->entry AS $entry) {
            $result[basename($entry->id)]=array('id'=>(String)$entry->id,'title'=>(String)$entry->title);
        }
        
    
        return $result;
    }
        
    
    public static function getWorksheet($fileId, $worksheetId, $params='') {
        
        $url='https://spreadsheets.google.com/feeds/cells/'.$fileId.'/'.$worksheetId.'/private/full';
        //else $url='https://spreadsheets.google.com/feeds/worksheets/'.$fileId.'/private/full/'.$worksheetId;
        
        if ($params) $url.='?'.$params;
        
        $result=array();
        $max_row=0;
        $max_col=0;
        
        $worksheet = self::request($url);
        

        foreach($worksheet->entry AS $entry) {
            $rc=explode('C',basename($entry->id));
            $rc[0]=substr($rc[0],1);
            
            $row=$rc[0]-1;
            $col=$rc[1]-1;
            $result[$row][$col] = (String)$entry->content;
            $max_row=max($max_row,$row);
            $max_col=max($max_col,$col);
        }
    
    
        for ($r=0;$r<=$max_row;$r++) {
            for ($c=0;$c<=$max_col;$c++) {
                if (!isset($result[$r][$c])) $result[$r][$c]=null;
            }
            ksort($result[$r]);
        }
        
        ksort($result);

        
    
        return $result;
    }

    public static function worksheet2db_array($data,$headers,$notnulls)
    {
        $res=array();
        $db=array();


        for ($run=0;$run<2;$run++)
        {
            foreach ($data[0] AS $i=>$h) {
                $h=mb_strtolower($h,'utf-8');
                
                foreach ($headers AS $header) {
                    $words=$header[0];
                    $db_keys=$header[1];
                    if (!is_array($words)) $words=array($words);
                    if (!is_array($db_keys)) $db_keys=array($db_keys);
                    
                    foreach ($words AS $word) {
                        $word=mb_strtolower($word,'utf-8');
                        
                        if ($h==$word || ($run==1 && strstr($h,$word))) {
                            foreach ($db_keys AS $db_key) {
                                if (!isset($db[$db_key])) $db[$db_key]=$i;
                            }
                            break 2;
                        }
                    }
                    
                    
                }
            }
        }


        
        if (!is_array($notnulls)) $notnulls=array($notnulls);
        
        foreach ($data AS $i=>$r) {
            if ($i==0) continue;
            
            $maybe=true;
            foreach($notnulls AS $notnull) if (!isset($db[$notnull]) || !isset($data[$i][$db[$notnull]]) || !$data[$i][$db[$notnull]]) $maybe=false;
            if (!$maybe) continue;
            
            $rec=array('__row__'=>$i+1);
            foreach($db AS $k=>$index) $rec[$k]=$r[$index];
            $res[]=$rec;
        }
        
        
        return $res;
    }
    
    
    public static function update_cell($fileId, $worksheetId, $row, $col,  $value)
    {
        $key=$fileId.'/'.$worksheetId;

        
        $r=$row+1;
        $c=$col+1;
        
        
        $url='https://spreadsheets.google.com/feeds/cells/'.$key.'/private/full/R'.$r.'C'.$c;
        
        
        $entry='<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                    <id>https://spreadsheets.google.com/feeds/cells/'.$key.'/private/full/R'.$r.'C'.$c.'</id>
                    <link rel="edit" type="application/atom+xml"
                        href="https://spreadsheets.google.com/feeds/cells/'.$key.'/private/full/R'.$r.'C'.$c.'"/>
                    <gs:cell row="'.$r.'" col="'.$c.'" inputValue="'.$value.'"/>
                </entry>';
            
        $result=self::request($url,'PUT',$entry,array('Content-Type'=>'application/atom+xml','If-Match'=>'*'));
    
    
        return $result;
    }
    
    
    public static function addWorksheet($id,$title,$rows=100,$cols=40)
    {
        $xml='<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                <title>'.$title.'</title>
                <gs:rowCount>'.$rows.'</gs:rowCount>
                <gs:colCount>'.$cols.'</gs:colCount>
            </entry>';
        
        return self::request('https://spreadsheets.google.com/feeds/worksheets/'.$id.'/private/full','POST',$xml);
        
    }
    
    
    public static function addListRow($fileId,$worksheetId,$row)
    {
       $key=$fileId.'/'.$worksheetId;
       
       
       $xml='<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">';
       
       foreach($row AS $k=>$v) $xml.='<gsx:'.$k.'>'.$v.'</gsx:'.$k.'>';
       
       $xml.='</entry>';
       
       return self::request('https://spreadsheets.google.com/feeds/list/'.$key.'/private/full','POST',$xml);
       
       
    }
    
    
    

}