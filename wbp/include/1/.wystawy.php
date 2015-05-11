<form method="post" action="<?php echo $self?>">
<?php
    WBP::kameleon_require_static_include($this);
    
    if (isset($_POST['lazyload'][$sid]))
    {
        $costxt=$_POST['lazyload'][$sid];
        $webtd=new webtdModel($sid);
        $webtd->costxt=$costxt;
        $webtd->save();
    }
    

    
?>
<p>
    <input type="hidden" name="lazyload[<?php echo $sid?>]" value="0"/>
    <input type="checkbox" name="lazyload[<?php echo $sid?>]" value="1" <?php if ($costxt) echo 'checked'?>/> Lazy load
</p>
<input type="submit" value="zapisz"/>
</form>