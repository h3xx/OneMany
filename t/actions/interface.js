$(document).ready(function () {
	window.iface = {
		elems: {
			dice: [
				$('#dice1'),
				$('#dice2'),
			],
			chat: $('#chat'),
			playerInfo: $('#players'),
			actionPanel: $('#actions'),
			board: $('#board'),
			propcard: $('#propcard'),
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
			playerInfo: {
				playerinfoUiArgs: {
					turnclass: 'inturn',
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
				self.setDice(false);
			}

			self.initPlayerInfo();
			self.initActionPanel();
			self.initBoard();
			//self.setPlayerInfo({id:2,turn:true});

			// FIXME : hardcore function implementation
		},

		pollGameUpdate: function () {
			var self = window.iface;
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
			var self = this,
			upd = jQuery.parseJSON(update);
			switch (upd.type) {
				case 'roll':
					self.gameData.roll = upd.val;
					self.setDice(true);
					break;
				case 'turn':
					self.updateTurn(upd.id);
					break;
				case 'askBuy':
					self.askToBuy(upd.who, upd.space);
					break;
				case 'buy':
					alert('buy is not implemented yet.');
					break;
				case 'move':
					self.moveUser(upd.id, upd.space);
					break;
				case 'cash':
					self._mergeAtId(self.gameData.users, upd.id, {
						cash: upd.cash,
					});
					self.setPlayerInfo({id:upd.id,cash:upd.cash});
					break;
				default:
					alert(upd.type + ' is an unknown update type');
					break;
			}
		},

		updateTurn: function (uid) {
			var self = this;
			self.setPlayerInfo({id:uid,turn:true});
			if (self.gameData.my_id == uid) {
				self.setActionPanel({selectedPanel:'roll'});
			} else {
				self.setActionPanel({selectedPanel:'waiting'});
			}
		},

		askToBuy: function (uid, sid) {
			var self = this;
			if (self.gameData.my_id == uid) {
				self.setActionPanel({buy: sid});
			}
		},

		moveUser: function (uid, sid) {
			var self = this;
			self.setPlayerInfo({id:uid,on_space:sid});
			self.setBoard({id:sid,user:uid});
		},

		spaceClick: function (sid) {
			var self = window.iface,
			pc = self.elems.propcard;
			pc.propcard({
				id:sid,
				closeCallback:self.spaceClose,
			});
			self.setActionPanel({selectedPanel:'prop'});
		},

		spaceClose: function (sid) {
			var self = window.iface;
			self.setActionPanel({selectedPanel:'roll'});
		},

		initDice: function () {
			for (var i in this.elems.dice) {
				this.elems.dice[i].dice(this.options.dice.diceUiArgs);
			}
		},

		setDice: function (runAnimation) {
			var self = this;

			if (runAnimation) {
				self.elems.dice[0].dice({running:1,number:-1});
				self.elems.dice[1].dice({running:1,number:-1});
				window.setTimeout(function () {
					self.setDice(false);
				}, self.options.dice.rollTimeout);
			} else {
				var a = self.gameData.roll[0],
				    b = self.gameData.roll[1];
				self.elems.dice[0].dice({running:0,number:a});
				self.elems.dice[1].dice({running:0,number:b});
			}
		},

		initPlayerInfo: function () {
			var self = this;
			self.elems.playerInfo
				.playerinfo({
					data: self.gameData.users,
				});
		},
		setPlayerInfo: function (data) {
			var self = this;
			self.elems.playerInfo
				.playerinfo({
					data: [data],
				});
		},
		initActionPanel: function () {
			var self = this,
			sp = (self._playerInfo(self.gameData.my_id).turn ? 'roll' : 'waiting');
			self.elems.actionPanel
				.actionpanel({
					selectedPanel: sp,
				});
		},
		setActionPanel: function (data) {
			var self = this;
			self.elems.actionPanel
				.actionpanel(data);
		},
		initBoard: function () {
			var self = this;
			self.elems.board
				.board({
					data: self.gameData.board,
					spaceClickCallback: self.spaceClick,
				});

			// set user locations
			var ulocs = [];
			for (var i in self.gameData.users) {
				var ud = self.gameData.users[i];
				ulocs[i] = {id: ud.on_space, user: ud.id};
			}

			self.elems.board
				.board({
					data: ulocs,
				});
		},
		setBoard: function (data) {
			var self = this;
			self.elems.board
				.board({
					data: [data]
				});
		},

		init: function () {
			this.pullInitialGameData();
			this.scheduleGameUpdatePoll();
		},

		_playerInfo: function (user_id) {
			var self = this,
			z = $.grep(self.gameData.users, function (elem, idx) {
				return elem.id == user_id;
			});
			if (z) {
				return z[0];
			}
			return [];
		},

		_mergeAtId: function (list, idVal, newData) {
			jQuery.map(list, function (elem, idx) {
				if (elem.id == idVal) {
					$.extend(elem, newData);
				}
			});
		},

	};
});
