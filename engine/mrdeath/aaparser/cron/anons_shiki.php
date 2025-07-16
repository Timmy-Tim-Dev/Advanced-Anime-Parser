<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
} 
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Начинаем добавление анонсируемого материала", round(microtime(true) - $time_update_start,4));
}
if ($aaparser_config['settings_anons']['anons_on'] == "") die("Модуль отключен в настройках");
if ($aaparser_config['settings_anons']['anons_kind'] != "") $kinder = ', kind: "'. $aaparser_config['settings_anons']['anons_kind'] .'"';
if ($aaparser_config['settings_anons']['anons_film_sort_by'] != "") $ordering = ', order: '.$aaparser_config['settings_anons']['anons_film_sort_by'];
$exclude_ids = array();
$res_shiki = $db->query("SELECT * FROM ".PREFIX."_shikimori_posts ");
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Получение данных с бд " . PREFIX . "_shikimori_posts", round(microtime(true) - $time_update_start,4));
}
while ( $shiki_row = $db->get_row ($res_shiki) ) {
	$exclude_ids[] = $shiki_row['shiki_id'];
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Получение каждой записи данных с бд " . PREFIX . "_shikimori_posts", round(microtime(true) - $time_update_start,4));
	}
}
$exclude_ids = implode(",",$exclude_ids);
if ($exclude_ids != "") $exclude_ids = ', excludeIds: "'.$exclude_ids.'"';

if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) {
    $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
    $shikimori_image_domain = 'https://'.clean_url($shikimori_api_domain);
} else $shikimori_api_domain = $shikimori_image_domain = 'https://shikimori.me/';


$postfields = ['query' => '{ animes(status: "anons", limit: 50, rating: "!rx" '.$exclude_ids . $kinder . $ordering.') { id }}'];
$shikimori_anons = request('https://shikimori.one/api/graphql', 1, $postfields);
$shikimori_anons = $shikimori_anons['data']['animes'];

if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Получение данных с API", round(microtime(true) - $time_update_start,4));
}
if ( $shikimori_anons ) {
	foreach ( $shikimori_anons as $result ) {
		if ( isset($result['id']) && $result['id'] ) $id_shiki = $result['id'];
		else continue;
		
		$where = "xfields REGEXP '(^|\\\\|)" . $aaparser_config['main_fields']['xf_shikimori_id'] ."\\\\|".$id_shiki. "(\\\\||$)'";
		
		$proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(anons_shiki.php) Получение данных с бд " . PREFIX . "_post", round(microtime(true) - $time_update_start,4));
		}
		if (isset($proverka['id']) && $proverka['id']) continue;
		$checked_id[] = $id_shiki;
		unset($id_shiki);
	}

	$shiki_id = $checked_id[0];
	$parse_action = 'parse';
	$parse_type = 'grabbing';
	include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/shikimori.php'));
} else {
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Закончили добавление анонсируемого материала", round(microtime(true) - $time_update_start,4));
		echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
	}
	die("Все материалы анонсов спарсились!");
}

//Работа с картинками

$id_news = 0;

$_REQUEST['module'] = 'aaparser';
include_once(DLEPlugins::Check(ENGINE_DIR . '/classes/uploads/upload.class.php'));
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Внедрение uploads.class.php", round(microtime(true) - $time_update_start,4));
}
if ( $mode != 'editnews' && $xfields_data['image'] && $aaparser_config['images']['poster'] == 1 ) $need_poster = true;
elseif ( $mode == 'editnews' && $xfields_data['image'] && $aaparser_config['images']['poster_edit'] == 1 ) $need_poster = true;
else $need_poster = false;

if ( $need_poster === true ) {
	if ( $xfields_data['shikimori_russian'] ) $poster_file = totranslit_it($xfields_data['shikimori_russian'], true, false);
	elseif ( $xfields_data['shikimori_name'] ) $poster_file = totranslit_it($xfields_data['shikimori_name'], true, false);
	elseif ( $xfields_list['kodik_title'] ) $poster_file = totranslit_it($xfields_list['kodik_title'], true, false);
	else $poster_file = totranslit_it($xfields_list['kodik_title_orig'], true, false);
	$poster = setPoster($xfields_data['image'], $poster_file, 'poster', $aaparser_config['images']['xf_poster'], $id_news);
	if ( isset($poster) && is_array($poster) ) {
		if ( $aaparser_config['images']['xf_poster'] ) $xfields_data['image'] = $poster['xfvalue'];
		else $xfields_data['image'] = $poster['link'];
		$xf_poster = $poster['xfvalue'];
		$poster_code = $poster['returnbox'];
		$poster_link = $poster['link'];
	}
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Загрузка постеров", round(microtime(true) - $time_update_start,4));
	}
}

