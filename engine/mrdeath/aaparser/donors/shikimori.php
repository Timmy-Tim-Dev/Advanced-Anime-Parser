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
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(shikimori.php) Начинаем инициализацию донора shikimori.php", round(microtime(true) - $time_update_start, 4));
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
    $postfields = [
		'query' => '{
			animes(search: "'.$search_name.'", limit: 50) {
				id
				malId
				name
				russian
				kind
				status
				airedOn { year month day date }
				url
			}
		}'
	];
    $shikimori = request('https://shikimori.one/api/graphql', 1, $postfields);
	$shikimori = $shikimori['data']['animes'];
    
    if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск списка материалов по API", round(microtime(true) - $time_update_start, 4));
	}
    if ( $shikimori && $search_name ) {
		
        foreach ( $shikimori as $result ) {
            
            if ( isset($result['id']) && $result['id'] ) $id_shiki = $result['id'];
            else continue;
            
            $kind = 'Аниме. '.$cat_type[$result['kind']];
            $rutitle = isset($result['russian']) ? $result['russian'] : '';
	    	$entitle = isset($result['name']) ? $result['name'] : '';
	    	$status = isset($result['status']) ? $status_type[$result['status']] : '';
	    	
			if ( isset($result['airedOn']['date']) && $result['airedOn']['date'] ) {
				$date_arr = explode('-', $result['airedOn']['date']);
				$year = $date_arr[0];
			} else $year = '';
            
            $shiki_link = isset($result['url']) ? $result['url'] : '';
			
			$where = "xfields REGEXP '(^|\\\\|)" . $aaparser_config['main_fields']['xf_shikimori_id'] ."\\\\|".$id_shiki. "(\\\\||$)'";
			// $where = "xfields LIKE '%".$aaparser_config['main_fields']['xf_shikimori_id']."|".$id_shiki."||%'";
            $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск записи в бд " . PREFIX . "_post", round(microtime(true) - $time_update_start, 4));
			}
	    	if (isset($proverka['id']) && $proverka['id']) {
	    	    $find_id = 'est';
	    	    $edit_link = $config['http_home_url'].$config['admin_path'].'?mod=editnews&action=editnews&id='.$proverka['id'];
	    	} else {
	    	    $find_id = 'net';
	    	    $edit_link = '';
	    	}
	    	
	    	$kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&shikimori_id='.$id_shiki);
	    	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Сделали поиск по базам kodik API", round(microtime(true) - $time_update_start, 4));
			}
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
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(shikimori.php) Разобрали полученный запрос", round(microtime(true) - $time_update_start, 4));
	}
   
} elseif ($parse_action == 'parse') {
    $xfields_data = [];
	if (isset($aaparser_config['settings']['alternate_shikimori']) && $aaparser_config['settings']['alternate_shikimori'] == 1) {
		$shikimori = request($shikimori_api_domain.'api/animes/'.$shiki_id);
	} else {
		$postfields = [
			'query' => '{
				animes(ids: "'.$shiki_id.'", limit: 50) {
					id
					malId
					name
					russian
					licenseNameRu
					english
					japanese
					synonyms
					kind
					rating
					score
					status
					episodes
					episodesAired
					duration
					airedOn { year month day date }
					releasedOn { year month day date }
					url
					season

					poster { id originalUrl }

					licensors
					nextEpisodeAt,

					genres { id name russian kind }
					studios { id name imageUrl }

					externalLinks {
					  id
					  kind
					  url
					  createdAt
					  updatedAt
					}

					personRoles {
					  id
					  rolesRu
					  rolesEn
					  person { id name poster { id } }
					}

					related {
					  id
					  anime {
						id
						name
					  }
					  relationKind
					  relationText
					}

					videos { id url name kind playerUrl imageUrl }
					
					scoresStats { score count }
					
					description
					descriptionHtml
				}
			}'
		];
		$shikimori = request('https://shikimori.one/api/graphql', 1, $postfields);
		$shikimori = $shikimori['data']['animes']['0'];
	}
	
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск id материала по API", round(microtime(true) - $time_update_start, 4));
	}
    if ($shikimori['code'] != "404") {
		if (isset($aaparser_config['settings']['alternate_shikimori']) && $aaparser_config['settings']['alternate_shikimori'] == 1) {
			$shikimori['licenseNameRu'] = isset($shikimori['license_name_ru']) ? $shikimori['license_name_ru'] : '';
			$shikimori['scoresStats'] = isset($shikimori['rates_scores_stats']) ? $shikimori['rates_scores_stats'] : '';
			$shikimori['episodesAired'] = isset($shikimori['episodes_aired']) ? $shikimori['episodes_aired'] : '';
			$shikimori['airedOn']['date'] = isset($shikimori['aired_on']) ? $shikimori['aired_on'] : '';
			$shikimori['releasedOn']['date'] = isset($shikimori['released_on']) ? $shikimori['released_on'] : '';
			$shikimori['nextEpisodeAt'] = isset($shikimori['next_episode_at']) ? $shikimori['next_episode_at'] : '';
			$shikimori['descriptionHtml'] = isset($shikimori['description_html']) ? trim(strip_tags($shikimori['description_html'])) : '';
			$shikimori['english'] = (isset($shikimori['english']) && $shikimori['english']) ? implode(', ', $shikimori['english']) : '';
			$shikimori['japanese'] = (isset($shikimori['japanese']) && $shikimori['japanese']) ? implode(', ', $shikimori['japanese']) : '';
			if ( isset($shikimori['image']['original']) && $shikimori['image']['original'] && strpos($shikimori['image']['original'], "missing_original") !== false) $shikimori['poster']['originalUrl'] = $shikimori_image_domain.$shikimori['image']['original'];
		}
		$xfields_data['shikimori_id'] = isset($shiki_id) ? $shiki_id : '';
		$xfields_data['shikimori_name'] = isset($shikimori['name']) ? $shikimori['name'] : '';
		$xfields_data['shikimori_russian'] = isset($shikimori['russian']) ? $shikimori['russian'] : '';
		$xfields_data['shikimori_english'] = isset($shikimori['english']) ? $shikimori['english'] : '';
		$xfields_data['shikimori_japanese'] = isset($shikimori['japanese']) ? $shikimori['japanese'] : '';
		$xfields_data['shikimori_synonyms'] = (isset($shikimori['synonyms']) && $shikimori['synonyms']) ? implode(', ', $shikimori['synonyms']) : '';
		$xfields_data['shikimori_license_name_ru'] = isset($shikimori['licenseNameRu']) ? $shikimori['licenseNameRu'] : '';
		if ( isset($shikimori['licensors']) && $shikimori['licensors'] ) $xfields_data['shikimori_licensors'] = implode(', ', $shikimori['licensors']);
		$xfields_data['shikimori_kind'] = isset($shikimori['kind']) ? $shikimori['kind'] : '';
		$xfields_data['shikimori_kind_ru'] = isset($shikimori['kind']) ? $cat_type[$shikimori['kind']] : '';
		$xfields_data['shikimori_score'] = (isset($shikimori['score']) && $shikimori['score'] != '0.0') ? $shikimori['score'] : '';
		$shikimori_votes = 0;
		if ( isset($shikimori['scoresStats']) && $shikimori['scoresStats'] ) {
			foreach ( $shikimori['scoresStats'] as $rates_scores_stats ) {
				$shikimori_votes = $shikimori_votes+$rates_scores_stats['count'];
			}
		}
		if ( $shikimori_votes > 0 ) $xfields_data['shikimori_votes'] = $shikimori_votes;
		else $xfields_data['shikimori_votes'] = '';
		$xfields_data['shikimori_status'] = isset($shikimori['status']) ? $shikimori['status'] : '';
		$xfields_data['shikimori_status_ru'] = isset($shikimori['status']) ? $status_type[$shikimori['status']] : '';
		$xfields_data['shikimori_episodes'] = isset($shikimori['episodes']) ? $shikimori['episodes'] : '';

		if ( isset($shikimori['episodesAired']) && $shikimori['episodesAired'] ) {
			$xfields_data['shikimori_episodes_aired'] = $shikimori['episodesAired'];
			$xfields_data['shikimori_episodes_aired_1'] = generate_numbers($shikimori['episodesAired'], 1);
			$xfields_data['shikimori_episodes_aired_2'] = generate_numbers($shikimori['episodesAired'], 2);
			$xfields_data['shikimori_episodes_aired_3'] = generate_numbers($shikimori['episodesAired'], 3);
			$xfields_data['shikimori_episodes_aired_4'] = generate_numbers($shikimori['episodesAired'], 4);
			$xfields_data['shikimori_episodes_aired_5'] = generate_numbers($shikimori['episodesAired'], 5);
			$xfields_data['shikimori_episodes_aired_6'] = generate_numbers($shikimori['episodesAired'], 6);
			$xfields_data['shikimori_episodes_aired_7'] = generate_numbers($shikimori['episodesAired'], 7);
			$xfields_data['shikimori_episodes_aired_8'] = generate_numbers($shikimori['episodesAired'], 8);
		}

		if ( isset($shikimori['airedOn']['date']) && $shikimori['airedOn']['date'] ) {
			$aired = explode('-', $shikimori['airedOn']['date']);
			$xfields_data['shikimori_aired_on'] = $shikimori['airedOn']['date'];
			$xfields_data['shikimori_aired_on_2'] = convert_date($shikimori['airedOn']['date'], 1);
			$xfields_data['shikimori_aired_on_3'] = convert_date($shikimori['airedOn']['date'], 2);
			$xfields_data['shikimori_aired_on_4'] = convert_date($shikimori['airedOn']['date'], 3);
			$xfields_data['shikimori_year'] = $aired[0];
			if ( $aired[1] == '12' || $aired[1] == '01' || $aired[1] == '02' ) $xfields_data['shikimori_season'] = 'Зима '.$aired[0];
			elseif ( $aired[1] == '03' || $aired[1] == '04' || $aired[1] == '05' ) $xfields_data['shikimori_season'] = 'Весна '.$aired[0];
			elseif ( $aired[1] == '06' || $aired[1] == '07' || $aired[1] == '08' ) $xfields_data['shikimori_season'] = 'Лето '.$aired[0];
			else $xfields_data['shikimori_season'] = 'Осень '.$aired[0];
		}

		if ( isset($shikimori['releasedOn']['date']) && $shikimori['releasedOn']['date'] ) {
			$xfields_data['shikimori_released_on'] = $shikimori['releasedOn']['date'];
			$xfields_data['shikimori_released_on_2'] = convert_date($shikimori['releasedOn']['date'], 1);
			$xfields_data['shikimori_released_on_3'] = convert_date($shikimori['releasedOn']['date'], 2);
			$xfields_data['shikimori_released_on_4'] = convert_date($shikimori['releasedOn']['date'], 3);
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

		$xfields_data['shikimori_plot'] = isset($shikimori['descriptionHtml']) ? trim(strip_tags($shikimori['descriptionHtml'])) : '';

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
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение по тегам", round(microtime(true) - $time_update_start, 4));
		}
		//Ссылки на прочие ресурсы
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск ссылок прочих ресурсов по API", round(microtime(true) - $time_update_start, 4));
		}
		
		$source_kind = [];
		foreach ( $shikimori['externalLinks'] as $source_link ) {
			$source_kind[$source_link['kind']] = $source_link['url'];
		}
		$xfields_data['myanimelist_id'] = isset($source_kind['myanimelist']) ? $source_kind['myanimelist'] : '';
		$xfields_data['official_site'] = isset($source_kind['official_site']) ? $source_kind['official_site'] : '';
		$xfields_data['wikipedia'] = isset($source_kind['wikipedia']) ? $source_kind['wikipedia'] : '';
		$xfields_data['anime_news_network'] = isset($source_kind['anime_news_network']) ? $source_kind['anime_news_network'] : '';
		$xfields_data['anime_db'] = isset($source_kind['anime_db']) ? $source_kind['anime_db'] : '';
		$xfields_data['world_art'] = isset($source_kind['world_art']) ? $source_kind['world_art'] : '';
		$xfields_data['kinopoisk'] = isset($source_kind['kinopoisk']) ? $source_kind['kinopoisk'] : '';
		$xfields_data['kage_project'] = isset($source_kind['kage_project']) ? $source_kind['kage_project'] : '';
		unset($source_kind);
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов для ссылок прочих ресурсов", round(microtime(true) - $time_update_start, 4));
		}
		

		//Авторский состав
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск авторского состава по API", round(microtime(true) - $time_update_start, 4));
		}
		$anime_authors = [];
		foreach ( $shikimori['personRoles'] as $role ) {
			if ( !$role['person'] ) continue;
			if ( in_array("Композитор гл. муз. темы", $role['rolesRu']) ) {
				if ( $role['person']['russian'] ) $anime_authors['composition'][] = $role['person']['russian'];
				else $anime_authors['composition'][] = $role['person']['name'];
			} elseif ( in_array("Сценарий", $role['rolesRu']) ) {
				if ( $role['person']['russian'] ) $anime_authors['script'][] = $role['person']['russian'];
				else $anime_authors['script'][] = $role['person']['name'];
			} elseif ( in_array("Продюсер", $role['rolesRu']) ) {
				if ( $role['person']['russian'] ) $anime_authors['producer'][] = $role['person']['russian'];
				else $anime_authors['producer'][] = $role['person']['name'];
			} elseif ( in_array("Режиссёр", $role['rolesRu']) ) {
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
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов для авторского состава", round(microtime(true) - $time_update_start, 4));
		}
		
		//Франшизы
		if ( isset($aaparser_config['settings']['parse_franshise']) && $aaparser_config['settings']['parse_franshise'] == 1 ) {
			$shiki_api = request($shikimori_api_domain."api/animes/".$shiki_id."/franchise");
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск франшизы по API", round(microtime(true) - $time_update_start, 4));
			}
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов для франшизы", round(microtime(true) - $time_update_start, 4));
			}
		}
		
		//Хронология
		if ( isset($aaparser_config['settings']['parse_chronology']) && $aaparser_config['settings']['parse_chronology'] == 1 ) {
			$postfields_chrono = [
				'query' => '{
					animes(ids: "'.$shiki_id.'", limit: 1) {
						id
						name
						chronology { 
							id
							airedOn { date }
						}
					}
				}'
			];
			$shiki_api = request('https://shikimori.one/api/graphql', 1, $postfields_chrono);
			$shiki_api = $shiki_api['data']['animes']['0'];
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск хронологии по API", round(microtime(true) - $time_update_start, 4));
			}
			$movies_id = [];

			if ( $shiki_api['chronology'] ) {
				if ( isset($aaparser_config['settings']['chronology_sort']) && $aaparser_config['settings']['chronology_sort'] == 1) $shiki_api['chronology'] = array_reverse($shiki_api['chronology']);
				foreach ( $shiki_api['chronology'] as $shiki_anime ) {
					$movies_id[] = $shiki_anime['id'];
				}
				if ( $movies_id ) $part_id = implode(',', $movies_id);
				else $part_id = '';
			}
			else $part_id = '';

			$xfields_data['shikimori_chronology'] = $part_id;
			unset($shiki_api, $movies_id, $part_id);
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов для хронологии", round(microtime(true) - $time_update_start, 4));
			}
		}
		
		//Похожие аниме
		if ( isset($aaparser_config['settings']['parse_similar']) && $aaparser_config['settings']['parse_similar'] == 1 ) {
			$shiki_api = request($shikimori_api_domain."api/animes/".$shikimori['id']."/similar");
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск похожих аниме по API", round(microtime(true) - $time_update_start, 4));
			}
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов для похожих аниме", round(microtime(true) - $time_update_start, 4));
			}
		}
		
		//Связанные аниме
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Поиск связанных аниме по API", round(microtime(true) - $time_update_start, 4));
		}
		$movies_id = [];

		if ( $shikimori['related'] ) {
			foreach ( $shikimori['related'] as $shiki_anime ) {
				if ( isset( $shiki_anime['anime']['id'] ) && $shiki_anime['anime']['id'] ) $movies_id[] = $shiki_anime['anime']['id'];
			}
			if ( $movies_id ) $part_id = implode(',', $movies_id);
			else $part_id = '';
		}
		else $part_id = '';

		$xfields_data['shikimori_related'] = $part_id;
		unset($movies_id, $part_id);
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов для связанных аниме", round(microtime(true) - $time_update_start, 4));
		}
		
		if ( isset($shikimori['poster']['originalUrl']) && $shikimori['poster']['originalUrl'] && strpos($shikimori['poster']['originalUrl'], "missing_original") !== false) $xfields_data['image'] = $shikimori['poster']['originalUrl'];
		else unset($xfields_data['image']);

		$next_episode_date = '';

		if ( isset($aaparser_config['settings']['next_episode_date_new']) ) {
			if ( $shikimori['nextEpisodeAt'] ) {
				$next_episode_at = strtotime($shikimori['nextEpisodeAt']);
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
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов времени", round(microtime(true) - $time_update_start, 4));
		}
		//Парсинг жанров напрямую со страницы аниме
		if ( $aaparser_config['settings']['parse_shikimori_genres'] == 1 && isset($shikimori['url']) ) {
			$shikimori_link = $shikimori_api_domain.$shikimori['url'];
			$shikimori_link = str_replace(['.me//', '.one//'], ['.me/', '.one/'], $shikimori_link);
			$shikimori_page = file_get_contents($shikimori_link);
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Парсинг жанров напрямую (file_get_contents) с сайта shikimori", round(microtime(true) - $time_update_start, 4));
			}
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
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение жанров по тегам с сайта shikimori", round(microtime(true) - $time_update_start, 4));
			}
		}
		
		//Парсинг с jikan
		$jikan_poster = 0;
		if ( $shiki_id && isset($aaparser_config['settings']['parse_jikan']) && $aaparser_config['settings']['parse_jikan'] == 1) {
			$jikan_api = request('https://api.jikan.moe/v4/anime/'.$shiki_id);
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) {
				$debugger_table_row .= tableRowCreate("(shikimori.php) Парсинг с jikan.moe", round(microtime(true) - $time_update_start, 4));
			}
			if (isset( $jikan_api['data']['images']['jpg']['large_image_url'] ) && $jikan_api['data']['images']['jpg']['large_image_url'] ) 
				$xfields_data['image'] = $jikan_api['data']['images']['jpg']['large_image_url'];
				$jikan_poster = 1;
			if ( isset( $jikan_api['data']['trailer']['embed_url'] ) && $jikan_api['data']['trailer']['embed_url'] ) 
				$xfields_data['youtube_trailer'] = $jikan_api['data']['trailer']['embed_url'];
			if ( isset( $jikan_api['data']['score'] ) && $jikan_api['data']['score'] ) 
				$xfields_data['myanimelist_rating'] = $jikan_api['data']['score'];
			if ( isset( $jikan_api['data']['scored_by'] ) && $jikan_api['data']['scored_by'] ) 
				$xfields_data['myanimelist_votes'] = $jikan_api['data']['scored_by'];
			
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(shikimori.php) Распределение тегов полученных с jikan.moe", round(microtime(true) - $time_update_start, 4));
			}
		}
      
	}
	
} elseif ( $parse_action == 'takeimage' ) {
	$xfields_data = [];
    $postfields = [
		'query' => '{
			animes(search: "'.$search_name.'", limit: 50) {
				id
				malId
				name
				russian
				poster { id originalUrl }
			}
		}'
	];
	$shikimori = request('https://shikimori.one/api/graphql', 1, $postfields);
	$shikimori = $shikimori['data']['animes']['0'];
	if ( isset($shikimori['poster']['originalUrl']) && $shikimori['poster']['originalUrl'] && strpos($shikimori['poster']['originalUrl'], "missing_original") !== false) $xfields_data['image'] = $shikimori['poster']['originalUrl'];
	else unset($xfields_data['image']);
	$xfields_data['shikimori_name'] = isset($shikimori['name']) ? $shikimori['name'] : '';
	$xfields_data['shikimori_russian'] = isset($shikimori['russian']) ? $shikimori['russian'] : '';
	
	$jikan_poster = 0;
	if ( $shiki_id && isset($aaparser_config['settings']['parse_jikan']) && $aaparser_config['settings']['parse_jikan'] == 1) {
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
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(shikimori.php) Закончили инициализацию донора shikimori.php", round(microtime(true) - $time_update_start, 4));
}