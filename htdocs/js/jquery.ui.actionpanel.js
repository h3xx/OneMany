(function($) {

$.widget("ui.actionpanel", {
	options: {
		servlet: 'responder.php',
		animateDuration: 200,
		selectedPanel: 'roll',
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
			.css('width', '100%')
			.addClass('panel');

		return pc;
	},

	makeRollPanel: function () {
		var self = this,
		rp = self.makePanelContainer()
			.append(
				// Roll button
				$('<button>Roll</button>')
					.button()
			);

		self.displays.roll = rp;

		return rp;
	},

	makeAuctionPanel: function () {
		var self = this,
		biddisp = $('<button></button>'),
		timedisp = $('<div></div>'),
		ap = self.makePanelContainer()
			.append(
				timedisp,
				biddisp
			);

		self.displays.auction = ap;
		ap.data('time', timedisp);
		ap.data('bid', biddisp);

		return ap;
	},

	setBidTime: function (time) {
		var self = this,
		ap = this.displays.auction,
		timedisp = ap.data('time');
		timedisp.text('Time left: ' + time);
	},

	setBidAmt: function (bid) {
		var self = this,
		ap = this.displays.auction,
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

	widget: function () {
		return this.uiActionPanel;
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiActionPanel = (self.uiActionPanel = $('<div></div>'))
			.css('width', '100%')
			.addClass('ui-actionpanel ui-widget-header ui-corner-all'),

		uiRollPanel = (self.uiRollPanel = self.makeRollPanel()),
		uiAuctionPanel = (self.uiAuctionPanel = self.makeAuctionPanel());

		uiActionPanel
			.append(uiRollPanel, uiAuctionPanel);

		this.setBidTime('30');
		this.setBidAmt('25');

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
		if (key == 'selectedPanel') {
			self.selectDisplay(value);
		}
		self._refresh();
	},
});

})(jQuery);

