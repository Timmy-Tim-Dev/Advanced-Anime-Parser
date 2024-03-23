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
    'special' => 'Спэшл',
    'music' => 'AMV'
];

$status_type = [
    'anons' => 'Анонс',
    'ongoing' => 'Онгоинг',
    'released' => 'Завершён'
    
];

$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';

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
			}
            else $year = '';
            
            $shiki_link = isset($result['url']) ? $shikimori_image_domain.$result['url'] : '';
			
			$where = "xfields LIKE '%".$aaparser_config['fields']['xf_shikimori_id']."|".$id_shiki."||%'";
            $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
			
	    	if (isset($proverka['id']) && $proverka['id']) {
	    	    $find_id = 'est';
	    	    $edit_link = $config['http_home_url'].'admin.php?mod=editnews&action=editnews&id='.$proverka['id'];
	    	}
	    	else {
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
    
}
elseif ($parse_action == 'parse') {
    
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
      }
      else $movie_kind = '';

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
          }
          elseif ( in_array("Сценарий", $role['roles_russian']) ) {
            if ( $role['person']['russian'] ) $anime_authors['script'][] = $role['person']['russian'];
            else $anime_authors['script'][] = $role['person']['name'];
          }
          elseif ( in_array("Продюсер", $role['roles_russian']) ) {
            if ( $role['person']['russian'] ) $anime_authors['producer'][] = $role['person']['russian'];
            else $anime_authors['producer'][] = $role['person']['name'];
          }
          elseif ( in_array("Режиссёр", $role['roles_russian']) ) {
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

      if ( isset($shikimori['image']['original']) && $shikimori['image']['original'] ) $xfields_data['image'] = $shikimori_image_domain.$shikimori['image']['original'];

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
	}
}