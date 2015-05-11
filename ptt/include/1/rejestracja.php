<?php

	$kurs=isset($_REQUEST['kurs'])?$_REQUEST['kurs']:'';
	if (!isset($action)) $action='';
	

	$alert_content = "Warunkiem zapisania się na kurs jest wyrażnie zgody na przetwarzanie danych. W tym celu należ w zaznaczyć odpowiedź TAK w jednym z pytań.";	
	
	if (!strlen($action)) 
	{
		$action="Rejestracja";
		$action_opis= sysmsg("Zarejestruj");

		if ($kurs)
		{
			$action="RejestracjaZamowienie";
			$action_opis= sysmsg("Zapisz się na kurs");	
		}
	}

	if ($AUTH_ID)
	{
		$query="SELECT * FROM klienci WHERE id=$AUTH_ID";
		parse_str(query2url($query));
		$check_2 = "checked";
	}
	if ($email_zgoda == '1') $check_1 = "checked";
?>


<form method=post action="<?php echo $next?>" name="userform">
<input type=hidden name=id value="<?php echo $id?>">

<table class="table table-responsive" align=center>
<tr>
	<td><?php echo sysmsg("Imię") ?>:<sup>*</sup> </td>
	<td><input class="col-md-12 col-sm-12" type=text name=AUTH_imie value="<?php echo $imie?>"></td>
</tr>
<tr>
	<td><?php echo sysmsg("Nazwisko") ?>:<sup>*</sup> </td>
	<td><input class="col-md-12 col-sm-12" size=40 type=text name=AUTH_nazwisko value="<?php echo $nazwisko?>"></td>
</tr>

<tr>
	<td><?php echo sysmsg("Ulica i numer domu") ?>:<sup>*</sup> </td>
	<td><input class="col-md-12 col-sm-12" size=40 type=text name=AUTH_adres value="<?php echo $adres?>"></td>
</tr>
<tr>
	<td><?php echo sysmsg("Kod pocztowy i miejscowość") ?>:<sup>*</sup> </td>
	<td><input class="col-md-3 col-sm-3" size=7 type=text name=AUTH_kod value="<?php echo $kod?>"> 
		<input class="col-md-9 col-sm-9" size=29 type=text name=AUTH_miasto value="<?php echo $miasto?>"></td>
</tr>


<tr>
	<td><?php echo sysmsg("Telefon stacjonarny") ?>: </td>
	<td><input class="col-md-12 col-sm-12" size=30 type=text name=AUTH_telefon value="<?php echo $telefon?>"></td>
</tr>

<tr>
	<td><?php echo sysmsg("Telefon komórkowy") ?>: </td>
	<td><input class="col-md-12 col-sm-12" size=30 type=text name=AUTH_gsm value="<?php echo $gsm?>"></td>
</tr>

<tr>
	<td><?php echo sysmsg("Adres E-mail") ?>: </td>
	<td><input class="col-md-12 col-sm-12" size=40 type=text name=AUTH_email value="<?php echo $email?>"></td>
</tr>

<!-- <tr>
	<td>Miejsce pracy / nauki: </td>
	<td><input class="col-md-12 col-sm-12" size=30 type=text name=AUTH_praca value="<?php echo $praca?>"></td>
</tr> -->

<tr>
	<td><?php echo sysmsg("Wiek i płeć") ?>: </td>
	<td>
		<select  class="col-md-6 col-sm-6" name=AUTH_wiek>
			<option value=""><?php echo sysmsg("Podaj wiek") ?></option>
			<option value="-15" <?php if ($wiek=="-15") echo "selected";?>><?php echo sysmsg("poniżej") ?> 15</option>
			<option value="16-20" <?php if ($wiek=="16-20") echo "selected";?>>16-20</option>
			<option value="21-30" <?php if ($wiek=="21-30") echo "selected";?>>21-30</option>
			<option value="31-40" <?php if ($wiek=="31-40") echo "selected";?>>31-40</option>
			<option value="41-" <?php if ($wiek=="41-") echo "selected";?>><?php echo sysmsg("powyżej") ?> 40</option>
		</select>
		<select class="col-md-6 col-sm-6" name=AUTH_plec>
			<option value=""><?php echo sysmsg("Podaj płeć") ?></option>
			<option value="K" <?php if ($plec=="K") echo "selected";?>><?php echo sysmsg("Kobieta") ?></option>
			<option value="M" <?php if ($plec=="M") echo "selected";?>><?php echo sysmsg("Mężczyzna") ?></option>

		</select>

	</td>
</tr>


<tr>
	<td><?php echo sysmsg("Nazwa użytkownika") ?>: <sup>*</sup></td>
	<td><input class="col-md-12 col-sm-12" type=text name=AUTH_login value="<?php echo $login?>"></td>
</tr>
<tr>
	<td><?php echo sysmsg("Hasło użytkownika") ?>: <sup>*</sup></td>
	<td><input class="col-md-12 col-sm-12" type=password name=AUTH_pass ></td>
</tr>
<tr>
	<td><?php echo sysmsg("Powtórzenie hasła") ?>: <sup>*</sup></td>
	<td><input class="col-md-12 col-sm-12" type=password name=AUTH_pass1></td>
</tr>
<tr>
	<td colspan=2 ><sup>*</sup><?php echo sysmsg("dane wymagane do prawidłowej rejestracji") ?></td>
