<?php

pg_Exec($db,"DELETE FROM akademiki;");
$lp=0;
foreach ($data AS $o)
{
    $o['cena']=str_replace(",",".",$o['cena']);
    $query="INSERT INTO akademiki (nazwa,adres,cena,miejsc)
             VALUES ('".$o['nazwa']."','".$o['adres']."',".$o['cena'].",".$o['ilosc-miejsc'].");\n";
    if (pg_Exec($db,$query)) $lp++;
}

echoflush("Usunięto poprzednie akademiki i wprowadzono $lp nowych.");