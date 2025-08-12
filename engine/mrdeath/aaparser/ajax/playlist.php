<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
  
if( !defined('DATALIFEENGINE' ) ) {
	die('Hacking attempt!');
}

require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));

if ( !isset($kodik_playlist_fullstory) ) {
  
header('Content-Type: text/html; charset=utf-8');

function kodik_api($url) {
	if ( $curl = curl_init()) {
		$headers = array(
        	'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.2924.87 Safari/537.36',
        	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        	'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
        	'Connection: keep-alive',
        	'Cache-Control: max-age=0',
        	'Upgrade-Insecure-Requests: 1'
    	);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$out = curl_exec($curl);
		$parse = json_decode($out, true);
		curl_close($curl);
	} else {
		$out = file_get_contents($url);
		$parse = json_decode($out, true);
	}
	return $parse;
}



$news_id = isset($_POST['news_id']) ? intval($_POST['news_id']) : false;
$api_token = $aaparser_config['settings']['kodik_api_key'] ?? false;
$action = $_POST['action'] ?? 'load_player';

$this_season = isset($_POST['this_season']) ? intval($_POST['this_season']) : 0;
$this_episode = isset($_POST['this_episode']) ? intval($_POST['this_episode']) : 0;
$this_translator = isset($_POST['this_translator']) ? intval($_POST['this_translator']) : 0;

if(isset($aaparser_config['player']['sw_setting']) && $aaparser_config['player']['sw_setting']) $sw_player_cookie = "data-player_cookie='1'";
else $sw_player_cookie = "data-player_cookie='0'";

if ( isset($aaparser_config['settings']['kodik_api_domain']) ) $kodik_api_domain = $aaparser_config['settings']['kodik_api_domain'];
else $kodik_api_domain = 'https://kodikapi.com/';
    
if ( isset($aaparser_config['player']['custom_cache']) && $aaparser_config['player']['custom_cache'] == 1 ) $playlist = kodik_cache('playlist_'.$news_id, false, 'player');
else $playlist = dle_cache('kodik_playlist', $news_id, false);

if ( $aaparser_config['player']['enable'] != 1 ) die('stop');
elseif ( !$api_token ) die('stop');
elseif ( !$aaparser_config['main_fields']['xf_shikimori_id'] && !$aaparser_config['main_fields']['xf_mdl_id'] && !$aaparser_config['player']['worldart_anime'] && !$aaparser_config['player']['worldart_cinema'] && !$aaparser_config['player']['kinopoisk_id'] && !$aaparser_config['player']['imdb_id'] ) die('stop');
elseif ( $playlist !== false ) $playlist = json_decode($playlist, true);
elseif ( $news_id ) {
    
    $news_row = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE id={$news_id}" );
    if ( !$news_row['id'] ) die('stop');
    
    $post_fields = xfieldsdataload($news_row['xfields']);

    $translations_priority_anime   = $aaparser_config['player']['translations_priority'] ? '&prioritize_translations='.$aaparser_config['player']['translations_priority'] : '';
    $translations_unpriority_anime = $aaparser_config['player']['translations_unpriority'] ? '&unprioritize_translations='.$aaparser_config['player']['translations_unpriority'] : '';
    $translations_hide_anime       = $aaparser_config['player']['translations_hide'] ? '&block_translations='.$aaparser_config['player']['translations_hide'] : '';
    $translation_id_anime          = '';
    if ( $aaparser_config['player']['translations_priority'] && strpos($aaparser_config['player']['translations_priority'], ',') === false ) {
        $translation_id_anime = '&translation_id=' . $aaparser_config['player']['translations_priority'];
        $translations_priority_anime = '';
    }

    $translations_priority_dorama   = $aaparser_config['player']['translations_priority_dorama'] ? '&prioritize_translations='.$aaparser_config['player']['translations_priority_dorama'] : '';
    $translations_unpriority_dorama = $aaparser_config['player']['translations_unpriority_dorama'] ? '&unprioritize_translations='.$aaparser_config['player']['translations_unpriority_dorama'] : '';
    $translations_hide_dorama       = $aaparser_config['player']['translations_hide_dorama'] ? '&block_translations='.$aaparser_config['player']['translations_hide_dorama'] : '';
    $translation_id_dorama          = '';
    if ( $aaparser_config['player']['translations_priority_dorama'] && strpos($aaparser_config['player']['translations_priority_dorama'], ',') === false ) {
        $translation_id_dorama = '&translation_id=' . $aaparser_config['player']['translations_priority_dorama'];
        $translations_priority_dorama = '';
    }

    if ( $aaparser_config['main_fields']['xf_shikimori_id'] && $post_fields[$aaparser_config['main_fields']['xf_shikimori_id']] ) {
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&shikimori_id='.$post_fields[$aaparser_config['main_fields']['xf_shikimori_id']].'&with_episodes_data=true'.$translation_id_anime.$translations_priority_anime.$translations_unpriority_anime.$translations_hide_anime);
    } elseif ( $aaparser_config['main_fields']['xf_mdl_id'] && $post_fields[$aaparser_config['main_fields']['xf_mdl_id']] ) {
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&mdl_id='.$post_fields[$aaparser_config['main_fields']['xf_mdl_id']].'&with_episodes_data=true'.$translation_id_dorama.$translations_priority_dorama.$translations_unpriority_dorama.$translations_hide_dorama);
    } elseif ( $aaparser_config['player']['worldart_anime'] && $post_fields[$aaparser_config['player']['worldart_anime']] ) {
        if ( stripos($post_fields[$aaparser_config['player']['worldart_anime']], 'world-art.ru') !== false )
            $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_link='.$post_fields[$aaparser_config['player']['worldart_anime']].'&with_episodes_data=true'.$translation_id_anime.$translations_priority_anime.$translations_unpriority_anime.$translations_hide_anime);
        else $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_animation_id='.$post_fields[$aaparser_config['player']['worldart_anime']].'&with_episodes_data=true'.$translation_id_anime.$translations_priority_anime.$translations_unpriority_anime.$translations_hide_anime);
    } elseif ( $aaparser_config['player']['worldart_cinema'] && $post_fields[$aaparser_config['player']['worldart_cinema']] ) {
        if ( stripos($post_fields[$aaparser_config['player']['worldart_cinema']], 'world-art.ru') !== false )
            $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_link='.$post_fields[$aaparser_config['player']['worldart_cinema']].'&with_episodes_data=true'.$translation_id_dorama.$translations_priority_dorama.$translations_unpriority_dorama.$translations_hide_dorama);
        else $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_cinema_id='.$post_fields[$aaparser_config['player']['worldart_cinema']].'&with_episodes_data=true'.$translation_id_dorama.$translations_priority_dorama.$translations_unpriority_dorama.$translations_hide_dorama);
    } elseif ( $aaparser_config['player']['kinopoisk_id'] && $post_fields[$aaparser_config['player']['kinopoisk_id']] )
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&kinopoisk_id='.$post_fields[$aaparser_config['player']['kinopoisk_id']].'&with_episodes_data=true'.$translation_id_anime.$translations_priority_anime.$translations_unpriority_anime.$translations_hide_anime);
    elseif ( $aaparser_config['player']['imdb_id'] && $post_fields[$aaparser_config['player']['imdb_id']] )
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&imdb_id='.$post_fields[$aaparser_config['player']['imdb_id']].'&with_episodes_data=true'.$translation_id_anime.$translations_priority_anime.$translations_unpriority_anime.$translations_hide_anime);
        
    if ( $kodik['results'] ) {
        $playlist = array();
        $max_episodes = array();
        $max_seasons = array();
        foreach ($kodik['results'] as $num => $translators) {
            $playlist[$num]['translator_name'] = $translators['translation']['title'];
            $playlist[$num]['translator_id'] = $translators['translation']['id'];
            $playlist[$num]['translator_link'] = $translators['link'];
            foreach ( $translators['seasons'] as $season => $episode ) {
                foreach ( $episode['episodes'] as $ep_num => $episode_links ) {
                    if ( $episode_links['title'] ) $playlist[$num]['episodes'][$season][$ep_num] = trim(str_replace('серия', '', $episode_links['title']));
                    else $playlist[$num]['episodes'][$season][$ep_num] = $ep_num;
                    $max_episode = $ep_num;
                }
                $max_season = $season;
            }
            $playlist[$num]['max_season'] = $max_season;
            $playlist[$num]['max_episode'] = $max_episode;
            $max_seasons[] = $max_season;
            $max_episodes[] = $max_episode;
        }
        array_multisort($max_seasons, SORT_DESC, $max_episodes, SORT_DESC, $playlist);
        $playlist[0]['serial_name'] = $kodik['results'][0]['title'];
        if ( $aaparser_config['player']['add_params'] && $post_fields[$aaparser_config['player']['add_params']] ) $playlist[0]['my_params'] = $post_fields[$aaparser_config['player']['add_params']];
        if ( $aaparser_config['player']['geoblock'] && $post_fields[$aaparser_config['player']['geoblock']] ) $playlist[0]['geoblock'] = $post_fields[$aaparser_config['player']['geoblock']];
        if ( isset($aaparser_config['player']['custom_cache']) && $aaparser_config['player']['custom_cache'] == 1 ) kodik_create_cache('playlist_'.$news_id, json_encode($playlist, JSON_UNESCAPED_UNICODE), false, 'player');
        else create_cache('kodik_playlist', json_encode($playlist, JSON_UNESCAPED_UNICODE), $news_id, false);
        unset($translators, $episode, $kodik, $max_episodes, $max_seasons, $max_episode, $max_season);
    }
}

}
elseif ( isset($kodik_playlist_fullstory) && $kodik_playlist_fullstory == 'yes' ) {
    $news_id = $row['id'];
    $action = 'load_player';
    
    if ( isset($aaparser_config['player']['custom_cache']) && $aaparser_config['player']['custom_cache'] == 1 ) $playlist = kodik_cache('playlist_'.$news_id, false, 'player');
    else $playlist = dle_cache('kodik_playlist', $news_id, false);
    if ( $playlist !== false ) $playlist = json_decode($playlist, true);
    else {
        $playlist = [];
        if ( $aaparser_config['player']['preloader'] ) $tpl->set( '{kodik_playlist}', '<div id="kodik_player_ajax" data-news_id="'.$row['id'].'" data-has_cache="no" '.$sw_player_cookie.'><div class="loading-kodik"><div class="arc"></div><div class="arc"></div><div class="arc"></div></div></div>' );
        else $tpl->set( '{kodik_playlist}', '<div id="kodik_player_ajax" data-news_id="'.$row['id'].'" data-has_cache="no" '.$sw_player_cookie.'></div>' );
    }
    
}

