var multiplayer_client=function(refresh_room, initialize, receive_invitation, show_rejection, begin_match, update_match, end_match, resign_opponent, disconnect_opponent, connection_closed, connection_error, close_repeated_instance,get_self_details, set_connection_status) {
	var _multiplayer_client={};
	function get_multiplayer_details(cryptic_user_details) {
		var cryptic_array = cryptic_user_details.match(/\d{3}/g);
		var i = cryptic_array.length;
		while(i--)
			cryptic_array[i] = String.fromCharCode((cryptic_array[i]-5)/4+6);
		return cryptic_array.join('');
	}
	var query_string = document.location.search.substring(1) || 'empty';
	var user_details = get_multiplayer_details(query_string.replace(/.*multiplayerDetails=([0-9]+).*/,'$1')).split(';');
	var self = {};
	for(var i in user_details) {
		self[user_details[i].split('=')[0]] = user_details[i].split('=')[1];
	}
	get_self_details(self);
	// var sock_url = 'ws://msmpgserver-eternity.rhcloud.com:8000/';
	// var sock_url = 'ws://192.168.0.7:8080/';
	// var sock_url = 'ws://10.81.234.6:8080/';
	// var sock_url = 'ws://tejit-pc:8080';
	// var sock_url = 'ws://192.168.1.42:8080';
	var sock_url = ' ws://122.248.246.221:8998';
	var room = self['gameID']+'-'+self['schoolCode']+'_'+self['childClass'];
	if(typeof WebSocket==='undefined') {
		set_connection_status('offline');
		connection_closed('websocket_undefined');
		return _multiplayer_client;
	}
	var connection = new WebSocket(sock_url, 'msmultiplayer');
	connection.onopen = function(event) {
		connection.send(JSON.stringify({
			'subject': 'initialize',
			'from': self['userID'],
			'room': room,
			'user_details': self
		}));
		/*connection.send(JSON.stringify({
			'subject': 'initialize',
			'userID': self['userID'],
			'childName': self['childName'],
			'profilePicture': self['profilePicture'],
			'childClass': self['childClass'],
			'schoolCode': self['schoolCode'],
			'room': room
		}));*/
		set_connection_status('online');
	};
	connection.onmessage=function(event) {
		var message = JSON.parse(event.data);
		switch(message['subject']) {
			case 'initialize':
				initialize(message['room_users_list']);
				break;
			case 'refresh_room':
				refresh_room(message['room_users_list']);
				break;
			case 'invitation':
				receive_invitation(message['from'], message['match_id'], message['to']);
				break;
			case 'reject_invite':
				show_rejection(message['from'], message['match_id'], message['reject_type']);
				break;
			case 'begin_match':
				begin_match(message['match_id'], message['match_setup']);
				break;
			case 'update_match':
				update_match(message['type'], message['from'], message['match_data']);
				break;
			case 'end_match':
				end_match(message['from'], message['cause']);
				break;
			case 'resignation':
				resign_opponent(message['from']);
				break;
			case 'player_disconnect':
				disconnect_opponent(message['player'], message['status']);
				break;
			case 'multiple_instances':
				close_repeated_instance();
				break;
		}
	};
	connection.onclose = function(event) {
		console.log('closed');
		set_connection_status('offline');
		console.log(event);
		connection_closed();
	};
	connection.onerror = function(event) {
		set_connection_status('offline');
		console.log('error');
		console.log(event);
		connection_error();
	};
	_multiplayer_client.send = function(sending_parameters) {
		sending_parameters['from'] = self['userID'];
		connection.send(JSON.stringify(sending_parameters));
	};
	_multiplayer_client.close = function() {
		connection.close();
	};
	return _multiplayer_client;
};