if ( $mode != 'editnews' && $aaparser_config['images']['screens'] == 1  ) $need_screens = true;
elseif ( $mode == 'editnews' && $aaparser_config['images']['screens_edit'] == 1 ) $need_screens = true;
else $need_screens = false;

if ( $need_screens === true ) {
	
	if ( $xfields_data['kadr_1'] ) {
		
		if ( $xfields_data['shikimori_russian'] ) $screen_named = totranslit_it($xfields_data['shikimori_russian'], true, false);
		elseif ( $xfields_data['shikimori_name'] ) $screen_named = totranslit_it($xfields_data['shikimori_name'], true, false);
		elseif ( $xfields_data['kodik_title'] ) $screen_named = totranslit_it($xfields_data['kodik_title'], true, false);
		else $screen_named = totranslit_it($xfields_data['kodik_title_orig'], true, false);
		
		if ( 1 <= $aaparser_config['images']['screens_count'] ) {
			$screen_1_file = $screen_named.'_kadr_1';
			$screen_1 = setPoster($xfields_data['kadr_1'], $screen_1_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
			if ( isset($screen_1) && is_array($screen_1) ) {
				$xfields_data['kadr_1'] = $screen_1['link'];
				$xf_screen_1 = $screen_1['xfvalue'];
				$kadr_1 = $screen_1['xfvalue'];
			}
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(anons_shiki.php) Загрузка скриншота 1", round(microtime(true) - $time_update_start,4));
			}
		}
		
		if ( $xfields_data['kadr_2'] AND 2 <= $aaparser_config['images']['screens_count'] ) {
			$screen_2_file = $screen_named.'_kadr_2';
			$screen_2 = setPoster($xfields_data['kadr_2'], $screen_2_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
			if ( isset($screen_2) && is_array($screen_2) ) {
				$xfields_data['kadr_2'] = $screen_2['link'];
				$xf_screen_2 = ",".$screen_2['xfvalue'];
				$kadr_2 = $screen_2['xfvalue'];
			}
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(anons_shiki.php) Загрузка скриншота 2", round(microtime(true) - $time_update_start,4));
			}
		}
		
		if ( $xfields_data['kadr_3'] AND 3 <= $aaparser_config['images']['screens_count'] ) {
			$screen_3_file = $screen_named.'_kadr_3';
			$screen_3 = setPoster($xfields_data['kadr_3'], $screen_3_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
			if ( isset($screen_3) && is_array($screen_3) ) {
				$xfields_data['kadr_3'] = $screen_3['link'];
				$xf_screen_3 = ",".$screen_3['xfvalue'];
				$kadr_3 = $screen_3['xfvalue'];
			}
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(anons_shiki.php) Загрузка скриншота 3", round(microtime(true) - $time_update_start,4));
			}
		}
		
		if ( $xfields_data['kadr_4'] AND 4 <= $aaparser_config['images']['screens_count'] ) {
			$screen_4_file = $screen_named.'_kadr_4';
			$screen_4 = setPoster($xfields_data['kadr_4'], $screen_4_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
			if ( isset($screen_4) && is_array($screen_4) ) {
				$xfields_data['kadr_4'] = $screen_4['link'];
				$xf_screen_4 = ",".$screen_4['xfvalue'];
				$kadr_4 = $screen_4['xfvalue'];
			}
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(anons_shiki.php) Загрузка скриншота 4", round(microtime(true) - $time_update_start,4));
			}
		}
		
		if ( $xfields_data['kadr_5'] AND 5 <= $aaparser_config['images']['screens_count'] ) {
			$screen_5_file = $screen_named.'_kadr_5';
			$screen_5 = setPoster($xfields_data['kadr_5'], $screen_5_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
			if ( isset($screen_5) && is_array($screen_5) ) {
				$xfields_data['kadr_5'] = $screen_5['link'];
				$xf_screen_5 = ",".$screen_5['xfvalue'];
				$kadr_5 = $screen_5['xfvalue'];
			}
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(anons_shiki.php) Загрузка скриншота 5", round(microtime(true) - $time_update_start,4));
			}
		}
		
	}
}

