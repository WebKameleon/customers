<?php
    if (!isset($this) || !is_object($this)) return;
    
    
    if (!$this->webtd['staticinclude']) {
        $webtd= new webtdModel($this->webtd['sid']);
        $webtd->staticinclude=1;
        $webtd->save();
    }
    
    if (!$this->webtd['next']) return;
    $webpage=new webpageModel();
    $pages=$webpage->getChildren($this->webtd['next']);
    
    $art='var ARTYSCI=[];';
    foreach ($pages AS $p) $art.='ARTYSCI[ARTYSCI.length]={nazwa:"'.$p['title'].'",href:"'.Bootstrap::$main->tokens->page_href($p['id']).'"};'; 

    echo "<script>$art</script>";
?>
