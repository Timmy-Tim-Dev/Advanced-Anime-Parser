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

require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
require_once ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php';
  
header('Content-Type: text/html; charset=utf-8');

function kodik_api($url) {
	if ( $curl = curl_init())
	{
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
	}
	else {
		$out = file_get_contents($url);
		$parse = json_decode($out, true);
	}
	return $parse;
}

$news_id = isset($_POST['news_id']) ? intval($_POST['news_id']) : false;
$api_token = $aaparser_config['settings']['kodik_api_key'] ?? false;
$action = $_POST['action'] ?? 'load_player';

$this_season = isset($_POST['this_season']) ? intval($_POST['this_season']) : false;
$this_episode = isset($_POST['this_episode']) ? intval($_POST['this_episode']) : false;
$this_translator = isset($_POST['this_translator']) ? $_POST['this_translator'] : false;
$active_translator = isset($_POST['active_translator']) ? $_POST['active_translator'] : false;

if ( isset($aaparser_config['settings']['kodik_api_domain']) ) $kodik_api_domain = $aaparser_config['settings']['kodik_api_domain'];
else $kodik_api_domain = 'https://kodikapi.com/';
    
if ( isset($aaparser_config['player']['custom_cache']) && $aaparser_config['player']['custom_cache'] == 1 ) $playlist = kodik_cache('playlist_'.$news_id, false, 'player');
else $playlist = dle_cache('kodik_playlist', $news_id, false);

