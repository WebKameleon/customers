<?php
    
    include (__DIR__.'/produkty.php');
	
    
    $profiles=array();
    $colors=array();
    
    foreach($products AS $pr) {
	
	
	if (isset($pr['color']) && $pr['color']) {
	    if (!isset($colors[$pr['color']])) {
		$colors[$pr['color']]=$pr['color_a'];
	    }
	    
	    if (!isset($colors[$pr['color']]['items'])) $colors[$pr['color']]['items']=array();
	    $colors[$pr['color']]['items'][]=$pr;
	}
	
	$model=isset($pr['model'])?str_replace(' ','',($pr['model'])):'';
	
	if ($model) {
	    if (!isset($profiles[$model])) $profiles[$model]=$pr; 
	    if (!isset($profiles[$model]['items'])) $profiles[$model]['items']=array();
	    $profiles[$model]['items'][]=$pr;
	}
    }
  
    