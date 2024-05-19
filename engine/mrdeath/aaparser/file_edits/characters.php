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

ini_set("memory_limit","256M");
ini_set('max_execution_time',300);
$type = $_GET['type'];
$id = (int)$_GET['id'];
require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
require_once ENGINE_DIR.'/mrdeath/aaparser/functions/module.php';
require_once ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php';
if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
else $shikimori_api_domain = 'https://shikimori.one/';
$shikimori_url_domain = clean_url($shikimori_api_domain);
if ($type == "people") {
	$dle_module = 'people';
	if ( isset($aaparser_config_push['persons']['persons_page_cache']) && $aaparser_config_push['persons']['persons_page_cache'] == 1 ) {
		$json = kodik_cache('people_'.$id, false, 'personas_characters_page');
		$json = json_decode($json, true);
	} else $json = false;
	
	if ($json === false || $json == '') {
		$json = [];
		$json = request($shikimori_api_domain . '/api/people/' . $id);
		if ( isset($aaparser_config_push['persons']['persons_page_cache']) && $aaparser_config_push['persons']['persons_page_cache'] == 1 && $json != '') kodik_create_cache('people_'.$id, json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), false, 'personas_characters_page');
	}	

	$al_id = '';
	if (!is_null($json['roles'])) {
		$al_ids = [];
		foreach ($json['roles'] as $role) {
			if (isset($role['animes'])) $al_ids = array_merge($al_ids, array_column($role['animes'], 'id'));
		}
		$al_id = implode(', ', $al_ids);
	}
	$tpl->load_template( 'characters/people.tpl' );

	change_tags($tpl, $al_id, "anime-list");
	
	change_tags($tpl, $json['id'], "id");
	change_tags($tpl, $json['name'], "name");
	change_tags($tpl, $json['russian'], "russian");
	change_tags($tpl, $json['japanese'], "japanese");
	change_tags($tpl, $json['job_title'], "job_title");
	change_tags($tpl, $json['url'], 'url', "https://" . $shikimori_url_domain);
	change_tags($tpl, $json['website'], "website");
	// change_tags($tpl, $json['description_source'], "description");
	// Нету описания у авторов
	
	change_tags_img($tpl, $json['image']['original'], "image_orig", $aaparser_config_push['persons']['default_image']);
	change_tags_img($tpl, $json['image']['preview'], "image_prev", $aaparser_config_push['persons']['default_image']);
	change_tags_img($tpl, $json['image']['x96'], "image_x96", $aaparser_config_push['persons']['default_image']);
	change_tags_img($tpl, $json['image']['x48'], "image_x48", $aaparser_config_push['persons']['default_image']);
	
	if ($json['birth_on']['day']) {
		$tpl->set("[birth_on]", "");
		$tpl->set("[/birth_on]", "");
		$tpl->set("{birth_on}", str_pad($json['birth_on']['day'], 1, '0', STR_PAD_LEFT).".".str_pad($json['birth_on']['month'], 1, '0', STR_PAD_LEFT).".".$json['birth_on']['year']);
		$tpl->set_block("'\\[not_birth_on\\](.*?)\\[/not_birth_on\\]'si", "");
	} else {
		$tpl->set("[not_birth_on]", "");
		$tpl->set("[/not_birth_on]", "");
		$tpl->set_block("'\\[birth_on\\](.*?)\\[/birth_on\\]'si", "");
		$tpl->set("{birth_on}", '');
	}	
	
	$tpl->compile('content');
	
	// микроразметка
	$metajson['id'] = isset($json['id']) ? $json['id'] : '';
	$metajson['name'] = isset($json['name']) ? $json['name'] : '';
	$metajson['russian'] = isset($json['russian']) ? $json['russian'] : '';
	$metajson['altname'] = isset($json['altname']) ? $json['altname'] : '';
	$metajson['japanese'] = isset($json['japanese']) ? $json['japanese'] : '';
	$metajson['url'] = isset($json['url']) ? $json['url'] : '';
	$metajson['description'] = isset($json['description']) ? $json['description'] : '';
	$metajson['description_no_spoiler'] = isset($descr_without_spoil) ? $descr_without_spoil : '';
	$metajson['spoiler'] = isset($spoil) ? $spoil : '';
	$metajson['birth_on'] = isset($json['birth_on']) ? $json['birth_on'] : '';
	$metajson['job_title'] = isset($json['job_title']) ? $json['job_title'] : '';
	$metajson['website'] = isset($json['website']) ? $json['website'] : '';
	
	$canonical = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if ( isset($aaparser_config_push['persons']['metatitle']) && $aaparser_config_push['persons']['metatitle'] != '') {
		$metatags['title'] = check_if($aaparser_config_push['persons']['metatitle'], $metajson);
	}
	if ( isset($aaparser_config_push['persons']['metadescr']) && $aaparser_config_push['persons']['metadescr'] != '') {
		$metatags['description'] = check_if($aaparser_config_push['persons']['metadescr'], $metajson);
	}
	if ( isset($aaparser_config_push['persons']['metakeyw']) && $aaparser_config_push['persons']['metakeyw'] != '') {
		$metatags['keywords'] = check_if($aaparser_config_push['persons']['metakeyw'], $metajson);
	}
}
if ($type == "characters") {
	$dle_module = 'characters';
		if ( isset($aaparser_config_push['persons']['persons_page_cache']) && $aaparser_config_push['persons']['persons_page_cache'] == 1 ) {
			$json = kodik_cache('character_'.$id, false, 'personas_characters_page');
			$json = json_decode($json, true);
		} else $json = false;
	
		if ($json === false || $json == '') {
			$json = [];
			$json = request($shikimori_api_domain . '/api/characters/' . $id);
			if ( isset($aaparser_config_push['persons']['persons_page_cache']) && $aaparser_config_push['persons']['persons_page_cache'] == 1 && $json != '') kodik_create_cache('character_'.$id, json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), false, 'personas_characters_page');
		}	
	
		$al_id = $ml_id = '';
		if (!is_null($json['animes'])) $al_id = implode(', ', array_column($json['animes'], 'id'));
		if (!is_null($json['mangas'])) $ml_id = implode(', ', array_column($json['mangas'], 'id'));
		$json['description'] = preg_replace('/\[\/?character(?:=[^\]]+)?\]/', '', $json['description']);
		if (preg_match('/\[spoiler(?:=[^\]]+)?\](.*?)\[\/spoiler\]/s', $json['description'], $matches)) {
			$spoil = $matches[1];
		}
		$descr_without_spoil = preg_replace('/\[spoiler(?:=[^\]]+)?\](.*?)\[\/spoiler\]/s', '', $json['description']);
		$json['description'] = preg_replace('/\[\/?spoiler(?:=[^\]]+)?\]/', '', $json['description']);
		
		$tpl->load_template( 'characters/characters.tpl' );
		
		change_tags($tpl, $al_id, "anime-list");	
		change_tags($tpl, $ml_id, "manga-list");
		
		change_tags($tpl, $json['id'], "id");	
		change_tags($tpl, $json['name'], "name");		
		change_tags($tpl, $json['russian'], "russian");				
		change_tags($tpl, $json['altname'], "altname");				
		change_tags($tpl, $json['japanese'], "japanese");				
		change_tags($tpl, $json['url'], "url", "https://" . $shikimori_url_domain);				
		change_tags($tpl, $json['description'], "description");				
		change_tags($tpl, $descr_without_spoil, "description_no_spoiler");				
		change_tags($tpl, $spoil, "spoiler");				
		
		change_tags_img($tpl, $json['image']['original'], "image_orig", $aaparser_config_push['persons']['default_image']);
		change_tags_img($tpl, $json['image']['preview'], "image_prev", $aaparser_config_push['persons']['default_image']);
		change_tags_img($tpl, $json['image']['x96'], "image_x96", $aaparser_config_push['persons']['default_image']);
		change_tags_img($tpl, $json['image']['x48'], "image_x48", $aaparser_config_push['persons']['default_image']);
		
		$tpl->compile('content');
		
		// микроразметка
		$metajson['id'] = isset($json['id']) ? $json['id'] : '';
		$metajson['name'] = isset($json['name']) ? $json['name'] : '';
		$metajson['russian'] = isset($json['russian']) ? $json['russian'] : '';
		$metajson['altname'] = isset($json['altname']) ? $json['altname'] : '';
		$metajson['japanese'] = isset($json['japanese']) ? $json['japanese'] : '';
		$metajson['url'] = isset($json['url']) ? $json['url'] : '';
		$metajson['description'] = isset($json['description']) ? $json['description'] : '';
		$metajson['description_no_spoiler'] = isset($descr_without_spoil) ? $descr_without_spoil : '';
		$metajson['spoiler'] = isset($spoil) ? $spoil : '';
		$metajson['birth_on'] = isset($json['birth_on']) ? $json['birth_on'] : '';
		$metajson['job_title'] = isset($json['job_title']) ? $json['job_title'] : '';
		$metajson['website'] = isset($json['website']) ? $json['website'] : '';
		
		$canonical = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if ( isset($aaparser_config_push['persons']['metatitle']) && $aaparser_config_push['persons']['metatitle'] != '') {
			$metatags['title'] = check_if($aaparser_config_push['persons']['metatitle'], $metajson);
		}
		if ( isset($aaparser_config_push['persons']['metadescr']) && $aaparser_config_push['persons']['metadescr'] != '') {
			$metatags['description'] = check_if($aaparser_config_push['persons']['metadescr'], $metajson);
		}
		if ( isset($aaparser_config_push['persons']['metakeyw']) && $aaparser_config_push['persons']['metakeyw'] != '') {
			$metatags['keywords'] = check_if($aaparser_config_push['persons']['metakeyw'], $metajson);
		}
}
	
?>