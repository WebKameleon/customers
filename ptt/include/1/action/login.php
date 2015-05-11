<?php

	if (strlen($_POST['SET_AUTH_LOGIN'])) $AUTH_LOGIN=$_POST['SET_AUTH_LOGIN'];
	if (strlen($_POST['SET_AUTH_PASS'])) $AUTH_PASS=$_POST['SET_AUTH_PASS'];


	$AUTH_LOGIN=strtolower(trim($AUTH_LOGIN));
	$AUTH_PASS=trim($AUTH_PASS);

	$id=0;
	$query="SELECT id FROM klienci WHERE login='$AUTH_LOGIN' AND pass='$AUTH_PASS' LIMIT 1";
	parse_str(query2url($query));

	if ($id) 
	{
		$AUTH_ID=$id;

		if (headers_sent())
		{
			echo "<script> document.cookie = \"AUTH_LOGIN=$AUTH_LOGIN; path=/\"; </script>";
			echo "<script> document.cookie = \"AUTH_PASS=$AUTH_PASS; path=/\"; </script>";
		}
		else
		{
			SetCookie("AUTH_LOGIN",$AUTH_LOGIN,0,'/');
			SetCookie("AUTH_PASS",$AUTH_PASS,0,'/');
		}


	}
	else $error=sysmsg('Brak użytkownika lub nieprawidłowe hasło. Proszę się zarejestrować lub skorzystać z pomocy - "Przypomnienie hasła"');
	

