$(document).ready(function () {
	var frmelems = [
		$('#logname'),
		$('#pw'),
		$('#login'),
	];

	$("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	$('#login')
		.button()
		.click(function(e) {
			e.preventDefault();
			var loginvars = 'login:' + $('#logname').val() + ':' + $('#pw').val();

			for (var i in frmelems) {
				frmelems[i].attr('disabled', 'disabled');
			}
			$('#progressbar').show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'user',
				'args': loginvars,
			}, function (data) {
				$('#progressbar').hide(500);
				$('#result').text(data.msg);
				if (!data.result) {
					for (var i in frmelems) {
						frmelems[i].removeAttr('disabled');
					}
				}
			});
		});
});
