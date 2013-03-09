$(document).ready(function () {
	window.iface = {

// data members {{{
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
			quitPanel: $('#quitpanel'),
			dialog: $('#dialog'),
		},
		options: {
			pollInterval: 2000,
			servelet: 'responder.php',
			loginLink: '../user/login.php',
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
// data members }}}

// initialization methods {{{

		dialogNotLoggedIn: function (msg) {
			var self = this;
			self.elems.dialog
				.attr('title', 'Not logged in.')
				.empty()
				.append(
					$('<div></div>')
						.text(msg),
					$('<div></div>')
						.addClass('gotologin')
						.append(
							$('<a></a>')
								.addClass('loginlink')
								.text('Log in')
								.attr('href', self.options.loginLink)
						)
				)
				.dialog({
					modal: true,
				})
				.show();
		},

		pullInitialGameData: function () {
			var self = this;
			$.post(self.options.servelet,
				{
					'method': 'ask',
					'func': 'init',
				},
				function (data) {
					if (!data || !data.board) {
						// uh-oh, we may not be logged in
						self.elems.chat.text(null).hide();
						self.dialogNotLoggedIn(data.msg);
						return;
					}
					self.gameData = data;
					self.setInitialInterface();
					// replay the last update
					self.procGameUpdate(data.update);
				});
		},

		setInitialInterface: function () {
			var self = this;

			// set dice
			self.initDice();
			if (self.gameData.roll) {
				self.setDice(false);
			}

			self.elems.dialog.hide();

			self.initPlayerInfo();
			self.initQuitPanel();
			self.initActionPanel();
			self.initBoard();
			//self.setPlayerInfo({id:2,turn:true});

			// FIXME : hardcore function implementation
		},

// initialization methods }}}

// update polling methods {{{

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
		},

		startGameUpdatePoll: function () {
			var self = this;
			window.setInterval(
				function () {
					self.pollGameUpdate();
				},
				self.options.pollInterval
			);
		},

// update polling methods }}}

// update processing {{{

		procGameUpdate: function (update) {
			var self = this,
			upd = jQuery.parseJSON(update);
			switch (upd.type) {
				case 'roll':
					self.gameData.roll = upd.val;
					self.setDice(true);
					break;
				case 'jail':
					self.jailUser(upd.id, upd.in_jail);
					break;
				case 'turn':
					self.updateTurn(upd.id);
					break;
				case 'askBuy':
					self.askToBuy(upd.who, upd.space);
					break;
				case 'buy':
					if (upd.owner && !self.isMe(upd.owner)) {
						self.setActionPanel({
							info:
								self._playerInfo(upd.owner).name +
								' bought ' +
								self._spaceInfo(upd.space).name
							});
					}
					self.elems.propcard.propcard({shown:false});
					break;
				case 'noBuy':
					if (upd.who && !self.isMe(upd.who)) {
						self.setActionPanel({
							info:
								self._playerInfo(upd.who).name +
								' declined to buy ' +
								self._spaceInfo(upd.space).name
							});
					}
					self.elems.propcard.propcard({shown:false});
					break;
				case 'auctionStart':
					self.auctionStart(upd.who, upd.space, upd.bid);
					break;
				case 'bid':
					self.auctionBid(upd.who, upd.bid);
					break;
				case 'card':
					if (upd.who && !self.isMe(upd.who)) {
						self.setActionPanel({
							info:
								self._playerInfo(upd.who).name +
								' drew a ' + upd.deck + ' card: ' +
								upd.msg
							});
					}
					break;
				case 'move':
					self.moveUser(upd.id, upd.space);
					break;
				case 'improve':
					self.setBoard({id:upd.space,houses:upd.houses});
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

// update processing }}}

// user info queries {{{

		isMyTurn: function () {
			var self = this;

			return self._playerInfo(self.gameData.my_id).turn;
		},

		isMe: function (uid) {
			var self = this;

			return uid == self.gameData.my_id;
		},

		amIInJail: function () {
			return this.isInJail(this.gameData.my_id);
		},

		isInJail: function (uid) {
			var self = this;

			return self._playerInfo(uid).jail;
		},

// user info queries }}}

		updateTurn: function (uid) {
			var self = this,
			iPanel;
			self.setPlayerInfo({id:uid,turn:true});

			if (self.isMe(uid)) {
				if (self.amIInJail()) {
					iPanel = 'jail';
				} else {
					iPanel = 'roll';
				}
			} else {
				iPanel = 'waiting';
			}
			self.setActionPanel({idlePanel:iPanel});
		},

		askToBuy: function (uid, sid) {
			var self = this,
			pc = self.elems.propcard;
			if (self.isMe(uid)) {
				self.setActionPanel({buy: self.gameData.board[sid].name});
			} else {
				self.setActionPanel({info: 'Asking ' + self._playerInfo(uid).name + ' if they want to buy ' + self.gameData.board[sid].name});
			}
			pc.propcard({
				id:sid,
				myId: self.gameData.my_id,
				persistNoCallbacks: true,
			});
		},

		auctionStart: function (uid, sid) {
			// placeholder - must actually start auction panel
			self.setActionPanel({
				info:
					self._playerInfo(upd.who).name +
					' started an auction of ' +
					self._spaceInfo(upd.space).name
				});
		},

		auctionBid: function (uid, bid) {
		},

		moveUser: function (uid, sid, sbid) {
			var self = this;
			self.setPlayerInfo({id:uid,on_space:sid});
			self.setBoard({id:sid,user:uid});
			if (!self.isMe(uid)) {
				self.setActionPanel({
					info:
						self._playerInfo(uid).name +
						' landed on ' +
						self._spaceInfo(sid).name
					});
			}
		},

		jailUser: function (uid, injail) {
			var self = this,
			uinfo = self._playerInfo(uid);

			// update our data
			uinfo.jail = injail;

			// update player info display
			self.setPlayerInfo({id:uid,jail:injail});

			// user thrown in jail is already notified
			if (!self.isMe(uid)) {
				var msg = uinfo.name +
					(injail ? ' got thrown in jail!' : ' got let out of jail!');

				self.setActionPanel({info: msg});
			}
		},

		spaceClick: function (sid) {
			var self = window.iface,
			pc = self.elems.propcard;
			pc.propcard({
				id:sid,
				closeCallback:self.spaceClose,
				myId: self.gameData.my_id,
				ownedCallback: function (isOwned) {
					if (isOwned) {
						self.setActionPanel({selectedPanel:'prop',propId:sid,idle:false});
					} else {
						self.setActionPanel({idle:true});
					}
				},
				persistNoCallbacks: false,
			});
		},

		spaceClose: function (sid) {
			var self = window.iface;
			self.setActionPanel({idle:true});
		},

// subpanel init/set methods {{{
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
		initQuitPanel: function () {
			var self = this;
			self.elems.quitPanel
				.quitpanel();
		},
		initActionPanel: function () {
			var self = this,
			myturn = self.isMyTurn(),
			sp = (myturn ? 'roll' : 'waiting');
			self.elems.actionPanel
				.actionpanel({
					selectedPanel: sp,
					idlePanel: sp,
					idle: true,
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

// subpanel init/set methods }}}

		init: function () {
			this.pullInitialGameData();
			this.startGameUpdatePoll();
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

		_spaceInfo: function (space_id) {
			var self = this,
			z = $.grep(self.gameData.board, function (elem, idx) {
				return elem.id == space_id;
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

// vi: fdm=marker
