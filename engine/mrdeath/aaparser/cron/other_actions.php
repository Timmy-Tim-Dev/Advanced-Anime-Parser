<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

    if ( isset($aaparser_config_push['calendar_settings']['enable_schedule']) && $aaparser_config_push['calendar_settings']['enable_schedule'] && $aaparser_config_push['main_fields']['xf_shikimori_id'] ) {
        $no_poster = '/templates/'.$config['theme'].'/dleimages/no_image.jpg';
        $sql = "SELECT * FROM " . PREFIX . "_raspisanie_ongoingov";
        $db->query( $sql );
  	    $raspisanie_ongoingov = [];
        while ( $row = $db->get_row() ) {
      	    $raspisanie_ongoingov[] = $row;
        }
  	    $shikimori_api = request($shikimori_api_domain.'api/calendar');
  	    $today_id = [strtolower(date("l", $_TIME)) => date("Y-m-d", $_TIME)];
  	    for ($i = 1; $i < 7; $i++) {
      	    $plus_time = $_TIME+($i*86400);
    	    $today_id[strtolower(date('l', $plus_time))] = date('Y-m-d', $plus_time);
	    }
  	    $spisok_raspisaniy = [];
  	    foreach ( $today_id as $today_name => $today_date ) {
      	    $temp_num = 0;
  		    foreach ( $shikimori_api as $api_anime ) {
          	    if ( strpos($api_anime['next_episode_at'], $today_date) !== false ) {
              	    $news_row = $db->super_query( "SELECT id, alt_name, date, title, category, xfields FROM " . PREFIX . "_post WHERE xfields LIKE '%{$aaparser_config_push['main_fields']['xf_shikimori_id']}|{$api_anime['anime']['id']}||%' AND approve=1" );
              	    if ( !$news_row['id'] ) {
                  	    unset($news_row);
                  	    continue;
                    }
              	    $news_row['category'] = intval( $news_row['category'] );
  				    $news_row['date'] = strtotime( $news_row['date'] );
  				    if( $config['allow_alt_url'] ) {
					    if( $config['seo_type'] == 1 OR $config['seo_type'] == 2 ) {
						    if( $news_row['category'] and $config['seo_type'] == 2 ) {
							    $full_link = $config['http_home_url'] . get_url( $news_row['category'] ) . "/" . $news_row['id'] . "-" . $news_row['alt_name'] . ".html";
						    } else $full_link = $config['http_home_url'] . $news_row['id'] . "-" . $news_row['alt_name'] . ".html";
					    } else $full_link = $config['http_home_url'] . date( 'Y/m/d/', $news_row['date'] ) . $news_row['alt_name'] . ".html";
				    } else $full_link = $config['http_home_url'] . "index.php?newsid=" . $news_row['id'];
              	    $temp_xfields = xfieldsdataload($news_row['xfields']);
              	    $spisok_raspisaniy[$today_name][$temp_num]['news_id'] = $news_row['id'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['shikimori_id'] = $api_anime['anime']['id'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['russian'] = stripslashes($news_row['title']);
              	    $spisok_raspisaniy[$today_name][$temp_num]['original'] = $api_anime['anime']['name'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['full_link'] = $full_link;
              	    if ( isset( $aaparser_config_push['main_fields']['xf_poster'] ) && $temp_xfields[$aaparser_config_push['main_fields']['xf_poster']] ) $spisok_raspisaniy[$today_name][$temp_num]['image'] = $temp_xfields[$aaparser_config_push['main_fields']['xf_poster']];
              	    else $spisok_raspisaniy[$today_name][$temp_num]['image'] = $no_poster;
              	    $spisok_raspisaniy[$today_name][$temp_num]['next_episode'] = $api_anime['next_episode'];
              	    $spisok_raspisaniy[$today_name][$temp_num]['next_date'] = $api_anime['next_episode_at'];
              	    unset($news_row, $full_link, $temp_xfields);
              	    $temp_num++;
                }
            }
        }
  
  	    foreach ( $spisok_raspisaniy as $rname => $rdata ) {
      	    if ( $rdata ) {
          	    $rdate = $today_id[$rname];
          	    $rlist = json_encode($rdata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
          	    $rlist = $db->safesql($rlist);
          	    $db->query( "UPDATE " . PREFIX . "_raspisanie_ongoingov SET date='{$rdate}', anime_list='{$rlist}' WHERE day_name='{$rname}'" );
            }
        }
  	    clear_cache(array("raspisanie_ongoingov"));
	    echo "Расписание аниме было обновлено. Данные перезаписаны.<br>";
    }
    if ( isset($aaparser_config['settings']['rooms_enable']) && $aaparser_config['settings']['rooms_enable'] ) {
        $rooms = $db->query( "SELECT url, leader FROM " . PREFIX . "_rooms_list" );
        $rooms_list = [];
        while($temp_rooms = $db->get_row($rooms)) {
            $rooms_list[$temp_rooms['url']] = $temp_rooms['leader'];
        }

        foreach ( $rooms_list as $room_url => $room_leader ) {
            $check_room = $db->super_query( "SELECT time, room_url, login FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_url}' AND login='{$room_leader}'" );
            if ( !$check_room['time'] || $check_room['time'] < ($_TIME-10800) ) {
                $db->query("DELETE FROM " . PREFIX . "_rooms_list WHERE url='{$room_url}'");
                $db->query("DELETE FROM " . PREFIX . "_rooms_visitors WHERE room_url='{$room_url}'");
                $db->query("DELETE FROM " . PREFIX . "_rooms_chat WHERE room_url='{$room_url}'");
            }
            unset($check_room);
        }
        echo "Не активные комнаты были удалены.<br>";
    }
    echo "Крон завершил свою работу";