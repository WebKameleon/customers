<?php
	$kameleon_prefix='/www/kameleon2';

	$src_pdo = array('dsn'=>'mysql:host=localhost;charset=utf8;dbname=wbp','user'=>'root','pass'=>'spierdalaj');
	$dst_pdo = array('dsn'=>'pgsql:host=localhost;dbname=cmspremium;port=5411','user'=>'cmsdecora','pass'=>'j4th84it44h8rg');

	if (file_exists(__DIR__.'/const.php')) include  __DIR__.'/const.php';

	require 'kameleon.php';
	require 'fun.php';

	
	try {

		$src=new PDO($src_pdo['dsn'],$src_pdo['user'],$src_pdo['pass']);
		$dst=new PDO($dst_pdo['dsn'],$dst_pdo['user'],$dst_pdo['pass']);
		
	} catch (Exception $e){
		die(print_r($e,1));
	}


	$_SERVER['wbp']['dbh']=$src;

	$kameleon_server='wbpicak';
	
	$kameleon_lang='pl';
	$kameleon_level=1;
	
	$q=$dst->query("SELECT * FROM servers WHERE nazwa='$kameleon_server'");
	if ($q) foreach ($q AS $server ){
	}
	
	if (!isset($server['id']) || !$server['id']) die("No server $kameleon_server\n");
	
	echo "Kameleon website $kameleon_server = ".$server['id']."\n";
	
	$_SERVER['plus']['artykuly']=500;	
	$_SERVER['plus']['aktualnosci_wbp']=1000;
	$_SERVER['plus']['tworcy']=50000;
	$_SERVER['plus']['katalogi']=55000;
	

	$_SERVER['kameleon']['dbh'] = $dst;
	$_SERVER['kameleon']['server'] = $server['id'];
	$_SERVER['kameleon']['lang'] = $kameleon_lang;
	$_SERVER['kameleon']['level'] = $kameleon_level;
	$_SERVER['kameleon']['ver'] = 1;
	$_SERVER['kameleon']['uimages'] = $kameleon_prefix.'/media/'.$kameleon_server.'/images/'.$_SERVER['kameleon']['ver'];
	$_SERVER['kameleon']['ufiles'] = $kameleon_prefix.'/media/'.$kameleon_server.'/files';
	
	@mkdir($_SERVER['kameleon']['uimages'],0775,true);
	
	
	$_SERVER['kameleon']['article_bgimg_w'] = 150;
	$_SERVER['kameleon']['article_bgimg_h'] = 0;
	$_SERVER['kameleon']['article_galery'] = 'gallery2';
	$_SERVER['kameleon']['article_galery_plus']=100;
	$_SERVER['kameleon']['article_galery_w'] = '100%';
	$_SERVER['kameleon']['article_galery_h'] = 150;
	
	

	$wbp_id=0;
	$wbp_module='';
	$next='wbp_module';
	$wbp_limit=0;
	$wbp_offset=0;
	$wbp_g_id=0;
	$wbp_like='';
	$wbp_kat=0;
	$wbp_gal=0;
	
	$force_rewrite = 0;
	$_SERVER['kameleon']['wbp_resursive']=false;


	for($i=1; $i<count($argv);$i++)
	{
		if ($argv[$i][0]=='-')
		{
			switch (strtolower(substr($argv[$i],1,1)))
			{
				case 'm':
				    $next='wbp_module';
				    break;
		
				case 'i':
				    $next='wbp_id';
				    break;
				
				case 'g':
				    $next='wbp_g_id';
				    break;				
		
				case 'l':
				    $next='wbp_limit';
				    break;
		
				case 'o':
				    $next='wbp_offset';
				    break;
				
				case 'k':
				    $next='wbp_kat';
				    break;
				
				case 'r':
				    $next='wbp_like';
				    break;
								
				
				case 'f':
				    $force_rewrite=1;
				    break;
				case 's':
					$_SERVER['kameleon']['wbp_resursive']=true;
					break;
				
				case 'a':
				    $next='wbp_gal';
				    break;				
			}

			continue;
		}
		$$next=$argv[$i];
	}

	if (!$wbp_module) usage($argv[0]);
	
	$wbp_module=preg_replace('/\.php$/','',$wbp_module);
	
	
	echo "Entering $wbp_module ... \n";

	include $wbp_module.'.php';


	$dst=null;
	$src=null;
	
	



