<?php

	$_admin=0;

	$query="SELECT * FROM admin";
	$res=pg_Exec($db,$query);

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		if ($ip==$_SERVER['REMOTE_ADDR']) 
		{
			$_admin=1;
			echo "<b>Twój adres: </b>";
		}
		$begin=substr($begin,0,16);
		echo "$ip ($username, $begin) <a href='$self${next_query_char}action=admin/DelIp&ip=$ip'>Usuń</a><br>";
	}


	if (!$_admin) echo "<a href='$self${next_query_char}action=admin/AddIp'>Dodaj mój adres !</a>";

