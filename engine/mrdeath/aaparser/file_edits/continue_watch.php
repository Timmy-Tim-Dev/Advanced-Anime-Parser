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

//Инициализация
$empty_saves = true;
$dle_module = 'continue_watch';

require_once ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php';

//Проверка авторизованности
if ( $is_logged ) {
    if ( $member_id['watched_series'] ) {
        $watched_series = json_decode($member_id['watched_series'], true);
        $empty_saves = false;
    }
    else $watched_series = [];
}

if ( $empty_saves === false ) {
	// Метатеги и т.д.
  	$list_link = $config['http_home_url'].'continue_watch/';
  	$canonical = $list_link;

  	$metatags['title'] = 'Продолжить просмотр';

  	$page_description = $metatags_description = $metatags['title'];

	$url_page = $config['http_home_url'] . "continue_watch";
	$user_query = "do=continue_watch";
    
    $user_title = $member_id['name'];
    $member_name = totranslit($user_title, true, false);
    
	//Кэш
  	$cache_prefix = $member_name."_".$cache_id;
  	$active = kodik_cache('continue_'.$member_name, false, 'continue_watch');
	$active = json_decode($active, true);
	
	//Создание просмотренных через Watched_series
	$temp_news = [];
	foreach ( $watched_series as $key => $value ) {
		if ( $value['news_id'] ) $temp_news[] = $value['news_id'];
	}
	
	// Перебор значений
	if( $active && implode(",", $active) == implode(",", $temp_news)) $news_list = $active;
	else $news_list = $temp_news;
	$short_news_cache = true;
	
    if ( $news_list ) $news_list = array_unique($news_list);
	
	//Формирование запроса
	$where = "p.id IN ('" . implode("','", $news_list) . "')";
	$where_count = "id IN ('" . implode("','", $news_list) . "')";

	$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$where} AND approve=1 ORDER BY FIND_IN_SET(id, '".implode(",", $news_list)."')";
	$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE {$where_count} AND approve=1";

	//Создание шаблона
	include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/show.short.php'));

	if (!$config['allow_quick_wysiwyg']) $allow_comments_ajax = false;

	if ($config['files_allow']) if (strpos ( $tpl->result['content'], "[attachment=" ) !== false) {
		$tpl->result['content'] = show_attach ( $tpl->result['content'], $attachments );
	}
	
	//Кэш
	if ( $news_found ) kodik_create_cache('continue_'.$member_name, json_encode($news_list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), false, 'continue_watch');

}
?>