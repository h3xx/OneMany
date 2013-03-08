$(document).ready(function () {
	var
	usub = $('#usub'),
	name = $('#name'),
	email = $('#email'),
	pass = $('#pass'),
	passv = $('#passv'),

	result = $('#result'),

	frmelems = $([]).add(name).add(email).add(pass).add(passv),
	passelems = $([]).add(pass).add(passv),

	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	passelems
		.change(function (e) {
			if (pass.val() && passv.val()) {
				if (pass.val() != passv.val()) {
					passelems
						//.removeClass('ui-state-highlight')
						.addClass('ui-state-error');
				} else {
					passelems
						//.addClass('ui-state-highlight')
						.removeClass('ui-state-error');
				}
			}
		});

	frmelems
		.change(function (e) {
			var self = $(this);
			if (!self.val()) {
				self.addClass('ui-state-error');
			} else {
				self.removeClass('ui-state-error');
			}
		});

	usub
		.button()
		.click(function(e) {
			e.preventDefault();
			if (pass.val() != passv.val()) {
				result.text('Passwords do not match.');
				passelems.addClass('ui-state-error');
				return;
			}

			var pwvars = [
				name.val(),
				email.val(),
				pass.val(),
			];

			frmelems.attr('disabled', 'disabled')
				.removeClass('ui-state-error');

			pbar.show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'signup',
				'args': pwvars,
			}, function (data) {
				pbar.hide(500);
				result.text(data.msg);
				if (!data.result) {
					frmelems.removeAttr('disabled');
				} else {
					frmelems.val(null).removeClass('ui-state-error');
					usub.attr('disabled', 'disabled');
				}
			});
		});
});
