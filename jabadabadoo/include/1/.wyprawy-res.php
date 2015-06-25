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
    
    <div class="row">
    
    <div class="col-md-2 col-sm-2">
     <input type="checkbox" name="flags[<?php echo $sid?>][homepage]" value="1" <?php if (isset($flags['homepage']) && $flags['homepage']) echo 'checked';?>/>
     <label>Strona główna</label>
    </div>
    
    <!--<div class="col-md-2 col-sm-2">
     <input type="checkbox" name="flags[<?php echo $sid?>][flag1]" value="1" <?php if (isset($flags['flag1']) && $flags['flag1']) echo 'checked';?>/>
     <label>Wyświetlaj dni</label>
    </div>-->
    
    <!--<input type="checkbox" name="flags[<?php echo $sid?>][flag2]" value="1" <?php if (isset($flags['flag2']) && $flags['flag2']) echo 'checked';?>/>
    flag2-->
    
    <div class="col-md-2 col-sm-2">
     <input type="checkbox" name="flags[<?php echo $sid?>][confirm]" value="1" <?php if (isset($flags['confirm']) && $flags['confirm']) echo 'checked';?>/>
     <label>Potwierdzone</label>
    </div>
    
    
    <div class="col-md-2 col-sm-2">
     <input type="checkbox" name="flags[<?php echo $sid?>][_uni]" value="1" <?php if (isset($flags['_uni']) && $flags['_uni']) echo 'checked';?>/>
     <label>Unikalne oferty</label>
    </div>
    
    <div class="col-md-2 col-sm-2">
     <input type="checkbox" name="flags[<?php echo $sid?>][_kaf]" value="1" <?php if (isset($flags['_kaf']) && $flags['_kaf']) echo 'checked';?>/>
     <label>Kafelki</label>
    </div>
    
   <div class="col-md-4 col-sm-4">
     <input type="checkbox" name="opt[<?php echo $sid?>][lazyload]" value="1" <?php if ($cos) echo 'checked';?>/>
     <label>Doczytuj oferty dynamicznie</label>
    </div>
    
    
    </div>
    
    <div class="row" style="margin-top:25px;">
        
     
        
    <div class="col-md-3 col-sm-4">
     <input type="checkbox" name="flags[<?php echo $sid?>][_hide_term]" value="1" <?php if (isset($flags['_hide_term']) && $flags['_hide_term']) echo 'checked';?>/>
     <label>Ukryj linię z terminem</label>
    </div>
   
    <div class="col-md-3 col-sm-4">
     <input type="text" placeholder="Kontynent" name="flags[<?php echo $sid?>][continent]" value="<?php if (isset($flags['continent']) && $flags['continent']) echo $flags['continent'];?>"/>
    </div>
       
    <div class="col-md-3 col-sm-4">
     <input value="Zapisz" type="submit" class="btn btn-default"/>
    </div> 
    
    </div>
</form>

