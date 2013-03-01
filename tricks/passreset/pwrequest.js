$(document).ready(function () {
	$("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	$('#emsub')
		.button()
		.click(function(e) {
			e.preventDefault();
			var pwvars = 'request:' + $('#email').val();

			$('#emsub').attr('disabled', 'disabled');
			$('#email').attr('disabled', 'disabled');
			$('#progressbar').show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'login',
				'args': pwvars,
			}, function (data) {
				$('#progressbar').hide(500);
				$('#email').val(null);
				$('#result').text(data.msg);
				if (!data.result) {
					$('#emsub').removeAttr('disabled');
					$('#email').removeAttr('disabled');
				}
			});
		});
});
