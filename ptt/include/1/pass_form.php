<?php
	$button_text = sysmsg("Przypomnij hasło");
	echo "
	<FORM METHOD=POST ACTION=\"$next\" name=\"passform\" onSubmit=\"return validatePass(this)\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"WyslijHaslo\">
	<table class=\"table table-responsive table-striped\">
	<!-- 
		<TR>
			<TD class='c2'>".sysmsg("Tu wpisz nazwę użytkownika")." </td>
			<TD class='c2'><INPUT TYPE=\"text\" NAME=\"user\"></td>
		</TR>
	 -->	
		<TR>
			<TD class='c2'>".sysmsg("Tu wpisz swój email")." </td>
			<TD class='c2'><INPUT TYPE=\"text\" NAME=\"email\"></TD>
		</TR>
		<TR>
		        <TD class='c2'>&nbsp;</TD>
			<TD colspan=\"2\" class='c2' align=\"center\"><INPUT TYPE=\"submit\" value=\"$button_text\" class=\"button\"></TD>
		</TR>
		</TABLE>
	</FORM>
	";

?>
	<script>
		function validatePass(obj)
		{
			if (obj.email.value.length == 0)
			{
				alert('<? echo sysmsg("Podaj Twój adres email") ?>.');
				return false;
			}
		return true;
		}
	</script>
