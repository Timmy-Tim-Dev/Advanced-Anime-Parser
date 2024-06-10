<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
  
if( !defined('DATALIFEENGINE' ) ) {
	die('Hacking attempt!');
}

if (!$config['allow_registration'])   $dle_login_hash = sha1( SECURE_AUTH_KEY . $_IP );
if($_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash) die('Hacking attempt!');

if ( $_REQUEST['subaction'] == 'subscribe' && $_REQUEST['post_id'] ) {
    if ( $member_id['push_subscribe'] ) $subscribes = json_decode($member_id['push_subscribe'], true);
  	else $subscribes = [];
  	$subscribes[] = $_REQUEST['post_id'];
  	$subscribes = array_unique($subscribes);
  	$new_subscribes = json_encode($subscribes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
  	$db->query( "UPDATE " . PREFIX . "_users SET push_subscribe='{$new_subscribes}' WHERE user_id='{$member_id['user_id']}'" );
  	clear_cache(array('subscribes_'));
    die(json_encode(array( 'status' => 'subscribed', )));
} elseif ( $_REQUEST['subaction'] == 'unsubscribe' && $_REQUEST['post_id'] ) {
    if ( $member_id['push_subscribe'] ) $subscribes = json_decode($member_id['push_subscribe'], true);
  	else $subscribes = [];
  	foreach ( $subscribes as $nposts => $vposts ) {
      	if ( $vposts == $_REQUEST['post_id'] ) unset($subscribes[$nposts]);
    }
  	$new_subscribes = json_encode($subscribes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
  	$db->query( "UPDATE " . PREFIX . "_users SET push_subscribe='{$new_subscribes}' WHERE user_id='{$member_id['user_id']}'" );
  	clear_cache(array('subscribes_'));
    die(json_encode(array( 'status' => 'unsubscribed', )));
}

?>