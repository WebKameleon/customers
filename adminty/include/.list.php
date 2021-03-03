
<div class="form-group row">
        
    <div class="col-sm-12">
        <input type="text" class="form-control" value="<?php echo $loopback['include'];?>" name="loopback[include]" placeholder="include relations: <?php echo $include?join(', ',$include):'choose path first';?>" title="include: <?php echo join(', ',$include);?>"/>
        <span class="messages"></span>
    </div>
        
        
</div>

<div class="fields">
<?php foreach ($fields AS $name=>$field): ?>
    <div class="form-group row">
        
        <div class="col-sm-6">
            <input type="text" class="form-control" value="<?php echo $field['label'];?>" name="loopback[fields][<?php echo $name?>][label]" placeholder="<?php echo $name?> - column name" title="label: <?php echo $name?>"/>
            <span class="messages"></span>
        </div>
        <div class="col-sm-6">
            <input type="text" class="form-control" value="<?php echo $field['eval'];?>" name="loopback[fields][<?php echo $name?>][eval]" placeholder="<?php echo $name?> - column evaluation code" title="eval: <?php echo $name?>"/>
            <span class="messages"></span>
        </div>
        
        <div class="col-sm-6">
            <input type="text" class="form-control" value="<?php echo $field['editable'];?>" name="loopback[fields][<?php echo $name?>][editable]" placeholder="<?php echo $name?> - editable" title="editable: <?php echo $name?>"/>
            <span class="messages"></span>
        </div>
        <div class="col-sm-6">
            <input type="text" class="form-control" value="<?php echo $field['title'];?>" name="loopback[fields][<?php echo $name?>][title]" placeholder="<?php echo $name?> - title" title="title: <?php echo $name?>"/>
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
    <div class="col-sm-8">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['add']['title'];?>" name="loopback[actions][add][title]" placeholder="add button title"/>
    </div>
    <div class="col-sm-3">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['add']['init'];?>" name="loopback[actions][add][init]" placeholder="init fun before"/>
    </div>
    
    <label class="col-sm-1" style="text-align:right">
        <i class="fa fa-edit"></i>
    </label>
    <div class="col-sm-11">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['edit']['title'];?>" name="loopback[actions][edit][title]" placeholder="edit button title"/>
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
        <i class="fa fa-window-restore"></i>
    </label>
    <div class="col-sm-5">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['popup']['title'];?>" name="loopback[actions][popup][title]" placeholder="popup button title"/>
    </div>
    <div class="col-sm-4">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['popup']['url'];?>" name="loopback[actions][popup][url]" placeholder="popup url"/>
    </div>
    <label class="col-sm-1">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['popup']['width'];?>" name="loopback[actions][popup][width]" placeholder="width"/>
    </label>
    <label class="col-sm-1">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['popup']['height'];?>" name="loopback[actions][popup][height]" placeholder="height"/>
    </label>
    
    <label class="col-sm-1" style="text-align:right">
        <i class="fa fa-trash"></i>
    </label>
       <div class="col-sm-5">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['trash']['title'];?>" name="loopback[actions][trash][title]" placeholder="trash button title"/>
    </div>
    <div class="col-sm-6">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['trash']['confirm'];?>" name="loopback[actions][trash][confirm]" placeholder="trash confirmation?"/>
    </div>
    
    <label class="col-sm-1" style="text-align:right">
        <i class="fa fa-question"></i>
    </label>
       <div class="col-sm-4">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['custom']['title'];?>" name="loopback[actions][custom][title]" placeholder="custom button title"/>
    </div>
    <div class="col-sm-3">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['custom']['icon'];?>" name="loopback[actions][custom][icon]" placeholder="custom icon class"/>
    </div>
    <div class="col-sm-4">
        <input type="text" class="form-control" value="<?php echo $loopback['actions']['custom']['class'];?>" name="loopback[actions][custom][class]" placeholder="custom button class"/>
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

<div class="form-group row">   
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[customAction]">
            <option value="">Choose custom action path</option>
            <?php echo $customOptions;?>
        </select>
        <span class="messages"></span>
    </div>
</div>

<div class="form-group row">   
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[order]">
            <option value="0,asc">Default order (first column DOWN)</option>
            <option value="0,desc" <?php if ($loopback['order']=='0,desc') echo 'selected';?>>First column UP</option>
            <option value="1,asc" <?php if ($loopback['order']=='1,asc') echo 'selected';?>>Second column DOWN</option>
            <option value="1,desc" <?php if ($loopback['order']=='1,desc') echo 'selected';?>>Second column UP</option>
            <option value="2,asc" <?php if ($loopback['order']=='2,asc') echo 'selected';?>>Third column DOWN</option>
            <option value="2,desc" <?php if ($loopback['order']=='2,desc') echo 'selected';?>>Third column UP</option>
            <option value="3,asc" <?php if ($loopback['order']=='3,asc') echo 'selected';?>>Fourth column DOWN</option>
            <option value="3,desc" <?php if ($loopback['order']=='3,desc') echo 'selected';?>>Fourth column UP</option>
            <option value="4,asc" <?php if ($loopback['order']=='4,asc') echo 'selected';?>>Fifth column DOWN</option>
            <option value="4,desc" <?php if ($loopback['order']=='4,desc') echo 'selected';?>>Fifth column UP</option>
        </select>
        </select>
        <span class="messages"></span>
    </div>
</div>

<div class="row form-group">
    <div class="col-12">
        <div class="checkbox-fade fade-in-primary d-">
            <label>
                <input type="hidden" name="loopback[follow]" value="0"/>
                <input type="checkbox" name="loopback[follow]" value="1" <?php if($loopback['follow']) echo 'checked';?>>
                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                <span class="text-inverse">Pass current location</span>
            </label>
        </div>
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