<?php
	$akademik=$_REQUEST['akademik'];


	echo "<ul>";
	$query="SELECT nazwa, id FROM akademiki ORDER BY nazwa";
	$res=pg_Exec($db,$query);
	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		echo "<li><a href='$next${znak}akademik=$id'>";
		if ($id==$akademik) echo "<span style='text-decoration:underline'><b>";
		echo $nazwa; 
		if ($id==$akademik) echo "</b></span>";
		echo "</a></li>\n";

	}
	echo "</ul>\n";

	if (!$akademik) return;


	$query="SELECT * FROM zapisy_a,klienci 
		WHERE ilosc>0 AND akademik_id=$akademik
		AND zapisy_a.klient_id=klienci.id
		ORDER BY zapisy_a.d_przyjazdu,nazwisko,imie,miasto";


	include("$INCLUDE_PATH/admin/raport_lista_query_wymagane.php");

