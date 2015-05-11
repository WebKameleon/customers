<?php
    ini_set('display_errors',true);
    
    $session=Bootstrap::$main->session();
    
    include __DIR__ . '/../system/pre.php';
    require_once (__DIR__.'/.importfun.php');
    
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
            
            echoflush("Analiza <b>$title</b>, ",false);
            
            
            $table='wbp_'.str_replace('-','_',Kameleon::str_to_url($title,-1,true));
            
            
            $sql="SELECT max(id) FROM $table";
            try{
                $q=$dbh->query($sql);
            }
            catch (Exception $e) {
                echoflush("brak tabeli $table, tworzenie");
                
                $sql="CREATE TABLE $table (id Serial, entered Integer, changed Integer)";
                $dbh->exec($sql);
            }
            
            $data=Spreadsheet::getWorksheet($_GET['importSpreadsheet'],$wk_id);
        
        
            
            $headers=$data[0];
            $max_predefined_rec_id=0;
            
            foreach($headers AS $i=>$field)
            {
                $field=str_replace('-','_',Kameleon::str_to_url($field,-1,true));
                if (trim($field)=='') continue;
                if ($field=='id') continue;
                
                $data[0][$i] = $field;
                
                $sql="SELECT $field FROM $table LIMIT 1";
                try{
                    $q=$dbh->query($sql);
                }
                catch (Exception $e) {
                    echoflush("brak pola $field, tworzenie");
                    
                    $type='Varchar';
                    if (substr($field,0,2)=='x_') $type='int2';
                    $sql="ALTER TABLE $table ADD $field $type";
                    $dbh->exec($sql);
                }                
            }
            
            foreach(array('lat','lng') AS $field)
            {
                $sql="SELECT $field FROM $table LIMIT 1";
                try{
                    $q=$dbh->query($sql);
                }
                catch (Exception $e) {
                    echoflush("brak pola $field, tworzenie");
                    
                    $sql="ALTER TABLE $table ADD $field Double precision";
                    $dbh->exec($sql);
                }             
            }

            $rec=0;
            
            $id=array_search('id',$headers);
            for ($i=1; $i<count($data); $i++)
            {
                $inserts=array();
                $values=array();
                $sets=array();
                
                $rec_id=$data[$i][$id];
                
                for ($j=0;$j<count($data[$i]);$j++)
                {
                    if ($j==$id) continue;
                    if (!$data[0][$j]) continue;
                    
                    $value=$data[$i][$j];
                    if (!strlen($value)) $value=null;
                    if (substr($data[0][$j],0,2)=='x_') $value=$value?1:null;
                
                    $inserts[]=$data[0][$j];
                    $values[]=$value;                
                    $sets[]=$data[0][$j].'=?';
                    
                }

                if (!count($inserts)) continue;
                
                                
                
                if ($rec_id)
                {
                    $sql="SELECT * FROM $table WHERE id=$rec_id";
                    $found=false;
                    $q=$dbh->query($sql);
                    foreach ($q AS $row)
                    {
                        $found=true;
                    }
                    
                    if (!$found)
                    {
                        $max_predefined_rec_id=max($max_predefined_rec_id,$rec_id);
                        $data[$i][$id]='';
                        $inserts[]='id';
                        $values[]=$rec_id;
                        
                    }
                    
                    
                }
                
                
                if (!$data[$i][$id])
                {
                    $inserts[]='entered';
                    $inserts[]='changed';
        
                    $values[]=Bootstrap::$main->now;
                    $values[]=Bootstrap::$main->now;                       

                    $sql="INSERT INTO $table (".implode(',',$inserts).") VALUES (".implode(',',array_fill (0,count($inserts),'?')).")";
                    
                } else {
                    $sets[]='changed=?';
                    $values[]=Bootstrap::$main->now;
                    $sql="UPDATE $table SET ".implode(',',$sets). "WHERE id=".$rec_id;
                }

                //mydie($values,$sql);
                
                try {
                
                    $q=$dbh->prepare($sql);
                    $q->execute($values);
                } catch (Exception $e)
                {
                    mydie($e);
                    
                }
                
                
                if (!$rec_id)
                {
                    $sql="SELECT max(id) FROM $table";
                    $q=$dbh->query($sql);
                    foreach ($q AS $row)
                    {
                        $rec_id=$row[0];
                        Spreadsheet::update_cell($_GET['importSpreadsheet'],$wk_id,$i,$id,$rec_id);
                    }
                    
                }
                
                $sql="SELECT * FROM $table WHERE id=$rec_id";
                $q=$dbh->query($sql);
                foreach ($q AS $row)
                {
                    
                    if (!$row['lat'] || !$row['lat']) if ($row['miasto'] && $row['kod'] && $row['ulica']) {
                    
                    
                        $address=$row['kod'].' '.$row['miasto'].', '.$row['ulica'];
                        $url='http://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&region=pl';
                        
                        $dta=json_decode(file_get_contents($url),1);
                        
                        if (is_array($dta['results']) && count($dta['results']))
                        {
                            $sql="UPDATE $table SET lat=".$dta['results'][0]['geometry']['location']['lat'].",lng=".$dta['results'][0]['geometry']['location']['lng']." WHERE id=$rec_id";
                            $dbh->exec($sql);
                        }
                        else
                        {
                            echoflush('Nie można znaleźć <b>'.$address.'</b>: '.$dta['status']);
                        }
                        
                    }
                    
                }
                
                $rec++;
            }
 
            $sql="DELETE FROM $table WHERE changed<".Bootstrap::$main->now;
            $dbh->exec($sql);
 
            if ($max_predefined_rec_id)
            {

                $sql="SELECT setval('${table}_id_seq',$max_predefined_rec_id)";
                $dbh->exec($sql);
            }
 
 
            echoflush("razem $rec pozycji");
            table2file($table,$dbh);
        }
        
        $sql="INSERT INTO wbp_imports (title,fileid,username,ip) VALUES ('$sp_title','".$_GET['importSpreadsheet']."','".$session['user']['username']."','".$_SERVER['REMOTE_ADDR']."')";
        
        $dbh->exec($sql);
    
        $txt.='<h1>KONIEC IMPORTU</h1>';    
        echoflush($txt);
    
        
        $ftp=new ftpController();
        $ftp->ftp_start('inc','',false);
        
        echoflush('<h1>Dane opublikowane</h1>');
        
        echo "<script>setTimeout(function() {parent.importer_close();},10000)</script>";
    
    }