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
	$time_donor_start = microtime(true);
	$stage = $stage ?? 1;
	echo "=================================<br/>Начинаем инициализацию донора world_art.php<br/>";
}
	if ( (isset($xfields_data['world_art']) && $xfields_data['world_art']) || (isset($xfields_data['kodik_worldart_link']) && $xfields_data['kodik_worldart_link']) ) {
	    
	    $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6,fr;q=0.5',
            'Connection: close',
            'Cookie: Apache=46.250.28.197.712171565702525183; fid=ef079e25-79f7-4a8e-bce7-c2a7eeb0b7c4; _ym_uid=1570117485361059967; _ym_d=1570117485; __utmz=145037234.1570197432.3.2.utmcsr=moonwalk.cc|utmccn=(referral)|utmcmd=referral|utmcct=/partners/serials_updates; __utma=145037234.1522562346.1565702525.1574876014.1577686921.11; __utmc=145037234; __utmt=1; _ym_isad=1; __utmb=145037234.3.10.1577686921',
            'Host: www.world-art.ru',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36'
        ];
        
        if ( isset($xfields_data['world_art']) && $xfields_data['world_art'] ) $wa_page = LoadPage($xfields_data['world_art'], "GET", $headers);
        else $wa_page = LoadPage($xfields_data['kodik_worldart_link'], "GET", $headers);
        $wa_page = iconv('windows-1251', 'utf-8', $wa_page);
	    if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$time_donor = microtime(true) - $time_donor_start;
			echo "Этап ".$stage.": Парсинг с world-art, прошло (".round($time_donor,4)." секунд)<br/>";
			$stage++;
		}
        if ( strpos($wa_page, '<b>Производство</b>') !== false ) {
            preg_match_all("|<table><tr><td align=left width=145 class='review' Valign=top><b>Производство<\/b><\/td><td width=15><\/td><td class='review' Valign=top>(.*)<\/td><\/tr><\/table>|U", $wa_page, $prod);
            if ( $prod[1][0] ) $xfields_data['worldart_country'] = $prod[1][0];
            else $xfields_data['worldart_country'] = '';
        }
        else $xfields_data['worldart_country'] = '';
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$time_donor = microtime(true) - $time_donor_start;
			echo "Этап ".$stage.": Распределение тега страны, прошло (".round($time_donor,4)." секунд)<br/>";
			$stage++;
		}
        if ( strpos($wa_page, 'newtag') !== false ) {
            $mas1 = explode("<td><div class='newtag'>", $wa_page);
            $mas2 = explode("</div></td>", $mas1[1]);
            $mas3 = explode("style=text-decoration:none>", $mas2[0]);
            $delete = array_shift($mas3);
            foreach ( $mas3 as $tag ) {
                $tag_ex = explode("</a>", $tag);
                $tags[] = html_entity_decode($tag_ex[0]);
            }
            if ( $tags ) $xfields_data['worldart_tags'] = implode(", ", $tags);
            else $xfields_data['worldart_tags'] = '';
        }
        else $xfields_data['worldart_tags'] = '';
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$time_donor = microtime(true) - $time_donor_start;
			echo "Этап ".$stage.": Распределение самих тегов, прошло (".round($time_donor,4)." секунд)<br/>";
			$stage++;
		}
        if ( strpos($wa_page, 'Краткое содержание') !== false AND strpos($wa_page, 'http://www.world-art.ru/animation/animation_update_synopsis.php') === false ) {
            preg_match_all("|Краткое содержание<\/font><\/b><\/td><\/tr><\/table><table width=100% cellspacing=0 cellpadding=0 border=0><tr><td width=100% height=1 bgcolor=#eaeaea><\/td><\/tr><\/table><table width=100% cellspacing=0 cellpadding=2 border=0><tr><td><p align=justify class='review'>(.*)</p>|U", $wa_page, $opis);
            if ( $opis[1][0] ) $xfields_data['worldart_plot'] = strip_tags($opis[1][0]);
            else $xfields_data['worldart_plot'] = '';
        }
        else $xfields_data['worldart_plot'] = '';
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$time_donor = microtime(true) - $time_donor_start;
			echo "Этап ".$stage.": Распределение тега краткое содержание, прошло (".round($time_donor,4)." секунд)<br/>";
			$stage++;
		}
        if ( strpos($wa_page, '<b>Средний балл</b>') !== false ) {
            preg_match_all("|<table><tr><td align=left width=145 class='review'><b>Средний балл</b></td><td width=15></td><td class='review'>(.*)&nbsp;из 10</td></tr></table>|U", $wa_page, $rate);
            if ( $rate[1][0] ) $xfields_data['worldart_rating'] = $rate[1][0];
            else $xfields_data['worldart_rating'] = '';
        }
        else $xfields_data['worldart_rating'] = '';
		if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$time_donor = microtime(true) - $time_donor_start;
			echo "Этап ".$stage.": Распределение тега рейтинга, прошло (".round($time_donor,4)." секунд)<br/>";
			$stage++;
		}
        if ( strpos($wa_page, '<b>Проголосовало</b>') !== false ) {
            preg_match_all("|<table><tr><td align=left width=145 class='review'><b>Проголосовало</b></td><td width=15></td><td class='review'>(.*) чел.</td><td><a href = \"(.*)\"><img src='../img/chart.jpg' width=20'></a></td></tr></table>|U", $wa_page, $vote);
            if ( $vote[1][0] ) $xfields_data['worldart_votes'] = $vote[1][0];
            else $xfields_data['worldart_votes'] = '';
        }
        else $xfields_data['worldart_votes'] = '';
	    if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
			$time_donor = microtime(true) - $time_donor_start;
			echo "Этап ".$stage.": Распределение тега голосов, прошло (".round($time_donor,4)." секунд)<br/>";
			$stage++;
		}
	} else $xfields_data['worldart_country'] = $xfields_data['worldart_tags'] = $xfields_data['worldart_plot'] = $xfields_data['worldart_rating'] = $xfields_data['worldart_votes'] = '';
if($aaparser_config['debugger']['enable'] == 1 && $aaparser_config['debugger']['donors'] == 1 ) { 
	$time_donor = microtime(true) - $time_donor_start;
	echo "Закончили инициализацию донора world_art.php<br/>=================================<br/>";
}