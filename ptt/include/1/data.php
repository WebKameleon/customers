<?

<?
$today = getdate(); 
$month = $today['mon']; 
$mday = $today['mday']; 
$year = $today['year']; 
$weekday = $today['weekday']; 
SWITCH ($weekday)
{
	CASE 'Monday': 		if ($lang=="p" || $lang=="i") $weekday="poniedziałek";	BREAK;
	CASE 'Tuesday': 	if ($lang=="p" || $lang=="i") $weekday="wtorek"; 		BREAK;
	CASE 'Wednesday': 	if ($lang=="p" || $lang=="i") $weekday="środa"; 		BREAK;
	CASE 'Thursday': 	if ($lang=="p" || $lang=="i") $weekday="czwartek"; 		BREAK;
	CASE 'Friday': 		if ($lang=="p" || $lang=="i") $weekday="piątek"; 		BREAK;
	CASE 'Saturday': 	if ($lang=="p" || $lang=="i") $weekday="sobota"; 		BREAK;
	CASE 'Sunday': 		if ($lang=="p" || $lang=="i") $weekday="niedziela"; 	BREAK;
}



SWITCH ($month)
{
	CASE '1':	if ($lang=="p" || $lang=="i") $month="styczeń";  else $month="january";		BREAK;
	CASE '2': 	if ($lang=="p" || $lang=="i") $month="luty"; else $month="february";		BREAK;
	CASE '3': 	if ($lang=="p" || $lang=="i") $month="marzec"; else $month="march"; 		BREAK;
	CASE '4': 	if ($lang=="p" || $lang=="i") $month="kwiecień"; else $month="april"; 		BREAK;
	CASE '5':	if ($lang=="p" || $lang=="i") $month="maj"; else $month="may"; 				BREAK;
	CASE '6': 	if ($lang=="p" || $lang=="i") $month="czerwiec"; else $month="june"; 		BREAK;
	CASE '7':	if ($lang=="p" || $lang=="i") $month="lipiec"; else $month="july";			BREAK;
	CASE '8':	if ($lang=="p" || $lang=="i") $month="sierpień"; else $month="august";		BREAK;
	CASE '9': 	if ($lang=="p" || $lang=="i") $month="wrzesień"; else $month="september";	BREAK;
	CASE '10': 	if ($lang=="p" || $lang=="i") $month="październik"; else $month="october";	BREAK;
	CASE '11': 	if ($lang=="p" || $lang=="i") $month="listopad"; else $month="november"; 	BREAK;
	CASE '12':	if ($lang=="p" || $lang=="i") $month="grudzień"; else $month="december";	BREAK;
}

if ($lang=="e")
{
	if ($mday==1) $mday_add="st";
	elseif ($mday==2) $mday_add="nd";
	elseif ($mday==3) $mday_add="rd";
	else $mday_add="th";
}


$data = $weekday." ".$mday.".".$month.".".$year;
echo "<div id=\"data\">".win2iso($data)."</div>";
?>