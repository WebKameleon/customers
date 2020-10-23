<script>
    if (!window.chart)
    window.chart={};
    
    window.chart['google-chart-{sid}'] = {
        root: '{loopbackRoot}',
        base: '{loopback.basePath}',
        action: '{loopback.action}',
        next: '{next_link}',
        self: '{self_link}',
        size: '{size}',
        auth: '{loopback.auth}',
        packages: '{loopback.packages}',
        title: '{title}',
        options: <?php echo $loopback['options']?json_encode($loopback['options']):'[]';?>,
        series: <?php echo $loopback['series']?json_encode($loopback['series']):'[]';?>,
        params: <?php echo json_encode($parameters);?>
    };
</script>
