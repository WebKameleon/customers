<?php
	$email=$_REQUEST['email'];

	$email=trim(strtolower(addslashes(stripslashes($email))));
	$id=0;
	$sql = "SELECT * FROM klienci 
			WHERE email = '$email' OR login='$email' 
			ORDER BY zapisy_all(id) DESC,id DESC
			LIMIT 1";
	
	parse_str(query2url($sql));
	$info = sysmsg("Jeżeli w naszej bazie znajduje się taki wpis - hasło zostało przesłane pod wskazany adres.");
	$sysinfo=$info;


	if (!$id) return;

	$mailto = $email;
	$sendmail_action="WyslijHaslo";
	$action="SendMail";


