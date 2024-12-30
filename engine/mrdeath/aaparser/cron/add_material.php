<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
if ($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1) { 
    $debugger_table_row .= tableRowCreate("(add_material.php) Начинаем добавление материала", round(microtime(true) - $time_update_start, 4));
}

$where = array();
$where_xf = [];
if ( $kind == 'anime' ) {
	$text_info = 'аниме';
	$text_info_2 = 'аниме';

	if ( $aaparser_config['grabbing']['tv'] == 1 ) $where[] = "'tv'";
	if ( $aaparser_config['grabbing']['movie'] == 1 ) $where[] = "'movie'";
	if ( $aaparser_config['grabbing']['ova'] == 1 ) $where[] = "'ova'";
	if ( $aaparser_config['grabbing']['ona'] == 1 ) $where[] = "'ona'";
	if ( $aaparser_config['grabbing']['special'] == 1 ) $where[] = "'special'";
	if ( $aaparser_config['grabbing']['music'] == 1 ) $where[] = "'music'";

	$where = "shikimori_id <> '' AND news_id=0 AND started=0 AND error=0 AND type IN(".implode(", ", $where).")";

	if ( $aaparser_config['grabbing']['this_year'] == 1 ) $order = "ORDER BY `year` DESC, `material_id` ASC LIMIT 1";
	else $order = "ORDER BY `material_id` ASC LIMIT 1";

	$material = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE ".$where." ".$order );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Получение данных с бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
	}
	if ( !$material['material_id'] ) {
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) На данный момент нечего добавлять", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die('На данный момент нечего добавлять!');
	}
	else $db->query( "UPDATE " . PREFIX . "_anime_list SET started=1 WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
	}
	$shiki_id = $material['shikimori_id'];
	$where_xf[] = "xfields REGEXP '(^|\\\\|)" . $aaparser_config['main_fields']['xf_shikimori_id'] ."\\\\|".$shiki_id. "(\\\\||$)'";

	$parse_action = 'parse';
	$parse_type = 'grabbing';
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Инициализация доноров начата", round(microtime(true) - $time_update_start,4));
	}
	include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/shikimori.php'));
	include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	if ( $aaparser_config['settings']['parse_wa'] == 1 ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/world_art.php'));
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Инициализация доноров завершена", round(microtime(true) - $time_update_start,4));
	}
} else {
	
	$text_info = 'дорама';
	$text_info_2 = 'дораму';

	$where = "mdl_id <> '' AND news_id=0 AND started=0 AND error=0";

	if ( $aaparser_config['grabbing_doram']['this_year'] == 1 ) $order = "ORDER BY `year` DESC, `material_id` ASC LIMIT 1";
	else $order = "ORDER BY `material_id` ASC LIMIT 1";

	$material = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE ".$where." ".$order );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Получение данных с бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
	}
	if ( !$material['material_id'] ) {
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) На данный момент нечего добавлять", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die('На данный момент нечего добавлять!');
	}
	else $db->query( "UPDATE " . PREFIX . "_anime_list SET started=1 WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
	}
	$mdl_id = $material['mdl_id'];
	$where_xf[] = "xfields REGEXP '(^|\\\\|)" . $aaparser_config['main_fields']['xf_mdl_id'] . "\\\\|" . $mdl_id . "(\\\\||$)'";

	$parse_action = 'parse';
	$parse_type = 'grabbing';
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Инициализация доноров начата", round(microtime(true) - $time_update_start,4));
	}
	include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Инициализация доноров завершена", round(microtime(true) - $time_update_start,4));
	}
}
$where_xf = implode(' OR ', $where_xf);
foreach ( $data_list as $tag_list ) {
	if ( !$xfields_data[$tag_list] ) $xfields_data[$tag_list] = '';
}
	
if ( $xfields_data['kodik_title'] ) $kodik_title = $xfields_data['kodik_title'];
else $kodik_title = $xfields_data['kodik_title_en'];

$stoped_time = time();

if ( !$xfields_data['shikimori_id'] && !$xfields_data['mydramalist_id'] ) {
	$db->query( "UPDATE " . PREFIX . "_anime_list SET error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
		echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
	}
	die('Похоже что у балансера технические проблемы. Пропущено '.$text_info_2.' '.$kodik_title.'. Добавим позже');
}

