<?php session_start(); ?><!DOCTYPE html>
<html>
<head>
<title>Login Form</title>
<link rel="stylesheet" href="../css/jquery-ui-1.10.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.10.1.min.js"></script>
<script type="text/javascript" src="login.js"></script>
<link rel="stylesheet" type="text/css" href="userform.css" />
</head>
<body>
<h1>Login Form</h1>
<form>
<div><a href="../list/">Go to game list</a></div>
<div id="loggedinas"></div>
<input id="logname" type="text" name="logname" placeholder="Username" value="<?= htmlentities(@$_SESSION['user_name']); ?>" />
<input id="pw" type="password" name="pw" placeholder="Password" />
<input id="login" type="submit" name="login" value="Login" />
</form>
<span id="gotoreset"><a href="pwrequest.php" id="resetlink">Reset your password</a></span> &middot;
<span id="gotosignup"><a href="signup.php" id="signuplink">Sign up</a></span>
<div id="progressbar"></div>
<div id="result"></div>
</body>
</html>
