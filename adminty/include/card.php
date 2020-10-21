<script>
    if (!window.card)
    window.card={};
    
    window.card[{sid}] = {
        root: '{loopbackRoot}',
        base: '{loopback.basePath}',
        action: '{loopback.action}',
        next: '{next_link}',
        self: '{self_link}',
        size: '{size}',
        auth: '{loopback.auth}',
        change: '{loopback.change}',
        icon: '{loopback.icon}',
        params: <?php echo json_encode($parameters);?>
    };
</script>
