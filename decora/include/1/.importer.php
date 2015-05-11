<?php
    if (isset($_SERVER['km_decora_import_dialog'])) return;
    $_SERVER['km_decora_import_dialog'] = true;
?>

<div id="km_decora_import_dialog" style="display: none">
    <iframe id="km_decora_import_iframe" style="width: 95%; height: 600px"></iframe>
</div>
<script>

    var dialog_importer = $("#km_decora_import_dialog");;

    function importer(id)
    {
        id=encodeURIComponent(id);
    
        if (dialog_importer.hasClass("km_decora_import_dialog") == false) {
            dialog_importer.dialog({
                autoOpen : false,
                modal: true,
                width: 800,
                height: 700,
                title : "Import"
               
            }).addClass("km_decora_import_dialog");

        }
        dialog_importer.dialog("open");

        $("#km_decora_import_iframe").attr('src','<?php echo $session['uincludes_ajax'] . '/.import-backend.php'?>?importSpreadsheet='+id);
    }
    
    
    function importer_close()
    {
        dialog_importer.removeClass("km_decora_import_dialog");
        dialog_importer.dialog("close");
    }
    
    
</script>
