$(document).ready(function () {

	var lia =
		$('#loggedinas')
		.text('Getting your user info.'),

	logout = $('#logout'),

	pbar = $('#progressbar')
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
				if (!data.name && !data.result) {
					// uh-oh, not logged in?
					lia.text(data.msg);
					logout.attr('disabled', 'disabled');
				} else {
					lia.text('You are logged in as ' + data.name);
				}
			} else {
				lia.text('Failure');
			}
		});

	logout
		.button()
		.click(function(e) {
			e.preventDefault();
			pbar.show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'user',
				'args': 'logout',
			}, function (data) {
				pbar.hide(500);
				$('#result').text(data.msg);
			});
		});
});
