
<form method="POST"
      action="<?php echo $self?>#kameleon_td<?php echo $sid?>">
Od dnia
<input type="date" name="odDnia"
    value="<?php if (isset($_POST['odDnia'])) echo $_POST['odDnia']?>">
<input type="submit" value="przelicz" />
</form>
<?php

if (!isset($_POST['odDnia']) || !$_POST['odDnia']) return;

$d=strtotime($_POST['odDnia']);
$user=Bootstrap::$main->session('user');



$sql='SELECT count(distinct(page_id)) AS pages FROM webtd WHERE server='.$this->webtd['server']."
        AND autor='".$user['username']."' AND nd_create>$d";
$q=$dbh->query($sql);
if ($q) foreach ($q AS $row ){
    $pages=$row['pages'];
}

$sql='SELECT plain FROM webtd WHERE server='.$this->webtd['server']."
        AND autor='".$user['username']."' AND nd_create>$d AND plain ~ '".UFILES_TOKEN."'";
        
$q=$dbh->query($sql);
$files=0;
if ($q) foreach ($q AS $row ){
    $files+=substr_count($row['plain'],UFILES_TOKEN);
}

echo '<font color="red">';
echo "Od: ".date('d-m-Y',$d).'<br/>';
echo "Strony: $pages<br/>";
echo "Pliki: $files<br/>";
echo "</font>";
