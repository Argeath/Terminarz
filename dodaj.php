<?php
include "head.php";
if (!$user) {
	exit;
}

if (isset($_POST['data']) && isset($_POST['klasa']) && isset($_POST['kategoria']) && isset($_POST['zawartosc']) && isset($_POST['przedmiot'])) {
	$check = checkWpis($_POST['data'], $_POST['klasa'], $_POST['kategoria'], $_POST['przedmiot']);
	if ($check === true) {
		dodajWpis($_POST['data'], $_POST['klasa'], $_POST['kategoria'], $_POST['zawartosc'], $_POST['przedmiot'], $user['id']);
		echo "<script>alert('Dodano.'); window.location.href = 'index.php?klasa=" . $_POST['klasa'] . "';</script>";
	} else {
		echo "<script>alert('" . $check . "'); window.location.href = 'index.php?klasa=" . $_POST['klasa'] . "';</script>";
	}
}
if ((int) $_GET['id'] == 0) {
	exit;
}

$id = $_GET['id'];
$dzien = substr($id, 0, 2);
$miesiac = substr($id, 2, 2);
$rok = substr($id, 4);
$klasa = getClass((int) $_GET['klasa']);

/*
Skrypt do generowania listy przedmiotów z tabeli przedmiotów szkoły (taka lista, którą dostawałem na początku roku szkolnego).
Javascript + JQuery
id tabeli: t
id diva do któego wypisze wyniki: result


function capitaliseFirstLetter(string)
{
return string.charAt(0).toUpperCase() + string.slice(1);
}
$(function() {
$("#t tr").each(function() {
$(this).children('td:nth-child(2)').each(function() {
$("#result").append("'" + capitaliseFirstLetter($(this).text().trim()) + "',<br />");
});
});
});


Jak nie działa to zawsze można zmieniać ręcznie :)
 */

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
	'Wychowanie fizyczne/dz');
sort($przedmioty);
?>
<div style="height: 100px; width: 100%;"></div>
<div class="form_class">
	<form action="dodaj.php" method="post">
	Data: <? echo $dzien.'-'.$miesiac.'-'.$rok; ?><br />
	<input type="hidden" name="data" value="<? echo $id; ?>">
	<input type="hidden" id="klas" name="klasa" value="<? echo $klasa['id']; ?>">

	<? if($klasa['specjalna'] == 1) { ?>
		Kategoria: Ogłoszenie<br />
		<input type="hidden" name="kategoria" value="OGL"/>
		<input type="hidden" name="przedmiot" value="Ogłoszenie"/>

	<? } else { ?>
		<div id="klasaDiv">Klasa: <? echo $klasa['nazwa']; ?><br /></div>
		<div id="przedmiotDiv">Przedmiot:
			<select name="przedmiot" id="przed">
				<? foreach($przedmioty as $przedmiot)
					echo '<option>'.$przedmiot.'</option>'; ?>
			</select>
		</div>
		Kategoria:
		<select name="kategoria" id="kat">
			<option value="ZAD">Zadanie</option>
			<option value="KARTK">Kartkówka</option>
			<option value="PPK">PPK</option>
			<option value="INNE">Inne</option>
			<? if($user['dostep'] == 2) echo '<option value="POW">Powiadomienie</option>'; ?>
		</select>
	<? } ?>
	Zawartość:
	<textarea name="zawartosc" style="max-width: 230px;"></textarea>
	<button class="btn btn-large btn-default btn-block" type="submit">Wpisz</button>
	</form>

	<a href="index.php?klasa=<? echo $klasa['id']; ?>">
	<?
	if($browser['name'] != 'Internet Explorer')
		echo '<button class="btn btn-large btn-default btn-block">';
	echo 'Wróć';
	if($browser['name'] != 'Internet Explorer')
		echo '</button>';
	echo '</a>';
	?>
</div>

<script>
	$("#przed").selectpicker();
	$("#kat").selectpicker().change(function () {
    $( "select option:selected" ).each(function() {
		if($( this ).val() == 'POW')
		{
			$("#przedmiotDiv").hide();
			$("#klasaDiv").hide();
		} else {
			$("#przedmiotDiv").show();
			$("#klasaDiv").show();
		}
    });
  }).change();
</script>

<?

	include("foot.php");
?>