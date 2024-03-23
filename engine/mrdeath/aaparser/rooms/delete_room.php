<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

ini_set("memory_limit","256M");
ini_set('max_execution_time',300);
ignore_user_abort(true);
set_time_limit(500);
session_write_close();

include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';

@header('Content-type: text/html; charset=' . $config['charset']);

date_default_timezone_set($config['date_adjust']);

if ( isset($_GET['key']) && $_GET['key'] != $aaparser_config['settings']['cron_key'] ) die('Cron secret key is wrong');
elseif ( !isset($_GET['key']) ) die('Cron secret key is empty');


$rooms = $db->query( "SELECT url, leader FROM " . PREFIX . "_rooms_list" );
$rooms_list = [];
while($temp_rooms = $db->get_row($rooms)) {
    $rooms_list[$temp_rooms['url']] = $temp_rooms['leader'];
}

foreach ( $rooms_list as $room_url => $room_leader ) {
    $check_room = $db->super_query( "SELECT time, room_url, login FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_url}' AND login='{$room_leader}'" );
    if ( !$check_room['time'] || $check_room['time'] < ($_TIME-10800) ) {
        $db->query("DELETE FROM " . PREFIX . "_rooms_list WHERE url='{$room_url}'");
        $db->query("DELETE FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_url}'");
        $db->query("DELETE FROM " . PREFIX . "_rooms_chat WHERE room_url='{$room_url}'");
    }
    unset($check_room);
}
die('Rooms was cleared');