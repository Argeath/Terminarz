<?php
include "db.php";
?>
<html>
<head>
	<title>ZSŁ Terminarz</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Terminarz Łączności" />
	<meta name="keywords" content="ZSŁ, Łączność, terminarz" />

	<link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css" />

	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.min.css">
	<link rel="stylesheet" href="bootstrap-select.min.css">

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

	<script type="text/javascript"
     src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js">
    </script>

	<script type="text/javascript" src="bootstrap-select.min.js"></script>

	<style type="text/css" media="print">
		.no-print { display: none; }
	</style>


</head>
<body style="background: 0;">

<?
	$archiwumData = NULL;
	$archiwumDataGet = $_GET['archiwum'];
	if(isset($archiwumDataGet) && ! empty($archiwumDataGet))
	{
		$archiwumData = strtotime($archiwumDataGet);
	}

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
<div class="no-print">
	<a href="zastepstwa.php">Wróć</a>
</div>
</center>

<script>
	window.print();
</script>