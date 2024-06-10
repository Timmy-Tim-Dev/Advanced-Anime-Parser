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

$room_id = $_GET['hash'];

$disable_index = 1;

$room_row = $db->super_query("SELECT * FROM " . PREFIX . "_rooms_list WHERE url='{$room_id}'");

if ( $room_row ) {
    
    $canonical = $config['http_home_url'].'room/'.$room_row['url'].'/';
  	
  	$metatags['title'] = 'Совместный просмотр '.$room_row['title'].' на растоянии';
  
  	$page_description = $metatags_description = 'Смотри '.$room_row['title'].' с друзьями одновременно на расстоянии. Комнаты для просмотра '.$room_row['title'].' с чатом.';
  
  	$tpl->load_template( 'rooms.tpl' );
    
  	if ( $room_row['leader'] != $member_id['name'] ) $its_leader = false;
  	else $its_leader = true;
  	
  	$leader_time = $room_row['time'];
  	$leader_iframe = $room_row['visitors_iframe'].'?translations=false';
  
  	$tpl->set( '{poster}', $room_row['poster'] );
  	$tpl->set( '{title}', $room_row['title'] );
  	if ( $room_row['leader'] != $member_id['name'] && $room_row['visitors_iframe'] ) $tpl->set( '{iframe}', $room_row['visitors_iframe'].'?translations=false' );
  	elseif ( $room_row['leader'] != $member_id['name'] ) $tpl->set( '{iframe}', $room_row['iframe'].'?translations=false' );
  	else $tpl->set( '{iframe}', $room_row['iframe'] );
  	$tpl->set( '{episode_num}', $room_row['episode_num'] );
  	$tpl->set( '{leader}', $room_row['leader'] );
  	$tpl->set( '{link}', $config['http_home_url']."room/".$room_row['url']."/" );
  	$tpl->set( '{id}', $room_row['url'] );
  	$tpl->set( '{shikimori_id}', $room_row['shikimori_id'] );
  	$tpl->set( '{mdl_id}', $room_row['mdl_id'] );
  	if ( $room_row['public'] == 1 ) {
  	    $tpl->set( '{public}', "checked" );
  	    $tpl->set( '[public]', "" );
		$tpl->set( '[/public]', "" );
  	} else {
  	    $tpl->set( '{public}', "" );
  	    $tpl->set_block( "'\\[public\\](.*?)\\[/public\\]'si", "" );
  	}
  	
  	if ( $its_leader === true ) {
  	    $tpl->set( '[if_leader]', "" );
		$tpl->set( '[/if_leader]', "" );

	} else $tpl->set_block( "'\\[if_leader\\](.*?)\\[/if_leader\\]'si", "" );
	
	if( date( 'Ymd', $room_row['created'] ) == date( 'Ymd', $_TIME ) ) $tpl->set( '{created}', $lang['time_heute'] . langdate( ", H:i", $room_row['created'] ) );
	elseif( date( 'Ymd', $room_row['created'] ) == date( 'Ymd', ($_TIME - 86400) ) ) $tpl->set( '{created}', $lang['time_gestern'] . langdate( ", H:i", $room_row['created'] ) );
	else $tpl->set( '{created}', langdate( $config['timestamp_active'], $room_row['created'] ) );
	
	$tpl->copy_template = preg_replace_callback ( "#\{created=(.+?)\}#i", "formdate", $tpl->copy_template );
  
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
  
  	$tpl->set( '{chat}', implode('', $room_msg) );
  
  	$not_first_visit = false;
  
  	$room_online_time = $_TIME-30;
  
  	$db->query("SELECT * FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_id}'");
  	while ( $room_online_row = $db->get_row() ) {
      	if ( $room_online_row['login'] == $member_id['name'] ) $not_first_visit = true;
      	if ( $room_online_row['time'] > $room_online_time ) continue;
      	if ( $room_online_row['login'] == $room_row['leader'] )
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
  
  	$tpl->set( '{online}', implode('', $room_visitors) );
  	
  	if ( $not_first_visit === false ) {
      	if ( !$member_id['foto'] ) $member_id['foto'] = '/templates/'.$config['skin'].'/dleimages/noavatar.png';
      	$db->query( "INSERT INTO " . PREFIX . "_rooms_chat (room_url, login, avatar, time, message) values ('{$room_id}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}', 'Вошел в комнату')" );
      	$db->query( "INSERT INTO " . PREFIX . "_rooms_visitors (room_url, login, avatar, time) values ('{$room_id}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}')" );
    }
  	
  	$tpl->compile( 'content' );
	$tpl->clear();
}