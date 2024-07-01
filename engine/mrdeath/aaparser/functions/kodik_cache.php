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

function kodik_cache($prefix, $cache_id = false, $cache_folder = "trash") {
	global $config, $is_logged, $member_id, $dlefastcache;
  
  	if( !is_dir( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" ) ) {
        @mkdir( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/", 0777 );
        @chmod( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/", 0777 );
    }
	
	if( ! $cache_id ) $key = $prefix;
	else {
		$cache_id = md5( $cache_id );
		$key = $prefix . "_" . $cache_id;
	}
	
	// Кэширование куда либо помимо файлового кэша
	// if( $config['cache_type'] ) {
		// if( $dlefastcache->connection > 0 ) {
			// return $dlefastcache->get($key);
		// }
	// }

	$buffer = @file_get_contents( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" . $key . ".tmp" );
	return $buffer;
}

function kodik_create_cache($prefix, $cache_text, $cache_id = false, $cache_folder = "trash") {
	global $config, $is_logged, $member_id, $dlefastcache;
  
  	if( !is_dir( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" ) ) {
        @mkdir( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/", 0777 );
        @chmod( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/", 0777 );
    }
	
	if( ! $cache_id ) $key = $prefix;
	else {
		$cache_id = md5( $cache_id );
		$key = $prefix . "_" . $cache_id;
	}
	
	if($cache_text === false) $cache_text = '';

	// Кэширование куда либо помимо файлового кэша
	// if( $config['cache_type'] ) {
		// if( $dlefastcache->connection > 0 ) {
			// $dlefastcache->set( $key, $cache_text );
			// return true;
		// }
	// }

	file_put_contents (ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" . $key . ".tmp", $cache_text, LOCK_EX);
	@chmod( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" . $key . ".tmp", 0777 );
	return true;
}

function kodik_clear_cache($cache_areas = false, $cache_folder = "trash") {
	global $dlefastcache, $config;
  
  	if( !is_dir( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" ) ) {
        @mkdir( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/", 0777 );
        @chmod( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/", 0777 );
    }

	//if( $config['cache_type'] ) {
		//if( $dlefastcache->connection > 0 ) {
			//$dlefastcache->clear( $cache_areas );
			//return true;
		//}
	//}

	if ( $cache_areas ) {
		if(!is_array($cache_areas)) $cache_areas = array($cache_areas);
	}
		
	$fdir = opendir( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder );
		
	while ( $file = readdir( $fdir ) ) {
		if( $file != '.htaccess' AND !is_dir(ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" . $file) ) {
			if( $cache_areas ) foreach($cache_areas as $cache_area) if( stripos( $file, $cache_area ) !== false ) @unlink( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" . $file );
			else @unlink( ENGINE_DIR . "/mrdeath/aaparser/cache/" . $cache_folder . "/" . $file );
		}
	}
	return true;
}