<?php
include "head.php";
/*  W przypadku nowych nauczycieli dodajemy do tabeli term_accounts same pola `imie` i `nazwisko`.
Nastepnie trzeba odkomomentować linijkę niżej("getLoginPass();") i skrypt(po jednorazowym wejściu na strone) sam generuje loginy i hasła nowym kontom. (pole `hasl` to niezakodowane hasło)
Później można spowrotem zakomentować linijkę.
 */
//genLoginPass();
$date = time();

if (isset($_GET['klasa'])) {
	$klasa = (int) $_GET['klasa'];
	$klas = getClass($klasa);
}
if ($klasa) {
	if ($browser['name'] != 'Internet Explorer') {
		printMiniClass($klas['id']);
	}
}
if (isset($_GET['month'])) {
	$miesiac = (int) $_GET['month'];
}

if (isset($_GET['year'])) {
	$rok = (int) $_GET['year'];
}

$day = date('d', $date);
if (isset($miesiac) && $miesiac > 0 && $miesiac <= 12) {
	$mstr = ($miesiac <= 9) ? '0' : '';
	$month = $mstr . '' . $miesiac;
} else {
	$month = date('m', $date);
	$miesiac = $month;
}

if (isset($rok)) {
	$year = $rok;
} else {
	$year = date('Y', $date);
}

$first_day = mktime(0, 0, 0, $month, 1, $year);
$title = dateV('F', $first_day);

$day_of_week = date('D', $first_day);

$days_in_month = cal_days_in_month(0, $month, $year);

switch ($day_of_week) {

	case "Mon":$blank = 0;break;

	case "Tue":$blank = 1;break;

	case "Wed":$blank = 2;break;

	case "Thu":$blank = 3;break;

	case "Fri":$blank = 4;break;

	case "Sat":$blank = 5;break;

	case "Sun":$blank = 6;break;

}

$days = 1;
?>
<div style="height: 65px; width: 100%;"></div>
<table id="site">
<tr>
<td style="border: 0; width: auto; padding-left: 30px;">
<? if($klasa) {
	if( ! $browser['name'] == 'Internet Explorer')
	{
		printMiniClass($klas['id']);
	}
?>
<div id="content">
	<table class="table">
	<tr>
	<?
		if(isset($miesiac))
		{
			$nm = $miesiac;
			$rk = $rok;
			if($miesiac == 12)
			{
				$nm = 0;
				if(!isset($rok))
					$rok = $year;
				$rk = $rok + 1;
			}
		}
		$mies = (isset($nm)) ? $nm+1 : '';
		$ro = (isset($rk)) ? '&year='.$rk : '';
		$kl = (isset($klasa)) ? '&klasa='.$klasa : '';
		$linkUp = "index.php?month=".$mies."".$ro."".$kl;
		if(isset($miesiac))
		{
			$nm = $miesiac;
			$rk = $rok;
			if($miesiac == 1)
			{
				$nm = 13;
				if(!isset($rok))
					$rok = $year;
				$rk = $rok - 1;
			}
		}
		$mies = (isset($nm)) ? $nm-1 : '';
		$ro = (isset($rk)) ? '&year='.$rk : '';
		$kl = (isset($klasa)) ? '&klasa='.$klasa : '';
		$linkDown = "index.php?month=".$mies."".$ro."".$kl;

		if($browser['name'] == 'Internet Explorer') {
	?>
		<td id="arrow_left_ie"><a href="<? echo $linkDown; ?>"><</a></td>
		<td colspan="2" style="border-right: 0; border-left: 0;"><span class="miesiac"><? echo $title; ?></span></td>
		<td style="border-left: 0; border-right: 0;"><span class="miesiac"><? echo $year; ?></span></td>
		<td id="arrow_right_ie"><a href="<? echo $linkUp; ?>">></a></td>
	<?
	} else {
	?>
	<td colspan="5">
		<span id="arrow_left"><a href="<? echo $linkDown; ?>"><</a></span>
		<span class="miesiac"><? echo $title.' '.$year; ?></span>
		<span id="arrow_right"><a href="<? echo $linkUp; ?>">></a></span>
	</td>
	<?
		}
	?>
	</tr>
	<tr><td><b>Poniedziałek</b></td><td><b>Wtorek</b></td><td><b>Środa</b></td><td><b>Czwartek</b></td><td><b>Piątek</b></td></tr>
	<tr><?

		$wpisy = getWpisy($klasa);

		$day_num = 1;
		if($blank < 5) {
			while($blank > 0)
			{
				echo "<td></td>";
				$blank--;
				$days++;
			}
		} else {
			$day_num = 8 - $blank;
		}
		while ($day_num <= $days_in_month)
		{
			$day_str = ($day_num <= 9) ? '0' : '';
			$tid=$day_str.''.$day_num.''.$month.''.$year;
			$tid2=$year.'-'.$month.'-'.$day_str.''.$day_num;
			$czasDnia = strtotime($tid2." 18:00:00");
			if($days < 6)
			{
				if(!$user || $czasDnia < time() || ($klas['specjalna'] == 1 && $user['dostep'] < 2))
					echo "<td class='td'><div class='day'><b>".$day_num."</b></div>";
				else {
					echo "<td class='td'><table class='tabday'><tr><td class='tabday'><b>".$day_num."</b></td>";
					echo '<td class="tadd">
							<a href="dodaj.php?id='.$tid.'&klasa='.$klasa.'">Dodaj wpis</a>
						</td>';
					echo "</tr></table>";
				}
				echo "<div style='clear: both;'> </div>";
				if(isset($wpisy) && !empty($wpisy[$tid2]))
				{
					foreach($wpisy[$tid2] as $wpis)
					{
						printWpis($wpis);
					}
				}
				echo "</td>";
			}
			$day_num++;
			$days++;
			if ($days > 7)
			{
				echo "</tr><tr>";
				$days = 1;
			}
		}
		while ( $days >1 && $days <=5 )
		{
			echo "<td> </td>";
			$days++;
		}
	?>
	</tr>
	</table>
	<div style='clear: both;'> </div>
</div>
</td>
<?
		if($browser['name'] == 'Internet Explorer')
		{
			printMiniClass($klas['id']);
		}
	} else {
		printClass();
	}
