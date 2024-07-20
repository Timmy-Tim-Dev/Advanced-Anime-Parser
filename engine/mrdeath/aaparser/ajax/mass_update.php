<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

require_once ENGINE_DIR.'/mrdeath/aaparser/functions/module.php';
require_once ENGINE_DIR.'/mrdeath/aaparser/functions/public.php';

$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';
$kodik_api_domain = isset($aaparser_config['settings']['kodik_api_domain']) ? $aaparser_config['settings']['kodik_api_domain'] : 'https://kodikapi.com/';

$action = isset($_GET['action']) ? $_GET['action'] : null;

$is_logged = false;

@header('Content-type: text/html; charset=' . $config['charset']);

date_default_timezone_set($config['date_adjust']);
$_TIME = time();

if (!$is_logged) $member_id['user_group'] = 5;
if ($is_logged && $member_id['banned'] == 'yes') die('User banned');

$user_group = get_vars('usergroup');

if ( $action == "update_news_get" ) {
		
		if ( !$aaparser_config['main_fields']['xf_shikimori_id'] && !$aaparser_config['main_fields']['xf_mdl_id'] ) die(json_encode(array( 'status' => 'fail' )));
	
	    if ( $aaparser_config['main_fields']['xf_shikimori_id'] && $aaparser_config['main_fields']['xf_mdl_id'] ) $where = "xfields LIKE '%".$aaparser_config['main_fields']['xf_shikimori_id']."|%' OR xfields LIKE '%".$aaparser_config['main_fields']['xf_mdl_id']."|%'";
	    elseif ( $aaparser_config['main_fields']['xf_shikimori_id'] ) $where = "xfields LIKE '%".$aaparser_config['main_fields']['xf_shikimori_id']."|%'";
	    else $where = "xfields LIKE '%".$aaparser_config['main_fields']['xf_mdl_id']."|%'";
	    $news = $db->query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
		
		$news_count = $news->num_rows;
		if($news_count == 0) return;
		$result_connect = array();
		$count = 0;
		
		while($temp_news = $db->get_row($news)) {
			$id = intval($temp_news['id']);
			$xfields = xfieldsdataload($temp_news['xfields']);
			if ( $xfields[$aaparser_config['main_fields']['xf_shikimori_id']] ) $shikimori_id = $xfields[$aaparser_config['main_fields']['xf_shikimori_id']];
			else $shikimori_id = 0;
			if ( $xfields[$aaparser_config['main_fields']['xf_mdl_id']] ) $mdl_id = $xfields[$aaparser_config['main_fields']['xf_mdl_id']];
			else $mdl_id = 0;

			if (!$shikimori_id && !$mdl_id) continue;			
			
			$result_connect[] = array(
				'id' => $id,
				'shikimori_id' => $shikimori_id,
				'mdl_id' => $mdl_id,
			);
			
			$count++;
		}
		if ($count > 0) echo json_encode($result_connect);
		else die(json_encode(array( 'status' => 'fail' )));
} elseif ( $action == "update_news" ) {
	
	if ( !isset($aaparser_config['main_fields']['xf_shikimori_id']) && !isset($aaparser_config['main_fields']['xf_mdl_id']) ) die(json_encode(array( 'status' => 'fail' )));
	    
	$news_id = $_GET['newsid'];
    $news_id = is_numeric($news_id) ? intval($news_id) : false;
    
    if(!$news_id) return;
    
	if ( $_GET['shikiid'] && $_GET['shikiid'] != 0 && $_GET['shikiid'] != '0' ) $shiki_id = $_GET['shikiid'];
	else $shiki_id = 0;
	if ( $_GET['mdlid'] && $_GET['mdlid'] != 0 && $_GET['mdlid'] != '0' ) $mdl_id = $_GET['mdlid'];
	else $mdl_id = 0;
	
	if( !$shiki_id && !$mdl_id ) return;
	
	$news_row = $db->super_query( "SELECT id, xfields, title FROM " . PREFIX . "_post WHERE id='{$news_id}'" );
	if ( !$news_row['xfields'] ) return;
	
	$parse_action = 'parse';
    if ( $aaparser_config['settings']['working_mode'] == 1 ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	else {
        if ( $shiki_id ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/shikimori.php'));
	    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	}
	
	if ( !$xfields_data ) return;
	if ( ( $shiki_id && !isset($xfields_data['shikimori_id']) ) || ( $shiki_id && isset($xfields_data['shikimori_id']) && $xfields_data['shikimori_id'] != $shiki_id ) ) return;
	if ( ( $mdl_id && !isset($xfields_data['mydramalist_id']) ) || ( $mdl_id && isset($xfields_data['mydramalist_id']) && $xfields_data['mydramalist_id'] != $mdl_id ) ) return;
	
	$xfields_data['worldart_country'] =	$xfields_data['worldart_tags'] = $xfields_data['worldart_plot'] = $xfields_data['worldart_rating'] = $xfields_data['worldart_votes'] = '';
	
	$black_list_xfields = ['image', 'kadr_1', 'kadr_2', 'kadr_3', 'kadr_4', 'kadr_5'];
	
	foreach ( $xfields_data as $tag_name => $tag_list ) {
	    if ( !$xfields_data[$tag_name] || in_array($tag_name, $black_list_xfields) ) $xfields_data[$tag_name] = '';
	}
	
	foreach ( $data_list as $tag_list ) {
	    if ( !$xfields_data[$tag_list] ) $xfields_data[$tag_list] = '';
	}
	            
	$xfields_list = array();
    
    foreach($aaparser_config['xfields'] as $named => $zna4enie) {
        $xfields_list[$named] = check_if($zna4enie, $xfields_data);
    }
    $delete_xf = ['title', 'short_story', 'full_story', 'alt_name', 'tags', 'meta_title', 'meta_description', 'meta_keywords', 'catalog'];
    foreach ( $delete_xf as $check_value ) {
        if( array_key_exists($check_value, $xfields_list) ) unset($xfields_list[$check_value]);
    }
                
    if ( $its_camrip === true && $aaparser_config['fields']['xf_camrip'] ) $xfields_list[$aaparser_config['fields']['xf_camrip']] = 1;
    if ( $its_lgbt === true && $aaparser_config['fields']['xf_lgbt'] ) $xfields_list[$aaparser_config['fields']['xf_lgbt']] = 1;
	if ( $shiki_id && $aaparser_config['main_fields']['xf_shikimori_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_shikimori_id']] = $shiki_id;
	if ( $mdl_id && $aaparser_config['main_fields']['xf_mdl_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_mdl_id']] = $mdl_id;
	if ( isset($next_episode_date) && $next_episode_date ) $xfields_list[$aaparser_config['settings']['next_episode_date_new']] = $next_episode_date;
                
    $old_xfields = xfieldsdataload($news_row['xfields']);
                
    foreach ( $xfields_list as $check_xf_name => $check_xf_data ) {
        if ( isset($aaparser_config['updates']['xf_translation_last_names']) && $check_xf_name == $aaparser_config['updates']['xf_translation_last_names'] ) continue;
        if ( $xfields_list[$check_xf_name] ) $old_xfields[$check_xf_name] = $xfields_list[$check_xf_name];
    }
    
    foreach ( $old_xfields as $check_xf_named => $check_xf_dated ) {
        if ( mb_strpos( $check_xf_dated, '{' ) !== false ) unset($old_xfields[$check_xf_named]);
    }
    
    if ( isset($next_episode_date) ) $old_xfields[$aaparser_config['settings']['next_episode_date_new']] = $next_episode_date;
                
    $new_xfields = xfieldsdatasaved($old_xfields);
	$new_xfields = $db->safesql( $new_xfields );
	            
	if (isset($aaparser_config['settings']['weak_mysql']) && $aaparser_config['settings']['weak_mysql'] == 1) {
		if (!function_exists('splitStrings')) {
			function splitStrings($string, $chunkSize) {
				$parts = str_split($string, $chunkSize);
				return $parts;
			}
		}
		$weak_mysql_count = $aaparser_config['settings']['weak_mysql_count'] ? $aaparser_config['settings']['weak_mysql_count'] : 1024;

		$xfields_parts = splitStrings($new_xfields, $weak_mysql_count);   
		foreach ($xfields_parts as $index => $part) {
			if ($index == 0) $db->query("UPDATE " . PREFIX . "_post SET `xfields` = '{$part}' WHERE id = '{$news_id}'");
			else $db->query("UPDATE " . PREFIX . "_post SET `xfields` = CONCAT(`xfields`, '{$part}') WHERE id = '{$news_id}'");
		 
			if ($db->error) {
				echo "Ошибка при отправке запроса: " . $db->error ."<br/>Попробуйте выключить слабый режим MYSQL или разбитие уменьшить";
				break;
			}
		}	
	} else $db->query("UPDATE " . PREFIX . "_post SET xfields='{$new_xfields}' WHERE id='{$news_id}'");
	
	$db->query("DELETE FROM " . PREFIX . "_xfsearch WHERE news_id='{$news_id}'");
	            
	$newpostedxfields = xfieldsdataload($new_xfields);
    $xf_search_words = array();
    
    foreach (xfieldsload() as $name => $value) {
        if ( $value[6] AND !empty($newpostedxfields[$value[0]]) ) {
			$temp_array = explode( ",", $newpostedxfields[$value[0]] );
			foreach ($temp_array as $value2) {
				$value2 = trim($value2);
				if($value2) {
					if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $xf_search_words[] = array( $db->safesql($value[0]), $db->safesql($value2), ($value[31]) ? $db->safesql(totranslit($value2, true, false)) : '' );
					else $xf_search_words[] = array( $db->safesql($value[0]), $db->safesql($value2) );
				}
			}
		}
    }
	if ( count($xf_search_words) AND $publish == 1 ) {
		
		$temp_array = array();
		
		foreach ( $xf_search_words as $value ) {
			if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $temp_array[] = "('" . $news_id . "', '" . $value[0] . "', '" . $value[1] . "', '" . $value[2] . "')";
			else $temp_array[] = "('" . $news_id . "', '" . $value[0] . "', '" . $value[1] . "')";
		}
		
		$xf_search_words = implode( ", ", $temp_array );
		if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue, tagvalue_translit) VALUES " . $xf_search_words );
		else $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
	}
	
	clear_cache( array('news_', 'full_'.$news_id) );
	
	$result_work = array(
		'news_id' => $news_id,
		'status' => 'данные в доп. поля проставлены успешно.'
	);
	$result = json_encode($result_work);
	echo $result;

} elseif ( $action == "update_news_metas" ) {
	
	if ( !isset($aaparser_config['main_fields']['xf_shikimori_id']) && !isset($aaparser_config['main_fields']['xf_mdl_id']) ) die(json_encode(array( 'status' => 'fail' )));
	    
	$news_id = $_GET['newsid'];
    $news_id = is_numeric($news_id) ? intval($news_id) : false;
    
    if(!$news_id) return;
    
	if ( $_GET['shikiid'] && $_GET['shikiid'] != 0 && $_GET['shikiid'] != '0' ) $shiki_id = "&shikimori_id=".$_GET['shikiid'];
	else $shiki_id = 0;
	if ( $_GET['mdlid'] && $_GET['mdlid'] != 0 && $_GET['mdlid'] != '0' ) $mdl_id = "&mdl_id=".$_GET['mdlid'];
	else $mdl_id = 0;
	$status_type = array( 'anons' => 'Анонс', 'ongoing' => 'Онгоинг', 'released' => 'Завершён' );
	$translation_type = array( 'subtitles' => 'Субтитры', 'voice' => 'Озвучка' );
	if( !$shiki_id && !$mdl_id ) return;
	
	$news_row = $db->super_query( "SELECT id, xfields, title FROM " . PREFIX . "_post WHERE id='{$news_id}'" );
	if ( !$news_row['xfields'] ) return;
	$xfields_post = xfieldsdataload( $news_row['xfields'] );
	if ($shiki_id && !$mdl_id) $kodik_updates_api = request($kodik_api_domain."search?token=". $kodik_apikey . $shiki_id ."&with_episodes=true&with_material_data=true");
	if ($mdl_id && !$shiki_id) $kodik_updates_api = request($kodik_api_domain."search?token=". $kodik_apikey . $mdl_id ."&with_episodes=true&with_material_data=true");
	$kodik_updates = array_reverse($kodik_updates_api['results']);
	if (empty($kodik_updates)) {
		$result_work = array(
			'news_id' => $news_id,
			'status' => 'Не найден по базе.'
		);
		$result = json_encode($result_work);
		echo $result;
		return;
	}
	if ($kodik_updates[0]['translation']['title']) $last_translation = trim($kodik_updates[0]['translation']['title']);
	if ( $xfields_post[$aaparser_config['main_fields']['xf_translation']] ) {
		$old_translations = explode(', ', $xfields_post[$aaparser_config['main_fields']['xf_translation']]);
		if ( !in_array($last_translation, $old_translations) ) $old_translations[] = $last_translation;
		$translation = implode(', ', $old_translations);
	} else $translation = '';

	$update_fields['title'] = $kodik_updates[0]['title_orig'] ? $kodik_updates[0]['title_orig'] : '';
	$update_fields['title_ru'] = $kodik_updates[0]['title'] ? $kodik_updates[0]['title'] : '';
	if ( $kodik_updates[0]['last_episode'] ) {
		$update_fields['episode'] = $kodik_updates[0]['last_episode'];
		$update_fields['episode_1'] = generate_numbers($kodik_updates[0]['last_episode'], 1);
		$update_fields['episode_2'] = generate_numbers($kodik_updates[0]['last_episode'], 2);
		$update_fields['episode_3'] = generate_numbers($kodik_updates[0]['last_episode'], 3);
		$update_fields['episode_4'] = generate_numbers($kodik_updates[0]['last_episode'], 4);
		$update_fields['episode_5'] = generate_numbers($kodik_updates[0]['last_episode'], 5);
		$update_fields['episode_6'] = generate_numbers($kodik_updates[0]['last_episode'], 6);
		$update_fields['episode_7'] = generate_numbers($kodik_updates[0]['last_episode'], 7);
		$update_fields['episode_8'] = generate_numbers($kodik_updates[0]['last_episode'], 8);
	} else $update_fields['episode'] = $update_fields['episode_1'] = $update_fields['episode_2'] = $update_fields['episode_3'] = $update_fields['episode_4'] = $update_fields['episode_5'] = $update_fields['episode_6'] = $update_fields['episode_7'] = $update_fields['episode_8'] = '';
	if ( $kodik_updates[0]['last_season'] ) {
		$update_fields['season'] = $kodik_updates[0]['last_season'];
		$update_fields['season_1'] = generate_numbers($kodik_updates[0]['last_season'], 1);
		$update_fields['season_2'] = generate_numbers($kodik_updates[0]['last_season'], 2);
		$update_fields['season_3'] = generate_numbers($kodik_updates[0]['last_season'], 3);
		$update_fields['season_4'] = generate_numbers($kodik_updates[0]['last_season'], 4);
		$update_fields['season_5'] = generate_numbers($kodik_updates[0]['last_season'], 5);
		$update_fields['season_6'] = generate_numbers($kodik_updates[0]['last_season'], 6);
		$update_fields['season_7'] = generate_numbers($kodik_updates[0]['last_season'], 7);
		$update_fields['season_8'] = generate_numbers($kodik_updates[0]['last_season'], 8);
	} else $update_fields['season'] = $update_fields['season_1'] = $update_fields['season_2'] = $update_fields['season_3'] = $update_fields['season_4'] = $update_fields['season_5'] = $update_fields['season_6'] = $update_fields['season_7'] = $update_fields['season_8'] = '';
	
	if ( $kodik_updates[0]['material_data']['all_status'] ) $update_fields['status'] = $kodik_updates[0]['material_data']['all_status'];
	else $update_fields['status'] = '';
	if ( $kodik_updates[0]['material_data']['all_status'] ) $update_fields['status_ru'] = $status_type[$kodik_updates[0]['material_data']['anime_status']];
	else $update_fields['status_ru'] = '';
	if ( $kodik_updates[0]['quality'] ) $update_fields['quality'] = $kodik_updates[0]['quality'];
	else $update_fields['quality'] = '';
	if ( $kodik_updates[0]['translation'] ) $update_fields['translation'] = $translation;
	else $update_fields['translation'] = '';
	if ( $kodik_updates[0]['translation']['type'] ) $update_fields['translation_type'] = $kodik_updates[0]['translation']['type'];
	else $update_fields['translation_type'] = '';
	if ( $kodik_updates[0]['translation']['type'] ) $update_fields['translation_type_ru'] = $translation_type[$kodik_updates[0]['translation']['type']];
	else $update_fields['translation_type_ru'] = '';

	if (isset($aaparser_config['updates']['metatitle'])) {
		$and_metatitle = $db->safesql( check_if($aaparser_config['updates']['metatitle'], $update_fields) );
		$set_metatitle = "metatitle='".$and_metatitle."'";
	} else $set_metatitle = '';
	
	if (isset($aaparser_config['updates']['metadescr'])) {
		$and_metadescr = $db->safesql( check_if($aaparser_config['updates']['metadescr'], $update_fields) );
		$set_metadescr = ", descr='".$and_metadescr."'";
	} else $set_metadescr = '';
	
	if (isset($aaparser_config['updates']['metakeywords'])) {
		$and_metakeywords = $db->safesql( check_if($aaparser_config['updates']['metakeywords'], $update_fields) );
		$set_metakeywords = ", keywords='".$and_metakeywords."'";
	} else $set_metakeywords = '';
	
	$db->query( "UPDATE " . PREFIX . "_post SET {$set_metatitle}{$set_metadescr}{$set_metakeywords} WHERE id='{$news_id}'" );
	
	clear_cache( array('news_', 'full_'.$news_id) );
	
	$result_work = array(
		'news_id' => $news_id,
		'status' => 'данные в метатеги проставлены успешно.'
	);
	$result = json_encode($result_work);
	echo $result;

} elseif ( $action == "update_news_img" ) {
	
	if ( !isset($aaparser_config['main_fields']['xf_shikimori_id']) && !isset($aaparser_config['main_fields']['xf_mdl_id']) ) die(json_encode(array( 'status' => 'fail' )));

	$news_id = $_GET['newsid'];
    $news_id = is_numeric($news_id) ? intval($news_id) : false;
    
    if(!$news_id) return;
    
	if ( $_GET['shikiid'] && $_GET['shikiid'] != 0 && $_GET['shikiid'] != '0' ) $shiki_id = "&shikimori_id=".$_GET['shikiid'];
	else $shiki_id = 0;
	if ( $_GET['mdlid'] && $_GET['mdlid'] != 0 && $_GET['mdlid'] != '0' ) $mdl_id = "&mdl_id=".$_GET['mdlid'];
	else $mdl_id = 0;
	if( !$shiki_id && !$mdl_id ) return;
	
	$news_row = $db->super_query( "SELECT id, xfields, title FROM " . PREFIX . "_post WHERE id='{$news_id}'" );
	if ( !$news_row['xfields'] ) return;
	$xfields_post = xfieldsdataload( $news_row['xfields'] );

	if ($shiki_id && !$mdl_id) $kodik_updates_api = request($kodik_api_domain."search?token=". $kodik_apikey . $shiki_id ."&with_episodes=true&with_material_data=true");
	if ($mdl_id && !$shiki_id) $kodik_updates_api = request($kodik_api_domain."search?token=". $kodik_apikey . $mdl_id ."&with_episodes=true&with_material_data=true");
	$kodik_updates = array_reverse($kodik_updates_api['results']);
	if (empty($kodik_updates)) {
		$result_work = array(
			'news_id' => $news_id,
			'status' => 'Не найден по базе. S-'. $shiki_id
		);
		$result = json_encode($result_work);
		echo $result;
		return;
	}

	$masserimage['image'] = $kodik_updates[0]['material_data']['poster_url'];
	if ( isset( $aaparser_config['settings']['parse_jikan'] ) && $shiki_id ) {
		$jikan_api = request('https://api.jikan.moe/v4/anime/'.$shiki_id);
		if ( isset( $jikan_api['data']['images']['jpg']['large_image_url'] ) && $jikan_api['data']['images']['jpg']['large_image_url'] ) 
			$masserimage['image'] = $jikan_api['data']['images']['jpg']['large_image_url'];
	}

	$member_id['user_group'] = 1;
	include_once(DLEPlugins::Check(ENGINE_DIR . '/classes/uploads/upload.class.php'));
	
	$update_fields['title'] = $kodik_updates[0]['title_orig'] ? $kodik_updates[0]['title_orig'] : '';
	$update_fields['title_ru'] = $kodik_updates[0]['title'] ? $kodik_updates[0]['title'] : '';
	
	if ( $update_fields['title_ru'] ) $poster_file = totranslit_it($update_fields['title_ru'], true, false);
	elseif ( $update_fields['title'] ) $poster_file = totranslit_it($update_fields['title'], true, false);
	$poster = setPoster($masserimage['image'], $poster_file, 'poster', $aaparser_config['images']['xf_poster'], $news_id);
	
	if ( isset($poster) && is_array($poster) ) {
		if ( $aaparser_config['images']['xf_poster'] ) $masserimage['image'] = $poster['xfvalue'];
		else $masserimage['image'] = $poster['link'];
		$xf_poster = $poster['xfvalue'];
		$poster_code = $poster['returnbox'];
	}

	if(isset($poster['error'])) {
		$result_work = array(
			'news_id' => $news_id,
			'status' => 'Ошибка связанная с постером.'
		);
		$result = json_encode($result_work);
		echo $result;
		return;
	}
	
	if ( $aaparser_config['grabbing']['author_name'] ) $author = $aaparser_config['grabbing']['author_name'];
	else {
		$avtr = $db->super_query(" SELECT name, user_id FROM " . PREFIX . "_users WHERE user_id=1 ");
		$author = $avtr['name'];
	}
	$author = $db->safesql($author);
	
	$images = array();
	if ($xf_poster) $images[] = $xf_poster;
	$images = implode('|||', $images);

	if (isset($xfields_post[$aaparser_config['images']['xf_poster']])) {
		$delpart = explode('|', $xfields_post[$aaparser_config['images']['xf_poster']]);
		$monthpart = explode('/', $delpart[0]);
		if (file_exists(ROOT_DIR . '/uploads/posts/'.$delpart[0])) {
			@unlink(ROOT_DIR . '/uploads/posts/'.$delpart[0]);
			@unlink(ROOT_DIR . '/uploads/posts/'.$monthpart[0].'/thumbs/'. $monthpart[1]);
		}
	}

	$delete_xf = ['title', 'short_story', 'full_story', 'alt_name', 'tags', 'meta_title', 'meta_description', 'meta_keywords', 'catalog'];
    foreach ( $delete_xf as $check_value ) {
        if( array_key_exists($check_value, $xfields_post) ) unset($xfields_post[$check_value]);
    }

	$xfields_post[$aaparser_config['images']['xf_poster']] = $xf_poster;
	$xfields_post = $db->safesql(xfieldsdatasaved($xfields_post));

	$db->query(" DELETE FROM " . PREFIX . "_images WHERE news_id = '{$news_id}'");
	$db->query(" INSERT INTO " . PREFIX . "_images (images, news_id, author, date) VALUES ('{$images}', '{$news_id}', '{$author}', '".time()."') ");
	
	if (isset($aaparser_config['settings']['weak_mysql']) && $aaparser_config['settings']['weak_mysql'] == 1) {
		if (!function_exists('splitStrings')) {
			function splitStrings($string, $chunkSize) {
				$parts = str_split($string, $chunkSize);
				return $parts;
			}
		}
		$weak_mysql_count = $aaparser_config['settings']['weak_mysql_count'] ? $aaparser_config['settings']['weak_mysql_count'] : 1024;

		$xfields_parts = splitStrings($xfields_post, $weak_mysql_count);   
		foreach ($xfields_parts as $index => $part) {
			if ($index == 0) $db->query("UPDATE " . PREFIX . "_post SET `xfields` = '{$part}' WHERE id = '{$news_id}'");
			else $db->query("UPDATE " . PREFIX . "_post SET `xfields` = CONCAT(`xfields`, '{$part}') WHERE id = '{$news_id}'");
		 
			if ($db->error) {
				echo "Ошибка при отправке запроса: " . $db->error ."<br/>Попробуйте выключить слабый режим MYSQL или разбитие уменьшить";
				break;
			}
		}	
	} else $db->query("UPDATE " . PREFIX . "_post SET xfields='{$xfields_post}' WHERE id='{$news_id}'");

	clear_cache( array('news_', 'full_'.$news_id) );
	
	$result_work = array(
		'news_id' => $news_id,
		'status' => 'картинка проставлена успешно.'
	);
	$result = json_encode($result_work);
	echo $result;

}

?>