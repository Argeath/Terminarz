<?php
include "head.php";
$date = time();
$day = date('d', $date);
$month = date('m', $date);
$year = date('Y', $date);
$archiwumDataPost = $_POST['archiwum'];
$archiwumData = NULL;
if (isset($archiwumDataPost) && !empty($archiwumDataPost)) {
	$archiwumData = strtotime($archiwumDataPost);
}

if (isset($_POST['usun']) && isset($_POST['zastid']) && $user && $user['dostep'] == 2) {
	$zastid = (int) $_POST['zastid'];
	if ($zastid > 0) {
		usunZast($zastid);
	}
}
if (isset($archiwumData)) {
	$time = getdate($archiwumData);
} else {
	$time = getdate();
}

$zastepstwa = array();
$przesunieciednia = 0;
for ($i = 0; $i < 2; $i++) {
	$day = mktime(0, 0, 0, $time['mon'], $time['mday']+$i + $przesunieciednia, $time['year']);
	$dw = date("w", $day);
	if ($dw == 6) {
		++$przesunieciednia;
		$day = mktime(0, 0, 0, $time['mon'], $time['mday']+$i + $przesunieciednia, $time['year']);
	}
	$dw = date("w", $day);
	if ($dw == 0) {
		++$przesunieciednia;
		$day = mktime(0, 0, 0, $time['mon'], $time['mday']+$i + $przesunieciednia, $time['year']);
	}
	$dw = date("w", $day);
	$ret = getZast($day);
	$title = dateV('l', $day);
	$nrdnia = date('d', $day);
	$nrmies = date('m', $day);
	$zastepstwa[$i] .= '<b>' . $nrdnia . '.' . $nrmies . ' (' . $title . ')</b><br /><br />';
	$edytuj = ($user && $user['dostep'] == 2) ? "<td style='width: 18%;'>Opcje</td>" : "";
	$zastepstwa[$i] .= '<table style="width:100%;">
			<tr>
				<td style="width: 20%;">Nauczyciel nieobecny</td>
				<td style="width: 8%;">Godzina lekcyjna</td>
				<td style="width: 7%;">Klasa</td>
				<td>Lekcja planowana</td>
				<td style="width: 20%;">Nauczyciel zastępujący</td>
				<td>Lekcja zmieniona na</td>
				' . $edytuj . '
			</tr>';
	if (mysql_num_rows($ret) != 0) {
		$assocs = mysql_fetch_rowsarr($ret);
		foreach ($assocs as $assoc) {
			$edytuj = ($user && $user['dostep'] == 2) ? "<td><form method='post' action='modyfzast.php' style='float: left;'><input type='hidden' name='zastid' value='" . $assoc['id'] . "'/><input name='modyf' type='submit' value='Modyfikuj' class='btn btn-warning'/></form><form method='post' style='float: left;'><input type='hidden' name='zastid' value='" . $assoc['id'] . "'/><input name='usun' type='submit' value='Usuń' class='btn btn-danger'/></form></td>" : "";
			$nl = getUserInfo($assoc['nauczyciel']);
			if ($nl) {
				$nlC = countInArray($assocs, "nauczyciel", $nl['id']);
				$nlT = $nl['nazwisko'] . ' ' . $nl['imie'];
				$nlText = (strpos($zastepstwa[$i], "<td rowspan='" . $nlC . "'>" . $nlT . "</td>") == false) ? "<td rowspan='" . $nlC . "'>" . $nlT . "</td>" : "";
			} else {
				$nlText = "<td></td>";
			}

			$nlna = getUserInfo($assoc['nauczyciel_na']);
			$nlnaT = "---";
			if ($nlna) {
				$nlnaT = $nlna['nazwisko'] . ' ' . $nlna['imie'];
			}
			$zastepstwa[$i] .= '<tr>
				' . $nlText . '
				<td>' . $assoc['godzina_lekcyjna'] . '</td>
				<td>' . getClassName($assoc['klasa']) . '</td>
				<td>' . $assoc['z'] . '</td>
				<td>' . $nlnaT . '</td>
				<td>' . $assoc['na'] . '</td>
				' . $edytuj . '
				</tr>';
		}
		$zastepstwa[$i] .= '</table><hr /><br />';
	} else {
		$zastepstwa[$i] .= '<tr><td colspan="7">Brak zastępstw.</td></tr></table><hr /><br />';
	}
}

?>

<div style="height: 65px; width: 100%;"></div>
<table id="site">
<tr>
<td style="border: 0; width: auto; padding-left: 30px;">
<div id="content">
	<form method="post">
		Archiwum: <input type="text" id="datepicker" name="archiwum"/>
	</form>
	<?
	foreach($zastepstwa as $zast)
	{
		echo $zast;
	}
	?>
	<?
	if($user['dostep']==2)
	{
		echo '<a href="nowezast.php">Dodaj zastępstwo</a><br /><br />';
		if( ! empty($archiwumDataPost))
			echo '<a href="zastdruk.php?archiwum='.$archiwumDataPost.'">Drukuj</a><br />';
		else
			echo '<a href="zastdruk.php">Drukuj</a><br />';

		if( ! empty($archiwumDataPost))
			echo '<a href="zastword.php?archiwum='.$archiwumDataPost.'">Zapisz do Microsoft Word</a>';
		else
			echo '<a href="zastword.php">Zapisz do Microsoft Word</a>';
	}
	?>
	<script type="text/javascript">
		$(function() {
			$.datepicker.setDefaults({
                closeText: 'Zamknij',
                prevText: '&#x3c;Poprzedni',
                nextText: 'Następny&#x3e;',
                currentText: 'Dziś',
                monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
                'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
                monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
                'Lip','Sie','Wrz','Pa','Lis','Gru'],
                dayNames: ['Niedziela','Poniedzialek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
                dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
                dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
                weekHeader: 'Tydz',
                dateFormat: 'yy-mm-dd',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''});
			$('#datepicker').datepicker().datepicker( "option", "dateFormat", "dd-mm-yy").datepicker( "setDate", "<?=$archiwumDataPost;?>" ).on('change', function() { this.form.submit(); });
		});
	</script>
</div>
</td>
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