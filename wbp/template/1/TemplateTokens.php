<?php

if (!class_exists ('TemplateTokens')) {
class TemplateTokens extends Tokens {
    
    protected $wbp_pageclass_cache;
    
    public function wbp_pageclass()
    {
        $wp=$this->webpage;
        if (isset($this->wbp_pageclass_cache[$wp['id']])) return $this->wbp_pageclass_cache[$wp['id']];
        
        if ($wp['class'])
        {
            $this->wbp_pageclass_cache[$wp['id']]=$wp['class'];
            return $wp['class'];
        }
        $webpage=new webpageModel();
        
        while (true)
        {
            if (!$wp['id']) return;
            $wp=$webpage->getOne($wp['prev']);
            if ($wp['class'])
            {
                $this->wbp_pageclass_cache[$wp['id']]=$wp['class'];
                return $wp['class'];
            }
        }
        
    }
    
    public function reset()
    {
        parent::reset();
        $this->wbp_js='';
    }
    
    public $wbp_js='';
    public function set_wbp_js($js)
    {
        foreach($js AS $f)
        {
            if (strstr($f,"\n")) {
                $this->wbp_js.="\n\t".'<script type="text/javascript">'."\n$f\n".'</script>';
            } else {
                if (substr($f,0,2)!='//' && substr($f,0,7)!='http://' && substr($f,0,8)!='https://')
                {
                    $f=Bootstrap::$main->session('template_dir').'/js/'.$f;
                }
                $this->wbp_js.="\n\t".'<script type="text/javascript" src="'.$f.'"></script>';
            }
        }
    }
    
    
    public $_counter;
    public function wbp_counter($i=-1)
    {
        if ($i==-1) return $this->_counter;
        
        if ($i==0) $this->_counter=array();
        if ($i==1) $this->_counter[]=count($this->_counter)+1; 
        
        
        
    }



    public function code_change($html,$style=null)
    {
        if ($this->webpage['type']==21 || $this->webpage['type']==22)
        {
            $html=preg_replace('/[\r\n]/','',Html::strip($html));
            
            
            
            while ($pos=strpos(strtolower($html),'<script'))
            {
                $end=strpos(substr(strtolower($html),$pos),'</script');
                
                if (!$end) break;
                
                $html=substr($html,0,$pos).substr($html,$pos+$end+9);
            }
            
            
            return $html;
        }
        return $html;
    }
}}
