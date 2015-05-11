<?php

if (!class_exists ('TemplateTokens')) {

class TemplateTokens extends Tokens {

    public function logo($sitelogo='') {
        $server=new serverModel();
        $logo=$server->option('logo');
        
        
        if (!$logo) return;
        
        
        if (!file_exists(Bootstrap::$main->session('uimages_path')."/$logo")) return;
        
        list($w,$h) = getimagesize(Bootstrap::$main->session('uimages_path')."/$logo");
        $config=Bootstrap::$main->getConfig();
        
    
        
        if ($w>$config['default']['logo']['width'] || $h>$config['default']['logo']['height']) {
            $src=Bootstrap::$main->session('uimages_path')."/$logo";
            $logo="logo_".time().".jpg";
            $dst=Bootstrap::$main->session('uimages_path')."/$logo";
            Bootstrap::$main->kameleon->min_image($src,$dst,$config['default']['logo']['width'],$config['default']['logo']['height'],true);
            $server->option('logo',$logo);
        }
        
        
        return '<a href="'.$this->href('','',0).'"><img src="'.Bootstrap::$main->session('uimages')."/".$logo.'"/></a>';
    }

    

    protected function accordion() {
	$this->webtd['staticinclude']=1;
	return $this->_include_file('accordion.php');
	
    }
	
	private $odd_or_even;
    
    public function odd_or_even () {
	return $this->odd_or_even++%2?'odd':'even';
    }


    public function code_change($html,$style=null)
    {
	include_once Bootstrap::$main->session('uincludes').'/code_change.php';
	if (function_exists('code_change')) {
	    $html=code_change($html);   
	}
	
        return parent::code_change($html,$style); 
    }

 
}
}
