<?php

    $threshold=$cos?:3;
    if (!$size) $size=8;
    
    $product=null;
	
    if (isset($_GET['product'])) {
	    $product = $_GET['product'];
	    $sql="SELECT * FROM decora_products WHERE ean='$product'";
	    $q=$dbh->query($sql);
	    if($q) foreach ($q AS $row)
	    {
		decora_row($row,$lang,$translate);		    
		$product = $row;
	    }
	    
	    
    }
    
    if ($product) {

	$features=array();
        for ($f=1;$f<=$size;$f++)
        {
            $feature=sprintf('feature_%02d',$f);
            if (!isset($product[$feature.'_scale'])) $product[$feature.'_scale']=0;
            
            
	    $features[$f]=$translate->dict($product['vendor'],$feature,'F');
	
	    $features[$f]['pure_name']=preg_replace('/\[[^\}]*\]/','',$features[$f]['name']);
	
            $features[$f]['scale'] = $product[$feature.'_scale'];
	    $features[$f]['value'] = $product[$feature.'_value'];
	    
        }    
    
	if (!$cos) include (__DIR__.'/podklad.html');
	return;
    }

    
    include (__DIR__.'/produkty.php');
    
    
    $features=array();
    $products_js='';
    foreach ($products AS $i=>&$product)
    {
	
	if (isset($costxt[3]) && $costxt[3] && !strstr($product['features'],$costxt[3])) {    
	    continue;
	}
	
	
        $products_js.="\tproducts[$i]={\n";
        $products_js.="\t\tean: '".$product['ean']."',\n";
        $products_js.="\t\tname: '".$product['name']."',\n";
	$products_js.="\t\thref: 'href=\"".$next."?product=".$product['ean']."\"',\n";
        $products_js.="\t\tdesc_short: '".trim(preg_replace('/ +/',' ',str_replace("\n",' ',$product['desc_short'])))."',\n";
                
        

        for ($f=1;$f<=$size;$f++)
        {
            $feature=sprintf('feature_%02d',$f);
            if (!isset($product[$feature.'_scale'])) $product[$feature.'_scale']=0;
            
            if (!isset($features[$f])) {
                $features[$f]=$translate->dict($product['vendor'],$feature,'F');
            
                $features[$f]['pure_name']=preg_replace('/\[[^\}]*\]/','',$features[$f]['name']);
		$features[$f]['show']=0;
            }
            
            $product[$feature] = $product[$feature.'_scale']>=$threshold ? 1 : 0;
            
	    if ($product[$feature]) $features[$f]['show']=1;
	    
	    
            $products_js.="\t\t$feature: ".$product[$feature].",\n";
        }
    

        
        $products_js.="};\n";
    }
    
?>

<script type="text/javascript">
    var products= new Array;
    
    
    <?php echo $products_js;?>

</script>


<?php
    if (!$cos) include (__DIR__.'/podklady.html');



	