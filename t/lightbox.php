<html>
<head>
<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="js/jquery.lightbox-0.5.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.lightbox-0.5.css" />
</head>
<body>
<a href="images/wheelbarrow.jpg" rel="lightbox"><img src="images/wheelbarrow.jpg" alt="wheelbarrow" /></a>
<script type="text/javascript">
$(function() {
	// Use this example, or...
	$('a[rel*=lightbox]').lightBox(); // Select all links that contains lightbox in the attribute rel
	// This, or...
	//$('#gallery a').lightBox(); // Select all links in object with gallery ID
	// This, or...
	//$('a.lightbox').lightBox(); // Select all links with lightbox class
	// This, or...
	//$('a').lightBox(); // Select all links in the page
	// ... The possibility are many. Use your creative or choose one in the examples above
});
</script>
</body>
</html>
