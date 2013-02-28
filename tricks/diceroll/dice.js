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
			pollGameUpdate();
		});

	pollGameUpdate();
});

function pollGameUpdate () {
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

	window.setTimeout(pollGameUpdate, 1000);
}

function setDice (a, b, r) {
	if (!r) {
		$('#dice1').dice({running:1,number:-1});
		$('#dice2').dice({running:1,number:-1});
		window.setTimeout(function () {
			setDice(a,b,true);
		}, 500);
	} else {
		$('#dice1').dice({running:0,number:a});
		$('#dice2').dice({running:0,number:b});
	}
}

function procGameUpdate (update) {
	upd = jQuery.parseJSON(update);
	if (upd.type == 'roll') {
		setDice(upd.val[0],upd.val[1],false);
		$('#currentnumber').append(upd.val + ' ');
	}
}
