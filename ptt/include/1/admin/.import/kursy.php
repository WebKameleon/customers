<?php

//pg_Exec($db,"DELETE FROM kursy WHERE rok=$C_ROK;");
$new=0;
$upd=0;
$rek=0;
$lp=0;
$sql='';
$chobj=array();
echoflush (count($data).' rekordów.');

foreach ($data AS $k)
{
    if (!$k['cykl']) continue;
    
    
    $k['cena']=0+str_replace(",",".",$k['cena']);
    
    if (!$k['godz_do'] && $k['godz_od'])
    {
        $k['godz_do']=date('H:i',strtotime($k['godz_od'])+1.5*3600);
    }
    
    echoflush(sprintf('%02d. ',++$lp).$k['taniec'].' / '.$k['zaawansowanie'].' / '.$k['prowadzacy'].' / '.$k['obiekt'].' / '.$k['godz_od']);

    
    $cykl="";
    $where="cykl='".$k['cykl']."' AND obiekt='".$k['obiekt']."' 
             AND godz_od='".$k['godz_od']."' AND godz_do='".$k['godz_do']."'
             AND pomieszczenie='".$k['sala']."' AND rok=$C_ROK";

    $query="SELECT * FROM kursy WHERE $where LIMIT 1";
    parse_str(query2url($query));    

    if (!isset($chobj[$k['obiekt']]))
    {
        $chobj[$k['obiekt']]=true;
        $query="SELECT count(*) AS c FROM obiekty WHERE kod='".$k['obiekt']."'";
        parse_str(query2url($query));
        if (!$c) {
            echoflush("Brak obiektu (".$k['obiekt'].")");
            continue;
        }
    }
    
    $sql='';
    
    foreach($k AS $key=>$v) $k[$key]=toText($v);
    
    if (!strlen($cykl))
    {
            $sql="INSERT INTO kursy (cykl,godz_od,godz_do,obiekt,pomieszczenie,
                                        taniec,zaawansowanie,prowadzacy,miejsc,cena,rok)
                    VALUES ('".$k['cykl']."','".$k['godz_od']."','".$k['godz_do']."','".$k['obiekt']."','".$k['sala']."',
                             '".$k['taniec']."','".$k['zaawansowanie']."','".$k['prowadzacy']."',".$k['miejsc'].",".$k['cena'].",$C_ROK);\n";
            $new++;
    }
    else
    {
            $changes="";
            if (toText($taniec)!=$k['taniec']) 
            {
                    $changes.=",taniec='".$k['taniec']."'";
            }
            if (toText($zaawansowanie)!=$k['zaawansowanie']) 
            {
                    $changes.=",zaawansowanie='".$k['zaawansowanie']."'";
            }
            if (toText($prowadzacy)!=$k['prowadzacy']) 
            {
                    $changes.=",prowadzacy='".$k['prowadzacy']."'";
            }
            if ($miejsc!=$k['miejsc']) 
            {
                    $changes.=",miejsc=".$k['miejsc']."";
            }
            if ($cena!=$k['cena']) 
            {
                    $changes.=",cena=".$k['cena']."";
            }
            

            if (strlen($changes))
            {
                    $sql="UPDATE kursy SET cykl='".$k['cykl']."' $changes WHERE $where;\n";
                    $upd++;
            }
    
    }
    
    if ($sql) if (!pg_Exec($db,$sql)) 
    {
        echoflush(nl2br($sql));
    } 
    
    $rek++;

}

 

echoflush("Wprowadzono $new terminów, zmieniono $upd.");