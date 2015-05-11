<?php

function decora_row(&$row,$lang,$translate=null)
{
	if (isset($row['name_'.$lang])) $row['name']=$row['name_'.$lang];
	if (isset($row['name_www_'.$lang])) $row['name_www']=$row['name_www_'.$lang];
	if (isset($row['desc_'.$lang])) $row['desc']=$row['desc_'.$lang];
	if (isset($row['desc_short_'.$lang])) $row['desc_short']=$row['desc_short_'.$lang];
	if (isset($row['price_'.$lang])) $row['price']=$row['price_'.$lang];
	if (isset($row['price_desc_'.$lang])) $row['price_desc']=$row['price_desc_'.$lang];
	if (isset($row['dim_'.$lang])) $row['dim']=$row['dim_'.$lang];
	
	if (isset($row['collection'])) $row['collection_i']=strtolower($row['collection']);

	foreach ($row AS $k=>$v) {
		if (is_integer($k)) unset($row[$k]);
		if (!is_array($v)) if (!strlen($v)) unset($row[$k]);
	}	
	
	
	if ($translate && isset($row['structure']) && strlen($row['structure']) ) {	
		$row['structure_a']=$translate->dict($row['vendor'],$row['structure'],'S');	
	}		

	if ($translate && isset($row['features']) && strlen($row['features']) ) {
		$features=explode(',',$row['features']);
		$features_a=array();
		foreach ($features AS &$feature) {
			$feature=trim($feature);
			if (!$feature) continue;
			
			if (substr($feature,0,2)=='AC') $row['abrasion_class']=$feature;
			if (strstr($feature,'gwara')) $row['warranty']=preg_replace('/[^0-9]/','',$feature);
			
			$fa=$translate->dict($row['vendor'],$feature,'F');
			if ($fa && count($fa)) $features_a[]=$fa;
		}
		$row['features_a']=$features_a;
	}	
	
	if ($translate && isset($row['color']) && strlen($row['color']) ) {
		$row['color_a']=$translate->dict($row['vendor'],$row['color'],'C');
	}
	
	if ($translate && isset($row['transparency']) && strlen($row['transparency']) ) {
		$row['transparency_a']=$translate->dict($row['vendor'],$row['transparency'],'C');
	}	
	
	
	if ($translate && isset($row['access']) && strlen($row['access'])) {
		$row['access_a']=$translate->dict($row['vendor'],$row['access'],'A');
	}
	
	
	if (isset($row['dimension']) && strlen($row['dimension'])) {
		$row['dimension_a']=array();
		$jednostka=preg_replace('/[0-9, x]/','',$row['dimension']);
		if ($translate) $jednostka=$translate->$jednostka;
		$wymiary=preg_replace('/[^0-9,x]/','',$row['dimension']);
		foreach (explode(',',$wymiary) AS $wymiar) {
			$row['dimension_a'][]=array('dimension'=>$wymiar,'dimesion'=>$wymiar,'unit'=>$jednostka);
		}
	}
	
	
	if (isset($row['assembly']) && $row['assembly']) {
		$assembly=$row['assembly'];
		if ($translate) $row['assembly_translated'] = $translate->$assembly;
		$row['assembly_raw'] = preg_replace('/[^a-z]/','',$assembly);
	}
	
	
}


function razy($a,$b)
{
	return $a*$b;
}

function plus($a,$b)
{
	return $a+$b;
}
