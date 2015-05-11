<?php

    $session=Bootstrap::$main->session();
    
    include __DIR__ . '/pre.php';
    require_once (__DIR__.'/.import.php');
    
   
    ini_set('implicit_flush', 1);
    for ($i = 0; $i < ob_get_level(); $i++) ob_end_flush();
    ob_implicit_flush(1);

    ini_set('display_errors','Off');
    session_write_close();
    
    
    header('Content-Type: text/html; charset=utf-8');

 
    
    function echoflush($txt,$flush=true)
    {
        if (is_string($txt)) echo $txt;
        else echo '<span title="'.str_replace('"','&quot;',print_r($txt,1)).'"><b>ERROR</b></span>';
        
        
        if ($flush) {
            echo "<br/>";
            for ($i=0;$i<10000;$i++) echo "<!--".md5($i)."-->\n";
            flush();
            @ob_flush();
        }
    }
    
    
    if (isset($_GET['importSpreadsheet'])) {
    
        $sp_title='';        
        $pos=strpos($_GET['importSpreadsheet'],'#');
        
        if ($pos) {
            $sp_title=addslashes(stripcslashes(substr($_GET['importSpreadsheet'],$pos+1)));
            $_GET['importSpreadsheet']=substr($_GET['importSpreadsheet'],0,$pos);
        }
        
        echoflush("Import arkusza <b>$sp_title</b> rozpoczęty.");
        $worksheets = Spreadsheet::listWorksheets($_GET['importSpreadsheet']);
        echoflush("Dane z arkusza <b>$sp_title</b> zostały pozyskane.");
        
        import_products($dbh,null);
        
        $errors=array();
        $products=0;
        $dicts=0;
        $txt='';
        
        
        foreach ($worksheets AS $id=>$worksheet) {
            $title=mb_strtolower($worksheet['title'],'utf-8');
            
            echoflush("Analiza <b>$title</b>, ",false);
 
            
            if (strlen($title)==2) {
                 echoflush("rozpoznano dane odbiorców - zakładka ma 2 znaki");
                $recipients=import_recipients($dbh,$title,Spreadsheet::getWorksheet($_GET['importSpreadsheet'],$id));
                if (is_array($recipients))
                {
                    $errors=$recipients['errors'];
                    $txt=$recipients['label'];
                    break;
                }
                
            }  elseif (strstr($title,'słownik')) {
                echoflush("rozpoznano słownik - występuje słowo <b>słownik</b>");
                
                $type='D';
                if (strstr($title,'kolor')) $type='C';
                elseif (strstr($title,'strukt')) $type='S';
                elseif (strstr($title,'cech')) $type='F';
                elseif (strstr($title,'dostęp')) $type='A';
                
                $d=import_dict($dbh,$type,Spreadsheet::getWorksheet($_GET['importSpreadsheet'],$id),$title);
                $dicts+=$d;
                echoflush("Znaleziono $d pozycji.");
            } else {
                
                echoflush("przyjęto, że są w środku dane z produktami");
                
                $p=import_products($dbh,Spreadsheet::getWorksheet($_GET['importSpreadsheet'],$id),$title);
                if (is_array($p)) {
                    $errors[$title]=$p;
                    echoflush($p);
                } else {
                    $products+=$p;
                    echoflush("Znaleziono $p pozycji w <b>$title</b>.");
                }
            }
        }
        
        $sql="INSERT INTO decora_imports (title,fileid,username,ip) VALUES ('$sp_title','".$_GET['importSpreadsheet']."','".$session['user']['username']."','".$_SERVER['REMOTE_ADDR']."')";
        $dbh->exec($sql);
        
        
        if ($products || $dicts) $txt="Zaimportowano $products produktów i $dicts pozycji słownikowych";

        
        $txt.='<h1>KONIEC</h1>';
        
        if (count($errors)) mydie($errors,$txt);
        else echoflush($txt);
    
        echo "<script>setTimeout(function() {parent.importer_close();},10000)</script>";
    
    }
