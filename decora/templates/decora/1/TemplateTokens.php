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

    
	//private $naglowek_h1=false;
	protected function box_title() {
		if ($this->webtd['level']==1 && !$this->naglowek_h1 && $this->webtd['page_id']>0) {
                        $this->naglowek_h1 = true;
                        return '<h1 class="title">'.$this->webtd['title'].'</h1>';
                }                
		return '<h3 class="title">'.$this->webtd['title'].'</h3>';
	}
    

    // protected function accordion() {
// 	
	// if ($this->mode == 2) {
	    // $this->webtd['html']='accordion.php';
	    // return $this->_include_file();
	// }
    // }
    
        protected function accordion() {
	return $this->_include_file('accordion.php');
	
    }

    
    private $odd_or_even;
    
    public function odd_or_even () {
	return $this->odd_or_even++%2?'odd':'even';
    }
    
    
    public function reset()
    {
        parent::reset();
	$this->odd_or_even=0;
    }
    
    
    
    public function code_change($html,$style=null)
    {
	include_once __DIR__.'/../../../include/1/code_change.php';
	if (function_exists('code_change')) {
	    $html=code_change($html);   
	}
	
        return parent::code_change($html,$style); 
    }
    
}
}
