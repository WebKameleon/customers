<form method="post">

<?php
    WBP::kameleon_require_static_include($this);
    require_once __DIR__.'/system/jotform.php';



    
    
    if (isset($_POST['form'][$sid]))
    {
        $costxt=$_POST['form'][$sid];
     
        $cos=isset($_POST['native'][$sid]) ? 1 : 0;
     
        if ($costxt==-1)
        {
            $user=new userModel();
            $user->getCurrent();

            $jotform=new jotform($jotform_subkey);
            
            $data='properties[title]='.urlencode($this->webtd['title']);
            /*
            $data.='&emails[0][to]='.urlencode($user->email);
            $data.='&emails[0][type]=notification';
            $data.='&emails[0][name]=notification';
            $data.='&emails[0][from]=default';
            $data.='&emails[0][subject]=New+Submission';
            $data.='&emails[0][html]=false';
            */
            
            $f=$jotform->newForm($data);
            $costxt=$f['content']['id'].':'.$f['content']['username'];
    
            //echo '<h4>przejdź na stronę <a target="jotform" href="http://www.jotform.com/myforms/">JotForm</a> i przerzuć nowy formularz do folderu WBP</h4>';
        }
     
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->costxt=$costxt;
        $webtd->cos=$cos;
        $webtd->save();
    }
    
    
    $jotform=new jotform($jotform_key);
    $folders=$jotform->getFolders();
    
    $formularze=array();

    if (isset($folders['content']['subfolders'])) foreach ($folders['content']['subfolders'] AS $sf)
    { 
        if ($sf['name']!='WBP') continue;
        foreach ($sf['forms'] AS $f)
        {
            if ($f['status']!='ENABLED') continue;
            $formularze[$f['id'].':'.$f['username']]=$f['title'];
        }
    }
    
    $jotform=new jotform($jotform_subkey);
    $folders=$jotform->getFolders();
    
	//mydie($folders);
    foreach ($folders['content']['forms'] AS $f)
    {
        if ($f['status']!='ENABLED') continue;
        $formularze[$f['id'].':'.$f['username']]=$f['title'];        
    }
    
    
    
?>
<select class="jotform_select" name="form[<?php echo $sid?>]" id="form_<?php echo $sid?>">
    <option value="">Wybierz</option>
    <option value="-1"> - Nowy formularz - </option>
    
    <?php
        foreach($formularze AS $k=>$f)
        {
            $selected=$k==$costxt?'selected':'';
            echo '<option value="'.$k.'" '.$selected.'>'.$f.'</option>';
        }
    ?>
</select>
&nbsp; <input type="checkbox" value="1" title="wstaw bezpośrednio z JotForm zamiast dodawać WBP kolory" name="native[<?php echo $sid?>]" <?php if($cos) echo 'checked';?>/>
<a class="jotform_edit" href="" onclick="return go_jotform_<?php echo $sid?>(this)" target="jotform">&raquo;</a>
<div class="clearfix"></div>
<p><input type="submit" value="Zapisz" /></p>
</form>


<iframe name="jotform" style="display: none"></iframe>
<form method="post" id="jotformform" target="jotform" action="https://www.jotform.com/server.php">
    <input type="hidden" name="action" value="login" />
    <input type="hidden" name="username" value="<?php echo $jotform_user;?>" />
    <input type="hidden" name="password" value="<?php echo $jotform_pass;?>" />
    <input type="hidden" name="remember" value="false" />
    <input type="hidden" name="includeUsage" value="false" />
    <input type="hidden" name="forceDeleted" value="0" />
    
</form>
<script>
    function go_jotform_<?php echo $sid?>(a)
    {
        var f = $('#form_<?php echo $sid?>').val();
        if (f!='') {
            var ff=f.split(':');
            f=ff[0];
            
            if (ff.length==2) {
                document.getElementById('jotformform').submit();
                km_preloader_show();
                setTimeout(function () {location.href='http://www.jotform.com/?formID='+f;}, 1000);
                return false;
            } else {
                alert('Proszę wybrać formularz');
                return false;
            }
        } else  {
            alert('Proszę wybrać formularz');
            return false;
        }
    }
</script>