$serial_name = $playlist[0]['serial_name'];
$add_params = isset($playlist[0]['my_params']) ? '&'.$playlist[0]['my_params'] : '';
$geoblock = isset($playlist[0]['geoblock']) ? '&geoblock='.$playlist[0]['geoblock'] : '';

if ( $is_logged ) {
    if ( $member_id['watched_series'] ) $watched_series = json_decode($member_id['watched_series'], true);
    else $watched_series = [];
        
    foreach ( $watched_series as $key => $value ) {
	   if ( $value['news_id'] == $news_id ) {
            $last_episode = $value['episode'];
            $last_season = $value['season'];
            $last_translator = $value['translation'];
        }
    }
}
elseif ( isset($_COOKIE['watched_series_'.$news_id]) ) {
    $watched_series = explode(',', htmlspecialchars($_COOKIE['watched_series_'.$news_id]));
    $last_translator = $watched_series[0];
    $last_season = $watched_series[1];
    $last_episode = $watched_series[2];
}


if ( $playlist[0]['geoblock'] && $aaparser_config['player']['geoblock_group'] ) {
    $geoblock_group = explode(',', $aaparser_config['player']['geoblock_group']);
    if (in_array($member_id['user_group'], $geoblock_group)) $geoblock = '&geoblock='.$playlist[0]['geoblock'];
    else $geoblock = '';
} else $geoblock = '';