if ( $aaparser_config['player']['enable'] != 1 ) die('stop');
elseif ( !$api_token ) die('stop');
elseif ( !$aaparser_config['fields']['xf_shikimori_id'] && !$aaparser_config['fields']['xf_mdl_id'] && !$aaparser_config['player']['worldart_anime'] && !$aaparser_config['player']['worldart_cinema'] && !$aaparser_config['player']['kinopoisk_id'] && !$aaparser_config['player']['imdb_id'] ) die('stop');
elseif ( $playlist !== false ) $playlist = json_decode($playlist, true);
elseif ( $news_id ) {
    
    $news_row = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE id={$news_id}" );
    if ( !$news_row['id'] ) die('stop');
    
    $post_fields = xfieldsdataload($news_row['xfields']);
    
    $translations_priority = $aaparser_config['player']['translations_priority'] ? '&prioritize_translations='.$aaparser_config['player']['translations_priority'] : '';
    $translations_unpriority = $aaparser_config['player']['translations_unpriority'] ? '&unprioritize_translations='.$aaparser_config['player']['translations_unpriority'] : '';
    $translations_hide = $aaparser_config['player']['translations_hide'] ? '&block_translations='.$aaparser_config['player']['translations_hide'] : '';
    
    if ( $aaparser_config['fields']['xf_shikimori_id'] && $post_fields[$aaparser_config['fields']['xf_shikimori_id']] ) {
        $translations_priority = $aaparser_config['player']['translations_priority'] ? '&prioritize_translations='.$aaparser_config['player']['translations_priority'] : '';
        $translations_unpriority = $aaparser_config['player']['translations_unpriority'] ? '&unprioritize_translations='.$aaparser_config['player']['translations_unpriority'] : '';
        $translations_hide = $aaparser_config['player']['translations_hide'] ? '&block_translations='.$aaparser_config['player']['translations_hide'] : '';
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&shikimori_id='.$post_fields[$aaparser_config['fields']['xf_shikimori_id']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
    }
    elseif ( $aaparser_config['fields']['xf_mdl_id'] && $post_fields[$aaparser_config['fields']['xf_mdl_id']] ) {
        $translations_priority = $aaparser_config['player']['translations_priority_dorama'] ? '&prioritize_translations='.$aaparser_config['player']['translations_priority_dorama'] : '';
        $translations_unpriority = $aaparser_config['player']['translations_unpriority_dorama'] ? '&unprioritize_translations='.$aaparser_config['player']['translations_unpriority_dorama'] : '';
        $translations_hide = $aaparser_config['player']['translations_hide_dorama'] ? '&block_translations='.$aaparser_config['player']['translations_hide_dorama'] : '';
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&mdl_id='.$post_fields[$aaparser_config['fields']['xf_mdl_id']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
    }
    elseif ( $aaparser_config['player']['worldart_anime'] && $post_fields[$aaparser_config['player']['worldart_anime']] ) {
        $translations_priority = $aaparser_config['player']['translations_priority'] ? '&prioritize_translations='.$aaparser_config['player']['translations_priority'] : '';
        $translations_unpriority = $aaparser_config['player']['translations_unpriority'] ? '&unprioritize_translations='.$aaparser_config['player']['translations_unpriority'] : '';
        $translations_hide = $aaparser_config['player']['translations_hide'] ? '&block_translations='.$aaparser_config['player']['translations_hide'] : '';
        if ( stripos($post_fields[$aaparser_config['player']['worldart_anime']], 'world-art.ru') !== false )
            $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_link='.$post_fields[$aaparser_config['player']['worldart_anime']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
        else $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_animation_id='.$post_fields[$aaparser_config['player']['worldart_anime']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
    }
    elseif ( $aaparser_config['player']['worldart_cinema'] && $post_fields[$aaparser_config['player']['worldart_cinema']] ) {
        $translations_priority = $aaparser_config['player']['translations_priority_dorama'] ? '&prioritize_translations='.$aaparser_config['player']['translations_priority_dorama'] : '';
        $translations_unpriority = $aaparser_config['player']['translations_unpriority_dorama'] ? '&unprioritize_translations='.$aaparser_config['player']['translations_unpriority_dorama'] : '';
        $translations_hide = $aaparser_config['player']['translations_hide_dorama'] ? '&block_translations='.$aaparser_config['player']['translations_hide_dorama'] : '';
        if ( stripos($post_fields[$aaparser_config['player']['worldart_cinema']], 'world-art.ru') !== false )
            $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_link='.$post_fields[$aaparser_config['player']['worldart_cinema']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
        else $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&worldart_cinema_id='.$post_fields[$aaparser_config['player']['worldart_cinema']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
    }
    elseif ( $aaparser_config['player']['kinopoisk_id'] && $post_fields[$aaparser_config['player']['kinopoisk_id']] )
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&kinopoisk_id='.$post_fields[$aaparser_config['player']['kinopoisk_id']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
    elseif ( $aaparser_config['player']['imdb_id'] && $post_fields[$aaparser_config['player']['imdb_id']] )
        $kodik = kodik_api( $kodik_api_domain.'search?token='.$api_token.'&imdb_id='.$post_fields[$aaparser_config['player']['imdb_id']].'&with_episodes=true'.$translations_priority.$translations_unpriority.$translations_hide);
        
    if ( $kodik['results'] ) {
        if ( isset($kodik['results'][0]['last_episode']) ) {
            $playlist = array();
            foreach ($kodik['results'] as $num => $translators) {
                foreach ( $translators['seasons'] as $season => $episode ) {
                    foreach ( $episode['episodes'] as $ep_num => $episode_links ) {
                        $playlist['episodes'][$season][$ep_num][$translators['translation']['title']]['translation_name'] = $translators['translation']['title'];
                        $playlist['episodes'][$season][$ep_num][$translators['translation']['title']]['translation_id'] = $translators['translation']['id'];
                        $playlist['episodes'][$season][$ep_num][$translators['translation']['title']]['link'] = $translators['link'].'?season='.$season.'&episode='.$season.'&only_translations='.$translators['translation']['id'];
                    }
                }
            }
            ksort($playlist['episodes']);
            foreach ( $playlist['episodes'] as $snum => $elist ) {
                ksort($playlist['episodes'][$snum]);
            }
        }
        else {
            $playlist = array();
            foreach ($kodik['results'] as $num => $translators) {
                $playlist['movie'][$translators['translation']['title']]['translation_name'] = $translators['translation']['title'];
                $playlist['movie'][$translators['translation']['title']]['translation_id'] = $translators['translation']['id'];
                $playlist['movie'][$translators['translation']['title']]['link'] = $translators['link'].'?only_translations='.$translators['translation']['id'].'&hide_selectors=true';
            }
        }
        
        $playlist['serial_name'] = $kodik['results'][0]['title'];
        $playlist['iframe'] = $kodik['results'][0]['link'];
        if ( $aaparser_config['player']['add_params'] && $post_fields[$aaparser_config['player']['add_params']] ) $playlist['my_params'] = $post_fields[$aaparser_config['player']['add_params']];
        if ( $aaparser_config['player']['geoblock'] && $post_fields[$aaparser_config['player']['geoblock']] ) $playlist['geoblock'] = $post_fields[$aaparser_config['player']['geoblock']];
        if ( isset($aaparser_config['player']['custom_cache']) && $aaparser_config['player']['custom_cache'] == 1 ) kodik_create_cache('playlist_'.$news_id, json_encode($playlist, JSON_UNESCAPED_UNICODE), false, 'player');
        else create_cache('kodik_playlist', json_encode($playlist, JSON_UNESCAPED_UNICODE), $news_id, false);
        unset($translators);
        unset($episode);
        unset($kodik);
        unset($max_episodes);
        unset($max_seasons);
        unset($max_episode);
        unset($max_season);
    }
}