if ( !$xfields_data['kodik_iframe'] ) {
	$db->query( "UPDATE " . PREFIX . "_anime_list SET error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
		echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
	}
	die('Похоже что у балансера технические проблемы. Пропущено '.$text_info_2.' '.$kodik_title.'. Добавим позже');
}

if ( isset( $aaparser_config['blacklist_shikimori'] ) && $xfields_data['shikimori_id'] ) {
	if ( in_array( $xfields_data['shikimori_id'], $aaparser_config['blacklist_shikimori'] ) ) {
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) Завершение скрипта", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die($text_info_2.' '.$kodik_title.' входит в чёрный список аниме. Пропущено');
	}
}

if ( isset( $aaparser_config['blacklist_mdl'] ) && $xfields_data['mydramalist_id'] ) {
	if ( in_array( $xfields_data['mydramalist_id'], $aaparser_config['blacklist_mdl'] ) ) {
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) Завершение скрипта", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die($text_info_2.' '.$kodik_title.' входит в чёрный список дорам. Пропущено');
	}
}

$searching_post = $db->super_query("SELECT id, xfields FROM " . PREFIX . "_post WHERE " . $where_xf);
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
	$debugger_table_row .= tableRowCreate("(add_material.php) Получение данных с " . PREFIX . "_post", round(microtime(true) - $time_update_start,4));
}
if ( $searching_post['id'] > 0 ) {
	$news_id = $searching_post['id'];
	$type = $material['type'];
	$db->query( "UPDATE " . PREFIX . "_anime_list SET news_id='{$news_id}' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
		echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
	}
	die('У вас на сайте уже есть '.$text_info.' '.$kodik_title.'. Добавление пропущено');
}

//Проверка годов выхода
if ( $aaparser_config['grabbing']['years'] && $xfields_data['shikimori_year'] ) {
	$allowed_years = explode(',', $aaparser_config['grabbing']['years']);
	if (!in_array($xfields_data['shikimori_year'], $allowed_years)) {
		$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die('Год выхода не входит в список разрешенных годов. Пропущено '.$text_info_2.' '.$kodik_title);
	}
}
if ( $aaparser_config['grabbing']['not_years'] && $xfields_data['shikimori_year'] ) {
	$not_allowed_years = explode(',', $aaparser_config['grabbing']['not_years']);
	if (in_array($xfields_data['shikimori_year'], $not_allowed_years)) {
		$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die('Год выхода входит в список запрещенных годов. Пропущено '.$text_info_2.' '.$kodik_title);
	}
}

//Проверка жанров
if ( $aaparser_config['grabbing']['genres'] && $xfields_data['shikimori_genres'] ) {
	$allowed_genres = explode(',', $aaparser_config['grabbing']['genres']);
	if ( $aaparser_config['grabbing']['this_country'] == 1 ) {
		if ( !in_array($xfields_data['shikimori_genres'], $allowed_genres) ) {
			$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
				echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
			}
			die('Страна не входит в список разрешенных стран. Пропущено '.$text_info_2.' '.$kodik_title);
		}
	} else {
		$material_genres = explode(', ', $xfields_data['shikimori_genres']);
		if (!count(array_intersect($material_genres, $allowed_genres))) {
			$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
				echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
			}
			die('Жанр не входит в список разрешенных жанров. Пропущено '.$text_info_2.' '.$kodik_title);
		}
	}
}
if ( $aaparser_config['grabbing']['not_genres'] && $xfields_data['shikimori_genres'] ) {
	$not_allowed_genres = explode(',', $aaparser_config['grabbing']['not_genres']);
	$material_genres = explode(', ', $xfields_data['shikimori_genres']);
	if (count(array_intersect($material_genres, $not_allowed_genres))) {
		$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die('Жанр входит в список запрещённых жанров. Пропущено '.$text_info_2.' '.$kodik_title);
	}
}
//Проверка озвучек
if ( $aaparser_config['grabbing']['translators'] && $xfields_data['kodik_translation'] ) {
	$allowed_translators = explode(',', $aaparser_config['grabbing']['translators']);
	$material_translators = explode(', ', $xfields_data['kodik_translation']);
	if (!count(array_intersect($material_translators, $allowed_translators))) {
		$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die('Озвучка не входит в список разрешенных озвучек. Пропущено '.$text_info_2.' '.$kodik_title);
	}
}
if ( $aaparser_config['grabbing']['not_translators'] && $xfields_data['kodik_translation'] ) {
	$not_allowed_translators = explode(',', $aaparser_config['grabbing']['not_translators']);
	if ( in_array($xfields_data['kodik_translation'], $not_allowed_translators) ) {
		$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
			echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
		}
		die('Озвучка входит в список запрещённых озвучек. Пропущено '.$text_info_2.' '.$kodik_title);
	}
}
//Проверка на вшитую рекламу и camrip
if ( $aaparser_config['grabbing']['if_camrip'] != 1 && $is_camrip === true ) {
	$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
		echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
	}
	die('В релизе есть вшитая реклама. Пропущено '.$text_info_2.' '.$kodik_title);
}

