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

$i = 0;

$rooms_found = false;
$list_tags = array('id', 'leader', 'news_id', 'poster', 'title', 'public', 'episode_num', 'season_num', 'time', 'leader_avatar');
      
while ( $row = $db->get_row( $sql_result ) ) {
    
	$rooms_found = true;
	$i ++;
  
  	foreach ( $list_tags as $tag_name ) {
        if ( $row[$tag_name] ) {
        	$tpl->set( "{".$tag_name."}", $row[$tag_name] );
			$tpl->set( "[".$tag_name."]", "" );
			$tpl->set( "[/".$tag_name."]", "" );
			$tpl->set_block( "'\\[not-".$tag_name."\\](.*?)\\[/not-".$tag_name."\\]'si", "" );
		} else {
      		$tpl->set( "{".$tag_name."}", '' );
			$tpl->set( "[not-".$tag_name."]", "" );
			$tpl->set( "[/not-".$tag_name."]", "" );
			$tpl->set_block( "'\\[".$tag_name."\\](.*?)\\[/".$tag_name."\\]'si", "" );
		}
    }
    
	$tpl->set( '{room-link}', $config['http_home_url'] . "room/" . $row['url'] . "/" );
	$tpl->set_block( "'\\[no-rooms\\](.*?)\\[/no-rooms\\]'si", "" );
	
	if ( $withcount == 'yes' ) {
	    
	    $room_online_time = $_TIME-30;
  
  	    $visitors_count = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$row['url']}' AND time>'".$room_online_time."'");
  	    
  	    $tpl->set( '{count}', $visitors_count['count'] );
  	    
	} else $tpl->set( '{count}', '' );
	
	if( date( 'Ymd', $row['created'] ) == date( 'Ymd', $_TIME ) ) $tpl->set( '{created}', $lang['time_heute'] . langdate( ", H:i", $row['created'] ) );
	elseif( date( 'Ymd', $row['created'] ) == date( 'Ymd', ($_TIME - 86400) ) ) $tpl->set( '{created}', $lang['time_gestern'] . langdate( ", H:i", $row['created'] ) );
	else $tpl->set( '{created}', langdate( $config['timestamp_active'], $row['created'] ) );
	
	$tpl->copy_template = preg_replace_callback ( "#\{created=(.+?)\}#i", "formdate", $tpl->copy_template );
		
	$tpl->compile( 'content', true, false );
}

if( $rooms_found === false && preg_match( "'\\[no-rooms\\](.*?)\\[/no-rooms\\]'si", $tpl->copy_template, $match ) ) $tpl->result['content'] = $match[1];

$tpl->clear();
$db->free( $sql_result );

?>