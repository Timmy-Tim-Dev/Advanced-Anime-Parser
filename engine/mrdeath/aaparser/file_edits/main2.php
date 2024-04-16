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

if ( $dle_module == 'showfull' ) {
  	require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
  	if ( $aaparser_config['settings']['rooms_enable'] == 1 || $aaparser_config['player']['enable'] == 1 || $aaparser_config_push['push_notifications']['enable'] == 1 ) {
      	$js_array[] = "engine/mrdeath/aaparser/js/rooms.js";
      	$css_array[] = "engine/mrdeath/aaparser/css/rooms.css";
    }
  	if ( $aaparser_config['player']['enable'] == 1 && $aaparser_config['player']['auto_next'] == 1 && (!isset($aaparser_config['player']['method']) || $aaparser_config['player']['method'] == 0) ) {
      	$js_array[] = "engine/mrdeath/aaparser/js/kodik_playlist_autonext.js";
    }
  	elseif ( $aaparser_config['player']['enable'] == 1 && (!isset($aaparser_config['player']['method']) || $aaparser_config['player']['method'] == 0) ) {
      	$js_array[] = "engine/mrdeath/aaparser/js/kodik_playlist.js";
    }
  	elseif ( $aaparser_config['player']['enable'] == 1 && $aaparser_config['player']['auto_next'] == 1 ) {
      	$js_array[] = "engine/mrdeath/aaparser/js/kodik_playlist_new_autonext.js";
    }
  	elseif ( $aaparser_config['player']['enable'] == 1 ) {
      	$js_array[] = "engine/mrdeath/aaparser/js/kodik_playlist_new.js";
    }

  	if ( $aaparser_config['settings']['next_episode_date_new'] && $aaparser_config['settings']['timer_enable'] == 1 ) {
      	$js_array[] = "engine/mrdeath/aaparser/js/timer.js";
      	$css_array[] = "engine/mrdeath/aaparser/css/timer.css";
    }
  	if ( $aaparser_config['integration']['personas_on'] == 1 ) {
      	$js_array[] = "engine/mrdeath/aaparser/js/persons.js";
    }
}
elseif ( $aaparser_config_push['calendar_settings']['enable_schedule'] && $dle_module == 'schedule' ) {
  	$js_array[] = "engine/mrdeath/aaparser/js/schedule.js";
    $css_array[] = "engine/mrdeath/aaparser/css/schedule.css";
}
elseif ( $aaparser_config_push['calendar_settings']['enable_schedule'] && $aaparser_config_push['calendar_settings']['schedule_main'] && $dle_module == 'main' ) {
  	$js_array[] = "engine/mrdeath/aaparser/js/schedule.js";
    $css_array[] = "engine/mrdeath/aaparser/css/schedule.css";
}
elseif ( $dle_module == 'enter_room' ) {
  	$js_array[] = "engine/mrdeath/aaparser/js/rooms.js";
    $css_array[] = "engine/mrdeath/aaparser/css/rooms.css";
}