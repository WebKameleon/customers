<?php

    $info_origin=$this->webpage['pagekey'];
    $webtd=new webtdModel();
    
    $tds=$webtd->getAll(array($page));
    
    $updated=0;
    $created=0;
    $autor='';
    foreach($tds AS $td)
    {
        if ($td['sid']==$sid) continue;
        $updated=max($updated,$td['nd_update']);
        $updated=max($updated,$td['nd_create']);
        $created=max($created,$td['nd_custom_date']);
    
        if ($updated==$td['nd_update'] || $updated==$td['nd_create'])
        {
            $autor=$td['autor_update']?:$td['autor'];
        }
    }
    
    $user=new userModel($autor);

?>



<div class="bip">
    <div class="source">
        <label>Źródło informacji:</label>
        <?php echo $info_origin; ?>
    </div>
    <div class="t_create">
        <label>Data powstania informacji:</label>
        <?php echo date('d.m.Y',$created); ?>
    </div>

    <div class="t_update">
        <label>Data zmiany informacji:</label>
        <?php echo date('d.m.Y',$updated); ?>
    </div>    

    <div class="a_update">
        <label>Osoba odpowiedzialna za zmianę informacji:</label>
        <?php echo $user->fullname; ?>
    </div>    

    
</div>