<?php
	include("head.php");
	if(!$user || $user['dostep'] != 2) exit;
	
	if(!empty($_POST['inputLogin']) && !empty($_POST['inputPassword']) && !empty($_POST['inputImie']) && !empty($_POST['inputNazwisko']))
	{
		addUser($_POST['inputLogin'], $_POST['inputPassword'], $_POST['inputImie'], $_POST['inputNazwisko']);
		echo "<script>alert('Dodano.');</script>";
	}
?>
<div style="height: 100px; width: 100%;"></div>
<div class="form_class">
<form action="users.php" method="post">
	<div class="form-group row">
		<? if($browser['name'] == 'Internet Explorer') echo "Login:<br />"; ?>
		<div class="col-lg-12">
		  <input type="text" class="form-control" name="inputLogin" placeholder="Login">
		</div>
	</div>
	<div class="form-group row">
		<? if($browser['name'] == 'Internet Explorer') echo "Hasło:<br />"; ?>
		<div class="col-lg-12">
		  <input type="password" class="form-control" name="inputPassword" placeholder="Hasło">
		</div>
	</div>
	<div class="form-group row">
		<? if($browser['name'] == 'Internet Explorer') echo "Imie:<br />"; ?>
		<div class="col-lg-6">
		  <input type="text" class="form-control" name="inputImie" placeholder="Imię">
		</div>
		<? if($browser['name'] == 'Internet Explorer') echo "Nazwisko:<br />"; ?>
		<div class="col-lg-6">
		  <input type="text" class="form-control" name="inputNazwisko" placeholder="Nazwisko">
		</div>
	</div>
	<button class="btn btn-large btn-default btn-block" type="submit">Dodaj</button>
</form>
<a href="index.php">
<?
if($browser['name'] != 'Internet Explorer') 
	echo '<button class="btn btn-large btn-default btn-block">';
echo 'Wróć';
if($browser['name'] != 'Internet Explorer') 
	echo '</button>';
echo '</a>';
?>
</div>
<?
include("foot.php");
?>