$(document).ready(function () {
	//Progressbar initialization
	var
	intervalId,
	pbar = $("#pbar")
		.progressbar({
			value: 0,
		}),

	//Button click event
	oper = $("#oper")
		.click(function (e) {
			//Disabling button
			oper.attr('disabled', 'disabled');

			//Making sure that progress indicate 0
			pbar
				.data('val', 0)
				.progressbar('value', 0);

			//Perform POST for triggering long running operation
			$.post('poll.php?start=1', function (data) {
				//Updating progress
				pbar
					.data('val', data.progress)
					.progressbar('value', data.progress);
				//Setting the timer
				intervalId = window.setInterval(function () {
					//Getting current operation progress
					$.get('poll.php', function (data) {
						//Updating progress
						pbar
							.data('val', data.progress)
							.progressbar('value', data.progress);

						//If operation is complete
						if (data.progress >= 100) {
							//Clear timer
							window.clearInterval(intervalId);
							//Enable button
							//$("#pbar").attr('disabled', '');
						}
					});
				}, 500);
			});
		}),

	noper = $('#noper')
		.click(function (e) {
			window.clearInterval(intervalId);
			/*
			intervalId = window.setInterval(function () {
				pbar.progressbar('value', pbar.data('val'));
			}, 500);
			*/
		});
});
