<?php
	include("head.php");
	$date = time();
	$day = date('d', $date);
	$month = date('m', $date);
	$year = date('Y', $date);
	
	$przedmioty = array(
		'Religia/etyka',
		'Godzina z wychowawcą',
		'Język polski',
		'Język angielski',
		'Język niemiecki',
		'Wiedza o kulturze',
		'Historia',
		'Wiedza o społeczeństwie',
		'Podstawy przedsiębiorczości',
		'Geografia',
		'Biologia',
		'Chemia',
		'Fizyka',
		'Matematyka',
		'Informatyka',
		'Wychowanie fizyczne',
		'Edukacja dla bezpieczeństwa',
		'Wychowanie do życia w rodzinie',
		'Historia i społeczeństwo',
		'Matematyka (zakres rozszerzony)',
		'Fizyka (zakres rozszerzony)',
		'Podstawy techniki komputerowej',
		'Podstawy algorytmiki i programowania',
		'Język angielski zawodowy',
		'Podejmowanie i prowadzenie działalności gospodarczej',
		'Podstawy sieci komputerowych',
		'Urządzenia techniki komputerowej',
		'Systemy operacyjne',
		'Sieciowe systemy operacyjne rodziny Linux',
		'Sieciowe systemy operacyjne rodziny Windows',
		'Programowanie strukturalne i obiektowe',
		'Grafika komputerowa i aplikacje internetowe',
		'Bazy danych',
		'Nowoczesne technologie w informatyce',
		'Podstawy elektrotechniki',
		'Układy analogowe i cyfrowe',
		'Systemy pomiarowe',
		'Podstawy teleinformatyki',
		'Sieci teleinformatyczne',
		'Pracownia systemów komputerowych',
		'Pracownia elektryczna i elektroniczna',
		'Pracownia teleinformatyczna',
		'Sieci rozległe',
		'Bezpieczeństwo w sieciach teleinformatycznych',
		'Sieci komputerowe',
		'Sieciowe systemy operacyjne',
		'Komputerowe wspomaganie projektowania',
		'Podstawy elektrotechniki i elektroniki',
		'Przyrządy i metody pomiarowe',
		'Technologia i materiałoznawstwo elektryczne',
		'Układy analogowe',
		'Układy cyfrowe',
		'Konstrukcja i eksploatacja urządzeń elektronicznych',
		'Układy mikroprocesorowe',
		'Układy automatyki',
		'Dzialalność gopodarcza',
		'Instalowanie urządzeń elektronicznych w praktyce',
		'Pracownia elektrotechniki i elektroniki',
		'Pracownia konstrukcji i eksploatacji urządzeń elektronicznych',
		'Pracownia konstrukcji i eksploatacji urządzeń cyfrowych',
		'Innowacyjne technologie',
		'Praktyka zawodowa',
		'Przetwarzanie i obróbka sygnałów',
		'Specjalizacja',
		'Zajęcia specjalizacyjne',
		'Elektrotechnika i elektronika',
		'Język angielski dla teleinformatyków',
		'Urządzenia elektroniczne',
		'Wychowanie fizyczne/dz',
		'NI');
	sort($przedmioty);
	
	if($user && $user['dostep'] == 2 && isset($_POST['data']) && isset($_POST['nauczyciel']) && isset($_POST['klasa']) && isset($_POST['godzina']) && isset($_POST['zawartosc']) && isset($_POST['zawartosc2']))
	{
		$check = checkZast($_POST['data'], $_POST['klasa'], $_POST['godzina']);
		if($check === true)
		{
			dodajZast($_POST['data'], $_POST['klasa'], $_POST['godzina'], $_POST['zawartosc'], $_POST['zawartosc2'], $_POST['nauczyciel'], $_POST['nauczycielna']);
			echo "<script>alert('Dodano.'); window.location.href = 'zastepstwa.php';</script>";
		} else {
			echo "<script>alert('".$check."'); window.location.href = 'zastepstwa.php';</script>";
		}
	}
	
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
        yearSuffix: ''});
  });
  </script>

<div style="height: 65px; width: 100%;"></div>
<table id="site">
<tr>
<td style="border: 0; width: auto; padding-left: 30px;">
<div id="content">
	<div style="width: 250px; margin: 0 auto;">
	<form method="post">
	Data: <br /><input name="data" type="text" id="datepicker" style="width: 220px;"><br />
	Nauczyciel: <br /><select name="nauczyciel" class="selectpicker">
	<?
	$nls = getNls();
	foreach($nls as $nl)
	{
		echo '<option value="'.$nl['id'].'">'.$nl['imie'].' '.$nl['nazwisko'].'</option>';
	}
	?>
	</select>
	
	
	Godzina lekcyjna: <br /><input name="godzina" type="text" style="width: 220px;"><br />
	Klasa: <br /><select name="klasa" class="selectpicker">
	<?
	$classes = getClasses(true, 0);
	foreach($classes as $class)
	{
		echo '<option value="'.$class['id'].'">'.$class['nazwa'].'</option>';
	}
	?>
	</select>
	
	Lekcja planowana:<br />
	<select name="zawartosc" class="selectpicker">
		<? foreach($przedmioty as $przedmiot)
			echo '<option>'.$przedmiot.'</option>'; ?>
	</select>
	Nauczyciel zastępujący: <br /><select name="nauczycielna" class="selectpicker">
	<option value="0">---</option>
	<?
	$nls = getNls();
	foreach($nls as $nl)
	{
		echo '<option value="'.$nl['id'].'">'.$nl['imie'].' '.$nl['nazwisko'].'</option>';
	}
	?>
	</select>
	Lekcja zmieniona na:<br />
	<textarea name="zawartosc2" style="max-width: 350px; min-width: 220px;"></textarea>
	<button class="btn btn-large btn-default btn-block" type="submit">Wpisz</button>
	</form>
	<script>$('.selectpicker').selectpicker();</script>
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