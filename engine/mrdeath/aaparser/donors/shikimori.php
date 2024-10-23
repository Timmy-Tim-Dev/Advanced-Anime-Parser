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

$cat_type = [
    'tv' => 'ТВ-сериал',
    'movie' => 'Фильм',
    'ova' => 'OVA',
    'ona' => 'ONA',
    'special' => 'Спецвыпуск',
    'tv_special' => 'TV Спецвыпуск',
    'music' => 'Клип',
    'pv' => 'Проморолик',
    'cm' => 'Реклама',
];

$status_type = [
    'anons' => 'Анонс',
    'ongoing' => 'Онгоинг',
    'released' => 'Завершён'
    
];

$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';
if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) {
    $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
    $shikimori_image_domain = 'https://'.clean_url($shikimori_api_domain);
} else $shikimori_api_domain = $shikimori_image_domain = 'https://shikimori.me/';

if ( $parse_action == 'search' ) {
    
    $shikimori = request($shikimori_api_domain.'api/animes?limit=50&search='.$search_name);
    
    if ( $shikimori && $search_name ) {
        foreach ( $shikimori as $result ) {
            
            if ( isset($result['id']) && $result['id'] ) $id_shiki = $result['id'];
            else continue;
            
            $kind = 'Аниме. '.$cat_type[$result['kind']];
            $rutitle = isset($result['russian']) ? $result['russian'] : '';
	    	$entitle = isset($result['name']) ? $result['name'] : '';
	    	$status = isset($result['status']) ? $status_type[$result['status']] : '';
	    	
			if ( isset($result['aired_on']) && $result['aired_on'] ) {
				$date_arr = explode('-', $result['aired_on']);
				$year = $date_arr[0];
			} else $year = '';
            
            $shiki_link = isset($result['url']) ? $shikimori_image_domain.$result['url'] : '';
			
			$where = "xfields LIKE '%".$aaparser_config['main_fields']['xf_shikimori_id']."|".$id_shiki."||%'";
            $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
			
	    	if (isset($proverka['id']) && $proverka['id']) {
	    	    $find_id = 'est';
	    	    $edit_link = $config['http_home_url'].$config['admin_path'].'?mod=editnews&action=editnews&id='.$proverka['id'];
	    	} else {
	    	    $find_id = 'net';
	    	    $edit_link = '';
	    	}
	    	
	    	$kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&shikimori_id='.$id_shiki);
	    	
	    	$kodik_exists = isset($kodik['results'][0]['id']) ? $kodik['results'][0]['id'] : '';
	    	$last_episode = isset($kodik['results'][0]['last_episode']) ? $kodik['results'][0]['last_episode'] : '';
	    	$last_season = isset($kodik['results'][0]['last_season']) ? $kodik['results'][0]['last_season'] : '';
	    	
	    	$mdl_id = 0;
	    	
            $responseArray[] = array(
				'kind' => $kind,
				'status' => $status,
				'title' => $rutitle,
				'orig_title' => $entitle,
				'year' => $year,
				'last_episode' => $last_episode,
				'last_season' => $last_season,
				'mdl_id' => $mdl_id,
				'shiki_id' => $id_shiki,
				'shiki_link' => $shiki_link,
				'kodik_exists' => $kodik_exists,
				'find_id' => $find_id,
				'edit_link' => $edit_link
			);
        }
    }
    
} elseif ($parse_action == 'parse') {
    
    $xfields_data = [];
    $shikimori = request($shikimori_api_domain.'api/animes/'.$shiki_id);
	
    if ($shikimori['code'] != "404") {
      $xfields_data['shikimori_id'] = isset($shiki_id) ? $shiki_id : '';
      $xfields_data['shikimori_name'] = isset($shikimori['name']) ? $shikimori['name'] : '';
      $xfields_data['shikimori_russian'] = isset($shikimori['russian']) ? $shikimori['russian'] : '';
      $xfields_data['shikimori_english'] = (isset($shikimori['english']) && $shikimori['english']) ? implode(', ', $shikimori['english']) : '';
      $xfields_data['shikimori_japanese'] = (isset($shikimori['japanese']) && $shikimori['japanese']) ? implode(', ', $shikimori['japanese']) : '';
      $xfields_data['shikimori_synonyms'] = (isset($shikimori['synonyms']) && $shikimori['synonyms']) ? implode(', ', $shikimori['synonyms']) : '';
      $xfields_data['shikimori_license_name_ru'] = isset($shikimori['license_name_ru']) ? $shikimori['license_name_ru'] : '';
	  if ( isset($shikimori['licensors']) && $shikimori['licensors'] ) $xfields_data['shikimori_licensors'] = implode(', ', $shikimori['licensors']);
      $xfields_data['shikimori_kind'] = isset($shikimori['kind']) ? $shikimori['kind'] : '';
      $xfields_data['shikimori_kind_ru'] = isset($shikimori['kind']) ? $cat_type[$shikimori['kind']] : '';
      $xfields_data['shikimori_score'] = (isset($shikimori['score']) && $shikimori['score'] != '0.0') ? $shikimori['score'] : '';
      $shikimori_votes = 0;
      if ( isset($shikimori['rates_scores_stats']) && $shikimori['rates_scores_stats'] ) {
        foreach ( $shikimori['rates_scores_stats'] as $rates_scores_stats ) {
          $shikimori_votes = $shikimori_votes+$rates_scores_stats['value'];
        }
      }
      if ( $shikimori_votes > 0 ) $xfields_data['shikimori_votes'] = $shikimori_votes;
      else $xfields_data['shikimori_votes'] = '';
      $xfields_data['shikimori_status'] = isset($shikimori['status']) ? $shikimori['status'] : '';
      $xfields_data['shikimori_status_ru'] = isset($shikimori['status']) ? $status_type[$shikimori['status']] : '';
      $xfields_data['shikimori_episodes'] = isset($shikimori['episodes']) ? $shikimori['episodes'] : '';

      if ( isset($shikimori['episodes_aired']) && $shikimori['episodes_aired'] ) {
        $xfields_data['shikimori_episodes_aired'] = $shikimori['episodes_aired'];
        $xfields_data['shikimori_episodes_aired_1'] = generate_numbers($shikimori['episodes_aired'], 1);
        $xfields_data['shikimori_episodes_aired_2'] = generate_numbers($shikimori['episodes_aired'], 2);
        $xfields_data['shikimori_episodes_aired_3'] = generate_numbers($shikimori['episodes_aired'], 3);
        $xfields_data['shikimori_episodes_aired_4'] = generate_numbers($shikimori['episodes_aired'], 4);
        $xfields_data['shikimori_episodes_aired_5'] = generate_numbers($shikimori['episodes_aired'], 5);
        $xfields_data['shikimori_episodes_aired_6'] = generate_numbers($shikimori['episodes_aired'], 6);
        $xfields_data['shikimori_episodes_aired_7'] = generate_numbers($shikimori['episodes_aired'], 7);
        $xfields_data['shikimori_episodes_aired_8'] = generate_numbers($shikimori['episodes_aired'], 8);
      }

      if ( isset($shikimori['aired_on']) && $shikimori['aired_on'] ) {
        $aired = explode('-', $shikimori['aired_on']);
        $xfields_data['shikimori_aired_on'] = $shikimori['aired_on'];
        $xfields_data['shikimori_aired_on_2'] = convert_date($shikimori['aired_on'], 1);
        $xfields_data['shikimori_aired_on_3'] = convert_date($shikimori['aired_on'], 2);
        $xfields_data['shikimori_aired_on_4'] = convert_date($shikimori['aired_on'], 3);
        $xfields_data['shikimori_year'] = $aired[0];
        if ( $aired[1] == '12' || $aired[1] == '01' || $aired[1] == '02' ) $xfields_data['shikimori_season'] = 'Зима '.$aired[0];
        elseif ( $aired[1] == '03' || $aired[1] == '04' || $aired[1] == '05' ) $xfields_data['shikimori_season'] = 'Весна '.$aired[0];
        elseif ( $aired[1] == '06' || $aired[1] == '07' || $aired[1] == '08' ) $xfields_data['shikimori_season'] = 'Лето '.$aired[0];
        else $xfields_data['shikimori_season'] = 'Осень '.$aired[0];
      }

      if ( isset($shikimori['released_on']) && $shikimori['released_on'] ) {
        $xfields_data['shikimori_released_on'] = $shikimori['released_on'];
        $xfields_data['shikimori_released_on_2'] = convert_date($shikimori['released_on'], 1);
        $xfields_data['shikimori_released_on_3'] = convert_date($shikimori['released_on'], 2);
        $xfields_data['shikimori_released_on_4'] = convert_date($shikimori['released_on'], 3);
      }

      $xfields_data['shikimori_rating'] = (isset($shikimori['rating']) && $shikimori['rating'] != 'none') ? $shikimori['rating'] : '';

      if ( isset($shikimori['duration']) && $shikimori['duration'] ) {
        $xfields_data['shikimori_duration'] = $shikimori['duration'];
        $xfields_data['shikimori_duration_2'] = convert_duration($shikimori['duration'], 1);
        $xfields_data['shikimori_duration_3'] = convert_duration($shikimori['duration'], 2);
        $xfields_data['shikimori_duration_4'] = convert_duration($shikimori['duration'], 3);
        if ( $shikimori['kind'] == 'movie' && $shikimori['duration'] < 30 ) $movie_kind = 'Короткометражный фильм';
        elseif ( $shikimori['kind'] == 'movie' && $shikimori['duration'] >= 30 ) $movie_kind = 'Полнометражный фильм';
      } else $movie_kind = '';

      $xfields_data['shikimori_plot'] = isset($shikimori['description_html']) ? trim(strip_tags($shikimori['description_html'])) : '';

      if ( isset($shikimori['genres']) && $shikimori['genres'] ) {
        $genres = [];
        foreach ( $shikimori['genres'] as $genre ) {
          $genres[] = mb_strtolower($genre['russian']);
        }
        $xfields_data['shikimori_genres'] = implode(', ', $genres);
      }

      if ( isset($shikimori['studios']) && $shikimori['studios'] ) {
        $studios = [];
        foreach ( $shikimori['studios'] as $studio ) {
          $studios[] = $studio['name'];
        }
        $xfields_data['shikimori_studios'] = implode(', ', $studios);
      }

      if ( isset($shikimori['videos']) && $shikimori['videos'] ) {
        $videos = [];
        foreach ( $shikimori['videos'] as $video ) {
          $videos[] = $video['player_url'];
        }
        $xfields_data['shikimori_videos'] = implode(', ', $videos);
      }

      //Ссылки на прочие ресурсы
      if ( isset($aaparser_config['settings']['other_sites']) && $aaparser_config['settings']['other_sites'] == 1 ) {
        $shikimori_links = request($shikimori_api_domain.'api/animes/'.$shiki_id.'/external_links');
		
        $xfields_data['myanimelist_id'] = isset($shikimori['myanimelist_id']) ? $shikimori['myanimelist_id'] : '';
        $source_kind = [];
        foreach ( $shikimori_links as $source_link ) {
          $source_kind[$source_link['kind']] = $source_link['url'];
        }

        $xfields_data['official_site'] = isset($source_kind['official_site']) ? $source_kind['official_site'] : '';
        $xfields_data['wikipedia'] = isset($source_kind['wikipedia']) ? $source_kind['wikipedia'] : '';
        $xfields_data['anime_news_network'] = isset($source_kind['anime_news_network']) ? $source_kind['anime_news_network'] : '';
        $xfields_data['anime_db'] = isset($source_kind['anime_db']) ? $source_kind['anime_db'] : '';
        $xfields_data['world_art'] = isset($source_kind['world_art']) ? $source_kind['world_art'] : '';
        $xfields_data['kinopoisk'] = isset($source_kind['kinopoisk']) ? $source_kind['kinopoisk'] : '';
        $xfields_data['kage_project'] = isset($source_kind['kage_project']) ? $source_kind['kage_project'] : '';
        unset($shikimori_links, $source_kind);
      }

      //Авторский состав
      if ( isset($aaparser_config['settings']['parse_authors']) && $aaparser_config['settings']['parse_authors'] == 1 ) {
        $shikimori_roles = request($shikimori_api_domain.'api/animes/'.$shiki_id.'/roles');
		
        $anime_authors = [];
        foreach ( $shikimori_roles as $role ) {
          if ( !$role['person'] ) continue;
          if ( in_array("Композитор гл. муз. темы", $role['roles_russian']) ) {
            if ( $role['person']['russian'] ) $anime_authors['composition'][] = $role['person']['russian'];
            else $anime_authors['composition'][] = $role['person']['name'];
          } elseif ( in_array("Сценарий", $role['roles_russian']) ) {
            if ( $role['person']['russian'] ) $anime_authors['script'][] = $role['person']['russian'];
            else $anime_authors['script'][] = $role['person']['name'];
          } elseif ( in_array("Продюсер", $role['roles_russian']) ) {
            if ( $role['person']['russian'] ) $anime_authors['producer'][] = $role['person']['russian'];
            else $anime_authors['producer'][] = $role['person']['name'];
          } elseif ( in_array("Режиссёр", $role['roles_russian']) ) {
            if ( $role['person']['russian'] ) $anime_authors['director'][] = $role['person']['russian'];
            else $anime_authors['director'][] = $role['person']['name'];
          }
        }

        if ( isset( $anime_authors['director'] ) && $anime_authors['director'] ) {
          if ( isset($aaparser_config['settings']['max_directors']) && $aaparser_config['settings']['max_directors'] > 0 ) $anime_authors['director'] = array_slice($anime_authors['director'], 0, $aaparser_config['settings']['max_directors']);
          $xfields_data['shikimori_director'] = implode(', ', $anime_authors['director']);
        }
        if ( isset( $anime_authors['producer'] ) && $anime_authors['producer'] ) {
          if ( isset($aaparser_config['settings']['max_producers']) && $aaparser_config['settings']['max_producers'] > 0 ) $anime_authors['producer'] = array_slice($anime_authors['producer'], 0, $aaparser_config['settings']['max_producers']);
          $xfields_data['shikimori_producer'] = implode(', ', $anime_authors['producer']);
        }
        if ( isset( $anime_authors['script'] ) && $anime_authors['script'] ) {
          if ( isset($aaparser_config['settings']['max_writers']) && $aaparser_config['settings']['max_writers'] > 0 && count($anime_authors['script'])>$aaparser_config['settings']['max_writers'] ) $anime_authors['script'] = array_slice($anime_authors['script'], 0, $aaparser_config['settings']['max_writers']);
          $xfields_data['shikimori_script'] = implode(', ', $anime_authors['script']);
        }
        if ( isset( $anime_authors['composition'] ) && $anime_authors['composition'] ) {
          if ( isset($aaparser_config['settings']['max_composers']) && $aaparser_config['settings']['max_composers'] > 0 && count($anime_authors['composition'])>$aaparser_config['settings']['max_composers'] ) $anime_authors['composition'] = array_slice($anime_authors['composition'], 0, $aaparser_config['settings']['max_composers']);
          $xfields_data['shikimori_composition'] = implode(', ', $anime_authors['composition']);
        }
        unset($shikimori_roles, $anime_authors);
      }

      //Франшизы
      if ( isset($aaparser_config['settings']['parse_franshise']) && $aaparser_config['settings']['parse_franshise'] == 1 ) {
        $shiki_api = request($shikimori_api_domain."api/animes/".$shikimori['id']."/franchise");
		
        $movies_id = [];

        if ( $shiki_api['nodes'] ) {
			// Сортировка массива в зависимости от значения $franchise_sort
			if ($aaparser_config['settings']['franchise_sort'] == 'date_asd') {
				usort($shiki_api['nodes'], function($a, $b) {
					return $a['date'] - $b['date']; // Сортировка по дате в порядке возрастания
				});
			} elseif ($aaparser_config['settings']['franchise_sort'] == 'date_dsa') {
				usort($shiki_api['nodes'], function($a, $b) {
					return $b['date'] - $a['date']; // Сортировка по дате в порядке убывания
				});
			} elseif ($aaparser_config['settings']['franchise_sort'] == 'name_asd') {
				usort($shiki_api['nodes'], function($a, $b) {
					return strcmp($a['name'], $b['name']); // Сортировка по названию в порядке возрастания
				});
			} elseif ($aaparser_config['settings']['franchise_sort'] == 'name_dsa') {
				usort($shiki_api['nodes'], function($a, $b) {
					return strcmp($b['name'], $a['name']); // Сортировка по названию в порядке убывания
				});
			} elseif ($aaparser_config['settings']['franchise_sort'] == 'id_asd') {
				usort($shiki_api['nodes'], function($a, $b) {
					return $a['id'] - $b['id']; // Сортировка по ID в порядке возрастания
				});
			} elseif ($aaparser_config['settings']['franchise_sort'] == 'id_dsa') {
				usort($shiki_api['nodes'], function($a, $b) {
					return $b['id'] - $a['id']; // Сортировка по ID в порядке убывания
				});
			}
				
			foreach ( $shiki_api['nodes'] as $shiki_anime ) {
				$movies_id[] = $shiki_anime['id'];
			}
			if ( $movies_id ) $part_id = implode(',', $movies_id);
			else $part_id = '';
        }
        else $part_id = '';

        $xfields_data['shikimori_franshise'] = $part_id;
        unset($shiki_api, $movies_id, $part_id);
      }

      //Похожие аниме
      if ( isset($aaparser_config['settings']['parse_similar']) && $aaparser_config['settings']['parse_similar'] == 1 ) {
        $shiki_api = request($shikimori_api_domain."api/animes/".$shikimori['id']."/similar");
		
        $movies_id = [];

        if ( $shiki_api ) {
          foreach ( $shiki_api as $shiki_anime ) {
            if ( isset($shiki_anime['id']) && $shiki_anime['id'] ) $movies_id[] = $shiki_anime['id'];
          }
          if ( $movies_id ) $part_id = implode(',', $movies_id);
          else $part_id = '';
        }
        else $part_id = '';

        $xfields_data['shikimori_similar'] = $part_id;
        unset($shiki_api, $movies_id, $part_id);
      }

      //Связанные аниме
      if ( isset($aaparser_config['settings']['parse_related']) && $aaparser_config['settings']['parse_related'] == 1 ) {
        $shiki_api = request($shikimori_api_domain."api/animes/".$shikimori['id']."/related");
		
        $movies_id = [];

        if ( $shiki_api ) {
          foreach ( $shiki_api as $shiki_anime ) {
            if ( isset( $shiki_anime['anime']['id'] ) && $shiki_anime['anime']['id'] ) $movies_id[] = $shiki_anime['anime']['id'];
          }
          if ( $movies_id ) $part_id = implode(',', $movies_id);
          else $part_id = '';
        }
        else $part_id = '';

        $xfields_data['shikimori_related'] = $part_id;
        unset($shiki_api, $movies_id, $part_id);
      }

      if ( isset($shikimori['image']['original']) && $shikimori['image']['original'] && strpos($shikimori['image']['original'], "missing_original") !== false) $xfields_data['image'] = $shikimori_image_domain.$shikimori['image']['original'];
	  else unset($xfields_data['image']);

      $next_episode_date = '';

      if ( isset($aaparser_config['settings']['next_episode_date_new']) ) {
        if ( $shikimori['next_episode_at'] ) {
          $next_episode_at = strtotime($shikimori['next_episode_at']);
          $xfields_data[$aaparser_config['settings']['next_episode_date_new']] = date("d.m.Y H:i:s", $next_episode_at);
          $next_episode_date = date("d.m.Y H:i:s", $next_episode_at);
        }
        else $xfields_data[$aaparser_config['settings']['next_episode_date_new']] = '';
      }
      else $xfields_data[$aaparser_config['settings']['next_episode_date_new']] = '';
      
      //Новые теги - длительность сериала и длительность серии
      if ( isset($shikimori['episodes']) && $shikimori['episodes'] && intval($shikimori['episodes']) > 1 ) {
          if ( intval($shikimori['episodes']) <= 13 ) $xfields_data['shikimori_tv_length'] = 'короткие (до 13 эп.)';
          elseif ( intval($shikimori['episodes']) > 13 && intval($shikimori['episodes']) <= 30 ) $xfields_data['shikimori_tv_length'] = 'средние (от 14 до 30 эп.)';
          elseif ( intval($shikimori['episodes']) > 30 ) $xfields_data['shikimori_tv_length'] = 'длинные (более 30 эп.)';
      }
      if ( isset($shikimori['duration']) && $shikimori['duration'] && intval($shikimori['duration']) > 0 ) {
          if ( intval($shikimori['duration']) <= 10 ) $xfields_data['shikimori_duration_length'] = 'до 10 мин.';
          elseif ( intval($shikimori['duration']) > 10 && intval($shikimori['duration']) <= 30 ) $xfields_data['shikimori_duration_length'] = 'от 11 до 30 мин.';
          elseif ( intval($shikimori['duration']) > 30 ) $xfields_data['shikimori_duration_length'] = 'свыше 30 мин.';
      }
      
      //Парсинг жанров напрямую со страницы аниме
      if ( $aaparser_config['settings']['parse_shikimori_genres'] == 1 && isset($shikimori['url']) ) {
          $shikimori_link = $shikimori_api_domain.$shikimori['url'];
          $shikimori_link = str_replace(['.me//', '.one//'], ['.me/', '.one/'], $shikimori_link);
          $shikimori_page = file_get_contents($shikimori_link);
          if ( strpos($shikimori_page, 'genre-ru') !== false ) {
              preg_match_all("|<span class='genre-ru'>(.*)<\/span>|U", $shikimori_page, $genresru, PREG_PATTERN_ORDER);
              if ( is_array($genresru) && isset($genresru[1]) ) {
                  $alt_genres = [];
                  foreach ( $genresru[1] as $gnru ) {
                      $alt_genres[] = mb_strtolower(trim($gnru));
                  }
                  if ( $alt_genres ) {
                      $alt_genres = array_unique($alt_genres);
                      if ( isset($xfields_data['shikimori_genres']) && $xfields_data['shikimori_genres'] ) {
                          $new_genres = [];
                          $old_genres = explode(', ', $xfields_data['shikimori_genres']);
                          foreach ( $old_genres as $old_genre ) {
                              $new_genres[] = $old_genre;
                          }
                          foreach ( $alt_genres as $alt_genre ) {
                              $new_genres[] = $alt_genre;
                          }
                          $new_genres = array_unique($new_genres);
                          $xfields_data['shikimori_genres'] = implode(', ', $new_genres);
                      }
                      else $xfields_data['shikimori_genres'] = implode(', ', $alt_genres);
                  }
              }
          }
      }
      
      //Парсинг с jikan
		if ( $shiki_id && isset($aaparser_config['settings']['parse_jikan']) && $aaparser_config['settings']['parse_jikan']) {
	    $jikan_api = request('https://api.jikan.moe/v4/anime/'.$shiki_id);
	    if (isset( $jikan_api['data']['images']['jpg']['large_image_url'] ) && $jikan_api['data']['images']['jpg']['large_image_url'] ) 
	        $xfields_data['image'] = $jikan_api['data']['images']['jpg']['large_image_url'];
			$jikan_poster = 1;
	    if ( isset( $jikan_api['data']['trailer']['embed_url'] ) && $jikan_api['data']['trailer']['embed_url'] ) 
	        $xfields_data['youtube_trailer'] = $jikan_api['data']['trailer']['embed_url'];
	    if ( isset( $jikan_api['data']['score'] ) && $jikan_api['data']['score'] ) 
	        $xfields_data['myanimelist_rating'] = $jikan_api['data']['score'];
	    if ( isset( $jikan_api['data']['scored_by'] ) && $jikan_api['data']['scored_by'] ) 
	        $xfields_data['myanimelist_votes'] = $jikan_api['data']['scored_by'];
	  }
      
	}
}