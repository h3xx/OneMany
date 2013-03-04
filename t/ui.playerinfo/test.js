$(document).ready(function () {
	$('#players')
		.playerinfo({
			data: [
				{id: 1, name: 'h3xx', cash: 1500, turn: true},
				{id: 2, name: 'barney', cash: 1500},
			],
		});
});
