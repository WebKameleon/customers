<?php


    $ware=include(__DIR__.'/recipient2ware.php');
    
    $webpage=new webpageModel($this->webpage['sid']);
    $pagekey=json_decode($webpage->pagekey);
    if (!is_object($pagekey)) $pagekey=new stdClass();
    

    
    if (isset($_POST['products'])) {
        $products=array_sum($_POST['products']);
    
        $pagekey->pr=$products;
        $webpage->pagekey=json_encode($pagekey);
        $webpage->save();        

    }
    
    
    if (!$pagekey->pr && $webpage->id && $webpage->prev) {
        $webpage2=new webpageModel();
        $parent=$webpage2->getOne($webpage->prev);
        $ppagekey=json_decode($parent['pagekey']);
        if (is_object($ppagekey) && isset($ppagekey->pr))
        {
            $pagekey->pr=$ppagekey->pr;
            $webpage->pagekey=json_encode($pagekey);
            $webpage->save();             
        }
    }
    
    
    
    $products=$pagekey->pr;
    
    $boxes='';
    foreach ($ware AS $pow=>$what) {
        $boxes.='<li style="font-size:8px"> <input type="checkbox"';
        if (($products & pow(2,$pow))>0) $boxes.=' checked';
        $boxes.=' name="products[]" value="'.pow(2,$pow).'"/> '.$what.'</li>';
    }
    
?>
<a href="javascript:wheretobuyFormManagement_toggle()">*</a>

<form method="post" id="wheretobuyForm" style="display: none">
    <ul>
        <?php echo $boxes?>
    </ul>
    <input type="hidden" name="products[]" value="0"/>
    <input type="submit" value="zapisz"/>
</form>
<script type="text/javascript">
    
    function wheretobuyFormManagement_toggle()
    {
        div=document.getElementById('wheretobuyForm');
        
        div.style.display=div.style.display=='none'?'block':'none';
    }
    
</script>


