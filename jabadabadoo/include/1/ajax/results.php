<?php
    foreach ($_REQUEST AS $k=>$v)
    {
        if (substr($k,0,7)=='wyprawy') {
            $_REQUEST[substr($k,8)]=$v;
            unset ($_REQUEST[$k]);
        }
    }


    $data=json_decode(file_get_contents(__DIR__.'/../system/wyprawy.json'),true);
    
    $data=$data['db'];
    
    
    $sort=isset($_REQUEST['sort'])?$_REQUEST['sort']:'price'; 
    
    $result=array();
    
    //mydie($data);
    foreach ($data AS $k=>$row)
    {
        if (isset($row[$sort])) $k=mb_strtolower(trim($row[$sort]),'utf8').'_'.$k;
        
        foreach ($_REQUEST AS $g=>$v)
        {
            if ($g[0]=='_') continue;
            
            if ($g=='d_from') {
                if ($v && strtotime($v)>$row['_to']) continue 2; 
		//else
		//if ($v && time() > $row['_to']) continue 2;
            } elseif ($g=='d_to') {
                if ($v && strtotime($v)<$row['_from']) continue 2;
            } else {
                if ($v && in_array($g,array_keys($row)) && $row[$g]!=$v) continue 2;
            }
    
        }
        $result[$k] = $row;
    }
    
    ksort($result);
    
    if (isset($_REQUEST['_uni']) && $_REQUEST['_uni'])
    {
        $trips=[];
        foreach($result AS $k=>$v)
        {
            if (isset($trips[$v['page']])) unset($result[$k]);
            $trips[$v['page']]=true;
        }
    }
    
    $offset=0;
    if (isset($_REQUEST['offset'])) $offset=$_REQUEST['offset'];
    $limit=0;
    
    if (isset($_REQUEST['home_link']) && $_REQUEST['home_link']=='.') $_REQUEST['home_link']='';
    
    if (isset($_REQUEST['limit']))
    {
        $limit=$_REQUEST['limit'];
        $result2=array();
        
        
        
        foreach ($result AS $row)
        {
            if (isset($_REQUEST['offset']) && $_REQUEST['offset'])
            {
                $_REQUEST['offset']--;
                continue;
            }
            if (isset($_REQUEST['uimages'])) $row['img']=$_REQUEST['uimages'].$row['img'];
            
            if (class_exists('Bootstrap')) {
                $row['url']=Bootstrap::$main->kameleon->href('','',$row['page'],0,1);
            } elseif (isset($_REQUEST['home_link']) ) {
                $row['url']=$_REQUEST['home_link'].$row['url'];
            }
            
            $row['url'].='?f='.$row['d_from'];
            
            if (isset($row['confirm'])) $row['confirm']+=0;
            if (isset($row['confirm'])) $row['confirm2']=$row['confirm3']=$row['confirm'];
            
            if (isset($row['confirm'])) $row['!confirm']=1-$row['confirm'];
            else $row['!confirm']=1;
            
            if (isset($row['!confirm'])) $row['!confirm2']=$row['!confirm3']=$row['!confirm'];
            
            if (isset($row['flag1'])) $row['!flag1']=1-$row['flag1'];
            else $row['!flag1']=1;
            
            if (isset($row['flag2'])) $row['!flag2']=1-$row['flag2'];
            else $row['!flag2']=1;
            
            if (isset($row['nego'])) $row['!nego']=$row['nego']?0:1;
            else $row['!nego']=1;
            
            if (!isset($row['nego'])) $row['nego']=0;
            
            if (isset($row['nego'])) $row['nego1']=$row['nego2']=$row['nego3']=$row['nego'];
            if (isset($row['!nego'])) $row['!nego1']=$row['!nego2']=$row['!nego3']=$row['!nego'];

            
            $result2[]=$row;
            
            if (!(--$_REQUEST['limit'])) break;
        }
        
    } else $result2=&$result;
    
    $ret=array('total'=>count($result),'offset'=>$offset,'limit'=>$limit,'data'=>$result2);
    
    
    
    
    
    if (isset($_REQUEST['debug'])) {
        if (function_exists('mydie')) mydie($ret);
        die('<pre>'.print_r($ret,1));
    }
    
    header('Content-type: application/json; charset=utf8');
    die(json_encode($ret,JSON_NUMERIC_CHECK));    
