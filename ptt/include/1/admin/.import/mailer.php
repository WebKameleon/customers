<?php



$lp=0;
foreach ($data AS $o)
{
    foreach ($o AS $k=>$v) $o[$k]=toText($v);
    if ($o['typ']=='html') $o['tresc']=nl2br($o['tresc']);

    $sql="DELETE FROM mailer WHERE action='".$o['akcja']."';
            INSERT INTO mailer (action,mailfrom,type,subject,msg,grupa)
            VALUES ('".$o['akcja']."','".$o['od']."','".$o['typ']."','".$o['temat']."','".$o['tresc']."',0)";
    if (pg_exec($db,$sql)) $lp++;
}


echoflush("Wprowadzono $lp maili.");
