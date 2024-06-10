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

function convert_date($date, $type) {
    if ( $type == 1 ) {
        $date_mas = explode("-", $date);
        return $date_mas[2].".".$date_mas[1].".".$date_mas[0];
    } elseif ( $type == 2 ) {
        $date_mas = explode("-", $date);
        $month_mas = [
            "01" => " января ",
            "02" => " февраля ",
            "03" => " марта ",
            "04" => " апреля ",
            "05" => " мая ",
            "06" => " июня ",
            "07" => " июля ",
            "08" => " августа ",
            "09" => " сентября ",
            "10" => " октября ",
            "11" => " ноября ",
            "12" => " декабря ",
        ];
        return intval($date_mas[2]).$month_mas[$date_mas[1]].$date_mas[0];
    }
    elseif ( $type == 3 ) {
        $date_mas = explode("-", $date);
        return $date_mas[2]."-".$date_mas[1]."-".$date_mas[0];
    }
}

function unique_multidim_array($array, $key) : array {
    $uniq_array = $key_array = array();

    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[] = $val[$key];
            $uniq_array[] = $val;
        }
    }
    return $uniq_array;
}

function in_arrayi($needle, $haystack) { return in_array(strtolower($needle), array_map('strtolower', $haystack)); }

function convert_duration($duration, $type) {
    if ( $type == 1 ) return ($duration*60);
    elseif ( $type == 2 ) {
        $hours = floor($duration / 60);
        $minutes = $duration % 60;
        if ( $hours == 0 ) return $minutes.' мин.';
        elseif ( $hours == 1 ) return $hours.' час '.$minutes.' мин.';
        else return $hours.' часа '.$minutes.' мин.';
    }
    elseif ( $type == 3 ) {
        $hours = floor($duration / 60);
        $minutes = $duration % 60;
        if ( $minutes < 10 ) $minutes = '0'.$minutes;
        if ( $hours == 0 ) return $minutes.':00';
        else return $hours.':'.$minutes.':00';
    }
}

$data_list = array( "shikimori_name", "shikimori_russian", "shikimori_english", "shikimori_japanese", "shikimori_synonyms", "shikimori_license_name_ru", "shikimori_kind", "shikimori_kind_ru", "shikimori_score", "shikimori_votes", "shikimori_status", "shikimori_status_ru", "shikimori_episodes", "shikimori_episodes_aired", "shikimori_episodes_aired_1", "shikimori_episodes_aired_2", "shikimori_episodes_aired_3", "shikimori_episodes_aired_4", "shikimori_episodes_aired_5", "shikimori_episodes_aired_6", "shikimori_episodes_aired_7", "shikimori_episodes_aired_8", "shikimori_aired_on", "shikimori_aired_on_2", "shikimori_aired_on_3", "shikimori_aired_on_4", "shikimori_released_on", "shikimori_released_on_2", "shikimori_released_on_3", "shikimori_released_on_4", "shikimori_year", "shikimori_season", "shikimori_rating", "shikimori_duration", "shikimori_duration_2", "shikimori_duration_3", "shikimori_duration_4", "shikimori_plot", "shikimori_genres", "shikimori_studios", "shikimori_videos", "myanimelist_id", "official_site", "wikipedia", "anime_news_network", "anime_db", "world_art", "kinopoisk", "kage_project", "shikimori_director", "shikimori_producer", "shikimori_script", "shikimori_composition", "shikimori_franshise", "shikimori_similar", "shikimori_related", "shikimori_id", "mydramalist_id", "image", "kadr_1", "kadr_2", "kadr_3", "kadr_4", "kadr_5", "kodik_title", "kodik_title_orig", "kodik_other_title", "kodik_year", "kodik_worldart_link", "kodik_mydramalist_tags", "kodik_status_en", "kodik_status_ru", "kodik_premiere_ru", "kodik_premiere_ru_2", "kodik_premiere_ru_3", "kodik_premiere_ru_4", "kodik_premiere_world", "kodik_premiere_world_2", "kodik_premiere_world_3", "kodik_premiere_world_4", "kodik_iframe", "kodik_quality", "kodik_kinopoisk_id", "kodik_imdb_id", "kodik_translation", "kodik_translation_last", "kodik_translation_types", "kodik_translation_types_ru", "kodik_tagline", "kodik_plot", "kodik_duration", "kodik_duration_2", "kodik_duration_3", "kodik_duration_4", "kodik_video_type", "kodik_countries", "kodik_genres", "kodik_kinopoisk_rating", "kodik_kinopoisk_votes", "kodik_imdb_rating", "kodik_imdb_votes", "kodik_mydramalist_rating", "kodik_mydramalist_votes", "kodik_minimal_age", "kodik_rating_mpaa", "kodik_actors", "kodik_directors", "kodik_producers", "kodik_writers", "kodik_composers", "kodik_editors", "kodik_designers", "kodik_operators", "kodik_last_season", "kodik_last_season_1", "kodik_last_season_2", "kodik_last_season_3", "kodik_last_season_4", "kodik_last_season_5", "kodik_last_season_6", "kodik_last_season_7", "kodik_last_season_8", "kodik_last_season_translated", "kodik_last_season_subtitles", "kodik_last_episode", "kodik_last_episode_1", "kodik_last_episode_2", "kodik_last_episode_3", "kodik_last_episode_4", "kodik_last_episode_5", "kodik_last_episode_6", "kodik_last_episode_7", "kodik_last_episode_8", "kodik_last_episode_translated", "kodik_last_episode_subtitles", "kodik_episodes_total", "kodik_episodes_aired", "catalog_rus", "catalog_eng" );