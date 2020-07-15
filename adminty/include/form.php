<form class="md-float-material form-material loopback" novalidate="" id="form_{sid}" rel="{loopbackRoot}|{swagger.basePath}|{loopback.action}|{next_link}|{loopback.auth}|{loopback.init_action}|{loopback.success_action}|{loopback.initAction}">
   
    {loop:parameters}
    {if:label}
    <div class="form-group form-primary">
      
        {if:fieldType=text}
        <input type="{fieldType}" name="{name}" class="form-control" {if:require}require="{require}"{endif:require} placeholder="{label}" title="{label}"/>
        {endif:fieldType=text}
        
        {if:fieldType=password}
        <input type="{fieldType}" name="{name}" class="form-control" {if:require}require="{require}"{endif:require} placeholder="{label}" title="{label}"/>
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
                <span class="text-inverse">{label}</span>
            </label>
          
        </div>
        
        {endif:type=boolean}
        
        <span class="messages"></span>
    </div>
    {endif:label}
    {endloop:parameters}

    
    <div class="row m-t-30">
        <div class="col-md-12">
            <button type="button" class="submit btn btn-{loopback.button_color} btn-md btn-{loopback.button_style} waves-effect waves-light text-center m-b-20">{loopback.submit}</button>
        </div>
    </div>

</form>