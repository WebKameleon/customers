<?php

    if (!$costxt) return;
    require_once __DIR__.'/system/jotform.php';
    

    $c=explode(':',$costxt);
    $costxt=$c[0];
    
    $jotform=new jotform($c[1]=='wbp'?$jotform_subkey:$jotform_key);
    
    if ($this->webtd['next'])
    {
        $url=$session['server']['http_url'];
        if (substr($url,-1)!='/') $url.='/'; 
        $webpage=new webpageModel();
        $wp=$webpage->getOne($this->webtd['next']);
        $url.=$wp['file_name'];
        $url=preg_replace('/index\.(php|html|htm)$/','',$url);
        $f=$jotform->getFormProp($costxt);
        if ($f['content']['activeRedirect']!='thankurl' || $f['content']['thankurl']!=$url)
        {
            $a=$jotform->setFormProp($costxt,'properties[thankurl]='.urlencode($url).'&properties[activeRedirect]=thankurl');
        }
    }
    
    if ($cos) {
        echo $jotform->iframe($costxt);
    } else {
        $form=$jotform->getFormSource($costxt);
        if (is_array($form))
        {
            Bootstrap::$main->tokens->set_wbp_js($form['scripts']);
            echo $form['html'];
            
            echo '<link rel="stylesheet" href="'.$session['template_dir'].'/css/jotform.css"/>';
        }
    }