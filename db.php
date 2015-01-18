<?php

/*
Terminarz
Wykonanie przez Dominik Kinal (kinaldominik@gmail.com) dla Zespół Szkół Łączności w Gdańsku ( Technikum nr 4 w Gdańsku )

TODO:
- lepszy system błędów (np. przy dodawaniu wpisów lub zastępstw)
- lepsze wspomaganie dla starszych przeglądarek ( ehh, ten Internet Explorer ;c )
- przenieść przedmioty do bazy danych( z plików dodaj.php i nowezast.php )
- czyszczenie bazy danych(wpisów, zastępstw i numerków), bo się dość szybko rozrastają
- limity nowego hasła przy zmianie (niepuste, min. 3 znaki itd.)

INFO:
- nie ma kategorii Sprawdzian ( ma nie być )
- Konto "usunięte" ma pole `dostep` = 0
- Konto administratora ma pole `dostep` = 2
- W przypadku zmiany listy przedmiotów, trzeba to zrobić w plikach dodaj.php i nowezast.php
- lista przedmiotów w nowezast.php ma dodatkowy przedmiot NI - Nauczanie indywidualne
- po zmianie hasła przez użytkownika, stare hasło zostaje zapisane do pola `starehaslo`, gdyby trzeba było przywrócić hasło, po przypadkowej zmianie
- Szczęśliwy numerek działa w ten sposób, że generuje losowo numery ( 1 - 30 (reszta ma pecha ;c )) dla całego przyszłego tygodnia,
wyświetla numerki tylko na "dzisiaj",
dany numer jest raz na miesiąc ( po zużyciu puli numerów, ustawia wszystkim pole `reset` na 1 i leci pula od początku)
- Łączenia klas ( dla zastępstw, np. na specjalizacjach łączone grupy z klas 3A i 3B) robimy w ten sposób:
- dodajemy nową klasę (np. 3AB) z polem `laczona` = 1 ( Dzięki temu nie wyświetla się w liście klas terminarza)
- Administrator ma możliwość dodania powiadomienia w terminarzu dla wszystkich klas poprzez kategorie "Powiadomienie"

CHANGELOG:
23.10.2014 - dodano "klase" Ogłoszenia - ma pole `specjalna` = 1 (żeby się wyświetlało jako 1)

 */

error_reporting(0);
$conn = mysql_connect("localhost", "login", "haslo") or die(mysql_error());
$db = mysql_select_db("database", $conn) or die(mysql_error());
mysql_query('SET NAMES "utf8"');
session_start();

$arrLocales = array('pl_PL', 'pl', 'Polish_Poland.28592');
setlocale(LC_ALL, $arrLocales);
date_default_timezone_set('Europe/Warsaw');

$browser = getBrowser();


/*
Funkcje do bazy danych
 */

// Logowanie

function getUser($login, $haslo) {
	$login = sanitize($login);
	$ret = mysql_query("SELECT * FROM `term_accounts` WHERE `login` = '" . $login . "' AND `haslo` = '" . $haslo . "' LIMIT 1") or die(mysql_error());
	$array = mysql_fetch_assoc($ret);
	return !empty($array) ? $array : false;
}

// Pobieranie konkretnego konta po ID

function getUserInfo($id) {
	$id = (int) $id;
	$ret = mysql_query("SELECT * FROM `term_accounts` WHERE `id` = " . $id . " LIMIT 1") or die(mysql_error());
	$array = mysql_fetch_assoc($ret);
	return !empty($array) ? $array : false;
}

// Dodawanie nowych kont

function addUser($login, $pass, $imie, $nazwisko) {
	$register_data = array();
	$register_data['login'] = mysql_real_escape_string($login);
	$register_data['haslo'] = sha1($pass);
	$register_data['imie'] = mysql_real_escape_string($imie);
	$register_data['nazwisko'] = mysql_real_escape_string($nazwisko);

	$fields = '`' . implode('`, `', array_keys($register_data)) . '`';
	$data = '\'' . implode('\', \'', $register_data) . '\'';

	mysql_query("INSERT INTO `term_accounts` ($fields) VALUES ($data)") or die(mysql_error());
	return true;
}

// Pobieranie wszystkich kont

function getUsersData() {
	$query = mysql_query("SELECT * FROM `term_accounts` ORDER BY `nazwisko` ASC;") or die(mysql_error());
	$array = array();
	while ($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}
	return $array;
}

