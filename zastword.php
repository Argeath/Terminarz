<?php
include "db.php";

$archiwumData = NULL;
$archiwumDataGet = $_GET['archiwum'];
if (isset($archiwumDataGet) && !empty($archiwumDataGet)) {
	$archiwumData = strtotime($archiwumDataGet);
}

if (isset($archiwumData)) {
	$time = getdate($archiwumData);
} else {
	$time = getdate();
}

if ($time['mon'] < 10) {
	$time['mon'] = '0' . $time['mon'];
}

$timeStr = $time['mday'] . '-' . $time['mon'] . '-' . $time['year'];
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=zastepstwa-" . $timeStr . ".doc");
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<style>
	table, th, td {
		border: 1px #000 solid;
		border-collapse:collapse;
		text-align: center!important;
		vertical-align: top;
	}
	</style>
</head>
<body style="background: 0;">

<?
	if(isset($archiwumData))
		$time = getdate($archiwumData);
	else
		$time = getdate();

	$zastepstwa = array();
	$przesunieciednia = 0;
	for($i=0; $i < 2; $i++)
	{
		$day = mktime(0,0,0,$time['mon'], $time['mday']+$i+$przesunieciednia, $time['year']);
		$dw = date( "w", $day);
		if($dw == 6)
		{
			++$przesunieciednia;
			$day = mktime(0,0,0,$time['mon'], $time['mday']+$i+$przesunieciednia, $time['year']);
		}
		$dw = date( "w", $day);
		if($dw == 0)
		{
			++$przesunieciednia;
			$day = mktime(0,0,0,$time['mon'], $time['mday']+$i+$przesunieciednia, $time['year']);
		}
		$dw = date( "w", $day);
		$ret = getZast($day);
		$title = dateV('l', $day);
		$nrdnia = $time['mday'] + $i+$przesunieciednia;
		$nrmies = $time['mon'];
		if($nrmies < 10)
			$nrmies = '0'.$nrmies;
		$zastepstwa[$i] .=  '<b>'.$nrdnia.'.'. $nrmies .' ('. $title .')</b><br /><br />';
		$zastepstwa[$i] .= '<table style="width:100%;">
			<tr>
				<td style="width: 20%;">Nauczyciel nieobecny</td>
				<td style="width: 8%;">Godzina lekcyjna</td>
				<td style="width: 7%;">Klasa</td>
				<td>Lekcja planowana</td>
				<td style="width: 20%;">Nauczyciel zastępujący</td>
				<td>Lekcja zmieniona na</td>
			</tr>';
		if(mysql_num_rows($ret) != 0)
		{
			$assocs = mysql_fetch_rowsarr($ret);
			foreach($assocs as $assoc)
			{
				$nl = getUserInfo($assoc['nauczyciel']);
				if($nl)
				{
					$nlC = countInArray($assocs, "nauczyciel", $nl['id']);
					$nlT = $nl['nazwisko'].' '.$nl['imie'];
					$nlText = (strpos($zastepstwa[$i], "<td rowspan='".$nlC."'>".$nlT."</td>")==false) ? "<td rowspan='".$nlC."'>".$nlT."</td>" : "";
				} else
					$nlText = "<td></td>";

				$nlna = getUserInfo($assoc['nauczyciel_na']);
				$nlnaT = "---";
				if($nlna)
				{
					$nlnaT = $nlna['nazwisko'].' '.$nlna['imie'];
				}
				$zastepstwa[$i] .= '<tr>
				'.$nlText.'
				<td>'.$assoc['godzina_lekcyjna'].'</td>
				<td>'.getClassName($assoc['klasa']).'</td>
				<td>'.$assoc['z'].'</td>
				<td>'.$nlnaT.'</td>
				<td>'.$assoc['na'].'</td>
				</tr>';
			}
			$zastepstwa[$i] .= '</table><hr /><br />';
		} else {
			$zastepstwa[$i] .= '<tr><td colspan="7">Brak zastępstw.</td></tr></table><hr /><br />';
		}
	}
?>
<center>
<?
foreach($zastepstwa as $zast)
{
	echo $zast;
	echo '<DIV style="page-break-after:always"></DIV>';
}
?>
</center>