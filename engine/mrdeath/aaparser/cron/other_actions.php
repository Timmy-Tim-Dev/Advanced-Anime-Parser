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
    if ( isset($aaparser_config['calendar_settings']['enable_schedule']) && $aaparser_config['calendar_settings']['enable_schedule'] && $aaparser_config['main_fields']['xf_shikimori_id'] ) {
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
  	    $kodik_api = request('https://dumps.kodik.biz/calendar.json?token='.$kodik_apikey);
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['other_material'] == 1 ) { 
			$debugger_table_row .= tableRowCreate("(other_actions.php) Получение данных с KODIK API CALENDAR", round(microtime(true) - $time_update_start,4));
		}
  	    $today_id = [strtolower(date("l", $_TIME)) => date("Y-m-d", $_TIME)];
  	    for ($i = 1; $i < 7; $i++) {
      	    $plus_time = $_TIME+($i*86400);
    	    $today_id[strtolower(date('l', $plus_time))] = date('Y-m-d', $plus_time);
	    }
  	    $spisok_raspisaniy = [];
  	    foreach ( $today_id as $today_name => $today_date ) {
      	    $temp_num = 0;
  		    foreach ( $kodik_api as $api_anime ) {
          	    if ( strpos($api_anime['next_episode_at'], $today_date) !== false ) {
              	    $news_row = $db->super_query( "SELECT id, alt_name, date, title, category, xfields FROM " . PREFIX . "_post WHERE xfields LIKE '%{$aaparser_config['main_fields']['xf_shikimori_id']}|{$api_anime['anime']['id']}||%' AND approve=1" );
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
              	    unset($news_row, $full_link, $temp_xfields);
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
	    echo "Расписание аниме было обновлено. Данные перезаписаны.<br>";
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
	