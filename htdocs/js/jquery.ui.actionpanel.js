(function($) {

$.widget("ui.actionpanel", {
	options: {
		servlet: 'responder.php',
		animateDuration: 200,
		selectedPanel: 'waiting',
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
			);

		self.displays.roll = rp;
		rp.hide();

		return rp;
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
			);

		self.displays.prop = pp;
		pp.hide();

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
			);

		self.displays.auction = ap;
		ap.data('time', timedisp);
		ap.data('bid', biddisp);
		ap.hide();

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
			);

		self.displays.buy = bp;
		bp.data('msg', msgdisp);
		bp.data('msgadd', msgadd);
		bp.data('yes', buy_yes);
		bp.data('no', buy_no);
		bp.hide();

		return bp;
	},

	makeInfoPanel: function () {
		var self = this,
		ip = self.makePanelContainer()
			.addClass('ui-actionpanel-info');

		self.displays.info = ip;
		ip.hide();

		return ip;
	},

	makeWaitingPanel: function () {
		var self = this,
		wp = self.makePanelContainer()
			.addClass('ui-actionpanel-waiting')
			.append(
				$('<div>Please wait for your turn...</div>')
			);

		self.displays.waiting = wp;
		wp.hide();

		return wp;
	},

	setBuyQuestion: function (what) {
		var self = this,
		bp = self.displays.buy;
		bp.data('msgadd').text(what);
	},

	setInfo: function (what) {
		var self = this,
		ip = self.displays.info;
		ip.text(what);
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
				args: 'sell:' + sid,
			},
			function (data) {
				if (data) {
					if (!data.result) {
						// TODO : handle failure
						alert('sellPropCallback: ' +data.msg);
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
					}
				}
			});
	},

	buyCallback: function (really) {
		var self = this;

		$.post(self.options.servlet,
			{
				method: 'tell',
				func: 'game',
				args: (really ? 'buy' : 'auction'),
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
		uiInfoPanel = (self.uiInfoPanel = self.makeInfoPanel());

		uiActionPanel
			.append(uiRollPanel, uiBuyPanel, uiPropPanel, uiAuctionPanel, uiInfoPanel, uiWaitingPanel);

		self._refresh();
		this.element.append(uiActionPanel);
	},

	_refresh: function () {
		var self = this;
		self.selectDisplay(self.options.selectedPanel);
	},

	_setOption: function (key, value) {
		var self = this;
		// _super and _superApply handle keeping the right this-context
		self._superApply(arguments);
		switch (key) {
			case 'selectedPanel':
				self.selectDisplay(value);
				break;
			case 'info':
				self.setInfo(value);
				self.selectDisplay('info');
				break;
			case 'buy':
				self.setBuyQuestion(value);
				self.selectDisplay('buy');
				break;
		}
	},
});

})(jQuery);

