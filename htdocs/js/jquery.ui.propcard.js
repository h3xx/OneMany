(function($) {

$.widget("ui.propcard", {
	options: {
		servlet: 'responder.php',
		id: null,
		data: null,
		shown: true,
		load: true,
		closeCallback: null,
		ownedCallback: null,
		myId: null,
	},

	widget: function () {
		return this.uiPropcard;
	},

	makeCard: function (data) {
		if (!data || data.cost < 0) {
			return;
		}
		var self = this,
		card = $('<div></div>')
			.addClass('propcard');

		if (data.is_mortgaged) {
			card.addClass('mortgaged');
		} else if (data.owner != null) {
			card.addClass('sold');
		}

		var hdr = $('<div></div>')
			.css({
				'color': data.color,
				'background-color': data.bcolor,
			})
			.addClass('header')
			.text(data.name),

		det = $('<div></div>')
			.addClass('details'),

		pbar = $('<div></div>')
			.addClass('pricebar'),

		rbar = $('<div></div>')
			.addClass('rentbar'),

		mbar = $('<div></div>')
			.addClass('mortgagebar'),

		obar = $('<div></div>')
			.addClass('ownerbar');

		//$(hdr).text('data:' + data);
		if (data.type == 'regular') {
			// price bar
			pbar.text('PRICE $' + data.cost + ' RENT $' + data.rent);
			// rent bar
			var hl = $('<div></div>')
				.addClass('housecounts')
				.html(
					'With 1 House<br />' +
					'With 2 Houses<br />' +
					'With 3 Houses<br />' +
					'With 4 Houses<br />' +
					'With HOTEL'
				),

			rl = $('<div></div>')
				.addClass('rentlist')
				.html(
					'$' + data.rent1 + '<br />' +
					'$' + data.rent2 + '<br />' +
					'$' + data.rent3 + '<br />' +
					'$' + data.rent4 + '<br />' +
					'$' + data.rent5
				);

			$(rbar).append(hl, rl);

			// mortgage bar
			$(mbar).html(
				'One house costs $' + data.housecost + '<br />' +
				'Mortgage value $' + data.mortgage
			);
		} else {
			// price bar
			$(pbar).text('PRICE $' + data.cost);

			if (data.type == 'rail') {
				var hl = $('<div></div>')
					.addClass('housecounts')
					.html(
						'<br />' +
						'If 1 owned<br />' +
						'If 2 owned<br />' +
						'If 3 owned<br />' +
						'If 4 owned'
					),

				rl = $('<div></div>')
					.addClass('rentlist')
					.html(
						'<br />' +
						'$' + data.rent + '<br />' +
						'$' + data.rent1 + '<br />' +
						'$' + data.rent2 + '<br />' +
						'$' + data.rent3
					);

				$(rbar).append(hl, rl);
			} else {
				// utility
				var cr = $('<div></div>')
					.addClass('conditionalrent')

					.html(
						'<br />' +
						'If 1 owned, rent equals<br />' +
						data.rent + ' times dice roll<br />' +
						'If 2 owned, rent equals<br />' +
						data.rent1 + ' times dice roll'
					);

				$(rbar).append(cr);
			}

			// mortgage bar
			$(mbar).html(
				'Mortgage value $' + data.mortgage
			);
		}

		if (data.owner != null) {
			obar.text('Owner: ' + data.oname);
		}

		det.append(pbar, rbar, mbar, obar);
		card.append(hdr, det);

		return card;
	},

	retrieve: function () {
		var self = this;

		$.post(self.options.servlet,
			{
				'method': 'ask',
				'func': 'propcardInfo',
				'args': self.options.id,
			},
			function (data) {
				if (data) {
					self.options.data = data;
					self.draw();
					if (self.options.ownedCallback && self.options.myId == data.owner) {
						self.options.ownedCallback();
					}
				}
			}
		);
	},

	draw: function () {
		var self = this;
		if (self.options.data != null) {
			$('.propcard').remove();
			self.widget()
				.append(self.makeCard(self.options.data))
				.show();
		}
	},

	_create: function () {
		var self = this,

		uiPropcard = (self.uiPropcard = $('<div></div>')
			.addClass('ui-propcard')
		)
			.click(function () {
				self.widget().hide();
				if (self.options.closeCallback) {
					self.options.closeCallback(self.options.id);
				}
			});

		this.element.append(uiPropcard);
		self._refresh();
	},

	_refresh: function () {
		var self = this;

		if (self.options.load && self.options.id != null) {
			self.retrieve();
		}
	},


	/* FIXME : implement
	_refresh: function () {
		var self = this;
	},

	_setOptions: function () {
		var self = this;
		self._superApply(arguments);
		self._refresh();
	},
	*/

	_setOption: function (key, value) {
		var self = this;
		// _super and _superApply handle keeping the right this-context
		self._superApply(arguments);
		if (key == 'id') {
			self._refresh();
		}
	},
});

})(jQuery);
