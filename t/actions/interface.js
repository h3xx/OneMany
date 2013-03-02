$(document).ready(function () {
	window.iface = {
		elems: {
			dice: [
				$('#dice1'),
				$('#dice2'),
			],
			chat: $('#chat'),
		},
		options: {
			pollInterval: 1000,
			servelet: 'responder.php',
			dice: {
				rollTimeout: 500,
				diceUiArgs: {
					'glyphSrc': 'images/dice.png',
					'glyphSize': 67.667,
					'juggleTimeout': 10000,
				},
			},
		},
		gameData: {},

		pullInitialGameData: function () {
			var self = this;
			$.post(self.options.servelet,
				{
					'method': 'ask',
					'func': 'init',
				},
				function (data) {
					if (!data) {
						// TODO : alert?
						return;
					}
					self.gameData = data;
					self.setInitialInterface();
				});
		},

		setInitialInterface: function () {
			var self = this;

			// set dice
			self.initDice();
			if (self.gameData.roll) {
				self.setDice(self.gameData.roll[0], self.gameData.roll[1], false);
			}

			// FIXME : hardcore function implementation
		},

		pollGameUpdate: function () {
			var self = this;
			$.post(self.options.servelet,
			{
				'method': 'ask',
				'func': 'pollGame',
				'args': (self.gameData ? self.gameData.state : -1),
			},
			function (data) {
				if (!data || !data.instructions) {
					return;
				}

				for (var i in data.instructions) {
					self.procGameUpdate(data.instructions[i]);
				}

				if (self.gameData.state < data.newstate) {
					self.gameData.state = data.newstate;
				}
			});
			self.scheduleGameUpdatePoll();
		},

		scheduleGameUpdatePoll: function () {
			window.setTimeout(this.pollGameUpdate, this.options.pollInterval);
		},

		procGameUpdate: function (update) {
			var self = this;
			upd = jQuery.parseJSON(update);
			switch (upd.type) {
				case 'roll':
					self.gameData.roll = upd.val;
					self.setDice(upd.val[0], upd.val[1], true);
					break;
				case 'buy':
					alert('buy is not implemented yet.');
					break;
				default:
					alert(upd.type + ' is an unknown update type');
					break;
			}
		},

		initDice: function () {
			for (var i in this.elems.dice) {
				this.elems.dice[i].dice(this.options.dice.diceUiArgs);
			}
		},

		setDice: function (a, b, runAnimation) {
			var self = this;
			if (runAnimation) {
				self.elems.dice[0].dice({running:1,number:-1});
				self.elems.dice[1].dice({running:1,number:-1});
				window.setTimeout(function () {
					self.setDice(a, b, false);
				}, self.options.dice.rollTimeout);
			} else {
				self.elems.dice[0].dice({running:0,number:a});
				self.elems.dice[1].dice({running:0,number:b});
			}
		},

		init: function () {
			this.pullInitialGameData();
			this.scheduleGameUpdatePoll();
		},

	};
});