// Zmiana hasła
// TODO: dodać jakieś limity hasła(nie puste, min. 3 litery itd.)

function setUserPassword($id, $pass) {
	$pass = sha1($pass);
	mysql_query("UPDATE `term_accounts` SET `haslo` = '" . $pass . "' WHERE `id` = " . $id . " LIMIT 1") or die(mysql_error());
}

// Pobieranie listy klas
// $laczone = true dla zastępstw

function getClasses($laczone = false, $specjalna = 1) {
	$laczoneQ = " WHERE";
	if (!$laczone) {
		$laczoneQ = "WHERE `laczona` = 0 AND";
	}

	$query = mysql_query("SELECT * FROM `term_klasy` " . $laczoneQ . " `specjalna` <= " . $specjalna . " ORDER BY `specjalna` DESC, `nazwa` ASC;") or die(mysql_error());
	$array = array();
	while ($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}
	return $array;
}

// "Moje wpisy"

function getMojeWpisy($user) {
	$query = mysql_query("SELECT * FROM `term_wpisy` WHERE `dodajacy` = " . $user['id'] . " ORDER BY `data` ASC;") or die(mysql_error());
	$array = array();
	while ($row = mysql_fetch_assoc($query)) {
		$array[$row['data']][] = $row;
	}

	return $array;
}

function printMojWpis($wpis) {
	$klasa = getClass($wpis['klasa']);
	echo '<a href="wpis.php?id=' . $wpis['id'] . '"><div class="wpis ' . $wpis['kategoria'] . '">
			<b>' . $klasa['nazwa'] . '</b><br />
			<span style="font-size: 12px;"><b><i>' . catToName($wpis['kategoria']) . '</i></b><br />
			<p style="">' . $wpis['text'] . '</p></span>
		  </div></a>';
}

// Pobieranie listy kont z pominięciem konta `admin` o `id` = 1

function getNls()//Nauczyciele
{
	$query = mysql_query("SELECT * FROM `term_accounts` WHERE `id` > 1 AND `dostep` > 0 ORDER BY `nazwisko`, `imie` ASC;") or die(mysql_error());
	$array = array();
	while ($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}

	return $array;
}

// Pobieranie konkretnej klasy

function getClass($id) {
	$id = (int) $id;
	$ret = mysql_query("SELECT * FROM `term_klasy` WHERE `id` = " . $id . " LIMIT 1") or die(mysql_error());
	$array = mysql_fetch_assoc($ret);
	return !empty($array) ? $array : false;
}

// Pobieranie nazwy klasy (1A itd.)

function getClassName($id) {
	$id = (int) $id;
	$ret = mysql_query("SELECT * FROM `term_klasy` WHERE `id` = " . $id . " LIMIT 1") or die(mysql_error());
	$array = mysql_fetch_assoc($ret);
	return !empty($array) ? $array['nazwa'] : false;
}

// Pobieranie wszystkich wpisów klasy
// TODO: optymalizacja - pobieranie tylko z konkretnego miesiąca

function getWpisy($klasa) {
	$query = mysql_query("SELECT * FROM `term_wpisy` WHERE `klasa` = " . $klasa . " OR `kategoria` = 'POW' ORDER BY `data` ASC;") or die(mysql_error());
	$array = array();
	while ($row = mysql_fetch_assoc($query)) {
		$array[$row['data']][] = $row;
	}

	return $array;
}

// Pobieranie konkretnego wpisu z terminarza

function getWpis($id) {
	$id = (int) $id;
	$ret = mysql_query("SELECT * FROM `term_wpisy` WHERE `id` = " . $id . " LIMIT 1") or die(mysql_error());
	$array = mysql_fetch_assoc($ret);
	return !empty($array) ? $array : false;
}

/*
Sprawdzanie wpisów przy dodawaniu
- 3 PPK na tydzień
- 1 PPK na dzień
 */

