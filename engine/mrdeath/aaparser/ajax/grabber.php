<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

ini_set("memory_limit","512M");
ini_set('max_execution_time',600);
ignore_user_abort(true);
set_time_limit(600);
session_write_close();

include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';
require_once ENGINE_DIR . '/mrdeath/aaparser/functions/module.php';
require_once ENGINE_DIR . '/mrdeath/aaparser/functions/public.php';

if (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/cron.log')) {
  	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/cron.log', "w+");
  	fwrite($fp, "");
  	fclose($fp);
}

@header('Content-type: text/html; charset=' . $config['charset']);

date_default_timezone_set($config['date_adjust']);

$user_group = array ();
	
$db->query( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );
	
while ( $row = $db->get_row() ) {
		
	$user_group[$row['id']] = array ();
		
	foreach ( $row as $key => $value ) {
		$user_group[$row['id']][$key] = stripslashes($value);
	}
	
}
set_vars( "usergroup", $user_group );
$db->free();
	
if ( $aaparser_config['grabbing']['author_id'] ) $main_userid = $aaparser_config['grabbing']['author_id'];
else $main_userid = 1;

$member_id = $db->super_query("SELECT * FROM " . PREFIX . "_users WHERE user_id=".$main_userid);
set_vars('member_id', $member_id);

$now_minutes = date("i", time());
$now_hours = date("H:i", time());

if ( isset($_GET['action']) ) $action = $_GET['action'];
elseif ( $now_hours == '01:00' || $now_hours == '01:01' || $now_hours == '01:02' || $now_hours == '01:03' || $now_hours == '01:04' ) $action = 'other';
elseif ( $now_minutes == '05' || $now_minutes == '06' || $now_minutes == '07' || $now_minutes == '08' || $now_minutes == '09' ) $action = 'grabbing';
elseif ( $now_minutes == 20 && isset($aaparser_config['settings']['update_on']) && $aaparser_config['settings']['update_on'] ) $action = 'update';
elseif ( $now_minutes == 35 && isset($aaparser_config['update_news']['cat_check']) && $aaparser_config['update_news']['cat_check'] ) $action = 'category_updating';
elseif ( $now_minutes == 50 && isset($aaparser_config['update_news']['xf_check']) && $aaparser_config['update_news']['xf_check'] ) $action = 'xfields_updating';
elseif ( isset($aaparser_config['settings_anons']['anons_on']) && ($now_minutes == 11 || $now_minutes == 21 || $now_minutes == 31 || $now_minutes == 41 || $now_minutes == 51)) $action = 'anons_shiki';
elseif ( isset($aaparser_config['settings']['grab_on']) && $aaparser_config['settings']['grab_on'] ) $action = 'add';
else $action = '';

if ( isset($_GET['kind']) ) $kind = $_GET['kind'];
elseif ( $aaparser_config['settings']['working_mode'] == 1 ) $kind = 'dorama';
elseif ( $aaparser_config['settings']['working_mode'] == 2 ) {
    $temp_number = rand(1, 2);
    if ( $temp_number == 1 ) $kind = 'anime';
    else $kind = 'dorama';
}
else $kind = 'anime';

if ( $kind == 'anime' && $aaparser_config['settings']['working_mode'] == 1 ) die('В режиме работы модуля выбраны дорамы, граббинг аниме отключён');
elseif ( $kind == 'dorama' && ( $aaparser_config['settings']['working_mode'] != 1 && $aaparser_config['settings']['working_mode'] != 2 ) ) die('В режиме работы модуля выбраны аниме, граббинг дорам отключён');

if ( isset($_GET['key']) && $_GET['key'] != $aaparser_config['settings']['cron_key'] ) die('Cron secret key is wrong');
elseif ( !isset($_GET['key']) ) die('Cron secret key is empty');

$cron_log = json_decode( file_get_contents( ENGINE_DIR .'/mrdeath/aaparser/data/cron.log' ), true );
if ( $cron_log['time'] && ($cron_log['time']+$aaparser_config['settings']['cron_time']) > $_TIME ) die('Сработала защита от повторного запуска крон');

if ( isset($aaparser_config['settings']['kodik_api_domain']) ) $kodik_api_domain = $aaparser_config['settings']['kodik_api_domain'];
else $kodik_api_domain = 'https://kodikapi.com/';

if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) {
    $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
    $shikimori_image_domain = 'https://'.clean_url($shikimori_api_domain);
}
else {
    $shikimori_api_domain = 'https://shikimori.me/';
    $shikimori_image_domain = 'https://shikimori.me';
}

$cron_log['time'] = $_TIME;
file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/cron.log', json_encode( $cron_log ));

if ( $action == 'grabbing' ) {
    
    $stop_time = time() - 3 * 24 * 3600;
    $db->query( "UPDATE " . PREFIX . "_anime_list SET started=0, error=0 WHERE ( error>'0' AND error<'".$stop_time."' ) AND started=1 AND news_id=0" );
    
    if ( $kind == 'all' ) {
        $temp_number = rand(1, 2);
        if ( $temp_number == 1 ) $kind = 'anime';
        else $kind = 'dorama';
    }
    
    $parse_action = 'grab';
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
}
elseif ( $action == 'update' && $kind == 'anime' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/update_anime.php'));
}
elseif ( $action == 'update' && $kind == 'dorama' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/update_dorama.php'));
}
elseif ( $action == 'add') {
	if (isset($aaparser_config['settings']['grab_on'])) {
		include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/add_material.php'));
	} else {
		echo "Функционал граббинга отключен, пожалуйста включите это в настройках модуля!";
	}
}
elseif ( $action == 'category_updating' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/category_updating.php'));
}
elseif ( $action == 'xfields_updating' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/xfields_updating.php'));
}
elseif ( $action == 'other' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/other_actions.php'));
}
elseif ( $action == 'anons_shiki' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/anons_shiki.php'));
}
elseif ( $action == 'anons_clean' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/anons_clean.php'));
}
elseif ( $action == 'update_franchise' ) {
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/cron/update_franchise.php'));
}
else {
    echo "Были переданы неверные параметры!";
}