//Буквенный каталог

if ( $xfields_data['shikimori_russian'] ) $xfields_data['catalog_rus'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['shikimori_russian'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
else $xfields_data['catalog_rus'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['kodik_title'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
if ( $xfields_data['shikimori_name'] ) $xfields_data['catalog_eng'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['shikimori_name'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
else $xfields_data['catalog_eng'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['kodik_title_en'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) {
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Создание буквенного каталога", round(microtime(true) - $time_update_start,4));
}
//Обработка категорий

$tags_array = array();

if ( $xfields_data['shikimori_status'] ) $tags_array[] = $shikimori['status'];
if ( $xfields_data['shikimori_status_ru'] ) $tags_array[] = $status_type[$shikimori['status']];

if ( $xfields_data['shikimori_id'] ) $tags_array[] = 'аниме';
elseif ( $xfields_data['mydramalist_id'] ) $tags_array[] = 'дорама';

if ( $xfields_data['shikimori_year'] ) $tags_array[] = $xfields_data['shikimori_year'];
elseif ( $xfields_data['kodik_year'] ) $tags_array[] = $xfields_data['kodik_year'];

if ( $xfields_data['shikimori_kind_ru'] ) $tags_array[] = $xfields_data['shikimori_kind_ru'];
elseif ( $xfields_data['kodik_video_type'] ) $tags_array[] = $xfields_data['kodik_video_type'];

if ($xfields_data['shikimori_kind'] == "ona") $xfields_data['shikimori_kind'] = "ONA";
elseif ($xfields_data['shikimori_kind'] == "ova") $xfields_data['shikimori_kind'] = "OVA";
elseif ($xfields_data['shikimori_kind'] == "movie") $xfields_data['shikimori_kind'] = "Фильм";
elseif ($xfields_data['shikimori_kind'] == "tv") $xfields_data['shikimori_kind'] = "ТВ-сериал";
elseif ($xfields_data['shikimori_kind'] == "special") $xfields_data['shikimori_kind'] = "Спэшл";
else $tags_array[] = $xfields_data['kodik_status_ru'];
$tags_array[] = $xfields_data['shikimori_kind'];

if ( $movie_kind ) $tags_array[] = $movie_kind;

if ( $xfields_data['kodik_countries'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_countries'])));

if ( $xfields_data['shikimori_genres'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['shikimori_genres'])));
if ( $xfields_data['kodik_genres'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_genres'])));

if ( $xfields_data['worldart_tags'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['worldart_tags'])));

if ( $xfields_data['kodik_translation'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_translation'])));

if ( $xfields_data['kodik_translation_types_ru'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_translation_types_ru'])));

if ( $xfields_data['shikimori_tv_length'] ) $tags_array[] = $xfields_data['shikimori_tv_length'];
elseif ( $xfields_data['kodik_tv_length'] ) $tags_array[] = $xfields_data['kodik_tv_length'];
	
if ( $xfields_data['shikimori_duration_length'] ) $tags_array[] = $xfields_data['shikimori_duration_length'];
elseif ( $xfields_data['kodik_duration_length'] ) $tags_array[] = $xfields_data['kodik_duration_length'];

if ( $aaparser_config['categories'] AND $tags_array ) {
	$tags_array = CheckGenres($tags_array);
	foreach ( $aaparser_config['categories'] as $key => $value ) {
		$finded = false;
		if ( strpos($value, ',') ) {
			$value2 = explode(',', $value);
			foreach ( $value2 as $value3 ) {
				if (in_arrayi($value3, $tags_array)) {
					$finded = true;
					break;
				}
			}
		} elseif (in_arrayi($value, $tags_array)) $finded = true;
		if ($finded) $parse_cat_list[] = $key;
	}

}

unset($tags_array);
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Обработка категории", round(microtime(true) - $time_update_start,4));
}
//Обработка шаблонов доп полей

$xfields_list = array();

$remove_kodik_tags = array();
$remove_kodik_tags[] = "catalog_eng";
$remove_kodik_tags[] = "catalog_rus";
$remove_kodik_tags[] = "kodik_episodes_aired";
$remove_kodik_tags[] = "kodik_episodes_total";
$remove_kodik_tags[] = "kodik_last_episode_8";
$remove_kodik_tags[] = "kodik_last_episode_7";
$remove_kodik_tags[] = "kodik_last_episode_6";
$remove_kodik_tags[] = "kodik_last_episode_5";
$remove_kodik_tags[] = "kodik_last_episode_4";
$remove_kodik_tags[] = "kodik_last_episode_3";
$remove_kodik_tags[] = "kodik_last_episode_2";
$remove_kodik_tags[] = "kodik_last_episode_1";
$remove_kodik_tags[] = "kodik_last_episode";
$remove_kodik_tags[] = "kodik_last_season_8";
$remove_kodik_tags[] = "kodik_last_season_7";
$remove_kodik_tags[] = "kodik_last_season_6";
$remove_kodik_tags[] = "kodik_last_season_5";
$remove_kodik_tags[] = "kodik_last_season_4";
$remove_kodik_tags[] = "kodik_last_season_3";
$remove_kodik_tags[] = "kodik_last_season_2";
$remove_kodik_tags[] = "kodik_last_season_1";
$remove_kodik_tags[] = "kodik_last_season";
$remove_kodik_tags[] = "kodik_operators";
$remove_kodik_tags[] = "kodik_designers";
$remove_kodik_tags[] = "kodik_kinopoisk_rating";
$remove_kodik_tags[] = "kodik_kinopoisk_votes";
$remove_kodik_tags[] = "kodik_imdb_rating";
$remove_kodik_tags[] = "kodik_imdb_votes";
$remove_kodik_tags[] = "kodik_mydramalist_rating";
$remove_kodik_tags[] = "kodik_mydramalist_votes";
$remove_kodik_tags[] = "kodik_minimal_age";
$remove_kodik_tags[] = "kodik_rating_mpaa";
$remove_kodik_tags[] = "kodik_actors";
$remove_kodik_tags[] = "kodik_video_type";
$remove_kodik_tags[] = "kodik_countries";
$remove_kodik_tags[] = "kodik_imdb_id";
$remove_kodik_tags[] = "kodik_translation";
$remove_kodik_tags[] = "kodik_translation_last";
$remove_kodik_tags[] = "kodik_translation_types";
$remove_kodik_tags[] = "kodik_tagline";
$remove_kodik_tags[] = "kodik_premiere_ru";
$remove_kodik_tags[] = "kodik_premiere_world";
$remove_kodik_tags[] = "kodik_iframe";
$remove_kodik_tags[] = "kodik_quality";
$remove_kodik_tags[] = "kodik_worldart_link";
$remove_kodik_tags[] = "kodik_mydramalist_tags";
$remove_kodik_tags[] = "kodik_editors";
$remove_kodik_tags[] = "worldart_plot";

function replaceKodikByShikimori($zna4enie, $tagKodik, $tagShikimori) {
	$zna4enie = str_replace("{".$tagKodik."}", "{".$tagShikimori."}", $zna4enie);
	$zna4enie = str_replace("[if_".$tagKodik."]", "[if_".$tagShikimori."]", $zna4enie);
	$zna4enie = str_replace("[/if_".$tagKodik."]", "[/if_".$tagShikimori."]", $zna4enie);
	$zna4enie = str_replace("[ifnot_".$tagKodik."]", "[ifnot_".$tagShikimori."]", $zna4enie);
	$zna4enie = str_replace("[/ifnot_".$tagKodik."]", "[/ifnot_".$tagShikimori."]", $zna4enie);	
	
	return $zna4enie;
}

foreach($aaparser_config['xfields'] as $named => $zna4enie) {
	
	foreach($remove_kodik_tags as $remove_tag) {
		$zna4enie = str_replace("{".$remove_tag."}", "", $zna4enie);
		$zna4enie = str_replace("[if_".$remove_tag."]", "", $zna4enie);
		$zna4enie = str_replace("[/if_".$remove_tag."]", "", $zna4enie);
		$zna4enie = str_replace("[ifnot_".$remove_tag."]", "", $zna4enie);
		$zna4enie = str_replace("[/ifnot_".$remove_tag."]", "", $zna4enie);			
	}		
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_title" ,"shikimori_russian");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_title_orig" ,"shikimori_name");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_other_title" ,"shikimori_synonyms");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_year" ,"shikimori_year");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_status_en" ,"shikimori_status");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_status_ru" ,"shikimori_status_ru");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_kinopoisk_id" ,"kinopoisk");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_plot" ,"shikimori_plot");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_duration" ,"shikimori_duration");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_duration_2" ,"shikimori_duration_2");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_duration_3" ,"shikimori_duration_3");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_duration_4" ,"shikimori_duration_4");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_genres" ,"shikimori_genres");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_directors" ,"shikimori_director");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_producers" ,"shikimori_producer");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_writers" ,"shikimori_script");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_composers" ,"shikimori_composition");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_premiere_world" ,"shikimori_aired_on_3");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_premiere_ru" ,"shikimori_aired_on_3");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_worldart_link" ,"world_art");
	
	// На всякий, вдруг будут данные с сериями
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_episodes_total" ,"shikimori_episodes");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_episodes_aired" ,"shikimori_episodes_aired");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode" ,"shikimori_episodes_aired");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_1" ,"shikimori_episodes_aired_1");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_2" ,"shikimori_episodes_aired_2");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_3" ,"shikimori_episodes_aired_3");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_4" ,"shikimori_episodes_aired_4");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_5" ,"shikimori_episodes_aired_5");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_6" ,"shikimori_episodes_aired_6");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_7" ,"shikimori_episodes_aired_7");
	$zna4enie = replaceKodikByShikimori($zna4enie, "kodik_last_episode_8" ,"shikimori_episodes_aired_8");

	$xfields_list[$named] = check_if($zna4enie, $xfields_data);
}
foreach ($xfields_list as $key => $value) {
	$xfields_list[$key] = preg_replace("#\{shikimori_.*?\}#uis", "", $xfields_list[$key]);
	
	if ($key == "kodik_kinopoisk_id")  {
		$temp_arr = explode("/", trim($xfields_list[$key], "/"));
		$xfields_list[$key] = $temp_arr[4];
	}
	if ($key == "kodik_worldart_link") $xfields_list[$key] = $xfields_data['world_art'];
	
	if (strpos($xfields_list[$key], "{mydramalist_id}") !== false)$xfields_list[$key] = str_replace("{mydramalist_id}", $xfields_data['mydramalist_id'], $xfields_list[$key]);
}	

