<?php

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
                    <label title="Text">
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="text" <?php if ($parameter['fieldType']=='text' || !$parameter['fieldType']) echo 'checked';?>/>
                        <i class="helper"></i>TX
                    </label>
                </div>
                <div class="radio radio-outline radio-inline">
                    <label title="Password">
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="password" <?php if ($parameter['fieldType']=='password') echo 'checked';?>/>
                        <i class="helper"></i>PS
                    </label>
                </div>
                <div class="radio radio-outline radio-inline">
                    <label title="Select">
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="select" <?php if ($parameter['fieldType']=='select') echo 'checked';?>/>
                        <i class="helper"></i>SL
                    </label>
                </div>
                <div class="radio radio-outline radio-inline">
                    <label title="Hidden">
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="hidden" <?php if ($parameter['fieldType']=='hidden') echo 'checked';?>/>
                        <i class="helper"></i>HD
                    </label>
                </div>
                <div class="radio radio-outline radio-inline">
                    <label title="Range slider">
                        <input  type="radio" name="loopback[parameters][<?php echo $name?>][fieldType]" value="range" <?php if ($parameter['fieldType']=='range') echo 'checked';?>/>
                        <i class="helper"></i>RG
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
        
        <?php if ($parameter['fieldType']=='range' && $swagger):?>
            <div class="col-sm-3" title="Range min for '<?php echo $name?>'">
                <input type="text" class="form-control" value="<?php echo $parameter['sliderMin'];?>" name="loopback[parameters][<?php echo $name?>][sliderMin]" placeholder="min value"/>
                <span class="messages"></span>
            </div>
            
            <div class="col-sm-3" title="Range max for '<?php echo $name?>'">
                <input type="text" class="form-control" value="<?php echo $parameter['sliderMax'];?>" name="loopback[parameters][<?php echo $name?>][sliderMax]" placeholder="max value"/>
                <span class="messages"></span>
            </div>
            
            <div class="col-sm-3" title="Range step for '<?php echo $name?>'">
                <input type="text" class="form-control" value="<?php echo $parameter['sliderStep'];?>" name="loopback[parameters][<?php echo $name?>][sliderStep]" placeholder="step value"/>
                <span class="messages"></span>
            </div>
            
            <div class="col-sm-3" title="Range default value for '<?php echo $name?>'">
                <input type="text" class="form-control" value="<?php echo $parameter['sliderValue'];?>" name="loopback[parameters][<?php echo $name?>][sliderValue]" placeholder="default value"/>
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