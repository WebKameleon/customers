<?php

if (!function_exists("unPolish"))
{
	function unPolish($str)
	{
		$str=ereg_replace("Ą","A",$str);
		$str=ereg_replace("ą","a",$str);
		$str=ereg_replace("Ą","A",$str);
		$str=ereg_replace("ą","a",$str);
		$str=ereg_replace("Ś","S",$str);
		$str=ereg_replace("ś","s",$str);
		$str=ereg_replace("Ś","S",$str);
		$str=ereg_replace("ś","s",$str);
		$str=ereg_replace("Ź","Z",$str);
		$str=ereg_replace("ź","z",$str);
		$str=ereg_replace("Ź","Z",$str);
		$str=ereg_replace("ź","z",$str);
		$str=ereg_replace("ę","e",$str);
		$str=ereg_replace("Ę","E",$str);
		$str=ereg_replace("ż","z",$str);
		$str=ereg_replace("Ż","Z",$str);
		$str=ereg_replace("ń","n",$str);
		$str=ereg_replace("Ń","N",$str);
		$str=ereg_replace("ó","o",$str);
		$str=ereg_replace("Ó","O",$str);
		$str=ereg_replace("ć","c",$str);
		$str=ereg_replace("Ć","C",$str);
		$str=ereg_replace("ł","l",$str);
		$str=ereg_replace("Ł","L",$str);
		return ($str);
	}
}

if (!function_exists("unhtml"))
{
	function unhtml($html)
	{
		return ereg_replace("\<[^>]*>","",$html);
	}
}

if (!function_exists("win2iso"))
{
	function win2iso($str)
	{
		$str=ereg_replace("Ą","Ą",$str);
		$str=ereg_replace("Ś","Ś",$str);
		$str=ereg_replace("Ź","Ź",$str);
		$str=ereg_replace("ą","ą",$str);
		$str=ereg_replace("ś","ś",$str);
		$str=ereg_replace("ź","ź",$str);
		return ($str);
	}
} 

if (!function_exists("ado_ExplodeName")) 
{
	function ado_ExplodeName ($result,$row)
	{
		global $WM;
		return $WM->ado_ExplodeName ($result,$row);
 	}
}

if (!function_exists("humandate"))  
{
	function humandate($unix) 
	{
		return date("d-m-Y",$unix);
	}
}

if (!function_exists("humanshort")) 	
{
	function humanshort($unix)
	{
		return substr(humandate($unix),0,5);
	}
}
 
if (!function_exists("FormatujDate")) 	
{
	function FormatujDate ($d)
	{
		return substr($d,8,2)."-".substr($d,5,2)."-".substr($d,0,4);
	}
}
 
if (!function_exists("FormatujDateSQL")) 	
{
	function FormatujDateSQL ($d)
	{
		return substr($d,6,4)."-".substr($d,3,2)."-".substr($d,0,2);
	}
}
 
if (!function_exists("u_Cena")) 	
{
	function u_Cena ($c)
	{
		return number_format($c,2,","," "). " zł";
	}
}
 
if (!function_exists("query2url")) 	
{
	function query2url($query)
	{
		global $db;
		
		$result=pg_Exec($db,$query);
		if ( pg_numRows($result)!=1 ) return "";
		
		$data=pg_fetch_row($result,0);
		$wynik="";
		for ($i=0;$i<count($data);$i++)
		{
		        if ($i) $wynik.="&";
		        $wynik.=pg_fieldname($result,$i)."=".urlencode(trim($data[$i]));
		}
		return $wynik;
	}
}
 
if (!function_exists("pg_ExplodeName")) 	
{
	function pg_ExplodeName ($result,$row)
	{
		$text="";
		$cols=pg_NumFields($result);
		for ($i=0;$i<$cols;$i++)
		{
			$name=pg_FieldName($result,$i);
			$data=pg_fetch_row($result,$row);
			$value=urlencode(trim($data[$i]));
			$text.="$name=$value";
			if ($i!=$cols-1) $text.="&";
		}
		return $text;
	}
	
}
 
if (!function_exists("toFloat")) 	
{
	function toFloat($f)
	{
		if (!strlen(trim($f))) return "NULL";
		$f=ereg_replace("[^0-9,\.]*","",$f);
		$f=ereg_replace(",",".",$f);
		return $f+0;
	}
}

if (!function_exists("iso2utf")) {
	function iso2utf($f_text)
	{
		global $lang;
		
		$_iso = "iso-8859-";
		SWITCH ($lang)
		{
			CASE 'i':	$_iso.=2; BREAK;
			CASE 'r':	$_iso.=5; BREAK;
			DEFAULT: $_iso.=1;
		}
		if ($lang=="t" || $lang=="p") $_iso = "windows-1250";
		
		return iconv($_iso, 'utf-8', $f_text);
	}
}

if (!function_exists('utf82iso')) {		
	function utf82iso($f_text)
	{

		$f_text = str_replace("Ĺ","ł",$f_text);
		$f_text = str_replace("ł","ł",$f_text);
		$f_text = str_replace("Ĺ","Ł",$f_text);
		$f_text = str_replace("Ĺ","ń",$f_text);
		$f_text = str_replace("Ĺ","Ń",$f_text);
		$f_text = str_replace("Ä","ę",$f_text);
		$f_text = str_replace("Ä","Ę",$f_text);
		$f_text = str_replace("Ăł","ó",$f_text);
		$f_text = str_replace("ó","ó",$f_text);
		$f_text = str_replace("Ă","Ó",$f_text);
		$f_text = str_replace("Ĺź","ż",$f_text);
		$f_text = str_replace("łť","ż",$f_text);
		$f_text = str_replace("Ĺť","Ż",$f_text);
		$f_text = str_replace("Ĺ","Ź",$f_text);
		$f_text = str_replace("ž","ą",$f_text);//ą		
		$f_text = str_replace("Ä","ą",$f_text);//ą
		$f_text = str_replace("Ä","Ą",$f_text);//Ą
		$f_text = str_replace("Ä","ź",$f_text);//Ą
		$f_text = str_replace("ł","ś",$f_text);//ś
		$f_text = str_replace("Ĺ","Ś",$f_text);//Ś		
		$f_text = str_replace("ę","ć",$f_text);
		$f_text = str_replace("Ä","Ć",$f_text);

		return $f_text;
	}
}

if (!function_exists('sysmsg')) {		
	function sysmsg($msg,$grupa="")
	{
	  global $db;
	  global $lang;
	
		$defaultlang="ms";
	
		$msg=trim($msg);
	
		$m=addslashes($msg);
	
	  	$query="SELECT msg_msg FROM messages WHERE msg_label='$m' AND msg_lang='$defaultlang'";
	  	parse_str(query2url($query));
	  	if (!strlen($msg_msg)) 
		{
			$query="INSERT INTO messages (msg_label,msg_lang,msg_msg,msg_group) 
					VALUES ('$m','$defaultlang','$m','$grupa')";
			pg_Exec($db,$query);
		}	
	
	
	  	$query="SELECT msg_msg FROM messages 
				WHERE msg_label='$m' AND msg_lang='$lang'";
	  	parse_str(query2url($query));
		
		if (!strlen($msg_msg)) return $msg;
	  	return stripslashes($msg_msg);
	}
}
?>
