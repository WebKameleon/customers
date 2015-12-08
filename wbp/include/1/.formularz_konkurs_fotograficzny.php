<?php
    $default_next=250;
    
    if (!$this->webtd['next'])
    {
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->next=$default_next;
        $webtd->save();
    }

    WBP::kameleon_require_static_include($this);

    $user=Bootstrap::$main->session('user');
    
    if ($user['username']!=$this->webtd['autor'])
    {
        $u=new userModel($this->webtd['autor']);
        echo '<span class="warning">Nie jesteś właścicielem artykułu (jest nim '.$u->fullname.')</span>';
        
        //return;
    }
    $tokens=json_decode($user['access_token'],true);
    $scopes=array('drive','spreadsheets');
    
    foreach ($scopes AS $scope)
        if (!isset($tokens[$scope]) || !$tokens[$scope])
            Bootstrap::$main->redirect('scopes/'.$scope.'?setreferpage='.$page);
    
    $data=unserialize(base64_decode($costxt));
    

    $access_data['oauth2'] = Bootstrap::$main->getConfig('oauth2');        
    $access_token=json_decode(Google::getUserClient(null,false,'drive')->getAccessToken());
    $access_data['access_token']=$access_token->access_token;    
    
    
    $configuration_file_name=md5($sid);
    $configuration=WBP::get_data($configuration_file_name);
    
    $configuration['sid']=$sid;
    $configuration['title']=$this->webtd['title']?:$this->webpage['title'];
    
    
    if (isset($_POST['drive'][$sid]))
    {
        $data['drive']=$_POST['drive'][$sid];
        $configuration['drive']=$data['drive'];
        
	/*

            $client=Google::getUserClient(null,false,'drive');
            $service = Google::getDriveService($client);
            $file=$service->files->get($_POST['drive'][$sid]['id']);

		mydie($file);
	*/
        
        $webtd=new webtdModel($sid);
        $webtd->costxt = base64_encode(serialize($data));
        $webtd->save();
    }
    
    if (isset($data['drive']) && !isset($configuration['drive'])) $configuration['drive']=$data['drive'];
    

    $access_token=json_decode($user['access_token'],true);
    foreach ($scopes AS $scope)
    {
        if (isset($access_token[$scope]) && !isset($configuration['tokens'][$scope]))
        {
            $configuration['tokens'][$scope]=json_decode($access_token[$scope]);
        }
    }
    
    
    
    if (isset($_POST['drive'][$sid]))
    {
		echo "<script>km_preloader_show();</script>";
		flush();
		ob_end_flush();		
		
        WBP::put_data($configuration_file_name,$configuration);
        
        register_shutdown_function(function() {
            $ftp=new ftpController();
            $ftp->ftp_start('inc','',false);
			echo "<script>km_preloader_hide();</script>";
        });
    }
    
    $local_spredsheet=0+@filemtime(__DIR__.'/kameleon/Spreadsheet.php');
    $kameleon_spredsheet=filemtime(APPLICATION_PATH.'/classes/Spreadsheet.php');
    
    if ($kameleon_spredsheet>$local_spredsheet) copy(APPLICATION_PATH.'/classes/Spreadsheet.php',__DIR__.'/kameleon/Spreadsheet.php');
      
?>

<form method="POST">
 <ul>
    <li>
        <label for="drive_a">Wybór arkusza</label>
        <a href="javascript:" id="drive_a" class="btn btn-success">Arkusz: <?php echo $data['drive']['name']?$data['drive']['name']:'wybierz'?></a>
        <input type="hidden" name="drive[<?php echo $sid?>][id]" value="<?php echo $data['drive']['id']?>" id="drive_id"/>
        <input type="hidden" name="drive[<?php echo $sid?>][name]" value="<?php echo $data['drive']['name']?>" id="drive_name"/>
    
        <i>dowolny arkusz, do którego będą zapisywane wpisy z formularza</i>
    </li>

    <li>
        <label for="drive_img_min">Minimalna liczba zdjęć</label>
        <select name="drive[<?php echo $sid?>][img_min]">
        <?php
            for ($i=1;$i<=5;$i++)
            {
                $selected=$data['drive']['img_min']==$i?'selected':'';
                echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            ?>
            
        </select>
    </li>

    <li>
        <label for="drive_img_max">Maksymalna liczba zdjęć</label>
        <select name="drive[<?php echo $sid?>][img_max]">
        <?php
            for ($i=1;$i<=20;$i++)
            {
                $selected=$data['drive']['img_max']==$i?'selected':'';
                echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            ?>
            <option value="0" <?php if ($data['drive']['img_max']==='0') echo 'selected' ?>>Bez ograniczeń</option>
        </select>
    </li>
    <li>
        <label for="drive_price_pln">Cena</label>
        <input type="text" class="price" name="drive[<?php echo $sid?>][price_pln]" value="<?php echo $data['drive']['price_pln']?>" /> PLN
    </li>
    <li>
        <label for="drive_price_eur">Cena</label>
        <input type="text" class="price" name="drive[<?php echo $sid?>][price_eur]" value="<?php echo $data['drive']['price_eur']?>" /> EUR
    </li>
    
    <li>
        <?php foreach ($contest_categories AS $class=>$txt) : ?>
        <input type="checkbox" class="price" name="drive[<?php echo $sid?>][cat][]" value="<?php echo $class; ?>" <?php if (isset($data['drive']['cat']) && in_array($class,$data['drive']['cat'])) echo 'checked'; ?> />
        <?php echo $txt; ?> <br/>
        <?php endforeach; ?>
    </li>
    
    <li>
        <label for="drive_email">E-mail do notyfikacji</label>
        <input type="text" class="email" name="drive[<?php echo $sid?>][email]" value="<?php echo $data['drive']['email']?>" /> 
    </li>    
    
</ul>
<p><input type="submit" value="Zapisz" onclick="km_preloader_show();"/></p>
</form>


<script type="text/javascript">

    function picked(data)
    { 
        if (data.action == google.picker.Action.PICKED) {
            var fileId = data.docs[0].id;
            var name = data.docs[0].name;
            
            $('#drive_a').html('Wybrano arkusz: '+name);
            $('#drive_id').val(fileId);
            $('#drive_name').val(name);

            
        }
    }
    
    function createPicker() {
        
        var picker = new google.picker.PickerBuilder().
                            addView(new google.picker.DocsView(google.picker.ViewId.SPREADSHEETS)).
                            setAppId('<?php echo $access_data['api_key']?>').
                            setOAuthToken('<?php echo $access_data['access_token']?>').
                            setCallback(picked).
                            setLocale('pl').
                            build().
                            setVisible(true);

    }

    function initPicker() {
        jQueryKam("#drive_part").show();
        jQueryKam("#drive_a").on('click',function() {
            gapi.load('picker', {
                'callback':createPicker
            });
        });
    }
    
    jQueryKam(".gdrive_folder").hide();
    jQueryKam("#drive_part").hide();
    

    
    

</script>

<script type="text/javascript" src="https://www.google.com/jsapi?key=<?php echo $access_data['api_key']?>"></script>
<script type="text/javascript" src="https://apis.google.com/js/client.js?onload=initPicker"></script>
