<?php

    $webtd=new webtdModel($this->webtd['sid']);
    if (!$cos) 
        $webtd->cos=1;
    $webtd->td = 3;
    $webtd->swfstyle = $loopback['card'];
    $webtd->save();
?>


<div class="parameters">
<?php foreach ($parameters AS $name=>$parameter): ?>
    <div class="form-group row">
        
        <div class="col-sm-12">
            <input type="text" class="form-control" value="<?php echo $parameter['label'];?>" name="loopback[parameters][<?php echo $name?>][label]" placeholder="<?php echo $name?> - label"/>
            <span class="messages"></span>
        </div>
        
        <?php if (isset($parameter['require']) && $cos) :?>
            <div class="col-sm-12">
                <input type="text" class="form-control" value="<?php echo $parameter['require'];?>" name="loopback[parameters][<?php echo $name?>][require]" placeholder="<?php echo $name?> - require text"/>
                <span class="messages"></span>
            </div>
        <?php endif;?>
        
        <?php if ($parameter['type']=='string' && $cos) :?>
        <div class="col-sm-12">
            <div class="checkbox-fade fade-in-primary d-">
                <label>
                    <input type="hidden" name="loopback[parameters][<?php echo $name?>][password]" value="0"/>
                    <input type="checkbox" name="loopback[parameters][<?php echo $name?>][password]" value="1" <?php if($parameter['password']) echo 'checked';?>>
                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                    <span class="text-inverse">Is it password</span>
                </label>
            </div>
        </div>
        <?php endif;?>
        
    </div>
<?php endforeach; ?>
</div>

<script>
    jQueryKam('#km_form_<?php echo $sid?> .parameters').sortable({
        zIndex: 10000
    });
</script>


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
    
    
</div>

<script>
    <?php if ($loopback['button_style']):?>
    jQueryKam('#km_form_<?php echo $sid?> .button-style').val('<?php echo $loopback['button_style']?>');
    <?php endif;?>
    <?php if ($loopback['button_color']):?>
    jQueryKam('#km_form_<?php echo $sid?> .button-color').val('<?php echo $loopback['button_color']?>');
    <?php endif;?>
    <?php if ($loopback['success_action']):?>
    jQueryKam('#km_form_<?php echo $sid?> .success-action').attr('v','<?php echo $loopback['success_action']?>');
    <?php endif;?>
    <?php if ($loopback['init_action']):?>
    jQueryKam('#km_form_<?php echo $sid?> .init-action').attr('v','<?php echo $loopback['init_action']?>');
    <?php endif;?>
    
</script>