function checkWpis($data, $klasa, $kategoria, $przedmiot) {
	$dzien = substr($data, 0, 2);
	$miesiac = substr($data, 2, 2);
	$rok = substr($data, 4);
	$dat = strtotime($rok . '-' . $miesiac . '-' . $dzien);
	$klasa = (int) $klasa;
	if ($kategoria == 'PPK') {
		$da = $rok . "-" . $miesiac . "-" . $dzien;

		$ret = mysql_query("SELECT * FROM `term_wpisy` WHERE `data` = '" . $da . "' AND `klasa` = " . $klasa . " AND `kategoria` = 'PPK' AND `przedmiot` != '" . $przedmiot . "'") or die(mysql_error());
		$array = mysql_fetch_assoc($ret);
		if (!empty($array)) {
			return "W tym dniu jest już wpisane PPK.";
		}

		$grup = false;
		$ret = mysql_query("SELECT * FROM `term_wpisy` WHERE `data` = '" . $da . "' AND `klasa` = " . $klasa . " AND `kategoria` = 'PPK' AND `przedmiot` = '" . $przedmiot . "'") or die(mysql_error());
		$array = mysql_fetch_assoc($ret);
		if (!empty($array)) {
			$grup = true;
		}

		$d = date("w", $dat);

		$dn = $rok . "-" . $miesiac . "-" . ($dzien - $d);
		$dk = $rok . "-" . $miesiac . "-" . ($dzien+(5 - $d));

		$ret = mysql_query("SELECT COUNT(*) FROM `term_wpisy` WHERE `data` > '" . $dn . "' AND `data` <= '" . $dk . "' AND `kategoria` = 'PPK' AND `klasa` = " . $klasa . " AND `grupowy` = 0") or die(mysql_error());
		$array = mysql_fetch_assoc($ret);
		if (!empty($array)) {
			if ($array["COUNT(*)"] >= 3 && !$grup) {
				return "W tym tygodniu są już wpisane 3 PPK.";
			}
		}
	}

	return true;
}

// Dodawanie wpisu

function dodajWpis($data, $klasa, $kategoria, $zawartosc, $przedmiot, $dodaj) {
	$dzien = substr($data, 0, 2);
	$miesiac = substr($data, 2, 2);
	$rok = substr($data, 4);
	$klasa = (int) $klasa;
	$kategoria = sanitize($kategoria);
	$zawartosc = trim($zawartosc);
	$dodaj = (int) $dodaj;

	// Możliwość dodania wielu PPK z jednego przedmiotu jednego dnia
	// Aby mogłby być wpisy przedmiotów dzielonych na grupy z innymi nauczycielami
	$grupowy = 0;
	if ($kategoria == 'PPK') {
		$ret = mysql_query("SELECT * FROM `term_wpisy` WHERE `data` = '" . $rok . "-" . $miesiac . "-" . $dzien . "' AND `klasa` = " . $klasa . " AND `kategoria` = 'PPK' AND `przedmiot` = '" . $przedmiot . "'") or die(mysql_error());
		$array = mysql_fetch_assoc($ret);
		if (!empty($array)) {
			$grupowy = 1;
		}
	}

	mysql_query("INSERT INTO `term_wpisy` (`data`, `klasa`, `kategoria`, `text`, `przedmiot`, `dodajacy`, `grupowy`) VALUES ('" . $rok . "-" . $miesiac . "-" . $dzien . "', " . $klasa . ", '" . $kategoria . "', '" . $zawartosc . "', '" . $przedmiot . "', " . $dodaj . ", " . $grupowy . ")") or die(mysql_error());
}

// Pobieranie wszystkich zastępstw z danego dnia

function getZast($day) {
	return mysql_query("SELECT * FROM `term_zastepstwa` WHERE DATE(`data`) = DATE(FROM_UNIXTIME(" . $day . ")) ORDER BY `nauczyciel` ASC");
}

// Pobieranie konkretnego zastępstwa

function getZastepstwo($id) {
	$id = (int) $id;
	$ret = mysql_query("SELECT * FROM `term_zastepstwa` WHERE `id` = " . $id . " LIMIT 1") or die(mysql_error());
	$array = mysql_fetch_assoc($ret);
	return !empty($array) ? $array : false;
}

// Sprawdzanie poprawności dodawania/modyfikowania zastępstwa

function checkZast($data, $klasa) {
	$dat = strtotime($data);
	$klasa = (int) $klasa;
	if ($klasa == 0) {
		return "Źle wybrana klasa.";
	}

	if (!$dat) {
		return "Źle wybrana data.";
	}

	return true;
}

// Dodawanie zastępstw

function dodajZast($data, $klasa, $godzina, $zawartosc, $zawartosc2, $nl, $nlna) {
	$godzina = trim($godzina);
	$klasa = (int) $klasa;
	$nl = (int) $nl;
	$nlna = (int) $nlna;
	$zawartosc = trim($zawartosc);
	$zawartosc2 = trim($zawartosc2);
	mysql_query("INSERT INTO `term_zastepstwa` (`data`, `nauczyciel`, `klasa`, `godzina_lekcyjna`, `z`, `na`, `nauczyciel_na`) VALUES ('" . $data . "', " . $nl . ", " . $klasa . ", '" . $godzina . "', '" . $zawartosc . "', '" . $zawartosc2 . "', " . $nlna . ")") or die(mysql_error());
}

