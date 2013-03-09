(function($) {

$.widget("ui.actionpanel", {
	options: {
		servlet: 'responder.php',
		animateDuration: 200,
		selectedPanel: 'waiting',
		idlePanel: 'waiting',
		idle: false,
		hasGojf: false,
		data: null,
		propId: null,
		auctionTimer: null,
		auctionPollInterval: 500,
		bidsteps: [1, 10, 25, 50, 100],
		currBid: 0,
	},
	displays: {},

	selectDisplay: function (dispId) {
		var self = this,
		showdisp = null;

		if (dispId == 'auction') {
			self.startAuctionPoll();
		} else {
			self.stopAuctionPoll();
		}

		// hide all other displays, then unhide the found one
		for (var id in self.displays) {
			var disp = self.displays[id];
			if (id == dispId) {
				showdisp = disp;
			} else {
				disp.hide(self.options.animateDuration);
			}
		}
		if (showdisp) {
			showdisp.show(self.options.animateDuration);
		}
	},

// constructors for different panels {{{

	makePanelContainer: function () {
		var self = this,
		pc =
			$('<div></div>')
			.addClass('panel');

		return pc;
	},

	makeRollPanel: function () {
		var self = this,
		rp = self.makePanelContainer()
			.addClass('ui-actionpanel-roll')
			.append(
				// Roll button
				$('<button>Roll</button>')
					.button()
					.click(function () {self.rollCallback();})
			)
			.hide();

		self.displays.roll = rp;

		return rp;
	},

	makeJailPanel: function () {
		var self = this,
		jp = self.makePanelContainer()
			.addClass('ui-actionpanel-jail')
			.append(
				// Roll button
				$('<button>Roll</button>')
					.button()
					.click(function () {self.rollCallback();}),
				$('<button>Use GOJF card</button>')
					.button()
					.click(function () {self.gojfCallback();})
			)
			.hide();

		self.displays.jail = jp;

		return jp;
	},

	makePropPanel: function () {
		var self = this,
		pp = self.makePanelContainer()
			.addClass('ui-actionpanel-prop')
			.append(
				// Sell button
				$('<button>Sell Property</button>')
					.button()
					.click(function () {self.sellPropCallback(self.options.propId);})
			)
			.append(
				// Buy house button
				$('<button>Buy 1 House</button>')
					.button()
					.click(function () {self.buyHouseCallback(self.options.propId);})
			)
			.append(
				// Buy house button
				$('<button>Sell 1 House</button>')
					.button()
					.click(function () {self.sellHouseCallback(self.options.propId);})
			)
			.hide();

		self.displays.prop = pp;

		return pp;
	},

	makeAuctionPanel: function () {
		var self = this,
		bidsteps = self.options.bidsteps,

		msgdisp =
			$('<div></div>')
			.addClass('auctionmsg'),

		timedisp =
			$('<div></div>')
			.addClass('auctiontime'),

		biddisp =
			$('<div></div>')
			.addClass('auctionbid'),

		winnerdisp =
			$('<div></div>')
			.addClass('auctionwinner'),

		buttondisp =
			$('<div></div>')
			.addClass('auctionbuttons'),

		ap = self.makePanelContainer()
			.addClass('ui-actionpanel-auction')
			.append(
				msgdisp,
				timedisp,
				winnerdisp,
				biddisp,
				buttondisp
			)
			.data('msg', msgdisp)
			.data('time', timedisp)
			.data('winner', winnerdisp)
			.data('bid', biddisp)
			.hide();

		for (var i in bidsteps) {
			var bid = bidsteps[i],
			btn = $('<button></button>')
				.addClass('ui-actionpanel-bidbtn')
				.data('amt', bid)
				.text('$' + bid)
				.button()
				.click(function () {
					var b = $(this).data('amt');
					self.bidCallback(b);
				});

			buttondisp.append(btn);
		}

		self.displays.auction = ap;

		return ap;
	},

	makeBuyPanel: function () {
		var self = this,
		buy_yes =
			$('<button>YES</button>')
			.addClass('ui-actionpanel-buyyes')
			.button()
			.click(function () {self.buyCallback(true);}),

		buy_no =
			$('<button>NO</button>')
			.addClass('ui-actionpanel-buyno')
			.button()
			.click(function () {self.buyCallback(false);}),

		msgadd =
			$('<div></div>'),

		msgdisp =
			$('<div></div>')
			.text('Would you like to buy this property?'),

		bp = self.makePanelContainer()
			.addClass('ui-actionpanel-buy')
			.append(
				msgdisp,
				msgadd,
				buy_yes,
				buy_no
			)
			.data('msg', msgdisp)
			.data('msgadd', msgadd)
			.data('yes', buy_yes)
			.data('no', buy_no)
			.hide();

		self.displays.buy = bp;

		return bp;
	},

	makeInfoPanel: function () {
		var self = this,
		ok = $('<button>OK</button>')
			.button()
			.click(function () {
				self.setIdle(true);
			}),
		disp = $('<div></div>'),
		ip = self.makePanelContainer()
			.addClass('ui-actionpanel-info')
			.append(disp, ok)
			.data('disp', disp)
			.hide();

		self.displays.info = ip;

		return ip;
	},

	makeWaitingPanel: function () {
		var self = this,
		wp = self.makePanelContainer()
			.addClass('ui-actionpanel-waiting')
			.append(
				$('<div>Please wait for your turn...</div>')
			)
			.hide();

		self.displays.waiting = wp;

		return wp;
	},

// constructors for different panels }}}

// auction polling {{{

	startAuctionPoll: function () {
		var self = this,
		options = self.options,
		atid = options.auctionTimer;

		if (atid == null) {
			options.auctionTimer = window.setInterval(
				function() {
					self.doAuctionPoll();
				},
				options.auctionPollInterval
			);
		}
	},

	stopAuctionPoll: function () {
		var self = this,
		options = self.options,
		atid = options.auctionTimer;

		if (atid != null) {
			window.clearInterval(atid);
			options.auctionTimer = null;
		}
	},

	doAuctionPoll: function () {
		var self = this;
		$.post(self.options.servlet,
			{
				method: 'ask',
				func: 'auction',
			},
			function (data) {
				if (data) {
					if (data.aseconds == null && !data.result) {
						// TODO : handle failure
						alert('doAuctionPoll:' + data.msg);
					} else {
						if (data.aseconds <= 0) {
							self.stopAuctionPoll();
						}

						self.setBidMsg('Auctioning ' + data.aname);
						self.setBidTime(data.aseconds);
						self.setBidAmt(self.options.currBid = data.abid);
						self.setBidWinner(data.auser);
					}
				}
			});
	},

// auction polling }}}

	setBuyQuestion: function (what) {
		var self = this,
		bp = self.displays.buy;
		bp.data('msgadd').text(what);
	},

	showInfo: function (what) {
		var self = this;
		self.setInfo(what);
		self.options.selectedPanel = 'info';
		self.options.idle = false;
		self.setIdle(false);
	},

	setInfo: function (what) {
		var self = this,
		ip = self.displays.info;
		ip.data('disp').text(what);
	},

	setBidMsg: function (msg) {
		var self = this,
		ap = self.displays.auction,
		msgdisp = ap.data('msg');
		msgdisp.text(msg);
	},

	setBidTime: function (time) {
		var self = this,
		ap = self.displays.auction,
		timedisp = ap.data('time');

		if (time < 0) {
			time = 0;
		}

		timedisp.text(
			/* first method - produces stupid displays like '20:4'
			(time / 60).toFixed(0) + // minutes
			':' +
			(time % 60).toFixed(0) // seconds
			*/
			(parseInt(time / 60) + (time % 60 / 100)).toFixed(2)
			.replace(/\./, ':')
		);
	},

	setBidAmt: function (bid) {
		var self = this,
		ap = self.displays.auction,
		biddisp = ap.data('bid');

		biddisp.text('Bid: $' + bid);
	},

	setBidWinner: function (winner) {
		if (winner != null) {
			var self = this,
			ap = self.displays.auction,
			winnerdisp = ap.data('winner');
			winnerdisp.text('Ahead: ' + winner);
		}
	},

	bidCallback: function (bidAmt) {
		var self = this,
		ap = self.displays.auction,
		currBid = parseInt(self.options.currBid);

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: 'bid:' + (currBid+bidAmt),
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert(data.msg);
					}
				}
			});

	},

	sellPropCallback: function (sid) {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: 'sell: ' + sid,
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert('sellPropCallback: ' +data.msg);
					} else {
						self.setIdle(true);
						// FIXME : clear violation of top-down
						$('#propcard').propcard({shown:false});
						self.setIdle(true);
					}

				}
			});
	},

	buyHouseCallback: function (sid) {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: 'buyHouse:' + sid,
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert('buyHouseCallback: ' +data.msg);
					}
				}
			});
	},

	sellHouseCallback: function (sid) {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: 'sellHouse:' + sid,
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert('sellHouseCallback: ' +data.msg);
					}
				}
			});
	},

	gojfCallback: function () {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: 'useGojf',
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert('gojfCallback: ' +data.msg);
					} else {
						self.showInfo(data.msg);
						//self.setIdle(false);
					}
				} else {
					alert('gojfCallback: ' +data);
				}
			});
	},

	rollCallback: function () {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: 'roll',
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert('rollCallback: ' +data.msg);
					} else {
						self.showInfo(data.msg);
						//self.setIdle(false);
					}
				} else {
					alert('rollCallback: ' +data);
				}
			});
	},

	buyCallback: function (really) {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: (really ? 'buy' : 'noBuy'),
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert(data.msg);
					} else {
						self.options.idle = true;
						self.setIdle(true);
						// FIXME : clear violation of top-down
						$('#propcard').propcard({shown:!really});
					}
				}
			});

	},

	widget: function () {
		return this.uiActionPanel;
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiActionPanel = (self.uiActionPanel = $('<div></div>'))
			.addClass('ui-actionpanel ui-widget-header ui-corner-all'),

		uiRollPanel = (self.uiRollPanel = self.makeRollPanel()),
		uiBuyPanel = (self.uiBuyPanel = self.makeBuyPanel());
		uiPropPanel = (self.uiPropPanel = self.makePropPanel());
		uiAuctionPanel = (self.uiAuctionPanel = self.makeAuctionPanel());
		uiWaitingPanel = (self.uiWaitingPanel = self.makeWaitingPanel());
		uiJailPanel = (self.uiJailPanel = self.makeJailPanel());
		uiInfoPanel = (self.uiInfoPanel = self.makeInfoPanel());

		uiActionPanel
			.append(
				uiRollPanel,
				uiBuyPanel,
				uiPropPanel,
				uiAuctionPanel,
				uiInfoPanel,
				uiJailPanel,
				uiWaitingPanel
			);

		self._refresh();
		this.element.append(uiActionPanel);
	},

	setIdle: function (isIdle) {
		var self = this;
		if (isIdle) {
			self.selectDisplay(self.options.idlePanel);
		} else {
			self.selectDisplay(self.options.selectedPanel);
		}
	},

	_refresh: function () {
		var self = this;
		self.selectDisplay(self.options.selectedPanel);
	},

	_setOption: function (key, value) {
		var self = this;
		// _super and _superApply handle keeping the right this-context
		switch (key) {
			case 'selectedPanel':
				self.options.selectedPanel = value;
				self.selectDisplay(value);
				break;
			case 'idlePanel':
				self.options.idlePanel = value;
				self.setIdle(self.options.idle);
				break;
			case 'idle':
				self.setIdle(value);
				break;
			case 'currBid':
				self.setBidAmt(value);
				break;
			case 'info':
				self.showInfo(value);
				break;
			case 'buy':
				self.setBuyQuestion(value);
				self.options.selectedPanel = 'buy';
				self.options.idle = false;
				self.setIdle(false);
				break;

		}
		self._superApply(arguments);
	},
});

})(jQuery);

// vi: fdm=marker
