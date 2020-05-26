<form class="md-float-material form-material loopback" novalidate="" id="form_{sid}" rel="{loopbackRoot}|{swagger.basePath}|{loopback.action}|{next_link}|{loopback.auth}|{loopback.init_action}|{loopback.success_action}">
   
    {loop:parameters}
    {if:label}
    <div class="form-group form-primary">
        {if:type=string}
        <input type="{if:password}password{endif:password}{if:!password}text{endif:!password}" name="{name}" class="form-control" {if:require}require="{require}"{endif:require} placeholder="{label}"/>
        <span class="messages"></span>
        {endif:type=string}
        
        {if:type=boolean}
        
        <div class="checkbox-fade fade-in-primary d-">
            <label>
                <input type="hidden" name="{name}" value="0"/>
                <input type="checkbox" name="{name}" value="1"/>
                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                <span class="text-inverse">{label}</span>
            </label>
          
        </div>
        <span class="messages"></span>

        {endif:type=boolean}
    </div>
    {endif:label}
    {endloop:parameters}

    
    <div class="row m-t-30">
        <div class="col-md-12">
            <button type="button" class="submit btn btn-{loopback.button_color} btn-md btn-{loopback.button_style} waves-effect waves-light text-center m-b-20">{loopback.submit}</button>
        </div>
    </div>

</form>