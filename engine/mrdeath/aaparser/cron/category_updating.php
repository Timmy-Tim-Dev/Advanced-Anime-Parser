<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
	$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';
	$kodik_api_domain = isset($aaparser_config['settings']['kodik_api_domain']) ? $aaparser_config['settings']['kodik_api_domain'] : 'https://kodikapi.com/';
	$shikimori_api_domain = isset($aaparser_config['settings']['shikimori_api_domain']) ? $aaparser_config['settings']['shikimori_api_domain'] : 'https://shikimori.one/';

    if ( !$aaparser_config['update_news']['cat_check'] ) die('Обновление категорий отключено в настройках');
    elseif ( $aaparser_config['update_news']['cat_check'] == 1 ) {
        $will_check = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE news_id>0 AND cat_check=1 LIMIT 1" );
        if ( $will_check ) {
            $flipped_cats = array_flip($aaparser_config['categories']);
            $news_row = $db->super_query( "SELECT id, category, title FROM " . PREFIX . "_post WHERE id='{$will_check['news_id']}'" );
            if ( $news_row['id'] == $will_check['news_id'] ) {
                $old_cats = explode(',', $news_row['category']);
                if ( $will_check['shikimori_id'] ) {
                    $shikimori = request($shikimori_api_domain.'api/animes/'.$will_check['shikimori_id']);
                    if ( $shikimori['status'] ) {
                        $status_type = array( 'anons' => 'Анонс', 'ongoing' => 'Онгоинг', 'released' => 'Завершён' );
                        $shikimori_status = $status_type[$shikimori['status']];
                        $will_be_updated = false;
                        if ( !in_array($flipped_cats[$shikimori_status], $old_cats) ) {
                            $will_be_updated = true;
                            foreach ( $old_cats as $key => $cat_id ) {
                                if ( $cat_id == $flipped_cats['Анонс'] ) unset($old_cats[$key]);
                                if ( $cat_id == $flipped_cats['Онгоинг'] ) unset($old_cats[$key]);
                                if ( $cat_id == $flipped_cats['Завершён'] ) unset($old_cats[$key]);
                            }
                            array_push($old_cats, $flipped_cats[$shikimori_status]);
                        }
                        if ( $will_be_updated === true ) {
                            $db->query("DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id='{$news_row['id']}'");
		                    $cat_ids = array ();
		                    foreach ( $old_cats as $value ) {
			                    $cat_ids[] = "('" . $news_row['id'] . "', '" . trim( $value ) . "')";
		                    }
		                    $cat_ids = implode( ", ", $cat_ids );
	                	    $db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
	                	    $new_cats = implode(',', $old_cats);
	                	    $db->query("UPDATE " . PREFIX . "_post SET category='{$new_cats}' WHERE id='{$news_row['id']}'");
	                	    clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats', 'full_') );
	                	    echo ('Обновили категории-статусы в аниме '.$news_row['title'].'. Новый статус аниме - '.$shikimori_status.'<br>');
	                	    $db->query("UPDATE " . PREFIX . "_anime_list SET tv_status='{$shikimori['status']}' WHERE material_id='{$will_check['material_id']}'");
                        }
                    }
                } elseif ( $will_check['mdl_id'] ) {
                    $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&mdl_id='.$will_check['mdl_id'].'&with_material_data=true');
                    if ( $kodik['results'][0]['material_data']['all_status'] ) {
                        $status_type = array( 'anons' => 'Анонс', 'ongoing' => 'Онгоинг', 'released' => 'Завершён' );
                        $kodik_status = $status_type[$kodik['results'][0]['material_data']['all_status']];
                        $will_be_updated = false;
                        if ( !in_array($flipped_cats[$kodik_status], $old_cats) ) {
                            $will_be_updated = true;
                            foreach ( $old_cats as $key => $cat_id ) {
                                if ( $cat_id == $flipped_cats['Анонс'] ) unset($old_cats[$key]);
                                if ( $cat_id == $flipped_cats['Онгоинг'] ) unset($old_cats[$key]);
                                if ( $cat_id == $flipped_cats['Завершён'] ) unset($old_cats[$key]);
                            }
                            array_push($old_cats, $flipped_cats[$kodik_status]);
                        }
                        if ( $will_be_updated === true ) {
                            $db->query("DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id='{$news_row['id']}'");
		                    $cat_ids = array ();
		                    foreach ( $old_cats as $value ) {
			                    $cat_ids[] = "('" . $news_row['id'] . "', '" . trim( $value ) . "')";
		                    }
		                    $cat_ids = implode( ", ", $cat_ids );
	                	    $db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
	                	    $new_cats = implode(',', $old_cats);
	                	    $db->query("UPDATE " . PREFIX . "_post SET category='{$new_cats}' WHERE id='{$news_row['id']}'");
	                	    clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats', 'full_') );
	                	    echo ('Обновили категории-статусы в новости '.$news_row['title'].'. Новый статус - '.$kodik_status.'<br>');
	                	    $db->query("UPDATE " . PREFIX . "_anime_list SET tv_status='{$kodik['results'][0]['material_data']['all_status']}' WHERE material_id='{$will_check['material_id']}'");
                        }
                    }
                }
            }
            $db->query("UPDATE " . PREFIX . "_anime_list SET cat_check=0 WHERE material_id='{$will_check['material_id']}'");
            clear_cache( array('news_', 'full_') );
            die('Проверка на обновление категорий-статусов завершена');
        } else die('У всех новостей актуальные категории');
    } elseif ( $aaparser_config['update_news']['cat_check'] == 2 ) {
        $will_check = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE news_id>0 AND cat_check=1 LIMIT 1" );
        if ( $will_check ) {
            $news_row = $db->super_query( "SELECT id, category, title FROM " . PREFIX . "_post WHERE id='{$will_check['news_id']}'" );
            if ( $news_row['id'] == $will_check['news_id'] ) {
                
                if ( $will_check['shikimori_id'] ) $shiki_id = $will_check['shikimori_id'];
                else $shiki_id = '';
                if ( $will_check['mdl_id'] ) $mdl_id = $will_check['mdl_id'];
                else $mdl_id = '';
                
                $parse_action = 'parse';
                if ( $aaparser_config['settings']['working_mode'] == 1 ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	            else {
                    if ( $shiki_id ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/shikimori.php'));
	                include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
	                if ( $aaparser_config['settings']['parse_wa'] == 1 && $shiki_id ) include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/world_art.php'));
	            }
                
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
    if ( $xfields_data['kodik_genres'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_genres'])));
    
    if ( $xfields_data['worldart_tags'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['worldart_tags'])));
    
    if ( $xfields_data['kodik_translation'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_translation'])));
    
	if ( $xfields_data['kodik_translation_types_ru'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $xfields_data['kodik_translation_types_ru'])));
	
	if ( $xfields_data['shikimori_tv_length'] ) $tags_array[] = $xfields_data['shikimori_tv_length'];
	elseif ( $xfields_data['kodik_tv_length'] ) $tags_array[] = $xfields_data['kodik_tv_length'];
	
	if ( $xfields_data['shikimori_duration_length'] ) $tags_array[] = $xfields_data['shikimori_duration_length'];
	elseif ( $xfields_data['kodik_duration_length'] ) $tags_array[] = $xfields_data['kodik_duration_length'];
	
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
		
		            $parse_cat_list = implode(",", $parse_cat_list);
		            
		            $db->query("UPDATE " . PREFIX . "_post SET category='{$parse_cat_list}' WHERE id='{$news_row['id']}'");
	                clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats', 'full_') );
	                
	                $db->query("DELETE FROM " . PREFIX . "_post_extras_cats WHERE news_id='{$news_row['id']}'");
		            $cat_ids = array ();
		            $old_cats = explode(',', $parse_cat_list);
		            foreach ( $old_cats as $value ) {
			           $cat_ids[] = "('" . $news_row['id'] . "', '" . trim( $value ) . "')";
		            }
		            $cat_ids = implode( ", ", $cat_ids );
	                $db->query( "INSERT INTO " . PREFIX . "_post_extras_cats (news_id, cat_id) VALUES " . $cat_ids );
	                echo ('Обновили все категории в новости '.$news_row['title'].'<br>');
	            }
            }
            $db->query("UPDATE " . PREFIX . "_anime_list SET cat_check=0 WHERE material_id='{$will_check['material_id']}'");
            clear_cache( array('news_', 'full_') );
            die('Готово');
        } else die('У всех новостей актуальные категории');
    }