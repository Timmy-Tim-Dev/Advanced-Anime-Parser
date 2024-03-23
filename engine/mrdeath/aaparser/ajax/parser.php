<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/


$action = isset($_GET['action']) ? $_GET['action'] : '';
$title = isset($_GET['title']) ? $_GET['title'] : '';
$shiki_id = isset($_GET['shiki_id']) ? $_GET['shiki_id'] : 0;
$mdl_id = isset($_GET['mdl_id']) ? $_GET['mdl_id'] : 0;
$id_news = isset($_GET['id_news']) ? $_GET['id_news'] : 0;
$mode = isset($_GET['mode']) ? $_GET['mode'] : '';

$is_logged = false;

include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';
require_once ENGINE_DIR . '/mrdeath/aaparser/functions/module.php';
require_once ENGINE_DIR . '/mrdeath/aaparser/functions/public.php';

@header('Content-type: text/html; charset=' . $config['charset']);

date_default_timezone_set($config['date_adjust']);

if( !$user_group ) $user_group = get_vars( "usergroup" );
if( !$user_group ) {
    $user_group = array ();

    $us = $dle_api->load_table( USERPREFIX . "_usergroups", '*',1,true,0,0, 'id', 'asc');

    foreach ( $us as $row) {

        $user_group[$row['id']] = array ();

        foreach ( $row as $key => $value ) {
            $user_group[$row['id']][$key] = stripslashes($value);
        }

    }
    set_vars( "usergroup", $user_group );
}

if (!$member_id) $member_id = get_vars( "member_id" );
if (!$member_id) {
    if (!isset($_COOKIE['dle_user_id'])) die("Пройдите авторизацию на сайте!");
    $member_id = $dle_api->load_table(USERPREFIX . '_users', '*', "user_id = {$_COOKIE['dle_user_id']}");
    set_vars('member_id', $member_id);
}

if ( isset($aaparser_config['settings']['kodik_api_domain']) ) $kodik_api_domain = $aaparser_config['settings']['kodik_api_domain'];
else $kodik_api_domain = 'https://kodikapi.com/';

if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) {
    $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
    $shikimori_image_domain = 'https://'.clean_url($shikimori_api_domain);
}
else {
    $shikimori_api_domain = 'https://shikimori.me/';
    $shikimori_image_domain = 'https://shikimori.me';
}

