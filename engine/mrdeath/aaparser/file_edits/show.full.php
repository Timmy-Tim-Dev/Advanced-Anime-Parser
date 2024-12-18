<?php

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if ( $member_id['watched_series'] ) $watched_series = json_decode($member_id['watched_series'], true);
else $watched_series = [];

$watched_episode = $watched_season = 0;
$watched_translator = '';

if(isset($aaparser_config['player']['sw_setting']) && $aaparser_config['player']['sw_setting']) $sw_player_cookie = "data-player_cookie='1'";
else $sw_player_cookie = "data-player_cookie='0'";

foreach ( $watched_series as $key => $value ) {
	if ( $value['news_id'] == $row['id'] ) {
      	$watched_episode = $value['episode'];
      	$watched_season = $value['season'];
      	$watched_translator = $value['translation'];
    }
}

if ( $watched_episode && $watched_season && $watched_translator ) {
  	$tpl->set('[watched_series]', "");
  	$tpl->set('[/watched_series]', "");
	$tpl->set('{watched_episode}', $watched_episode);
	$tpl->set('{watched_season}', $watched_season);
	$tpl->set('{watched_translator}', $watched_translator);
}
else {
  	$tpl->set_block("'\\[watched_series\\](.*?)\\[/watched_series\\]'si", '');
	$tpl->set('{watched_episode}', "");
	$tpl->set('{watched_season}', "");
	$tpl->set('{watched_translator}', "");
}

if ( $aaparser_config['push_notifications']['enable'] && $is_logged ) {
	if ( $member_id['push_subscribe'] ) $push_subscribe = json_decode($member_id['push_subscribe'], true);
	else $push_subscribe = [];
	
	if ( in_array($row['id'], $push_subscribe) ) {
  		$push_subscribe_block = '<div class="js-toggle-fav btn"><span id="push_subscribe" onclick="PushSubscribe(\''.$row['id'].'\', \'subscribe\');return false;" style="display:none;"><i class="'.$aaparser_config['push_notifications']['fa_icons'].' fa-bell"></i> Отслеживать</span><span id="push_unsubscribe" class="is-active" onclick="PushSubscribe(\''.$row['id'].'\', \'unsubscribe\');return false;"><i class="'.$aaparser_config['push_notifications']['fa_icons'].' fa-bell-slash"></i> Отписаться</span></div>';
      	$user_subscribed = '<div class="poster_subscribes"><div class="poster_subscribes-item" title="Вы подписаны"><span class="'.$aaparser_config['push_notifications']['fa_icons'].' fa-bell"></span></div></div>';
	} else {
  		$push_subscribe_block = '<div class="js-toggle-fav btn"><span id="push_subscribe" onclick="PushSubscribe(\''.$row['id'].'\', \'subscribe\');return false;"><i class="'.$aaparser_config['push_notifications']['fa_icons'].' fa-bell"></i> Отслеживать</span><span id="push_unsubscribe" class="is-active" onclick="PushSubscribe(\''.$row['id'].'\', \'unsubscribe\');return false;" style="display:none;"><i class="'.$aaparser_config['push_notifications']['fa_icons'].' fa-bell-slash"></i> Отписаться</span></div>';
      	$user_subscribed = '';
	}
  
	$is_viewed = $db->super_query("select count(*) as count FROM ".PREFIX."_subscribe_info where user_id={$member_id['user_id']} and post_id=".$row['id']);
  	if ($is_viewed['count'] > 0) {
     	$db->query("DELETE FROM ".PREFIX."_subscribe_info WHERE user_id='{$member_id['user_id']}' AND post_id='{$row['id']}'");
    }
  	$tpl->set( '[push_subscribe]', "" );
  	$tpl->set( '[/push_subscribe]', "" );
	$tpl->set( '{push_subscribe}', $push_subscribe_block );
	$tpl->set( '{user_subscribed}', $user_subscribed );
} else {
  	$tpl->set_block("'\\[push_subscribe\\](.*?)\\[/push_subscribe\\]'si", '');
  	$tpl->set( '{push_subscribe}', "" );
  	$tpl->set( '{user_subscribed}', "" );
}

if ( isset($aaparser_config['player']['player_method']) && $aaparser_config['player']['player_method'] == 1 ) {
    $kodik_playlist_fullstory = 'yes';
    if ( $aaparser_config['player']['method'] == 1 ) include (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/ajax/playlist_new.php'));
    elseif ( $aaparser_config['player']['method'] == 2 ) include (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/ajax/playlist_screen.php'));
    else include (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/ajax/playlist.php'));
} else {
    if ( $aaparser_config['player']['preloader'] ) $tpl->set( '{kodik_playlist}', '<div id="kodik_player_ajax" data-news_id="'.$row['id'].'" data-has_cache="no" '.$sw_player_cookie.'><div class="loading-kodik"><div class="arc"></div><div class="arc"></div><div class="arc"></div></div></div>' );
    else $tpl->set( '{kodik_playlist}', '<div id="kodik_player_ajax" data-news_id="'.$row['id'].'" data-has_cache="no" '.$sw_player_cookie.'></div>' );
}

if ( isset($aaparser_config['persons']['personas_on']) && $aaparser_config['persons']['personas_on'] == 1 && isset($aaparser_config['main_fields']['xf_shikimori_id']) && isset($xfieldsdata[$aaparser_config['main_fields']['xf_shikimori_id']]) && $xfieldsdata[$aaparser_config['main_fields']['xf_shikimori_id']] ) {
    if ( $aaparser_config['persons']['main_characters'] == 1 ) $tpl->set( '{kodik_main_characters}', '<div id="main_characters_block" data-sh_id="'.strip_tags($xfieldsdata[$aaparser_config['main_fields']['xf_shikimori_id']]).'">Загрузка...</div>' );
    else $tpl->set( '{kodik_main_characters}', '' );
    if ( $aaparser_config['persons']['characters'] == 1 ) $tpl->set( '{kodik_sub_characters}', '<div id="sub_characters_block" data-sh_id="'.strip_tags($xfieldsdata[$aaparser_config['main_fields']['xf_shikimori_id']]).'">Загрузка...</div>' );
    else $tpl->set( '{kodik_sub_characters}', '' );
    if ( $aaparser_config['persons']['persons'] == 1 ) $tpl->set( '{kodik_persons}', '<div id="persons_block" data-sh_id="'.strip_tags($xfieldsdata[$aaparser_config['main_fields']['xf_shikimori_id']]).'">Загрузка...</div>' );
    else $tpl->set( '{kodik_persons}', '' );
} else {
    $tpl->set( '{kodik_main_characters}', '' );
    $tpl->set( '{kodik_sub_characters}', '' );
    $tpl->set( '{kodik_persons}', '' );
}
if (isset($aaparser_config['player']['voicerate_mod']) && $aaparser_config['player']['voicerate_mod'] == 1) {
    $tpl->set( '{kodik_voicerate}', '<div id="voicerate_mod" data-news_id="'.$row['id'].'">Загрузка...</div>' );
} else $tpl->set( '{kodik_voicerate}', '' );

if ( isset($aaparser_config['persons']['personas_on_dorama']) && $aaparser_config['persons']['personas_on_dorama'] == 1 && isset($aaparser_config['main_fields']['xf_mdl_id']) && isset($xfieldsdata[$aaparser_config['main_fields']['xf_mdl_id']]) && $xfieldsdata[$aaparser_config['main_fields']['xf_mdl_id']] ) {
    $kodik_persons_dorama = 'yes';
    include (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/ajax/persons.php'));
} else $tpl->set( '{kodik_persons_dorama}', '' );