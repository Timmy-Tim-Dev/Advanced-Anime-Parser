<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
 
if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if ( $dle_module == 'enter_room' ) {

  	$kodik_player_js = <<<HTML
<script>
let iframe = null;
let last_message = {$last_chat_id};
let leader_time = Number({$leader_time});
let leader_iframe = '{$leader_iframe}';

$('body').on('click', '.anime-player__fullscreen-btn, .anime-player__fullscreen-btn-close', function() {
	$('body').toggleClass('room--full');
});

$('.room-chat__send-message-btn').on('click', function() {
	sendRoomMessage();
});

$('#isPublic').on('click', function() {
	var room_id = $('#room-data').attr("data-id");
	if (document.getElementById('isPublic').checked) {
        var room_status = 'public';
    } else {
        var room_status = 'private';
    }
    $.get(dle_root + "engine/ajax/controller.php?mod=message_room", { room_status: room_status, room_id: room_id, action: 'room_status', user_hash: dle_login_hash }, function(data){
		if ( data.status == "ok" ) {

		}
	}, "json");
});

var room_input = document.getElementById("room-chat");
room_input.addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    sendRoomMessage();
  }
});


function sendRoomMessage() {
    var room_id = $('#room-data').attr("data-id");
    let textArea = $('.room-chat__send-form textarea');
    if (textArea.val() == "") return false;

    $.get(dle_root + "engine/ajax/controller.php?mod=message_room", { message: textArea.val(), room_id: room_id, action: 'send', user_hash: dle_login_hash }, function(data){
		if ( data.status == "sended" ) {

        	const Item = ({
            	data
        	}) => data.message;
        	let room_messages = $('.room-chat__messages');
        	if (room_messages.length > 0) {
            	room_messages.prepend(Item({
                	data: data
            	}));
        	}
       		if (document.getElementById('soundSwitch').checked) {
            	let audio = new Audio('/uploads/room.mp3');
            	audio.play();
       		}
            last_message = Number(data.last_chat_id);
		}
	}, "json");

    textArea.val("");
}

function getRoomUpdates() {
    var room_id = $('#room-data').attr("data-id");
    var room_leader = $('#room-data').attr("data-leader");
    var now_time = $('#room-status').html();
    var room_episode = $('#room-episode').html();
    findIframe();
    $.get(dle_root + "engine/ajax/controller.php?mod=message_room", { room_leader: room_leader, room_id: room_id, action: 'check', user_hash: dle_login_hash }, function(data){
		if ( data.status == "updated" ) {
        	if ( Number(data.last_chat_id) > last_message ) {
            	last_message = Number(data.last_chat_id);
                if (document.getElementById('soundSwitch').checked) {
                	let audio = new Audio('/uploads/room.mp3');
            		audio.play();
                }
            }
            $('.room-chat__messages').html(data.messages);

            $('.room-users').html(data.visitors);
			videoSpeed(data.speed);
            if ( data.not_leader ) {
				
                if ( data.visitors_iframe != leader_iframe ) {
                	leader_iframe = data.visitors_iframe;
                    episodeChange(data.visitors_iframe, Number(data.episode));
                }
                else if ( data.pause == 1 ) {
                    pauseVideo();
                } 
                else if ( data.pause == 0 ) {
                    playVideo();
                    if ( $('#room-status').html() == 'На паузе' && document.body.classList.contains('room--full') ) {
                    	$('.anime-player__info2-btn').show();
                        $('.anime-player__info-btn').hide();
                    }
                    else if ( $('#room-status').html() == 'На паузе' ) {
                    	$('.anime-player__info2-btn').hide();
                        $('.anime-player__info-btn').show();
                    }
                }
                if ( data.time ) {
                	leader_time = Number(data.time);
                }
            }
		}
	}, "json");
}

$(document).ready(function(){
	setInterval( getRoomUpdates, 3000 );
});

function playVideo() {
    iframe.postMessage({
        key: "kodik_player_api",
        value: {
            method: "play"
        }
    }, '*');
}

function videoSpeed(valik) {
    iframe.postMessage({
        key: "kodik_player_api",
        value: {
            method: "speed",
			speed: parseFloat(valik)
        }
    }, '*');
}

function pauseVideo() {
    iframe.postMessage({
        key: "kodik_player_api",
        value: {
            method: "pause"
        }
    }, '*');
}

