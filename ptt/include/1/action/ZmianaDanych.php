<?php


	$_POST['AUTH_login']=strtolower(trim($_POST['AUTH_login']));
	$_POST['AUTH_pass']=trim($_POST['AUTH_pass']);

	if ($_POST['AUTH_pass']!=$_POST['AUTH_pass1']) $error=sysmsg("Podane hasło i powtórzenie nie sš zgodne!");


	$query="SELECT count(*) AS c FROM klienci WHERE login='".$_POST['AUTH_login']."' AND id<>$AUTH_ID";
	parse_str(query2url($query));

	if ($c || !strlen($_POST['AUTH_login'])) 
		$error=sysmsg("Niestety nazwa użytkownika istnieje, wymyśl coś innego!");

	if (strlen($error)) return;

	$imie=trim(toText($_POST['AUTH_imie']));
	$nazwisko=trim(toText($_POST['AUTH_nazwisko']));
	$adres=trim(toText($_POST['AUTH_adres']));
	$kod=trim(toText($_POST['AUTH_kod']));
	$miasto=trim(toText($_POST['AUTH_miasto']));
	$email=strtolower(trim(toText($_POST['AUTH_email'])));
	$telefon=trim(toText($_POST['AUTH_telefon']));
	$gsm=trim(toText($_POST['AUTH_gsm']));
	$praca=trim(toText($_POST['AUTH_praca']));

	$login=$_POST['AUTH_login'];
	$pass=$_POST['AUTH_pass'];
	
	$wiek=$_POST['AUTH_wiek'];
	$plec=$_POST['AUTH_plec'];

	if (strlen($_POST['AUTH_pass'])) $pass_too=",pass='$pass'";
	if (!strlen($_POST['AUTH_zgoda'])) $_POST['AUTH_zgoda'] = '0';
	
	$query="UPDATE klienci SET
		 	imie='$imie',
			nazwisko='$nazwisko',
			adres='$adres',
			kod='$kod',
			miasto='$miasto',
			email='$email',
			telefon='$telefon',
			gsm='$gsm',
			email_zgoda = '".$_POST['AUTH_zgoda']."',
			praca='$praca',wiek='$wiek',plec='$plec',		
			login='$login'$pass_too
		 WHERE id=$AUTH_ID";
	

	//echo nl2br($query); return;
	if (pg_Exec($db,$query)) 
	{
		if (strlen($_POST['AUTH_pass']))
		{
			$action="login";
			$AUTH_LOGIN=$login;
			$AUTH_PASS=$pass;
		}
		$sysinfo=sysmsg("Dane zostały zmienione");

	}
