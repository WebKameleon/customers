<script>
    if (!window.list)
    window.list={};
    
    window.list[{sid}] = {
        root: '{loopbackRoot}',
        base: '{swagger.basePath}',
        action: '{loopback.action}',
        deleteAction: '{loopback.deleteAction}',
        putAction: '{loopback.putAction}',
        postAction: '{loopback.postAction}',
        next: '{next_link}',
        auth: '{loopback.auth}',
        columns: <?php echo json_encode($fields);?>,
        buttons: <?php echo json_encode($loopback['actions']);?>
    };
    
    
</script>
