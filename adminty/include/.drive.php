<?php

    if ($this->webtd['ob']!==3 || $this->webtd['staticinclude']!=1) {
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->ob = 3;
        $webtd->staticinclude = 1;
        $webtd->save();
    }
    
    //mydie($loopback);
    
    if ($swagger) {
        $sw=swagger($swagger,isset($loopback['postAction'])?$loopback['postAction']:null,[],[]);
        $postOptions=$sw['loopbackOptions'];
    }
?>


<div class="form-group row">   
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[postAction]">
            <option value="">Choose save token path</option>
            <?php echo $postOptions;?>
        </select>
        <span class="messages"></span>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-12">
        Button:
    </div>
    <div class="col-sm-12">
        <input type="text" class="form-control" value="<?php echo $loopback['token']?>" name="loopback[token]" placeholder="Drive get token text">
        <span class="messages"></span>
    </div>
    <div class="col-sm-12">
        <select class="select2 col-sm-12 button-style" name="loopback[button_style]">
            <option value="square">Submit button style - default</option>
            <option value="round">Submit button style - rounded</option>
            <option value="skew">Submit button style - skew</option>
            <option value="out">Submit button style - inner solid border</option>
            <option value="out-dashed">Submit button style - inner dashed border</option>
            <option value="out-dotted">Submit button style - inner dotted border</option>
        </select>
        <span class="messages"></span>
    </div>
    
    <div class="col-sm-12">
        <select class="select2 col-sm-12 button-color" name="loopback[button_color]">
            <option value="primary">Submit button color - primary</option>
            <option value="success">Submit button color - success</option>
            <option value="info">Submit button color - info</option>
            <option value="warning">Submit button color - warning</option>
            <option value="danger">Submit button color - danger</option> 
        </select>
        <span class="messages"></span>
    </div>
</div>


<script>
    <?php if ($loopback['button_style']):?>
    jQueryKam('#km_form_<?php echo $sid?> .button-style').val('<?php echo $loopback['button_style']?>');
    <?php endif;?>
    <?php if ($loopback['button_color']):?>
    jQueryKam('#km_form_<?php echo $sid?> .button-color').val('<?php echo $loopback['button_color']?>');
    <?php endif;?>
    
</script>