// Modyfikowanie zastępstw

function modyfZast($id, $data, $klasa, $godzina, $zawartosc, $zawartosc2, $nl, $nlna) {
	$id = (int) $id;
	$godzina = trim($godzina);
	$klasa = (int) $klasa;
	$nl = (int) $nl;
	$nlna = (int) $nlna;
	$zawartosc = trim($zawartosc);
	$zawartosc2 = trim($zawartosc2);
	mysql_query("UPDATE `term_zastepstwa` SET `data`='" . $data . "', `nauczyciel`=" . $nl . ", `klasa`=" . $klasa . ", `godzina_lekcyjna`='" . $godzina . "', `z`='" . $zawartosc . "', `na`='" . $zawartosc2 . "', `nauczyciel_na`=" . $nlna . " WHERE `id`=" . $id . "") or die(mysql_error());
}

// Usuwanie wspisu z terminarza

function usunWpis($id) {
	$id = (int) $id;
	mysql_query("DELETE FROM `term_wpisy` WHERE `id` = " . $id . "") or die(mysql_error());
}

// Usuwanie zastępstwa

function usunZast($id) {
	$id = (int) $id;
	mysql_query("DELETE FROM `term_zastepstwa` WHERE `id` = " . $id . "") or die(mysql_error());
}

/*  W przypadku nowych nauczycieli dodajemy do tabeli term_accounts same pola `imie` i `nazwisko`.
Po uruchomieniu funkcji genLoginPass() skrypt(po jednorazowym wejściu na strone) sam generuje loginy i hasła nowym kontom. (pole `hasl` to niezakodowane hasło)
 */

function genLoginPass() {
	$query = mysql_query("SELECT * FROM `term_accounts` WHERE `login` IS NULL;") or die(mysql_error());
	$array = array();
	while ($row = mysql_fetch_assoc($query)) {
		$array[] = $row;
	}

	foreach ($array as $row) {
		$id = (int) $row['id'];
		$im = cleanString($row['imie'], true);
		$na = cleanString($row['nazwisko'], true);
		$im = substr($im, 0, 3);
		$na = substr($na, 0, 3);
		$login = $im . '' . $na;
		$haslo = rand(100000, 999999);
		$pass = sha1($haslo);
		$q = "UPDATE `term_accounts` SET `hasl` = '" . $haslo . "', `haslo` = '" . $pass . "', `login` = '" . $login . "' WHERE `id` = " . $id . " LIMIT 1";
		var_dump($q);
		mysql_query($q) or die(mysql_error());
	}
}

// System szczęśliwych numerków
// TODO: niegenerowanie w dni wolne(wakacje, ferie, święta)
//		 automatyczne czyszczenie po wakacjach

function losujNumerek() {
	$rett = mysql_query("SELECT COUNT(*) AS ile FROM `numerek` WHERE `reset` = 0") or die(mysql_error());
	$ar = mysql_fetch_array($rett);
	if ($ar['ile'] >= 30) {
		mysql_query("UPDATE `numerek` SET `reset` = 1 WHERE `reset` = 0") or die(mysql_error());
	}

	$nr = 0;
	while ($nr == 0) {
		$rand = rand(1, 30);
		$ret2 = mysql_query("SELECT * FROM `numerek` WHERE `numer` = " . $rand . " AND `reset` = 0") or die(mysql_error());
		$arr = mysql_fetch_array($ret2);
		if (!$arr || empty($arr)) {
			$nr = $rand;
		}
	}

	return $nr;
}

function generujNumerekNaTydzien() {
	$time = getdate();
	$first_day_of_week = mktime(0, 0, 0, $time['mon'], $time['mday']-$time['wday'], $time['year']);
	for ($i = 1; $i < 6; $i++) {
		$day = mktime(0, 0, 0, $time['mon'], $time['mday']-$time['wday']+$i, $time['year']);
		$ret = mysql_query("SELECT `numer` FROM `numerek` WHERE DATE(`data`) = DATE(FROM_UNIXTIME(" . $day . "))") or die(mysql_error());
		$assoc = mysql_fetch_assoc($ret);
		if ($assoc == false) {
			$los = losujNumerek();
			mysql_query("INSERT INTO `numerek` (`numer`, `data`) VALUES (" . $los . ", DATE_FORMAT(FROM_UNIXTIME(" . $day . "), '%Y:%m:%d'))") or die(mysql_error());
		}
	}
}

