<?php
    ini_set('display_errors',true);
    
    $session=Bootstrap::$main->session();
    
    $INCLUDE_PATH=__DIR__.'/..';
    include __DIR__ . '/../system/pre.php';

    
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
            for ($i=0;$i<100;$i++) echo "<!--".md5($i)."-->\n";
            flush();
            ob_flush();
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
        

        
        
        $txt='';
        
        
        foreach ($worksheets AS $wk_id=>$worksheet) {
            $title=mb_strtolower($worksheet['title'],'utf-8');
            echoflush("Analiza <b>$title</b>: ",false);
            
            $title=Kameleon::str_to_url($title);
            $php=__DIR__.'/.import/'.$title.'.php';
            if (!file_exists($php)) {
                echoflush("brak pliku $php");
                continue;
            }
            
            $sp=Spreadsheet::getWorksheet($_GET['importSpreadsheet'],$wk_id);
        
            $headers=$sp[0];
            $data=[];
            for ($i=1;$i<count($sp);$i++)
            {
                $rek=[];
                for ($j=0;$j<count($sp[$i]);$j++) {
                    $rek[Kameleon::str_to_url($headers[$j],-1)]=$sp[$i][$j];
                }
                $data[]=$rek;
            }
            include($php);

        }
        
        if (isset($_GET['tdsid']))
        {
            $webtd=new webtdModel($_GET['tdsid']);
            $imports=unserialize(base64_decode($webtd->web20));
        
            $imports[$_GET['importSpreadsheet']] = ['date'=>Bootstrap::$main->now,'title'=>$sp_title,'username'=>$session['user']['username'],'ip'=>$_SERVER['REMOTE_ADDR']];    
        
            $webtd->web20=base64_encode(serialize($imports));
            $webtd->save();
        }
    
        $txt.='<h1>KONIEC IMPORTU</h1>';    
        echoflush($txt);
    
        
        echo "<script>setTimeout(function() {parent.importer_close();},10000)</script>";
    
    }