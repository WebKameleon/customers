<?php


function usage($me)
{
    die("$me [-m] article|aktual [-u lang] [-i ID -a gallery] [-g ID] [-l limit] [-o offset] [-k kat] [-r like_expr] [-f] [-s]\n\t-g greater then ID\n\t-f = force rewrite\n\t-s = recursive\n");
}


function katalogi($k)
{
    $sql="SELECT * FROM wbp_Katalogi LEFT JOIN wbp_KatalogiKategorie ON Katalogi_KategoriaId=KatalogiKategorieId WHERE KatalogiId=$k";

    $q=$_SERVER['wbp']['dbh']->query($sql);
    if ($q) foreach ($q AS $row ){
    
    }

    return array('nazwa'=>$row['KatalogiTytul'],'kategoria'=>$row['KatalogiKategorieNazwa'],'plik'=>$row['KatalogiPlik']);

}