//Проверка на сцены LGBT
if ( $aaparser_config['grabbing']['if_lgbt'] != 1 && $is_lgbt === true ) {
	$db->query( "UPDATE " . PREFIX . "_anime_list SET skipped=1, error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
		echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
	}
	die('В релизе есть lgbt сцены. Пропущено '.$text_info_2.' '.$kodik_title);
}
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
	$debugger_table_row .= tableRowCreate("(add_material.php) Проверка условии списков и поиск по бд", round(microtime(true) - $time_update_start,4));
}
//Работа с картинками

$id_news = 0;

$_REQUEST['module'] = 'aaparser';
include_once(DLEPlugins::Check(ENGINE_DIR . '/classes/uploads/upload.class.php'));
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Внедрение uploads.class.php", round(microtime(true) - $time_update_start,4));
}
if ( $mode != 'editnews' && $xfields_data['image'] && $aaparser_config['images']['poster'] == 1 ) $need_poster = true;
elseif ( $mode == 'editnews' && $xfields_data['image'] && $aaparser_config['images']['poster_edit'] == 1 ) $need_poster = true;
else $need_poster = false;

if ( $need_poster === true ) {
	if ( $xfields_data['shikimori_russian'] ) $poster_file = totranslit_it($xfields_data['shikimori_russian'], true, false);
	elseif ( $xfields_data['shikimori_name'] ) $poster_file = totranslit_it($xfields_data['shikimori_name'], true, false);
	elseif ( $xfields_data['kodik_title'] ) $poster_file = totranslit_it($xfields_data['kodik_title'], true, false);
	else $poster_file = totranslit_it($xfields_data['kodik_title_orig'], true, false);
	$poster = setPoster($xfields_data['image'], $poster_file, 'poster', $aaparser_config['images']['xf_poster'], $id_news);
	if ( isset($poster) && is_array($poster) ) {
		if ( $aaparser_config['images']['xf_poster'] ) $xfields_data['image'] = $poster['xfvalue'];
		else $xfields_data['image'] = $poster['link'];
		$xf_poster = $poster['xfvalue'];
		$poster_code = $poster['returnbox'];
		$poster_link = $poster['link'];
	}
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Загрузка постеров", round(microtime(true) - $time_update_start,4));
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(add_material.php) Загрузка скриншота 1", round(microtime(true) - $time_update_start,4));
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(add_material.php) Загрузка скриншота 2", round(microtime(true) - $time_update_start,4));
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(add_material.php) Загрузка скриншота 3", round(microtime(true) - $time_update_start,4));
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(add_material.php) Загрузка скриншота 4", round(microtime(true) - $time_update_start,4));
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(add_material.php) Загрузка скриншота 5", round(microtime(true) - $time_update_start,4));
			}
		}
		
	}
}

//Буквенный каталог

