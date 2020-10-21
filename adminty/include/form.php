<?php
    
    foreach($parameters AS &$parameter) {
        if ($parameter['fieldType']=='range' && $parameter['rangeLabels']) {
            $labels=explode(',',trim($parameter['rangeLabels']));
            $parameter['rangeLabels']='"'.implode('","',$labels).'"';
        }
    }
    
?>

<form class="md-float-material form-material loopback" novalidate="" id="form_{sid}" rel="{loopbackRoot}|{loopback.basePath}|{loopback.action}|{next_link}|{loopback.auth}|{loopback.init_action}|{loopback.success_action}|{loopback.initAction}|{loopback.addValueToNext}">
    
    {loop:parameters}
    {if:label}
    <div class="form-group form-primary{if:!loopback.card} row{endif:!loopback.card}">
        
        {if:loopback.card}
        <div class="col-md-12">
        {endif:loopback.card}
        
        {if:!loopback.card}
        <label class="col-md-3 col-form-label">{label}</label>
        <div class="col-md-9">
        {endif:!loopback.card}

            {if:fieldType=text}
            <input type="{fieldType}" name="{name}" class="form-control{if:loopback.round} form-control-round{endif:loopback.round}" {if:require}require="{require}"{endif:require} placeholder="{label}" title="{label}"/>
            {endif:fieldType=text}
            
            {if:fieldType=password}
            <input type="{fieldType}" name="{name}" class="form-control{if:loopback.round} form-control-round{endif:loopback.round}" {if:require}require="{require}"{endif:require} placeholder="{label}" title="{label}"/>
            {endif:fieldType=password}
            
            {if:fieldType=select}
            <select class="select2 col-sm-12 loopback-form-select" name="{name}" title="{label}" rel="{select}|{selectLabel}|{selectValue}">
                <option value="">{label}</option>
            </select>
            {endif:fieldType=select}
           
            {if:fieldType=hidden}
            <input type="{fieldType}" name="{name}"/>
            {endif:fieldType=hidden}
            
            
            {if:type=boolean}
            <div class="checkbox-fade fade-in-primary">
                <label>
                    <input type="hidden" name="{name}" rel="checkbox" value="0"/>
                    <input type="checkbox" name="{name}" value="1"/>
                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                    {if:loopback.card}<span class="text-inverse">{label}</span>{endif:loopback.card}
                </label>
            </div>
            {endif:type=boolean}
            
            {if:fieldType=range}
            <div class="range-slider">
                <input name="{name}" type="text" class="range-slider" style="display:none" data-slider-min="{sliderMin}" data-slider-max="{sliderMax}"  value="{sliderValue}" data-slider-step="{sliderStep}">
            </div>
            {endif:fieldType=range}
            
            {if:type=object}
            <textarea name="{name}" class="form-control{if:loopback.round} form-control-round{endif:loopback.round}" {if:require}require="{require}"{endif:require} placeholder="{label}" title="{label}"></textarea>
            {endif:type=object}
        
        </div>
        
        <span class="messages"></span>
    </div>
    {endif:label}
    {endloop:parameters}

    
    <div class="row m-t-30">
        <div class="col-md-12"{if:loopback.round} style="padding: 0 30px;"{endif:loopback.round}>
            {if:loopback.return}
            <button type="button" class="return right btn btn-{loopback.return_button_color} btn-md btn-{loopback.return_button_style} waves-effect waves-light text-center m-b-20">{loopback.return}</button>
            {endif:loopback.return}
            <button type="button" class="submit btn btn-{loopback.button_color} btn-md btn-{loopback.button_style} waves-effect waves-light text-center m-b-20">{loopback.submit}</button>
        </div>
    </div>

</form>