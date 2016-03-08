<?php

    $weblink=new weblinkModel();
    
    if(isset($_GET['marker']['sid'])) {
    
        $weblink=new weblinkModel($_GET['marker']['sid']);
        $weblink->titlea=$_GET['marker']['lat'];
        $weblink->titleb=$_GET['marker']['lng'];
        $weblink->save();
        die();
    }
    
    if(isset($_GET['map']['sid'])) {
        $webtd=new webtdModel($sid);
        $webtd->costxt = $_GET['map']['lat'].','.$_GET['map']['lng'].','.$_GET['map']['zoom'];
        $webtd->save();
        die();
    }    
    
    if (!$this->webtd['staticinclude'] || !$this->webtd['menu_id']) {
        $webtd=new webtdModel($this->webtd['sid']);
        $webtd->staticinclude=1;
        if (!$webtd->menu_id) {
            
            $webtd->menu_id=$this->webtd['menu_id']=$weblink->get_new_menu_id();
        }
        $webtd->save();
    }
    
    $wlinks=$weblink->getAll($this->webtd['menu_id'],0);
    
    $items='<ul class="marker-add">';
    
    foreach ($wlinks AS $lp=>$l) {

        if (!$l['titlea']) {
            $items.='<li><a href="javascript:" onclick="addMarker2Map(this,'.$lp.')"><img title="'.$l['alt'].'" src="'.Bootstrap::$main->session('uimages').'/'.$l['img'].'"></a></li>';
        }
    }
    
    $items.='</ul>';
    

    echo $items;
    include __DIR__ .'/map.php';
    
    
?>

<script>
    function addMarker2Map(a,i) {
        var mcenter=map.getCenter();
        add_marker(i,mcenter.lat(),mcenter.lng());
       
        $(a).parent().fadeOut(500);
    }
    
    var markerDragFun = function (lat,lng,sid) {
        $.get('<?php echo $self; ?>&marker[lat]='+lat+'&marker[lng]='+lng+'&marker[sid]='+sid);
    }
    
    var mapIdle = function(lat,lng,z) {
        $.get('<?php echo $self; ?>&map[zoom]='+z+'&map[lat]='+lat+'&map[lng]='+lng+'&map[sid]=<?php echo $sid;?>');        
    }
    
</script>