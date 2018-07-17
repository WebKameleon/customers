<?php

	if (strstr($_SERVER['HTTP_HOST'],'webkameleon')) return;
	
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='off') {
		Header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		die();
	}