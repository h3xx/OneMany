(function($) {

$.widget("ui.playerinfo", {
	options: {
		data: {},
		turnclass: 'inturn',
	},
	displays: {}, // indexed by 'id1', 'id26', etc for user_id

	playerInfoBuff: function (name, cash, isTurn) {
		var self = this,
		csh = $('<span></span>')
			.text('$' + cash),
		nam = $('<span></span>')
			.text(name),
		buff = $('<div></div>')
			.append(nam)
			.append(csh);

		if (isTurn) {
			buff.addClass(self.options.turnclass);
		} else {
			buff.removeClass(self.options.turnclass);
		}

		return {
			'main': buff,
			'name': nam,
			'cash': csh,
		};
	},

	setTurn: function (id) {
		var self = this,
		tc = self.options.turnclass;

		for (var i in self.displays) {
			if (i == ('id' + id)) {
				self.displays[i].main.addClass(tc);
			} else {
				self.displays[i].main.removeClass(tc);
			}
		}
	},

	widget: function () {
		return this.uiPlayerInfo;
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiPlayerInfo = (self.uiPlayerInfo = $('<div></div>'));
		for (var i in options.data) {
			var udata = options.data[i];
			pbuff = self.playerInfoBuff(udata.name, udata.cash, udata.turn);
			self.displays['id' + udata.id] = pbuff;
			uiPlayerInfo.append(pbuff.main);
		}

		this.element.append(uiPlayerInfo);
	},

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

});

})(jQuery);
