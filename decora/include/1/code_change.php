<?php

	$lang=Bootstrap::$main->session('lang');
	include __DIR__.'/pre.php';
	
	define('LINK_TOKEN','a029e531bf93a8951c516e5f61ea7c9b2');

	function code_change($html)
	{
		$lang=Bootstrap::$main->session('lang');
		$dbh=&$_SERVER['dbh'];
		
		$server=Bootstrap::$main->session('server');
		
		if (isset($server['nazwa']) && strstr($server['nazwa'],'vidella'))
		{
			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-de-noflag">#',
					   '<a href="http://www.vidella.de/de/" title="\\1" class="flag-de-noflag">', $html);

			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-pl-noflag">#',
					   '<a href="http://www.vidella.com/" title="\\1" class="flag-pl-noflag">', $html);
			
			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-ru-noflag">#',
					   '<a href="http://www.vidella.ru/ru/" title="\\1" class="flag-ru-noflag">', $html);
			
			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-en-noflag">#',
					   '<a href="http://www.vidella.eu/en/" title="\\1" class="flag-en-noflag">', $html);
		}
		
		if (isset($server['nazwa']) && strstr($server['nazwa'],'decora'))
		{
			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-de-noflag">#',
					   '<a href="http://www.decora.pl/de/" title="\\1" class="flag-de-noflag">', $html);

			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-pl-noflag">#',
					   '<a href="http://www.decora.pl/" title="\\1" class="flag-pl-noflag">', $html);
			
			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-ru-noflag">#',
					   '<a href="http://www.decora.ru/ru/" title="\\1" class="flag-ru-noflag">', $html);
			
			$html=preg_replace('#<a href="[^"]+" title="([^"]+)" class="flag-en-noflag">#',
					   '<a href="http://www.decora.eu/en/" title="\\1" class="flag-en-noflag">', $html);
		}


		if (isset($server['nazwa']) && strstr($server['nazwa'],'vidella'))
		{
		
			$start=LINK_TOKEN.'begin';
			$end=LINK_TOKEN.'end';
			
			//return $html;
			
			//$html=str_replace('href="."','href="javascript:void(0)"',$html);
			$html=preg_replace('#="([^"]+\?product=[0-9_]+)&#', '="'.$start.'\\1'.$end.'?', $html);
			$html=preg_replace("#='([^']+\?product=[0-9_]+)&#", "='".$start.'\\1'.$end.'?', $html);
	
			$html=preg_replace('#="([^"]+\?product=[0-9_]+)"#', '="'.$start.'\\1'.$end.'"', $html);
			$html=preg_replace("#='([^']+\?product=[0-9_]+)'#", "='".$start.'\\1'.$end."'", $html);
			
		
			
			while (true) {
				$pos=strpos($html,$start);
				if ($pos===false) break;
			    
				
				$href=substr($html,$pos);
				$endpos=strpos($href,$end);
				
				if (!$endpos) break;
				
				$href=substr($href,0,$endpos+strlen($end));
				
				$target=$href;
				
				$target=str_replace($start,'',$target);
				$target=str_replace($end,'',$target);
			    
				$link=explode('?',$target);
				if (substr($link[0],-1)=='/') $link[0].='index.php';
				$file=basename($link[0]);
				
				$product=substr($link[1],8);
				$_product=explode('_',$product);
				
				$ean=$_product[0];
				if (isset($_product[1]) && strlen($_product[1])>strlen($ean) ) $ean=$_product[1];
				
				$sql="SELECT * FROM decora_products WHERE ean='".$ean."' LIMIT 1";
				$q=$dbh->query($sql);
				if ($q) foreach ($q AS $row ){
					decora_row($row,$lang); 
				}
				
				$name='NONAME';
				
				foreach (array('name','name_www','collection') AS $k)
				{
					if (isset($row[$k])) {
						$name=trim($row[$k]);
						break;
					}
				}
				$name=str_replace(',','_',$name);
				$name=Bootstrap::$main->kameleon->str_to_url($name);
				
				$target=dirname($link[0]).'/'.substr($file,0,strlen($file)-4).','.$name.','.$product;
			
			
				$html=str_replace($href,$target,$html);
			}
		}
		
		
		return $html;
	}
