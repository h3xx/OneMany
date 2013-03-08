$(document).ready(function () {
	var glist =
		$('#games')
		.addClass('ui-widget'),
	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	pbar.show(500);

	var allFields = $([]).add($('gamename'));

	$('#dialog-form')
		.dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				'Create Game': function () {
					var self = $(this),
					gn = $('#gamename').val();
					if (!gn) {
						$('#gamename').addClass('ui-state-error');
						return;
					}
					self.dialog('close');
				},
				'Cancel': function () {
					$(this).dialog('close');
				},
			},
			close: function () {
				allFields.val('').removeClass('ui-state-error');

			}
		});

	$('#create-game')
		.text('Create Game')
		.button()
		.click(function() {
			$('#dialog-form').dialog('open');
		});

	$.post('responder.php', {
		'method': 'ask',
		'func': 'listGames',
	}, function (data) {
		pbar.hide(500);
		if (data) {
			glist.empty();
			glist.append(
				$('<div></div>')
				.addClass('row ui-widget-header')
				.append(
					$('<div></div>')
					.addClass('cell')
					.text('ID'),

					$('<div></div>')
					.addClass('cell')
					.text('Name'),

					$('<div></div>')
					.addClass('cell')
					.text('Players'),

					$('<div></div>')
					.addClass('cell')
				)
			);
			for (var i in data) {
				var gdata = data[i];

				glist.append(
					$('<div></div>')
					.addClass('row ui-widget-content')
					.append(
						$('<div></div>')
						.addClass('cell')
						.text(gdata.id),

						$('<div></div>')
						.addClass('cell')
						.text(gdata.name),

						$('<div></div>')
						.addClass('cell')
						.text(gdata.sz),

						$('<button></button>')
						.addClass('cell')
						.button()
						.click(function () {
							var gid = $(this).data('gid');
							$.post('responder.php',
								{
									method: 'tell',
									func: 'join',
									args: gid,
								},
								function (data) {
									if (!data.result) {
										alert(data.msg);
									}
								});

						})
						.data('gid', gdata.id)
						.text('Join')
					)
				);
			}
		}
	});
});
