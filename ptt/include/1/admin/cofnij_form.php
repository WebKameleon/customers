<?php
	$showundo=$_REQUEST['showundo'];
	$dataod=$_REQUEST['dataod'];
	$datado=$_REQUEST['datado'];
	
	$acc_date = date("d-m-Y");

	if (!strlen($dataod)) $dataod = $acc_date;
	if (!strlen($datado)) $datado = $acc_date;

	echo "
		<FORM METHOD=\"POST\" ACTION=\"$self\" name=\"cofnijform\">
		<TABLE class=\"table table-responsive table-striped\">
		<TR>
			<TD>Data od</TD>
			<TD>Data do</TD>
			<td></td>
		</TR>
		<TR>
			<TD><INPUT class=\"form-control\" TYPE=\"text\" NAME=\"dataod\" value=\"$dataod\"></TD>
			<TD><INPUT class=\"form-control\" TYPE=\"text\" NAME=\"datado\" value=\"$datado\"></TD>
			<TD>
				<INPUT TYPE=\"hidden\" name=\"showundo\" value=\"1\">
				<INPUT TYPE=\"submit\" value=\"Szukaj\" class=\"btn btn-default\">			
			</TD>				
		</TR>
		
		
		</TABLE>

		</FORM>
	";


	if ($showundo)
	{
		$add_cond = "";

		$_dataod = "CURRENT_DATE";
		$_datado = "CURRENT_DATE";
		if (strlen($dataod)) $_dataod = "'".FormatujDateSql($dataod)."'"; 
		if (strlen($datado)) $_datado = "'".FormatujDateSql($datado)."'"; 

		$add_cond.= " WHERE d_wykonania >= $_dataod AND d_wykonania <= $_datado ";
		
		include("$INCLUDE_PATH/admin/undo.php");
	}

