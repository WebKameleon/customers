<form method="POST" action="<?php echo $self?>#kameleon_td<?php echo $sid?>" id="wystawa_<?php echo $sid?>">
<ul>

<?php
	WBP::kameleon_require_static_include($this);

        $objects=WBP::get_file_db('objects');
	$tdmodel=new webtdModel();
        
        if (isset($_POST['kalendarz'][$sid]))
        {
            $kalendarz=array();
            foreach ($_POST['kalendarz'][$sid]['from'] AS $i=>$from)
            {
                if ($_POST['kalendarz'][$sid]['from'][$i] && $_POST['kalendarz'][$sid]['to'][$i] && $_POST['kalendarz'][$sid]['object'][$i]) {
            
                    $kalendarz[strtotime($_POST['kalendarz'][$sid]['from'][$i])] = array (
                        'from'=>strtotime($_POST['kalendarz'][$sid]['from'][$i]),
                        'to'=>strtotime($_POST['kalendarz'][$sid]['to'][$i]),
                        'object'=>$_POST['kalendarz'][$sid]['object'][$i],
                    );
                }
            }
            ksort($kalendarz);
            
            $webpage=new webpageModel($this->webpage['sid']);
            $this->webpage['pagekey'] = $webpage->pagekey=base64_encode(serialize($kalendarz));
            
            $webpage->save();
            
            $webtd=new webtdModel();
            $wystawy=array();
            $aktualne_wystawy=array();
            $zajawki=array();
            
            foreach ($webpage->getChildren($this->webtd['page_id']) AS $pg)
            {
                if ($pg['hidden']) continue;
		
		$tdks=$tdmodel->getAll([$pg['id']]);
		
                
                $time=unserialize(base64_decode($pg['pagekey']));
                if (is_array($time)) foreach($time AS $t)
                {
                    $key=$t['from'];
                    while(isset($wystawy[$key])) $key++;
                    $t['id']=$pg['id'];
                    $t['title']=$pg['title_short']?:$pg['title'];
                    $t['href']=$pg['file_name'];
                    
                    if (!isset($zajawki[$pg['id']]))
                    {
                        $tds=$webtd->getAll(array($pg['id']));
                        foreach ($tds AS $td)
                        {
                            if ($td['trailer'])
                            {
                                $zajawki[$pg['id']] = $td['trailer'];
                                break;
                            }
                        }
                        
                    }
                    $t['trailer']=$zajawki[$pg['id']];
		    if ($tdks[0]['bgimg']) $t['img']='widgets/articlelist/gfx/icon/'.$tdks[0]['bgimg'];
                    
                    $wystawy[$key]=$t;
                    if ($t['to']>time()) $aktualne_wystawy[$key]=$t;
                }
                
            }
            krsort($wystawy);
            krsort($aktualne_wystawy);
            
            WBP::put_data('wystawy',$wystawy);
            WBP::put_data('aktualne_wystawy',$aktualne_wystawy);
	    
	    register_shutdown_function(function() {
		$ftp=new ftpController();
		$ftp->ftp_start('inc','',false);
	    });
        }
	$kalendarz=unserialize(base64_decode($this->webpage['pagekey']));
	
        
        
        
        
        $fotoobj=array();
        
        foreach ($objects AS $obj)
        {
            if (!$obj['x_fotografia']) continue;
            $fotoobj[$obj['miasto'].$obj['nazwa']." - ".$obj['id']] = array($obj['id'],$obj['miasto'].' - '.$obj['nazwa']);
        }
        ksort($fotoobj);
        
        
        
        foreach ($kalendarz AS $kal)
        {
            echo '<li>';
            echo 'od <input type="date" name="kalendarz['.$sid.'][from][]" value="'.date('Y-m-d',$kal['from']).'"/>';
            echo 'do <input type="date" name="kalendarz['.$sid.'][to][]" value="'.date('Y-m-d',$kal['to']).'"/>';
            
            echo '<select name="kalendarz['.$sid.'][object][]"><option value="">Wybierz</option>';
            foreach ($fotoobj AS $obj)
            {
                $selected=$obj[0]==$kal['object']?'selected':'';
                echo '<option value="'.$obj[0].'" '.$selected.'>'.$obj[1].'</option>';
            }
            echo '</select>';
            
            echo '</li>';
            
        }

            echo '<li>';
            echo 'od <input type="date" name="kalendarz['.$sid.'][from][]" placeholder="nowa wizyta"/>';
            echo 'do <input type="date" name="kalendarz['.$sid.'][to][]" placeholder="nowa wizyta"/>';
            
            echo '<select name="kalendarz['.$sid.'][object][]"><option value="">Wybierz</option>';
            foreach ($fotoobj AS $obj)
            {
                
                echo '<option value="'.$obj[0].'">'.$obj[1].'</option>';
            }
            echo '</select>';
            
            echo '</li>';
       
        //mydie($kalendarz);
        
        
?>
</ul>
<input type="submit" value="zapisz" />
</form>

<script>
    c=jQueryKam('#wystawa_<?php echo $sid?>').parent().children().each(function () {
        if (this.nodeName.toLowerCase() <?php echo $this->webpage['id']==$this->webtd['page_id'] ? '==':'!='?> 'form') {
            jQueryKam(this).hide();
        }
    });
    
</script>