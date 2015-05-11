<?php


function db_Exec($db,$query)
{
	return pg_Exec($db,$query);
}

function db_FieldName($result,$i)
{
	return pg_FieldName($result,$i);
}
function db_NumRows($result)
{
	return pg_NumRows($result);
}

function db_NumFields($result)
{
	return pg_NumFields($result);
}

function db_Fetch_Row($result,$row)
{
	return pg_Fetch_Row($result,$row);
}

function db_Fetch_Object($result,$i)
{
	return pg_Fetch_Object($result,$i);
}

function db_Connect($connect_str_or_array)
{
	//dla pgSQL-a str:
	return pg_Connect($connect_str_or_array);

}

function db_Close($db)
{
	return pg_Close($db);
}

function db_Result($res,$row,$col)
{
	return pg_Result($res,$row,$col);
}


// Teraz nasze funkcje użytkowe

function sql2url($query)
{
	global $db;

	$result=db_Exec($db,$query);
	if ( db_numRows($result)!=1 ) return "";

	$data=db_fetch_row($result,0);
	$wynik="";
	for ($i=0;$i<count($data);$i++)
	{	
		if ($i) $wynik.="&";
		$wynik.=db_fieldname($result,$i)."=".urlencode(trim($data[$i]));
	}
	return $wynik;
}

function db_ExplodeName ($result,$row)
{
 $text="";
 $cols=db_NumFields($result);
 for ($i=0;$i<$cols;$i++)
 {
  $name=db_FieldName($result,$i);
  $data=db_fetch_row($result,$row);
  $value=urlencode(trim($data[$i]));
  $text.="$name=$value";
  if ($i!=$cols-1)
   $text.="&";
 }
 return $text;
}

function db_ObjectArray($db,$query)
{
	$wynik="";
	$result=db_Exec($db,$query);
	
	$cols=db_NumFields($result);
	for ($j=0;$j<$cols;$j++) $pola[]=db_FieldName($result,$j);
	for ($i=0;$i<db_NumRows($result);$i++)
	{
		$obj=db_Fetch_Object($result,$i);
		for ($j=0;$j<$cols;$j++) $obj->$pola[$j]=trim($obj->$pola[$j]);
		$wynik[]=$obj;
	}
	return($wynik);
}


?>
