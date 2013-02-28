$(document).ready(function () {
	var dice_elems = [$('#dice1'), $('#dice2')];
	for (var d in dice_elems) {
		dice_elems[d]
		//.hide()
		.dice({
			/*
			'callback': function (num) {
				$('#currentnumber').html('Current number: '+num);

			},*/
			'glyphSrc': 'images/dice.png',
			'glyphSize': 67.667,
			'juggleTimeout': 10000,
		});
	}

	$('#roll')
		.button()
		.click(function (e) {
			for (var d in dice_elems) {
				dice_elems[d]
				.dice({
					'running': 1,
					'number': -1,
				});
			}
			$.post('responder.php', {
				'method': 'tell',
				'func': 'game',
				'args': 'roll',
			},
			function (data) {
				if (!data.result) {
					$('#currentnumber').text('failed: ' + data.msg);
					dice_elems[0].dice({'running': 0});
					dice_elems[1].dice({'running': 0});
				}
			});


		});

	window.gamestate = 0;
	$('#poll')
		.button()
		.click(function (e) {
			$.post('responder.php', {
				'method': 'ask',
				'func': 'pollGame',
				'args': window.gamestate,
			},
			function (data) {
				if (!data && !data.instructions) {
					return;
				}

				for (var i in data.instructions) {
					procGameUpdate(data.instructions[i]);
				}

				if (window.gamestate < data.newstate) {
					window.gamestate = data.newstate;
				}
			});

		});
});

function procGameUpdate (update) {
	upd = jQuery.parseJSON(update);
	if (upd.type == 'roll') {
		$('#dice1').dice({running:0,number:upd.val[0]});
		$('#dice2').dice({running:0,number:upd.val[1]});
	}
	$('#currentnumber').append(upd.val[0]);
}