function numerek() {
	generujNumerekNaTydzien();
	$time = getdate();
	for ($i = 0; $i < 1; $i++) {
		$day = mktime(0, 0, 0, $time['mon'], $time['mday']+$i, $time['year']);
		$dayDate = getDate($day);
		$ret = mysql_query("SELECT `numer` FROM `numerek` WHERE DATE(`data`) = DATE(FROM_UNIXTIME(" . $day . "))") or die(mysql_error());
		$assoc = mysql_fetch_assoc($ret);
		if ($assoc) {
			$title = dateV('l', $day);
			echo $dayDate['mday'] . '.' . sprintf("%02d", $dayDate['mon']) . ' (' . $title . ')<br /><h1>' . $assoc['numer'] . '</h1><br />';
		}
	}
}

function mysql_fetch_rowsarr($result, $numass = MYSQL_ASSOC) {
	$got = array();

	if (mysql_num_rows($result) == 0) {
		return $got;
	}

	mysql_data_seek($result, 0);

	while ($row = mysql_fetch_array($result, $numass)) {
		array_push($got, $row);
	}

	return $got;
}

function countInArray($array, $field, $value) {
	$count = 0;
	if (empty($array)) {
		return 0;
	}

	foreach ($array as $a) {
		if ($a[$field] == $value) {
			$count++;
		}
	}

	return $count;
}

function dniWMiesiacu($month, $year)//Unused
{
	$array = array();

	$days = 1;
	$first_day = mktime(0, 0, 0, $month, 1, $year);
	$title = dateV('f', $first_day);

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

	$day_num = 1;
	if ($blank < 5) {
		$days = $blank + 1;
	} else {
		$day_num = 8 - $blank;
	}
	while ($day_num <= $days_in_month) {
		$day_str = ($day_num <= 9) ? '0' : '';
		$tid = $day_str . '' . $day_num . '' . $month . '' . $year;
		$tid2 = $year . '-' . $month . '-' . $day_str . '' . $day_num;
		$czasDnia = strtotime($tid2 . " 18:00:00");
		if ($days < 6) {
			$array[] = array('text' => $day_str . '' . $day_num . ' ' . $title . ' ' . $year, 'data' => $tid);
		}
		$day_num++;

		$days++;
		if ($days > 7) {
			$days = 1;
		}
	}

	return $array;
}

// Funkcje do wyświetlania (wiem, że nie powinno tak się robić)
// Dodatkowe wspomaganie dla IE (ehh, te stare komputery w szkole)

function printLogin($link) {
	$browser = getBrowser();
	echo '<form action="' . $Link . '" method="post" class="form_login">
		<h3>Logowanie dla nauczycieli</h3>';
	if ($browser['name'] == 'Internet Explorer') {
		echo 'Login:<br />';
	}

	echo '<div class="form-group row">
			<input type="text" class="form-control" name="login" placeholder="Nazwa użytkownika"/>
		</div>';
	if ($browser['name'] == 'Internet Explorer') {
		echo 'Hasło:<br />';
	}

	echo '
		<div class="form-group row">
			<input type="password" class="form-control" name="password" placeholder="Hasło"/>
		</div>
		<button class="btn btn-large btn-default btn-block" type="submit">Loguj</button>
		</form>';
}

function printClass() {
	echo '<form method="GET" action="index.php" class="form_class">
		<h3>Wybór klasy</h3>
		<div class="form-group row">
			<select name="klasa" class="selectpicker">';
	$classes = getClasses();
	foreach ($classes as $class) {
		echo '<option value="' . $class['id'] . '">' . $class['nazwa'] . '</option>';
	}

	echo '</select>
		</div>
		<button class="btn btn-large btn-default btn-block" type="submit">Wybierz</button>
		</form><script>$(".selectpicker").selectpicker();</script>';
}

