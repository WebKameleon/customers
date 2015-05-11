<?php
    require_once __DIR__.'/../system/fun.php';
    $date_format='d.m.Y';
    
    if (isset($_GET['current']) && $_GET['current'])
    {
        $current=true;
        $wystawy=$przyszle_wystawy=WBP::get_data('aktualne_wystawy');
    }
    else
    {
        $current=false;
        $wystawy=WBP::get_data('wystawy');
        $przyszle_wystawy=WBP::get_data('aktualne_wystawy');
    }
    
    if (isset($_GET['current'])) unset($_GET['current']);
    
    $kameleon=false;
    if (isset($_GET['kameleon']))
    {
        $kameleon=$_GET['kameleon'];
        unset($_GET['kameleon']);
        
    }
    $me='/';
    if (isset($_GET['me']))
    {
        $me=$_GET['me'];
        unset($_GET['me']);
        
    }
    
    
    $result=array();
    $now=time();
    
    
    $offset=0;
    if (isset($_GET['offset'])) $offset=$_GET['offset'];

    
    foreach ($przyszle_wystawy AS $k=>$w)
    {
        if ($w['from']<$now) unset($przyszle_wystawy[$k]);
    }
    
    ksort($przyszle_wystawy);
    
    if ($current)
    {
        ksort($wystawy);
        foreach($wystawy AS $w)
        {
            //if ($w['from']>$now || $w['to']<$now) continue;
            $result[]=$w;
        }
    }
    else
    {
        $result=&$wystawy;
    }
    
    $objects=WBP::get_file_db('objects');
    
    $result2=array();
    $total=0;
    
    $limit=0;
    if (isset($_GET['limit'])) $limit=$_GET['limit'];
    
    foreach ($result AS $row)
    {
        if (!$current && $row['to']>$now) continue;
        
        foreach ($_GET AS $g=>$v)
        {
            if ($g=='offset' || $g=='limit' || $g=='debug') continue;
            
            if (substr($g,0,7)=='object_' && $v)
            {
                $row[$g]='';
                if (isset($objects[$row['object']]) && is_array($objects[$row['object']])) foreach ($objects[$row['object']] AS $k=>$o) $row['object_'.$k]=$o;
                
            }
            
            if ($v && in_array($g,array_keys($row)) && $row[$g]!=$v) continue 2;
        }
        $total++;
        
        if (isset($_GET['offset']) && $_GET['offset'])
        {
            $_GET['offset']--;
            continue;
        }
        
        
        if (isset($_GET['limit'])) if ((--$_GET['limit'])<0) continue;
    
        $result2[]=$row;
    }
    
    

    
    foreach ($result2 AS &$r)
    {
        if (isset($objects[$r['object']]) && is_array($objects[$r['object']])) foreach ($objects[$r['object']] AS $k=>$o) if ($o) $r['object_'.$k]=$o;

        
        $r['from']=date($date_format,$r['from']);
        $r['to']=date($date_format,$r['to']);
        if ($kameleon)
        {
            $r['href']=$kameleon.'index/get/'.$r['id'];
        }
        else
        {
            $r['href']=WBP::relative_dir($me,$r['href']);
        }
	$r['href']=preg_replace('~index\.(html|php)$~','',$r['href']);
        
        foreach($przyszle_wystawy AS $w)
        {
            if ($w['id']==$r['id'])
            {
                foreach ($objects[$w['object']] AS $k=>$o) if ($o) $w['object_'.$k]=$o;
                
                $w['from']=date($date_format,$w['from']);
                $w['to']=date($date_format,$w['to']);
                unset($w['id']);
                unset($w['href']);
                unset($w['title']);
                unset($w['trailer']);
                unset($w['object']);
                
                foreach ($w AS $k=>$n) $r['next_'.$k]=$n;
                
                break;
            }
        }
        if (!isset($r['next_from']))
        {
            $r['next_from']=null;
            $r['next_to']=null;
            $r['next_object_nazwa']=null;
            $r['next_object_miasto']=null;
        }
    }
    
    $ret=array('total'=>$total,'offset'=>$offset,'data'=>$result2,'limit'=>$limit);
    if (isset($_GET['debug']))
    {
        header('Content-type: text/plain; charset=utf8');
        die(print_r($ret,1));
    }
    
    
    header('Content-type: application/json; charset=utf8');
    echo json_encode($ret);
