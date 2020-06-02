<?php

    $webtd=new webtdModel($this->webtd['sid']);

    $webtd->ob = 3;
    //$webtd->swfstyle = $loopback['card'];
    $webtd->save();
    
    if ($swagger) {
        $sw=swagger($swagger,isset($loopback['putAction'])?$loopback['putAction']:null,[],[]);
        $putOptions=$sw['loopbackOptions'];
        $sw=swagger($swagger,isset($loopback['deleteAction'])?$loopback['deleteAction']:null,[],[]);
        $deleteOptions=$sw['loopbackOptions'];
        $sw=swagger($swagger,isset($loopback['postAction'])?$loopback['postAction']:null,[],[]);
        $postOptions=$sw['loopbackOptions'];
    }
    
?>


<div class="fields">
<?php foreach ($fields AS $name=>$field): ?>
    <div class="form-group row">
        
        <div class="col-sm-12">
            <input type="text" class="form-control" value="<?php echo $field['label'];?>" name="loopback[fields][<?php echo $name?>][label]" placeholder="<?php echo $name?> - column name"/>
            <span class="messages"></span>
        </div>
        
  
        <div class="col-sm-12">
            <input type="text" class="form-control" value="<?php echo $field['editable'];?>" name="loopback[fields][<?php echo $name?>][editable]" placeholder="<?php echo $name?> - editable"/>
            <span class="messages"></span>
        </div>
    
        
        
    </div>
<?php endforeach; ?>
</div>

<script>
    jQueryKam('#km_form_<?php echo $sid?> .fields').sortable({
        zIndex: 10000
    });
</script>

<div class="form-group row">
    <div class="col-sm-12">
        Button actions:
    </div>
    <label class="col-sm-1" style="text-align:right">
        <i class="fa fa-plus"></i>
    </label>
    <div class="col-sm-11">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['add']['title'];?>" name="loopback[actions][add][title]" placeholder="add button title"/>
    </div>

    <label class="col-sm-1" style="text-align:right">
        <i class="fa fa-copy"></i>
    </label>
    <div class="col-sm-5">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['copy']['title'];?>" name="loopback[actions][copy][title]" placeholder="copy button title"/>
    </div>
    <div class="col-sm-6">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['copy']['text'];?>" name="loopback[actions][copy][text]" placeholder="copy text"/>
    </div>
    
    <label class="col-sm-1" style="text-align:right">
        <i class="fa fa-trash"></i>
    </label>
       <div class="col-sm-5">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['trash']['title'];?>" name="loopback[actions][trash][title]" placeholder="trash button title"/>
    </div>
    <div class="col-sm-6">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['trash']['confirm'];?>" name="loopback[actions][trash][confirm]" placeholder="trash confirmation?"/>
    </div>
    
</div>

<div class="form-group row">   
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[postAction]">
            <option value="">Choose post path</option>
            <?php echo $postOptions;?>
        </select>
        <span class="messages"></span>
    </div>
</div>

<div class="form-group row">   
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[putAction]">
            <option value="">Choose put path</option>
            <?php echo $putOptions;?>
        </select>
        <span class="messages"></span>
    </div>
</div>

<div class="form-group row">   
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[deleteAction]">
            <option value="">Choose delete path</option>
            <?php echo $deleteOptions;?>
        </select>
        <span class="messages"></span>
    </div>
</div>


<script>
    /*
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
    */
</script>