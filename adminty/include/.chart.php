<?php
    include(__DIR__.'/system/parameters.php');
?>

<div class="form-group row">
    <label class="col-sm-3 col-form-label">
        Packages:
    </label>
    <div class="col-sm-9">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['packages'];?>"
               name="loopback[packages]"
               placeholder="packages, eg: corechart,bar"
               />
        <span class="messages"></span>
    </div>
         
</div>


<div class="form-group row">
    <label class="col-sm-3 col-form-label">
        Chart title:
    </label>
    <div class="col-sm-9">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['options']['title'];?>"
               name="loopback[options][title]"
               placeholder="title"
               />
        <span class="messages"></span>
    </div>
    <label class="col-sm-3 col-form-label">
        Area width:
    </label>
    <div class="col-sm-9">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['options']['chartArea']['width'];?>"
               name="loopback[options][chartArea][width]"
               placeholder="50%"
               />
        <span class="messages"></span>
    </div>
    <label class="col-sm-3 col-form-label">
        Is stacked:
    </label>
    <div class="col-sm-9">
        <div class="checkbox-fade fade-in-primary d-">
            <label>
                <input type="checkbox" name="loopback[options][isStacked]" value="true" <?php if($loopback['options']['isStacked']) echo 'checked';?>>
                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                <span class="text-inverse">stacked</span>
            </label>
        </div>
    </div>
    
    <label class="col-sm-3 col-form-label">
        hAxis minValue:
    </label>
    <div class="col-sm-9">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['options']['hAxis']['minValue'];?>"
               name="loopback[options][hAxis][minValue]"
               placeholder="e.g. 0"
               />
        <span class="messages"></span>
    </div>
    <label class="col-sm-3 col-form-label">
        hAxis title:
    </label>
    <div class="col-sm-9">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['options']['hAxis']['title'];?>"
               name="loopback[options][hAxis][title]"
               placeholder="title"
               />
        <span class="messages"></span>
    </div>
    <label class="col-sm-3 col-form-label">
        vAxis title:
    </label>
    <div class="col-sm-9">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['options']['vAxis']['title'];?>"
               name="loopback[options][vAxis][title]"
               placeholder="title"
               />
        <span class="messages"></span>
    </div>
        
</div>


<div class="form-group row">
    <label class="col-sm-3 col-form-label">
        Series 1:
    </label>
    <div class="col-sm-3">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['series'][0]['label'];?>"
               name="loopback[series][0][label]"
               placeholder="title of series"
               />
        <span class="messages"></span>
    </div>
    <div class="col-sm-3">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['series'][0]['vValue'];?>"
               name="loopback[series][0][vValue]"
               placeholder="vValue using {variable}"
               />
        <span class="messages"></span>
    </div>
    <div class="col-sm-3">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['series'][0]['hValue'];?>"
               name="loopback[series][0][hValue]"
               placeholder="hValue - key"
               />
        <span class="messages"></span>
    </div>
    
    
    <label class="col-sm-3 col-form-label">
        Series 2:
    </label>
    <div class="col-sm-3">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['series'][1]['label'];?>"
               name="loopback[series][1][label]"
               placeholder="title of series"
               />
        <span class="messages"></span>
    </div>
    <div class="col-sm-3">
       
    </div>
    <div class="col-sm-3">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['series'][1]['hValue'];?>"
               name="loopback[series][1][hValue]"
               placeholder="hValue - key"
               />
        <span class="messages"></span>
    </div>
    
    <label class="col-sm-3 col-form-label">
        Series 3:
    </label>
    <div class="col-sm-3">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['series'][2]['label'];?>"
               name="loopback[series][2][label]"
               placeholder="title of series"
               />
        <span class="messages"></span>
    </div>
    <div class="col-sm-3">
        
    </div>
    <div class="col-sm-3">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['series'][2]['hValue'];?>"
               name="loopback[series][2][hValue]"
               placeholder="hValue - key"
               />
        <span class="messages"></span>
    </div>
         
</div>
