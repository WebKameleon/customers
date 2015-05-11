<?php
/*
if (!isset($KAMELEON_MODE) || !$KAMELEON_MODE) 
{
	if (strtolower($_SERVER['HTTP_HOST'])=='www.ptt.poznan.pl')
	{
		Header("Location: http://www.ptt-poznan.pl".$REQUEST_URI);
		die();
	}
	
}
*/
$error='';

foreach ($_COOKIE AS $k=>$v) if (substr($k,0,4)=='AUTH') $$k=$v;


require_once (__DIR__."/fun.php");
require_once (__DIR__."/userfun.php");
require_once (__DIR__."/db.php");

if (!isset($REMOTE_USER)) $REMOTE_USER='';
if (!strlen($REMOTE_USER) && isset($_SERVER['PHP_AUTH_USER'])) $REMOTE_USER=$_SERVER['PHP_AUTH_USER'];

$C_ROK=date('Y');
//$C_ROK=2014;

if (class_exists('Bootstrap')) 
        $C_DB_CONNECT = "host=vps5.gammanet.pl port=5432 user=pttsql password=Hokk!760 dbname=ptt_bilety";
else
        $C_DB_CONNECT = "host=localhost port=5432 user=pttsql password=Hokk!760 dbname=ptt_bilety";setlocale(LC_CTYPE,"pl_PL.UTF-8");

global $db;

$db=@pg_Connect($C_DB_CONNECT);
if (!$db) 
{
	$error="System chwilowo nieczynny, spróbuj później";
}
@pg_set_client_encoding ($db, 'utf-8');

$kiedy_znowu=$C_ROK;
if (date('m')>=8) $kiedy_znowu++;

$sale_rok=$C_ROK;
if (date('m')>9) $sale_rok++;
if (date('m')==9 && date('d')>30) $sale_rok++;

if (!isset($pagetype)) $pagetype=0;

if ( ($pagetype==1 || $pagetype==3) && !is_pttAdmin() && !$KAMELEON_MODE && !saleOn($sale_rok) ) 
	$error="Zapisy na Międzynarodowe Warsztaty Tańca Współczesnego rozpoczną się 15 maja $kiedy_znowu od godz 12.00. Zapraszamy";


$CYKLE_NAKLAD=array("A:C");

$CYKLE=array("A"=>"16.08-23.08.14");

$ile_dajemy_czasu=15; 	//dni
$czas_przejscia=20;	//min


$COLORS=array("#A70F0E","#C24C4C","#FFE7E7");

$znak="?";
if (isset($next) && strstr($next,$znak)) $znak="&";

$next_query_char=$znak;

ini_set('display_errors','On');
error_reporting(7);
