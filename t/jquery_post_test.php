<?php
if (!isset($_POST['q'])) {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="../htdocs/css/jquery-ui-1.10.0.custom.css" />
		<script type="text/javascript" src="../htdocs/js/jquery-1.9.1.js"></script>
		<script type="text/javascript" src="../htdocs/js/jquery-ui-1.10.0.custom.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$.post('<?= $_SERVER['PHP_SELF']; ?>', {"q":"hello world"}, function (data) {
				alert(data.msg);
			});
		});
		</script>
	</head>
	<body>
		<div id="out" />

	</body>
</html>
<?php
} else {
	#$in = json_decode($_POST['q']);
	header('Content-Type: application/json');
	print(json_encode(['msg' => 'hi there! you said: `' . $_POST['q'] . "'"]));
}
