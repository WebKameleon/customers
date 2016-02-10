<?php
    $merchant_id='29309';
    $session_id=$_GET['id'];
    $amount=100*$_GET['kwota'];
    $crc='ebdbddeee1a94ce3';
    $sign=md5($session_id.'|'.$merchant_id.'|'.$amount.'|PLN|'.$crc);
    $email=$_GET['email'];
    $title=$_GET['title'];
    

    $data['p24_session_id'] = $session_id;
    $data['p24_merchant_id'] = $merchant_id;
    $data['p24_pos_id'] = $merchant_id;
    $data['p24_amount'] = $amount;
    $data['p24_currency']="PLN" ;
    $data['p24_description'] = $title;
    $data['p24_country']="PL" ;
    $data['p24_email']=$email;
    $data['p24_language']="pl" ;
    $data['p24_url_return']="http://kreatywnycad.pl/" ;
    $data['p24_api_version']="3.2" ;
    $data['p24_sign'] = $sign;
    $data['p24_encoding']="UTF-8";     
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://secure.przelewy24.pl/trnRegister");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec ($ch);
    $token='';
    parse_str($result);
    if ($token) header('Location: https://secure.przelewy24.pl/trnRequest/'.$token);
    
/*
p24_session_id,
p24_merchant_id, p24_amount,p24_currency oraz klucza CRC. Łącznikiem pól jest znak „|”.
Przykład:
 md5 dla ciągu: abcdefghijk|9999|2500|PLN|a123b456c789d012
 wynosi: 6c7f0bb62c046fbc89921dc3b2b23ede


    http://kreatywnycad.pl/platnosci24.php?kwota={kwota}&id={id}&title={form_title}
*/

