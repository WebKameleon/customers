<?php
    require_once __DIR__.'/../system/fun.php';

    if (!isset($_GET['obj']) || !is_string($_GET['obj'])) return;

    $obj=$_GET['obj'];
    unset($_GET['obj']);

    $data=WBP::get_file_db($obj);
    
    $sort=isset($_GET['sort'])?$_GET['sort']:'nazwa'; 
    
    $result=array();
    
    foreach ($data AS $k=>$row)
    {
        if (isset($row[$sort])) $k=mb_strtolower(trim($row[$sort]),'utf8').'_'.$k;
        
        foreach ($_GET AS $g=>$v)
        {
            
            if ($v && in_array($g,array_keys($row)) && $row[$g]!=$v) continue 2;
         
            if (/*$obj=='objects' && */ $g=='kategoria' && in_array($v,array_keys($row)) && !$row[$v]) continue 2;

            if (/*$obj=='objects' && */ $g=='kategorie')
            {
                $jest=false;
                //mydie(explode(',',$v));
                foreach (explode(',',$v) AS $kat) if (in_array($kat,array_keys($row)) && $row[$kat])
                {
                    $jest=true;
                    break;
                }
                if (!$jest) continue 2;
            }
            
        }
        
        
        
        $result[$k] = $row;
    }
    
    ksort($result);
    
    $offset=0;
    if (isset($_GET['offset'])) $offset=$_GET['offset'];
    $limit=0;
    
    if (isset($_GET['limit']))
    {
        $limit=$_GET['limit'];
        $result2=array();
        
        foreach ($result AS $row)
        {
            if (isset($_GET['offset']) && $_GET['offset'])
            {
                $_GET['offset']--;
                continue;
            }
            $result2[]=$row;
            
            if (!(--$_GET['limit'])) break;
        }
        
    } else $result2=&$result;
    
    $ret=array('total'=>count($result),'offset'=>$offset,'limit'=>$limit,'data'=>$result2);
    if (isset($_GET['debug']))
    {
        header('Content-type: text/plain; charset=utf8');
        die(print_r($ret,1));
    }
    header('Content-type: application/json; charset=utf8');
    echo json_encode($ret);