<!DOCTYPE html>
<html>
<head>
<title>Verify Your Email</title>
<link rel="stylesheet" href="css/jquery-ui-1.10.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.1.min.js"></script>
<script type="text/javascript" src="usverify.js"></script>
<link rel="stylesheet" type="text/css" href="usform.css" />
</head>
<body>
<h1>Verify Your Email</h1>
<form>
<input id="vfyvars" type="hidden" name="vfyvars" value="<?= htmlentities(@$_GET['args']) ?>" />
</form>
<div id="progressbar"></div>
<div id="result"></div>
</body>
</html>
