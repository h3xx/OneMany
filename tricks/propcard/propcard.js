function loadpropcard (where, propcardid) {
	var elem = $(where);
	$.post('responder.php',
		{
			'method': 'ask',
			'func': 'propcardInfo',
			'args': propcardid,
		},
		function (data) {
			if (data) {
				var card = document.createElement('div');
				$(card).addClass('propcard');
				if (data.is_mortgaged) {
					$(card).addClass('mortgaged');
				} else if (data.owner) {
					$(card).addClass('sold');
				}

				var hdr = document.createElement('div');
				$(hdr).css({
					'color': data.color,
					'background-color': data.bcolor,
				});
				$(hdr).addClass('header');
				$(hdr).text(data.name);

				var det = document.createElement('div');
				$(det).addClass('details');

				var pbar = document.createElement('div');
				$(pbar).addClass('pricebar');

				var rbar = document.createElement('div');
				$(rbar).addClass('rentbar');

				var mbar = document.createElement('div');
				$(mbar).addClass('mortgagebar');

				//$(hdr).text('data:' + data);
				if (data.type == 'regular') {
					// price bar
					$(pbar).text('PRICE $' + data.cost + ' RENT $' + data.rent);
					// rent bar
					var hl = document.createElement('div');
					$(hl).addClass('housecounts');
					$(hl).html(
						'With 1 House<br />' +
						'With 2 Houses<br />' +
						'With 3 Houses<br />' +
						'With 4 Houses<br />' +
						'With HOTEL'
					);

					var rl = document.createElement('div');
					$(rl).addClass('rentlist');
					$(rl).html(
						'$' + data.rent1 + '<br />' +
						'$' + data.rent2 + '<br />' +
						'$' + data.rent3 + '<br />' +
						'$' + data.rent4 + '<br />' +
						'$' + data.rent5
					);

					$(rbar).append(hl);
					$(rbar).append(rl);

					// mortgage bar
					$(mbar).html(
						'One house costs $' + data.housecost + '<br />' +
						'Mortgage value $' + data.mortgage
					);
				} else {
					// price bar
					$(pbar).text('PRICE $' + data.cost);

					if (data.type == 'rail') {
						var hl = document.createElement('div');
						$(hl).addClass('housecounts');
						$(hl).html(
							'<br />' +
							'If 1 owned<br />' +
							'If 2 owned<br />' +
							'If 3 owned<br />' +
							'If 4 owned'
						);

						var rl = document.createElement('div');
						$(rl).addClass('rentlist');
						$(rl).html(
							'<br />' +
							'$' + data.rent + '<br />' +
							'$' + data.rent1 + '<br />' +
							'$' + data.rent2 + '<br />' +
							'$' + data.rent3
						);

						$(rbar).append(hl);
						$(rbar).append(rl);
					} else {
						// utility
						var cr = document.createElement('div');
						$(cr).addClass('conditionalrent');

						$(cr).html(
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

				$(card).append(hdr);
				$(det).append(pbar);
				$(det).append(rbar);
				$(det).append(mbar);
				$(card).append(det);
				elem.append(card);
			}
		}
	);
}
