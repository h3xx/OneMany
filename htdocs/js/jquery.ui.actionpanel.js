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
	},
	displays: {},

	selectDisplay: function (dispId) {
		var self = this,
		showdisp = null;

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
		biddisp = $('<button></button>'),
		timedisp = $('<div></div>'),
		ap = self.makePanelContainer()
			.addClass('ui-actionpanel-auction')
			.append(
				timedisp,
				biddisp
			)
			.data('time', timedisp)
			.data('bid', biddisp)
			.hide();

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

	setBidTime: function (time) {
		var self = this,
		ap = self.displays.auction,
		timedisp = ap.data('time');
		timedisp.text('Time left: ' + time);
	},

	setBidAmt: function (bid) {
		var self = this,
		ap = self.displays.auction,
		biddisp = ap.data('bid');

		biddisp.text('Bid: $' + bid)
			.button()
			.click(function () {self.bidCallback(bid);});
	},

	bidCallback: function (bidAmt) {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: 'bid:' + bidAmt,
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
						$('#propcard').propcard({shown:false});
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

