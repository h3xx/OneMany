(function($) {

$.widget('ui.quitpanel', {
	options: {
	},

	leaveGameCallback: function () {
		alert('leaveGameCallback');
	},

	logoutCallback: function () {
		alert('logoutCallback');
	},

	makePanel: function () {
		var self = this,

		pan = $('<div></div>')
			.addClass('panel')
			.append(
				$('<button>Leave Game</button>')
					.addClass('ui-quitpanel-button ui-quitpanel-leave')
					.button()
					.click(function () {self.leaveGameCallback();}),
				$('<button>Logout</button>')
					.addClass('ui-quitpanel-button ui-quitpanel-logout')
					.button()
					.click(function () {self.logoutCallback();})
			);

		return pan;
	},

	widget: function () {
		return this.uiQuitpanel;
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiQuitpanel = (self.uiQuitpanel = $('<div></div>'))
			.addClass('ui-quitpanel ui-widget-header ui-corner-all')
			.append(self.makePanel());

		this.element.append(uiQuitpanel);
	},
});

})(jQuery);
