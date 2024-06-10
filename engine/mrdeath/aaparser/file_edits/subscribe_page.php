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

$error_subscribes = false;
$allow_userinfo = true;
$user_title = $member_id['name'];
$member_name = totranslit($user_title, true, false);
$dle_module = 'subscribe_page';

$my_subscribes = dle_cache( "subscribes_".$member_name, false );
if( $my_subscribes === false ) {
	$user_row = $db->super_query("SELECT name, push_subscribe FROM " . PREFIX . "_users WHERE name='{$user_title}'");
	if ( $user_row['push_subscribe'] ) {
		$my_subscribes = $user_row['push_subscribe'];
	  	create_cache( "subscribes_".$member_name, $my_subscribes, false );
	} else $error_subscribes = true;
}
if ( $error_subscribes === false ) {

	$my_subscribes = json_decode($my_subscribes, true);

  	$list_link = $config['http_home_url'].'subscribes/';
  	$canonical = $list_link;

  	$metatags['title'] = 'Подписки на уведомления пользователя '.$user_title;

  	$page_description = $metatags_description = $metatags['title'];

	$url_page = $config['http_home_url'] . "subscribes";
	$user_query = "do=subscribes&amp;user=" . urlencode ( $user_title );

	$where = "p.id IN ('" . implode("','", $my_subscribes) . "')";
	$where_count = "id IN ('" . implode("','", $my_subscribes) . "')";

	if ($cstart < 0) $cstart = 0;
	if ($cstart) {
		$cstart = $cstart - 1;
		$cstart = $cstart * $config['news_number'];
	}
  	if ($cstart) $cache_id = ($cstart / $config['news_number']) + 1;
	else $cache_id = 1;

  	$cache_prefix = $member_name."_".$cache_id;

  	$config['max_cache_pages'] = intval($config['max_cache_pages']);
	if($config['max_cache_pages'] < 3) $config['max_cache_pages'] = 3;

  	if ($config['allow_cache'] AND $cache_id <= $config['max_cache_pages']) {
		$active = dle_cache( "subscribes_", $cache_prefix, true );
		if( $active ) $active = json_decode($active, true);
		$short_news_cache = true;
	} else $active = $short_news_cache = false;

  	if ( is_array($active) ) {

		if( isset( $active['content'] ) ) $tpl->result['content'] .= $active['content'];

		if( isset($active['navigation']) ) $tpl->result['navigation'] = $active['navigation'];
		else $tpl->result['navigation'] = '';

		if( isset( $active['last-modified'] ) ) {
			if( $active['last-modified'] > $_DOCUMENT_DATE ) $_DOCUMENT_DATE = $active['last-modified'];
		}

		$active = null;
		$news_found = true;
		if ($config['allow_quick_wysiwyg'] and ($user_group[$member_id['user_group']]['allow_edit'] or $user_group[$member_id['user_group']]['allow_all_edit'])) $allow_comments_ajax = true;
		else $allow_comments_ajax = false;

	} else {

		$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$where} AND approve=1 ORDER BY p.date DESC LIMIT " . $cstart . "," . $config['news_number'];
		$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE {$where_count} AND approve=1";

		include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/show.short.php'));

		if (!$config['allow_quick_wysiwyg']) $allow_comments_ajax = false;

		if ($config['files_allow']) if (strpos ( $tpl->result['content'], "[attachment=" ) !== false) $tpl->result['content'] = show_attach ( $tpl->result['content'], $attachments );

		if ($news_found AND $cache_id <= $config['max_cache_pages'] ) create_cache ( "subscribes_", json_encode( array('content' => $tpl->result['content'], 'navigation' => $tpl->result['navigation'], 'description' => $page_description, 'last-modified' => $_DOCUMENT_DATE ) , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), $cache_prefix, true );
	}

	if($tpl->result['content'] AND $canonical AND isset($_GET['cstart']) AND intval($_GET['cstart']) AND intval($_GET['cstart']) != 1 ) {
		if( $config['allow_alt_url'] ) $canonical .= "page/".intval($_GET['cstart'])."/";
		else {
			if ($user_query) $canonical = "{$PHP_SELF}?cstart=".intval($_GET['cstart'])."&".str_replace('&amp;', '&', $user_query);
			else $canonical = "{$PHP_SELF}?cstart=".intval($_GET['cstart']);
		}
	}

}
?>

