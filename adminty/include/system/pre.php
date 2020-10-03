<?php
    if ($this->webtd['page_id']<0 && $this->mode==2)
        return;
    include_once __DIR__.'/fun.php';
    
    $loopbackRoot=loopbackRootUrl($this->webpage);
    
    if (isset($_POST['loopbackRootUrl']) && strlen($_POST['loopbackRootUrl'])) {
        $loopbackRoot=loopbackRootUrl($this->webpage,$_POST['loopbackRootUrl']);
    }
    
    $loopback=[];$parameters=[]; $fields=[];
    $loopbackOptions='';$lastGroup='';
    $swagger=null;
    $swaggerSummary=null;
    
    if ($loopbackRoot && $this->mode>1) {
        $swagger=json_decode(file_get_contents($loopbackRoot.'explorer/swagger.json'),true);
    }
    
    
    
    if (isset($_POST['loopback']) && isset($_POST['loopbackSid']) && $_POST['loopbackSid']==$this->webtd['sid'] ) {
        $loopback=$_POST['loopback'];
        
        $swaggerSummary=swaggerSummary($swagger,$loopback);
        $parameters = $loopback['parameters'] = $swaggerSummary['parameters'];
        $fields = $loopback['fields'] = $swaggerSummary['fields'];
        $loopback['basePath'] = $swagger['basePath'];
        getRelations($loopbackRoot, $swagger, $loopback);
        
        $costxt=base64_encode(json_encode($loopback));
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->costxt=$costxt;
        $webtd->ob=3;
        $webtd->swfstyle = $loopback['card'];
        $webtd->save();
    } elseif ($costxt) {
        $loopback=json_decode(base64_decode($costxt),true);
        $parameters = $loopback['parameters'] ;
        $fields = $loopback['fields'];
        if ($swagger) {
            $swaggerSummary=swaggerSummary($swagger,$loopback);
            getRelations($loopbackRoot, $swagger, $loopback);
            
            $parameters = $loopback['parameters'] = $swaggerSummary['parameters'];
            $fields = $loopback['fields'] = $swaggerSummary['fields'];
        }
    }
    
    if (count($loopback)) {
        $include=$loopback['includes'];
        $relations=$loopback['relations'];
    }
    
    if ($swaggerSummary)
        $loopbackOptions=$swaggerSummary['loopbackOptions'];
    
        
    if ($swagger) {
        //list
        $sw=swagger($swagger,isset($loopback['putAction'])?$loopback['putAction']:null,[],[]);
        $putOptions=$sw['loopbackOptions'];
        $sw=swagger($swagger,isset($loopback['deleteAction'])?$loopback['deleteAction']:null,[],[]);
        $deleteOptions=$sw['loopbackOptions'];
        $sw=swagger($swagger,isset($loopback['postAction'])?$loopback['postAction']:null,[],[]);
        $postOptions=$sw['loopbackOptions'];
        
        //form
        $sw=swagger($swagger,isset($loopback['initAction'])?$loopback['initAction']:null,[],[]);
        $initOptions=$sw['loopbackOptions'];
    }


?>
<?php if ($this->mode >1) :?>

<form action="<?php echo $self_link;?>" method="post" id="km_form_<?php echo $sid?>" class="md-float-material form-material" novalidate="">
<input type="hidden" name="loopbackSid" value="<?php echo $sid;?>"/>
<div class="form-group row">
    <div class="col-sm-12">
        <input type="text" class="form-control" value="" name="loopbackRootUrl" placeholder="<?php echo isset($this->webpage) && $this->webpage['pagekey']?$this->webpage['pagekey']:'Change Loopback root url'?>">
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

