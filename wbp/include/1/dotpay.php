<div class="col-md-4 col-sm-4 col-xs-4">
    <form method="post" action="https://ssl.dotpay.eu" id="form-dotpay-<?php echo $sid?>">
        <input type="hidden" name="id" value="<?php echo $dotpay_id;?>" />
        <input type="hidden" name="amount" value="<?php echo $costxt;?>" />
        <input type="hidden" name="description" value="<?php echo $this->webtd['title'];?>" />
        <input type="hidden" name="lang" value="<?php echo $lang?>" />
        <input type="hidden" name="URL" value="<?php echo $next?>" />
        <input type="hidden" name="forename" value="" />
        <input type="hidden" name="surname" value="" />
        <input type="hidden" name="email" value="" />
        <input type="hidden" name="street" value="" />
        <input type="hidden" name="street_n1" value="" />
        <input type="hidden" name="street_n2" value="" />
        <input type="hidden" name="city" value="" />
        <input type="hidden" name="postcode" value="" />
        <input type="hidden" name="phone" value="" />
        <input type="hidden" name="country" value="" />

        <button type="submit" class="btn btn-primary dotpay">
            <i class="glyphicon glyphicon-dotpay"></i>
            <span>Dotpay</span>
        </button>        
    </form>
</div>
<script>
    
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
    
    var el=document.getElementById('form-dotpay-<?php echo $sid?>').elements;
    for (var i=0;i<el.length;i++) {
        if (el[i].tagName.toLowerCase()!='input') continue;
        if (el[i].type.toLowerCase()!='hidden') continue;
        if (el[i].name=='id' || el[i].name=='amount' || el[i].name=='URL' || el[i].name=='description') continue;
        
        var parval=getParameterByName(el[i].name);
        if (parval!=null) el[i].value=parval;
    }
    
    
    <?php if ((!isset($KAMELEON_MODE) || !$KAMELEON_MODE) && $cos): ?>

    document.getElementById('form-dotpay-<?php echo $sid?>').submit();

    <?php endif; ?>

</script>
