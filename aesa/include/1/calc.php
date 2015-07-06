<?php
    if (!$costxt) return;
    
    $file='map/'.$costxt.'.json';
    
    $data=json_decode(file_get_contents(__DIR__.'/'.$file),1);
    
    $include=isset($KAMELEON_MODE) && $KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    
    //mydie($data);

?>

<div class="calc">
    <div class="calc-title">calc</div>

    <div class="col-md-3 col-sm-3">
        <ul>
            {loop:data}
            <li class="kat{__loop__} {if:__loop__=1}current{endif:__loop__=1}">
            <div class="radio"><input {if:__loop__=1}checked="checked"{endif:__loop__=1} id="optionsRadios{__loop__}" name="optionsRadios" type="radio" value="{__loop__}" /></div>
            </li>
            {endloop:data}
            
        </ul>
    </div>

<div class="col-md-9 ol-sm-9">
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <div class="row">
                <div class="col-md-5 col-sm-5 col-xs-12">
                    <select class="form-control from">
                        {loop:data.1.0}
                        {if:!first}
                        <option>{__loop__}</option>
                        {endif:!first}
                        {endloop:data.1.0}
                    </select>
                </div>

                <div class="col-md-2 col-sm-2 col-xs-12">
                    <div class="arrow">&nbsp;</div>
                </div>

                <div class="col-md-5 col-sm-5 col-xs-12">
                    <select class="form-control to">
                        {loop:data.1.0}
                        {if:!first}
                        <option>{__loop__}</option>
                        {endif:!first}
                        {endloop:data.1.0}
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-4">
            <div class="price"></div>
        </div>
    </div>
</div>

<div class="clearfix">&nbsp;</div>
</div>

<script>
    var calc_json='<?php echo $include.'/'.$file;?>';
</script>