$delete_xf = ['title', 'short_story', 'full_story', 'alt_name', 'tags', 'meta_title', 'meta_description', 'meta_keywords', 'catalog'];
foreach ( $delete_xf as $check_value ) {
	if( array_key_exists($check_value, $xfields_list) ) unset($xfields_list[$check_value]);
}
$short_story = $xfields_list['short_story'];
$full_story = $xfields_list['full_story'];

if (trim(strip_tags($shikimori['description_html'])) == "" && isset($aaparser_config['settings_anons']['descript'])) {
	$short_story = check_if($aaparser_config['settings_anons']['descript'], $xfields_list);
	$short_story = check_if($aaparser_config['settings_anons']['descript'], $xfields_data);
	$short_story = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $short_story);
	$full_story = check_if($aaparser_config['settings_anons']['descript'], $xfields_list);
	$full_story = check_if($aaparser_config['settings_anons']['descript'], $xfields_data);
	$full_story = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $full_story);
}

if ( $aaparser_config['main_fields']['xf_shikimori_id'] && $xfields_data['shikimori_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_shikimori_id']] = $xfields_data['shikimori_id'];
if ( $aaparser_config['main_fields']['xf_mdl_id'] && $xfields_data['mydramalist_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_mdl_id']] = $xfields_data['mydramalist_id'];
if ( $aaparser_config['images']['xf_poster'] && $xfields_data['image']) $xfields_list[$aaparser_config['images']['xf_poster']] = $xfields_data['image'];
if ( $aaparser_config['images']['xf_poster_text'] && $xfields_data['image']) $xfields_list[$aaparser_config['images']['xf_poster_text']] = $poster_link;
if ( $aaparser_config['images']['xf_screens'] && $xfields_data['kadr_1']) $xfields_list[$aaparser_config['images']['xf_screens']] = $xf_screen_1.$xf_screen_2.$xf_screen_3.$xf_screen_4.$xf_screen_5;
if ( $aaparser_config['fields']['xf_camrip'] && $is_camrip === true ) $xfields_list[$aaparser_config['fields']['xf_camrip']] = 1;
if ( $aaparser_config['fields']['xf_lgbt'] && $is_lgbt === true ) $xfields_list[$aaparser_config['fields']['xf_lgbt']] = 1;
if ( isset($next_episode_date) && $next_episode_date && $aaparser_config['settings']['next_episode_date_new'] ) $xfields_list[$aaparser_config['settings']['next_episode_date_new']] = $next_episode_date;


