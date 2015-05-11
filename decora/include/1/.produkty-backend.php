<?php
    $data=array();
    
    $session=Bootstrap::$main->session();
    $sid=$_GET['sid'];
    
    $webtd=new webtdModel($sid);
    
    if ($webtd->server != $session['server']['id']) return;
    
    
    if (!$webtd->ob) {
        $webtd->ob=3;
        $webtd->save();
    }    
    
    
    $lang=$session['lang'];
    include __DIR__.'/pre.php';
    
    
    
    $readfromdb=isset($_GET['readfromdb'])?$_GET['readfromdb']:false;
    
    $vendor=false;
    if (isset($_GET['vendor'])) {
        $vendor=$_GET['vendor'];
        $product=$_GET['product'];
        $collection=$_GET['collection'];
    }
    
     if ($readfromdb) {
        $costxt = explode(':',$webtd->costxt);
        
        if (!$vendor && isset($costxt[0])) $vendor=$costxt[0];
        if (!$product && isset($costxt[1])) $product=$costxt[1];
        if (!$collection && isset($costxt[2])) $collection=$costxt[2];
    }   
 
 
    if ($vendor || $readfromdb) {   
    
       $data['vendors']=array();
       $data['selected_vendor']=$vendor;
       $data['products']=array();
       $data['selected_product']=$product;
       $data['collections']=array();
       $data['selected_collection']=$collection;
       
       
       $sql="SELECT vendor FROM decora_products GROUP BY vendor ORDER BY vendor";
   
   
       $q=$dbh->query($sql);
       if ($q) foreach ($q AS $row ){
           $data['vendors'][]=$row['vendor'];
       }
    
   
       if ($vendor) {
           $sql="SELECT product FROM decora_products WHERE vendor='$vendor' GROUP BY product ORDER BY product";
           
           $q=$dbh->query($sql);
           if ($q) foreach ($q AS $row ){
               $data['products'][]=$row['product'];
           }       
           
       }
       
       if ($vendor && $product) {
           $sql="SELECT collection FROM decora_products WHERE vendor='$vendor' AND product='$product' GROUP BY collection ORDER BY collection";
           
           
           $q=$dbh->query($sql);
           if ($q) foreach ($q AS $row ){
               $data['collections'][]=$row['collection'];
           }       
           
       }
       
       
       $webtd->costxt="$vendor:$product:$collection";
       if (!$readfromdb) $webtd->save();
       
    }
    
    if (isset($_GET['cos'])) {
        $webtd->cos=$_GET['cos'];
        $webtd->save();
    }
    
    
    Header('Content-type: application/json; charset=utf-8');
    die(json_encode($data));