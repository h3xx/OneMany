$(document).ready(function () {
	$("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	$('#usub')
		.button()
		.click(function(e) {
			var elems = [
				$('#name'),
				$('#email'),
				$('#pass'),
			];
			e.preventDefault();
			var pwvars = [
				$('#name').val(),
				$('#email').val(),
				$('#pass').val(),
			];

			for (var i in elems) {
				elems[i].attr('disabled', 'disabled');
			}

			$('#progressbar').show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'signup',
				'args': pwvars,
			}, function (data) {
				$('#progressbar').hide(500);
				$('#result').text(data.msg);
				if (!data.result) {
					for (var i in elems) {
						elems[i]
							.removeAttr('disabled');
					}
				} else {
					for (var i in elems) {
						elems[i]
							.val(null);
					}
				}
				$('#usub').removeAttr('disabled');

			});
		});
});