function seekVideo(seconds) {
    iframe.postMessage({
        key: "kodik_player_api",
        value: {
            method: "seek",
            seconds: Number(seconds)
        }
    }, '*');
    $('#room-status').html(fancyTimeFormat(Number(seconds)));
}

function episodeChange(iframe_link, set_episode) {
    $('#room-player')[0].src = iframe_link;
    if ( set_episode ) {
    	$('#room-episode').html(set_episode);
        $('.room-anime__episode').show();
    }
}

function findIframe() {
    if ($('.room__player iframe').length > 0) {
        iframe = $('.room__player iframe')[0].contentWindow;
    }
}


function toSeconds(timeStr) {
  const [hours, minutes, seconds] = timeStr.split(':').map(Number);
  return hours * 3600 + minutes * 60 + seconds;
}

function fancyTimeFormat(duration) {
  const hrs = ~~(duration / 3600);
  const mins = ~~((duration % 3600) / 60);
  const secs = ~~duration % 60;

  let ret = "";

  if (hrs > 0) {
    ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
  }

  ret += "" + mins + ":" + (secs < 10 ? "0" : "");
  ret += "" + secs;

  return ret;
}

function CopyRoomLink() {
  var copyText = $('#copy-room-link').html();
  var temp = $("<input>");
  $("body").append(temp);
  temp.val(copyText).select();
  document.execCommand("copy");
  temp.remove();
}

function kodikMessageListener(message) {
    var room_id = $('#room-data').attr("data-id");
    var room_leader = $('#room-data').attr("data-leader");
    var shikimori_id = $('#room-data').attr("data-shikimori_id");
    var mdl_id = $('#room-data').attr("data-mdl_id");
    if ( room_leader == '{$member_id['name']}' ) {
    	if (message.data.key == 'kodik_player_time_update') {
        	leader_time = message.data.value;
    		$.get(dle_root + "engine/ajax/controller.php?mod=message_room", { room_id: room_id, action: 'update_time', time: message.data.value, user_hash: dle_login_hash }, function(data){
				if ( data.status == "updated" ) {
					$('#room-status').html(fancyTimeFormat(message.data.value));
				}
			}, "json");
    	}
    	else if (message.data.key == 'kodik_player_play') {
      		$.get(dle_root + "engine/ajax/controller.php?mod=message_room", { room_id: room_id, action: 'set_play', user_hash: dle_login_hash }, function(data){
				if ( data.status == "play" ) {
					
				}
			}, "json");
    	}
    	else if (message.data.key == 'kodik_player_pause') {
      		$.get(dle_root + "engine/ajax/controller.php?mod=message_room", { room_id: room_id, action: 'set_pause', user_hash: dle_login_hash }, function(data){
				if ( data.status == "paused" ) {
					$('#room-status').html('На паузе');
				}
			}, "json");
    	}
		else if (message.data.key == 'kodik_player_speed_change') {
      		$.get(dle_root + "engine/ajax/controller.php?mod=message_room", { room_id: room_id, action: 'set_speed', speed: message.data.value.speed, user_hash: dle_login_hash }, function(data){
				if ( data.speed ) {
					
				}
			}, "json");
    	}
    	else if (message.data.key == 'kodik_player_current_episode') {
      		$.get(dle_root + "engine/ajax/controller.php?mod=message_room", { room_id: room_id, action: 'set_episode', episode: message.data.value, shikimori_id: shikimori_id, mdl_id: mdl_id, user_hash: dle_login_hash }, function(data){
				if ( data.status == "complete" && data.episode ) {
					$('#room-episode').html(data.episode);
                    $('.room-anime__episode').show();
				}
			}, "json");
    	}
	} else {
    	if (message.data.key == 'kodik_player_time_update') {
        	if ( leader_time > Number(message.data.value)+6 || leader_time < Number(message.data.value)-6 ) {
            	seekVideo(leader_time);
            }
    		$('#room-status').html(fancyTimeFormat(message.data.value));
            $('.anime-player__info2-btn').hide();
            $('.anime-player__info-btn').hide();
    	}
    	else if (message.data.key == 'kodik_player_pause') {
      		$('#room-status').html('На паузе');
    	}
    }
}

  if (window.addEventListener) {
    window.addEventListener('message', kodikMessageListener);
  } else {
    window.attachEvent('onmessage', kodikMessageListener);
  }

$(document).ready(function(){
	$('.room-anime__episode').hide();
});
</script></body>
HTML;
  	$tpl->result['main'] = str_replace('</body>', $kodik_player_js, $tpl->result['main']);
}