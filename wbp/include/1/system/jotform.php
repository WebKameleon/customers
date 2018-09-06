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
        
        //mydie(htmlspecialchars($html));
    
        $scripts=array();
        $txt=$html;
        while ($pos=stripos($txt,'<script'))
        {
            $txt=substr($txt,$pos);
            $end=stripos($txt,'</script>');
            if ($end) {
                $src=stripos($txt,'src="');
                $endtag=stripos($txt,'>');
                
                if ($src<$endtag && strlen(trim($src))>0 ) {
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
        
        $html=preg_replace('~<script.+?</script>~is','',$html);
        
        
        return array('scripts'=>$scripts,'html'=>implode("\n",$links).implode("\n",$styles).$html);
    }
    
    public function iframe($formId) {
	$iframe = '<script nodefer="defer" type="text/javascript" src="https://form.jotformeu.com/jsform/'.$formId.'"></script>';
	return $iframe;
    }
    public function xiframe($formId) {

        $iframe = '

 <iframe id="JotFormIFrame-"'.$formId.' onload="window.parent.scrollTo(0,0)" allowtransparency="true" allowfullscreen="true" src="https://form.jotformeu.com/'.$formId.'" frameborder="0" style="width: 1px; min-width: 100%; height:1069px; border:none;" scrolling="no" > </iframe> 

	<script type="text/javascript"> 
		var ifr = document.getElementById("JotFormIFrame-80582078833362"); 
		if(window.location.href && window.location.href.indexOf("?") > -1) 
		{ 
			var get = window.location.href.substr(window.location.href.indexOf("?") + 1); 
			if(ifr && get.length > 0) { 
				var src = ifr.src; src = src.indexOf("?") > -1 ? src + "&" + get : src + "?" + get; 
				ifr.src = src; 
			} 
		} 
		window.handleIFrameMessage = function(e) { 
			if (typeof(e.data)!="string") return;
			var args = e.data.split(":"); 
			if (args.length > 2) { 
				iframe = document.getElementById("JotFormIFrame-" + args[(args.length - 1)]); 
			} else { 
				iframe = document.getElementById("JotFormIFrame"); 
			} 
			if (!iframe) { 
				return; 
			} 
			switch (args[0]) { 
				case "scrollIntoView": iframe.scrollIntoView(); break; 
				case "setHeight": iframe.style.height = args[1] + "px"; break; 
				case "collapseErrorPage": if (iframe.clientHeight > window.innerHeight) { iframe.style.height = window.innerHeight + "px"; } break; 
				case "reloadPage": window.location.reload(); break; 
				case "loadScript": var src = args[1]; if (args.length > 3) { src = args[1] + ":" + args[2]; } var script = document.createElement("script"); script.src = src; script.type = "text/javascript"; document.body.appendChild(script); break; 
				case "exitFullscreen": 
					if (window.document.exitFullscreen) window.document.exitFullscreen(); 
					else if (window.document.mozCancelFullScreen) window.document.mozCancelFullScreen(); 
					else if (window.document.mozCancelFullscreen) window.document.mozCancelFullScreen(); 
					else if (window.document.webkitExitFullscreen) window.document.webkitExitFullscreen(); 
					else if (window.document.msExitFullscreen) window.document.msExitFullscreen(); break; 
			} 
			var isJotForm = (e.origin.indexOf("jotform") > -1) ? true : false; 
			if(isJotForm && "contentWindow" in iframe && "postMessage" in iframe.contentWindow) { 
				var urls = {"docurl":encodeURIComponent(document.URL),"referrer":encodeURIComponent(document.referrer)}; 
				iframe.contentWindow.postMessage(JSON.stringify({"type":"urls","value":urls}), "*"); 
			} 
		}; 
		if (window.addEventListener) { 
			window.addEventListener("message", handleIFrameMessage, false); 
		} else if (window.attachEvent) { 
			window.attachEvent("onmessage", handleIFrameMessage); 
		} 
</script>
              ';
    
    
        return $iframe;
    }
}
