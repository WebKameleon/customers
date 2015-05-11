<form method=post action="<?php echo $next;?>">
<table class="table table-responsive" align="center">
<input type="hidden" name="action" value="login"/>
<tr>
	<td><?php echo sysmsg("Nazwa użytkownika"); ?> : </td>
	<td><input class="col-md-12 col-sm-12" type="text" name="SET_AUTH_LOGIN" value="<?php echo $AUTH_LOGIN;?>"></td>
</tr>
<tr>
	<td><?php echo sysmsg("Hasło użytkownika"); ?> : </td>
	<td><input class="col-md-12 col-sm-12" type="password" name="SET_AUTH_PASS"></td>
</tr>

<tr>
	<td colspan=2 align=right><input type=submit value="Login" class="btn btn-default"></td>
</tr>

</table>


</form>
