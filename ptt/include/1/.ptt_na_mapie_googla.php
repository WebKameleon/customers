<?php
    $webtd=new webtdModel($sid);

    $adresy=unserialize(base64_decode($webtd->web20))?:[];
    $adresy2=array();
    
    
    if (isset($_POST['ptt'][$sid]))
    {

        foreach($_POST['ptt'][$sid] AS $ptt)
        {
            $name=$ptt['name'];
            $address=$ptt['address'];
            if (!$name || !$address) continue;
            
            $adresy2[$name]=array('address'=>$address,'geo'=>null);
            
            if (isset($adresy[$name]['address']) && $address==$adresy[$name]['address'] && $adresy[$name]['geo'])
            {
                $adresy2[$name]['geo']=$adresy[$name]['geo'];
            }
            else
            {
                $url='http://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&region=PL';
                $adresy2[$name]['geo']=json_decode(file_get_contents($url),true);                
            }
        }
        
        $webtd->web20=base64_encode(serialize($adresy2));
        $webtd->staticinclude=1;
        $webtd->save();
        $adresy=$adresy2;
    }
?>
<form method="post">
    <?php foreach ($adresy AS $name=>$a): ?>
    <p>
        <input style="width:150px" name="ptt[<?php echo $sid;?>][<?php echo $name;?>][name]" value="<?php echo $name;?>" placeholder="Nazwa"/>
        <input style="width:300px" name="ptt[<?php echo $sid;?>][<?php echo $name;?>][address]" value="<?php echo $a['address'];?>" placeholder="Adres"/>
    </p>

    
    <?php endforeach; ?>
    <p>
        <input style="width:150px" name="ptt[<?php echo $sid;?>][_new][name]" placeholder="Nazwa"/>
        <input style="width:300px" name="ptt[<?php echo $sid;?>][_new][address]" placeholder="Adres"/>
    </p>
    
    <input type="submit" value="Zapisz"/>
</form>