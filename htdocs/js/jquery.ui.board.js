(function($) {

$.widget("ui.board", {
	options: {
		data: {},
		boardImage: 'images/board-bg.png',
		nameImage: 'images/name.svg',
	},
	displays: {}, // indexed by 'id1', 'id26', etc for space_id

	widget: function () {
		return this.uiBoard;
	},

	makeBoard: function () {
		var self = this,
		brd = $('<div></div>')
			.css('position', 'relative'),
		bg = $('<img>')
			.attr('src', self.options.boardImage)
			.addClass('ui-board-bg'),
		nam = $('<img>')
			.attr('src', self.options.nameImage)
			.css('position', 'absolute')
			.css('top', '420px')
			.css('left', '220px')
			.addClass('ui-board-name');

		brd
			.append(bg)
			.append(nam);

		return brd;
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiBoard = (self.uiBoard = $('<div></div>'))
			.addClass('ui-board ui-corner-all')
			.append(self.makeBoard());



		this.element.append(uiBoard);
	},

	/* FIXME : implement
	_refresh: function () {
		var self = this,
		options = self.options;

		for (var i in options.data) {
			var udata = options.data[i],
			disp = self.displays['id' + udata.id];
			if (disp) {
				disp.name.text(udata.name);
				disp.cash.text('$' + udata.cash);
			}
		}
	},

	_setOption: function (key, value) {
		var self = this;
		// _super and _superApply handle keeping the right this-context
		if (key == 'data') {
			// expect an array
			for (var i in value) {
				var turn = value[i].turn,
				data = self.options.data;
				if (turn != null && turn) {
					self.setTurn(value[i].id);
				}

				// merge at same id in our data
				jQuery.map(data, function (elem, idx) {
					if (elem.id == value[i].id) {
						$.extend(elem, value[i]);
					}
				});
			}
		}
		// -- dangerous -- self._superApply(arguments);
		self._refresh();
	},
	*/
});

})(jQuery);
