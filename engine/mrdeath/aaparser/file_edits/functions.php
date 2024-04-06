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

function custom_rooms( $matches=array() ) {
	global $db, $is_logged, $member_id, $user_group, $_TIME, $lang, $PHP_SELF, $url_page, $user_query, $custom_news, $remove_canonical, $_DOCUMENT_DATE;

	if ( !count($matches) ) return "";

	$param_str = trim($matches[1]);

  	$where = array();

	$aviable = array("global");
	$sql_select = "SELECT r.id, r.url, r.leader, r.news_id, r.poster, r.title, r.public, r.episode_num, r.season_num, r.leader_last_login, r.created, rv.avatar as leader_avatar FROM " . PREFIX . "_rooms_list r LEFT JOIN " . PREFIX . "_rooms_visitors rv ON (r.url=rv.room_url AND r.leader=rv.login)";
	$ids_for_sort = false;

	if( preg_match( "#newsid=['\"](.+?)['\"]#i", $param_str, $match ) ) {
		$temp_array = array();
		$where_id = array();
		$match[1] = explode (',', trim($match[1]));

		foreach ($match[1] as $value) {

			if( count(explode('-', $value)) == 2 ) {
				$value = explode('-', $value);
				$where_id[] = "r.news_id >= '" . intval($value[0]) . "' AND r.news_id <= '".intval($value[1])."'";

			} else $temp_array[] = intval($value);

		}

		if ( count($temp_array) ) {

			$where_id[] = "r.news_id IN ('" . implode("','", $temp_array) . "')";
			$ids_for_sort = "FIND_IN_SET(r.news_id, '".implode(",", $temp_array)."') ";
		}

		if ( count($where_id) ) {
			$custom_id = "(".implode(' OR ', $where_id).")";
			$where[] = $custom_id;

		}

	}

	if( preg_match( "#newsidexclude=['\"](.+?)['\"]#i", $param_str, $match ) ) {

		$temp_array = array();
		$where_id = array();
		$match[1] = explode (',', trim($match[1]));

		foreach ($match[1] as $value) {

			if( count(explode('-', $value)) == 2 ) {
				$value = explode('-', $value);
				$where_id[] = "(r.news_id < '" . intval($value[0]) . "' OR r.news_id > '".intval($value[1])."')";

			} else $temp_array[] = intval($value);

		}

		if ( count($temp_array) ) {

			$where_id[] = "r.news_id NOT IN ('" . implode("','", $temp_array) . "')";
		}

		if ( count($where_id) ) {
			$custom_id = implode(' AND ', $where_id);
			$where[] = $custom_id;

		}
	}

	if( preg_match( "#leader=['\"](.+?)['\"]#i", $param_str, $match ) ) {

		$match[1] = explode (',', $match[1]);

		$temp_array = array();

		foreach ($match[1] as $value) {

			$value = $db->safesql(trim($value));
			$temp_array[] = "r.leader = '{$value}'";

		}

		$where[] = "(".implode(' OR ', $temp_array).")";


	}

	if( preg_match( "#leaderexclude=['\"](.+?)['\"]#i", $param_str, $match ) ) {

		$match[1] = explode (',', $match[1]);

		$temp_array = array();

		foreach ($match[1] as $value) {

			$value = $db->safesql(trim($value));
			$temp_array[] = "r.leader != '{$value}'";

		}

		$where[] = implode(' AND ', $temp_array);


	}

	if( preg_match( "#template=['\"](.+?)['\"]#i", $param_str, $match ) ) {
		$custom_template = trim($match[1]);
	} else $custom_template = "shortstory";

  	if( preg_match( "#withcount=['\"](.+?)['\"]#i", $param_str, $match ) ) {
		$withcount = trim($match[1]);
	} else $withcount = false;

	$custom_from = 0;
    $custom_all = 0;

	if( preg_match( "#limit=['\"](.+?)['\"]#i", $param_str, $match ) ) {
		$custom_limit = intval($match[1]);
	} else $custom_limit = 1;

  	if( preg_match( "#activetime=['\"](.+?)['\"]#i", $param_str, $match ) ) {
		$activetime = intval($match[1]);
	} else $activetime = 30;

	if( preg_match( "#hideprivate=['\"](.+?)['\"]#i", $param_str, $match ) ) {
		if ( $match[1] != "no" ) $where[] = "r.public = 0 AND r.leader_last_login>'".($_TIME-$activetime)."'";
      	else $where[] = "r.leader_last_login>'".($_TIME-$activetime)."'";
	}
	else $where[] = "r.public = 0 AND r.leader_last_login>'".($_TIME-$activetime)."'";

  	$news_msort = 'DESC';
  	$news_sort = 'id';

	$sql_count = "";

	$tpl = new dle_template();
	$tpl->dir = TEMPLATE_DIR;
	$tpl->is_custom = true;

	$tpl->load_template( $custom_template . '.tpl' );

	$sql_select .= " WHERE ".implode(' AND ', $where)." ORDER BY " . $news_sort . " " . $news_msort . " LIMIT " . $custom_from . "," . $custom_limit;

	$sql_result = $db->query( $sql_select );

	include (DLEPlugins::Check(ENGINE_DIR . '/modules/show.rooms.php'));

	$tpl->is_custom = false;

	return $tpl->result['content'];

}