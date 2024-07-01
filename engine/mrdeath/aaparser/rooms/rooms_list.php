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

$dle_module = 'rooms_list';

if ( $member_id['user_group'] != 5 ) {
  
  	$canonical = $config['http_home_url'].'rooms/';
  	$metatags['title'] = 'Совместный просмотр онлайн на растоянии';
  	$page_description = $metatags_description = 'Смотри вместе с друзьями одновременно на расстоянии. Комнаты для просмотра с чатом.';

	$url_page = $config['http_home_url'] . "rooms/";
	$user_query = "do=rooms_list";

	if ($cstart < 0) $cstart = 0;
	if ($cstart) {
		$cstart = $cstart - 1;
		$cstart = $cstart * $config['news_number'];
	}
  	if ($cstart) $cache_id = ($cstart / $config['news_number']) + 1;
	else $cache_id = 1;
  
  	$active = $short_news_cache = false;
	$disable_index = 1;
	
	if ( $aaparser_config['settings']['active_time'] ) $activetime = intval($aaparser_config['settings']['active_time']);
	else $activetime = 30;
  
    if ( $aaparser_config['settings']['show_private'] == 1 ) {
	    $sql_select = "SELECT r.url, r.leader, r.public, r.episode_num, r.season_num, r.created, r.leader_last_login, rv.avatar as leader_avatar, p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_rooms_list r LEFT JOIN " . PREFIX . "_rooms_visitors rv ON (r.url=rv.room_url AND r.leader=rv.login) LEFT JOIN " . PREFIX . "_post p ON (r.news_id=p.id) LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE r.leader_last_login>'".($_TIME-$activetime)."' AND p.approve=1 ORDER BY r.id DESC LIMIT " . $cstart . "," . $config['news_number'];
	    $sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_rooms_list r LEFT JOIN " . PREFIX . "_rooms_visitors rv ON (r.url=rv.room_url AND r.leader=rv.login) LEFT JOIN " . PREFIX . "_post p ON (r.news_id=p.id) WHERE p.approve=1 AND r.leader_last_login>'".($_TIME-$activetime)."'";
    } else {
        $sql_select = "SELECT r.url, r.leader, r.public, r.episode_num, r.season_num, r.created, r.leader_last_login, rv.avatar as leader_avatar, p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_rooms_list r LEFT JOIN " . PREFIX . "_rooms_visitors rv ON (r.url=rv.room_url AND r.leader=rv.login) LEFT JOIN " . PREFIX . "_post p ON (r.news_id=p.id) LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE r.public = 0 AND r.leader_last_login>'".($_TIME-$activetime)."' AND p.approve=1 ORDER BY r.id DESC LIMIT " . $cstart . "," . $config['news_number'];
	    $sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_rooms_list r LEFT JOIN " . PREFIX . "_rooms_visitors rv ON (r.url=rv.room_url AND r.leader=rv.login) LEFT JOIN " . PREFIX . "_post p ON (r.news_id=p.id) WHERE p.approve=1 AND r.public = 0 AND r.leader_last_login>'".($_TIME-$activetime)."'";
    }

	include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/show.short.php'));
				
	if (!$config['allow_quick_wysiwyg']) $allow_comments_ajax = false;
      
	if ($config['files_allow']) if (strpos ( $tpl->result['content'], "[attachment=" ) !== false) $tpl->result['content'] = show_attach ( $tpl->result['content'], $attachments );
			
	if($tpl->result['content'] AND $canonical AND isset($_GET['cstart']) AND intval($_GET['cstart']) AND intval($_GET['cstart']) != 1 ) {
		if( $config['allow_alt_url'] ) $canonical .= "page/".intval($_GET['cstart'])."/";
		else {
			if ($user_query) $canonical = "{$PHP_SELF}?cstart=".intval($_GET['cstart'])."&".str_replace('&amp;', '&', $user_query);
			else $canonical = "{$PHP_SELF}?cstart=".intval($_GET['cstart']);
		}
	}
  	
}
?>