<?php
    
    if (!isset($KAMELEON_MODE) || $KAMELEON_MODE) $session['include_path']=$INCLUDE_PATH;
    
    $include=(isset($KAMELEON_MODE) && $KAMELEON_MODE)?$session['uincludes_ajax']:$session['include_path'];
    $ajax_exhibition=$include.'/ajax/exhibition-order.php';
    
    $configuration_file_name=md5($sid);
    $configuration=WBP::get_data($configuration_file_name);
    
    if (!isset($configuration['drive']['id'])) return;

    $wystawy_all=WBP::get_data('wystawy');
    
    $wystawy=array();
    
    foreach($wystawy_all AS $w)
    {    
        $wystawy[$w['title'].$w['id']] = array('id'=>$w['id'],'title'=>$w['title']);
        
    }
    ksort($wystawy);
    $wystawy_options='';
    
    foreach($wystawy AS $w)
    {
        $selected='';
        $wystawy_options.='<option value="'.$w['id'].'" '.$selected.'>'.$w['title'].'</option>';
    }
?>




<div class="container-fluid">
  
    <form id="exhibition-order" enctype="multipart/form-data">
                
        <input type="hidden" name="sid" value="<?php echo md5($sid);?>"/>
    
        <div class="col-lg-7">                 
                <div>
                    <label for="institution">Nazwa instytucji</label><input name="institution" type="text" class="txbx institution required" />
                </div>
                <div>
                    <label for="address">Adres</label><textarea class="txar address required" name="address"></textarea>
                </div>
                
                <div>
                    <label for="phone">Telefon</label><input name="phone" type="text" class="txbx phone required" required="1"/>
                </div> 
                <div>
                    <label for="email">E-mail</label><input name="email" type="text" class="txbx email required" required="1"/>
                </div>
                
                <div>
                    <label for="exhibition">Nazwa wystawy</label>
                    <select name="exhibition" class="required">
                        <option value="">Wybierz wystawę</option>
                        <?php echo $wystawy_options?>
                    </select>
                </div>
                
                
                <div class="header">
                    Sugerowany termin:
                </div>                
                <div>
                    <label for="since">Data od</label><input name="since" type="date" class="txbx since required" />
                </div>
                
                <div>
                    <label for="till">Data do</label><input name="till" type="date" class="txbx till required" />
                </div>                
                
                <div class="header">
                    Osoba upoważniona do odbioru wystawy:
                </div>
                
                <div>
                    <label for="receiverName">Imię</label><input name="receiverName" type="text" class="txbx receiverName" />
                </div>
                
                <div>
                    <label for="receiverSurname">Nazwisko</label><input name="receiverSurname" type="text" class="txbx receiverSurname" />
                </div>
                
                <div class="header">
                Dane zamawiającego:
                </div>
                
                <div>
                    <label for="orderName">Imię</label><input name="orderName" type="text" class="txbx orderName required" />
                </div>
                
                <div>
                    <label for="orderSurname">Nazwisko</label><input name="orderSurname" type="text" class="txbx orderSurname required" />
                </div>
                
                <div class="warning" style="display: none"></div>
                
                <input type="button" value="Zamów" class="order-button"/>
                
                
            </div>

            

    </form>
</div>



<script type="text/javascript">
  
window.onload = function() {
 
 
    var lh=location.href;
    lh=lh.split('?');
    if (typeof(lh[1])!='undefined') {
        var q=lh[1].split('&');
        for(i=0;i<q.length;i++)
        {
            var qq=q[i].split('=');
            if (qq[0]=='id') {
                $('#exhibition-order select[name="exhibition"]').val(qq[1]);    
            }
            
        }
    }
    
    
    $('#exhibition-order input,#exhibition-order textarea,#exhibition-order select').change(function () {
        $(this).removeClass('error');
        $('#exhibition-order .warning').fadeOut();
    });
    
    $('#exhibition-order .order-button').click(function() {
        
        var orderButton=$(this);
        orderButton.val('Zamawianie, proszę czekać ...').addClass('order-button-gray');
        
        $('#exhibition-order .warning').hide();
        var data=$('#exhibition-order').serialize();
        $.post('<?php echo $ajax_exhibition?>',data,function (resp) {
            orderButton.val('Zamów').removeClass('order-button-gray');
            if (resp.error!=null) {
                $('#exhibition-order .warning').html(resp.error).fadeIn();
                $('#exhibition-order .'+resp.obj).addClass('error');
            } else {
                <?php if ($next!=$self): ?>
                    location.href='<?php echo $next?>';
                <?php else: ?>
                    alert('Dziękujemy');
                <?php endif ?>
            }
        });
        
    });
    
    <?php if (isset($KAMELEON_MODE) && $KAMELEON_MODE==1):?>
    $('#exhibition-order').dblclick(function() {
        $('select').val('29610').removeClass('error');
        $('input.required').val('Testowanko').removeClass('error');
        $('textarea.required').val('Poznań').removeClass('error');
        $('input.since').val('<?php echo date('Y-m-d',time()+5*24*3600)?>').removeClass('error');
        $('input.till').val('<?php echo date('Y-m-d',time()+19*24*3600)?>').removeClass('error');
    });
    <?php endif ?>    
    

}    
</script>