if ( $xfields_data['shikimori_russian'] ) $xfields_data['catalog_rus'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['shikimori_russian'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
else $xfields_data['catalog_rus'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['kodik_title'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
if ( $xfields_data['shikimori_name'] ) $xfields_data['catalog_eng'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['shikimori_name'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
else $xfields_data['catalog_eng'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['kodik_title_en'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Создание буквенного каталога", round(microtime(true) - $time_update_start,4));
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
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Обработка категории", round(microtime(true) - $time_update_start,4));
}
//Обработка шаблонов доп полей

$xfields_list = array();

foreach($aaparser_config['xfields'] as $named => $zna4enie) {
	 $xfields_list[$named] = check_if($zna4enie, $xfields_data);
}
$delete_xf = ['title', 'short_story', 'full_story', 'alt_name', 'tags', 'meta_title', 'meta_description', 'meta_keywords', 'catalog'];
foreach ( $delete_xf as $check_value ) {
	if( array_key_exists($check_value, $xfields_list) ) unset($xfields_list[$check_value]);
}

if ( $aaparser_config['main_fields']['xf_shikimori_id'] && $xfields_data['shikimori_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_shikimori_id']] = $xfields_data['shikimori_id'];
if ( $aaparser_config['main_fields']['xf_mdl_id'] && $xfields_data['mydramalist_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_mdl_id']] = $xfields_data['mydramalist_id'];
if ( $aaparser_config['images']['xf_poster'] && $xfields_data['image']) $xfields_list[$aaparser_config['images']['xf_poster']] = $xfields_data['image'];
if ( $aaparser_config['images']['xf_poster_text'] && $xfields_data['image']) $xfields_list[$aaparser_config['images']['xf_poster_text']] = $poster_link;
if ( $aaparser_config['images']['xf_screens'] && $xfields_data['kadr_1']) $xfields_list[$aaparser_config['images']['xf_screens']] = $xf_screen_1.$xf_screen_2.$xf_screen_3.$xf_screen_4.$xf_screen_5;
if ( $aaparser_config['fields']['xf_camrip'] && $is_camrip === true ) $xfields_list[$aaparser_config['fields']['xf_camrip']] = 1;
if ( $aaparser_config['fields']['xf_lgbt'] && $is_lgbt === true ) $xfields_list[$aaparser_config['fields']['xf_lgbt']] = 1;

if ( isset($next_episode_date) && $next_episode_date && $aaparser_config['settings']['next_episode_date_new'] ) {
	$xfields_list[$aaparser_config['settings']['next_episode_date_new']] = $next_episode_date;
}

$title = check_if($aaparser_config['xfields']['title'], $xfields_data);
$short_story = check_if($aaparser_config['xfields']['short_story'], $xfields_data);
$full_story = check_if($aaparser_config['xfields']['full_story'], $xfields_data);
$alt_name = check_if($aaparser_config['xfields']['alt_name'], $xfields_data);
$tags = check_if($aaparser_config['xfields']['tags'], $xfields_data);
$meta_titles = check_if($aaparser_config['xfields']['meta_title'], $xfields_data);
$meta_descrs = check_if($aaparser_config['xfields']['meta_description'], $xfields_data);
$meta_keywords = check_if($aaparser_config['xfields']['meta_keywords'], $xfields_data);
$catalog = check_if($aaparser_config['xfields']['catalog'], $xfields_data);

if ( $tags ) $tags_array = explode(',', $tags);

$title = $db->safesql( $title );
$short_story = $db->safesql( $short_story );
$full_story = $db->safesql( $full_story );
$alt_name = totranslit_it( $alt_name, true, false );
$alt_name = $db->safesql( $alt_name );
if ( isset($parse_cat_list) && $parse_cat_list ) $category_list = $db->safesql( implode( ',', $parse_cat_list ) );
else $category_list = '';
$tags = $db->safesql( $tags );
$meta_titles = $db->safesql( $meta_titles );
$meta_descrs = $db->safesql( $meta_descrs );
$meta_keywords = $db->safesql( $meta_keywords );
$catalog = $db->safesql( $catalog );
$new_date = date( "Y-m-d H:i:s", time() );
$xfields_list = xfieldsdatasaved($xfields_list);
$xfields_list = $db->safesql( $xfields_list );

$publish = 1;

if ( $aaparser_config['grabbing']['publish'] != 1 ) $publish = 0;
if ( $aaparser_config['grabbing']['publish_image'] == 1 && !$xfields_data['image'] ) $publish = 0;
if ( $aaparser_config['grabbing']['publish_plot'] == 1 && !$short_story ) $publish = 0;

if ( $aaparser_config['grabbing']['publish_main'] != 1 ) $publish_main = 0;
else $publish_main = 1;

if ( $aaparser_config['grabbing']['allow_rating'] != 1 ) $allow_rating = 0;
else $allow_rating = 1;

if ( $aaparser_config['grabbing']['allow_comments'] != 1 ) $allow_comments = 0;
else $allow_comments = 1;

if ( $aaparser_config['grabbing']['allow_br'] != 1 ) $allow_br = 0;
else $allow_br = 1;

if ( $aaparser_config['grabbing']['allow_rss'] != 1 ) $allow_rss = 0;
else $allow_rss = 1;

if ( $aaparser_config['grabbing']['allow_turbo'] != 1 ) $allow_turbo = 0;
else $allow_turbo = 1;

if ( $aaparser_config['grabbing']['allow_zen'] != 1 ) $allow_zen = 0;
else $allow_zen = 1;

if ( $aaparser_config['grabbing']['dissalow_index'] == 1 ) $dissalow_index = 1;
else $dissalow_index = 0;

if ( $aaparser_config['grabbing']['dissalow_search'] == 1 ) $dissalow_search = 1;
else $dissalow_search = 0;

if ( $aaparser_config['grabbing']['author_name'] && $aaparser_config['grabbing']['author_id'] ) {
	$author = $aaparser_config['grabbing']['author_name'];
	$author_id = $aaparser_config['grabbing']['author_id'];
}
else {
	$avtr = $db->super_query(" SELECT name, user_id FROM " . PREFIX . "_users WHERE user_id=1 ");
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Получение данных с бд " . PREFIX . "_users", round(microtime(true) - $time_update_start,4));
	}
	$author = $avtr['name'];
	$author_id = 1;
}
$author = $db->safesql($author);
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Обработка шаблонов", round(microtime(true) - $time_update_start,4));
}
if ( !$title || !$alt_name ) {
	$db->query( "UPDATE " . PREFIX . "_anime_list SET error='{$stoped_time}' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление данных в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
		echo $debugger_table_start.$debugger_table_row.$debugger_table_end.$debugger_table_style;
	}
	die('Возникла ошибка, Отсутствует название тайтла, возможно необходимо проверить настройки.<br>Если вы используете парсинг дорам, то Вам необходимо использовать теги Kodik. Так как у дорам отсутсвуют данные с SHIKIMORI');
}

$shikimori_franshise = $shiki_id;
$db->query( "INSERT INTO " . PREFIX . "_post (date, autor, short_story, full_story, xfields, title, descr, keywords, category, alt_name, allow_comm, approve, allow_main, fixed, allow_br, symbol, tags, metatitle, franchise_aap) values ('$new_date', '{$author}', '$short_story', '$full_story', '$xfields_list', '$title', '$meta_descrs', '$meta_keywords', '$category_list', '$alt_name', '$allow_comments', '$publish', '$publish_main', '0', '$allow_br', '$catalog', '$tags', '$meta_titles', '$shikimori_franshise')" );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Добавление записи в бд " . PREFIX . "_post", round(microtime(true) - $time_update_start,4));
}
$id = $db->insert_id();

if ( $id > 0 ) {
	$news_id = $id;
	$db->query( "UPDATE " . PREFIX . "_anime_list SET news_id='{$news_id}' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление записи в бд " . PREFIX . "_post", round(microtime(true) - $time_update_start,4));
	}
}
else {
	$db->query( "UPDATE " . PREFIX . "_anime_list SET error='{$stoped_time}', news_id='0' WHERE material_id='{$material['material_id']}'" );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Обновление записи в бд " . PREFIX . "_anime_list", round(microtime(true) - $time_update_start,4));
	}
}

$db->query( "INSERT INTO " . PREFIX . "_post_extras (news_id, allow_rate, disable_index, user_id, disable_search, allow_rss, allow_rss_turbo, allow_rss_dzen) VALUES ('{$id}', '{$allow_rating}', '{$dissalow_index}', '{$author_id}', '{$dissalow_search}', '{$allow_rss}', '{$allow_turbo}', '{$allow_zen}')" );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) {
	$debugger_table_row .= tableRowCreate("(add_material.php) Добавление записи в бд " . PREFIX . "_post_extras", round(microtime(true) - $time_update_start,4));
}
if( is_array($tags_array) && count($tags_array) AND $publish == 1 ) {
	$tags = array ();
	
	foreach ( $tags_array as $value ) {
		if ( !$value ) continue;
		$value = $db->safesql( $value );
		if ( $aaparser_config['integration']['latin_tags'] == 1 ) $tags[] = "('" . $id . "', '" . trim( $value ) . "', '" . totranslit(trim($value), true, false) . "')";
		else $tags[] = "('" . $id . "', '" . trim( $value ) . "')";
	}
	
	$tags = implode( ", ", $tags );
	if ( $aaparser_config['integration']['latin_tags'] == 1 ) $db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag, tag_translit) VALUES " . $tags );
	else $db->query( "INSERT INTO " . PREFIX . "_tags (news_id, tag) VALUES " . $tags );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Добавление записи в бд " . PREFIX . "_tags", round(microtime(true) - $time_update_start,4));
	}
}

