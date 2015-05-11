<?php

    
    $product=null;
    
    if (isset($_GET['product'])) {
        $pr=explode('_',$_GET['product']);
    
        if (count($pr)==2) {
            $uimages_check=$KAMELEON_MODE?$session['uimages_path']:$uimages;
            
            $pr[1]=str_replace("'",'',$pr[1]);
            

            if (true || file_exists($uimages_check.'/products/karnisze/full/'.$pr[1].'_full.jpg') )
            {
                
                $_costxt=explode(':',$costxt);
                $vendor=$_costxt[0];
                $product=$_costxt[1];
                $collection=$_costxt[2];
                
                $sql="SELECT * FROM decora_products WHERE vendor='$vendor' AND $lang=1";
                $sql.=" AND product='$product'";
                $sql.=" AND collection='$collection'";
                $sql.=" AND ean='".$pr[1]."'";
                
                
                $q=$dbh->query($sql);
                if ($q) foreach ($q AS $row ){
                    
                    decora_row($row,$lang,$translate); 
                    
                    $product=$row;
                    $begin=$pr[0];
                }
                
                if ($product) {
                    $color=$translate->dict($product['vendor'],$begin,'C');
                }
                
            }
        }
    }
    
    if ($product) {
        if (!$cos) include(__DIR__.'/karnisz.html');
        return;
    }
    
    
    
    include (__DIR__.'/produkty.php');
    
    $karnisze=array();
    $colors=array();
    
    foreach($products AS &$product) {
        $colors[strtolower($product['color'])][]=$product;
    }
    
    $unknown_colors=array();
    foreach($colors AS $color=>$items)
    {
        if (is_array($items[0]['color_a'])) {
            $karnisze[$color]['collection_i']=$items[0]['collection_i'];
            $karnisze[$color]['color']=$items[0]['color_a'];
            $karnisze[$color]['items']=$items;
            $karnisze[$color]['__clear__'] = true;
        } else $unknown_colors[]=$color;
    }
    
    foreach($unknown_colors AS $color)
    {
        if ($color=='uni') {
            foreach($karnisze AS &$karnisz)
            {
                $unis=$colors[$color];
                if (isset($karnisz['items'][0]['color'])) foreach($unis AS &$uni)
                {
                    $uni['color'] = $karnisz['items'][0]['color'];
                    $uni['color_a']['key'] = $uni['color'];
                }
                $karnisz['items']=array_merge($karnisz['items'],$unis);
            }
        }
    }
    
    if (isset($items)) unset($items);
    if (isset($color)) unset($color);
    if (isset($unknown_colors)) unset($unknown_colors);
    
    
    $first_index_a=array_keys($karnisze);
    $first_index=$first_index_a[0];
    
    $collection=$karnisze[$first_index]['items'][0]['collection'];
    $name_www=$karnisze[$first_index]['items'][0]['name_www'];
    $collection_i=$karnisze[$first_index]['items'][0]['collection_i'];
    $color_key=$karnisze[$first_index]['color']['key'];
    $ean=$karnisze[$first_index]['items'][0]['ean'];


    if (!$cos) include(__DIR__.'/karnisze.html');
