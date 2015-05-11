<?php

    include (__DIR__.'/db.php');
    require_once (__DIR__.'/.import.php');
    
    
    $ajax_path = $session['uincludes_ajax'] . '/.import-backend.php';

    if (isset($_GET['importSpreadsheet'])) {
        
        $link=$ajax_path.='?'.http_build_query($_GET);
        
        echo $link;
    }
    
    



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
    
    
    $ajax_path=$session['uincludes_ajax'].'/.recipients-backend.php?sid='.$sid;
    
    $weblink=new weblinkModel();

    if (isset($_POST['products_menu']))
    {
        $truesid=0;
        
        foreach($_POST['products_menu'] AS $sid=>$sum)
        {
            if ($sid==0) {
                $truesid=$sum;
                continue;
            }
            if (is_array($sum)) $sum=array_sum($sum);
            $sum+=0;
            $weblink->get($sid);
            $d=unserialize(base64_decode($weblink->d_xml));
            $d['onclick']="return setProducts($sum,".($truesid==$sid?'true':'false').")";
            $weblink->d_xml=base64_encode(serialize($d));
            $weblink->description="$sum";
            $weblink->save();
        }
    }
    
    
    
    $webtd=Bootstrap::$main->tokens->webtd;
    $th='';
    $td='';
    if ($webtd['menu_id']) {
        
        $ware=include(__DIR__.'/recipient2ware.php');
        
        
        $links=$weblink->getAll($webtd['menu_id']);
        foreach ($links AS &$link)
        {
            $d=unserialize(base64_decode($link['d_xml']));
            $onclick=$d['onclick'];
            
            $th.='<th>'.$link['alt'].' <input type="radio" name="products_menu[0]" value="'.$link['sid'].'" title="'.$link['alt'].' = wszystko/all/alles/все"';
            if (strstr($onclick,'true')) $th.=' checked';
            $th.='/></th>';

            $products=preg_replace('/[^0-9]/','',$onclick);
            $boxes='<input type="hidden" name="products_menu['.$link['sid'].']" value="0"/>';
            foreach ($ware AS $pow=>$what) {
                $boxes.='<li style="font-size:8px"> <input type="checkbox"';
                if (($products & pow(2,$pow))>0) $boxes.=' checked';
                $boxes.=' name="products_menu['.$link['sid'].'][]" value="'.pow(2,$pow).'"/> '.$what.'</li>';
            }
            $td.='<td valign="top"><ul>'.$boxes.'<ul></td>';
        }
    }
    
    $table='';
    if ($th) {
        $table='<form method="post" action="'.$self.'"><table width="100%" cellspacing="3"><tr>'.$th.'</tr><tr>'.$td.'</tr></table><input type="submit" value="zapisz"/></form>';
    }
    
    
?>




<a href="javascript:importManagement_<?php echo $sid?>_toggle()">*</a>

<div id="importManagement_<?php echo $sid?>" style="display:none">

    <form id="importFileForm_<?php echo $sid?>" method="get" action="<?php echo $action?>" class="import-list-form container">
        <select id="importFileSelector_<?php echo $sid?>" name="importSpreadsheet" >
            <option>Importuj z arkusza</option>
            <option value="0">Pobierz pliki</option>
            <?php
                foreach ($imports AS $k=>$import)
                {
                    echo '<option value="'.$k.'#'.$import['title'].'">'.$import['title'].', import: '.$import['who'].', '.$import['date'].'</option>';
                }
            ?>            
        </select>
    </form>
    
    <?php echo $table?>
</div>


<script type="text/javascript">
    
    function importManagement_<?php echo $sid?>_toggle()
    {
        div=document.getElementById('importManagement_<?php echo $sid?>');
        
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
            //km_preloader_show();
            //$('#importFileForm_<?php echo $sid?>').submit();
            
            importer($('#importFileSelector_<?php echo $sid?>').val());
        }
    });
    
    
   
    
</script>


<?php

    include(__DIR__.'/.importer.php');