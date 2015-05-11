<?php

    ini_set('display_errors',true);

    $user=Bootstrap::$main->session('user');
    $tokens=json_decode($user['access_token'],true);
    $scopes=array('drive','spreadsheets');
    
    foreach ($scopes AS $scope)
        if (!isset($tokens[$scope]) || !$tokens[$scope])
            Bootstrap::$main->redirect('scopes/'.$scope.'?setreferpage='.$page);


    include __DIR__.'/../system/db.php';
    
    
    if (!isset($_SERVER['imports']))
    {
        $imports=array();
        
        $sql="SELECT fileid,max(date) AS date FROM wbp_imports GROUP BY fileid ORDER BY max(date) DESC";
        $q=$dbh->query($sql);
        if ($q) foreach ($q AS $row ){
            $sql="SELECT title,username FROM wbp_imports WHERE fileid='".$row['fileid']."' AND date='".$row['date']."'";
            $q2=$dbh->query($sql);
        
            if ($q2) foreach ($q2 AS $row2 ){
            }

            $user=new userModel($row2['username']);
            
            if (!$row2['title']) $row2['title']=$row['fileid'];
            $imports[$row['fileid']]=array('date'=>substr($row['date'],0,16),'who'=>$user->fullname,'title'=>$row2['title']);
        }
        
        $_SERVER['imports'] = $imports;
        
    }
    
    $imports = $_SERVER['imports'];
    $imports_json=json_encode($imports);
    
    $url=Bootstrap::$main->getRoot().'ajax/gdrive_files?q='.urlencode("mimeType='application/vnd.google-apps.spreadsheet'");
    $action=Bootstrap::$main->getRoot().'index/get/'.$page;
    
    
   

?>

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

    <div id="km_wbp_import_dialog" style="display: none">
    <iframe id="km_wbp_import_iframe" style="width: 95%; height: 600px"></iframe>
    </div>    
    
<script type="text/javascript">
    var imports=JSON.parse('<?php echo $imports_json?>');
    

    window.onload = function () {
        
        if (typeof $.ui == "undefined") {
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.src = "//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js";
            document.getElementsByTagName("head")[0].appendChild(script);
        }        
        
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
               
                importer($('#importFileSelector_<?php echo $sid?>').val());
                
            }
        });
    }
    
    function importer(id)
    {
        var dialog_importer = $("#km_wbp_import_dialog");
        
        id=encodeURIComponent(id);
    
        if (dialog_importer.hasClass("km_wbp_import_dialog") == false) {
            dialog_importer.dialog({
                autoOpen : false,
                modal: true,
                width: 800,
                height: 700,
                title : "Import"
               
            }).addClass("km_wbp_import_dialog");

        }
        dialog_importer.dialog("open");

        $("#km_wbp_import_iframe").attr('src','<?php echo $session['uincludes_ajax'] . '/admin/.import-backend.php'?>?importSpreadsheet='+id);
    }
    
    
    function importer_close()
    {
        var dialog_importer = $("#km_wbp_import_dialog");
        
        dialog_importer.removeClass("km_wbp_import_dialog");
        dialog_importer.dialog("close");
    }
</script>

