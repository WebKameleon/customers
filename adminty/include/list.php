<?php


    /*
    
    if ($loopback['action'] && $loopback['include']) {
        
        $relations=$relationArray;
        
        
        if ($loopback['include'] && strlen($loopback['include'])) {
            foreach(explode(',',$loopback['include']) AS $inc) {
                if (!$relations[$inc]['fields'])
                    continue;
                
                foreach ($relations[$inc]['fields'] AS $f=>$field) {
                    $name=$inc.'.'.$f;
                    
                    $fields[$name]['name'] = $inc.':'.$field['name'];
                    $fields[$name]['type'] = $field['type'];
                }
            }
        }
        
    }
    */
    //mydie($relations,$loopback['include']); 
    
?>
<script>
    if (!window.list)
    window.list={};
    
    window.list[{sid}] = {
        root: '{loopbackRoot}',
        base: '{loopback.basePath}',
        action: '{loopback.action}',
        deleteAction: '{loopback.deleteAction}',
        putAction: '{loopback.putAction}',
        postAction: '{loopback.postAction}',
        next: '{next_link}',
        self: '{self_link}',
        auth: '{loopback.auth}',
        columns: <?php echo json_encode($fields);?>,
        buttons: <?php echo json_encode($loopback['actions']);?>,
        order: '{loopback.order}',
        size: <?php echo $size?:10;?>,
        include: '<?php echo $loopback['include'];?>',
        relations: <?php echo json_encode($relations);?>,
        follow: <?php echo $loopback['follow']?1:0;?>
    };
    
    
</script>
