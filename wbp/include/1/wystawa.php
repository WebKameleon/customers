<?php
	if ($page==$this->webtd['page_id']) return;

	$kalendarz=unserialize(base64_decode($this->webpage['pagekey']));
	

	$objects=WBP::get_file_db('objects');
	
	foreach($kalendarz AS &$k)
	{
		$k['object']=$objects[$k['object']];
	}
	
	$kameleon=Bootstrap::$main->kameleon;
	
	
/*
	
<!-- loop:kalendarz -->
<!-- if:__index__=1 -->
<h4>Inauguracja:</h4><ul>
<!-- endif:__index__=1 -->
<!-- if:__index__=2 -->
</ul><h4>Kolejne prezentacje:</h4><ul>
<!-- endif:__index__=2 -->
<li>od: {from|kameleon.date} do: {to|kameleon.date} <b>{object.miasto} - {object.nazwa}</b></li>
<!-- endloop:kalendarz -->
</ul>

*/