if ($xfields_data['shikimori_russian']) $title = $xfields_data['shikimori_russian'];
elseif ($xfields_data['shikimori_name']) $title = $xfields_data['shikimori_name'];
elseif ($xfields_data['shikimori_english']) $title = $xfields_data['shikimori_english'];
elseif ($xfields_data['shikimori_japanese']) $title = $xfields_data['shikimori_japanese'];
elseif ($xfields_data['shikimori_synonyms']) $title = $xfields_data['shikimori_synonyms'];

$alt_name = totranslit($title);
$alt_name = str_replace(".", "", $alt_name);
$alt_name = str_replace(",", "", $alt_name);

$tags = check_if($aaparser_config['xfields']['tags'], $xfields_data);
$tags = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $tags);
$meta_titles = check_if($aaparser_config['xfields']['meta_title'], $xfields_data);
$meta_titles = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $meta_titles);
$meta_descrs = check_if($aaparser_config['xfields']['meta_description'], $xfields_data);
$meta_descrs = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $meta_descrs);
$meta_keywords = check_if($aaparser_config['xfields']['meta_keywords'], $xfields_data);
$meta_keywords = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $meta_keywords);
$catalog = check_if($aaparser_config['xfields']['catalog'], $xfields_data);
$catalog = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $catalog);
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Разбор тегов", round(microtime(true) - $time_update_start,4));
}
if ( $tags ) $tags_array = explode(',', $tags);

