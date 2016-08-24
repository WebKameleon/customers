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

<?php if ((!isset($KAMELEON_MODE) || !$KAMELEON_MODE) && $cos): ?>

<script>
    document.getElementById('form-dotpay-<?php echo $sid?>').submit();
</script>

<?php endif; ?>
