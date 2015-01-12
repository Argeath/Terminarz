<?php
	include("head.php");
	$date = time();
	$day = date('d', $date);
	$month = date('m', $date);
	$year = date('Y', $date);
	
	$zastid = $_POST['zastid'];
	$zast = getZastepstwo($zastid);
	if(!$zast)
		echo "<script>alert('Błąd.'); window.location.href = 'zastepstwa.php';</script>";
	
	if($user && $user['dostep'] == 2 && isset($_POST['data']) && isset($_POST['nauczyciel']) && isset($_POST['klasa']) && isset($_POST['godzina']) && isset($_POST['zawartosc']) && isset($_POST['zawartosc2']))
	{
		$check = checkZast($_POST['data'], $_POST['klasa']);
		if($check === true)
		{
			modyfZast($_POST['zastid'], $_POST['data'], $_POST['klasa'], $_POST['godzina'], $_POST['zawartosc'], $_POST['zawartosc2'], $_POST['nauczyciel'], $_POST['nauczycielna']);
			echo "<script>alert('Zmieniono.'); window.location.href = 'zastepstwa.php';</script>";
		} else {
			echo "<script>alert('".$check."'); window.location.href = 'zastepstwa.php';</script>";
		}
	}
	$data = strtotime($zast['data']);
	$data = date("Y-m-d", $data);
	$nau = $zast['nauczyciel'];
	$cl = $zast['klasa'];
	$godz = $zast['godzina_lekcyjna'];
	$nlna = $zast['nauczyciel_na'];
	$z = $zast['z'];
	$na = $zast['na'];
	?>

<script>
  $(function() {
    $( "#datepicker" ).datepicker({ 
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
        yearSuffix: ''}).datepicker("setDate", "<?=$data;?>");
  });
  </script>

<div style="height: 65px; width: 100%;"></div>
<table id="site">
<tr>
<td style="border: 0; width: auto; padding-left: 30px;">
<div id="content">
	<div style="width: 250px; margin: 0 auto;">
	<form method="post">
	<input type="hidden" name="zastid" value="<?=$zastid;?>"/>
	Data: <br /><input name="data" type="text" id="datepicker" style="width: 220px;"><br />
	Nauczyciel: <br /><select name="nauczyciel" class="selectpickernau">
	<?
	$nls = getNls();
	foreach($nls as $nl)
	{
		echo '<option value="'.$nl['id'].'">'.$nl['imie'].' '.$nl['nazwisko'].'</option>';
	}
	?>
	</select>
	
	
	Godzina lekcyjna: <br /><input name="godzina" type="text" style="width: 220px;" value="<?=$godz;?>"><br />
	Klasa: <br /><select name="klasa" class="selectpickercl">
	<?
	$classes = getClasses();
	foreach($classes as $class)
	{
		echo '<option value="'.$class['id'].'">'.$class['nazwa'].'</option>';
	}
	?>
	</select>
	
	Lekcja planowana:<br />
	<textarea name="zawartosc" style="max-width: 350px; min-width: 220px;"><?=$z;?></textarea>
	Nauczyciel zastępujący: <br /><select name="nauczycielna" class="selectpickernn">
	<option value="0"> </option>
	<?
	$nls = getNls();
	foreach($nls as $nl)
	{
		echo '<option value="'.$nl['id'].'">'.$nl['imie'].' '.$nl['nazwisko'].'</option>';
	}
	?>
	</select>
	Lekcja zmieniona na:<br />
	<textarea name="zawartosc2" style="max-width: 350px; min-width: 220px;"><?=$na;?></textarea>
	<button class="btn btn-large btn-default btn-block" type="submit">Zmień</button>
	</form>
	<script>$('.selectpickernau').val(<?=$nau;?>).selectpicker();</script>
	<script>$('.selectpickercl').val(<?=$cl;?>).selectpicker();</script>
	<script>$('.selectpickernn').val(<?=$nlna;?>).selectpicker();</script>
	<a href="zastepstwa.php">
	<?
	if($browser['name'] != 'Internet Explorer') 
		echo '<button class="btn btn-large btn-default btn-block">';
	echo 'Wróć';
	if($browser['name'] != 'Internet Explorer') 
		echo '</button>';
	echo '</a>';
	?>
	</div>
</div>
</td>
<td style="border: 0; width: 250px; padding-right: 10px;">
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