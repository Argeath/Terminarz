<?php
	include("head.php");
?>

<div class="form_class" style="margin-top: 100px; width: 500px;">
	<h3>Zespół Szkół Łączności w Gdańsku</h3><br />
	Wykonał <a href="https://www.facebook.com/dkinal"><b>Dominik Kinal (4A)</b></a> dla <a href="https://www.facebook.com/zslsu"><b>Samorządu Uczniowskiego</b></a>.<br />
	Kontakt: <i>kinaldominik@gmail.com</i><br /><br />
	<a href="#" id="back"><button class="btn btn-large btn-primary btn-block">Wróć</button></a>
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