if( $category_list AND $publish == 1 ) {
	$cat_ids = array ();
	$cat_ids_arr = explode( ",", $category_list );
	foreach ( $cat_ids_arr as $value ) {
		$cat_ids[] = "('" . $id . "', '" . trim( $value ) . "')";
	}
	
	$cat_ids = implode( ", ", $cat_ids );
	$db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Добавление записи в бд " . PREFIX . "_post_extras_cats", round(microtime(true) - $time_update_start,4));
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
if ( count($xf_search_words) AND $publish == 1 ) {
	
	$temp_array = array();
	
	foreach ( $xf_search_words as $value ) {
		
		if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $temp_array[] = "('" . $id . "', '" . $value[0] . "', '" . $value[1] . "', '" . $value[2] . "')";
		else $temp_array[] = "('" . $id . "', '" . $value[0] . "', '" . $value[1] . "')";
	}
	
	$xf_search_words = implode( ", ", $temp_array );
	if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue, tagvalue_translit) VALUES " . $xf_search_words );
	else $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Добавление записи в бд " . PREFIX . "_xfsearch", round(microtime(true) - $time_update_start,4));
	}
}

if ( $xfields_data['image'] OR $xfields_data['kadr_1'] ) {
	if ( $aaparser_config['grabbing']['author_name'] ) {
		$author = $aaparser_config['grabbing']['author_name'];
	} else {
		$avtr = $db->super_query(" SELECT name, user_id FROM " . PREFIX . "_users WHERE user_id=1 ");
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(add_material.php) Получение записи с бд " . PREFIX . "_users", round(microtime(true) - $time_update_start,4));
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
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Добавление записи в бд " . PREFIX . "_images", round(microtime(true) - $time_update_start,4));
	}
}

