<?php

class WBP {

    static function put_data($name,$data)
    {
        file_put_contents(__DIR__."/../data/$name.php",'<?php'."\n/*\n".serialize($data)."\n*/");
    }

    static function get_data($name)
    {
        $file=__DIR__.'/../data/'.$name.'.php';

        if (!file_exists($file)) return;
        
        $file=file_get_contents($file);
    
        $pos=strpos($file,'/*');
        $file=substr($file,$pos+2);
        $file=substr($file,0,strlen($file)-2);
        
        $data=unserialize(trim($file));
        
        return $data;


    }

    static function get_file_db($obj,$key=false)
    {  
        $data=self::get_data('wbp_'.$obj);
        if ($key && is_array($data)) return $data[$key];
        return $data;
    }
    
    public static function relative_dir($myself, $target)
    {
        $myself = preg_replace("#^\./#", "", $myself);
        $target = preg_replace("#^\./#", "", $target);
        $myself = preg_replace("#/\./#", "/", $myself);
        $target = preg_replace("#/\./#", "/", $target);

        $me = explode("/", $myself);
        $him = explode("/", $target);
        $wynik = '';
        $up = '';

        $the_same = 1;
        for ($i = 0; $i < count($me) - 1; $i++) {
            if (!isset($him[$i])) $him[$i] = '';

            if ($me[$i] != $him[$i]) $the_same = 0;

            if (!$the_same) {
                $up .= "../";
                if (strlen($wynik) && strlen($him[$i])) $wynik .= "/";
                $wynik .= "$him[$i]";
            }
        }
        for (; $i < count($him); $i++) {
            if (strlen($wynik)) $wynik .= "/";
            $wynik .= "$him[$i]";
        }
        $wynik = "$up$wynik";

        return $wynik;
    }
    
    static function str_to_url($s, $case = 0, $dots=false)
    {
 
	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
		'ß' => 'ss', 
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
		'ÿ' => 'y',
 
		// Latin symbols
		'©' => '(c)',
 
		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
 
		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 
 
		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',
 
		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
 
		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
		'Ž' => 'Z', 
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z', 
 
		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
		'Ż' => 'Z', 
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',
 
		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);


        
        $out = str_replace(array_keys($char_map), $char_map, $s);
        $out = str_replace(' ', '-', trim($out));
        
      
        
        
        $out=str_replace('/','-',$out);
        if ($dots) $out=str_replace('.','-',$out);
        $out=preg_replace('#[^0-9a-z\/\-\._]#i','-',$out);
        $out=preg_replace('#-+#','-',$out);

        while (strlen($out)>3 && $out[0]=='-') $out=substr($out,1);
        
        if ($case == -1) {
            return strtolower($out);
        } else {
            if ($case == 1) {
                return strtoupper($out);
            } else {
                return ($out);
            }
        }
    }

    
    static function kameleon_require_static_include($obj)
    {
        $webtd=new webtdModel($obj->webtd['sid']);
        $webpage=new webpageModel($obj->webpage['sid']);
        
        //$webpage->file_name=preg_replace('/.php$/','.html',$webpage->file_name);
        $webtd->staticinclude=1;
        
        $webtd->save();
        $webpage->save();
        
    }
    
    
    static function contest_paied($sid,$photo_id,$date,$amount)
    {
        $td_data=self::get_data($sid);

        Spreadsheet::setToken(null);
        if (!isset($_SESSION['spreadsheets_access_token'])) $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
        $token=Spreadsheet::setToken($_SESSION['spreadsheets_access_token']);    
        foreach($token AS $k=>$v) $td_data['tokens']['spreadsheets']->$k=$v;
        $_SESSION['spreadsheets_access_token']=$td_data['tokens']['spreadsheets'];
	
	session_write_close();

	$sheets=Spreadsheet::listWorksheets($td_data['drive']['id']);
		
	$worksheet_id=null;
	foreach ($sheets AS $id=>$contents)
	{
	    if ($contents['title']==$td_data['title'])
	    {
		$worksheet_id=$id;
		break;
	    }
	}	
	
	$data=Spreadsheet::getWorksheet($td_data['drive']['id'], $worksheet_id );
	
	$header=$data[0];
	$idx_id=-1;
	$idx_payment=-1;
	foreach($header AS $i=>$h)
	{
	    if ($h=='id') $idx_id=$i;
	    if ($h=='payment') $idx_payment=$i;
	    
	}
	
	for ($i=1; $i<count($data); $i++)
	{
	    if ($data[$i][$idx_id]==$photo_id)
	    {
		Spreadsheet::update_cell($td_data['drive']['id'], $worksheet_id,$i,$idx_payment,"$date - $amount");
	    }
	}
	
    }

}