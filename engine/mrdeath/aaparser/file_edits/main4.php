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

if ($is_logged) {
	$subs_count = $db->super_query("SELECT count(*) as count FROM ".PREFIX."_subscribe_info where user_id=".$member_id['user_id']);
	$push_subscribe = json_decode($member_id['push_subscribe'], true);
	$cache_file = ENGINE_DIR . "/cache/member_{$member_id['user_id']}.tmp";
	$tpl->result['main'] = str_replace( '{subscribe-notification-count}', $subs_count['count'], $tpl->result['main'] );
	if ($push_subscribe !== null) $subscribe_count = count($push_subscribe);
	else $subscribe_count = 0;
  
  	$tpl->result['main'] = str_replace('{subscribe-total}', $subscribe_count, $tpl->result['main']);
	$tpl->result['main'] = str_replace('[is_logged]', '', $tpl->result['main'] );
	$tpl->result['main'] = str_replace('[/is_logged]', '', $tpl->result['main'] );
	
	$checkernewsid = $db->super_query("select id FROM ".PREFIX."_post where id=".$newsid);
  	
	if ($checkernewsid && $newsid>0) {
      	
      	if (file_exists($cache_file) && (filemtime($cache_file) + 60 > time())) {
          	$is_viewed = unserialize(dle_cache( "member_".$member_id['user_id'], false ));
        } else {
           	$is_viewed = $db->super_query("select count(*) as count FROM ".PREFIX."_subscribe_info where user_id={$member_id['user_id']} and post_id=".$newsid);
         	create_cache("member_".$member_id['user_id'], serialize($is_viewed), false );
        }
      	if ($is_viewed['count'] > 0) {
          	$db->query("DELETE FROM ".PREFIX."_subscribe_info where user_id={$member_id['user_id']} and post_id=".$newsid);
        }
	}
} else {
	$tpl->result['main'] = preg_replace( "'\\[is_logged\\](.*?)\\[/is_logged\\]'is", "", $tpl->result['main'] );
}
