<?php
	include("head.php");
	$date = time(); 
	
	if(! $user) exit;

	if(isset($_GET['month']))
		$miesiac = (int)$_GET['month'];
	if(isset($_GET['year']))
		$rok = (int)$_GET['year'];
	
	$day = date('d', $date);
	if(isset($miesiac))
	{
		$mstr = ($miesiac <= 9) ? '0' : '';
		$month = $mstr.''.$miesiac;
	}
	else {
		$month = date('m', $date);
		$miesiac = $month;
	}
		
	if(isset($rok))
		$year = $rok;
	else
	$year = date('Y', $date);

	$first_day = mktime(0,0,0,$month, 1, $year);
	$title = dateV('F', $first_day);
	
	$day_of_week = date('D', $first_day); 
	$days_in_month = cal_days_in_month(0, $month, $year);

	switch($day_of_week){ 
		case "Mon": $blank = 0; break; 
		case "Tue": $blank = 1; break; 
		case "Wed": $blank = 2; break; 
		case "Thu": $blank = 3; break; 
		case "Fri": $blank = 4; break; 
		case "Sat": $blank = 5; break; 
		case "Sun": $blank = 6; break; 
	}
	
	$days = 1;
?>
<div style="height: 65px; width: 100%;"></div>
<table id="site">
<tr>
<td style="border: 0; width: auto; padding-left: 30px;">
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
		$linkUp = "moje.php?month=".$mies."".$ro;
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
		$linkDown = "moje.php?month=".$mies."".$ro;
		
		if($browser['name'] == 'Internet Explorer') {
	?>
		<td id="arrow_left_ie"><a href="<? echo $linkDown; ?>"><</a></td>
		<td colspan="2" style="border-right: 0; border-left: 0;"><span class="miesiac">MOJE WPISY - <? echo $title; ?></span></td>
		<td style="border-left: 0; border-right: 0;"><span class="miesiac"><? echo $year; ?></span></td>
		<td id="arrow_right_ie"><a href="<? echo $linkUp; ?>">></a></td>
	<?
	} else {
	?>
	<td colspan="5">
		<span id="arrow_left"><a href="<? echo $linkDown; ?>"><</a></span>
		<span class="miesiac"><? echo 'MOJE WPISY - '.$title.' '.$year; ?></span>
		<span id="arrow_right"><a href="<? echo $linkUp; ?>">></a></span>
	</td>
	<?
		}
	?>
	</tr>
	<tr><td><b>Poniedziałek</b></td><td><b>Wtorek</b></td><td><b>Środa</b></td><td><b>Czwartek</b></td><td><b>Piątek</b></td></tr>
	<tr><?
	
		$wpisy = getMojeWpisy($user);
	
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
				echo "<td class='td'><div class='day'><b>".$day_num."</b></div>";
				echo "<div style='clear: both;'> </div>";
				if(isset($wpisy) && !empty($wpisy[$tid2]))
				{
					foreach($wpisy[$tid2] as $wpis)
					{
						printMojWpis($wpis);
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
	<a href="index.php">Wróć do terminarza</a>
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
			echo '<button class="btn btn-large btn-primary btn-block">';
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
	include("foot.php");
?>