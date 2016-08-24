<form method="post">

<?php
    WBP::kameleon_require_static_include($this);
    require_once __DIR__.'/system/jotform.php';



    
    
    if (isset($_POST['form']['kwota'][$sid]))
    {
        
        $costxt=$_POST['form']['kwota'][$sid];
     
        $cos=isset($_POST['form']['auto'][$sid]) ? 1 : 0;
     
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->costxt=$costxt;
        $webtd->cos=$cos;
        $webtd->save();
        
    }
    
    
    
    
?>
Kwota: <input class="" name="form[kwota][<?php echo $sid?>]" placeholder="w zł" value="<?php echo $costxt?>"/>

<br/>

    
Automatycznie przejdź do Dotpaya &nbsp; <input type="checkbox" value="1" title="Automatycznie przejdź do dotpaya" name="form[auto][<?php echo $sid?>]" <?php if($cos) echo 'checked';?>/>

<div class="clearfix"></div>
<p><input type="submit" value="Zapisz" /></p>
</form>

