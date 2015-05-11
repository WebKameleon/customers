<form method=post action="<?php echo $next?>"  enctype="multipart/form-data" >

Plik : <input type=file name=plik size=80><br>
<span xdisabled>
<input type=checkbox name=DeleteBefore value=1> Usuń wszystkie przed wprowadzeniem<br>
</span>
<br>
<input type=submit value="Wprowadź <?php echo $title?>" class=button>


<input type=hidden name=action value="<?php echo $costxt?>">
</form>