$serial_name = $playlist['serial_name'];
$add_params = isset($playlist['my_params']) ? '&'.$playlist['my_params'] : '';
$geoblock = isset($playlist['geoblock']) ? '&geoblock='.$playlist['geoblock'] : '';

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


if ( $playlist['geoblock'] && $aaparser_config['player']['geoblock_group'] ) {
    $geoblock_group = explode(',', $aaparser_config['player']['geoblock_group']);
    $geo_block = false;
    if (in_array($member_id['user_group'], $geoblock_group)) $geoblock = '&geoblock='.$playlist['geoblock'];
    else $geoblock = '';
}
else $geoblock = '';

if ($playlist['movie'] && $action == 'load_player') {
    
    //Кнопки озвучек
    
    $iframe_link = '';
    
    foreach ($playlist['movie'] as $translation => $episode_data) {
        if ( isset($last_translator) ) {
            if ( $last_translator == $episode_data['translation_name'] ) {
                $active_tr = " active";
                $iframe_link = $episode_data['link'];
            }
            else $active_tr = "";
        }
        else {
            if ( !$iframe_link ) {
                $active_tr = " active";
                $iframe_link = $episode_data['link'];
            }
            else $active_tr = "";
        }
        $translators .= '<li id="translator-'.$episode_data['translation_id'].'" onclick="kodik_translates();" class="b-translator__item'.$active_tr.'" data-this_link="'.$episode_data['link'].'" data-this_translator="'.$episode_data['translation_name'].'">'.$episode_data['translation_name'].'</li>';
    }
    $translators = '<div class="b-translators__block"><div class="b-translators__title">В переводе:</div><ul id="translators-list" class="b-translators__list">'.$translators.'</ul></div>';

    $ajax_player = '<div id="player" class="b-player" style="text-align: center;">';
    
    if ( $aaparser_config['player']['buttons'] == 1 && $iframe_link ) {
        
        $iframe_link.$add_params.$geoblock;
        
        $iframe .= '<div id="ibox"><div id="player_kodik" style="height: 100%; margin: 0 auto; width: 100%;"><iframe src="'.$iframe_url.'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe></div>';
    
        $ajax_player = $ajax_player . $iframe;

        $ajax_player .= '</div></div>';
    
        $ajax_player = $translators . $ajax_player;
    
    }
    else {
        
        $iframe_url = $playlist['iframe'].$add_params.$geoblock;
        $iframe .= '<div id="ibox"><div id="player_kodik" style="height: 100%; margin: 0 auto; width: 100%;"><iframe src="'.$iframe_url.'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe></div>';
        
        $ajax_player = $ajax_player . $iframe;
        $ajax_player .= '</div></div>';
    }

    echo $ajax_player;
    
}
elseif ($playlist['episodes'] && $action == 'load_player') {
    
    //Кнопки сезонов
    
    if ( count($playlist['episodes']) > 1 ) $show_seasons = true;
    else $show_seasons = false;
    
    $seasons = '<ul id="simple-seasons-tabs" class="b-simple_seasons__list clearfix">';
    $episodes = '<div class="prenext"><div class="prevpl" onclick="prevpl();">&lsaquo;</div><div id="simple-episodes-tabs">';
    $season_num = "";
    $episode_num = '';
    $tr_id = '';
    $translators = '';
    
    foreach ( $playlist['episodes'] as $season => $episodes_arr ) {
        if ( isset($last_season) ) {
            if ( $last_season == $season ) {
                $active_szn = " active";
                $season_num = $season;
            }
            else {
                $active_szn = "";
            }
        }
        else {
            if ( !$season_num && $season_num != 0 ) {
                $active_szn = " active";
                $season_num = $season;
            }
            else {
                $active_szn = "";
            }
        }
                
        if ( $season == 0 ) $seasons .= '<li id="season-'.$season.'" onclick="kodik_seasons();" class="b-simple_season__item' . $active_szn . '" data-this_season="' . $season . '">Спешлы</li>';
        else $seasons .= '<li id="season-'.$season.'" onclick="kodik_seasons();" class="b-simple_season__item' . $active_szn . '" data-this_season="' . $season . '">Сезон ' . $season . '</li>';
        
        //Кнопки серий
    
        $iframe_sublink = '';
        
        if ( isset($aaparser_config['player']['vertical_eps']) && $aaparser_config['player']['vertical_eps'] && count($episodes_arr) > $aaparser_config['player']['vertical_eps'] ) $sub_class = ' show-flex-grid';
        else $sub_class = '';
    
        if ( !$episode_num ) $episodes .= '<ul id="simple-episodes-list" class="season-tab-'.$season.' b-simple_episodes__list clearfix'.$sub_class.'">';
        else $episodes .= '<ul id="simple-episodes-list" class="season-tab-'.$season.' b-simple_episodes__list clearfix'.$sub_class.'" style="display:none">';
    
        foreach ( $episodes_arr as $episode => $translation_arr ) {
            if ( isset($last_episode) ) {
                if ( $last_episode == $episode ) {
                    $active_epzd = " active";
                    $episode_num = $episode;
                    $iframe_sublink = $playlist['iframe'].'?season='.$season.'&episode='.$episode.'&only_episode=true';
                }
                else $active_epzd = "";
            }
            elseif ( $aaparser_config['player']['last_episode'] == 1 ) {
                if ( max(array_keys($playlist['episodes'][$season])) == $episode ) {
                    $active_epzd = " active";
                    $episode_num = $episode;
                    $iframe_sublink = $playlist['iframe'].'?season='.$season.'&episode='.$episode.'&only_episode=true';
                }
                else $active_epzd = "";
            }
            else {
                if ( !$episode_num && $episode_num != 0 ) {
                    $active_epzd = " active";
                    $episode_num = $episode;
                    $iframe_sublink = $playlist['iframe'].'?season='.$season.'&episode='.$episode.'&only_episode=true';
                }
                else $active_epzd = "";
            }
        
            $episodes .= '<li id="episode-'.$season.'-'.$episode.'" onclick="kodik_episodes();" class="b-simple_episode__item' . $active_epzd . '" data-this_season="' . $season . '" data-this_episode="' . $episode . '">Серия ' . $episode . '</li>';
            
            
            //Кнопки озвучек
                
            if ( !$tr_id && $active_epzd ) $translators .= '<ul id="translators-list" class="translation-tab-'.$season.'-'.$episode.' b-translators__list">';
            else $translators .= '<ul id="translators-list" class="translation-tab-'.$season.'-'.$episode.' b-translators__list" style="display:none">';
    
            foreach ($translation_arr as $translation => $episode_data) {
                if ( isset($last_translator) ) {
                    if ( $last_translator == $episode_data['translation_name'] && $active_epzd ) {
                        $active_tr = " active";
                        $tr_id = $episode_data['translation_id'];
                    }
                    else $active_tr = "";
                }
                else {
                    if ( !$tr_id && $active_epzd ) {
                        $active_tr = " active";
                        $tr_id = $episode_data['translation_id'];
                    }
                    else $active_tr = "";
                }
                $translators .= '<li id="translation-'.$season.'-'.$episode.'-'.$episode_data['translation_id'].'" onclick="kodik_translates();" class="b-translator__item'.$active_tr.'" data-this_link="'.$episode_data['link'].'&hide_selectors=true'.$add_params.$geoblock.'" data-this_translator="'.$episode_data['translation_name'].'" data-this_translator_id="'.$episode_data['translation_id'].'">'.$episode_data['translation_name'].'</li>';
            }  
            $translators = $translators.'</ul>';
            
        }
        $episodes .= '</ul>';
        
    }
    
    $seasons .= '</ul>';
    $episodes .= '</div><div class="nextpl" onclick="nextpl();">&rsaquo;</div></div>';
    
    $translators = '<div class="b-translators__block"><div class="b-translators__title">В переводе:</div>'.$translators.'</div>';
    
    //Запоминание серии
    
    if (isset($last_season) && isset($last_translator) && isset($last_episode) && $show_seasons === true) {
        $lastepisodeout = '<div class="b-post__lastepisodeout"><h2><i class="fa fa-eye" style="font-size: 20px !important;"></i>  ' . $serial_name . '<span id="les">. Вы остановились на ' . $last_season . ' сезоне ' . $last_episode . ' серии в озвучке «' . $last_translator . '»</span><i class="fa fa-trash" onclick="del('.$news_id.');" id="lesc" title="Удалить отметку"></i></h2> </div>';
    }
    elseif (isset($last_season) && isset($last_translator) && isset($last_episode)) {
        $lastepisodeout = '<div class="b-post__lastepisodeout"><h2><i class="fa fa-eye" style="font-size: 20px !important;"></i>  ' . $serial_name . '<span id="les">. Вы остановились на ' . $last_episode . ' серии в озвучке «' . $last_translator . '»</span><i class="fa fa-trash" onclick="del('.$news_id.');" id="lesc" title="Удалить отметку"></i></h2> </div>';
    }
    else $lastepisodeout = '';

    $ajax_player = '<div id="player" class="b-player" style="text-align: center;">';
    
    if ( $aaparser_config['player']['buttons'] == 1 ) {
        
        $iframe_url = $playlist['iframe'].'?season='.$season_num.'&episode='.$episode_num.'&only_translations='.$tr_id.'&hide_selectors=true'.$add_params.$geoblock;
        
        $iframe .= '<div id="ibox"><div id="player_kodik" style="height: 100%; margin: 0 auto; width: 100%;"><iframe src="'.$iframe_url.'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe></div>';
    
        if ( $show_seasons === true ) $ajax_player = $ajax_player . $seasons . $iframe . $episodes;
        else $ajax_player = $ajax_player . $iframe . $episodes;

        $ajax_player .= '</div></div>';
    
        $ajax_player = $lastepisodeout . $translators . $ajax_player;
    
    }
    else {
        
        $iframe_url = $playlist['iframe'].$add_params.$geoblock;
        $iframe .= '<div id="ibox"><div id="player_kodik" style="height: 100%; margin: 0 auto; width: 100%;"><iframe src="'.$iframe_url.'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe></div>';
        
        $ajax_player = $ajax_player . $iframe;
        $ajax_player .= '</div></div>';
        $ajax_player = $lastepisodeout . $ajax_player;
    }

    echo $ajax_player;
    
}