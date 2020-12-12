<?php
    $id='dirve'.$sid;
    $template_dir = Bootstrap::$main->session('template_dir');
?>


<script>
    var googleDrive={
        root: '{loopbackRoot}',
        base: '{loopback.basePath}',
        getAction: '{loopback.action}',
        postAction: '{loopback.postAction}',
        buttonId: '{id}',
        auth: '{loopback.auth}'
    };
</script>

<div class="row m-t-30">
    <div class="col-md-12">
        <button id="{id}" type="button" class="submit btn btn-{loopback.button_color} btn-md btn-{loopback.button_style} waves-effect waves-light text-center m-b-20">{loopback.token}</button>
    </div>
</div>

<script type="text/javascript" src="{template_dir}/js/drive.js<?php if ($this->mode) echo '?t='.time(); ?>" defer="defer"></script>
