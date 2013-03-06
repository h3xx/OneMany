$(document).ready(function () {
	$("#progressbar")
		.progressbar({
			value: false,
		});

	// post the verify request
	$.post('responder.php', {
		'method': 'tell',
		'func': 'verify',
		'args': $('#vfyvars').val(),
	}, function (data) {
		$('#progressbar').hide(500);
		if (data) {
			$('#result').text(data.msg);
		} else {
			$('#result').text('No data.');
		}
	});
});
