<!DOCTYPE html>
<html>
<head>
<title>Password Reset Form</title>
<link rel="stylesheet" href="css/jquery-ui-1.10.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.1.min.js"></script>
<script type="text/javascript" src="pwreset.js"></script>
<link rel="stylesheet" type="text/css" href="pwform.css" />
</head>
<body>
<h1>Password Reset Form</h1>
<form>
<input id="resetvars" type="hidden" name="resetvars" value="<?= htmlentities(@$_GET['args']) ?>" />
<input id="newpw" type="text" name="newpw" placeholder="Enter a new password" />
<input id="pwsub" type="submit" name="pwsub" value="Change" />
</form>
<div id="progressbar"></div>
<div id="result"></div>
</body>
</html>