function printMiniClass($klasa) {
	$browser = getBrowser();
	echo '<form method="GET" action="index.php" class="form_mclass form-inline" style="">
			<select id="smallclass" name="klasa" class="selectpicker" onchange="this.form.submit()">';
	$classes = getClasses();
	foreach ($classes as $class) {
		echo '<option value="' . $class['id'] . '">' . $class['nazwa'] . '</option>';
	}

	echo "</select>";
	echo "</form>";
	if ($browser['name'] != 'Internet Explorer') {
		echo "<script>$('.selectpicker').val(" . $klasa . ").selectpicker();</script>";
	} else {
		echo "<script>document.getElementById('smallclass').value = '" . $klasa . "';</script>";
	}
}

function printWpis($wpis) {
	if ($wpis['kategoria'] == 'POW' || $wpis['kategoria'] == 'OGL') {
		echo '<a href="wpis.php?id=' . $wpis['id'] . '"><div class="wpis ' . $wpis['kategoria'] . '">
				<b>' . catToName($wpis['kategoria']) . '</b><br />
				<p style="">' . $wpis['text'] . '</p></span>
			  </div></a>';

	} else {
		echo '<a href="wpis.php?id=' . $wpis['id'] . '"><div class="wpis ' . $wpis['kategoria'] . '">
				<b>' . $wpis['przedmiot'] . '</b><br />
				<span style="font-size: 12px;"><b><i>' . catToName($wpis['kategoria']) . '</i></b><br />
				<p style="">' . $wpis['text'] . '</p></span>
			  </div></a>';
	}
}

// Funkcje pomocnicze (prosto z internetu :) )

function dateV($format, $timestamp = null) {
	$to_convert = array(
		'l' => array('dat' => 'N', 'str' => array('Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Niedziela')),
		'F' => array('dat' => 'n', 'str' => array('styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień')),
		'f' => array('dat' => 'n', 'str' => array('stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia')),
	);
	if ($pieces = preg_split('#[:/.\-, ]#', $format)) {

		if ($timestamp === null) {$timestamp = time();}
		foreach ($pieces as $datepart) {
			if (array_key_exists($datepart, $to_convert)) {
				$replace[] = $to_convert[$datepart]['str'][(date($to_convert[$datepart]['dat'], $timestamp) - 1)];
			} else {
				$replace[] = date($datepart, $timestamp);
			}
		}
		$result = strtr($format, array_combine($pieces, $replace));
		return $result;
	}
}

function getBrowser() {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$bname = 'Unknown';
	$platform = 'Unknown';
	$version = "";

	//First get the platform?
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'mac';
	} elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	}

	// Next get the name of the useragent yes separately and for good reason.
	if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
		$bname = 'Internet Explorer';
		$ub = "MSIE";
	} elseif (preg_match('/Firefox/i', $u_agent)) {
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
	} elseif (preg_match('/Chrome/i', $u_agent)) {
		$bname = 'Google Chrome';
		$ub = "Chrome";
	} elseif (preg_match('/Safari/i', $u_agent)) {
		$bname = 'Apple Safari';
		$ub = "Safari";
	} elseif (preg_match('/Opera/i', $u_agent)) {
		$bname = 'Opera';
		$ub = "Opera";
	} elseif (preg_match('/Netscape/i', $u_agent)) {
		$bname = 'Netscape';
		$ub = "Netscape";
	}

	// Finally get the correct version number.
	$known = array('Version', $ub, 'other');
	$pattern = '#(?<browser>' . join('|', $known) .
	')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	if (!preg_match_all($pattern, $u_agent, $matches)) {
		// we have no matching number just continue
	}

	// See how many we have.
	$i = count($matches['browser']);
	if ($i != 1) {
		//we will have two since we are not using 'other' argument yet
		//see if version is before or after the name
		if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
			$version = $matches['version'][0];
		} else {
			$version = $matches['version'][1];
		}
	} else {
		$version = $matches['version'][0];
	}

	// Check if we have a number.
	if ($version == null || $version == "") {$version = "?";}

	return array(
		'userAgent' => $u_agent,
		'name' => $bname,
		'version' => $version,
		'platform' => $platform,
		'pattern' => $pattern,
	);
}

function catToName($cat) {
	switch ($cat) {
		case "ZAD":return "Zadanie";
		case "SPR":return "Sprawdzian";
		case "KARTK":return "Kartkówka";
		case "INNE":return "Inne";
		case "POW":return "Powiadomienie";
		case "OGL":return "Ogłoszenie";
		default:return $cat;
	}
}

function sanitize($data) {
	return htmlentities(strip_tags(mysql_real_escape_string($data)));
}

function curPageName() {
	return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
}

