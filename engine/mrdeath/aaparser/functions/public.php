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

require_once ENGINE_DIR . '/mrdeath/aaparser/functions/tags_list.php';
$data_list = array_unique(array_merge($data_list,$data_list_kodik));
