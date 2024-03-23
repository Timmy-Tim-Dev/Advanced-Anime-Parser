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

$news_id = intval($_REQUEST['news_id']);
$kodik_data = $_REQUEST['kodik_data'];
$action = $_REQUEST['action'];

require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';

$maximum_animes = $aaparser_config['player']['max_remembers'];

if ( $is_logged && $member_id['user_id'] && $news_id && $kodik_data && $maximum_animes ) {
    
    if ( !$kodik_data['episode'] ) die(json_encode(array( 'status' => false )));
    
  	if ( $member_id['watched_series'] ) $watched_series = json_decode($member_id['watched_series'], true);
  	else $watched_series = [];
  
  	foreach ( $watched_series as $key => $value ) {
      	if ( $value['news_id'] == $news_id ) unset($watched_series[$key]);
    }
    
    $kodik_translation_title = explode('(', $kodik_data['translation']['title']);
  
  	$new_serie = [
      	'news_id' => $news_id,
        'episode' => $kodik_data['episode'],
        'season' => $kodik_data['season'],
        'translation' => trim($kodik_translation_title[0])
    ];
  	
  	array_unshift($watched_series, $new_serie);
  
  	if ( $maximum_animes > 0 ) {
      	$watched_series = array_slice($watched_series, 0, $maximum_animes);
    }
  
  	$my_watched_series = json_encode($watched_series, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
  	$db->query( "UPDATE " . PREFIX . "_users SET watched_series='{$my_watched_series}' WHERE user_id='{$member_id['user_id']}'" );
  	die(json_encode(array(
		'msg' => 'ok',
    	'status' => true,
      	'season' => $kodik_data['season'],
      	'episode' => $kodik_data['episode'],
      	'translator' => $kodik_data['translation']['id'],
      	'news_id' => $news_id
	)));
}
elseif ( $news_id && $kodik_data && $maximum_animes ) {
    
    if ( !$kodik_data['episode'] ) die(json_encode(array( 'status' => false )));
    
    $kodik_translation_title = explode('(', $kodik_data['translation']['title']);
    
    if (isset($_COOKIE['watched_series_'.$news_id])) {
        unset($_COOKIE['watched_series_'.$news_id]); 
        setcookie('watched_series_'.$news_id, null, -1, '/'); 
    }
    setcookie('watched_series_'.$news_id, trim($kodik_translation_title[0]).','.$kodik_data['season'].','.$kodik_data['episode'], time()+3600);
    die(json_encode(array(
		'msg' => 'ok',
    	'status' => true,
      	'season' => $kodik_data['season'],
      	'episode' => $kodik_data['episode'],
      	'translator' => $kodik_data['translation']['id'],
      	'news_id' => $news_id
	)));
}
if ( $is_logged && $member_id['user_id'] && $news_id && $action == 'delete_watched' ) {
    if ( $member_id['watched_series'] ) $watched_series = json_decode($member_id['watched_series'], true);
  	else $watched_series = [];
  
  	foreach ( $watched_series as $key => $value ) {
      	if ( $value['news_id'] == $news_id ) unset($watched_series[$key]);
    }
    
    $my_watched_series = json_encode($watched_series, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
  	$db->query( "UPDATE " . PREFIX . "_users SET watched_series='{$my_watched_series}' WHERE user_id='{$member_id['user_id']}'" );
  	die(json_encode(array(
		'msg' => 'ok',
    	'status' => true
	)));
}
elseif ( $news_id && $action == 'delete_watched' ) {
    if (isset($_COOKIE['watched_series_'.$news_id])) {
        unset($_COOKIE['watched_series_'.$news_id]); 
        setcookie('watched_series_'.$news_id, null, -1, '/'); 
    }

    die(json_encode(array(
		'msg' => 'ok',
    	'status' => true
	)));
}