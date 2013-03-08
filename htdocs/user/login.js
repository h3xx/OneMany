$(document).ready(function () {
	var login = $('#login'),
	pw = $('#pw'),
	logname = $('#logname'),
	lia =
		$('#loggedinas')
		.text('Getting your user info.'),
	frmelems = $([]).add(login).add(pw).add(logname),

	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	$.post('responder.php',
		{
			'method': 'ask',
			'func': 'myUserInfo',
		}, function (data) {
			if (data) {
				if (data.name) {
					lia.text('You are logged in as ' + data.name);
				} else if (!data.result) {
					lia.text(data.msg);
				}
			} else {
				lia.text('Failure' + data);
			}
		});

	login
		.button()
		.click(function(e) {
			e.preventDefault();
			var loginvars = 'login:' + logname.val() + ':' + pw.val();

			frmelems.attr('disabled', 'disabled');
			pbar.show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'user',
				'args': loginvars,
			}, function (data) {
				pbar.hide(500);
				$('#result').text(data.msg);
				if (!data.result) {
					frmelems.removeAttr('disabled');
				} else {
					// hide now-inaccurate data
					lia.hide();
				}
			});
		});
});
