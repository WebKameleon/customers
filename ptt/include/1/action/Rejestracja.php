<?php

	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:'';
	
	$_POST['AUTH_login']=str_replace(' ','_',strtolower(trim($_POST['AUTH_login'])));
	$_POST['AUTH_pass']=trim($_POST['AUTH_pass']);

	if (!strlen($_POST['AUTH_pass'])) $error=sysmsg("Musisz podać hasło!");
	if ($_POST['AUTH_pass']!=$_POST['AUTH_pass1']) $error=sysmsg("Podane hasło i powtórzenie nie są zgodne!");


	$query="SELECT count(*) AS c FROM klienci WHERE login='".$_POST['AUTH_login']."'";
	parse_str(query2url($query));
	
	//mydie($query);

	if ($c || !strlen($_POST['AUTH_login'])) 
		$error=sysmsg("Niestety zaproponowana nazwa użytkownika istnieje, wymyśl coś innego!");

	if (strlen($error)) return;

	if (!strlen(trim($_POST['AUTH_imie']))) $error=sysmsg("Proszę podać imię");
	if (!strlen(trim($_POST['AUTH_nazwisko']))) $error=sysmsg("Proszę podać nazwisko");
	if (!strlen(trim($_POST['AUTH_adres']))) $error=sysmsg("Proszę podać adres");
	if (!strlen(trim($_POST['AUTH_miasto']))) $error=sysmsg("Proszę podać miejscowość");


	if (strlen($error)) return;

	$imie=ucWords(strtolower(trim(toText($_POST['AUTH_imie']))));
	$nazwisko=ucWords(strtolower(trim(toText($_POST['AUTH_nazwisko']))));
	$adres=trim(toText($_POST['AUTH_adres']));
	$kod=trim(toText($_POST['AUTH_kod']));
	$miasto=trim(toText($_POST['AUTH_miasto']));
	$email=strtolower(trim(toText($_POST['AUTH_email'])));
	$telefon=trim(toText($_POST['AUTH_telefon']));
	$gsm=trim(toText($_POST['AUTH_gsm']));

	$login=$_POST['AUTH_login'];
	$pass=$_POST['AUTH_pass'];

	$praca=trim(toText($_POST['AUTH_praca']));
	$wiek=$_POST['AUTH_wiek'];
	$plec=$_POST['AUTH_plec'];
	
	$zgoda = date("d.m.Y, G:i:s")." ; $REMOTE_ADDR";
	
	if (!strlen($_POST['AUTH_zgoda'])) $_POST['AUTH_zgoda'] = '0';

	$query="INSERT INTO klienci (imie,nazwisko,adres,kod,miasto,login,pass,
					email,telefon,gsm,praca,wiek,plec,zgoda, email_zgoda)
		VALUES ('$imie','$nazwisko','$adres','$kod','$miasto','$login','$pass',
					'$email','$telefon','$gsm','$praca','$wiek','$plec','$zgoda','".$_POST['AUTH_zgoda']."')";
	

//	echo nl2br($query); return;
	if (pg_Exec($db,$query)) 
	{
		$action="login";
		$AUTH_LOGIN=$login;
		$AUTH_PASS=$pass;

	}


