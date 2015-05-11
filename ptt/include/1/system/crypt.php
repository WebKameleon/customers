<?php

function encrypt($string, $key='9d76e44e9273d4e8483f1d341a28e472') {
    for ($i=0;$i<strlen($string);$i++) $string[$i]=$string[$i] xor $key[$i%strlen($key)];
    return base64_encode($string);
}

/**
 * Returns decrypted original string
 */
function decrypt($encrypted, $key='9d76e44e9273d4e8483f1d341a28e472') {
    $string=base64_decode($encrypted);
    for ($i=0;$i<strlen($string);$i++) $string[$i]=$string[$i] xor $key[$i%strlen($key)];
    return $string;
}