<?php



$data=array();

if (isset($_GET['products']))
{
    $products=explode(',',$_GET['products']);
    
    $products_and='';
    foreach ($products AS $product)
    {
        $product+=0;
        if ($product) $products_and.=" AND receives&$product>0";
    }
}


if (isset($_GET['lat']) && isset($_GET['lng']) && isset($_GET['lang']) && isset($_GET['products'])) {
    $lat=0+$_GET['lat'];
    $lng=0+$_GET['lng'];
    $lang=substr($_GET['lang'],0,2);
    
    include_once (__DIR__.'/pre.php');
    
    
    $sql="SELECT *,decora_distance(lat,lng,$lat,$lng) AS distance
            FROM decora_recipients WHERE lang='$lang' $products_and AND lat IS NOT NULL
            AND decora_distance(lat,lng,$lat,$lng)<=35
            ORDER BY decora_distance(lat,lng,$lat,$lng)";
    
    
    $q=$dbh->query($sql);
    if ($q) {
        $lastcity='';
        $data['results']=array();
        foreach ($q AS $row ) {
            foreach ($row AS $k=>$v) if (is_int($k)) unset($row[$k]);
            $row['newcity']=$lastcity!=$row['city']?1:0;
            $lastcity=$row['city'];
            $data['results'][]=$row;
        }
    }
    //$data['sql']=$sql;
}


if (isset($_GET['province']) && isset($_GET['lang']) && isset($_GET['products'])) {
    $province=addslashes($_GET['province']);
    $lang=substr($_GET['lang'],0,2);
    
    include_once (__DIR__.'/pre.php');
    
    
    $sql="SELECT *
            FROM decora_recipients WHERE lang='$lang' $products_and AND province='$province'
            ORDER BY city,pri";
    
    
    
    $q=$dbh->query($sql);
    if ($q) {
    
        $lastcity='';
        $data['results']=array();
        foreach ($q AS $row ) {
            foreach ($row AS $k=>$v) if (is_int($k)) unset($row[$k]);
            $row['newcity']=$lastcity!=$row['city']?1:0;
            $lastcity=$row['city'];
            $data['results'][]=$row;
        }
        
        
    }
    //$data['sql']=$sql;
}



if (isset($_GET['id']) && $_GET['id']>0 && isset($_GET['lang']) && isset($_GET['products'])) {
    $id=0+$_GET['id'];
    $lang=substr($_GET['lang'],0,2);
    
    include_once (__DIR__.'/pre.php');
    
    
    $sql="SELECT * FROM decora_recipients WHERE id=$id";
    
    $lat=0;
    $lng=0;
    $q=$dbh->query($sql);
    if ($q) {
        foreach ($q AS $row ) {
            foreach ($row AS $k=>$v) if (is_int($k)) unset($row[$k]);
            $data['result']=$row;
            $lat=$row['lat'];
            $lng=$row['lng'];
        }        
        
    }
    
    $sql="SELECT *,decora_distance(lat,lng,$lat,$lng) AS distance
            FROM decora_recipients WHERE lang='$lang' $products_and AND lat IS NOT NULL
            AND decora_distance(lat,lng,$lat,$lng)<=15
            ORDER BY decora_distance(lat,lng,$lat,$lng)";    
    
    $q=$dbh->query($sql);
    if ($q) {
        $lastcity='';
        $data['results']=array();
        foreach ($q AS $row ) {
            foreach ($row AS $k=>$v) if (is_int($k)) unset($row[$k]);
            $row['newcity']=$lastcity!=$row['city']?1:0;
            $lastcity=$row['city'];
            $data['results'][]=$row;
        }
    }
    //$data['sql']=$sql;
}



Header('Content-type: application/json');
die(json_encode($data));