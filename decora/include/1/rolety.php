<?php

    
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

	$__product=$product;
	$costxt=$row['vendor'].':'.$row['product'].':'.$row['collection'];
	include (__DIR__.'/produkty.php');
	$product=$__product;
	if (!$cos) include (__DIR__.'/roleta.html');
	return;
    }


    $order_by="representant is NULL,representant DESC,id";    
    include (__DIR__.'/produkty.php');
	
    
    $sql="SELECT transparency FROM decora_products WHERE vendor='$costxt[0]' AND $lang=1";
    if ($costxt[1]) $sql.=" AND product='$costxt[1]'";
    if ($costxt[2]) $sql.=" AND collection='$costxt[2]'";
    $sql.=" GROUP BY transparency ORDER BY transparency";
    
    $transparency=array();
    $q=$dbh->query($sql);
    if ($q) foreach ($q AS $row ) {
    
	if (!$row['transparency']) continue;
        $transparency[$row['transparency']]=array('key'=>$row['transparency']);
        $transparency[$row['transparency']]['label']=$translate->dict($costxt[0],$row['transparency'],'C');
    }
    
        
    $title=(is_object($this)) ? $this->webpage['title'] : '';
    
?>
<script type="text/javascript">
    var products= new Array;
    
    {loop:products}
    products[{__loop__}]={
        ean: {ean},
        name: '{name?}',
        name_www: '{name_www?}',
        collection: '{collection}',
        color: '{color_a.name}',
        palette: '{palette}',
        transparency: '{transparency}',
        assembly: '{assembly}',
	href: 'href="<?php echo $next?>?product={ean}"'
    };
    {endloop:products}
    

    var roleta_name='<?php echo $title;?>';
    var roleta_href=location.href;
    
    
    
</script>


<!-- <style> .special-for-plain {display:none} </style> -->

<?php
	$plain=$this->webtd['plain'];

    if (!$cos) include (__DIR__.'/rolety.html');
