<?php
header('Content-Type: text/javascript');
?>
$(document).ready(function(){
	window.cbox = $("#chatbox").chatbox({
		id: "chatbox",
		user:{"name": "h3xx"},
		title: "test chat",
		pollInterval: 200,
		state: {cid:-1},
		messageSent: function(id, user, msg) {
			//$("#chatlog").append(user.name + ": " + msg + "<br/>");
			$.post('chattest.php',
				{
					'method': 'tell',
					'func': 'chat',
					'args': msg,
				},
				function (data) {
					if (!data.result) {
						alert("Failed to post message.");
					} else {
						var elem = $("#chatbox");
						elem.chatbox("option", "pollSemaphore").hold = true;
						elem.chatbox("option", "boxManager").addMsg(user.name, msg, {'uclass': 'chat-me'});
						elem.chatbox("option", "state").cid = data.newstate;
						elem.chatbox("option", "pollSemaphore").hold = false;
					}
				}
			);
		},
		messagePoll: function(id) {
			var elem = $("#chatbox");
			$.post('chattest.php',
				{
					'method': 'ask',
					'func': 'pollChat',
					'args': elem.chatbox("option", "state").cid,
				},
				function (data) {
					for (var i in data.instructions) {
						var d = data.instructions[i];
						var uc = (d.user == elem.chatbox("option", "user").name ? 'chat-me' : null);
						elem.chatbox("option", "boxManager").addMsg(d.user, d.text, {'uclass': uc});
					}
					elem.chatbox("option", "state").cid = data.newstate;
				}
			);

		},
	});
});

