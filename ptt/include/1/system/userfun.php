<?php


function undo($query,$opis)
{
	global $db;
	
	if (!strlen($REMOTE_USER)) $REMOTE_USER="system";

	$undo_q=addslashes($query);
	$opis=addslashes($opis);

	$query="INSERT INTO undo (username,d_wykonania,opis,undo)
		VALUES('".$_SERVER['PHP_AUTH_USER']."',CURRENT_DATE,'$opis','$undo_q')";

	pg_Exec($db,$query);
}

function sysmsg($msg)
{

	global $db;
	global $lang;

	$defaultlang="ms";

	$m=addslashes($msg);

  	$query="SELECT msg_msg FROM messages WHERE msg_label='$m' AND msg_lang='$defaultlang'";
  	parse_str(sql2url($query));

  	if (!strlen($msg_msg)) 
	{
		$query="INSERT INTO messages (msg_label,msg_lang,msg_msg) VALUES ('$m','$defaultlang','$m')";
		db_Exec($db,$query);
	}	


  	$query="SELECT msg_msg FROM messages WHERE msg_label='$m' AND msg_lang='$lang'";
  	parse_str(sql2url($query));
	
  	if (!strlen($msg_msg)) return $msg;
  	return stripslashes($msg_msg);
}

function znizka($KLIENT_ID)
{
	global $db;


	$query="SELECT cykl,count(*) AS c FROM zapisy,kursy
		 WHERE klient_id=$KLIENT_ID AND ilosc>0
		 AND kursy.id=zapisy.kurs_id
		 AND cykl IN ('A','B')
		 GROUP BY cykl";
	$res=pg_Exec($db,$query);


	for ($i=0;$i<pg_NumRows($res) && pg_NumRows($res)>1;$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		if ($cykl=="B") $znizka+=$c*30;
	}

	return $znizka;
}

function znizka2002($KLIENT_ID)
{
	global $db;


	$query="SELECT cykl,sum(zapisy.cena) AS ceny FROM zapisy,kursy
		 WHERE klient_id=$KLIENT_ID AND ilosc>0
		 AND kursy.id=zapisy.kurs_id
		 AND cykl IN ('A','B','A1')
		 GROUP BY cykl";
	$res=pg_Exec($db,$query);


	for ($i=0;$i<pg_NumRows($res) && pg_NumRows($res)>1;$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$znizka+=0.1*$ceny;
	}

	return $znizka;
}

function is_pttAdmin()
{
	static $REMOTE_ADMIN;
	global $db;


	if (strstr($REMOTE_ADMIN,":".$_SERVER['REMOTE_ADDR'].":")) return true;	

	$query="SELECT * FROM admin";
	$res=pg_Exec($db,$query);

	for ($i=0;$i<pg_NumRows($res);$i++)
	{
		parse_str(pg_ExplodeName($res,$i));
		$ips.=":$ip:";
	}

	$REMOTE_ADMIN=$ips;

	if (strstr($REMOTE_ADMIN,":".$_SERVER['REMOTE_ADDR'].":")) return true;	


	return false;
}


function saleOff($cykl)
{
	global $sale_rok;

	$t=time();
	$soff=mktime(10,0,0,8,21,$sale_rok);
	if ($t>$soff) return true;
	return 0;
}

function saleOn($rok=2009)
{
	$t=time();
	$son=mktime(12,0,0,5,15,$rok);
	if ($t>$son) return true;
	return false;
}

