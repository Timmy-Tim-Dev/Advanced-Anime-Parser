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

$action = $_GET['action'];
$room_id = $_GET['room_id'];

if ( $action == 'send' ) {
  	$message = $_GET['message'];
  	if ( !$member_id['foto'] ) $member_id['foto'] = '/templates/'.$config['skin'].'/dleimages/noavatar.png';
  	$message_area = '<div class="room-chat__message system">
               <div class="room-chat__avatar">
                  <img src="'.$member_id['foto'].'" alt="Аватарка">
               </div>
               <div class="room-chat__text">'.$message.'</div>
               <div class="room-chat__time">'.date("H:i", $_TIME).'</div>
            </div>';
  
  	$message = $db->safesql( $message );
  
  	$db->query( "INSERT INTO " . PREFIX . "_rooms_chat (room_url, login, avatar, time, message) values ('{$room_id}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}', '{$message}')" );

  	$last_chat_id = $db->insert_id();
  
  	die(json_encode(array(
		'status' => 'sended',
		'message' => $message_area,
		'last_chat_id' => $last_chat_id
	)));
}
elseif ( $action == 'check' ) {
    
    include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';
    
  	$room_leader = $_GET['room_leader'];
  	$room_msg = $room_visitors = [];
    $last_chat_id = 0;
  	$db->query("SELECT * FROM " . PREFIX . "_rooms_chat WHERE room_url='{$room_id}' ORDER BY id DESC");
  	while ( $room_msg_row = $db->get_row() ) {
  	    if ( $room_msg_row['id'] > $last_chat_id ) $last_chat_id = $room_msg_row['id'];
      	$room_msg[] = '<div class="room-chat__message system">
               <div class="room-chat__avatar">
                  <img src="'.$room_msg_row['avatar'].'" alt="Аватарка">
               </div>
               <div class="room-chat__text">'.$room_msg_row['message'].'</div>
               <div class="room-chat__time">'.date("H:i", $room_msg_row['time']).'</div>
            </div>';
    }
  	$db->free();
  	$room_msg = implode('', $room_msg);
  
  	$db->query( "UPDATE " . PREFIX . "_rooms_visitors SET time='{$_TIME}' WHERE room_url='{$room_id}' AND login='{$member_id['name']}'" );
  	
  	if ( $aaparser_config['settings']['leader_afk'] == 1 && $room_leader == $member_id['name'] )
  	    $db->query( "UPDATE " . PREFIX . "_rooms_list SET leader_last_login='{$_TIME}' WHERE url='{$room_id}' AND leader='{$room_leader}'" );
  
  	$room_online_time = $_TIME-30;
  
  	$db->query("SELECT * FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_id}' AND time>'".$room_online_time."'");
  	while ( $room_online_row = $db->get_row() ) {
      	if ( $room_online_row['login'] == $room_leader )
      		$room_visitors[] = '<div class="room-user">
            <div class="room-user__avatar">
               <img src="'.$room_online_row['avatar'].'" alt="Аватар">
            </div>
            <div class="room-user__name">
               '.$room_online_row['login'].'
            </div>
            <div class="room-user__role">
               <i class="'.$aaparser_config_push['push_notifications']['fa_icons_rooms'].' fa-crown"></i>
            </div>
         </div>';
      	else
      		$room_visitors[] = '<div class="room-user">
            <div class="room-user__avatar">
               <img src="'.$room_online_row['avatar'].'" alt="Аватар">
            </div>
            <div class="room-user__name">
               '.$room_online_row['login'].'
            </div>
         </div>';
    }
  	$db->free();
  	$room_visitors = implode('', $room_visitors);
  
  	$room_row = $db->super_query("SELECT * FROM " . PREFIX . "_rooms_list WHERE url='{$room_id}'");
  	if ( $room_row['leader'] != $member_id['name'] ) $not_leader = "yes";
  
  	die(json_encode(array(
		'status' => 'updated',
		'messages' => $room_msg,
		'visitors' => $room_visitors,
		'not_leader' => $not_leader,
		'time' => $room_row['time'],
		'episode' => $room_row['episode_num'],
		'season' => $room_row['season_num'],
		'translation' => $room_row['translation'],
		'visitors_iframe' => $room_row['visitors_iframe'].'?translations=false',
		'pause' => $room_row['pause'],
		'last_chat_id' => $last_chat_id
	)));
}
elseif ( $action == 'update_time' ) {
  	$room_time = $_GET['time']+1;
  	
  	$db->query( "UPDATE " . PREFIX . "_rooms_list SET time='{$room_time}', leader_last_login='{$_TIME}' WHERE url='{$room_id}'" );
  	die(json_encode(array(
		'status' => 'updated'
	)));
}
elseif ( $action == 'set_pause' ) {
    if ( !$member_id['foto'] ) $member_id['foto'] = '/templates/'.$config['skin'].'/dleimages/noavatar.png';
  	$db->query( "UPDATE " . PREFIX . "_rooms_list SET pause=1, leader_last_login='{$_TIME}' WHERE url='{$room_id}'" );
  	$db->query( "INSERT INTO " . PREFIX . "_rooms_chat (room_url, login, avatar, time, message) values ('{$room_id}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}', 'Поставил на паузу')" );
  	die(json_encode(array(
		'status' => 'paused'
	)));
}
elseif ( $action == 'set_play' ) {
    if ( !$member_id['foto'] ) $member_id['foto'] = '/templates/'.$config['skin'].'/dleimages/noavatar.png';
  	$db->query( "UPDATE " . PREFIX . "_rooms_list SET pause=0, leader_last_login='{$_TIME}' WHERE url='{$room_id}'" );
  	$db->query( "INSERT INTO " . PREFIX . "_rooms_chat (room_url, login, avatar, time, message) values ('{$room_id}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}', 'Продолжил просмотр')" );
  	die(json_encode(array(
		'status' => 'play'
	)));
}
elseif ( $action == 'set_episode' ) {
    if ( !$member_id['foto'] ) $member_id['foto'] = '/templates/'.$config['skin'].'/dleimages/noavatar.png';
    include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';
    require_once ENGINE_DIR . '/mrdeath/aaparser/functions/module.php';
    
  	$room_episode = $_GET['episode'];
  	$shikimori_id = $_GET['shikimori_id'];
  	$mdl_id = $_GET['mdl_id'];
  
  	if ( $room_episode['episode'] ) $episode_num = $room_episode['episode'];
  	else $episode_num = 0;
  	if ( $room_episode['season'] ) $season_num = $room_episode['season'];
  	else $season_num = 0;
  	if ( $room_episode['translation']['id'] ) $translation = $room_episode['translation']['id'];
  	else $translation = 0;
  	
  	if ( $aaparser_config['settings']['kodik_api_key'] ) $kodik_apikey = $aaparser_config['settings']['kodik_api_key'];
    else $kodik_apikey = '9a3a536a8be4b3d3f9f7bd28c1b74071';
    
    if ( isset($aaparser_config['settings']['kodik_api_domain']) ) $kodik_api_domain = $aaparser_config['settings']['kodik_api_domain'];
    else $kodik_api_domain = 'https://kodikapi.com/';
    
    if ( $shikimori_id ) $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&shikimori_id='.$shikimori_id.'&with_material_data=true&translation_id='.$translation.'&with_episodes_data=true');
    else $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&mdl_id='.$mdl_id.'&with_material_data=true&translation_id='.$translation.'&with_episodes_data=true');
    
    $kodik_result = array_shift($kodik['results']);
    if ( $kodik_result['type'] == "anime-serial" || $kodik_result['type'] == "foreign-serial" ) $visitors_link = $kodik_result['seasons'][$season_num]['episodes'][$episode_num]['link'];
    else $visitors_link = $kodik_result['link'];
  
  	$db->query( "UPDATE " . PREFIX . "_rooms_list SET episode_num='{$episode_num}', season_num='{$season_num}', translation='{$translation}', visitors_iframe ='{$visitors_link}', leader_last_login = '{$_TIME}' WHERE url='{$room_id}'" );
  	if ( $episode_num > 0 ) $db->query( "INSERT INTO " . PREFIX . "_rooms_chat (room_url, login, avatar, time, message) values ('{$room_id}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}', 'Переключил на {$episode_num} серию')" );
  	die(json_encode(array(
		'status' => 'complete',
      	'episode' => $episode_num
	)));
}
elseif ( $action == 'room_status' ) {
    if ( $_GET['room_status'] == "public" ) $db->query( "UPDATE " . PREFIX . "_rooms_list SET public=1 WHERE url='{$room_id}'" );
  	else $db->query( "UPDATE " . PREFIX . "_rooms_list SET public=0 WHERE url='{$room_id}'" );
  	die(json_encode(array(
		'status' => 'ok'
	)));
}