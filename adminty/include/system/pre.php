<?php
    include_once __DIR__.'/fun.php';
    
    $loopbackRoot=loopbackRootUrl($this->webpage);
    
    if (isset($_POST['loopbackRootUrl']) && strlen($_POST['loopbackRootUrl'])) {
        $loopbackRoot=loopbackRootUrl($this->webpage,$_POST['loopbackRootUrl']);
    }
    if (isset($_POST['loopback'])) {
        $costxt=base64_encode(json_encode($_POST['loopback']));
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->costxt=$costxt;
        $webtd->save();
    }
    
    
    
    $loopbackOptions='';
    $lastGroup='';
    
    $loopback=[];
    
    if ($costxt) {
        $loopback=json_decode(base64_decode($costxt),true);
    }
    
    $parameters=[]; $fields=[];
    if ($loopbackRoot) {
        $swagger=json_decode(file_get_contents($loopbackRoot.'explorer/swagger.json'),true);
        
        $sw=swagger($swagger,isset($loopback['action'])?$loopback['action']:null,isset($loopback['parameters'])?$loopback['parameters']:[],isset($loopback['fields'])?$loopback['fields']:[]);
        $loopbackOptions=$sw['loopbackOptions'];
        $parameters = $sw['parameters'];
        $fields = $sw['fields'];
    }


?>
<?php if ($this->mode >1) :?>

<form action="<?php echo $self_link;?>" method="post" id="km_form_<?php echo $sid?>" class="md-float-material form-material" novalidate="">
<div class="form-group row">
    <div class="col-sm-12">
        <input type="text" class="form-control" value="" name="loopbackRootUrl" placeholder="<?php echo strlen($loopbackRoot)?$loopbackRoot:'Loopback root url'?>">
        <span class="messages"></span>
    </div>
</div>
<div class="form-group row">   
    <div class="col-sm-12">
        <select class="select2 col-sm-12" name="loopback[action]">
            <option value="">Choose path</option>
            <?php echo $loopbackOptions;?>
        </select>
        <span class="messages"></span>
    </div>
</div>


<?php endif;?>

