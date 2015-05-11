<?php
    if (!isset($costxt)) $costxt='';
    
    $url=$costxt?:'http://www.gpw.pl/wyniki/a45_u__8c/n_DCR_full_data.html';

    $gpw=@json_decode(@json_encode(simplexml_load_string(file_get_contents($url))),1);
    
    
    
    
    if (isset($gpw['kurs']))
    {
        $kurs=isset($gpw['kurs'][0]) ? end($gpw['kurs']) : $gpw['kurs'];
    }
    else
    {
        $kurs=array();
        $kurs['pion']=$gpw['kurs_odniesienia'];
        $kurs['wolumen']=0;
    }
    
    
    $zmiana=$gpw['zmiana']=100*($kurs['pion']-$gpw['kurs_odniesienia'])/$gpw['kurs_odniesienia'];
    
    
    $dzis=$gpw['data'];

    if (!isset($_GET['js'])) return;
    
    
    header('Content-Type: application/javascript; charset=utf-8');
?>   
$('#gpw_kurs_odniesienia').html('<?php echo $gpw['kurs_odniesienia']?>');
$('#gpw_kurs').html('<?php echo $kurs['pion']?>');
$('#gpw_zmiana').html('<?php echo round($zmiana,2)?>');
$('#gpw_wolumen').html('<?php $kurs['wolumen']?>');    
    
    
    
<?php die(); ?>
/*    
    $sql="SELECT count(*) FROM decora_gpw WHERE rate_date='$dzis'";
    $q=$dbh->query($sql);
    $c=0;
    foreach($q AS $row) $c=$row[0];
    
    if (!$c)
    {
        $sql="DELETE FROM decora_gpw WHERE rate_date='$dzis'; INSERT INTO decora_gpw (rate_date) VALUES ('$dzis')";
        $dbh->exec($sql);
    }
    
    $sql="UPDATE decora_gpw SET
            rate_min=".$kurs['min'].",
            rate_max=".$kurs['max'].",
            rate_open=".$gpw['kurs_odniesienia'].",
            rate_close=".$kurs['pion'].",
            rate_change=$zmiana,
            volume=".$kurs['wolumen']."
            WHERE rate_date='$dzis'";
    $dbh->exec($sql);
    
*/