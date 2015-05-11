<?php

$lp=0;
foreach ($data AS $o)
{
    $query="DELETE FROM obiekty WHERE kod='".$o['kod']."';
             INSERT INTO obiekty (kod,nazwa,adres,grupa)
             VALUES ('".$o['kod']."','".$o['nazwa']."','".$o['adres']."',".$o['grupa'].");\n";

    if (pg_Exec($db,$query)) 
    {
            $lp++;
    }
    else
    {
            echoflush("Błąd $query");
            break;
    }
}

echoflush("Wprowadzono $lp obiektów.");
