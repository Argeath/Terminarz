<?php include("db.php"); ?>
<html>
<head>
	<title>ZSŁ Terminarz</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Terminarz Zespołu Szkół Łączności w Gdańsku" />
	<meta name="keywords" content="ZSŁ, Łączność, terminarz, Gdańsk, Zespół, Szkół, Łączności, sprawdzian, PPK, kartkówka, ogłoszenia" />
	
	<link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css" />
	
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.min.css">
	<link rel="stylesheet" href="bootstrap-select.min.css">

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script> 

	<script type="text/javascript" src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js"></script>

	<script type="text/javascript" src="bootstrap-select.min.js"></script>
	
	<link rel="apple-touch-icon" sizes="57x57" href="/favicons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/favicons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/favicons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/favicons/apple-touch-icon-152x152.png">
	<link rel="icon" type="image/png" href="/favicons/favicon-196x196.png" sizes="196x196">
	<link rel="icon" type="image/png" href="/favicons/favicon-160x160.png" sizes="160x160">
	<link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="/favicons/mstile-144x144.png">
</head>
<body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-12606596-5', 'auto');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
<div id="naglowek">
	<h1 class="center">Terminarz</h1>
	<a href="kontakt.php"><img src="http://s-trojmiasto.pl/zdj/obiekty/logo/1/4275.jpg" class="logo"/></a>
</div>
<?php
	if(!isset($_SESSION) || !isset($_SESSION['pass']) || !isset($_SESSION['login']))
	{
		if (isset($_POST['login']) && isset($_POST['password']))
		{
			$user = getUser($_POST['login'], sha1($_POST['password']));
			if(!$user || $user['dostep'] < 1)
				echo "<script>alert('Błędna nazwa użytkownika bądź hasło!');</script>";
			else {
				$_SESSION['pass'] = $user['haslo'];
				$_SESSION['login'] = $user['login'];
			}
		}
	} else {
		$user = getUser($_SESSION['login'], $_SESSION['pass']);
		if( ! $user || $user['dostep'] < 1)
		{
			echo "<script>alert('Błędna nazwa użytkownika bądź hasło!');</script>";
			$user = null;
		}
	}
?></div>