<?php

    if (!$this->webtd['staticinclude']) {
        $webtd=new webtdModel($sid);
        $webtd->staticinclude=1;
        $webtd->save();
    }

    if (isset($_GET['form'][$sid])) {
        $costxt=$_GET['form'][$sid];
        $webtd=new webtdModel($sid);
        $webtd->costxt=$costxt;
        $webtd->save();        
    }

    require_once __DIR__.'/system/const.php';
    require_once __DIR__.'/system/jotform.php';
    
    
    $forms = Bootstrap::$main->session('forms');
    
    if (!$forms) {
        $jotform=new jotform($JOTFORM_API_KEY);
        $forms=$jotform->getForms();
        
        Bootstrap::$main->session('forms',$forms);  
    }
    
    $options='';
    if (isset($forms['content'])) foreach ($forms['content'] AS $content)
    {
        if ($content['status']=='ENABLED') {
            $val=$content['id'].':'.$content['height'];
            $sel=$val==$costxt?' selected':'';
            $options.='<option value="'.$val.'"'.$sel.'>'.$content['title'].'</option>';
        }
    }
    
?>
<form method="get" action="<?php echo $self;?>">
    <select name="form[<?php echo $sid;?>]" onchange="submit()">
        <option valie="">Wybierz formularz</option>
        <?php echo $options;?>
    </select>
</form>
    