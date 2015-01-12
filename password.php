<?php
	include("head.php");
	if(!$user)
	{
		echo "<script>alert('Musisz być zalogowany!'); window.location.href = 'index.php';</script>";
		printLogin("index.php");
		exit;
	}
	if(isset($_POST['oldpassword']) && isset($_POST['spassword']) && isset($_POST['npassword']))
	{
		if(sha1($_POST['oldpassword']) == $user['haslo'])
		{
			if($_POST['spassword'] == $_POST['npassword'])
			{
				setUserPassword($user['id'], $_POST['spassword']);
				session_destroy();
				echo "<script>alert('Hasło zostało zmienione.'); window.location.href = 'index.php';</script>";
			} else {
				echo "<script>alert('Hasła się nie zgadzają.');</script>";
			}
		} else {
			echo "<script>alert('Złe hasło.');</script>";
		}
	}
	
?>
<div style="height: 100px; width: 100%;"></div>
<div class="form_class">
	<form action="password.php" method="post">
		<h3>Zmiana hasła</h3>
		<? if($browser['name'] == 'Internet Explorer') echo "Stare hasło:<br />"; ?>
		<div class="form-group row">
			<input type="password" class="form-control" name="oldpassword" placeholder="Stare hasło"/>
		</div>
		<? if($browser['name'] == 'Internet Explorer') echo "Nowe hasło:<br />"; ?>
		<div class="form-group row">
			<input type="password" class="form-control" name="spassword" placeholder="Nowe hasło"/>
		</div>
		<? if($browser['name'] == 'Internet Explorer') echo "Powtórz hasło:<br />"; ?>
		<div class="form-group row">
			<input type="password" class="form-control" name="npassword" placeholder="Powtórz nowe hasło"/>
		</div>
		<button class="btn btn-large btn-default btn-block" type="submit">Zmień hasło</button>
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