function strptime_array_to_timestamp($array) {
	if (!empty($array['unparsed'])) {
		return false;
	}

	return mktime(isset($array['tm_hour']) ? $array['tm_hour'] : null,

		isset($array['tm_min']) ? $array['tm_min'] : null,
		isset($array['tm_sec']) ? $array['tm_sec'] : null,
		isset($array['tm_mon']) ? 1 + $array['tm_mon'] : null,
		isset($array['tm_mday']) ? $array['tm_mday'] : null,
		isset($array['tm_year']) ? 1900 + $array['tm_year'] : null);
}

function cleanString($string, $toLower = true, $space = '_') {

	$chars = array(

		chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',

		chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',

		chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',

		chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',

		chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',

		chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',

		chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',

		chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',

		chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',

		chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',

		chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',

		chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',

		chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',

		chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',

		chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',

		chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',

		chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',

		chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',

		chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',

		chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',

		chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',

		chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',

		chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',

		chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',

		chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',

		chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',

		chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',

		chr(195) . chr(191) => 'y',

		chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',

		chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',

		chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',

		chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',

		chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',

		chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',

		chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',

		chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',

		chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',

		chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',

		chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',

		chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',

		chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',

		chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',

		chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',

		chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',

		chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',

		chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',

		chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',

		chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',

		chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',

		chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',

		chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',

		chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',

		chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',

		chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',

		chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',

		chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',

		chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',

		chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',

		chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',

		chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',

		chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',

		chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',

		chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',

		chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',

		chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',

		chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',

		chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',

		chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',

		chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',

		chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',

		chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',

		chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',

		chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',

		chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',

		chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',

		chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',

		chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',

		chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',

		chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',

		chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',

		chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',

		chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',

		chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',

		chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',

		chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',

		chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',

		chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',

		chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',

		chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',

		chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',

		chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',

		chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',

		chr(226) . chr(130) . chr(172) => 'E',

		chr(194) . chr(163) => '',

		' ' => $space,

	);

	$string = strtr($string, $chars);

	if ($toLower && function_exists('mb_strtolower')) {

		return mb_strtolower($string);

	} else {

		return strtolower($string);

	}

}

/*
Struktura bazy danych:

CREATE TABLE IF NOT EXISTS `numerek` (
`id` int(11) NOT NULL auto_increment,
`numer` int(3) NOT NULL,
`data` timestamp NOT NULL default CURRENT_TIMESTAMP,
`reset` int(2) NOT NULL default '0',
PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `term_accounts` (
`id` int(30) NOT NULL auto_increment,
`login` varchar(30) default NULL,
`haslo` varchar(255) default NULL COMMENT 'kodowane w SHA1',
`imie` varchar(30) NOT NULL,
`nazwisko` varchar(30) NOT NULL,
`dostep` int(11) NOT NULL default '1',
`starehaslo` text,
`hasl` varchar(11) default NULL,
PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `term_klasy` (
`id` int(11) NOT NULL auto_increment,
`nazwa` varchar(11) collate utf8_unicode_ci NOT NULL,
`laczona` int(2) NOT NULL default '0',
PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `term_wpisy` (
`id` int(255) NOT NULL auto_increment,
`data` date NOT NULL,
`klasa` int(11) NOT NULL,
`dodajacy` int(11) NOT NULL,
`kategoria` enum('ZAD','KARTK','SPR','PPK','INNE','POW') collate utf8_unicode_ci NOT NULL,
`text` text collate utf8_unicode_ci NOT NULL,
`przedmiot` varchar(50) character set utf8 NOT NULL,
`dodane` timestamp NOT NULL default CURRENT_TIMESTAMP,
`grupowy` int(2) NOT NULL default '0',
PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS `term_zastepstwa` (
`id` int(11) NOT NULL auto_increment,
`data` timestamp NOT NULL default CURRENT_TIMESTAMP,
`nauczyciel` int(11) NOT NULL,
`klasa` int(11) NOT NULL,
`godzina_lekcyjna` varchar(5) collate utf8_unicode_ci NOT NULL,
`z` text character set utf8 NOT NULL,
`na` text character set utf8,
`nauczyciel_na` int(11) default NULL,
PRIMARY KEY  (`id`)
);
 */
?>
