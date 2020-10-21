<?php

    $webtd=new webtdModel($this->webtd['sid']);
    if (!$cos) {
        $webtd->cos=1;
        $webtd->save();
    }

    
    include(__DIR__.'/system/parameters.php');
        
?>




<div class="row form-group">
    <div class="col-12">
        <div class="checkbox-fade fade-in-primary d-">
            <label>
                <input type="hidden" name="loopback[card]" value="0"/>
                <input type="checkbox" name="loopback[card]" value="1" <?php if($loopback['card']) echo 'checked';?>>
                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                <span class="text-inverse">Card style</span>
            </label>
        </div>
    </div>
</div>

<div class="row form-group">
    <div class="col-12">
        <div class="checkbox-fade fade-in-primary d-">
            <label>
                <input type="hidden" name="loopback[round]" value="0"/>
                <input type="checkbox" name="loopback[round]" value="1" <?php if($loopback['round']) echo 'checked';?>>
                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                <span class="text-inverse">Round style</span>
            </label>
        </div>
    </div>
</div>


<div class="form-group row">
    <div class="col-sm-12">
        Submit button:
    </div>
    <div class="col-sm-12">
        <input type="text" class="form-control" value="<?php echo $loopback['submit']?>" name="loopback[submit]" placeholder="Submit button text">
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

<div class="form-group row">
    <div class="col-sm-12">
        Return button:
    </div>
    <div class="col-sm-12">
        <input type="text" class="form-control" value="<?php echo $loopback['return']?>" name="loopback[return]" placeholder="Return button text">
        <span class="messages"></span>
    </div>
    <div class="col-sm-12">
        <select class="select2 col-sm-12 return-button-style" name="loopback[return_button_style]">
            <option value="square">Return button style - default</option>
            <option value="round">Return button style - rounded</option>
            <option value="skew">Return button style - skew</option>
            <option value="out">Return button style - inner solid border</option>
            <option value="out-dashed">Return button style - inner dashed border</option>
            <option value="out-dotted">Return button style - inner dotted border</option>
        </select>
        <span class="messages"></span>
    </div>
    
    <div class="col-sm-12">
        <select class="select2 col-sm-12 return-button-color" name="loopback[return_button_color]">
            <option value="primary">Return button color - primary</option>
            <option value="success">Return button color - success</option>
            <option value="info">Return button color - info</option>
            <option value="warning">Return button color - warning</option>
            <option value="danger">Return button color - danger</option> 
        </select>
        <span class="messages"></span>
    </div>
</div>



<div class="form-group row">
    <div class="col-sm-12">
        Actions:
    </div>
    <div class="col-sm-12">
        <select class="select2 col-sm-12 success-action loopback-functions" name="loopback[success_action]">
            <option value="">Success action</option>
        </select>
        <span class="messages"></span>
    </div>
    <div class="col-sm-12">
        <select class="select2 col-sm-12 init-action loopback-functions" name="loopback[init_action]">
            <option value="">Init action</option>
        </select>
        <span class="messages"></span>
    </div>
    
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[initAction]">
            <option value="">Choose init form path</option>
            <?php echo $initOptions;?>
        </select>
        <span class="messages"></span>
    </div>
    
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[addValueToNext]">
            <option value="">Add form value to next link</option>
            <?php echo $nextValueOptions;?>
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
    
    <?php if ($loopback['return_button_style']):?>
    jQueryKam('#km_form_<?php echo $sid?> .return-button-style').val('<?php echo $loopback['return_button_style']?>');
    <?php endif;?>
    <?php if ($loopback['return_button_color']):?>
    jQueryKam('#km_form_<?php echo $sid?> .return-button-color').val('<?php echo $loopback['return_button_color']?>');
    <?php endif;?>
    
    <?php if ($loopback['success_action']):?>
    jQueryKam('#km_form_<?php echo $sid?> .success-action').attr('v','<?php echo $loopback['success_action']?>');
    <?php endif;?>
    <?php if ($loopback['init_action']):?>
    jQueryKam('#km_form_<?php echo $sid?> .init-action').attr('v','<?php echo $loopback['init_action']?>');
    <?php endif;?>
    
</script>