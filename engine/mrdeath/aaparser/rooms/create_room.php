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

function room_hash($length) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $result = '';
    for ($i = 0; $i <= $length; $i++)
        $result .= $characters[mt_rand(0, 61)];
    return $result;
}

$news_id = $_GET['news_id'];
$iframe = $_GET['iframe'];
$title = $_GET['title'];
$poster = $_GET['poster'];
if ( $_GET['shikimori_id'] ) $shikimori_id = $_GET['shikimori_id'];
else $shikimori_id = '';
if ( $_GET['mdl_id'] ) $mdl_id = $_GET['mdl_id'];
else $mdl_id = '';

if ( $aaparser_config['settings']['rooms_limit'] == 1 ) {
    $checking_exists = $db->super_query( "SELECT url, leader, news_id FROM " . PREFIX . "_rooms_list WHERE leader='{$member_id['name']}' AND news_id='{$news_id}'" );
    if ( $checking_exists['url'] ) 
        die(json_encode(array(
	        'status' => 'created',
	        'link' => $checking_exists['url']
        )));
}

$room_hash = room_hash(10);

if ( !$member_id['foto'] ) $member_id['foto'] = '/templates/'.$config['skin'].'/dleimages/noavatar.png';

$db->query( "INSERT INTO " . PREFIX . "_rooms_list (url, leader, news_id, poster, title, iframe, shikimori_id, mdl_id, created, leader_last_login) values ('{$room_hash}', '{$member_id['name']}', '{$news_id}', '{$poster}', '{$title}', '{$iframe}', '{$shikimori_id}', '{$mdl_id}', '{$_TIME}', '{$_TIME}')" );
$db->query( "INSERT INTO " . PREFIX . "_rooms_chat (room_url, login, avatar, time, message) values ('{$room_hash}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}', 'Создал и вошел в комнату')" );
$db->query( "INSERT INTO " . PREFIX . "_rooms_visitors (room_url, login, avatar, time) values ('{$room_hash}', '{$member_id['name']}', '{$member_id['foto']}', '{$_TIME}')" );

die(json_encode(array(
	'status' => 'created',
	'link' => $room_hash
)));