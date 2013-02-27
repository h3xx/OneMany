<html>
<head>
<title>Password Reset Form</title>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="pwreset.js"></script>
</head>
<body>
<h1>Password Reset Form</h1>
<input id="resetvars" type="hidden" name="resetvars" value="<?= htmlentities(@$_GET['args']) ?>" />
<input id="newpw" type="text" name="newpw" placeholder="Enter a new password" />
<input id="pwsub" type="button" name="pwsub" value="Submit" />
<div id="result" />
</body>
</html>
