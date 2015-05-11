<?php
	$kurs=$_REQUEST['kurs'];

	echo "<br><br>Taniec : ".$kurs[nazwa]." - ".$kurs[cena];
	echo "
	<FORM METHOD=POST ACTION=\"$next\">
	<INPUT TYPE=\"hidden\" name=\"klient\" value=\"$kurs[k_id]\">
	<INPUT TYPE=\"hidden\" name=\"kurs\" value=\"$kurs[id]\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"admin/NowaCena\">
	<INPUT TYPE=\"hidden\" name=\"nazwa_tanca\" value=\"$kurs[nazwa]\">
	<TABLE>
	<TR>
		<TD>Nowa cena :</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"nowa_cena\"></TD>
	</TR>
	<TR>
		<TD colspan=\"2\"><INPUT TYPE=\"submit\" value=\"Zapisz cenę\" class=\"button\"></TD>
	</TR>
	</TABLE>
	</FORM>
	";

