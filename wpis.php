<?php
	include("head.php");
	$id = (int)$_GET['id'];
	$wpis = getWpis($id);
	if(!$wpis)
	{
		die("Błąd");
	}
	$klasa = getClass($wpis['klasa']);
	if(isset($user) && isset($_GET['action']) && $_GET['action'] == 'usun' && ($wpis['dodajacy'] == $user['id'] || $user['dostep'] == 2))
	{
		usunWpis($id);
		echo "<script>alert('Usunięto.'); window.location.href = 'index.php?klasa=".$wpis['klasa']."';</script>";
	}
?>

<div class="form_class" style="margin-top: 100px;">
<? if($wpis['kategoria'] == 'POW') { 
		$dodano = date("d.m.Y", strtotime($wpis['dodane']));
?>
	Data: <? echo $wpis['data']; ?><br />
	Dodano: <?= $dodano; ?><br />
	Klasa: Wszystkie<br />
	Kategoria: <? echo catToName($wpis['kategoria']); ?><br />
  <? } elseif($wpis['kategoria'] == 'OGL') { ?>
    Data: <?= $wpis['data']; ?> <br />
	Dodano: <?= $dodano; ?><br />
    Kategoria: <?= catToName($wpis['kategoria']); ?><br />
<? } else { ?>
	Data: <? echo $wpis['data']; ?><br />
	Dodano: <?= $dodano; ?><br />
	Klasa: <? echo $klasa['nazwa']; ?><br />
	Przedmiot: <? echo $wpis['przedmiot']; ?><br />
	Kategoria: <? echo catToName($wpis['kategoria']); ?><br />
<? } ?>
	<p style="word-wrap:break-word;">Zawartość: <? echo $wpis['text']; ?></p><br />
	<? if(isset($user) && ($wpis['dodajacy'] == $user['id'] || $user['dostep'] == 2))
		echo '<a href="wpis.php?id='.$id.'&action=usun" class="btn btn-large btn-default btn-block">Usuń wpis</a>';
	?>
	<a href="#" id="back" class="btn btn-large btn-default btn-block">Wróć</a>
</div>

<script>
	$("#back").click(function(event) {
		event.preventDefault();
		history.back(1);
	});
</script>
	
<?
	
	include("foot.php");
?>