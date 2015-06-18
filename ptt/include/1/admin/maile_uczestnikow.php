<?php

	$query="SELECT DISTINCT email FROM zapisy LEFT join klienci ON klient_id=klienci.id ";

	$res=pg_Exec($db,$query);
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_explodeName($res,$i));
		echo "$email<br/>";
	}
