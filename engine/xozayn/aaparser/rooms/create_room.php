<?php

/*
=====================================================
 Copyright (c) 2022-2026 Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ENGINE_DIR . '/xozayn/aaparser/functions/module.php'));

function room_hash($length) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $result = '';
    for ($i = 0; $i < $length; $i++)
        $result .= $characters[random_int(0, 61)];
    return $result;
}

if ( !$is_logged || !$member_id['user_id'] ) die(json_encode(array( 'status' => 'error', 'text' => 'Открывать комнаты совместного просмотра могут только авторизованные пользователи' )));

$news_id = intval($_GET['news_id']);

$news_row = $news_id > 0 ? $db->super_query( "SELECT id, title FROM " . PREFIX . "_post WHERE id='{$news_id}'" ) : false;
if ( !$news_row || !$news_row['id'] ) die(json_encode(array( 'status' => 'error', 'text' => 'Новость не найдена' )));

$iframe = aap_room_url($_GET['iframe'], AAP_ROOMS_IFRAME_HOSTS);
if ( $iframe === '' ) die(json_encode(array( 'status' => 'error', 'text' => 'Некорректная ссылка на плеер' )));
$poster = aap_room_url($_GET['poster']);
$title = mb_substr(trim(strip_tags(stripslashes($news_row['title']))), 0, 255, 'UTF-8');

$iframe = $db->safesql($iframe);
$poster = $db->safesql($poster);
$title = $db->safesql($title);

if ( $_GET['shikimori_id'] ) $shikimori_id = intval($_GET['shikimori_id']);
else $shikimori_id = '';
if ( $_GET['mdl_id'] ) $mdl_id = intval($_GET['mdl_id']);
else $mdl_id = '';

$leader_name = $db->safesql($member_id['name']);

if ( $aaparser_config['settings']['rooms_limit'] == 1 ) {
    $checking_exists = $db->super_query( "SELECT url, leader, news_id FROM " . PREFIX . "_rooms_list WHERE leader='{$leader_name}' AND news_id='{$news_id}'" );
    if ( $checking_exists['url'] )
        die(json_encode(array(
	        'status' => 'created',
	        'link' => $checking_exists['url']
        )));
}

$room_hash = room_hash(10);

if ( !$member_id['foto'] ) $member_id['foto'] = '/templates/'.$config['skin'].'/dleimages/noavatar.png';
$leader_foto = $db->safesql($member_id['foto']);

$db->query( "INSERT INTO " . PREFIX . "_rooms_list (url, leader, news_id, poster, title, iframe, shikimori_id, mdl_id, created, leader_last_login) values ('{$room_hash}', '{$leader_name}', '{$news_id}', '{$poster}', '{$title}', '{$iframe}', '{$shikimori_id}', '{$mdl_id}', '{$_TIME}', '{$_TIME}')" );
$db->query( "INSERT INTO " . PREFIX . "_rooms_chat (room_url, login, avatar, time, message) values ('{$room_hash}', '{$leader_name}', '{$leader_foto}', '{$_TIME}', 'Создал и вошел в комнату')" );
$db->query( "INSERT INTO " . PREFIX . "_rooms_visitors (room_url, login, avatar, time) values ('{$room_hash}', '{$leader_name}', '{$leader_foto}', '{$_TIME}')" );

die(json_encode(array(
	'status' => 'created',
	'link' => $room_hash
)));