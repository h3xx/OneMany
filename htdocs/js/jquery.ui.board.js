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
					(self.displays['id' + id++] = {}).pieces =
					$('<div></div>').addClass('iconsCorner')
				)
		);

		for (var x = 0; x < 9; ++x) {
			var cell = $('<td></td>')
				.addClass('propVert propMain')
				// outer edge
				.append(
					(self.displays['id' + id] = {}).pieces =
					$('<div></div>').addClass('iconsVert')
				)
				// spacing
				.append(
					$('<div></div>').addClass('vert')
				)
				// houses space
				.append(
					(self.displays['id' + id++] = {}).houses =
					$('<div></div>').addClass('iconsVert')
				);

			row1.append(cell);
		}

		// Jail
		row1.append(
			$('<td></td>')
				.addClass('propCrnr propMain jail')
				.append(
					(self.displays['id' + id++] = {}).pieces =
					$('<div></div>').addClass('iconsCorner')
				)
		);

		// rows 2-9
		for (var x = 0; x < 9; ++x) {
			var rowx = $('<tr></tr>');

			var cell1 = $('<td></td>')
				.addClass('propHorz propMain')
				// outer edge
				.append(
					(self.displays['id' + id] = {}).pieces =
					$('<div></div>').addClass('iconsHorz')
				)
				// spacing
				.append(
					$('<div></div>').addClass('horz')
				)
				// houses space
				.append(
					(self.displays['id' + id++] = {}).houses =
					$('<div></div>').addClass('iconsHorz')
				),

			cell2 = $('<td></td>')
				.attr('colspan', '9'),

			cell3 = $('<td></td>')
				.addClass('propHorz propMain')
				// houses space
				.append(
					(self.displays['id' + id] = {}).houses =
					$('<div></div>').addClass('iconsHorz')
				)
				// spacing
				.append(
					$('<div></div>').addClass('horz')
				)
				// outer edge
				.append(
					(self.displays['id' + id++] = {}).pieces =
					$('<div></div>').addClass('iconsHorz')
				);

			pbody.append(
				rowx
					.append(cell1)
					.append(cell2)
					.append(cell3)
			);
		}

		// bottom row
		var row10 = $('<tr></tr>');
		pbody.append(row10);

		// Go To Jail
		row10.append(
			$('<td></td>')
				.addClass('propCrnr propMain')
				.append(
					(self.displays['id' + id++] = {}).pieces =
					$('<div></div>').addClass('iconsCorner')
				)
		);

		for (var x = 0; x < 9; ++x) {
			var cell = $('<td></td>')
				.addClass('propVert propMain')
				// outer edge
				.append(
					(self.displays['id' + id] = {}).houses =
					$('<div></div>').addClass('iconsVert')
				)
				// spacing
				.append(
					$('<div></div>').addClass('vert')
				)
				// houses space
				.append(
					(self.displays['id' + id++] = {}).pieces =
					$('<div></div>').addClass('iconsVert')
				);

			row10.append(cell);
		}

		// Free Parking
		row10.append(
			$('<td></td>')
				.addClass('propCrnr propMain')
				.append(
					(self.displays['id' + id++] = {}).pieces =
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
			.append(bg)
			.append(nam)
			.append(self.makePieces());

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
