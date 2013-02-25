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
					}
				}
			);
		},
		messagePoll: function(id, isFirst) {
			var elem = $("#chatbox");
			var self = this;
			$.post('chattest.php',
				{
					'method': 'ask',
					'func': 'pollChat',
					'args': elem.chatbox("option", "state").cid,
				},
				function (data) {
					for (var i in data.instructions) {
						var d = data.instructions[i];
						if (isFirst) {
							var e = document.createElement('div');
							$('#chatlog').append(e);

							var peerName = document.createElement("span");
							$(peerName).text(d.user + ": ");
							e.appendChild(peerName);

							var msgElement = document.createElement("span");
							$(msgElement).text(d.text);
							e.appendChild(msgElement);
							$(e).addClass("ui-chatbox-msg");
						} else {
							var uc = (d.user == elem.chatbox("option", "user").name ? 'chat-me' : null);
							elem.chatbox("option", "boxManager").addMsg(d.user, d.text, {'uclass': uc});
						}
					}
					if (isFirst) {
						elem.chatbox("option", "boxManager")._scrollToBottom();
					}
					elem.chatbox("option", "state").cid = data.newstate;
				}
			);
		},
	});
});

// vi: ft=javascript