if ($playlist && $action == 'load_player') {
	$translators = $seasons = '';
	$hoverik_used = [];
    if (isset($aaparser_config['player']['auto_next']) && $aaparser_config['player']['auto_next'] == 1) $autonext = 'yes';
	else $autonext = 'no';
    $ajax_player = '<div id="player" class="b-player" style="text-align: center;" data-autonext="'. $autonext .'">';
    $episodes = '<div class="prenext"><div class="prevpl" onclick="prevpl();">&lsaquo;</div><div id="simple-episodes-tabs">';
    $season_num = $episode_numb = '';
    $show_seasons = false;
    $show_seasons_attr = ' data-hide_seasons="yes"';
    
    if ($playlist[0]['translator_name']) {
        
        //Проверяем нужно ли показывать кнопки сезонов
        
        foreach ($playlist as $tempnum => $temptranslation) {
            if ( is_array($playlist[$tempnum]['episodes']) && count($playlist[$tempnum]['episodes']) > 1 ) {
                $show_seasons = true;
                $show_seasons_attr = ' data-hide_seasons="no"';
            }
        }
        
        //Кнопки озвучек
        
        $translator_num = 0;
        foreach ($playlist as $num => $translation) {
            if ( isset($last_translator) ) {
                if ( $last_translator == $translation['translator_name'] ) {
                    $active_tr = " active";
                    $translator_num = $num;
                } else $active_tr = "";
            } else {
                if ( $num == 0 ) $active_tr = " active";
                else $active_tr = "";
            }
            if ( $aaparser_config['player']['hide_episodes'] == 1 || !$playlist[$num]['episodes']) $translators .= '<li onclick="kodik_translates_alt();" class="b-translator__item'.$active_tr.'" data-this_link="'.$translation['translator_link'].'?translations=false&only_translations='.$translation['translator_id'].$add_params.$geoblock.'">'.$translation['translator_name'].'</li>';
            else $translators .= '<li onclick="kodik_translates();" class="b-translator__item'.$active_tr.'" data-this_translator="'.$translation['translator_id'].'">'.$translation['translator_name'].'</li>';
            
            //Кнопки сезонов
            if ($playlist[$num]['episodes'] !== null) {
				if ( $show_seasons === true && $active_tr ) $seasons .= '<ul data-count="'.count($playlist[$num]['episodes']).'" class="season-tab-'.$translation['translator_id'].' b-simple_seasons__list clearfix">';
				else $seasons .= '<ul data-count="'.count($playlist[$num]['episodes']).'" class="season-tab-'.$translation['translator_id'].' b-simple_seasons__list clearfix" style="display:none">';
            }
        
            foreach ($playlist[$num]['episodes'] as $season => $episode) {
            
                if ( isset($last_season) ) {
                    if ( $last_season == $season && $active_tr ) {
                        $active_szn = " active";
                        $season_num = 'not_first';
                    } else $active_szn = "";
                } else {
                    if ( $playlist[$num]['max_season'] == $season && $active_tr ) {
                        $active_szn = " active";
                        $season_num = 'not_first';
                    } else $active_szn = "";
                }
                
                if ( $season == 0 ) $seasons .= '<li id="season-'.$playlist[$num]['translator_id'].'-'.$season.'" onclick="kodik_seasons();" class="b-simple_season__item' . $active_szn . '" data-this_season="' . $season . '" data-this_translator="' . $playlist[$num]['translator_id'] . '">Спешлы</li>';
                else $seasons .= '<li id="season-'.$playlist[$num]['translator_id'].'-'.$season.'" onclick="kodik_seasons();" class="b-simple_season__item' . $active_szn . '" data-this_season="' . $season . '" data-this_translator="' . $playlist[$num]['translator_id'] . '">Сезон ' . $season . '</li>';
            
                //Кнопки серий
                
                if ( isset($aaparser_config['player']['vertical_eps']) && $aaparser_config['player']['vertical_eps'] && count($episode) > $aaparser_config['player']['vertical_eps'] ) $sub_class = ' show-flex-grid';
                else $sub_class = '';
            
                if ( $active_tr && $active_szn ) $episodes .= '<ul id="episodes-tab-'.$translation['translator_id'].'-'.$season.'" class="episode-tab-'.$translation['translator_id'].'-'.$season.' b-simple_episodes__list clearfix'.$sub_class.'">';
                else $episodes .= '<ul id="episodes-tab-'.$translation['translator_id'].'-'.$season.'" class="episode-tab-'.$translation['translator_id'].'-'.$season.' b-simple_episodes__list clearfix'.$sub_class.'" style="display:none">';
            
                if ( isset($last_season) ) $season_num = $last_season;
                else $season_num = $playlist[$num]['max_season'];
				
                foreach ($playlist[$num]['episodes'][$season] as $episode_num => $episode_title) {
					if(isset($aaparser_config['player']['sw_setting']) && $aaparser_config['player']['sw_setting']) {
						$takedcookies = $_COOKIE["kodik_newsid_".$news_id."_episode_".$episode_num];
						if ($takedcookies !== null) {
							$sw_cookies = (json_decode($takedcookies, true));
							$sw_time = isset($sw_cookies['time']) && $sw_cookies['time'] > 0 ? floor($sw_cookies['time'] / 60) : 0;
							$sw_duration = isset($sw_cookies['duration']) && $sw_cookies['duration'] > 0 ? floor($sw_cookies['duration'] / 60) . "мин." : 0;
							if (isset($sw_cookies['time']) && isset($sw_cookies['duration']) && $sw_cookies['time'] > 0 && $sw_cookies['duration'] > 0) {
								$progress = round(($sw_cookies['time'] / (int)$sw_cookies['duration']) * 100);
							} else $progress = 0;
							// echo "<br>Episode->(".$episode_num."), Time->(".$sw_time."), Duration->(".$sw_duration."), Progress->(".$progress.")"; // Test echo
						} else unset($sw_time, $sw_duration, $progress);
						if ($progress != 0 && !in_array($episode_num, $hoverik_used)) {
							$hoverik_used[] = $episode_num;
							$hoverik .= "<div class='sw_hover' data-sw_episode='".$episode_num."'><p>Вы посмотрели</p><p>".$sw_time." из ".$sw_duration."</p><progress min='0' max='100' value='".$progress."'>%".$progress."</progress></div>";
						} elseif (!in_array($episode_num, $hoverik_used)) {
							$hoverik_used[] = $episode_num; 
							$hoverik .= "<div class='sw_hover' hidden style='display:none' data-sw_episode='".$episode_num."'><p>Вы ещё не смотрели</p><progress min='0' max='100' value='0' hidden>%0</progress></div>";
						}
					}
                    if ( isset($last_episode) ) {
                        if ( $last_episode == $episode_num && $active_tr && $active_szn ) {
                            $active_epzd = " active";
                            $episode_numb = 'not_first';
                        } else $active_epzd = "";
                    } elseif ( $aaparser_config['player']['last_episode'] == 1 ) {
                        if ( $playlist[$translator_num]['max_episode'] == $episode_num && $active_tr && $active_szn ) {
                            $active_epzd = " active";
                            $play_episode = $episode_num;
                            $episode_numb = 'not_first';
                        } else $active_epzd = "";
                    } else {
                        if ( !$play_episode && $active_tr && $active_szn ) {
                            $active_epzd = " active";
                            $play_episode = $episode_num;
                            $episode_numb = 'not_first';
                        } else $active_epzd = "";
                    }
					
                    $episodes .= '<li id="episode-'.$season.'-'.$episode_num.'-'.$playlist[$num]['translator_id'].'" onclick="kodik_episodes();" class="b-simple_episode__item' . $active_epzd . '" data-this_season="' . $season . '" data-this_episode="' . $episode_num . '" data-this_translator="' . $playlist[$num]['translator_id'] . '" data-this_link="'.$translation['translator_link'].'?season='.$season.'&episode='.$episode_num.'&only_translations='.$translation['translator_id'].'&hide_selectors=true'.$add_params.$geoblock.'">Серия ' . $episode_title .'</li>';
                }
                $episodes .= '</ul>';
            
            }
        
            $seasons .= '</ul>';
            
        }
        $translators = '<div class="b-translators__block"><div class="b-translators__title">В переводе:</div><ul id="translators-list" class="b-translators__list">'.$translators.'</ul></div>';
        
    }
    $episodes .= '</div><div class="nextpl" onclick="nextpl();">&rsaquo;</div><div class="sw_hidden_for_player">'.$hoverik.'</div></div>';
    
    if (isset($last_season) && isset($last_translator) && isset($last_episode) && $show_seasons === true) {
        $lastepisodeout = '<div class="b-post__lastepisodeout"><h2><i class="'.$aaparser_config['player']['fa_icons'].' fa-eye" style="font-size: 20px !important;"></i>  ' . $serial_name . '<span id="les">. Вы остановились на ' . $last_season . ' сезоне ' . $last_episode . ' серии в озвучке «' . $last_translator . '»</span><i class="'.$aaparser_config['player']['fa_icons'].' fa-trash" onclick="del('.$news_id.');" id="lesc" title="Удалить отметку"></i></h2> </div>';
    }
    elseif (isset($last_season) && isset($last_translator) && isset($last_episode)) {
        $lastepisodeout = '<div class="b-post__lastepisodeout"><h2><i class="'.$aaparser_config['player']['fa_icons'].' fa-eye" style="font-size: 20px !important;"></i>  ' . $serial_name . '<span id="les">. Вы остановились на ' . $last_episode . ' серии в озвучке «' . $last_translator . '»</span><i class="'.$aaparser_config['player']['fa_icons'].' fa-trash" onclick="del('.$news_id.');" id="lesc" title="Удалить отметку"></i></h2> </div>';
    }
    else $lastepisodeout = '';
    
    if ( $aaparser_config['player']['buttons'] == 1 && $playlist[$translator_num]['translator_link'] ) {
        
        if ( $aaparser_config['player']['hide_episodes'] != 1 ) {
            
            if ( $last_season > 0 ) $this_season = "&season=".$last_season;
            else $this_season = "&season=".$playlist[$translator_num]['max_season'];
            if ( $last_episode > 0 ) $this_episode = "&episode=".$last_episode;
            else $this_episode = "&episode=".$play_episode;
            if ( $last_translator > 0 ) $this_translator = "&only_translations=".$last_translator;
            else $this_translator = "&only_translations=".$playlist[$translator_num]['translator_id'];
            
            $iframe_url = $playlist[$translator_num]['translator_link'].'?hide_selectors=true'.$add_params.$geoblock;
        }
        elseif ( $aaparser_config['player']['hide_episodes'] == 1 ) {
            
            $this_season = $last_translator = '';
            if ( $last_translator > 0 ) $this_translator = "&only_translations=".$last_translator;
            else $this_translator = "&only_translations=".$playlist[$translator_num]['translator_id'];
            
            $iframe_url = $playlist[$translator_num]['translator_link'].'?translations=false'.$add_params.$geoblock;
        } else {
            
            if ( $last_season > 0 ) $this_season = "?season=".$last_season.$add_params.$geoblock;
            else $this_season = "?season=".$playlist[$translator_num]['max_season'].$add_params.$geoblock;
            if ( $last_episode > 0 ) $this_episode = "&only_episode=true&episode=".$last_episode;
            else $this_episode = "&only_episode=true&episode=".$playlist[$translator_num]['max_episode'];
            $this_translator = '';
            
            $iframe_url = $playlist[$translator_num]['translator_link'];
        }
        $iframe .= '<div id="ibox"><div id="player-loader-overlay"></div><div id="player_kodik"'.$show_seasons_attr.' style="height: 100%; margin: 0 auto; width: 100%;"><iframe src="'.$iframe_url.$this_season.$this_translator.$this_episode.'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe></div>';
    
        if ($aaparser_config['player']['hide_episodes'] != 1 && $playlist[$num]['episodes']) $ajax_player = $ajax_player . $seasons . $iframe . $episodes;
		elseif ($aaparser_config['player']['hide_episodes'] != 1 && !$playlist[$num]['episodes'])  $ajax_player = $ajax_player . $seasons . $iframe;
        else $ajax_player = $ajax_player . $iframe;

        $ajax_player .= '</div></div>';
    
        $ajax_player = $lastepisodeout . $translators . $ajax_player;
    
    } else {
        
        if ( $playlist[$translator_num]['translator_link'] ) $iframe_url = $playlist[$translator_num]['translator_link'];
        else $iframe_url = $playlist[$translator_num]['translator_link'];
        $iframe .= '<div id="ibox"><div id="player-loader-overlay"></div><div id="player_kodik"'.$show_seasons_attr.' style="height: 100%; margin: 0 auto; width: 100%;"><iframe src="'.$iframe_url.'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe></div>';
        
        $ajax_player = $ajax_player . $iframe;
        $ajax_player .= '</div></div>';
        $ajax_player = $lastepisodeout . $ajax_player;
    }

    if ( isset($kodik_playlist_fullstory) && $kodik_playlist_fullstory == 'yes' ) {
        $tpl->set( '{kodik_playlist}', '<div id="kodik_player_ajax" data-news_id="'.$row['id'].'" data-has_cache="yes" '.$sw_player_cookie.'>'.$ajax_player.'</div>' );
    } else echo $ajax_player;
    
} elseif ( isset($kodik_playlist_fullstory) && $kodik_playlist_fullstory == 'yes' ) {
    if ( $aaparser_config['player']['preloader'] ) $tpl->set( '{kodik_playlist}', '<div id="kodik_player_ajax" data-news_id="'.$row['id'].'" data-has_cache="no" '.$sw_player_cookie.'><div class="loading-kodik"><div class="arc"></div><div class="arc"></div><div class="arc"></div></div></div>' );
    else $tpl->set( '{kodik_playlist}', '<div id="kodik_player_ajax" data-news_id="'.$row['id'].'" data-has_cache="no" '.$sw_player_cookie.'></div>' );
}