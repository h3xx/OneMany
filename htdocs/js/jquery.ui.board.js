(function($) {

$.widget("ui.board", {
	options: {
		data: {},
		boardImage: 'images/board-bg.png',
		nameImage: 'images/name.svg',
		houseImage: 'images/house.svg',
		hotelImage: 'images/hotel.svg',
		home: '#propcard', // FIXME
	},
	displays: {}, // indexed by 'id1', 'id26', etc for space_id
	elems: {},
	widget: function () {
		return this.uiBoard;
	},

	makePieces: function () {
		var self = this,
		piec = $('<table></table>')
			.addClass('ui-board-pieces'),
		pbody = $('<tbody></tbody>');

		piec.append(pbody);

		var id = 0;

		// first row
		var row1 = $('<tr></tr>');
		pbody.append(row1);

		// GO
		row1.append(
			$('<td></td>')
				.addClass('propCrnr propMain go')
				.append(
					(self.displays['id0'] = {}).pieces =
					$('<div></div>').addClass('iconsCorner')
				)
		);

		for (var x = 0; x < 9; ++x) {
			var disp = (self.displays['id' + (x+1)] = {}),
			cell = $('<td></td>')
				.addClass('propVert propMain')
				// outer edge
				.append(
					disp.pieces =
					$('<div></div>').addClass('iconsVert')
				)
				// spacing
				.append(
					$('<div></div>').addClass('vert')
				)
				// houses space
				.append(
					disp.houses =
					$('<div></div>').addClass('iconsVert')
				)
				.data('id', (x+1))
				// propcard popout
				.click(function () {
					$(self.options.home)
						.propcard({id:$(this).data('id')})
				});

			row1.append(cell);
		}

		// Jail
		row1.append(
			$('<td></td>')
				.addClass('propCrnr propMain jail')
				.append(
					(self.displays['id10'] = {}).pieces =
					$('<div></div>').addClass('iconsCorner')
				)
		);

		// rows 2-9
		for (var x = 0; x < 9; ++x) {
			var rowx = $('<tr></tr>'),
			disp1 = (self.displays['id' + (39-x)] = {}),

			cell1 = $('<td></td>')
				.addClass('propHorz propMain')
				// outer edge
				.append(
					disp1.pieces =
					$('<div></div>').addClass('iconsHorz')
				)
				// spacing
				.append(
					$('<div></div>').addClass('horz')
				)
				// houses space
				.append(
					disp1.houses =
					$('<div></div>').addClass('iconsHorz')
				)
				.data('id', (39-x))
				// propcard popout
				.click(function () {
					$(self.options.home)
						.propcard({id:$(this).data('id')})
				}),

			cell2 = $('<td></td>')
				.attr('colspan', '9'),

			disp3 = (self.displays['id' + (11+x)] = {}),
			cell3 = $('<td></td>')
				.addClass('propHorz propMain')
				// houses space
				.append(
					disp3.houses =
					$('<div></div>').addClass('iconsHorz')
				)
				// spacing
				.append(
					$('<div></div>').addClass('horz')
				)
				// outer edge
				.append(
					disp3.pieces =
					$('<div></div>').addClass('iconsHorz')
				)
				.data('id', (11+x))
				// propcard popout
				.click(function () {
					$(self.options.home)
						.propcard({id:$(this).data('id')})
				});

			pbody.append(rowx.append(cell1, cell2, cell3));
		}

		// bottom row
		var row10 = $('<tr></tr>');
		pbody.append(row10);

		// Go To Jail
		row10.append(
			$('<td></td>')
				.addClass('propCrnr propMain')
				.append(
					(self.displays['id29'] = {}).pieces =
					$('<div></div>').addClass('iconsCorner')
				)
		);

		for (var x = 0; x < 9; ++x) {
			var disp = (self.displays['id' + (29-x)] = {}),
			cell = $('<td></td>')
				.addClass('propVert propMain')
				// outer edge
				.append(
					disp.houses =
					$('<div></div>').addClass('iconsVert')
				)
				// spacing
				.append(
					$('<div></div>').addClass('vert')
				)
				// houses space
				.append(
					disp.pieces =
					$('<div></div>').addClass('iconsVert')
				)
				.data('id', (29-x))
				// propcard popout
				.click(function () {
					$(self.options.home)
						.propcard({id:$(this).data('id')})
				});

			row10.append(cell);
		}

		// Free Parking
		row10.append(
			$('<td></td>')
				.addClass('propCrnr propMain')
				.append(
					(self.displays['id19'] = {}).pieces =
					$('<div></div>').addClass('iconsCorner')
				)
		);

		return piec;
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
			.append(bg, nam, self.makePieces());

		return brd;
	},

	setHouses: function (id, numHouses) {
		var self = this,
		disp = self.displays['id'+id];
		if (!disp) return;
		var elem = disp.houses;
		if (!elem) return;
		var alreadyHouses = elem.data('houses') | 0;

		if (alreadyHouses == numHouses) return; // don't gotta do shit

		if (numHouses == 5) {
			elem.remove('img');
			//elem.append($('<img>').attr('src', self.options.hotelImage).addClass('hotel'));
			elem.append(self.elems.hotel);
		} else {
			var i = 0;
			if (numHouses > alreadyHouses) {
				i = alreadyHouses;
			} else {
				elem.remove('img');
			}
			for (; i < numHouses; ++i) {

				//elem.append($('<img>').attr('src', self.options.houseImage).addClass('house'));
				elem.append(self.elems.house);
			}
		}

		// update the data
		elem.data('houses', numHouses);
	},

	_create: function () {
		var self = this,
		options = self.options,

		uiBoard = (self.uiBoard = $('<div></div>'))
			.addClass('ui-board ui-corner-all')
			.append(self.makeBoard());

		// populate element cache
		self.elems['house'] = $('<img>')
			.attr('src', options.houseImage)
			.addClass('house');
		self.elems['hotel'] = $('<img>')
			.attr('src', options.hotelImage)
			.addClass('hotel');
		//self.displays['id3'].houses.append(self.elems.house);

		this.element.append(uiBoard);
		self._refresh();
	},

	_refresh: function () {
		var self = this,
		options = self.options;

		for (var i in options.data) {
			var sdata = options.data[i];
			self.setHouses(sdata.id, sdata.houses);
		}
	},

	_setOptions: function () {
		var self = this;
		self._superApply(arguments);
		self._refresh();
	},

	/* FIXME : implement
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
