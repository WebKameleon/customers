<?php


$shoper_url='http://wbp.shoparena.pl';
$shoper_url='http://www.wydawnictwo.wbp.poznan.pl';
$shoper_api_url=$shoper_url.'/webapi/json/';
if (isset($_GET['echo'])) $shoper_api_url='http://www.wbp.poznan.pl/inc/ajax/echo.php';
$shoper_img_url=$shoper_url.'/environment/cache/images/300_300_productGfx_';

//readfile($shoper_api_url);
//readfile('http://gold01.webkameleon.com/xal.php');

require_once __DIR__.'/../system/fun.php';

$cache_token='wbp_shoper_'.md5('a'.serialize($_GET).$_SERVER['HTTP_HOST']);

$shoper=WBP::cache($cache_token);

if ($shoper && !isset($_GET['debug']) && strlen($shoper)>20 && !isset($_GET['debug']) && !isset($_GET['fetch']) )
{
    header('Content-type: application/json; charset=utf8');
    die($shoper);
    
}

if (isset($_GET['debug'])) {
	echo '<pre>';
	ini_set('display_errors',1);
}

function login(&$c, $login, $password) {
    $params = Array(
        "method" => "login",
        "params" => Array($login, $password)
    );
    //curl_setopt($c, CURLOPT_POSTFIELDS, array('json'=>json_encode($params)));
    curl_setopt($c, CURLOPT_POSTFIELDS, "json=" . json_encode($params));
    $result = (Array) json_decode(curl_exec($c));
    if (isset($_GET['debug'])) {
	print_r([
		'req'=>$params,
		'rsp'=>$result,
		'info'=>curl_getinfo($c)
	]);
    }
    if (isset($result['error'])) {
        return null;
    } else {
        return $result[0];
    }
}    

function getError($c, $session){
    $params = Array(
        "method" => "call",
        "params" => Array($session, 'internals.validation.errors', null)
    );
    curl_setopt($c, CURLOPT_POSTFIELDS, "json=".json_encode($params));
    $result = (Array) json_decode(curl_exec($c));
    return $result;
}

$c = curl_init();

if (isset($_GET['debug'])) curl_setopt($c, CURLOPT_VERBOSE, true);
if (isset($_GET['debug'])) curl_setopt($c, CURLINFO_HEADER_OUT, 1);

$headers = [
    'Accept: */*',
    'Content-Type: application/x-www-form-urlencoded'
];

curl_setopt($c, CURLOPT_URL, $shoper_api_url);
curl_setopt($c, CURLOPT_POST, true);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_USERAGENT, 'NIC; bo adnministrator z Szopera ma problem ze slowem na litere G');
//curl_setopt($c, CURLOPT_HTTPHEADER, $headers);

// zalogowanie użytkownika i pobranie identyfikatora sesji
$session = login($c, "webkameleon", "Kameleon2014");


$params = Array(
    "method" => "call",
    "params" => Array($session, "product.list", 
            Array(true, true, false,false,false, null),
        )
);

// zakodowanie parametrów dla metody POST
$postParams = "json=".json_encode($params);
curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);

// dekodowanie rezultatu w formacie JSON do tablicy result
$data = curl_exec($c);
$result = json_decode($data,1);        
if (isset($_GET['debug'])) print_r([$params,$result]);
$ids=array();
foreach ($result AS $prod)
{
    if (isset($_GET['main_page']) && $prod['translations']['pl_PL']['main_page'] ) $ids[]=$prod['product_id'];
    if (isset($_GET['is_product_of_day']) && $prod['is_product_of_day']) $ids[]=$prod['product_id'];
}

$result=array();
if (count($ids))
{
    $params = Array(
        "method" => "call",
        "params" => Array($session, "product.list", 
                Array(true, true, true,true,true, $ids),
            )
    );
    
    // zakodowanie parametrów dla metody POST
    $postParams = "json=".json_encode($params);
    curl_setopt($c, CURLOPT_POSTFIELDS, $postParams);
    
    // dekodowanie rezultatu w formacie JSON do tablicy result
    $data = curl_exec($c);
    $result = json_decode($data,1);      
    
}

foreach ($result AS &$r)
{
    if (is_array($r['images']) && count($r['images']))
    {
        $r['img']=$shoper_img_url.$r['images'][0]['unic_name'].'.jpg';
        $r['alt']=$r['images'][0]['name'];
    }

    $r['author']='';    
    if (isset($r['attributes'][9][167])) $r['author']=trim($r['attributes'][9][167]);
    if ($r['author'])
    {
        $r['title']=$r['translations']['pl_PL']['name'];
        $r['title']=str_replace($r['author'],'',$r['title']);
        $r['title']=trim($r['title']);
        if ($r['title'][0]=='-') $r['title']=substr($r['title'],1);
        $r['title']=trim($r['title']);
    }

    $r['url']=$shoper_url.'/pl/p/'.WBP::str_to_url($r['translations']['pl_PL']['name']).'/'.$r['product_id'];
}

curl_close($c);

if (isset($_GET['debug']) && function_exists('mydie')) mydie($result);

header('Content-type: application/json; charset=utf8');



$data = json_encode($result);

die(WBP::cache($cache_token,$data));

