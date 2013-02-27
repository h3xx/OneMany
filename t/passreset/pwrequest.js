$(document).ready(function () {
	$('#emsub').click(function() {
		var pwvars = 'request:' + $('#email').val();
		//alert(pwvars);

		$.post('responder.php', {
			'method': 'tell',
			'func': 'login',
			'args': pwvars,
		}, function (data) {
			$('#email').val(null);
			$('#result').text(data.msg);
		});
	});
});