$title = $db->safesql( $title );
$short_story = $db->safesql( $short_story );
$full_story = $db->safesql( $full_story );
$alt_name = totranslit_it( $alt_name, true, false );
$alt_name = $db->safesql( $alt_name );

if ($aaparser_config['settings_anons']['cat_id'] !="")$parse_cat_list[] = $aaparser_config['settings_anons']['cat_id'];
$parse_cat_list = array_unique($parse_cat_list);
if ( isset($parse_cat_list) && $parse_cat_list ) $category_list = $db->safesql( implode( ',', $parse_cat_list ) );
else $category_list = '';
$tags = $db->safesql( $tags );
$meta_titles = $db->safesql( $meta_titles );
$meta_descrs = $db->safesql( $meta_descrs );
$meta_keywords = $db->safesql( $meta_keywords );
$catalog = $db->safesql( $catalog );
$new_date = date( "Y-m-d H:i:s", time() );

foreach ($xfields_list as $key=>$value) {
	$xfields_list[$key] = preg_replace("#\{.*?\}|\[if.*?\].*?\[\/if.*?\]#uis", "", $xfields_list[$key]);
}

if ( !$title || !$alt_name ) die('Возникла ошибка, Отсутствует название тайтла, возможно необходимо проверить настройки.');

$xfields_list = xfieldsdatasaved($xfields_list);
$xfields_list = $db->safesql( $xfields_list );

$publish = 1;

if ( $aaparser_config['settings_anons']['publish'] != 1 ) $publish = 0;
if ( $aaparser_config['settings_anons']['publish_image'] == 1 && !$xfields_data['image'] ) $publish = 0;
if ( $aaparser_config['settings_anons']['publish_plot'] == 1 && !$short_story ) $publish = 0;

if ( $aaparser_config['settings_anons']['publish_main'] != 1 ) $publish_main = 0;
else $publish_main = 1;

if ( $aaparser_config['settings_anons']['allow_rating'] != 1 ) $allow_rating = 0;
else $allow_rating = 1;

