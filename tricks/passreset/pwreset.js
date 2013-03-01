$(document).ready(function () {
	$("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	$('#pwsub')
		.button()
		.click(function(e) {
			e.preventDefault();
			var pwvars = 'reset:' + $('#resetvars').val() + ':' + $('#newpw').val();

			$('#pwsub').attr('disabled', 'disabled');
			$('#newpw').attr('disabled', 'disabled');
			$('#progressbar').show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'login',
				'args': pwvars,
			}, function (data) {
				$('#progressbar').hide(500);
				$('#newpw').val(null);
				$('#result').text(data.msg);
				if (!data.result) {
					$('#pwsub').removeAttr('disabled');
					$('#newpw').removeAttr('disabled');
				}
			});
		});
});
