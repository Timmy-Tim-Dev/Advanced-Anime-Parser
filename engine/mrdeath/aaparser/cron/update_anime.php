<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

    if (file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/updates_history.json') && $aaparser_config_push['updates_block']['enable_history'] == 1) {
  	    $updates_history = json_decode( file_get_contents( ENGINE_DIR .'/mrdeath/aaparser/data/updates_history.json' ), true );
    } else $updates_history = false;
    
    $today_day_name = date('Y-m-d', $_TIME);

    if ( isset($aaparser_config['integration']['telegram_posting']) && $aaparser_config['integration']['telegram_posting'] == 1 && file_exists(ENGINE_DIR . "/inc/maharder/telegram/helpers/sender.php") ) {
        include_once (DLEPlugins::Check(ENGINE_DIR . "/inc/maharder/telegram/helpers/sender.php"));
        $send_to_telegram = true;
    } else $send_to_telegram = false;
    
    if ( isset($aaparser_config['integration']['social_posting']) && $aaparser_config['integration']['social_posting'] == 1 && file_exists(ENGINE_DIR.'/modules/socialposting/posting.php') ) {
        $send_to_social_posting = true;
    } else $send_to_social_posting = false;
    
    if ( isset($aaparser_config['integration']['google_indexing']) && $aaparser_config['integration']['google_indexing'] == 1 && file_exists(ENGINE_DIR.'/xoopw/indexing/init.php') ) {
        include_once (DLEPlugins::Check(ENGINE_DIR . '/xoopw/indexing/init.php'));
        $indexing = new \XOO\Indexing\Indexing($db);
        $send_to_google_indexing = true;
    } else $send_to_google_indexing = false;
	
	$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';
	
	$status_type = array( 'anons' => 'Анонс', 'ongoing' => 'Онгоинг', 'released' => 'Завершён' );
	
	$max_news = isset($aaparser_config['updates']['max_check']) ? $aaparser_config['updates']['max_check'] : 50;
	
	$kodik_updates_api = request($kodik_api_domain."list?token=".$kodik_apikey."&has_field=shikimori_id&with_episodes=true&with_material_data=true&limit=".$max_news);
	
	$updated_news_list = [];
    
    $kodik_updates = array_reverse($kodik_updates_api['results']);

    foreach ( $kodik_updates as $anime_check ) {
        
        if ( $aaparser_config_push['updates_block']['enable_history'] == 1 && $updates_history ) $need_update_history = true;
        else $need_update_history = false;
        $was_updated_history = false;
		
		$quality = $xf_shiki = '';
		if ( !$anime_check['shikimori_id'] ) continue;
		$xf_shiki = "xfields LIKE '%".$aaparser_config_push['main_fields']['xf_shikimori_id']."|".$anime_check['shikimori_id']."||%'";
		$checking_post = $db->super_query( "SELECT id, xfields, title, approve, category, date, alt_name, short_story, full_story FROM " . PREFIX . "_post WHERE ".$xf_shiki );
		if ( $checking_post['id'] > 0 ) $xfields_post = xfieldsdataload( $checking_post['xfields'] );
        else {
			unset($checking_post, $xf_shiki);
			continue;
		}

		if (in_array($checking_post['id'], $updated_news_list)) continue;
        //Очистка кастумного кеша кодик
        if ( isset($aaparser_config_push['player']['custom_cache']) && $aaparser_config_push['player']['custom_cache'] == 1 ) kodik_clear_cache('playlist_'.$checking_post['id'], 'player');
		
		$title_en = $anime_check['title_orig'];
		$title_ru = $anime_check['title'];
		if ( $anime_check['last_season'] ) $last_season_k = $anime_check['last_season'];
		else $last_season_k = 0;
		if ( $anime_check['last_episode'] ) $last_episode_k = $anime_check['last_episode'];
		else $last_episode_k = 0;
		$quality = $anime_check['quality'];
		$iframe_link = $anime_check['link'];
		
		$serial_status_k = $anime_check['material_data']['anime_status'];
		$serial_status_ru_k = $status_type[$anime_check['material_data']['anime_status']];
		
		$sh_rat = $anime_check['material_data']['shikimori_rating'];
		$sh_gol = $anime_check['material_data']['shikimori_votes'];
		
		$kp_rat = $anime_check['material_data']['kinopoisk_rating'];
		$kp_gol = $anime_check['material_data']['kinopoisk_votes'];
		
		$imdb_rat = $anime_check['material_data']['imdb_rating'];
		$imdb_gol = $anime_check['material_data']['imdb_votes'];
		
		$last_translation = trim($anime_check['translation']['title']);
		if ( $xfields_post[$aaparser_config_push['main_fields']['xf_translation']] ) {
			$old_translations = explode(', ', $xfields_post[$aaparser_config_push['main_fields']['xf_translation']]);
			if ( !in_array($last_translation, $old_translations) ) $old_translations[] = $last_translation;
			$translation = implode(', ', $old_translations);
		} else $translation = '';
		
		
		$material_row =  $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE shikimori_id={$anime_check['shikimori_id']}" );
		if ( $material_row['material_id'] && $material_row['news_id'] > 0 && $aaparser_config['update_news']['cat_check'] == 1 && $material_row['tv_status'] != $serial_status_k ) {
		    $db->query("UPDATE " . PREFIX . "_anime_list SET tv_status='{$serial_status_k}', cat_check=1 WHERE material_id='{$material_row['material_id']}'");
		    $material_row['cat_check'] = 1;
		}
		
		$need_update = $need_update_date = $send_push = 0;
        $news_id = $checking_post['id'];
		$update_fields = [];
		$reason_updation = '';
		
		//Проверка на выход новой озвучки в последней доступной серии
		
		if ( $last_episode_k > 0 && $aaparser_config_push['main_fields']['xf_series'] && $xfields_post[$aaparser_config_push['main_fields']['xf_series']] == $last_episode_k && $aaparser_config_push['main_fields']['xf_translation_last'] && $aaparser_config['updates']['xf_translation_last_names'] && $last_translation && !$aaparser_config['grabbing']['translators'] && !$aaparser_config['grabbing']['not_translators'] ) {
		    if ( $xfields_post[$aaparser_config['updates']['xf_translation_last_names']] ) $translation_last_names = explode(', ', $xfields_post[$aaparser_config['updates']['xf_translation_last_names']]);
		    else $translation_last_names = [];
		    if ( !in_array($last_translation, $translation_last_names) ) {
		        $translation_last_names[] = $last_translation;
		        $xfields_post[$aaparser_config_push['main_fields']['xf_translation_last']] = $last_translation;
		        $xfields_post[$aaparser_config['updates']['xf_translation_last_names']] = implode(', ', $translation_last_names);
                $need_update = 1;
                if ( $aaparser_config['updates']['new_translation_last'] == 1 ) $need_update_date = 1;
                $updated_news_list[] = $checking_post['id'];
                $reason_updation .= '. Добавлена озвучка '.$last_translation.' в '.$last_episode_k.' серии';
                
                if ( $aaparser_config_push['updates_block']['new_translation_history'] == 1 && $need_update_history === true && isset($updates_history) && is_array($updates_history) ) {
                    $add_updates_history = [];
                    $add_updates_history['translation'] = $last_translation;
                    $add_updates_history['episode'] = $last_episode_k;
                    $add_updates_history['season'] = $last_season_k;
                    $was_updated_history = true;
                    $need_update_history = false;
                }
                
		    }
        }
        
        //Проверка на выход нового сезона или новой серии сериала
		
		if ( $aaparser_config_push['main_fields']['xf_series'] && $aaparser_config_push['main_fields']['xf_season'] && $last_episode_k > 0 && $last_season_k > 0 ) {
            if ( $xfields_post[$aaparser_config_push['main_fields']['xf_series']] < $last_episode_k ) {
                if ( $aaparser_config['updates']['xf_translation_last_names'] ) $xfields_post[$aaparser_config['updates']['xf_translation_last_names']] = $last_translation;
                if ( $aaparser_config_push['main_fields']['xf_translation_last'] ) $xfields_post[$aaparser_config_push['main_fields']['xf_translation_last']] = $last_translation;
                $updated_news_list[] = $checking_post['id'];
                $xfields_post[$aaparser_config_push['main_fields']['xf_series']] = $last_episode_k;
                if ( $aaparser_config['updates']['xf_series_1'] ) $xfields_post[$aaparser_config['updates']['xf_series_1']] = generate_numbers($last_episode_k, 1);
                if ( $aaparser_config['updates']['xf_series_2'] ) $xfields_post[$aaparser_config['updates']['xf_series_2']] = generate_numbers($last_episode_k, 2);
                if ( $aaparser_config['updates']['xf_series_3'] ) $xfields_post[$aaparser_config['updates']['xf_series_3']] = generate_numbers($last_episode_k, 3);
                if ( $aaparser_config['updates']['xf_series_4'] ) $xfields_post[$aaparser_config['updates']['xf_series_4']] = generate_numbers($last_episode_k, 4);
                if ( $aaparser_config['updates']['xf_series_5'] ) $xfields_post[$aaparser_config['updates']['xf_series_5']] = generate_numbers($last_episode_k, 5);
                if ( $aaparser_config['updates']['xf_series_6'] ) $xfields_post[$aaparser_config['updates']['xf_series_6']] = generate_numbers($last_episode_k, 6);
                if ( $aaparser_config['updates']['xf_series_7'] ) $xfields_post[$aaparser_config['updates']['xf_series_7']] = generate_numbers($last_episode_k, 7);
                if ( $aaparser_config['updates']['xf_series_8'] ) $xfields_post[$aaparser_config['updates']['xf_series_8']] = generate_numbers($last_episode_k, 8);
                $need_update = $send_push = 1;
                if ( $aaparser_config['updates']['new_series'] == 1 ) $need_update_date = 1;
                $reason_updation .= '. Добавлена '.$last_episode_k.' серия';
                
                if ( $need_update_history === true && $was_updated_history === false && isset($updates_history) && is_array($updates_history) ) {
                    $add_updates_history = [];
                    $add_updates_history['translation'] = $last_translation;
                    $add_updates_history['episode'] = $last_episode_k;
                    $add_updates_history['season'] = $last_season_k;
                    $was_updated_history = true;
                    $need_update_history = false;
                }
                
            }
            if ( $xfields_post[$aaparser_config_push['main_fields']['xf_season']] < $last_season_k ) {
                if ( $aaparser_config['updates']['xf_translation_last_names'] ) $xfields_post[$aaparser_config['updates']['xf_translation_last_names']] = $last_translation;
                if ( $aaparser_config_push['main_fields']['xf_translation_last'] ) $xfields_post[$aaparser_config_push['main_fields']['xf_translation_last']] = $last_translation;
                $updated_news_list[] = $checking_post['id'];
                $xfields_post[$aaparser_config_push['main_fields']['xf_season']] = $last_season_k;
                if ( $aaparser_config['updates']['xf_season_1'] ) $xfields_post[$aaparser_config['updates']['xf_series_1']] = generate_numbers($last_season_k, 1);
                if ( $aaparser_config['updates']['xf_season_2'] ) $xfields_post[$aaparser_config['updates']['xf_season_2']] = generate_numbers($last_season_k, 2);
                if ( $aaparser_config['updates']['xf_season_3'] ) $xfields_post[$aaparser_config['updates']['xf_season_3']] = generate_numbers($last_season_k, 3);
                if ( $aaparser_config['updates']['xf_season_4'] ) $xfields_post[$aaparser_config['updates']['xf_season_4']] = generate_numbers($last_season_k, 4);
                if ( $aaparser_config['updates']['xf_season_5'] ) $xfields_post[$aaparser_config['updates']['xf_season_5']] = generate_numbers($last_season_k, 5);
                if ( $aaparser_config['updates']['xf_season_6'] ) $xfields_post[$aaparser_config['updates']['xf_season_6']] = generate_numbers($last_season_k, 6);
                if ( $aaparser_config['updates']['xf_season_7'] ) $xfields_post[$aaparser_config['updates']['xf_season_7']] = generate_numbers($last_season_k, 7);
                if ( $aaparser_config['updates']['xf_season_8'] ) $xfields_post[$aaparser_config['updates']['xf_season_8']] = generate_numbers($last_season_k, 8);
                $need_update = $send_push = 1;
                if ( $aaparser_config['updates']['new_series'] == 1 ) $need_update_date = 1;
                $reason_updation .= '. Добавлен '.$last_season_k.' сезон';
                
                if ( $need_update_history === true && $was_updated_history === false && isset($updates_history) && is_array($updates_history) ) {
                    $add_updates_history = [];
                    $add_updates_history['translation'] = $last_translation;
                    $add_updates_history['episode'] = $last_episode_k;
                    $add_updates_history['season'] = $last_season_k;
                    $was_updated_history = true;
                    $need_update_history = false;
                }
            }
        }
        
        //Проверка на выход нового сезона отдельно в озвучке или в субтитрах
        
        if ( ($aaparser_config['updates']['xf_season_translated'] || $aaparser_config['updates']['xf_season_subtitles']) && $anime_check['last_season'] ) {
            if ( $anime_check['translation']['type'] == 'voice' ) {
                if ( $aaparser_config['updates']['xf_season_translated'] && (!$xfields_post[$aaparser_config['updates']['xf_season_translated']] || $xfields_post[$aaparser_config['updates']['xf_season_translated']] < $anime_check['last_season'] ) ) {
                    $xfields_post[$aaparser_config['updates']['xf_season_translated']] = $anime_check['last_season'];
                    $need_update = 1;
                }
            } elseif ( $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] != "1858" ) {
                if ( $aaparser_config['updates']['xf_season_subtitles'] && (!$xfields_post[$aaparser_config['updates']['xf_season_subtitles']] || $xfields_post[$aaparser_config['updates']['xf_season_subtitles']] < $anime_check['last_season'] ) ) {
                    $xfields_post[$aaparser_config['updates']['xf_season_subtitles']] = $anime_check['last_season'];
                    $need_update = 1;
                }
            } elseif ( $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] == "1858" ) {
                if ( $aaparser_config['updates']['xf_season_autosubtitles'] && (!$xfields_post[$aaparser_config['updates']['xf_season_autosubtitles']] || $xfields_post[$aaparser_config['updates']['xf_season_autosubtitles']] < $anime_check['last_season'] ) ) {
                    $xfields_post[$aaparser_config['updates']['xf_season_autosubtitles']] = $anime_check['last_season'];
                    $need_update = 1;
                }
            }
        }
        
        //Проверка на выход новой серии отдельно в озвучке или в субтитрах
        
        if ( ($aaparser_config['updates']['xf_series_translated'] || $aaparser_config['updates']['xf_series_subtitles']) && $anime_check['last_episode'] ) {
            if ( $anime_check['translation']['type'] == 'voice' ) {
                if ( $aaparser_config['updates']['xf_series_translated'] && (!$xfields_post[$aaparser_config['updates']['xf_series_translated']] || $xfields_post[$aaparser_config['updates']['xf_series_translated']] < $anime_check['last_episode'] ) ) {
                    $xfields_post[$aaparser_config['updates']['xf_series_translated']] = $anime_check['last_episode'];
                    $need_update = 1;
                }
            } elseif ( $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] != "1858" ) {
                if ( $aaparser_config['updates']['xf_series_subtitles'] && (!$xfields_post[$aaparser_config['updates']['xf_series_subtitles']] || $xfields_post[$aaparser_config['updates']['xf_series_subtitles']] < $anime_check['last_episode'] ) ) {
                    $xfields_post[$aaparser_config['updates']['xf_series_subtitles']] = $anime_check['last_episode'];
                    $need_update = 1;
                }
            } elseif ( $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] == "1858" ) {
                if ( $aaparser_config['updates']['xf_series_autosubtitles'] && (!$xfields_post[$aaparser_config['updates']['xf_series_autosubtitles']] || $xfields_post[$aaparser_config['updates']['xf_series_autosubtitles']] < $anime_check['last_episode'] ) ) {
                    $xfields_post[$aaparser_config['updates']['xf_series_autosubtitles']] = $anime_check['last_episode'];
                    $need_update = 1;
                }
            }
        }
		
		//Проверка на изменение рейтинга и голосов
		
		if ($sh_rat && $aaparser_config['updates']['xf_rating_sh'] ) {
            if ($xfields_post[$aaparser_config['updates']['xf_rating_sh']] != $sh_rat ) {
                $xfields_post[$aaparser_config['updates']['xf_rating_sh']] = $sh_rat;
			}
        }
		if ($kp_rat && $aaparser_config['updates']['xf_rating_kp'] ) {
            if ($xfields_post[$aaparser_config['updates']['xf_rating_kp']] != $kp_rat ) {
                $xfields_post[$aaparser_config['updates']['xf_rating_kp']] = $kp_rat;
			}
        }
		if ($imdb_rat && $aaparser_config['updates']['xf_rating_imdb'] ) {
            if ($xfields_post[$aaparser_config['updates']['xf_rating_imdb']] != $imdb_rat ) {
                $xfields_post[$aaparser_config['updates']['xf_rating_imdb']] = $imdb_rat;
			}
        }
		if ($sh_gol && $aaparser_config['updates']['xf_golos_sh'] ) {
            if ($xfields_post[$aaparser_config['updates']['xf_golos_sh']] != $sh_gol ) {
                $xfields_post[$aaparser_config['updates']['xf_golos_sh']] = $sh_gol;
			}
        }
		if ($kp_gol && $aaparser_config['updates']['xf_golos_kp'] ) {
            if ($xfields_post[$aaparser_config['updates']['xf_golos_kp']] != $kp_gol ) {
                $xfields_post[$aaparser_config['updates']['xf_golos_kp']] = $kp_gol;
			}
        }
		if ($imdb_gol && $aaparser_config['updates']['xf_golos_imdb'] ) {
            if ($xfields_post[$aaparser_config['updates']['xf_golos_imdb']] != $imdb_gol ) {
                $xfields_post[$aaparser_config['updates']['xf_golos_imdb']] = $imdb_gol;
			}
        }
        
        //Проверка на изменение статуса сериала для субтитров и автосубтитров
		$status_type = array( 'anons' => 'Анонс', 'ongoing' => 'Онгоинг', 'released' => 'Завершён' );
		
		if ($aaparser_config['updates']['xf_status_sub']) {
			$status = "ongoing";
			if ($anime_check['last_episode'] >= $anime_check['material_data']['episodes_total']) $status = "released";
			if ($xfields_post[$aaparser_config['updates']['xf_status_sub']] != $status && $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] != "1858") {
				$need_update = 1;
				$xfields_post[$aaparser_config['updates']['xf_status_sub']] = $status;			
			}						
		}
		if ($aaparser_config['updates']['xf_status_ru_sub']) {
			$status = "Онгоинг";
			if ($anime_check['last_episode'] >= $anime_check['material_data']['episodes_total']) $status = "Завершён";
			if ($xfields_post[$aaparser_config['updates']['xf_status_ru_sub']] != $status && $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] != "1858") {
				$need_update = 1;
				$xfields_post[$aaparser_config['updates']['xf_status_ru_sub']] = $status;			
			}						
		}
		
		if ($aaparser_config['updates']['xf_status_autosub']) {
			$status = "ongoing";
			if ($anime_check['last_episode'] >= $anime_check['material_data']['episodes_total']) $status = "released";
			if ($xfields_post[$aaparser_config['updates']['xf_status_autosub']] != $status && $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] == "1858") {
				$need_update = 1;
				$xfields_post[$aaparser_config['updates']['xf_status_autosub']] = $status;			
			}						
		}
		if ($aaparser_config['updates']['xf_status_ru_autosub']) {
			$status = "Онгоинг";
			if ($anime_check['last_episode'] >= $anime_check['material_data']['episodes_total']) $status = "Завершён";
			if ($xfields_post[$aaparser_config['updates']['xf_status_ru_autosub']] != $status && $anime_check['translation']['type'] == 'subtitles' && $anime_check['translation']['id'] == "1858") {
				$need_update = 1;
				$xfields_post[$aaparser_config['updates']['xf_status_ru_autosub']] = $status;		
			}						
		}	
		
		if ($aaparser_config['updates']['xf_status_voice']) {
			$status = "ongoing";
			if ($anime_check['last_episode'] >= $anime_check['material_data']['episodes_total']) $status = "released";
			if ($xfields_post[$aaparser_config['updates']['xf_status_voice']] != $status && $anime_check['translation']['type'] == 'voice') {
				$need_update = 1;
				$xfields_post[$aaparser_config['updates']['xf_status_voice']] = $status;		
			}						
		}
		if ($aaparser_config['updates']['xf_status_ru_voice']) {
			$status = "Онгоинг";
			if ($anime_check['last_episode'] >= $anime_check['material_data']['episodes_total']) $status = "Завершён";
			if ($xfields_post[$aaparser_config['updates']['xf_status_ru_voice']] != $status && $anime_check['translation']['type'] == 'voice') {
				$need_update = 1;
				$xfields_post[$aaparser_config['updates']['xf_status_ru_voice']] = $status;	
			}						
		}	
		
        //Проверка на изменение статуса сериала
        
        if ( ($serial_status_k || $serial_status_ru_k) && $material_row['cat_check'] != 1 ) {
            if ( $aaparser_config['updates']['xf_status'] && $serial_status_k && $xfields_post[$aaparser_config['updates']['xf_status']] != $serial_status_k ) {
                $xfields_post[$aaparser_config['updates']['xf_status']] = $serial_status_k;
                $need_update = 1;
                if ( $aaparser_config['updates']['new_status'] == 1 ) $need_update_date = 1;
                
            }
			if ( $aaparser_config['updates']['xf_status_ru'] && $serial_status_ru_k && $xfields_post[$aaparser_config['updates']['xf_status_ru']] != $serial_status_ru_k ) {
                $xfields_post[$aaparser_config['updates']['xf_status_ru']] = $serial_status_ru_k;
                $need_update = 1;
                if ( $aaparser_config['updates']['new_status'] == 1 ) $need_update_date = 1;
                $reason_updation .= '. Изменился статус аниме на '.$serial_status_ru_k;
            }
        }
		
		//Проверка на изменение качества фильма
        
        if ( $aaparser_config_push['main_fields']['xf_quality'] && $quality && $anime_check['type'] != 'anime-serial' ) {
            if ( $xfields_post[$aaparser_config_push['main_fields']['xf_quality']] != $quality ) {
                $xfields_post[$aaparser_config_push['main_fields']['xf_quality']] = $quality;
                $need_update = 1;
                $send_push = 1;
                if ( $aaparser_config['updates']['new_quality'] == 1 ) $need_update_date = 1;
                $reason_updation .= '. Добавлено новое качество аниме фильма '.$quality;
            }
        }
        
        //Проверка на появление новой озвучки
        
        if ( $aaparser_config_push['main_fields']['xf_translation'] && $translation && !$aaparser_config['grabbing']['translators'] && !$aaparser_config['grabbing']['not_translators'] ) {
            if ( $xfields_post[$aaparser_config_push['main_fields']['xf_translation']] != $translation ) {
                $xfields_post[$aaparser_config_push['main_fields']['xf_translation']] = $translation;
                $need_update = 1;
                if ( $aaparser_config['updates']['new_translation'] == 1 ) $need_update_date = 1;
            }
        }
		
		$update_fields['title'] = $title_en;
		$update_fields['title_ru'] = $title_ru;
		if ( $last_episode_k ) {
			$update_fields['episode'] = $last_episode_k;
			$update_fields['episode_1'] = generate_numbers($last_episode_k, 1);
			$update_fields['episode_2'] = generate_numbers($last_episode_k, 2);
			$update_fields['episode_3'] = generate_numbers($last_episode_k, 3);
			$update_fields['episode_4'] = generate_numbers($last_episode_k, 4);
			$update_fields['episode_5'] = generate_numbers($last_episode_k, 5);
			$update_fields['episode_6'] = generate_numbers($last_episode_k, 6);
			$update_fields['episode_7'] = generate_numbers($last_episode_k, 7);
			$update_fields['episode_8'] = generate_numbers($last_episode_k, 8);
		} else $update_fields['episode'] = $update_fields['episode_1'] = $update_fields['episode_2'] = $update_fields['episode_3'] = $update_fields['episode_4'] = $update_fields['episode_5'] = $update_fields['episode_6'] = $update_fields['episode_7'] = $update_fields['episode_8'] = '';
		if ( $last_season_k ) {
			$update_fields['season'] = $last_season_k;
			$update_fields['season_1'] = generate_numbers($last_season_k, 1);
			$update_fields['season_2'] = generate_numbers($last_season_k, 2);
			$update_fields['season_3'] = generate_numbers($last_season_k, 3);
			$update_fields['season_4'] = generate_numbers($last_season_k, 4);
			$update_fields['season_5'] = generate_numbers($last_season_k, 5);
			$update_fields['season_6'] = generate_numbers($last_season_k, 6);
			$update_fields['season_7'] = generate_numbers($last_season_k, 7);
			$update_fields['season_8'] = generate_numbers($last_season_k, 8);
		} else $update_fields['season'] = $update_fields['season_1'] = $update_fields['season_2'] = $update_fields['season_3'] = $update_fields['season_4'] = $update_fields['season_5'] = $update_fields['season_6'] = $update_fields['season_7'] = $update_fields['season_8'] = '';
		
		if ( $serial_status_k ) $update_fields['status'] = $serial_status_k;
		else $update_fields['status'] = '';
		if ( $serial_status_ru_k ) $update_fields['status_ru'] = $serial_status_ru_k;
		else $update_fields['status_ru'] = '';
		if ( $quality ) $update_fields['quality'] = $quality;
		else $update_fields['quality'] = '';
		if ( $translation ) $update_fields['translation'] = $translation;
		else $update_fields['translation'] = '';
		if ( $translation_type ) $update_fields['translation_type'] = $translation_type;
		else $update_fields['translation_type'] = '';
		if ( $translation_type_ru ) $update_fields['translation_type_ru'] = $translation_type_ru;
		else $update_fields['translation_type_ru'] = '';
        
		$descript = "";
		// Проверка анонс
		
		if ($anime_check['material_data']['description'] !="" && $aaparser_config['settings_anons']['description_update'] == "1") { 
			$shiki_id = $anime_check['shikimori_id'];
	        $parse_action = 'parse';
			$parse_type = 'grabbing';
			
	        include  (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/shikimori.php'));
			include  (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/donors/kodik.php'));
		
			$descript =  $db->safesql( check_if( $aaparser_config['settings_anons']['descript'], $xfields_data) );
			$new_descript = $anime_check['material_data']['description'];
			if (trim(strip_tags($checking_post['short_story'])) == trim(strip_tags( $descript )) || $checking_post['short_story'] == "") $db->query("UPDATE ".PREFIX."_post set short_story='$new_descript' WHERE id=".$checking_post['id']);
			if (trim(strip_tags($checking_post['full_story'])) == trim(strip_tags( $descript )) || $checking_post['full_story'] == "") $db->query("UPDATE ".PREFIX."_post set full_story='$new_descript' WHERE id=".$checking_post['id']);
		}
				
		//
		
        if ( $need_update == 1 ) {
            
            if ( isset($aaparser_config['settings']['next_episode_date_new']) ) {
                $shikimori_temp = request($shikimori_api_domain.'api/animes/'.$anime_check['shikimori_id']);
                if ( $shikimori_temp['next_episode_at'] ) {
                    $next_episode_at = strtotime($shikimori_temp['next_episode_at']);
                    $xfields_post[$aaparser_config['settings']['next_episode_date_new']] = date("d.m.Y H:i:s", $next_episode_at);
                }
                else $xfields_post[$aaparser_config['settings']['next_episode_date_new']] = '';
                unset($shikimori_temp);
            }
            
            if ( $aaparser_config_push['main_fields']['xf_player'] && $iframe_link ) $xfields_post[$aaparser_config_push['main_fields']['xf_player']] = $iframe_link;
			if ( $aaparser_config['updates']['change_title'] == 1 && $aaparser_config['updates']['title'] ) {
				$and_title = $db->safesql( check_if($aaparser_config['updates']['title'], $update_fields) );
				$set_title = ", title='".$and_title."'";
			} else $set_title = '';
			
			if ( $aaparser_config['updates']['change_cpu'] == 1 && $aaparser_config['updates']['cpu'] ) {
				$and_cpu = $db->safesql( check_if($aaparser_config['updates']['cpu'], $update_fields) );
				$set_cpu = ", alt_name='".$and_cpu."'";
			} else $set_cpu = '';
			
			if ( $aaparser_config['updates']['change_metatitle'] == 1 && $aaparser_config['updates']['metatitle'] ) {
				$and_metatitle = $db->safesql( check_if($aaparser_config['updates']['metatitle'], $update_fields) );
				$set_metatitle = ", metatitle='".$and_metatitle."'";
			} else $set_metatitle = '';
			
			if ( $aaparser_config['updates']['change_metadescr'] == 1 && $aaparser_config['updates']['metadescr'] ) {
				$and_metadescr = $db->safesql( check_if($aaparser_config['updates']['metadescr'], $update_fields) );
				$set_metadescr = ", descr='".$and_metadescr."'";
			} else $set_metadescr = '';
			
			if ( $aaparser_config['updates']['change_metakeywords'] == 1 && $aaparser_config['updates']['metakeywords'] ) {
				$and_metakeywords = $db->safesql( check_if($aaparser_config['updates']['metakeywords'], $update_fields) );
				$set_metakeywords = ", keywords='".$and_metakeywords."'";
			} else $set_metakeywords = '';
			
			$new_time = time();
            $new_date = date( "Y-m-d H:i:s", $new_time );
	        $xfields_post = xfieldsdatasaved($xfields_post);
	        $xfields_post = $db->safesql( $xfields_post );
	        
	        if ( $need_update_date == 1 ) $db->query( "UPDATE " . PREFIX . "_post SET xfields='{$xfields_post}', date='{$new_date}' {$set_title}{$set_cpu}{$set_metatitle}{$set_metadescr}{$set_metakeywords} WHERE id='{$news_id}'" );
	        else $db->query( "UPDATE " . PREFIX . "_post SET xfields='{$xfields_post}' {$set_title}{$set_cpu}{$set_metatitle}{$set_metadescr}{$set_metakeywords} WHERE id='{$news_id}'" );
	        $db->query( "UPDATE " . PREFIX . "_post_extras SET editdate='{$new_time}' WHERE news_id='{$news_id}'" );
			if ( $need_update_date == 1 ) echo 'Обновилось аниме '.$checking_post['title'].$reason_updation.'. Новость была апнута<br>';
			else echo 'Обновилось аниме '.$checking_post['title'].$reason_updation.'<br>';
			
			if( $config['allow_alt_url'] ) {
			    if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
				    if( $checking_post['category'] and $config['seo_type'] == 2 ) {
					    $cats_url = get_url( $checking_post['category'] );
					    if($cats_url) {
						    $full_link = $config['http_home_url'] . $cats_url . "/" . $checking_post['id'] . "-" . $checking_post['alt_name'] . ".html";
					    } else $full_link = $config['http_home_url'] . $checking_post['id'] . "-" . $checking_post['alt_name'] . ".html";
				    } else $full_link = $config['http_home_url'] . $checking_post['id'] . "-" . $checking_post['alt_name'] . ".html";
			    } else $full_link = $config['http_home_url'] . date( 'Y/m/d/', strtotime( $checking_post['date'] ) ) . $checking_post['alt_name'] . ".html";
		    } else $full_link = $config['http_home_url'] . "index.php?newsid=" . $checking_post['id'];
		    
		    $xfields_data = xfieldsdataload($xfields_post);
  		    if ( $aaparser_config_push['main_fields']['xf_poster'] && $xfields_data[$aaparser_config_push['main_fields']['xf_poster']] ) {
       		    if ( strpos($xfields_data[$aaparser_config_push['main_fields']['xf_poster']], '/uploads/posts/') === false ) $image = $config['http_home_url'].'uploads/posts/'.$xfields_data[$aaparser_config_push['main_fields']['xf_poster']];
       		    else $image = $xfields_data[$aaparser_config_push['main_fields']['xf_poster']];
       		    $temp_image = explode('|', $image);
       		    $image = $temp_image[0];
    	    } elseif ( $aaparser_config_push['main_fields']['poster_empty'] ) $image = $aaparser_config_push['main_fields']['poster_empty'];
    	    else $image = '';
    	    
    	    if ( $checking_post['approve'] == 1 && $was_updated_history === true && isset($add_updates_history) && is_array($add_updates_history) ) {
    	        $add_updates_history['image'] = $image;
    	        $add_updates_history['link'] = $full_link;
    	        $add_updates_history['title'] = $checking_post['title'];
    	        $add_updates_history['time'] = $_TIME;
    	        if ( isset($updates_history[$today_day_name]) && $today_day_name ) array_unshift($updates_history[$today_day_name], $add_updates_history);
    	        else $updates_history[$today_day_name][] = $add_updates_history;
                krsort($updates_history);
    	        if ( isset($aaparser_config_push['updates_block']['count_days']) && $aaparser_config_push['updates_block']['count_days'] && count($updates_history) > $aaparser_config_push['updates_block']['count_days'] ) $updates_history = array_slice($updates_history, 0, $aaparser_config_push['updates_block']['count_days']);
    	        if ( isset($aaparser_config_push['updates_block']['count_history']) && $aaparser_config_push['updates_block']['count_history'] && count($updates_history[$today_day_name]) > $aaparser_config_push['updates_block']['count_history'] ) $updates_history[$today_day_name] = array_slice($updates_history[$today_day_name], 0, $aaparser_config_push['updates_block']['count_history']);
    	        file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/updates_history.json', json_encode( $updates_history , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    	        unset($add_updates_history);
    	    }
			
			if ( $aaparser_config_push['push_notifications']['enable'] && $checking_post['approve'] == 1 && $send_push == 1 ) {
			    $res = $db->query( "SELECT user_id, push_subscribe FROM " . PREFIX . "_users WHERE push_subscribe LIKE '%\"".$news_id."\"%'" );
	            $users_list = [];
	            while ( $user_list = $db->get_row($res) ) {
					// 11.02.24
					if ( $user_list['user_id'] ) $users_list[] = $user_list['user_id'];
					$db->query("INSERT INTO ".PREFIX."_subscribe_info set user_id='{$user_list['user_id']}', post_id='$news_id'");
	            }
  	            if ( $users_list ) {
  		            if ( $aaparser_config_push['push_notifications']['tv_title'] && $aaparser_config_push['push_notifications']['tv_text'] && $xfields_data[$aaparser_config_push['main_fields']['xf_series']] ) {
      		            $notification = str_replace( ['{episode}', '{season}', '{title}', '{translation}'], [$xfields_data[$aaparser_config_push['main_fields']['xf_series']],                   $xfields_data[$aaparser_config_push['main_fields']['xf_season']], $checking_post['title'], $xfields_data[$aaparser_config_push['main_fields']['xf_translation_last']]], $aaparser_config_push['push_notifications']['tv_text'] );
      		            DLE_Send_Push( $aaparser_config_push['push_notifications']['tv_title'], $notification, $full_link, $image, $users_list );
    	            } elseif ( $aaparser_config_push['push_notifications']['movie_title'] && $aaparser_config_push['push_notifications']['movie_text'] && $xfields_data[$aaparser_config_push['main_fields']['xf_quality']] ) {
      		            $notification = str_replace( ['{quality}', '{title}'], [$xfields_data[$aaparser_config_push['main_fields']['xf_quality']], $checking_post['title']], $aaparser_config_push['push_notifications']['movie_text'] );
      		            DLE_Send_Push( $aaparser_config_push['push_notifications']['movie_title'], $notification, $full_link, $image, $users_list );
    	            }
                }
			}
			
			if ( $send_to_telegram === true && $checking_post['approve'] == 1 ) sendTelegram($news_id, "editnews");
			
			if ( $send_to_social_posting === true && $checking_post['approve'] == 1 ) {
			    $approve = 1;
			    $id = $news_id;
                $category_list = $checking_post['category'];
                include ENGINE_DIR.'/modules/socialposting/posting.php';
			}
			
			if( $config['news_indexnow'] && $checking_post['approve'] == 1 ) $result = DLESEO::IndexNow( $full_link );
			
			if ( isset($aaparser_config_push['push_notifications']['google_indexing']) && $aaparser_config_push['push_notifications']['google_indexing'] == 1 && $checking_post['approve'] == 1 ) {
		        $indexing_action = 'send';
	            $indexing_type = 'URL_UPDATED';
	            $indexing_url = $full_link;
	            include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/google_indexing/indexing.php'));
			}
			
			if ( $send_to_google_indexing === true && $checking_post['approve'] == 1 ) {
		        $indexing->setUrls($full_link);
                $indexing->Index();
			}
			
			if ( $aaparser_config_push['push_notifications']['enable_tgposting'] == 1 && $aaparser_config_push['push_notifications']['tg_cron_modupdate'] == 1 && $checking_post['approve'] == 1 ) {
                telegram_sender($news_id, 'editnews_cron');
            }
			
        }
		unset($xfields_post, $title_en, $title_ru, $news_id, $update_fields, $checking_post, $last_season_k, $last_episode_k, $serial_status_k, $serial_status_ru_k, $quality, $translation, $translation_type, $translation_type_ru, $playlist, $translators_list, $translators_types, $need_update, $material_row);
	}
	clear_cache( array('news_', 'full_', 'kodik_playlist_') );
    die('Проверка обновлений аниме завершена');