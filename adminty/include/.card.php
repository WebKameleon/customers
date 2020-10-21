<?php
    include(__DIR__.'/system/parameters.php');
?>

<div class="form-group row">
        
    <div class="col-sm-8">
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['change'];?>"
               name="loopback[change]"
               placeholder="post-eval change action"
               title="post-eval change action"/>
        <span class="messages"></span>
    </div>
    <div class="col-sm-4">
        <a style="position: absolute; left: 20px; top: 6px; font-size: 14px;" href="https://feathericons.com/" target="_blank">icon-</a>
        <input type="text"
               class="form-control"
               value="<?php echo $loopback['icon'];?>"
               name="loopback[icon]"
               placeholder="icon"
               title="icon"
               style="padding-left: 38px;"
        />
        <span class="messages"></span>
    </div>
        
        
</div>
