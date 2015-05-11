<?php

    $token=$costxt;
    $costxt=explode(':',$costxt);
    if (!$costxt[0]) return;

    
    if (!isset($_SERVER[$token])) {
        
        if ( isset($costxt[0])) $vendor=$costxt[0];
        if ( isset($costxt[1])) $product=$costxt[1];
        if ( isset($costxt[2])) $collection=$costxt[2];
        
        $sql="SELECT * FROM decora_products WHERE vendor='$vendor' AND $lang=1";
        if ($product) $sql.=" AND product='$product'";
        if ($collection) $sql.=" AND collection='$collection'";
        if (isset($order_by)) $sql.=" ORDER BY ".$order_by;
        else$sql.=" ORDER BY id";
        
        $products=array();

        
    
        $q=$dbh->query($sql);
        if ($q) foreach ($q AS $row ){
            
            decora_row($row,$lang,$translate);          
            
            
            if (isset($row['color']) && strlen($row['color']) && strstr($row['color'],',') ) {
                $colors=explode(',',$row['color']);
                
                foreach ($colors AS $color) {
                    $color=trim($color);
                    $row['color']=$color;
                    $row['color_a']=$translate->dict($row['vendor'],$color,'C');
                    
                    $products[]=$row;
                }
                continue;
            }
            
            
            $products[]=$row;
        }
        $_SERVER[$token]=$products;
    }

    if (isset($row)) unset($row);
    if (isset($row2)) unset($row2);
    
    $products=$_SERVER[$token];
    
    $product=$products[0];
    $_SERVER['product']=$product;
    $_SERVER['products']=$products;
    
    //mydie($product);
