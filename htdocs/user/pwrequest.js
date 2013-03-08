$(document).ready(function () {
	var
	emsub = $('#emsub'),
	email = $('#email'),
	result = $('#result'),
	frmelems = $([]).add(emsub).add(email),

	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	frmelems
		.change(function (e) {
			var self = $(this);
			if (!self.val()) {
				self.addClass('ui-state-error');
			} else {
				self.removeClass('ui-state-error');
			}
		});

	emsub
		.button()
		.click(function(e) {
			e.preventDefault();

			var pwvars = 'request:' + email.val();

			frmelems.attr('disabled', 'disabled')
				.removeClass('ui-state-error');

			pbar.show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'user',
				'args': pwvars,
			}, function (data) {
				pbar.hide(500);
				email.val(null);
				result.text(data.msg);
				if (!data.result) {
					frmelems.removeAttr('disabled');
				}
			});
		});
});
