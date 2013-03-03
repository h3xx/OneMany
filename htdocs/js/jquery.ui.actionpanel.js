(function($) {

$.widget("ui.actionpanel", {
	options: {
		animateDuration: 200,
	},
	displays: {},

	selectDisplay: function (dispId) {
		var self = this,
		showdisp = null;

		// hide all other displays, then unhide the found one
		for (var id in self.displays) {
			var disp = self.displays[i];
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

	widget: function () {
		return this.uiActionPanel;
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiActionPanel = (self.uiActionPanel = $('<div></div>'))
			.css('width', '100%')
			.addClass('ui-actionpanel ui-widget-header ui-corner-all'),

		uiRollPanel = (self.uiRollPanel = self.makeRollPanel());

		uiActionPanel
			.append(uiRollPanel);

		this.element.append(uiActionPanel);
	},

	/* FIXME : implement
	_refresh: function () {
		var self = this;
	},
	*/

	/* FIXME : implement
	_setOption: function (key, value) {
		var self = this;
		// _super and _superApply handle keeping the right this-context
		self._superApply(arguments);
		self._refresh();
	},
	*/

});

})(jQuery);

