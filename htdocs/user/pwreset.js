$(document).ready(function () {
	var
	pwsub = $('#pwsub'),
	newpw =	$('#newpw'),
	newpwv = $('#newpwv'),
	resetvars = $('#resetvars'),
	result = $('#result'),

	frmelems = $([]).add(pwsub).add(newpw).add(newpwv),
	passelems = $([]).add(newpw).add(newpwv),

	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	if (!resetvars.val()) {
		frmelems.attr('disabled', 'disabled');
		$('#result').text('No data for password reset (did you click the link in your email?)');
	}

	frmelems
		.change(function (e) {
			var self = $(this);
			if (!self.val()) {
				self.addClass('ui-state-error');
			} else {
				self.removeClass('ui-state-error');
			}
		});

	passelems
		.change(function (e) {
			if (newpw.val() && newpwv.val()) {
				if (newpw.val() != newpwv.val()) {
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

	pwsub
		.button()
		.click(function(e) {
			e.preventDefault();
			if (newpw.val() != newpwv.val()) {
				result.text('Passwords do not match.');
				passelems.addClass('ui-state-error');
				return;
			}

			var pwvars = 'reset:' + resetvars.val() + ':' + newpw.val();

			frmelems.attr('disabled', 'disabled')
				.removeClass('ui-state-error');

			pbar.show(500);

			$.post('responder.php', {
				'method': 'tell',
				'func': 'user',
				'args': pwvars,
			}, function (data) {
				pbar.hide(500);
				newpw.val(null);
				newpwv.val(null);
				result.text(data.msg);
				if (!data.result) {
					frmelems.removeAttr('disabled');
				}
			});
		});
});
