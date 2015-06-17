<?php
    if (!$costxt) return;
    
    $file='map/'.$costxt.'.json';
    
    $data=json_decode(file_get_contents(__DIR__.'/'.$file),1);
    
    $include=isset($KAMELEON_MODE) && $KAMELEON_MODE?$session['uincludes_ajax']:$session['include_path'];
    
    //mydie($data['markers']);
?>

<div class="calc">
<div class="calc-title">Kalkulator</div>

<div class="col-md-3 col-sm-3">
<ul>
	<li class="kat1 current">
	<div class="radio"><input checked="checked" id="optionsRadios1" name="optionsRadios" type="radio" value="option1" /></div>
	</li>
	<li class="kat2">
	<div class="radio"><input id="optionsRadios2" name="optionsRadios" type="radio" value="option2" /></div>
	</li>
	<li class="kat3">
	<div class="radio"><input id="optionsRadios3" name="optionsRadios" type="radio" value="option3" /></div>
	</li>
	<li class="kat4">
	<div class="radio"><input id="optionsRadios4" name="optionsRadios" type="radio" value="option4" /></div>
	</li>
	<li class="kat5">
	<div class="radio"><input id="optionsRadios5" name="optionsRadios" type="radio" value="option5" /></div>
	</li>
</ul>
</div>

<div class="col-md-9 ol-sm-9">
<div class="row">
<div class="col-md-8 col-sm-8">
<div class="row">
<div class="col-xs-5"><select class="form-control"><option>Konin</option><option>Słupca</option><option>Września</option><option>Poznań</option><option>Buk</option><option>Nowy Tomyśl</option><option>Trzciel</option><option>Jordanowo</option><option>Torzym</option><option>Tarnawa</option> </select></div>

<div class="col-xs-2">
<div class="arrow">&nbsp;</div>
</div>

<div class="col-xs-5"><select class="form-control"><option>Konin</option><option>Słupca</option><option>Września</option><option>Poznań</option><option>Buk</option><option>Nowy Tomyśl</option><option>Trzciel</option><option>Jordanowo</option><option>Torzym</option><option>Tarnawa</option> </select></div>
</div>
</div>

<div class="col-md-4 col-sm-4">
<div class="prize">29 zł</div>
</div>
</div>
</div>

<div class="clearfix">&nbsp;</div>
</div>

<script>
    var calc_json='<?php echo $include.'/'.$file;?>';
</script>