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
	});
});
