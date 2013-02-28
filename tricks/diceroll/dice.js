$(document).ready(function () {
	window.poop = $('#dice')
		//.hide()
		.dice({
			'id': '#dice',
			'callback': function (num) {
				$('#currentnumber').html('Current number: '+num);

			},
			'glyphSrc': 'images/dice.png',
			'glyphSize': 67.667,
			'juggleTimeout': 10000,
		});

	$('#roll')
		.button()
		.click(function (e) {
			$('#dice')
				.dice({
					'running': 1,
					'number': -1,
				});

		});
	$('#stop')
		.button()
		.click(function (e) {
			$('#dice')
				.dice({
					'running': 0,
					'number': Math.floor(Math.random() * 6) + 1,
				});

		});
});
