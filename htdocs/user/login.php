<!DOCTYPE html>
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
<input id="logname" type="text" name="logname" placeholder="Username" />
<input id="pw" type="password" name="pw" placeholder="Password" />
<input id="login" type="submit" name="login" value="Login" />
</form>
<div id="gotoreset"><a href="pwrequest.php" id="resetlink">Request a password reset</a></div>
<div id="progressbar"></div>
<div id="result"></div>
</body>
</html>
