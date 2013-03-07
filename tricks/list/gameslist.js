$(document).ready(function () {
	var glist = $('#games'),
	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.hide();

	pbar.show(500);

	$.post('responder.php', {
		'method': 'ask',
		'func': 'listGames',
	}, function (data) {
		pbar.hide(500);
		if (data) {
			glist.empty();
			glist.append(
				$('<div></div>')
				.addClass('row header')
				.append(
					$('<div></div>')
					.addClass('cell')
					.text('ID'),

					$('<div></div>')
					.addClass('cell')
					.text('Name'),

					$('<div></div>')
					.addClass('cell')
					.text('Players')
				)
			);
			for (var i in data) {
				var gdata = data[i];

				glist.append(
					$('<div></div>')
					.addClass('row')
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
