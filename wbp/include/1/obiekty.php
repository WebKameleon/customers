<?php

	$table=$costxt?:'objects';
	$objects=WBP::get_file_db($table);
	
	if (!$cos) return;
	if (!isset($objects[$cos])) return;
	
	$obiekt = $objects[$cos];
	include __DIR__.'/obiekty.html';
	
	$obj_table=$table;
	include __DIR__.'/system/map.php';
