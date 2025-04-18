<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
	$debugger_table_row .= tableRowCreate("(other_actions.php) Начинаем обновление расписания и совместного  просмотра", round(microtime(true) - $time_update_start,4));
}
    if ( isset($aaparser_config['calendar_settings']['enable_schedule']) && $aaparser_config['calendar_settings']['enable_schedule'] && ($aaparser_config['main_fields']['xf_shikimori_id'] || $aaparser_config['main_fields']['xf_mdl_id'])) {
        $no_poster = '/templates/'.$config['theme'].'/dleimages/no_image.jpg';
        $sql = "SELECT * FROM " . PREFIX . "_raspisanie_ongoingov";
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(other_actions.php) Получение данных с бд " . PREFIX . "_raspisanie_ongoingov", round(microtime(true) - $time_update_start,4));
		}
        $db->query( $sql );
  	    $raspisanie_ongoingov = [];
        while ( $row = $db->get_row() ) {
      	    $raspisanie_ongoingov[] = $row;
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(other_actions.php) Получение данных каждой записи с бд " . PREFIX . "_raspisanie_ongoingov", round(microtime(true) - $time_update_start,4));
			}
        }
		if ( $aaparser_config['settings']['working_mode'] !== 0 ) {
			$json = request('https://dumps.kodik.biz/calendar-drama.json?token='.$kodik_apikey);
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(other_actions.php) Получение данных с KODIK API CALENDAR MYDRAMALIST", round(microtime(true) - $time_update_start,4));
				$debugger_table_row .= tableRowCreate("(other_actions.php) Начинаем обработку данных с KODIK API CALENDAR MYDRAMALIST", round(microtime(true) - $time_update_start,4));
			}
			foreach ( $json['items'] as $mdl_key => $mdl_item ) {
				if ($mdl_item['episode_number'] == 0) continue;
				$mdl_data[$mdl_key]['next_episode'] = $mdl_item['episode_number'];
				$mdl_data[$mdl_key]['duration'] = $mdl_item['duration'];
				$mdl_data[$mdl_key]['anime']['id'] = ltrim(preg_replace('#/episode/.*$#', '', $mdl_item['permalink']), '/');
				$mdl_data[$mdl_key]['anime']['name'] = ucwords(str_replace('-', ' ', preg_replace('/^\d+-/', '', $mdl_data[$mdl_key]['anime']['id'])));;
				$mdl_data[$mdl_key]['next_episode_at'] = gmdate('Y-m-d\TH:i:s\Z', $mdl_item['released_at']);
				$mdl_data[$mdl_key]['dorama'] = true;
			}

			foreach ($mdl_data as $mdl_key => $mdl_item) {
				$group_key = $mdl_item['anime']['id'] . '|' . $mdl_item['next_episode_at'];
				
				if (!isset($temp_grouped[$group_key])) {
					$temp_grouped[$group_key] = [
						'original_keys' => [$mdl_key],
						'episodes' => [$mdl_item['next_episode']],
						'duration' => $mdl_item['duration'],
						'anime' => $mdl_item['anime'],
						'next_episode_at' => $mdl_item['next_episode_at'],
						'dorama' => $mdl_item['dorama'],
					];
				} else {
					$temp_grouped[$group_key]['original_keys'][] = $mdl_key;
					$temp_grouped[$group_key]['episodes'][] = $mdl_item['next_episode'];
				}
			}

			$final_mdl_data = [];

			foreach ($temp_grouped as $group) {
				$all_episodes = [];
				foreach ($group['episodes'] as $ep_str) {
					foreach (explode('-', $ep_str) as $ep) {
						$all_episodes[] = (int)$ep;
					}
				}
				sort($all_episodes);
				$min_ep = min($all_episodes);
				$max_ep = max($all_episodes);

				$new_item = [
					'next_episode' => $min_ep === $max_ep ? "$min_ep" : "$min_ep-$max_ep",
					'duration' => $group['duration'],
					'anime' => $group['anime'],
					'next_episode_at' => $group['next_episode_at'],
					'dorama' => $group['dorama'],
				];

				$final_mdl_data[$group['original_keys'][0]] = $new_item;
			}
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(other_actions.php) Закончили обработку данных с KODIK API CALENDAR MYDRAMALIST", round(microtime(true) - $time_update_start,4));
			}
		} 
		if ($aaparser_config['settings']['working_mode'] == 0 || $aaparser_config['settings']['working_mode'] == 2) {
			$kodik_api = request('https://dumps.kodik.biz/calendar.json?token='.$kodik_apikey);
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(other_actions.php) Получение данных с KODIK API CALENDAR SHIKIMORI", round(microtime(true) - $time_update_start,4));
			}
		}
  	    $today_id = [strtolower(date("l", $_TIME)) => date("Y-m-d", $_TIME)];
		
		$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
		
  	    for ($i = 1; $i < 7; $i++) {
      	    $plus_time = $_TIME+($i*86400);
    	    $today_id[strtolower(date('l', $plus_time))] = date('Y-m-d', $plus_time);
	    }
		uksort($today_id, function($a, $b) use ($days) {
			return array_search($a, $days) - array_search($b, $days);
		});
		$merged_data = array_merge(
			is_array($kodik_api) ? $kodik_api : [],
			is_array($final_mdl_data) ? $final_mdl_data : []
		);
		
  	    $spisok_raspisaniy = [];
  	    foreach ( $today_id as $today_name => $today_date ) {
      	    $temp_num = 0;
  		    foreach ( $merged_data as $api_anime ) {
          	    if ( strpos($api_anime['next_episode_at'], $today_date) !== false ) {
					if ($api_anime['dorama'] == 1) $where_xf = "xfields REGEXP '(^|\\\\|)" . $aaparser_config['main_fields']['xf_mdl_id'] . "\\\\|" . $api_anime['anime']['id'] . "(\\\\||$)'";
					else $where_xf = "xfields REGEXP '(^|\\\\|)" . $aaparser_config['main_fields']['xf_shikimori_id'] ."\\\\|".$api_anime['anime']['id']. "(\\\\||$)'";

              	    $news_row = $db->super_query( "SELECT id, alt_name, date, title, category, xfields FROM " . PREFIX . "_post WHERE approve=1 AND ". $where_xf);
              	    if ( !$news_row['id'] ) {
                  	    unset($news_row);
                  	    continue;
                    }
              	    $news_row['category'] = intval( $news_row['category'] );
  				    $news_row['date'] = strtotime( $news_row['date'] );
  				    if( $config['allow_alt_url'] ) {
					    if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
						    if( $news_row['category'] and $config['seo_type'] == 2 ) {
							    $full_link = "/" . get_url( $news_row['category'] ) . "/" . $news_row['id'] . "-" . $news_row['alt_name'] . ".html";
						    } else $full_link ="/" . $news_row['id'] . "-" . $news_row['alt_name'] . ".html";
					    } else $full_link = "/" . date( 'Y/m/d/', $news_row['date'] ) . $news_row['alt_name'] . ".html";
				    } else $full_link = "/" . "index.php?newsid=" . $news_row['id'];
              	    $temp_xfields = xfieldsdataload($news_row['xfields']);
              	    $spisok_raspisaniy[$today_name][$temp_num]['news_id'] = $news_row['id'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['shikimori_id'] = $api_anime['anime']['id'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['russian'] = stripslashes($news_row['title']);
              	    $spisok_raspisaniy[$today_name][$temp_num]['original'] = $api_anime['anime']['name'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['full_link'] = $full_link;
              	    if ( isset( $aaparser_config['main_fields']['xf_poster'] ) && $temp_xfields[$aaparser_config['main_fields']['xf_poster']] ) $spisok_raspisaniy[$today_name][$temp_num]['image'] = $temp_xfields[$aaparser_config['main_fields']['xf_poster']];
              	    else $spisok_raspisaniy[$today_name][$temp_num]['image'] = $no_poster;
              	    $spisok_raspisaniy[$today_name][$temp_num]['next_episode'] = $api_anime['next_episode'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['next_date'] = $api_anime['next_episode_at'];
              	    unset($news_row, $full_link, $temp_xfields, $where_xf);
              	    $temp_num++;
					if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
						$debugger_table_row .= tableRowCreate("(other_actions.php) Формирование данных для записи", round(microtime(true) - $time_update_start,4));
					}
                }
            }
        }
  
  	    foreach ( $spisok_raspisaniy as $rname => $rdata ) {
      	    if ( $rdata ) {
          	    $rdate = $today_id[$rname];
          	    $rlist = json_encode($rdata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
          	    $rlist = $db->safesql($rlist);
          	    $db->query( "UPDATE " . PREFIX . "_raspisanie_ongoingov SET date='{$rdate}', anime_list='{$rlist}' WHERE day_name='{$rname}'" );
				if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) {
					$debugger_table_row .= tableRowCreate("(other_actions.php) Обнолвение данных дня (".$rname.") в бд " . PREFIX . "_raspisanie_ongoingov", round(microtime(true) - $time_update_start,4));
				}
            }
        }
  	    clear_cache(array("raspisanie_ongoingov"));
	    echo "Расписание тайтлов было обновлено. Данные перезаписаны.<br>";
    }
    if ( isset($aaparser_config['settings']['rooms_enable']) && $aaparser_config['settings']['rooms_enable'] ) {
        $rooms = $db->query( "SELECT url, leader FROM " . PREFIX . "_rooms_list" );
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) {
			$debugger_table_row .= tableRowCreate("(other_actions.php) Получение данных с бд " . PREFIX . "_rooms_list", round(microtime(true) - $time_update_start,4));
		}
        $rooms_list = [];
        while($temp_rooms = $db->get_row($rooms)) {
            $rooms_list[$temp_rooms['url']] = $temp_rooms['leader'];
			if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
				$debugger_table_row .= tableRowCreate("(other_actions.php) Получение данных с бд " . PREFIX . "_rooms_list", round(microtime(true) - $time_update_start,4));
			}
        }

        foreach ( $rooms_list as $room_url => $room_leader ) {
            $check_room = $db->super_query( "SELECT time, room_url, login FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_url}' AND login='{$room_leader}'" );
            if ( !$check_room['time'] || $check_room['time'] < ($_TIME-10800) ) {
                $db->query("DELETE FROM " . PREFIX . "_rooms_list WHERE url='{$room_url}'");
				if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) {
					$debugger_table_row .= tableRowCreate("(other_actions.php) Удаление данных (".$room_url.") записи с бд " . PREFIX . "_rooms_list", round(microtime(true) - $time_update_start,4));
				}
                $db->query("DELETE FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_url}'");
				if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
					$debugger_table_row .= tableRowCreate("(other_actions.php) Удаление данных (".$room_url.") записи с бд " . PREFIX . "_rooms_visitors", round(microtime(true) - $time_update_start,4));
				}
                $db->query("DELETE FROM " . PREFIX . "_rooms_chat WHERE room_url='{$room_url}'");
				if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
					$debugger_table_row .= tableRowCreate("(other_actions.php) Удаление данных (".$room_url.") записи с бд " . PREFIX . "_rooms_chat", round(microtime(true) - $time_update_start,4));
				}
            }
            unset($check_room);
        }
        echo "Не активные комнаты были удалены.<br>";
    }
	if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
		$debugger_table_row .= tableRowCreate("(other_actions.php) Закончили обновление расписания и совместного просмотра", round(microtime(true) - $time_update_start,4));
	}
    echo "Крон завершил свою работу<br>";
	