$row = $db->super_query( "SELECT id, date, category, alt_name FROM " . PREFIX . "_post WHERE id='{$id}' LIMIT 1" );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Получение записи с бд " . PREFIX . "_post", round(microtime(true) - $time_update_start,4));
}
if( $config['allow_alt_url'] ) {
	if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
		if( $row['category'] and $config['seo_type'] == 2 ) {
			$cats_url = get_url( $row['category'] );
			if($cats_url) $full_link = $config['http_home_url'] . $cats_url . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
			else $full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
		} else $full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
	} else $full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime( $row['date'] ) ) . $row['alt_name'] . ".html";
} else $full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];

if( $config['news_indexnow'] && $publish == 1 ) $result = DLESEO::IndexNow( $full_link );
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Сео разбор", round(microtime(true) - $time_update_start,4));
}
if ( $aaparser_config['push_notifications']['google_indexing'] == 1 && $publish == 1 ) {
	$indexing_action = 'send';
	$indexing_type = 'URL_UPDATED';
	$indexing_url = $full_link;
	include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/google_indexing/indexing.php'));
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Отправка Google Indexing", round(microtime(true) - $time_update_start,4));
	}
}

if ( $aaparser_config['push_notifications']['enable_tgposting'] == 1 && $aaparser_config['push_notifications']['tg_cron_modadd'] == 1 && $publish == 1 ) {
	telegram_sender($id, 'addnews_cron');
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php) Отправка Telegram Posting", round(microtime(true) - $time_update_start,4));
	}
}
    
if ( $aaparser_config['integration']['ksep'] == 1 && file_exists(ENGINE_DIR.'/mrdeath/ksep/modules/aap.php') ) {
    if ( isset($shiki_id) && $shiki_id ) $shikiid = $shiki_id;
    else $shikiid = false;
    if ( isset($mdl_id) && $mdl_id ) $mdlid = $mdl_id;
    else $mdlid = false;
    $rowid = $row['id'];
    $required_from = 'aap';
    require_once ENGINE_DIR.'/mrdeath/ksep/data/config.php';
    require_once ENGINE_DIR.'/mrdeath/ksep/functions/module.php';
    require_once ENGINE_DIR.'/mrdeath/ksep/modules/aap.php';
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(add_material.php)  Инициализация посерийного модуля", round(microtime(true) - $time_update_start,4));
	}
}
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['add_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(add_material.php) Закончили добавление материала", round(microtime(true) - $time_update_start,4));
}
clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats') );
echo "Добавили ".$text_info_2." ".$kodik_title;