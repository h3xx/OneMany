(function($) {

$.widget("ui.propcard", {
	options: {
		servlet: 'responder.php',
		id: null,
		data: null,
		shown: true,
		load: true,
	},
	displays: {},

	widget: function () {
		return this.uiPropcard;
	},

	doLoad: function () {
		var self = this,
		options = self.options;

	},

	makeCard: function (data) {
		var self = this,
		card = $('<div></div>')
			.addClass('propcard');

		if (data.is_mortgaged) {
			card.addClass('mortgaged');
		} else if (data.owner) {
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
			.addClass('mortgagebar');

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

		det.append(pbar, rbar, mbar);
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
					self.widget().append(self.makeCard(data));
				}
			}
		);
	},

	_create: function () {
		var self = this,

		uiPropcard = (self.uiPropcard = $('<div></div>')
			.addClass('ui-propcard')
		);

		this.element.append(uiPropcard);
		self._refresh();
	},

	_refresh: function () {
		var self = this;

		if (self.options.data == null) {
			if (self.options.load) {
				self.retrieve();
			}
		}

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
