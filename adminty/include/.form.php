<?php

    $webtd=new webtdModel($this->webtd['sid']);
    if (!$cos) 
        $webtd->cos=1;
    $webtd->ob = 3;
    $webtd->swfstyle = $loopback['card'];
    $webtd->save();
    
    
    if ($swagger) {
        $sw=swagger($swagger,isset($loopback['initAction'])?$loopback['initAction']:null,[],[]);
        $initOptions=$sw['loopbackOptions'];
    }
    
    
    $nextValueOptions='';
    foreach ($parameters AS $name=>$parameter) {
        $s=$loopback['addValueToNext']==$name?'selected':'';
        $nextValueOptions.='<option '.$s.' value="'.$name.'">'.$name.'</option>';
    }
    
?>


<div class="parameters">
<?php foreach ($parameters AS $name=>$parameter): ?>
    <div class="form-group row">
        
        <div class="col-sm-12" title="label for the field: <?php echo $name?>">
            <input type="text" class="form-control" value="<?php echo $parameter['label'];?>" name="loopback[parameters][<?php echo $name?>][label]" placeholder="<?php echo $name?> - label"/>
            <span class="messages"></span>
        </div>
        
        <?php if (isset($parameter['require']) && $cos) :?>
            <div class="col-sm-12" title="text if field '<?php echo $name?>' is missing">
                <input type="text" class="form-control" value="<?php echo $parameter['require'];?>" name="loopback[parameters][<?php echo $name?>][require]" placeholder="<?php echo $name?> - require text"/>
                <span class="messages"></span>
            </div>
        <?php endif;?>
        
        <?php if (strlen($parameter['label']) && ($parameter['type']=='string' || $parameter['type']=='number')) :?>

        <div class="col-sm-12">
            <div class="form-radio" tytle="Field type">
             
                <div class="radio radio-outline radio-inline">
                    <label>
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="text" <?php if ($parameter['fieldType']=='text' || !$parameter['fieldType']) echo 'checked';?>/>
                        <i class="helper"></i>Txt
                    </label>
                </div>
                <div class="radio radio-outline radio-inline">
                    <label>
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="password" <?php if ($parameter['fieldType']=='password') echo 'checked';?>/>
                        <i class="helper"></i>Pass
                    </label>
                </div>
                <div class="radio radio-outline radio-inline">
                    <label>
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="select" <?php if ($parameter['fieldType']=='select') echo 'checked';?>/>
                        <i class="helper"></i>Sel
                    </label>
                </div>
                <div class="radio radio-outline radio-inline">
                    <label>
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="hidden" <?php if ($parameter['fieldType']=='hidden') echo 'checked';?>/>
                        <i class="helper"></i>Hide
                    </label>
                </div>
            </div>
        </div>
        <?php endif;?>
        
        <?php if ($parameter['fieldType']=='select' && $swagger):?>
        <?php
            $sws=swagger($swagger,isset($parameter['select'])?$parameter['select']:null,[],[]);
            $selectOptions=$sws['loopbackOptions'];
        ?>
        <div class="col-sm-12">
            <select class="select2 col-sm-12 select-path" name="loopback[parameters][<?php echo $name?>][select]">
                <option value="">Select path</option>
                <?php echo $selectOptions;?>
            </select>
            <span class="messages"></span>
        </div>
        <?php endif;?>
        
        <?php if (isset($parameter['select']) && $parameter['select']):?>
            <div class="col-sm-6" title="Select label for '<?php echo $name?>'">
                <input type="text" class="form-control" value="<?php echo $parameter['selectLabel'];?>" name="loopback[parameters][<?php echo $name?>][selectLabel]" placeholder="Select label for '<?php echo $name?>'"/>
                <span class="messages"></span>
            </div>
            <div class="col-sm-6" title="Select value for '<?php echo $name?>'">
                <input type="text" class="form-control" value="<?php echo $parameter['selectValue'];?>" name="loopback[parameters][<?php echo $name?>][selectValue]" placeholder="Select value for '<?php echo $name?>'"/>
                <span class="messages"></span>
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