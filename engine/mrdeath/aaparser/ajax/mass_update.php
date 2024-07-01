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
	
	    if ( $aaparser_config['main_fields']['xf_shikimori_id'] && $aaparser_config['main_fields']['xf_mdl_id'] ) $where = "xfields LIKE '%|".$aaparser_config['main_fields']['xf_shikimori_id']."|%' OR xfields LIKE '%|".$aaparser_config['main_fields']['xf_mdl_id']."|%'";
	    elseif ( $aaparser_config['main_fields']['xf_shikimori_id'] ) $where = "xfields LIKE '%|".$aaparser_config['main_fields']['xf_shikimori_id']."|%'";
	    else $where = "xfields LIKE '%|".$aaparser_config['main_fields']['xf_mdl_id']."|%'";
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
	            
	$db->query("UPDATE " . PREFIX . "_post SET xfields='{$new_xfields}' WHERE id='{$news_id}'");
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

}

?>