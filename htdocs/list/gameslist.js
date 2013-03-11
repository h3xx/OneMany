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
	gameUrl = window.location.href + '../game/',

	gamename = $('#gamename'),
	dlgRules = $('#dialog-rules'),
	pbar = $("#progressbar")
		.progressbar({
			value: false,
		})
		.show(500),

	allFields = $([]).add(gamename),
	ruleFields = $([]),

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
					gamename.removeClass('ui-state-error');

					var createData = {
						rules: collateRules(),
						name: gn,
					};

					sendCreateGame(createData, function () {self.dialog('close');});
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

// makeRules {{{
	// make rules dialogs
	makeRules = function () {
		$.post('responder.php',
			{
				method: 'ask',
				func: 'rules',
			},
			function (data) {
				if (data) {
					for (var i in data) {
						var rud = data[i],
						rulabel = $('<label></label>')
							.attr('for', rud.name)
							.addClass('sublabel rulelabel')
							.text(rud.name),
						ruinput = $('<input></input>')
							.addClass('ruleinput')
							.attr('name', rud.name)
							.attr('title', rud.desc)
							.attr('value', rud.val),
						rubuff = $('<div></div>')
							.addClass('rule')
							.append(rulabel, ruinput);

						dlgRules.append(rubuff);
						ruleFields.add(ruinput);
					}
				}
				// activate fancy tooltips
				$(document).tooltip();
			});
	},
// makeRules }}}

// collateRules {{{
	collateRules = function () {
		// FIXME : gather rules into an array
		return {
			'starting_cash': 1499,
		};
	},

// collateRules }}}

// joinGame {{{
	joinGame = function (gid) {
		$.post('responder.php',
			{
				method: 'tell',
				func: 'game',
				args: 'join:' + gid,
			},
			function (data) {
				if (data.result) {
					window.location = gameUrl;
				} else {
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
				glist
				.empty()
				.append(
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
							.text(gdata.sz + '/' + gdata.sz_max),

							joinbtn =
							$('<button></button>')
							.data('gid', gdata.id)
							.addClass('cell')
							.button()
							.click(function () {
								joinGame($(this).data('gid'));
							})
							.text(gdata.ingame ? 'Back to Game' : 'Join')
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

// sendCreateGame {{{
	sendCreateGame = function (data, successCallback) {
		$.post('responder.php',
			{
				method: 'tell',
				func: 'create',
				args: data
			},
			function (data) {
				if (data.result) {
					if (successCallback) {
						successCallback();
					}
					window.location = gameUrl;
				} else {
					alert(data.msg);
				}
			});

	},
// sendCreateGame }}}

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

	makeRules();
});

// vi: fdm=marker