?>
<td style="border: 0; width: 250px; padding-right: 10px;">
<div class="menu form_login" style="margin-bottom: 5px;">
<h2><a href="index.php">Terminarz</a></h2><hr />
<h2><a href="zastepstwa.php">Zastępstwa</a></h2>
</div>
<?
	$mn = (isset($month)) ? '&month='.$month : '';
	$ro = (isset($year)) ? '&year='.$year : '';
	$kl = (isset($klasa)) ? '&klasa='.$klasa : '';
	$link = "index.php?month=".$mn."".$ro."".$kl;
	if(!$user) printLogin($link); else {
		$us = ($user['dostep']==2) ? '<a href="users.php">Dodaj nowe konto</a><br />' : '';
		echo '<div class="menu form_login">
				<a href="moje.php">Moje wpisy</a><br />
				<a href="password.php">Zmień hasło</a>
				<br />'.$us.'<br />
				<a href="logout.php">';
		if($browser['name'] != 'Internet Explorer')
			echo '<button class="btn btn-large btn-default btn-block">';
		echo 'Wyloguj';
		if($browser['name'] != 'Internet Explorer')
			echo '</button>';
		echo '</a>
		      </div>';
	}
?>
<div class="form_login" style="margin-top: 5px;">
	<span style="font-size: 23px;">
	<?
		$time = getdate();
		$first_day_of_week = mktime(0,0,0,$time['mon'], $time['mday']-$time['wday']+1, $time['year']);
		$last_day_of_week = mktime(0,0,0,$time['mon'], $time['mday']-$time['wday']+5, $time['year']);
		$first_day_name = date('d.m', $first_day_of_week);
		$last_day_name = date('d.m', $last_day_of_week);
		echo 'Szczęśliwy numerek na:<br /><br />';
		numerek();
	?>
	</span>
</div>
</td>
</tr>
</table>


<div style="clear: both;"> </div>

<?php
include "foot.php";
?>