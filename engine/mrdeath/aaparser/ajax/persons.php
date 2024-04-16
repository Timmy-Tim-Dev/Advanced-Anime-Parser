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

require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';

if ($aaparser_config['integration']['personas_on'] == 1) {

	require_once ENGINE_DIR.'/mrdeath/aaparser/functions/module.php';
	require_once ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php';
	
	if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
	else $shikimori_api_domain = 'https://shikimori.one/';
	$shiki_id = isset($_POST['sh_id']) ? $_POST['sh_id'] : '';
	
	$html_data = file_get_contents(ROOT_DIR.'/templates/'.$config['skin'].'/persons_info.tpl');
	if ($shiki_id == '') {
		die('Не был передан Shikimori id');
	}
	if ( isset($aaparser_config['integration']['personas_cache']) && $aaparser_config['integration']['personas_cache'] == 1 ) $shiki_request = kodik_cache('personas_'.$shiki_id, false, 'personas_characters');
	if (!$shiki_request || $shiki_request == "null" || $shiki_request == '{"main":{"items":[]},"sub":{"items":[]},"all":{"items":[]}}') {
		$shiki_request = request($shikimori_api_domain. 'api/animes/'.$shiki_id.'/roles');
		// print_r ($shiki_request);
		$main_b = $sub_b = $all_b = array(
			'items' => array ()
		);
		if ( !$shiki_request['message'] || !$shiki_request['code'] ) {
			foreach ( $shiki_request as $item ) {
				// Привязка по переменным
				$id_char = isset($item['character']['id']) ? $item['character']['id'] : '';
				$id_pers = isset($item['person']['id']) ? $item['person']['id'] : '';
				
				$nameeng_char = isset($item['character']['name']) ? $item['character']['name'] : '';
				$namerus_char = isset($item['character']['russian']) ? $item['character']['russian'] : '';
				
				$nameeng_pers = isset($item['person']['name']) ? $item['person']['name'] : '';
				$namerus_pers = isset($item['person']['russian']) ? $item['person']['russian'] : '';
				
				$noimage = $aaparser_config['integration']['default_image'];
				
				$image_orig_char = isset($item['character']['image']['original']) ? $shikimori_api_domain . $item['character']['image']['original'] : $noimage;
				$image_prev_char = isset($item['character']['image']['preview']) ? $shikimori_api_domain . $item['character']['image']['preview'] : $noimage;
				$image_x96_char = isset($item['character']['image']['x96']) ? $shikimori_api_domain . $item['character']['image']['x96'] : $noimage;
				$image_x48_char = isset($item['character']['image']['x48']) ? $shikimori_api_domain . $item['character']['image']['x48'] : $noimage;
				
				$image_orig_pers = isset($item['person']['image']['original']) ? $shikimori_api_domain . $item['person']['image']['original'] : $noimage;
				$image_prev_pers = isset($item['person']['image']['preview']) ? $shikimori_api_domain . $item['person']['image']['preview'] : $noimage;
				$image_x96_pers = isset($item['person']['image']['x96']) ? $shikimori_api_domain . $item['person']['image']['x96'] : $noimage;
				$image_x48_pers = isset($item['person']['image']['x48']) ? $shikimori_api_domain . $item['person']['image']['x48'] : $noimage;
				
				$url_char = isset($item['character']['url']) ? $shikimori_api_domain . $item['character']['url'] : '';
				$url_pers = isset($item['person']['url']) ? $shikimori_api_domain . $item['person']['url'] : '';
				
				if($item['roles'][0] == 'Main') {
					$roletype = "Главные герои";
					$finded_main = 1;
					$main_chars = array (
						'find' => $finded_main,
						'id' => $id_char,
						'role' => $roletype,
						'name_eng' => $nameeng_char,
						'name_rus' => $namerus_char,
						'url' => $url_char,
						'image_orig' => $image_orig_char,
						'image_prev' => $image_prev_char,
						'image_x96' => $image_x96_char,
						'image_x48' => $image_x48_char
					);
					array_push($main_b['items'], $main_chars);
					
				} elseif ($item['roles'][0] == 'Supporting') {
					$roletype = "Второстепенные герои";
					$finded_sub = 1;
					$sub_chars = array (
						'find' => $finded_sub,
						'id' => $id_char,
						'role' => $roletype,
						'name_eng' => $nameeng_char,
						'name_rus' => $namerus_char,
						'url' => $url_char,
						'image_orig' => $image_orig_char,
						'image_prev' => $image_prev_char,
						'image_x96' => $image_x96_char,
						'image_x48' => $image_x48_char
					);
					array_push($sub_b['items'], $sub_chars);
				} else {
					$roletype = implode(", ", $item['roles_russian']);
					$finded_other = 1;
					$all_pers = array (
						'find' => $finded_other,
						'id' => $id_pers,
						'role' => $roletype,
						'name_eng' => $nameeng_pers,
						'name_rus' => $namerus_pers,
						'url' => $url_pers,
						'image_orig' => $image_orig_pers,
						'image_prev' => $image_prev_pers,
						'image_x96' => $image_x96_pers,
						'image_x48' => $image_x48_pers
					);
					array_push($all_b['items'], $all_pers);
				}
			}
			$full_b = array (
				'main' => $main_b,
				'sub' => $sub_b,
				'all' => $all_b,
			);
			if ( isset($aaparser_config['integration']['personas_cache']) && $aaparser_config['integration']['personas_cache'] == 1 ) kodik_create_cache('personas_'.$shiki_id, json_encode($full_b, JSON_UNESCAPED_UNICODE), false, 'personas_characters');
		} else {
			echo "Состояние > " . $shiki_request['message'];
			echo "<br/> Код > " .$shiki_request['code'];
		}
	} else {
		$full_b = json_decode($shiki_request, true);
	}
	$main_b = $full_b['main'];
	$sub_b = $full_b['sub'];
	$all_b = $full_b['all'];
	
	$html_data = change_tags($html_data, 'main_characters', $main_b['items'][0]);
	$html_data = change_tags($html_data, 'sub_characters', $sub_b['items'][0]);
	$html_data = change_tags($html_data, 'all_personas', $all_b['items'][0]);
	
	foreach ($main_b['items'] as $items) {
		$main_data = change_tags_take($html_data, 'main_characters_item');
		$main_data = change_tags($main_data, 'main_characters_role', $items['role']);
		$main_data = change_tags($main_data, 'main_characters_name_eng', $items['name_eng']);
		$main_data = change_tags($main_data, 'main_characters_name_rus', $items['name_rus']);
		$main_data = change_tags($main_data, 'main_characters_url', $items['url']);
		$main_data = change_tags($main_data, 'main_characters_id', $items['id']);
		$main_data = change_tags($main_data, 'main_characters_image_orig', $items['image_orig']);
		$main_data = change_tags($main_data, 'main_characters_image_prev', $items['image_prev']);
		$main_data = change_tags($main_data, 'main_characters_image_x96', $items['image_x96']);
		$main_data = change_tags($main_data, 'main_characters_image_x48', $items['image_x48']);
		$main_data_arr .= $main_data;
	}
	foreach ($sub_b['items'] as $items) {
		$sub_data = change_tags_take($html_data, 'sub_characters_item');
		$sub_data = change_tags($sub_data, 'sub_characters_role', $items['role']);
		$sub_data = change_tags($sub_data, 'sub_characters_name_eng', $items['name_eng']);
		$sub_data = change_tags($sub_data, 'sub_characters_name_rus', $items['name_rus']);
		$sub_data = change_tags($sub_data, 'sub_characters_url', $items['url']);
		$sub_data = change_tags($sub_data, 'sub_characters_id', $items['id']);
		$sub_data = change_tags($sub_data, 'sub_characters_image_orig', $items['image_orig']);
		$sub_data = change_tags($sub_data, 'sub_characters_image_prev', $items['image_prev']);
		$sub_data = change_tags($sub_data, 'sub_characters_image_x96', $items['image_x96']);
		$sub_data = change_tags($sub_data, 'sub_characters_image_x48', $items['image_x48']);
		$sub_data_arr .= $sub_data;
	}
	foreach ($all_b['items'] as $items) {
		$all_data = change_tags_take($html_data, 'all_personas_item');
		$all_data = change_tags($all_data, 'all_personas_role', $items['role']);
		$all_data = change_tags($all_data, 'all_personas_name_eng', $items['name_eng']);
		$all_data = change_tags($all_data, 'all_personas_name_rus', $items['name_rus']);
		$all_data = change_tags($all_data, 'all_personas_url', $items['url']);
		$all_data = change_tags($all_data, 'all_personas_id', $items['id']);
		$all_data = change_tags($all_data, 'all_personas_image_orig', $items['image_orig']);
		$all_data = change_tags($all_data, 'all_personas_image_prev', $items['image_prev']);
		$all_data = change_tags($all_data, 'all_personas_image_x96', $items['image_x96']);
		$all_data = change_tags($all_data, 'all_personas_image_x48', $items['image_x48']);
		$all_data_arr .= $all_data;
	}
	
	$html_data = change_tags_set($html_data, 'main_characters_item', $main_data_arr);
	$html_data = change_tags_set($html_data, 'sub_characters_item', $sub_data_arr);
	$html_data = change_tags_set($html_data, 'all_personas_item', $all_data_arr);
	
	echo $html_data;
} else {
	die ('Функционал отключен! Пожалуйста включите если Вам это необходимо');
}

?>