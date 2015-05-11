<?php

    if (!is_object($pagekey)) $pagekey=json_decode($pagekey);
    $products=0+$pagekey->pr;

?>

<script type="text/javascript">
    $('a[href="<?php echo $next;?>"]').each (function () {
        this.href+='<?php echo $next_sign;?>products=<?php echo $products?>';
    });
</script>