if ( $aaparser_config['settings_anons']['allow_comments'] != 1 ) $allow_comments = 0;
else $allow_comments = 1;

if ( $aaparser_config['settings_anons']['allow_br'] != 1 ) $allow_br = 0;
else $allow_br = 1;

if ( $aaparser_config['settings_anons']['allow_rss'] != 1 ) $allow_rss = 0;
else $allow_rss = 1;

if ( $aaparser_config['settings_anons']['allow_turbo'] != 1 ) $allow_turbo = 0;
else $allow_turbo = 1;

if ( $aaparser_config['settings_anons']['allow_zen'] != 1 ) $allow_zen = 0;
else $allow_zen = 1;

if ( $aaparser_config['settings_anons']['dissalow_index'] == 1 ) $dissalow_index = 1;
else $dissalow_index = 0;

if ( $aaparser_config['settings_anons']['dissalow_search'] == 1 ) $dissalow_search = 1;
else $dissalow_search = 0;

if ( $aaparser_config['grabbing']['author_name'] && $aaparser_config['grabbing']['author_id'] ) {
	$author = $aaparser_config['grabbing']['author_name'];
	$author_id = $aaparser_config['grabbing']['author_id'];
} else {
	$avtr = $db->super_query(" SELECT name, user_id FROM " . PREFIX . "_users WHERE user_id=1 ");
	$author = $avtr['name'];
	$author_id = 1;
}
$author = $db->safesql($author);
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Обработка шаблонов", round(microtime(true) - $time_update_start,4));
}
$shikimori_franshise = $shiki_id;

$db->query( "INSERT INTO " . PREFIX . "_post (date, autor, short_story, full_story, xfields, title, descr, keywords, category, alt_name, allow_comm, approve, allow_main, fixed, allow_br, symbol, tags, metatitle, franchise_aap) values ('$new_date', '{$author}', '$short_story', '$full_story', '$xfields_list', '$title', '$meta_descrs', '$meta_keywords', '$category_list', '$alt_name', '$allow_comments', '$publish', '$publish_main', '0', '$allow_br', '$catalog', '$tags', '$meta_titles', '$shikimori_franshise')" );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_post", round(microtime(true) - $time_update_start,4));
}
$id = $db->insert_id();
	
$db->query("INSERT INTO ".PREFIX."_shikimori_posts set post_id=$id, shiki_id=$shiki_id");	
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_shikimori_posts", round(microtime(true) - $time_update_start,4));
}
$db->query("INSERT INTO " . PREFIX . "_anime_list (shikimori_id, year, news_id,type, tv_status) VALUES( '{$shiki_id}', '{$year}', '{$id}', '".$shikimori['kind']."', 'anons' ) " );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
}
if ($config['version_id'] == '18.1') $db->query( "INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate, disable_index, user_id, disable_search, allow_rss, allow_rss_dzen) VALUES ('{$id}', '{$allow_rating}', '{$dissalow_index}', '{$author_id}', '{$dissalow_search}', '{$allow_rss}', '{$allow_zen}')" );
else $db->query( "INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate, disable_index, user_id, disable_search, allow_rss, allow_rss_turbo, allow_rss_dzen) VALUES ('{$id}', '{$allow_rating}', '{$dissalow_index}', '{$author_id}', '{$dissalow_search}', '{$allow_rss}', '{$allow_turbo}', '{$allow_zen}')" );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_post_extras", round(microtime(true) - $time_update_start,4));
}
if( is_array($tags_array) && count($tags_array) ) {
	
	$tags = array ();
	
	foreach ( $tags_array as $value ) {
		if ( !$value ) continue;
		if ( $aaparser_config['integration']['latin_tags'] == 1 ) $tags[] = "('" . $id . "', '" . trim( $value ) . "', '" . totranslit(trim($value), true, false) . "')";
		else $tags[] = "('" . $id . "', '" . trim( $value ) . "')";
	}
	
	$tags = implode( ", ", $tags );
	if ( $aaparser_config['integration']['latin_tags'] == 1 ) $db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag, tag_translit) VALUES " . $tags );
	else $db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_tags", round(microtime(true) - $time_update_start,4));
	}
}

