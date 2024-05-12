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

$kodik_cat_type = [
    'foreign-movie'=>'Дорама фильм',
    'anime'=>'Аниме фильм',
    'foreign-serial'=>'Дорама сериал',
    'anime-serial'=>'Аниме сериал',
];

$status_type = [
    'anons' => 'Анонс',
    'ongoing' => 'Онгоинг',
    'released' => 'Завершён'
];

$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';

if ($parse_action == 'search') {
    
	if ( isset( $aaparser_config['settings']['working_mode'] ) && $aaparser_config['settings']['working_mode'] == 1 ) $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&title='.$search_name.'&types=foreign-movie,foreign-serial&with_material_data=true&limit=50');
	else $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&title='.$search_name.'&with_material_data=true&limit=50');
	
	if ( $kodik['results'] ) {
		foreach ( $kodik['results'] as $result ) {
		    
		    if ( !isset($result['shikimori_id']) && !isset($result['mdl_id']) ) continue;
		    
		    $kind = $kodik_cat_type[$result['type']];
			$year = isset($result['year']) ? $result['year'] : '';
            
            $shikimori_id = isset($result['shikimori_id']) ? $result['shikimori_id'] : 0;
            $shiki_link = isset($result['shikimori_id']) ? $shikimori_api_domain.'animes/'.$result['shikimori_id'] : '';
            $mydramalist_id = isset($result['mdl_id']) ? $result['mdl_id'] : 0;
            
            $where = [];
            
            if ( $aaparser_config_push['main_fields']['xf_shikimori_id'] && $shikimori_id ) $where[] = "xfields LIKE '%".$aaparser_config_push['main_fields']['xf_shikimori_id']."|".$shikimori_id."||%'";
            if ( $aaparser_config_push['main_fields']['xf_mdl_id'] && $mydramalist_id ) $where[] = "xfields LIKE '%".$aaparser_config_push['main_fields']['xf_mdl_id']."|".$mydramalist_id."||%'";
			
			if ( $where ) {
			    $where = implode(' OR ', $where);
                $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
			}
			
	    	if (isset($proverka['id']) && $proverka['id']) {
	    	    $find_id = 'est';
	    	    $edit_link = $config['http_home_url'].'admin.php?mod=editnews&action=editnews&id='.$proverka['id'];
	    	}
	    	else {
	    	    $find_id = 'net';
	    	    $edit_link = '';
	    	}
	    	
	    	$unique_id = isset($result['shikimori_id']) ? $result['shikimori_id'] : $result['mdl_id'];
	    	$rutitle = isset($result['title']) ? $result['title'] : '';
	    	$entitle = isset($result['title_orig']) ? $result['title_orig'] : '';
	    	$status = isset($result['status']) ? $status_type[$result['status']] : '';
	    	
	    	$kodik_exists = isset($result['id']) ? $result['id'] : '';
	    	$last_episode = isset($result['last_episode']) ? $result['last_episode'] : '';
	    	$last_season = isset($result['last_season']) ? $result['last_season'] : '';
            
            $responseArray[] = array(
				'kind' => $kind,
				'status' => $status,
				'shiki_id' => $shikimori_id,
				'shiki_link' => $shiki_link,
				'mdl_id' => $mydramalist_id,
				'unique_id' => $unique_id,
				'title' => $rutitle,
				'orig_title' => $entitle,
				'year' => $year,
				'find_id' => $find_id,
				'kodik_exists' => $kodik_exists,
				'last_episode' => $last_episode,
				'last_season' => $last_season,
				'edit_link' => $edit_link
			);
        }
	}
}
elseif ($parse_action == 'parse') {
    
    if ( !isset($xfields_data) && !$xfields_data ) $xfields_data = [];
    
	if ( $shiki_id )  $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&shikimori_id='.$shiki_id.'&with_episodes=true&with_material_data=true');
    elseif ( $mdl_id )  $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&mdl_id='.$mdl_id.'&with_episodes=true&with_material_data=true');
	
	if ( isset( $kodik['results'] ) && $kodik['results'] && isset( $kodik['results'][0]['seasons'] ) && $kodik['results'][0]['seasons'] ) {
	    
        $playlist = $playlist_alt = $translators_list = $translators_types = [];

		$last_season = $last_tanslated_season = $last_subtitled_season = $last_autosubtitled_season = 0;
        foreach ($kodik['results'] as $num => $translators) {
            foreach ( $translators['seasons'] as $season => $episode ) {
                foreach ( $episode['episodes'] as $ep_num => $episode_links ) {
                    $playlist[$season]['episodes'][$ep_num] = $ep_num;
					if ($translators['translation']['id'] == '1858' && $translators['translation']['type'] == 'subtitles') {
						$playlist_alt['autosubtitles'][$season]['episodes'][$ep_num] = $ep_num;
					} elseif ($translators['translation']['id'] != '1858' && $translators['translation']['type'] == 'subtitles') {
						$playlist_alt['subtitles'][$season]['episodes'][$ep_num] = $ep_num;
					} elseif ($translators['translation']['type'] == 'voice') {
						$playlist_alt['voice'][$season]['episodes'][$ep_num] = $ep_num;
					}
					$translators_list[] = trim($translators['translation']['title']);
					$translators_types[] = trim($translators['translation']['type']);
					if ( $season > $last_season ) $last_season = $season;
					if ($translators['translation']['type'] == 'voice' && $season > $last_tanslated_season) {
					$last_tanslated_season = $season;
					} elseif ($translators['translation']['type'] == 'subtitles' && $season > $last_subtitled_season) {
						// Проверяем id для определения autosubtitles
						if ($translators['translation']['id'] == '1858') {
							$last_autosubtitled_season = $season;
						} else {
							$last_subtitled_season = $season;
						}
					}
				}
            }
        }
		$translators_types = array_unique($translators_types);
		$translators_list = array_unique($translators_list);
        unset($translators);
        unset($episode);
    }
	elseif ( isset( $kodik['results'] ) && $kodik['results'] ) {
	    
		$translators_list = $translators_types = [];
		
        foreach ($kodik['results'] as $num => $translators) {
            $translators_list[] = trim($translators['translation']['title']);
			$translators_types[] = trim($translators['translation']['type']);
        }
		$translators_types = array_unique($translators_types);
		$translators_list = array_unique($translators_list);
        unset($translators);
	}
	
	$kodik_data = array_shift($kodik['results']);
	
	$its_camrip = $kodik_data['camrip'] ? true : false;
	$its_lgbt = $kodik_data['lgbt'] ? true : false;
	
	$xfields_data['kodik_title'] = isset($kodik_data['title']) ? $kodik_data['title'] : '';
	$xfields_data['kodik_title_orig'] = isset($kodik_data['title_orig']) ? $kodik_data['title_orig'] : '';
	$xfields_data['kodik_other_title'] = isset($kodik_data['other_title']) ? $kodik_data['other_title'] : '';
	$xfields_data['kodik_year'] = isset($kodik_data['year']) ? $kodik_data['year'] : '';
	$xfields_data['kodik_worldart_link'] = isset($kodik_data['worldart_link']) ? $kodik_data['worldart_link'] : '';
	$xfields_data['kodik_mydramalist_tags'] = isset($kodik_data['material_data']['mydramalist_tags']) ? implode(', ', $kodik_data['material_data']['mydramalist_tags']) : '';
	if ( $xfields_data['kodik_mydramalist_tags'] && isset($aaparser_config['settings']['tags_tolower']) && $aaparser_config['settings']['tags_tolower'] == 1 ) $xfields_data['kodik_mydramalist_tags'] = mb_strtolower($xfields_data['kodik_mydramalist_tags'], 'UTF-8');
	if ( $xfields_data['kodik_mydramalist_tags'] && isset($aaparser_config['settings']['translate_tags']) && $aaparser_config['settings']['translate_tags'] == 1 ) {
	    require_once ENGINE_DIR . '/mrdeath/aaparser/functions/GoogleTranslateForFree.php';
	    $tr = new GoogleTranslateForFree();
	    $tranlsted_mdl_tags = $tr->translate('en', 'ru', $xfields_data['kodik_mydramalist_tags'], 5);
	    if ( $tranlsted_mdl_tags ) {
	        $xfields_data['kodik_mydramalist_tags'] = $tranlsted_mdl_tags;
	        if ( isset($aaparser_config['settings']['translate_tags_tolower']) && $aaparser_config['settings']['translate_tags_tolower'] == 1 ) $xfields_data['kodik_mydramalist_tags'] = mb_strtolower($xfields_data['kodik_mydramalist_tags'], 'UTF-8');
	    }
	}
	$xfields_data['kodik_status_en'] = isset($kodik_data['material_data']['all_status']) ? $kodik_data['material_data']['all_status'] : '';
	$xfields_data['kodik_status_ru'] = isset($kodik_data['material_data']['all_status']) ? $status_type[$kodik_data['material_data']['all_status']] : '';
	
	if ( isset($kodik_data['material_data']['premiere_ru']) && $kodik_data['material_data']['premiere_ru'] ) {
        $xfields_data['kodik_premiere_ru'] = $kodik_data['material_data']['premiere_ru'];
        $xfields_data['kodik_premiere_ru_2'] = convert_date($kodik_data['material_data']['premiere_ru'], 1);
        $xfields_data['kodik_premiere_ru_3'] = convert_date($kodik_data['material_data']['premiere_ru'], 2);
        $xfields_data['kodik_premiere_ru_4'] = convert_date($kodik_data['material_data']['premiere_ru'], 3);
    }

    if ( isset($kodik_data['material_data']['premiere_world']) && $kodik_data['material_data']['premiere_world'] ) {
        $xfields_data['kodik_premiere_world'] = $kodik_data['material_data']['premiere_world'];
        $xfields_data['kodik_premiere_world_2'] = convert_date($kodik_data['material_data']['premiere_world'], 1);
        $xfields_data['kodik_premiere_world_3'] = convert_date($kodik_data['material_data']['premiere_world'], 2);
        $xfields_data['kodik_premiere_world_4'] = convert_date($kodik_data['material_data']['premiere_world'], 3);
    }
	
	$xfields_data['kodik_iframe'] = isset($kodik_data['link']) ? $kodik_data['link'] : '';
	$xfields_data['kodik_quality'] = isset($kodik_data['quality']) ? $kodik_data['quality'] : '';
	$xfields_data['kodik_kinopoisk_id'] = isset($kodik_data['kinopoisk_id']) ? $kodik_data['kinopoisk_id'] : '';
	$xfields_data['kodik_imdb_id'] = isset($kodik_data['imdb_id']) ? $kodik_data['imdb_id'] : '';
	$xfields_data['mydramalist_id'] = isset($kodik_data['mdl_id']) ? $kodik_data['mdl_id'] : '';
	$xfields_data['kodik_translation'] = isset($translators_list) ? implode(', ', $translators_list) : '';
	$xfields_data['kodik_translation_last'] = isset($kodik_data['translation']['title']) ? trim($kodik_data['translation']['title']) : '';
	$xfields_data['kodik_translation_types'] = isset($translators_types) ? implode(', ', $translators_types) : '';
	$xfields_data['kodik_translation_types_ru'] = isset($translators_types) ? str_replace( array('voice', 'subtitles'), array('Озвучка', 'Субтитры'), $xfields_data['kodik_translation_types'] ) : '';
	$xfields_data['kodik_tagline'] = isset($kodik_data['material_data']['tagline']) ? $kodik_data['material_data']['tagline'] : '';
	$xfields_data['kodik_plot'] = isset($kodik_data['material_data']['description']) ? $kodik_data['material_data']['description'] : '';
	if ( isset($kodik_data['material_data']['duration']) && $kodik_data['material_data']['duration'] ) {
        $xfields_data['kodik_duration'] = $kodik_data['material_data']['duration'];
        $xfields_data['kodik_duration_2'] = convert_duration($kodik_data['material_data']['duration'], 1);
        $xfields_data['kodik_duration_3'] = convert_duration($kodik_data['material_data']['duration'], 2);
        $xfields_data['kodik_duration_4'] = convert_duration($kodik_data['material_data']['duration'], 3);
        if ( $kodik_data['type'] == 'anime' && $kodik_data['material_data']['duration'] < 30 ) $movie_kind = 'Короткометражный фильм';
        elseif ( $kodik_data['type'] == 'anime' && $kodik_data['material_data']['duration'] >= 30 ) $movie_kind = 'Полнометражный фильм';
    }
    else $movie_kind = '';
    if ( $kodik_data['type'] == 'anime' || $kodik_data['type'] == 'foreign-movie' ) $xfields_data['kodik_video_type'] = 'фильм';
    else $xfields_data['kodik_video_type'] = 'сериал';
	$xfields_data['kodik_countries'] = isset($kodik_data['material_data']['countries']) ? implode(', ', $kodik_data['material_data']['countries']) : '';
	if ( isset($kodik_data['material_data']['anime_genres']) ) $xfields_data['kodik_genres'] = implode(', ', RenameGenres($kodik_data['material_data']['anime_genres']));
	elseif ( isset($kodik_data['material_data']['drama_genres']) ) $xfields_data['kodik_genres'] = implode(', ', RenameGenres($kodik_data['material_data']['drama_genres']));
	else $xfields_data['kodik_genres'] = '';
	
	$xfields_data['kodik_kinopoisk_rating'] = isset($kodik_data['material_data']['kinopoisk_rating']) ? $kodik_data['material_data']['kinopoisk_rating'] : '';
	$xfields_data['kodik_kinopoisk_votes'] = isset($kodik_data['material_data']['kinopoisk_votes']) ? $kodik_data['material_data']['kinopoisk_votes'] : '';
	$xfields_data['kodik_imdb_rating'] = isset($kodik_data['material_data']['imdb_rating']) ? $kodik_data['material_data']['imdb_rating'] : '';
	$xfields_data['kodik_imdb_votes'] = isset($kodik_data['material_data']['imdb_votes']) ? $kodik_data['material_data']['imdb_votes'] : '';
	$xfields_data['kodik_mydramalist_rating'] = isset($kodik_data['material_data']['mydramalist_rating']) ? $kodik_data['material_data']['mydramalist_rating'] : '';
	$xfields_data['kodik_mydramalist_votes'] = isset($kodik_data['material_data']['mydramalist_votes']) ? $kodik_data['material_data']['mydramalist_votes'] : '';
	$xfields_data['kodik_minimal_age'] = isset($kodik_data['material_data']['minimal_age']) ? $kodik_data['material_data']['minimal_age'] : '';
	$xfields_data['kodik_rating_mpaa'] = isset($kodik_data['material_data']['rating_mpaa']) ? $kodik_data['material_data']['rating_mpaa'] : '';
	
	if ( isset($kodik_data['material_data']['actors']) ) {
	    if ( isset($aaparser_config['settings']['max_actors']) && $aaparser_config['settings']['max_actors'] > 0 ) $kodik_data['material_data']['actors'] = array_slice($kodik_data['material_data']['actors'], 0, $aaparser_config['settings']['max_actors']);
	    $xfields_data['kodik_actors'] = implode(', ', $kodik_data['material_data']['actors']);
	}
	if ( isset($kodik_data['material_data']['directors']) ) {
	    if ( isset($aaparser_config['settings']['max_directors']) && $aaparser_config['settings']['max_directors'] > 0 ) $kodik_data['material_data']['directors'] = array_slice($kodik_data['material_data']['directors'], 0, $aaparser_config['settings']['max_directors']);
	    $xfields_data['kodik_directors'] = implode(', ', $kodik_data['material_data']['directors']);
	}
	if ( isset($kodik_data['material_data']['producers']) ) {
	    if ( isset($aaparser_config['settings']['max_producers']) && $aaparser_config['settings']['max_producers'] > 0 ) $kodik_data['material_data']['producers'] = array_slice($kodik_data['material_data']['producers'], 0, $aaparser_config['settings']['max_producers']);
	    $xfields_data['kodik_producers'] = implode(', ', $kodik_data['material_data']['producers']);
	}
	if ( isset($kodik_data['material_data']['writers']) ) {
	    if ( isset($aaparser_config['settings']['max_writers']) && $aaparser_config['settings']['max_writers'] > 0 ) $kodik_data['material_data']['writers'] = array_slice($kodik_data['material_data']['writers'], 0, $aaparser_config['settings']['max_writers']);
	    $xfields_data['kodik_writers'] = implode(', ', $kodik_data['material_data']['writers']);
	}
	if ( isset($kodik_data['material_data']['composers']) ) {
	    if ( isset($aaparser_config['settings']['max_composers']) && $aaparser_config['settings']['max_composers'] > 0 ) $kodik_data['material_data']['composers'] = array_slice($kodik_data['material_data']['composers'], 0, $aaparser_config['settings']['max_composers']);
	    $xfields_data['kodik_composers'] = implode(', ', $kodik_data['material_data']['composers']);
	}
	if ( isset($kodik_data['material_data']['editors']) ) {
	    if ( isset($aaparser_config['settings']['max_editors']) && $aaparser_config['settings']['max_editors'] > 0 ) $kodik_data['material_data']['editors'] = array_slice($kodik_data['material_data']['editors'], 0, $aaparser_config['settings']['max_editors']);
	    $xfields_data['kodik_editors'] = implode(', ', $kodik_data['material_data']['editors']);
	}
	if ( isset($kodik_data['material_data']['designers']) ) {
	    if ( isset($aaparser_config['settings']['max_designers']) && $aaparser_config['settings']['max_designers'] > 0 ) $kodik_data['material_data']['designers'] = array_slice($kodik_data['material_data']['designers'], 0, $aaparser_config['settings']['max_designers']);
	    $xfields_data['kodik_designers'] = implode(', ', $kodik_data['material_data']['designers']);
	}
	if ( isset($kodik_data['material_data']['operators']) ) {
	    if ( isset($aaparser_config['settings']['max_operators']) && $aaparser_config['settings']['max_operators'] > 0 ) $kodik_data['material_data']['operators'] = array_slice($kodik_data['material_data']['operators'], 0, $aaparser_config['settings']['max_operators']);
	    $xfields_data['kodik_operators'] = implode(', ', $kodik_data['material_data']['operators']);
	}
	
	if ( isset($playlist) ) {
		$last_episode = max($playlist[$last_season]['episodes']);
		$xfields_data['kodik_last_season'] = $last_season;
		$xfields_data['kodik_last_season_1'] = generate_numbers($last_season, 1);
		$xfields_data['kodik_last_season_2'] = generate_numbers($last_season, 2);
		$xfields_data['kodik_last_season_3'] = generate_numbers($last_season, 3);
		$xfields_data['kodik_last_season_4'] = generate_numbers($last_season, 4);
		$xfields_data['kodik_last_season_5'] = generate_numbers($last_season, 5);
		$xfields_data['kodik_last_season_6'] = generate_numbers($last_season, 6);
		$xfields_data['kodik_last_season_7'] = generate_numbers($last_season, 7);
		$xfields_data['kodik_last_season_8'] = generate_numbers($last_season, 8);
		$xfields_data['kodik_last_episode'] = $last_episode;
		$xfields_data['kodik_last_episode_1'] = generate_numbers($last_episode, 1);
		$xfields_data['kodik_last_episode_2'] = generate_numbers($last_episode, 2);
		$xfields_data['kodik_last_episode_3'] = generate_numbers($last_episode, 3);
		$xfields_data['kodik_last_episode_4'] = generate_numbers($last_episode, 4);
		$xfields_data['kodik_last_episode_5'] = generate_numbers($last_episode, 5);
		$xfields_data['kodik_last_episode_6'] = generate_numbers($last_episode, 6);
		$xfields_data['kodik_last_episode_7'] = generate_numbers($last_episode, 7);
		$xfields_data['kodik_last_episode_8'] = generate_numbers($last_episode, 8);
		$xfields_data['kodik_episodes_total'] = $kodik_data['material_data']['episodes_total'];
		$xfields_data['kodik_episodes_aired'] = $kodik_data['material_data']['episodes_aired'];
		if ( isset($playlist_alt['voice'][$last_tanslated_season]['episodes']) && $playlist_alt['voice'][$last_tanslated_season]['episodes'] ) {
		    $xfields_data['kodik_last_season_translated'] = $last_tanslated_season;
		    $xfields_data['kodik_last_episode_translated'] = max($playlist_alt['voice'][$last_tanslated_season]['episodes']);
		}
		if ( isset($playlist_alt['subtitles'][$last_subtitled_season]['episodes']) && $playlist_alt['subtitles'][$last_subtitled_season]['episodes'] ) {
		    $xfields_data['kodik_last_season_subtitles'] = $last_subtitled_season;
		    $xfields_data['kodik_last_episode_subtitles'] = max($playlist_alt['subtitles'][$last_subtitled_season]['episodes']);
		}
		if ( isset($playlist_alt['autosubtitles'][$last_autosubtitled_season]['episodes']) && $playlist_alt['autosubtitles'][$last_autosubtitled_season]['episodes'] ) {
		    $xfields_data['kodik_last_season_autosubtitles'] = $last_autosubtitled_season;
		    $xfields_data['kodik_last_episode_autosubtitles'] = max($playlist_alt['autosubtitles'][$last_autosubtitled_season]['episodes']);
		} else {
			$xfields_data['kodik_last_season_autosubtitles'] = '';
			$xfields_data['kodik_last_episode_autosubtitles'] = '';
		}
	}
	
	if (isset($kodik_data['episodes_count']) && $xfields_data['kodik_last_episode_translated'] != '' && $kodik_data['translation']['type'] == 'voice') {
		$xfields_data['kodik_status_en_voice'] = ($xfields_data['kodik_last_episode_translated'] >= $kodik_data['episodes_count']) ? 'released' : (($xfields_data['kodik_last_episode_translated'] < $kodik_data['episodes_count']) ? 'ongoing' : '');
		$xfields_data['kodik_status_ru_voice'] = ($xfields_data['kodik_last_episode_translated'] >= $kodik_data['episodes_count']) ? 'Завершён' : (($xfields_data['kodik_last_episode_translated'] < $kodik_data['episodes_count']) ? 'Онгоинг' : '');
	} else {
		$xfields_data['kodik_status_en_voice'] = '';
		$xfields_data['kodik_status_ru_voice'] = '';
	}
	if (isset($kodik_data['episodes_count']) && $xfields_data['kodik_last_episode_subtitles'] != '' && $kodik_data['translation']['type'] == 'subtitles' && $kodik_data['translation']['id'] != "1858") {
		$xfields_data['kodik_status_en_sub'] = ($xfields_data['kodik_last_episode_subtitles'] >= $kodik_data['episodes_count']) ? 'released' : (($xfields_data['kodik_last_episode_subtitles'] < $kodik_data['episodes_count']) ? 'ongoing' : '');
		$xfields_data['kodik_status_ru_sub'] = ($xfields_data['kodik_last_episode_subtitles'] >= $kodik_data['episodes_count']) ? 'Завершён' : (($xfields_data['kodik_last_episode_subtitles'] < $kodik_data['episodes_count']) ? 'Онгоинг' : '');
	} else {
		$xfields_data['kodik_status_en_sub'] = '';
		$xfields_data['kodik_status_ru_sub'] = '';
	}
	if (isset($kodik_data['episodes_count']) && $xfields_data['kodik_last_episode_autosubtitles'] != '' && $kodik_data['translation']['type'] == 'subtitles' && $kodik_data['translation']['id'] == "1858") {
		$xfields_data['kodik_status_en_autosub'] = ($xfields_data['kodik_last_episode_autosubtitles'] >= $kodik_data['episodes_count']) ? 'released' : (($xfields_data['kodik_last_episode_autosubtitles'] < $kodik_data['episodes_count']) ? 'ongoing' : '');
		$xfields_data['kodik_status_ru_autosub'] = ($xfields_data['kodik_last_episode_autosubtitles'] >= $kodik_data['episodes_count']) ? 'Завершён' : (($xfields_data['kodik_last_episode_autosubtitles'] < $kodik_data['episodes_count']) ? 'Онгоинг' : '');
	} else {
		$xfields_data['kodik_status_en_autosub'] = '';
		$xfields_data['kodik_status_ru_autosub'] = '';
	}
	
	if ( isset($kodik_data['screenshots']) ) {
		$xfields_data['kadr_1'] = $kodik_data['screenshots'][0];
		$xfields_data['kadr_2'] = $kodik_data['screenshots'][1];
		$xfields_data['kadr_3'] = $kodik_data['screenshots'][2];
		$xfields_data['kadr_4'] = $kodik_data['screenshots'][3];
		$xfields_data['kadr_5'] = $kodik_data['screenshots'][4];
	}
	
	if ( !isset($xfields_data['image']) && !$xfields_data['image'] && isset($kodik_data['material_data']['poster_url']) && $kodik_data['material_data']['poster_url'] ) $xfields_data['image'] = $kodik_data['material_data']['poster_url'];
  
}
elseif ( $parse_action == 'grab' && $kind == 'anime' ) {
    
    $anime_kind_add = $camrip_add = $lgbt_add = $years_add = $genres_add = $translators_add = '';
    if ( $aaparser_config['grabbing']['tv'] ) {
        if ( $anime_kind_add ) $anime_kind_add .= ',tv';
        else $anime_kind_add = '&anime_kind=tv';
    }
    if ( $aaparser_config['grabbing']['movie'] ) {
        if ( $anime_kind_add ) $anime_kind_add .= ',movie';
        else $anime_kind_add = '&anime_kind=movie';
    }
    if ( $aaparser_config['grabbing']['ova'] ) {
        if ( $anime_kind_add ) $anime_kind_add .= ',ova';
        else $anime_kind_add = '&anime_kind=ova';
    }
    if ( $aaparser_config['grabbing']['ona'] ) {
        if ( $anime_kind_add ) $anime_kind_add .= ',ona';
        else $anime_kind_add = '&anime_kind=ona';
    }
    if ( $aaparser_config['grabbing']['special'] ) {
        if ( $anime_kind_add ) $anime_kind_add .= ',special';
        else $anime_kind_add = '&anime_kind=special';
    }
    if ( $aaparser_config['grabbing']['music'] ) {
        if ( $anime_kind_add ) $anime_kind_add .= ',music';
        else $anime_kind_add = '&anime_kind=music';
    }
    if ( !$aaparser_config['grabbing']['if_camrip'] ) $camrip_add = '&camrip=false';
    if ( !$aaparser_config['grabbing']['if_lgbt'] ) $lgbt_add = '&lgbt=false';
    if ( $aaparser_config['grabbing']['years'] ) {
        $years_add = '&year='.$aaparser_config['grabbing']['years'];
    }
    if ( $aaparser_config['grabbing']['genres'] ) {
        $genres_add = '&all_genres='.$aaparser_config['grabbing']['genres'];
    }
    if ( $aaparser_config['grabbing']['translators'] ) {
        $translators_add = '&translation_id='.$aaparser_config['grabbing']['translators'];
    }
    
    if ( !$anime_kind_add ) die('Вы не выбрали ни одного типа аниме - сериал, фильм, ova, ona, спэшл или amv');

			//sort
			$film_sort_by = "";
			if ($aaparser_config['settings']['film_sort_by']) {
				$film_sort_by = '&sort='.$aaparser_config['settings']['film_sort_by'];
			}
			//
	$kodik_log = json_decode( file_get_contents( ENGINE_DIR .'/mrdeath/aaparser/data/kodik.log' ), true );
	if ( $kodik_log[$kind] ) $grab_url = $kodik_log[$kind];
	else $grab_url = $kodik_api_domain.'list?token='.$kodik_apikey.'&with_episodes=true&with_material_data=true&limit=100&types=anime,anime-serial'.$anime_kind_add.$camrip_add.$lgbt_add.$years_add.$genres_add.$translators_add;

    $grab = request($grab_url.$film_sort_by);
    
    if ( !$grab['results'] ) {
		unset($kodik_log[$kind]);
		file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/kodik.log', json_encode( $kodik_log ));
        die('Балансер Kodik временно недоступен');
    }
    else {
        $grab_info_old = '';
		$grab_info_new = '';
        $grab_num_old = 1;
        $grab_num_new = 1;
        foreach ($grab['results'] as $result) {
			
			if ( !$result['shikimori_id'] && !$result['mdl_id'] ) continue;
			
			if ( isset( $aaparser_config['blacklist_shikimori'] ) && $result['shikimori_id'] ) {
			    if ( in_array( $result['shikimori_id'], $aaparser_config['blacklist_shikimori'] ) ) continue;
			}
            
            $shikimori_id = $mdl_id = $year = $types = $serial_status = $cheking = '';
            $where = [];
            
            if ( $result['shikimori_id'] ) {
                $shikimori_id = $result['shikimori_id'];
                $where[] = "shikimori_id='".$shikimori_id."'";
            }
            
            if ( $result['mdl_id'] ) {
                $mdl_id = $result['mdl_id'];
                $where[] = "mdl_id='".$mdl_id."'";
            }
            
            $where = implode(' OR ', $where);
            if ( $result['year'] ) $year = $result['year'];
            if ( $result['material_data']['anime_kind'] ) $types = $result['material_data']['anime_kind'];
            if ( $result['material_data']['anime_status'] ) $serial_status = $result['material_data']['anime_status'];
            
            if ( $where ) {
                $cheking = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE ".$where );
                if ( $cheking['material_id'] > 0 ) {
                    if ( $cheking["tv_status"] != $serial_status ) {
                        if ( $cheking['news_id'] > 0 && $aaparser_config['update_news']['cat_check'] == 1 ) {
                            $db->query("UPDATE " . PREFIX . "_anime_list SET tv_status='{$serial_status}', cat_check=1 WHERE material_id='{$cheking['material_id']}'");
                        }
                        elseif ( $cheking['news_id'] == 0 ) {
                            $db->query("UPDATE " . PREFIX . "_anime_list SET tv_status='{$serial_status}' WHERE material_id='{$cheking['material_id']}'");
                        }
                    }
                    $grab_info_old .= $grab_num_old.'. Аниме '.$result['title'].' есть в базе модуля. Пропущено<br>';
                    $grab_num_old++;
                }
                else {
                    $db->query("INSERT INTO " . PREFIX . "_anime_list (shikimori_id, year, type, tv_status) VALUES( '{$shikimori_id}', '{$year}', '{$types}', '{$serial_status}' ) " );
                    $grab_info_new .= $grab_num_new.'. Аниме '.$result['title'].' добавлено в базу модуля<br>';
                    $grab_num_new++;
                }
                unset($cheking);
            }
            else {
                continue;
            }
        }
		
		if ( $grab['next_page'] ) $kodik_log[$kind] = $grab['next_page'];
		else unset($kodik_log[$kind]);
		file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/kodik.log', json_encode( $kodik_log ));
		if($grab_info_new != ''){
			echo 'Добавлено:<br>';
			echo $grab_info_new;
			echo '<br>';
		}
		if($grab_info_old != ''){
			echo 'Пропущено:<br>';
			echo $grab_info_old;
		}
        exit("<br>База аниме успешно обновлена!");
    }
}
elseif ( $parse_action == 'grab' && $kind == 'dorama' ) {
    
    $kind_add = $country_add = $camrip_add = $lgbt_add = $years_add = $genres_add = $translators_add = '';
    if ( $aaparser_config['grabbing_doram']['tv'] ) {
        if ( $kind_add ) $kind_add .= ',foreign-serial';
        else $kind_add = '&types=foreign-serial';
    }
    if ( $aaparser_config['grabbing_doram']['movie'] ) {
        if ( $kind_add ) $kind_add .= ',foreign-movie';
        else $kind_add = '&types=foreign-movie';
    }
    
    if ( !$kind_add ) die('Вы не выбрали в настройках модуля ни одного типа дорам - сериал или фильм');
    
    if ( $aaparser_config['grabbing_doram']['skorea'] ) {
        if ( $country_add ) $country_add .= ',Корея+Южная';
        else $country_add = '&countries=Корея+Южная';
    }
    if ( $aaparser_config['grabbing_doram']['china'] ) {
        if ( $country_add ) $country_add .= ',Китай';
        else $country_add = '&countries=Китай';
    }
    if ( $aaparser_config['grabbing_doram']['japanese'] ) {
        if ( $country_add ) $country_add .= ',Япония';
        else $country_add = '&countries=Япония';
    }
    if ( $aaparser_config['grabbing_doram']['tailand'] ) {
        if ( $country_add ) $country_add .= ',Таиланд';
        else $country_add = '&countries=Таиланд';
    }
    if ( $aaparser_config['grabbing_doram']['taivan'] ) {
        if ( $country_add ) $country_add .= ',Тайвань';
        else $country_add = '&countries=Тайвань';
    }
    if ( $aaparser_config['grabbing_doram']['phillipines'] ) {
        if ( $country_add ) $country_add .= ',Филиппины';
        else $country_add = '&countries=Филиппины';
    }
    
    if ( !$country_add ) die('Вы не выбрали в настройках модуля ни одной страны, выпускающей дорамы');

    if ( !$aaparser_config['grabbing_doram']['if_camrip'] ) $camrip_add = '&camrip=false';
    if ( !$aaparser_config['grabbing_doram']['if_lgbt'] ) $lgbt_add = '&lgbt=false';
    if ( $aaparser_config['grabbing_doram']['years'] ) {
        $years_add = '&year='.$aaparser_config['grabbing_doram']['years'];
    }
    if ( $aaparser_config['grabbing_doram']['genres'] ) {
        $genres_add = '&all_genres='.$aaparser_config['grabbing_doram']['genres'];
    }
    if ( $aaparser_config['grabbing_doram']['translators'] ) {
        $translators_add = '&translation_id='.$aaparser_config['grabbing_doram']['translators'];
    }
    
	
	$kodik_log = json_decode( file_get_contents( ENGINE_DIR .'/mrdeath/aaparser/data/kodik.log' ), true );
	if ( $kodik_log[$kind] ) $grab_url = $kodik_log[$kind];
	else $grab_url = $kodik_api_domain.'list?token='.$kodik_apikey.'&with_episodes=true&with_material_data=true&limit=100'.$kind_add.$country_add.$camrip_add.$lgbt_add.$years_add.$genres_add.$translators_add;
    
    $grab = request($grab_url);
    
    if ( !$grab['results'] ) {
		unset($kodik_log[$kind]);
		file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/kodik.log', json_encode( $kodik_log ));
        die('Балансер Kodik временно недоступен');
    }
    else {
        foreach ($grab['results'] as $result) {
			
			if ( !$result['shikimori_id'] && !$result['mdl_id'] ) continue;
			
			if ( isset( $aaparser_config['blacklist_mdl'] ) && $result['mdl_id'] ) {
			    if ( in_array( $result['mdl_id'], $aaparser_config['blacklist_mdl'] ) ) continue;
			}
            
            $shikimori_id = $mdl_id = $year = $types = $serial_status = $cheking = '';
            $where = [];
            
            if ( $result['shikimori_id'] ) {
                $shikimori_id = $result['shikimori_id'];
                $where[] = "shikimori_id='".$shikimori_id."'";
            }
            else $shikimori_id = '';
            
            if ( $result['mdl_id'] ) {
                $mdl_id = $result['mdl_id'];
                $where[] = "mdl_id='".$mdl_id."'";
            }
            else $mdl_id = '';
            
            $where = implode(' OR ', $where);
            if ( $result['year'] ) $year = $result['year'];
            if ( $result['material_data']['anime_kind'] ) $types = $result['material_data']['anime_kind'];
            if ( $result['material_data']['anime_status'] ) $serial_status = $result['material_data']['anime_status'];
            elseif ( $result['material_data']['all_status'] ) $serial_status = $result['material_data']['all_status'];
            
            if ( $where ) {
                $cheking = $db->super_query( "SELECT * FROM " . PREFIX . "_anime_list WHERE ".$where );
                if ( $cheking['material_id'] > 0 ) {
                    if ( $cheking["tv_status"] != $serial_status )
                        if ( $cheking['news_id'] > 0 && $aaparser_config['update_news']['cat_check'] == 1 ) 
                            $db->query("UPDATE " . PREFIX . "_anime_list SET tv_status='{$serial_status}', cat_check=1 WHERE material_id='{$cheking['material_id']}'");
                        elseif ( $cheking['news_id'] == 0 )
                            $db->query("UPDATE " . PREFIX . "_anime_list SET tv_status='{$serial_status}' WHERE material_id='{$cheking['material_id']}'");
                        
                }
                else
                    $db->query("INSERT INTO " . PREFIX . "_anime_list (shikimori_id, mdl_id, year, type, tv_status) VALUES( '{$shikimori_id}', '{$mdl_id}', '{$year}', '{$types}', '{$serial_status}' ) " );
            }
            else continue;
        }
		
		if ( $grab['next_page'] ) $kodik_log[$kind] = $grab['next_page'];
		else unset($kodik_log[$kind]);
		file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/kodik.log', json_encode( $kodik_log ));
		
        exit("База дорам успешно обновлена!");
    }
}