</tr>

<tr>
	<td colspan=2 align=left>
	<?php if ($kurs) include("$INCLUDE_PATH/termin.php"); ?>
	</td>
</tr>

<?php if ($lang == 'pl') { ?>

<tr>
	<td colspan=2 >	<?php 
	$txt1 = "Wyrażam zgodę na przetwarzanie moich danych osobowych: imienia, 
	nazwiska, adresu zamieszkania, numer telefonu, adresu email w celu rezerwacji 
	miejsc na kursach tańca oraz otrzymywania na swoją skrzynkę mailową informacji o 
	spektaklach i innych wydarzeniach organizowanych przez Polski Teatr Tańca - Balet 
	Poznański, ul. Kozia 4, 61-835 Poznań. Osobie, której dane dotyczą, przysługuje 
	prawo do wglądu w dane oraz prawo ich poprawienia lub usunięcia. Podanie danych 
	jest dobrowolne. Podstawa prawna - ustawa z dnia 29 sierpnia 1997 r. o ochronie 
	danych osobowych - Dz. U. Nr 101, poz. 926 oraz ustawa z dnia 18 lipca 2002 o 
	świadczeniach usług drogą elektroniczną Dz. U. nr 144 poz. 1204.";
	echo $txt1."
	<br><INPUT TYPE=\"checkbox\" NAME=\"AUTH_zgoda\" value=\"1\" onClick=\"setSecondCheck(document.userform)\" $check_1> <B>TAK</B>"; 
	?>
	</td>
</tr>

<tr>
	<td colspan=2 >	<?php 
	$txt2 = "Wyrażam zgodę na przetwarzanie moich danych osobowych: imienia, 
	nazwiska, adresu zamieszkania, numer telefonu, adresu email w celu rezerwacji 
	miejsc na kursach tańca organizowanych przez Polski Teatr Tańca - Balet Poznański, 
	ul. Kozia 4, 61-835 Poznań. Osobie, której dane dotyczą, przysługuje prawo do
	wglądu w dane oraz prawo ich poprawienia lub usunięcia. Podstawa prawna - ustawa 
	z dnia 29 sierpnia 1997 r. o ochronie danych osobowych - Dz.U. Nr 133, poz. 883 
	z późn. zm.";
	echo $txt2."
	<br><INPUT TYPE=\"checkbox\" NAME=\"AUTH_zgoda2\" value=\"1\" $check_2> <B>TAK</B>"; 
	?>
	</td>
</tr>
<?php } ?>

<tr>
	<td colspan=2 ><input type=button onClick="validateForm(document.userform);" value="<?php echo $action_opis ?>" class="btn btn-default"></td>
</tr>


</table>
<input type=hidden name=action value="<?php echo $action?>">
<input type=hidden name=kurs value="<?php echo $kurs?>">
</form>


<script>
	function validateForm(obj)
	{
		setSecondCheck(obj);

		if (obj.AUTH_imie.value.length == 0)
		{
			alert('<?php echo sysmsg("Proszę podać imię") ?>.');
			obj.AUTH_imie.focus();
			return;
		}

		if (obj.AUTH_nazwisko.value.length == 0)
		{
			alert('<?php echo sysmsg("Proszę podać nazwisko") ?>.');
			obj.AUTH_nazwisko.focus();
			return;
		}

		if (obj.AUTH_adres.value.length == 0)
		{
			alert('<?php echo sysmsg("Proszę podać adres") ?>.');
			obj.AUTH_adres.focus();
			return;
		}

		if (obj.AUTH_kod.value.length == 0)
		{
			alert('<?php echo sysmsg("Proszę podać kod pocztowy") ?>.');
			obj.AUTH_kod.focus();
			return;
		}

		if (obj.AUTH_miasto.value.length == 0)
		{
			alert('<?php echo sysmsg("Proszę podać miejscowość") ?>.');
			obj.AUTH_miasto.focus();
			return;
		}

		if (obj.AUTH_login.value.length == 0)
		{
			alert('<?php echo sysmsg("Proszę podać nazwę użytkownika") ?>.');
			obj.AUTH_login.focus();
			return;
		}

		<?php if (!strlen($AUTH_ID))
		{
			echo "
			if (obj.AUTH_pass.value.length == 0)
			{
				alert('".sysmsg("Proszę podać hasło")."');
				obj.AUTH_pass.focus();
				return;
			}
			";
		}
		?>
		
	if (obj.AUTH_pass.value.length != 0)
			if (obj.AUTH_pass1.value != obj.AUTH_pass.value)
			{
				alert('<?php echo sysmsg("Hasło niezgodne z potwierdzeniem") ?>.');
				obj.AUTH_pass.focus();
				return;
			}

		<?php if ($lang == 'pl') { ?>

			if (obj.AUTH_zgoda2.checked == false && obj.AUTH_zgoda.checked == false)
			{
				alert('<?php echo $alert_content ?>');
				return;
			}
		<?php } ?>
	obj.submit();
	}

	function setSecondCheck(obj)
	{
		<?php
		if ($lang == 'pl')
		{
			echo "if (obj.AUTH_zgoda.checked == true) obj.AUTH_zgoda2.checked = true";
		}
		?>
	}
</script>
