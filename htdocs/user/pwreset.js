$(document).ready(function () {
	var frmelems = [
		$('#pwsub'),
		$('#newpw'),
		$('#newpwv'),
	];

	if (!$('#resetvars').val()) {
		for (var i in frmelems) {
			frmelems[i].attr('disabled', 'disabled');
		}
		$('#result').text('No data for password reset (did you click the link in your email?)');
	}

	$("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	$('#pwsub')
		.button()
		.click(function(e) {
			e.preventDefault();
			if ($('#newpw').val() != $('#newpwv').val()) {
				$('#result').text('Passwords do not match.');
				return;
			}

			var pwvars = 'reset:' + $('#resetvars').val() + ':' + $('#newpw').val();

			for (var i in frmelems) {
				frmelems[i].attr('disabled', 'disabled');
			}
			$('#progressbar').show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'login',
				'args': pwvars,
			}, function (data) {
				$('#progressbar').hide(500);
				$('#newpw').val(null);
				$('#newpwv').val(null);
				$('#result').text(data.msg);
				if (!data.result) {
					for (var i in frmelems) {
						frmelems[i].removeAttr('disabled');
					}
				}
			});
		});
});
