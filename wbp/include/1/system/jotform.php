<?php

class jotform {
    private $key;
    
    public function __construct($key)
    {
        $this->key = $key;
    }
    
    
    private function req($url,$type='GET',$data=null)
    {
        $rurl='https://api.jotform.com/'.$url.'?apiKey='.$this->key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rurl);
        if ($data)
        {
            if (strtoupper($type)=='POST')
            {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            if (strtoupper($type)=='PUT')
            {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                $data=json_encode($data);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret=json_decode(curl_exec ($ch), true);
        curl_close ($ch);
        
        return $ret;  
    }
    
    
    public function getForms()
    {
        return $this->req('user/forms');
    }
    public function getFolders()
    {
        return $this->req('user/folders');
    }
    
    public function getForm($formId)
    {
        return $this->req('form/'.$formId);
    }
    public function getFormProp($formId)
    {
        return $this->req('form/'.$formId.'/properties');
    }
    
    public function setFormProp($formId,$data)
    {
        return $this->req('form/'.$formId.'/properties','POST',$data);
    }

    public function getFolder($folderId)
    {
        return $this->req('folder/'.$folderId);
    }
    
    public function newForm($data)
    {
        return $this->req('form','POST',$data);
    }
        
    public function getFormSource($formId)
    {
        $form=$this->getForm($formId);
        if (!isset($form['content']['url'])) return null;
        $url=$form['content']['url'];
       
        $html=file_get_contents($url);
    
        $scripts=array();
        $txt=$html;
        while ($pos=stripos($txt,'<script'))
        {
            $txt=substr($txt,$pos);
            $end=stripos($txt,'</script>');
            if ($end) {
                $src=stripos($txt,'src="');
                $endtag=stripos($txt,'>');
                
                if ($src<$endtag) {
                    $txt=substr($txt,$src+5);
                    $end=strpos($txt,'"');
                    $scripts[]=substr($txt,0,$end);
                } else {
                    $scripts[]=substr($txt,$endtag+1,$end-$endtag-1)."\n";
                }
            }
            $txt=substr($txt,1);
        }    
            
        
        $links=array();
        $txt=$html;
        
        
        while ($pos=stripos($txt,'<link'))
        {
            $txt=substr($txt,$pos);
            $end=stripos($txt,'/>');
            $rel=stripos($txt,'rel="stylesheet"');
            
            if ($rel<$end) $links[]=substr($txt,0,$end)."/>";
            
            $txt=substr($txt,1);
        }    
        
        $styles=array();
        $txt=$html;
        
        while ($pos=stripos($txt,'<style'))
        {
            $txt=substr($txt,$pos);
            $end=stripos($txt,'</style>');
            
            $style=substr($txt,0,$end)."</style>";
            $style=str_replace('!important','',$style);
            $style=str_replace(' ;',';',$style);
            $style=preg_replace('/body[^\}]+\}/i','',$style);
            $style=preg_replace('/html[^\}]+\}/i','',$style);
            
            $styles[]=$style;
            
            $txt=substr($txt,1);
        }
    
        
        
        $body=stripos($html,'<body');
        $html=substr($html,$body);
        $end=strpos($html,'>');
        $html=trim(substr($html,$end+1));
        $end=stripos($html,'</body>');
        $html=substr($html,0,$end);
        
        
        return array('scripts'=>$scripts,'html'=>implode("\n",$links).implode("\n",$styles).$html);
    }
}