$(document).ready(function () {
	var glist =
		$('#games')
		.addClass('ui-widget'),
	lia =
		$('#loggedinas')
		.text('Getting your user info.'),
	gli =
		$('#gotologin')
		.hide(),

	loggedIn = false,

	gamename = $('#gamename'),
	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.show(500),

	allFields = $([]).add(gamename),

	dialogform = $('#dialog-form')
		.dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				'Create Game': function () {
					var self = $(this),
					gn = gamename.val();
					if (!gn) {
						gamename.addClass('ui-state-error');
						return;
					}
					// FIXME : do something with this information
					self.dialog('close');
				},
				'Cancel': function () {
					$(this).dialog('close');
				},
			},
			close: function () {
				allFields.val('').removeClass('ui-state-error');

			}
		}),

	creategame = $('#create-game')
		.text('Create Game')
		.button()
		.click(function() {
			dialogform.dialog('open');
		}),

	// the elements that are to be disabled if not logged in
	nliDisable = $([]).add(creategame),

// joinGame {{{
	joinGame = function (gid) {
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

	},
// joinGame }}}

// createGamesList {{{
	createGamesList = function () {
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
					var gdata = data[i], joinbtn;

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

							joinbtn =
							$('<button></button>')
							.data('gid', gdata.id)
							.addClass('cell')
							.button()
							.click(function () {
								joinGame($(this).data('gid'));
							})
							.text('Join')
						)
					);

					nliDisable.add(joinbtn);
					if (!loggedIn) {
						joinbtn.attr('disabled', 'disabled');
					}
				}
			}

			/* this should work but it doesn't */
			if (!loggedIn) {
				gli.show();
				nliDisable.attr('disabled', 'disabled');
			}
		});
	};
// createGamesList }}}

	$.post('responder.php',
		{
			'method': 'ask',
			'func': 'myUserInfo',
		}, function (data) {
			if (data) {
				if (data.name) {
					lia.text('You are logged in as ' + data.name);
					loggedIn = true;
				} else if (!data.result) {
					lia.text(data.msg);
				}
			} else {
				lia.text('Failure' + data);
			}
			createGamesList();
		});


});

// vi: fdm=marker
