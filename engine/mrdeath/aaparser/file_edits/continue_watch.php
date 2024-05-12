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

$empty_saves = true;
$dle_module = 'continue_watch';

if ( $is_logged ) {
    if ( $member_id['watched_series'] ) {
        $watched_series = json_decode($member_id['watched_series'], true);
        $empty_saves = false;
    }
    else $watched_series = [];
}

if ( $empty_saves === false ) {
    
    $user_title = $member_id['name'];
    $member_name = totranslit($user_title, true, false);
    
    $news_list = [];
    foreach ( $watched_series as $key => $value ) {
        if ( $value['news_id'] ) $news_list[] = $value['news_id'];
    }
    if ( $news_list ) $news_list = array_unique($news_list);

  	$list_link = $config['http_home_url'].'continue_watch/';
  	$canonical = $list_link;

  	$metatags['title'] = 'Продолжить просмотр';

  	$page_description = $metatags_description = $metatags['title'];

	$url_page = $config['http_home_url'] . "continue_watch";
	$user_query = "do=continue_watch";

	$where = "p.id IN ('" . implode("','", $news_list) . "')";
	$where_count = "id IN ('" . implode("','", $news_list) . "')";

  	$cache_prefix = $member_name."_".$cache_id;

  	$active = dle_cache( "continue_".$member_name, false, true );
	if( $active ) {
		$active = json_decode($active, true);
	}
	$short_news_cache = true;

  	if ( is_array($active) ) {

		if( isset( $active['content'] ) ) {
			$tpl->result['content'] .= $active['content'];
		}

		$tpl->result['navigation'] = '';

		if( isset( $active['last-modified'] ) ) {

			if( $active['last-modified'] > $_DOCUMENT_DATE ) {
				$_DOCUMENT_DATE = $active['last-modified'];
			}

		}

		$active = null;
		$news_found = true;
		if ($config['allow_quick_wysiwyg'] and ($user_group[$member_id['user_group']]['allow_edit'] or $user_group[$member_id['user_group']]['allow_all_edit'])) $allow_comments_ajax = true;
		else $allow_comments_ajax = false;

	} else {

		$sql_select = "SELECT p.id, p.autor, p.date, p.short_story, CHAR_LENGTH(p.full_story) as full_story, p.xfields, p.title, p.category, p.alt_name, p.comm_num, p.allow_comm, p.fixed, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.view_edit, e.editdate, e.editor, e.reason FROM " . PREFIX . "_post p LEFT JOIN " . PREFIX . "_post_extras e ON (p.id=e.news_id) WHERE {$where} AND approve=1 ORDER BY FIND_IN_SET(id, '".implode(",", $news_list)."')";
		$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE {$where_count} AND approve=1";

		include_once (DLEPlugins::Check(ENGINE_DIR . '/modules/show.short.php'));

		if (!$config['allow_quick_wysiwyg']) $allow_comments_ajax = false;

		if ($config['files_allow']) if (strpos ( $tpl->result['content'], "[attachment=" ) !== false) {
			$tpl->result['content'] = show_attach ( $tpl->result['content'], $attachments );
		}
		if ( $news_found ) create_cache ( "continue_".$member_name, json_encode( array('content' => $tpl->result['content'], 'navigation' => $tpl->result['navigation'], 'description' => $page_description, 'last-modified' => $_DOCUMENT_DATE ) , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), false, true );
	}
}
?>