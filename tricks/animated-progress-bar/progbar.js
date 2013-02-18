$(document).ready(function () {
	//Progressbar initialization
	$("#pbar").progressbar({
		value: 0,
//		change: function() {
//
//		},
	});
	//Button click event
	$("#oper").click(function (e) {
		//Disabling button
		$("#oper").attr('disabled', 'disabled');
		//Making sure that progress indicate 0
		$("#pbar").progressbar('value', 0);
		//Perform POST for triggering long running operation
		$.post('poll.php?start=1', function (data) {
			//Updating progress
			$("#pbar").progressbar('value', data.progress);
			//Setting the timer
			window.progressIntervalId = window.setInterval(function () {
				//Getting current operation progress
				$.get('poll.php', function (data) {
					//Updating progress
					$("#pbar").progressbar('value', data.progress);
					//If operation is complete
					if (data.progress == 100) {
						//Clear timer
						window.clearInterval(window.progressIntervalId);
						//Enable button
						$("#pbar").attr('disabled', '');
					}
				});
			}, 500);
		});
	});
});
