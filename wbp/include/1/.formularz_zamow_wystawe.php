<?php
    WBP::kameleon_require_static_include($this);

    $user=Bootstrap::$main->session('user');
    //if ($user['username']!=$this->webtd['autor']) return;
    
    $tokens=json_decode($user['access_token'],true);
    $scopes=array('drive');
    
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
        WBP::put_data($configuration_file_name,$configuration);
        register_shutdown_function(function() {
            $ftp=new ftpController();
            $ftp->ftp_start('inc','',false);
        });
    }    
?>

<form method="POST">
 <ul>
    <li>
        <label for="drive_a">Wybór wzorca dokumentu</label>
        <a href="javascript:" id="drive_a" class="btn btn-success">Dokument: <?php echo $data['drive']['name']?$data['drive']['name']:'wybierz'?></a>
        <input type="hidden" name="drive[<?php echo $sid?>][id]" value="<?php echo $data['drive']['id']?>" id="drive_id"/>
        <input type="hidden" name="drive[<?php echo $sid?>][name]" value="<?php echo $data['drive']['name']?>" id="drive_name"/>
        <i>Pola do wykorzystania: [institution], [address], [phone],[email], [title], [since], [till], [receiverName],[receiverSurname], [orderName], [orderSurname], [now]</i>
    
    </li>
    <li>
        <label for="drive[<?php echo $sid?>][email]">Notyfikacja E-mail</label>
        <input type="text" name="drive[<?php echo $sid?>][email]" value="<?php echo $data['drive']['email']?>" />
    </li>
</ul>
<div>
    <input type="submit" value="Zapisz"/>
    <p class="warning"><?php
        if (!$this->webtd['next']) echo "<span>Ustaw stronę następną</span>";    
    ?></p>
</div>
</form>



<script type="text/javascript">

    function picked(data)
    { 
        if (data.action == google.picker.Action.PICKED) {
            var fileId = data.docs[0].id;
            var name = data.docs[0].name;
            
            $('#drive_a').html('Wybrano dokument: '+name);
            $('#drive_id').val(fileId);
            $('#drive_name').val(name);

            
        }
    }
    
    function createPicker() {
        
        var picker = new google.picker.PickerBuilder().
                            addView(new google.picker.DocsView(google.picker.ViewId.DOCUMENTS)).
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