if( $category_list) {
	$cat_ids = array ();
	$cat_ids_arr = explode( ",", $category_list );
	foreach ( $cat_ids_arr as $value ) {
		$cat_ids[] = "('" . $id . "', '" . trim( $value ) . "')";
	}
	
	$cat_ids = implode( ", ", $cat_ids );
	$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_post_extras_cats", round(microtime(true) - $time_update_start,4));
	}
}

$newpostedxfields = xfieldsdataload($xfields_list);
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
if ( count($xf_search_words) ) {
	
	$temp_array = array();
	
	foreach ( $xf_search_words as $value ) {
		
		if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $temp_array[] = "('" . $id . "', '" . $value[0] . "', '" . $value[1] . "', '" . $value[2] . "')";
		else $temp_array[] = "('" . $id . "', '" . $value[0] . "', '" . $value[1] . "')";
	}
	
	$xf_search_words = implode( ", ", $temp_array );
	if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue, tagvalue_translit) VALUES " . $xf_search_words );
	else $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_xfsearch", round(microtime(true) - $time_update_start,4));
	}
}

if ( $xfields_data['image'] OR $xfields_data['kadr_1'] ) {
	if ( $aaparser_config['grabbing']['author_name'] ) {
		$author = $aaparser_config['grabbing']['author_name'];
	} else {
		$avtr = $db->super_query(" SELECT name, user_id FROM " . PREFIX . "_users WHERE user_id=1 ");
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(anons_shiki.php) Получение записи с " . PREFIX . "_users", round(microtime(true) - $time_update_start,4));
		}
		$author = $avtr['name'];
	}
	$author = $db->safesql($author);
	
	$images = array();
	if ($xf_poster) $images[] = $xf_poster;
	if ($kadr_1) $images[] = $kadr_1;
	if ($kadr_2) $images[] = $kadr_2;
	if ($kadr_3) $images[] = $kadr_3;
	if ($kadr_4) $images[] = $kadr_4;
	if ($kadr_5) $images[] = $kadr_5;
	
	$images = implode('|||', $images);

	$db->query(" INSERT INTO " . PREFIX . "_images (images, news_id, author, date) VALUES ('{$images}', '{$id}', '{$author}', '".time()."') ");
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Добавление записи в " . PREFIX . "_images", round(microtime(true) - $time_update_start,4));
	}
}

if( $config['news_indexnow'] && $publish == 1 ) {
	$row = $db->super_query( "SELECT id, date, category, alt_name FROM " . PREFIX . "_post WHERE id='{$id}' LIMIT 1" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Получение записи с " . PREFIX . "_post", round(microtime(true) - $time_update_start,4));
	}
	if( $config['allow_alt_url'] ) {
		if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
			if( $row['category'] and $config['seo_type'] == 2 ) {
				$cats_url = get_url( $row['category'] );
				if($cats_url) {
					$full_link = $config['http_home_url'] . $cats_url . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
				} else $full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
			} else $full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
		} else $full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime( $row['date'] ) ) . $row['alt_name'] . ".html";
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(anons_shiki.php) Сео разбор", round(microtime(true) - $time_update_start,4));
		}
	} else $full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
	
	$result = DLESEO::IndexNow( $full_link );
}

if ( $aaparser_config['integration']['google_indexing'] == 1 && file_exists(ENGINE_DIR.'/xoopw/indexing/init.php') && $publish == 1 ) {
	include_once (DLEPlugins::Check(ENGINE_DIR . '/xoopw/indexing/init.php'));
	$indexing = new \XOO\Indexing\Indexing($db);
	
	if( $config['allow_alt_url'] ) {
		if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
			if( $row['category'] and $config['seo_type'] == 2 ) {
				$cats_url = get_url( $row['category'] );
				if($cats_url) {
					$full_link = $config['http_home_url'] . $cats_url . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
				} else $full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
			} else $full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
		} else $full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime( $row['date'] ) ) . $row['alt_name'] . ".html";
	} else $full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
	
	$indexing->setUrls($full_link);
	$indexing->Index();
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(anons_shiki.php) Инициализация Google Indexing", round(microtime(true) - $time_update_start,4));
	}
}

clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats') );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['anons_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(anons_shiki.php) Закончили добавление анонсируемого материала", round(microtime(true) - $time_update_start,4));
}
echo "Добавили: ".$title;