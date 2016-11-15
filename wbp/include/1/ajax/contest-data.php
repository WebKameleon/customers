<?php
    session_start();

    $contest=isset($_SESSION['contest'])?$_SESSION['contest']:[];
    
	Header('Content-type: application/json');    
	die(json_encode($contest));