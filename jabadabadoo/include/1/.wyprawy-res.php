<?php

    if (isset($_POST['flags'][$sid]))
    {
        $webtd=new webtdModel($sid);
        $costxt=$webtd->costxt=base64_encode(serialize($_POST['flags'][$sid]));
        $cos=$webtd->cos=$_POST['opt'][$sid]['lazyload'];
        $webtd->save();
    }
    
    $flags=unserialize(base64_decode($costxt));
?>
<form method="post">
    <input type="hidden" name="flags[<?php echo $sid?>][noflag]" value=""/>
    <input type="checkbox" name="flags[<?php echo $sid?>][homepage]" value="1" <?php if (isset($flags['homepage']) && $flags['homepage']) echo 'checked';?>/>
    homepage,
    <input type="checkbox" name="flags[<?php echo $sid?>][flag1]" value="1" <?php if (isset($flags['flag1']) && $flags['flag1']) echo 'checked';?>/>
    flag1,
    <input type="checkbox" name="flags[<?php echo $sid?>][flag2]" value="1" <?php if (isset($flags['flag2']) && $flags['flag2']) echo 'checked';?>/>
    flag2
    <input type="checkbox" name="flags[<?php echo $sid?>][confirm]" value="1" <?php if (isset($flags['confirm']) && $flags['confirm']) echo 'checked';?>/>
    potwierdzone
    
    <br/>
    
    <input type="checkbox" name="flags[<?php echo $sid?>][_uni]" value="1" <?php if (isset($flags['_uni']) && $flags['_uni']) echo 'checked';?>/>
    unikalne
    
    <br/>

    <input type="checkbox" name="flags[<?php echo $sid?>][_kaf]" value="1" <?php if (isset($flags['_kaf']) && $flags['_kaf']) echo 'checked';?>/>
    kafelki
    
    <br/>

    
    <input type="checkbox" name="opt[<?php echo $sid?>][lazyload]" value="1" <?php if ($cos) echo 'checked';?>/>
    leniwy ładunek
    
    <br/>
    
    <input type="checkbox" name="flags[<?php echo $sid?>][_hide_term]" value="1" <?php if (isset($flags['_hide_term']) && $flags['_hide_term']) echo 'checked';?>/>
    ukryj linię z terminem    
    
    <br/>    
  
    <input type="text" placeholder="Kontynent" name="flags[<?php echo $sid?>][continent]" value="<?php if (isset($flags['continent']) && $flags['continent']) echo $flags['continent'];?>"/>
    
    
    <br/>
    
    
    <input value="zapisz" type="submit"/>
</form>

