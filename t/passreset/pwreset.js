$(document).ready(function () {
	$('#pwsub').click(function() {
		var pwvars = $('#resetvars').val() + ':' + $('#newpw').val();

		$.post('responder.php', {
			'method': 'tell',
			'func': 'login',
			'args': pwvars,
		}, function (data) {
			$('#result').text(data.msg);
		});
	});
});
