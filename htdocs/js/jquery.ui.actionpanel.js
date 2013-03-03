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

	widget: function () {
		return this.uiActionPanel;
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiActionPanel = (self.uiActionPanel = $('<div></div>'));

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

