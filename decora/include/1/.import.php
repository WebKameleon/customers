<?php

    function import_addslashes($v)
    {
        return str_replace('\\"','"',addslashes($v));
    }

    function import_dict($dbh,$type,$data,$sheet='')
    {
        $count=0;
        $dbarray = Spreadsheet::worksheet2db_array($data,array(
            array(array('kod','cecha','struktura'),'dkey'),
            array(array('marka'),'vendor'),
            array(array('opis pl','nazwa pl','pl'),'name_pl'),
            array(array('opis en','nazwa en','opis eu','nazwa eu','en','eu'),'name_en'),
            array(array('opis de','nazwa de','de'),'name_de'),
            array(array('opis ru','nazwa ru','ru'),'name_ru'),
        ),array('dkey','name_pl'));
        
        
        foreach ($dbarray AS $db) {
            $sql="DELETE FROM decora_dict WHERE dkey='".$db['dkey']."' AND type='$type' AND vendor='".$db['vendor']."';";
        
            $inserts=array('type');
            $values=array($type);
            foreach($db AS $k=>$v) {
                if ($k=='__row__') continue;
                $inserts[]=$k;
                $values[]=import_addslashes($v);
            }
            $sql.="INSERT INTO decora_dict (".implode(',',$inserts).") VALUES ('".implode("','",$values)."')";
            
            
            try {
                $count++;
                if ($dbh->exec($sql)!== false) continue; 
            } catch (Exception $e) {
                
            }
            
            $error=$dbh->errorInfo();
            $error['q']="\n$sql\n";
            $error['r']=$db['__row__'];
            $error['s']=$sheet;
            mydie($error);
                    


        }
        
        return $count;
        
    }

    function import_products($dbh,$data,$sheet='')
    {
        
        static $deleted;
        $count=0;
        
        if (is_null($data)) {
            $deleted=array();
            return;
        }

        $trans=array(
            array(array('marka'),'vendor'),
            array(array('produkt','grupa produktowa-poziom I'),'product'),
            array('ean','ean'),
            array('sap','sap'),
            array(array('kolekcja'),'collection'),
            array(array('kod koloru'),'color'),
            array(array('nazwa pl'),'name_pl'),
            array(array('nazwa en','nazwa eu'),'name_en'),
            array(array('nazwa de'),'name_de'),
            array(array('nazwa ru'),'name_ru'),
            array(array('nazwa www pl'),'name_www_pl'),
            array(array('nazwa www en','nazwa www eu'),'name_www_en'),
            array(array('nazwa www de'),'name_www_de'),
            array(array('nazwa www ru'),'name_www_ru'),
            array(array('opis krótki pl'),'desc_short_pl'),
            array(array('opis krótki en','opis krótki eu'),'desc_short_en'),
            array(array('opis krótki de'),'desc_short_de'),
            array(array('opis krótki ru'),'desc_short_ru'),
            array(array('opis ceny pl'),'price_desc_pl'),
            array(array('opis ceny en','opis ceny eu'),'price_desc_en'),
            array(array('opis ceny de'),'price_desc_de'),
            array(array('opis ceny ru'),'price_desc_ru'),
            array(array('opis pl'),'desc_pl'),
            array(array('opis en','opis eu'),'desc_en'),
            array(array('opis de'),'desc_de'),
            array(array('opis ru'),'desc_ru'),
            array(array('struktura'),'structure'),
            array(array('cechy'),'features'),            
            array(array('materiał'),'source'),
            array(array('wymiar pl'),'dim_pl'),
            array(array('wymiar en','opis eu'),'dim_en'),
            array(array('wymiar de'),'dim_de'),
            array(array('wymiar ru'),'dim_ru'),
            array(array('wymiary','rozmiar'),'dimension'),
            array(array('opakowanie - sztuk'),'pieces'),
            array(array('paleta - sztuk'),'box_pieces'),
            array(array('opakowanie - m2','opakowanie - mb'),'set_quantity'),
            array(array('cena pl'),'price_pl'),
            array(array('cena en','cena eu'),'price_en'),
            array(array('cena de'),'price_de'),
            array(array('cena ru'),'price_ru'),
            array(array('montaż','mocuj'),'assembly'),
            array(array('model','symbol'),'model'),
            array(array('dostępność'),'access'),
            array(array('grubość'),'thickness'),
            array(array('długość'),'length'),
            array(array('szerokość'),'width'),
            array(array('gęstość'),'density'),
            array(array('wykończenie'),'finish'),
            array(array('kolorystyczna'),'palette'),
            array(array('transparentność'),'transparency'),
            array(array('reprezentant','representant'),'representant'),
            array(array('video','youtube'),'video'),
            array('cena',array('price_pl','price_en','price_de','price_ru')),
            array('pl','pl'),
            array(array('en','eu'),'en'),
            array('de','de'),
            array('ru','ru'),
        );
        
        for ($i=1;$i<=12;$i++) {
            $trans[]=array(sprintf('cecha_%02d_wart',$i),sprintf('feature_%02d_value',$i));
            $trans[]=array(sprintf('cecha_%02d_skala',$i),sprintf('feature_%02d_scale',$i));
            
        }
        

        
        $dbarray = Spreadsheet::worksheet2db_array($data,$trans,array('ean','vendor','product'));
        
        $errors=array();
        
        $number_array=array('set_quantity','thickness','pieces','length','width','height','box_pieces');
        
        $deleted_tokens=array();
        
        foreach ($dbarray AS $db) {
            $db['vendor']=trim(mb_strtolower($db['vendor'],'utf-8'));
            $db['product']=trim(mb_strtolower($db['product'],'utf-8'));
            $db['ean']=trim($db['ean']);
            
            $token=$db['vendor'].'-'.$db['product'];
            
            if (!isset($deleted[$token])) {
                echoflush("Usuwanie wszystkiego z kategorii <b>$token</b>");
                $sql="DELETE FROM decora_products WHERE vendor='".$db['vendor']."' AND product='".$db['product']."'";
                $dbh->exec($sql);
                $deleted[$token]=true;
                $deleted_tokens[]=$token;       
            }
            
        
            $inserts=array();
            $values=array();
            foreach($db AS $k=>$v) {
                
                if ($k=='__row__') continue;
                if ((strstr($k,'price_') && !strstr($k,'desc')) || in_array($k,$number_array) || preg_match('/feature_[0-9]+_scale/',$k) ) {
                    $v=str_replace(',','.',$v);
                    $v=preg_replace('/[^0-9\.]/','',$v);
                }
                
                if (!strlen($v)) continue;
            
		if ($k=='video' && substr($v,0,4)!='http') $v="http://www.youtube.com/watch?v=$v";
                
                if ($v=='BRAK DANYCH') $v='N/D';
                
                $inserts[]=$k;
                if (strlen($k)==2) // pl,ru,de...
                {
                    $values[]=$v?1:0;
                }
                else 
                    $values[]="'".import_addslashes($v)."'";
                
            }
            $sql="INSERT INTO decora_products (".implode(',',$inserts).")\nVALUES (".implode(",",$values).")";
            
            
            
            try {
                
                if ($dbh->exec($sql)!== false) {
                    $count++;
                }
            } catch (Exception $e) {
                $error=$dbh->errorInfo();
                $error['error query']="\n$sql\n";
                $error['spreadsheet reference']=$sheet.' -> '.$db['__row__'];
                $error['deleted']=$deleted;
                $error['deleted_tokens']=$deleted_tokens;
                $errors[]=$error;
            }   
            
        }
        
        if (count($errors)) return $errors;
        return $count;
    }
    
    
    function import_recipients($dbh,$lang,$data)
    {
        $count=0;
        $trans=array(
            array(array('odbiorca'),'id'),
            array(array('wyróżnie','pri'),'pri'),
            array(array('sobota'),'hours_sa'),
            array(array('niedziela'),'hours_su'),
            array(array('godziny'),'hours_week'),
            array(array('nazwa sklepu'),'name'),
            array(array('kod'),'zip'),
            array(array('woj'),'province'),
            array(array('miasto'),'city'),
            array(array('ulica'),'street'),
            array(array('kom'),'tel2'),
            array(array('tel'),'tel1'),
            array(array('fax'),'fax'),
            array(array('mail'),'mail'),
            array(array('www'),'www'),
            
        );
        
        
        $ware=include(__DIR__.'/recipient2ware.php');
        foreach ($ware AS $k=>$v) $trans[]=array($v,'_'.$k);
        
        
        
        $dbarray = Spreadsheet::worksheet2db_array($data,$trans,array('id'));
        
        
        if (!count($dbarray)) mydie('No data in spreadsheet','Error');
        
        $sql="SELECT id, lat,lng FROM decora_recipients WHERE lang='$lang' AND lat IS NOT NULL AND lng IS NOT NULL";
        
        $latlng=array();
        $q=$dbh->query($sql);
    	if ($q) foreach ($q AS $row ){
        	$latlng[$row['id']]=array($row['lat'],$row['lng']);
    	}
        
        $errors=array();
        $dbh->beginTransaction();
        $sql="DELETE FROM decora_recipients WHERE lang='$lang'";
        $dbh->exec($sql);
        
        
        foreach ($dbarray AS $db) {
   
            $inserts=array('lang');
            $values=array("'$lang'");
            $receives=0;
            
            foreach($db AS $k=>$v) {
                if (!strlen($v) || $v=='0') continue;
                if ($k=='__row__') continue;
                
                if ($k[0]=='_') {
                    if ($v) $receives += pow(2,substr($k,1));
                    continue;
                }
                
                if ($k=='pri') {
                    $v=str_replace(',','.',$v);
                    $v=preg_replace('/[^0-9\.]/','',$v);
                }
                $inserts[]=$k;
                $values[]="'".import_addslashes($v)."'";
                
            }
            $inserts[]='receives';
            $values[]=$receives;
            
            
            if (isset($latlng[$db['id']])) {
                $inserts[]='lat';
                $inserts[]='lng';
                $values[]=$latlng[$db['id']][0];
                $values[]=$latlng[$db['id']][1];
                
            }  else {
                $address=$db['zip'].' '.$db['city'].', '.$db['street'];
                $url='http://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&region='.$lang;
                $data=json_decode(file_get_contents($url));
                
                
                if (is_array($data->results) && count($data->results))
                {
                    $inserts[]='lat';
                    $inserts[]='lng';
                    $values[]=$data->results[0]->geometry->location->lat;
                    $values[]=$data->results[0]->geometry->location->lng;
                } else {
                    $errors[$address]=$data;
                }
            }
            
            $sql="INSERT INTO decora_recipients (".implode(',',$inserts).")\nVALUES (".implode(",",$values).")";
            
            try {
                $count++;
                if ($dbh->exec($sql)!== false) continue; 
            } catch (Exception $e) {
                
            }
            
            
            $error=$dbh->errorInfo();
            $error['q']="\n$sql\n";
            $error['r']=$db['__row__'];
            $dbh->rollback();
            mydie($error);
        
            
        }
        
        
        $dbh->commit();
        
        if (count($errors) ) {
            return(array('errors'=>$errors,'label'=>'Errors: '.count($errors).', added records: '.$count));
        }
        
        
        return $count;
    }
    
    
    
