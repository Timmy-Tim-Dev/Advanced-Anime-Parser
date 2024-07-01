<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

    if ( !$aaparser_config['update_news']['xf_check'] ) die('Обновление доп. полей отключено в настройках');
    elseif ( $aaparser_config['update_news']['xf_check'] == 1 ) {
        $will_check = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE news_id>0 AND news_update=1 LIMIT 1" );
        if ( $will_check ) {
            $news_row = $db->super_query( "SELECT id, xfields, title FROM " . PREFIX . "_post WHERE id='{$will_check['news_id']}'" );
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
                
                $old_xfields = xfieldsdataload($news_row['xfields']);
                
                foreach ( $xfields_list as $check_xf_name => $check_xf_data ) {
                    if ( isset($aaparser_config['updates']['xf_translation_last_names']) && $check_xf_name == $aaparser_config['updates']['xf_translation_last_names'] ) continue;
                    if ( $xfields_list[$check_xf_name] && !$old_xfields[$check_xf_name] ) $old_xfields[$check_xf_name] = $xfields_list[$check_xf_name];
                }
                
                foreach ( $old_xfields as $check_xf_named => $check_xf_dated ) {
                    if ( mb_strpos( $check_xf_dated, '{' ) !== false ) unset($old_xfields[$check_xf_named]);
                }
                
                $new_xfields = xfieldsdatasaved($old_xfields);
	            $new_xfields = $db->safesql( $new_xfields );
	            
	            $db->query("UPDATE " . PREFIX . "_post SET xfields='{$new_xfields}' WHERE id='{$news_row['id']}'");
	            $db->query("DELETE FROM " . PREFIX . "_xfsearch WHERE news_id='{$news_row['id']}'");
	            
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
			
			            if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $temp_array[] = "('" . $news_row['id'] . "', '" . $value[0] . "', '" . $value[1] . "', '" . $value[2] . "')";
			            else $temp_array[] = "('" . $news_row['id'] . "', '" . $value[0] . "', '" . $value[1] . "')";
		            }
		
		            $xf_search_words = implode( ", ", $temp_array );
		            if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue, tagvalue_translit) VALUES " . $xf_search_words );
		            else $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
	            }
	            
	            clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats', 'full_') );
                echo ('Обновили доп. поля в новости '.$news_row['title'].'<br>');
            }
            $db->query("UPDATE " . PREFIX . "_anime_list SET news_update=0 WHERE material_id='{$will_check['material_id']}'");
            clear_cache( array('news_', 'full_') );
            die('Проверка на обновление доп полей завершена');
        } else die('В очереди на обновление данных в доп полях нет новостей');
    } elseif ( $aaparser_config['update_news']['xf_check'] == 2 ) {
        $will_check = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE news_id>0 AND news_update=1 LIMIT 1" );
        if ( $will_check ) {
            $news_row = $db->super_query( "SELECT id, xfields, title FROM " . PREFIX . "_post WHERE id='{$will_check['news_id']}'" );
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
	            
	            $black_list_xfields_arr = ['image', 'kadr_1', 'kadr_2', 'kadr_3', 'kadr_4', 'kadr_5'];
	
	            foreach ( $xfields_data as $tag_name => $tag_list ) {
	                if ( !$xfields_data[$tag_name] || in_array($tag_name, $black_list_xfields_arr) ) $xfields_data[$tag_name] = '';
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
                if ( $its_lgbt === true && $aaparser_config['fields']['xf_lgbt'] )$xfields_list[$aaparser_config['fields']['xf_lgbt']] = 1;
	            if ( $shiki_id && $aaparser_config['main_fields']['xf_shikimori_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_shikimori_id']] = $shiki_id;
	            if ( $mdl_id && $aaparser_config['main_fields']['xf_mdl_id'] ) $xfields_list[$aaparser_config['main_fields']['xf_mdl_id']] = $mdl_id;
                
                $old_xfields = xfieldsdataload($news_row['xfields']);
                
                $black_list_xfields = explode(',', $aaparser_config['update_news']['not_xfields']);
                
                foreach ( $xfields_list as $check_xf_name => $check_xf_data ) {
                    if ( isset($aaparser_config['updates']['xf_translation_last_names']) && $check_xf_name == $aaparser_config['updates']['xf_translation_last_names'] ) continue;
                    if ( $xfields_list[$check_xf_name] && !in_array($check_xf_name, $black_list_xfields) ) $old_xfields[$check_xf_name] = $xfields_list[$check_xf_name];
                }
                
                $new_xfields = xfieldsdatasaved($old_xfields);
	            $new_xfields = $db->safesql( $new_xfields );
	            
	            $db->query("UPDATE " . PREFIX . "_post SET xfields='{$new_xfields}' WHERE id='{$news_row['id']}'");
	            $db->query("DELETE FROM " . PREFIX . "_xfsearch WHERE news_id='{$news_row['id']}'");
	            
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
			
			            if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $temp_array[] = "('" . $news_row['id'] . "', '" . $value[0] . "', '" . $value[1] . "', '" . $value[2] . "')";
			            else $temp_array[] = "('" . $news_row['id'] . "', '" . $value[0] . "', '" . $value[1] . "')";
		            }
		
		            $xf_search_words = implode( ", ", $temp_array );
		            if ( $aaparser_config['integration']['latin_xfields'] == 1 ) $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue, tagvalue_translit) VALUES " . $xf_search_words );
		            else $db->query( "INSERT INTO " . PREFIX . "_xfsearch (news_id, tagname, tagvalue) VALUES " . $xf_search_words );
	            }
	            
	            clear_cache( array('news_', 'tagscloud_', 'archives_', 'calendar_', 'topnews_', 'rss', 'stats', 'full_') );
                echo ('Обновили доп. поля в новости '.$news_row['title'].'<br>');
            }
            $db->query("UPDATE " . PREFIX . "_anime_list SET news_update=0 WHERE material_id='{$will_check['material_id']}'");
            clear_cache( array('news_', 'full_') );
            die('Проверка на обновление доп полей завершена');
        } else die('В очереди на обновление данных в доп полях нет новостей');
    }