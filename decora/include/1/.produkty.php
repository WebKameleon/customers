<?php

    include_once (__DIR__.'/db.php');

    
    if (!isset($_SERVER['imports']))
    {
        $imports=array();
        
        $sql="SELECT fileid,max(date) AS date FROM decora_imports GROUP BY fileid ORDER BY max(date) DESC";
        $q=$dbh->query($sql);
        if ($q) foreach ($q AS $row ){
            $sql="SELECT title,username FROM decora_imports WHERE fileid='".$row['fileid']."' AND date='".$row['date']."'";
            $q2=$dbh->query($sql);
        
            if ($q2) foreach ($q2 AS $row2 ){
            }

            $user=new userModel($row2['username']);
            
            if (!$row2['title']) $row2['title']=$row['fileid'];
            $imports[$row['fileid']]=array('date'=>$row['date'],'who'=>$user->fullname,'title'=>$row2['title']);
        }
        
        $_SERVER['imports'] = $imports;
        
    }
    $imports = $_SERVER['imports'];
    $imports_json=json_encode($imports);
    
    $url=Bootstrap::$main->getRoot().'ajax/gdrive_files?q='.urlencode("mimeType='application/vnd.google-apps.spreadsheet'");
    $action=Bootstrap::$main->getRoot().'index/get/'.$page;
    
    
    $ajax_path=$session['uincludes_ajax'].'/.produkty-backend.php?sid='.$sid;
    

?>



<a href="javascript:productManagement_<?php echo $sid?>_toggle()">*</a>

<div id="productManagement_<?php echo $sid?>" style="display:none">
    <div id="productSelectors_<?php echo $sid?>" class="product-list-selector container">
        <select id="productSelectorsVendor_<?php echo $sid?>">
            <option value="">Wybierz producenta</option>
        </select>
        <select id="productSelectorsProduct_<?php echo $sid?>">
            <option value="">Wszystkie linie produktowe</option>
        </select>
        <select id="productSelectorsCollection_<?php echo $sid?>">
            <option value="">Wszystkie kolekcje</option>
        </select>
    </div>
    
    
    
    <form id="importFileForm_<?php echo $sid?>" method="get" action="<?php echo $action?>" class="product-list-form container">
    <select id="importFileSelector_<?php echo $sid?>" name="importSpreadsheet" >
        <option>Importuj z arkusza</option>
        <option value='0'>Pobierz pliki</option>
        <?php
            foreach ($imports AS $k=>$import)
            {
                echo '<option value="'.$k.'#'.$import['title'].'">'.$import['title'].', import: '.$import['who'].', '.$import['date'].'</option>';
            }
        ?>
    </select>
    </form>
    
    
    <div class="product-list-form container">
            <input id="cosCheckbox_<?php echo $sid?>" type="checkbox" <?php if ($cos) echo 'checked';?> /> Ukryj pliki html
    </div>

</div>





<script type="text/javascript">
    
    function productManagement_<?php echo $sid?>_toggle()
    {
        div=document.getElementById('productManagement_<?php echo $sid?>');
        
        div.style.display=div.style.display=='none'?'block':'none';
    }
    
    
    var imports=JSON.parse('<?php echo $imports_json?>');
    

 
    $('#importFileSelector_<?php echo $sid?>').on('change', function () {

        if (this.value=='0') {
            km_preloader_show();
            $.getJSON('<?php echo $url?>',function (data) {
                              
                $("#importFileSelector_<?php echo $sid?>").find('option:not(:first)').remove();
                $("#importFileSelector_<?php echo $sid?>").find('option:first').text('Wybierz arkusz');
                $.each(data.items,function (i,item){
                    title=item.title;
                    cl='normal';
                    if (typeof imports[item.id]!='undefined') {
                        title+=' [zaimportował(a) '+imports[item.id].who+' '+imports[item.id].date+']';
                        dataImportu = new Date(imports[item.id].date);
                        dataModyfikacji= new Date(item.modifiedDate);
                        cl='imported';
                        if (dataModyfikacji>dataImportu) cl='modified';
                        
                    }
                    
                    var value=item.id+'#'+item.title;
                    $("#importFileSelector_<?php echo $sid?>").append('<option class="'+cl+'" value="'+value+'">'+title+'</option>');
                });
                km_preloader_hide();
                
            });
        } else {
            /*
            km_preloader_show();
            setTimeout(function() {
                $('#importFileForm_<?php echo $sid?>').submit();
            },500);
            */
            
            importer($('#importFileSelector_<?php echo $sid?>').val());
            
        }
    });
    
    $('#cosCheckbox_<?php echo $sid?>').on('click',function() {
        var url = '<?php echo $ajax_path?>&cos='+(this.checked?1:0);
        $.getJSON(url,function (data) {
        });
        
    });

    
    
    function productSelectors_<?php echo $sid?>(readfromdb)
    {
        var url = '<?php echo $ajax_path?>&' + $.param({
            vendor : $('#productSelectorsVendor_<?php echo $sid?>').val(),
            product : $('#productSelectorsProduct_<?php echo $sid?>').val(),
            collection : $('#productSelectorsCollection_<?php echo $sid?>').val(),
            readfromdb : readfromdb === true ? 1 : 0
        });
        
        $.getJSON(url, function (data) {
            $('#productSelectorsVendor_<?php echo $sid?> option:not(:first)').remove();
            $.each(data.vendors,function (k,v){
                $('#productSelectorsVendor_<?php echo $sid?>').append('<option value="'+v+'">'+v+'</option>');
            });
            $('#productSelectorsVendor_<?php echo $sid?> option[value="'+data.selected_vendor+'"]').attr('selected','selected');

            $('#productSelectorsProduct_<?php echo $sid?> option:not(:first)').remove();
            $.each(data.products,function (k,v){
                $('#productSelectorsProduct_<?php echo $sid?>').append('<option value="'+v+'">'+v+'</option>');
            });
            $('#productSelectorsProduct_<?php echo $sid?> option[value="'+data.selected_product+'"]').attr('selected','selected');

            $('#productSelectorsCollection_<?php echo $sid?> option:not(:first)').remove();
            $.each(data.collections,function (k,v){
                $('#productSelectorsCollection_<?php echo $sid?>').append('<option value="'+v+'">'+v+'</option>');
            });
            $('#productSelectorsCollection_<?php echo $sid?> option[value="'+data.selected_collection+'"]').attr('selected','selected');
        });
        
    }
    
    $(function(){
    
        $('#productSelectorsVendor_<?php echo $sid?>').on('change', productSelectors_<?php echo $sid?>);
        $('#productSelectorsProduct_<?php echo $sid?>').on('change', productSelectors_<?php echo $sid?>);
        $('#productSelectorsCollection_<?php echo $sid?>').on('change', productSelectors_<?php echo $sid?>);
    
        productSelectors_<?php echo $sid?>(true);   
    });

    
    
</script>




<?php

    include(__DIR__.'/.importer.php');