if ( $action == "parser_search" ) {
    
    $search_name = str_replace(' ', '+', $title);
    
    $parse_action = 'search';
    if ( $aaparser_config['settings']['working_mode'] == 1 || $aaparser_config['settings']['working_mode'] == 2 ) {
        include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
        $responseArray = unique_multidim_array($responseArray,'unique_id');
    }
    else {
        include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/shikimori.php'));
        $responseArray = unique_multidim_array($responseArray,'shiki_id');
    }
	
	if ($responseArray) {
		die(json_encode(array(
			'status' => 'results',
			'result' => $responseArray,
		)));
	} else {
		die(json_encode(array(
			'status' => 'error',
			'error' => '#02',
		)));
	}
}
elseif ( $action == "parser_kodikplayer" ) {
	$shiki_id = $_REQUEST['shiki_id'];
	$parse_action = 'parse';
	include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));	
	echo $kodik['results'][0]['link'];
}
elseif ( $action == "parser_get" ) {
	
	$parse_action = 'parse';
	
	if ( $aaparser_config['settings']['working_mode'] == 1 ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	else {
        if ( $shiki_id ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/shikimori.php'));
	    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	    if ( $aaparser_config['settings']['parse_wa'] == 1 && $shiki_id ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/world_art.php'));
	}
	
	foreach ( $data_list as $tag_list ) {
	    if ( !$xfields_data[$tag_list] ) $xfields_data[$tag_list] = '';
	}
	
	//Работа с картинками
	
	if ( isset( $aaparser_config['settings']['parse_jikan'] ) && $shiki_id ) {
	    $jikan_api = request('https://api.jikan.moe/v4/anime/'.$shiki_id);
	    if ( isset( $jikan_api['data']['images']['jpg']['large_image_url'] ) && $jikan_api['data']['images']['jpg']['large_image_url'] ) 
	        $xfields_data['image'] = $jikan_api['data']['images']['jpg']['large_image_url'];
	}
	
	$_REQUEST['module'] = 'aaparser';
	include_once(DLEPlugins::Check(ENGINE_DIR . '/classes/uploads/upload.class.php'));
	
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
            $xfields_data['image'] = $poster['link'];
            $xf_poster = $poster['xfvalue'];
	        $poster_code = $poster['returnbox'];
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
	                $screens_code = $screen_1['returnbox'];
	                $kadr_1 = $screen_1['xfvalue'];
                }
	        }
	        
	        if ( $xfields_data['kadr_2'] AND 2 <= $aaparser_config['images']['screens_count'] ) {
                $screen_2_file = $screen_named.'_kadr_2';
                $screen_2 = setPoster($xfields_data['kadr_2'], $screen_2_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
                if ( isset($screen_2) && is_array($screen_2) ) {
                    $xfields_data['kadr_2'] = $screen_2['link'];
                    $xf_screen_2 = ",".$screen_2['xfvalue'];
	                $screens_code .= $screen_2['returnbox'];
	                $kadr_2 = $screen_2['xfvalue'];
                }
	        }
	        
	        if ( $xfields_data['kadr_3'] AND 3 <= $aaparser_config['images']['screens_count'] ) {
                $screen_3_file = $screen_named.'_kadr_3';
                $screen_3 = setPoster($xfields_data['kadr_3'], $screen_3_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
                if ( isset($screen_3) && is_array($screen_3) ) {
                    $xfields_data['kadr_3'] = $screen_3['link'];
                    $xf_screen_3 = ",".$screen_3['xfvalue'];
	                $screens_code .= $screen_3['returnbox'];
	                $kadr_3 = $screen_3['xfvalue'];
                }
	        }
	        
	        if ( $xfields_data['kadr_4'] AND 4 <= $aaparser_config['images']['screens_count'] ) {
                $screen_4_file = $screen_named.'_kadr_4';
                $screen_4 = setPoster($xfields_data['kadr_4'], $screen_4_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
                if ( isset($screen_4) && is_array($screen_4) ) {
                    $xfields_data['kadr_4'] = $screen_4['link'];
                    $xf_screen_4 = ",".$screen_4['xfvalue'];
	                $screens_code .= $screen_4['returnbox'];
	                $kadr_4 = $screen_4['xfvalue'];
                }
	        }
	        
	        if ( $xfields_data['kadr_5'] AND 5 <= $aaparser_config['images']['screens_count'] ) {
                $screen_5_file = $screen_named.'_kadr_5';
                $screen_5 = setPoster($xfields_data['kadr_5'], $screen_5_file, 'kadr', $aaparser_config['images']['xf_screens'], $id_news);
                if ( isset($screen_5) && is_array($screen_5) ) {
                    $xfields_data['kadr_5'] = $screen_5['link'];
                    $xf_screen_5 = ",".$screen_5['xfvalue'];
	                $screens_code .= $screen_5['returnbox'];
	                $kadr_5 = $screen_5['xfvalue'];
                }
	        }
	        
	    }
	}
	
	//Буквенный каталог
	
	if ( $xfields_data['shikimori_russian'] ) $xfields_data['catalog_rus'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['shikimori_russian'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
	else $xfields_data['catalog_rus'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['kodik_title'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
	if ( $xfields_data['shikimori_name'] ) $xfields_data['catalog_eng'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['shikimori_name'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
	else $xfields_data['catalog_eng'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $xfields_data['kodik_title_en'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
	
	//Обработка категорий
	
	$tags_array = array();
	
	if ( $xfields_data['shikimori_id'] || $shiki_id ) $tags_array[] = 'аниме';
	elseif ( $xfields_data['mydramalist_id'] ) $tags_array[] = 'дорама';
	
	if ( $xfields_data['shikimori_year'] ) $tags_array[] = $xfields_data['shikimori_year'];
	elseif ( $xfields_data['kodik_year'] ) $tags_array[] = $xfields_data['kodik_year'];
	
    if ( $xfields_data['shikimori_kind_ru'] ) $tags_array[] = $xfields_data['shikimori_kind_ru'];
    elseif ( $xfields_data['kodik_video_type'] ) $tags_array[] = $xfields_data['kodik_video_type'];
    
    if ( $xfields_data['shikimori_status_ru'] ) $tags_array[] = $xfields_data['shikimori_status_ru'];
    elseif ( $xfields_data['kodik_status_ru'] ) $tags_array[] = $xfields_data['kodik_status_ru'];
    
    if ( $movie_kind ) $tags_array[] = $movie_kind;
    
    if ( $xfields_data['kodik_countries'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_countries'])));
    
    if ( $xfields_data['shikimori_genres'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['shikimori_genres'])));
    elseif ( $xfields_data['kodik_genres'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_genres'])));
    
    if ( $xfields_data['worldart_tags'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['worldart_tags'])));
    
    if ( $xfields_data['kodik_translation'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_translation'])));
    
	if ( $xfields_data['kodik_translation_types_ru'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_translation_types_ru'])));
	
	if ( $aaparser_config['categories'] AND $tags_array ) {
		
		foreach ( $aaparser_config['categories'] as $key => $value ) {
		    $finded = true;
		    if ( strpos($value, ',') ) {
		        $value2 = explode(',', $value);
		        foreach ( $value2 as $value3 ) {
		            if ( !in_arrayi($value3, $tags_array) ) {
		                $finded = false;
		                break;
		            }
		        }
		    }
		    elseif( !in_arrayi($value, $tags_array) ) $finded = false;
		    if ( $finded ) $parse_cat_list[] = $key;
		}
		
		if (is_array($parse_cat_list)) {
			$parse_cat_list = implode(",", $parse_cat_list);
		}
	
	}
	
	//Обработка шаблонов доп полей
	
	$array_data2 = [];
    
    $delete_xf = ['title', 'alt_name', 'tags', 'meta_title', 'meta_description', 'meta_keywords', 'catalog'];
    foreach($aaparser_config['xfields'] as $named => $zna4enie) {
        if ( in_array($named, $delete_xf) ) continue;
        $array_data2[$named] = check_if($zna4enie, $xfields_data);
    }
	
	if ( isset($aaparser_config['xfields']['title']) ) $array_data2['title'] = check_if($aaparser_config['xfields']['title'], $xfields_data);
    if ( isset($aaparser_config['xfields']['alt_name']) ) $array_data2['alt_name'] = check_if($aaparser_config['xfields']['alt_name'], $xfields_data);
    if ( isset($aaparser_config['xfields']['tags']) ) $array_data2['tags'] = check_if($aaparser_config['xfields']['tags'], $xfields_data);
    if ( isset($aaparser_config['xfields']['meta_title']) ) $array_data2['meta_titles'] = check_if($aaparser_config['xfields']['meta_title'], $xfields_data);
    if ( isset($aaparser_config['xfields']['meta_description']) ) $array_data2['meta_descrs'] = check_if($aaparser_config['xfields']['meta_description'], $xfields_data);
    if ( isset($aaparser_config['xfields']['meta_keywords']) ) $array_data2['meta_keywords'] = check_if($aaparser_config['xfields']['meta_keywords'], $xfields_data);
	if ( isset($aaparser_config['xfields']['catalog']) ) $array_data2['catalog'] = check_if($aaparser_config['xfields']['catalog'], $xfields_data);
    
    $array_data2['parse_cat_list'] = $parse_cat_list;
    if ( $poster_code && $aaparser_config['images']['xf_poster'] ) {
        $array_data2['xf_poster'] = $poster_code;
        $array_data2['xf_poster_name'] = $aaparser_config['images']['xf_poster'];
        $array_data2['xf_poster_url'] = $xf_poster;
    }
    if ( $screens_code && $aaparser_config['images']['xf_screens'] ) {
        $array_data2['xf_screens'] = $screens_code;
        $array_data2['xf_screens_name'] = $aaparser_config['images']['xf_screens'];
        $array_data2['xf_screens_url'] = $xf_screen_1.$xf_screen_2.$xf_screen_3.$xf_screen_4.$xf_screen_5;
    }
    
    if ( isset($next_episode_date) && $next_episode_date ) $array_data2[$aaparser_config['settings']['next_episode_date_new']] = $next_episode_date;
    
    //Связывание изображений с новостью в бд
    
    if ( $xfields_data['image'] OR $xfields_data['kadr_1'] ) {
        if ( $aaparser_config['grabbing']['author_name'] ) {
            $author = $aaparser_config['grabbing']['author_name'];
        }
        else {
            $avtr = $db->super_query(" SELECT name, user_id FROM " . PREFIX . "_users WHERE user_id=1 ");
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

	    $rowz = $db->super_query(" SELECT * FROM " . PREFIX . "_images WHERE news_id='{$id_news}' AND author='{$author}' ");
	
	    if( $rowz['id'] ) {
		    if ($rowz['images']) {
		    	$db->query(" UPDATE " . PREFIX . "_images SET images=CONCAT(images, '|||', '".$images."') WHERE id='{$rowz['id']}'");
		    } else {
		    	$db->query(" UPDATE " . PREFIX . "_images SET images='{$images}' WHERE id='{$rowz['id']}'");
		    }
		
	    } else {
		    $db->query(" INSERT INTO " . PREFIX . "_images (images, news_id, author, date) VALUES ('{$images}', '0', '{$author}', '".time()."') ");
	    }
    }
    
    if ( $its_camrip === true && $aaparser_config['fields']['xf_camrip'] ) {
        $array_data2['is_camrip'] = 1;
        $array_data2['is_camrip_field'] = $aaparser_config['fields']['xf_camrip'];
    }
    else {
        $array_data2['is_camrip'] = '';
        $array_data2['is_camrip_field'] = '';
    }
    
    if ( $its_lgbt === true && $aaparser_config['fields']['xf_lgbt'] ) {
        $array_data2['is_lgbt'] = 1;
        $array_data2['is_lgbt_field'] = $aaparser_config['fields']['xf_lgbt'];
    }
    else {
        $array_data2['is_lgbt'] = '';
        $array_data2['is_lgbt_field'] = '';
    }
    
    if ($array_data2){

        die(json_encode(array(
            'status' => 'paste',
            'result' => $array_data2,
        ), JSON_UNESCAPED_UNICODE));

    } else {

        die(json_encode(array(
            'status' => 'error',
            'error' => '#02',
        )));

    }

}
else {

    die('Hacking attempt!');

}

?>