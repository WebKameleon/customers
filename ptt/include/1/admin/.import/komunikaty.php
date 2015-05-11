<?php

pg_Exec($db,"TRUNCATE messages");

$lp++;

foreach ($data AS $msg)
{
    foreach ($msg AS $k=>$v) $msg[$k]=toText($v);
    $label=$msg['label'];
    
    if (!$label) continue;
    
    foreach ($msg AS $k=>$v)
    {
        $lang=$k=='label'?'ms':$k;
        $sql="INSERT INTO messages (msg_label,msg_lang,msg_msg) VALUES ('$label','$lang','$v')";
        if (pg_exec($db,$sql)) $lp++;
    }
    
}

echoflush("Wprowadzono $lp wpisów.");