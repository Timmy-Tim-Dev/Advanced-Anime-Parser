<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

 
if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

//Инициализация
ini_set("memory_limit","256M");
ini_set('max_execution_time',300);
$type = $_GET['type'];
$id = $_GET['id'];
$newid = explode('-', $id)[0];
require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/module.php'));
require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));
if ( isset($aaparser_config['settings']['shikimori_api_domain']) ) $shikimori_api_domain = $aaparser_config['settings']['shikimori_api_domain'];
else $shikimori_api_domain = 'https://shikimori.one/';
$shikimori_url_domain = clean_url($shikimori_api_domain);
$site_url_domain = clean_url($config['http_home_url']);
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
$current_url = $protocol . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if ($type == "persons") {
	// Начало
	if (!function_exists('mdl_request')) {
        function mdl_request($url) {
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60 );
            curl_setopt($ch, CURLOPT_TIMEOUT, 60 );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
            $headers = [
    		    'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.2924.87 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
                'Connection: keep-alive',
                'Cache-Control: max-age=0',
                'Upgrade-Insecure-Requests: 1'
		    ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		    $mdl_page = curl_exec ($ch);
		    curl_close ($ch);
  
  		    return $mdl_page;
        }
    }
	
	$dle_module = 'persons';
	if ( isset($aaparser_config['persons']['persons_dorama_page_cache']) && $aaparser_config['persons']['persons_dorama_page_cache'] == 1 ) {
		$json = kodik_cache('persons_'.$newid, false, 'dorama_persons_page');
		$json = json_decode($json, true);
	} else $json = false;
	
	if ($json === false || $json == '') {
		
		// Парсим актёров
	    if (isset($aaparser_config['persons']['personas_other_dorama_api']) && $aaparser_config['persons']['personas_other_dorama_api'] == 1) {		
			$mdl_url = "https://api.allorigins.win/get?url=https://mydramalist.com/people/".$id;
			$pers_request = mdl_request($mdl_url);
			$pers_request = json_decode($pers_request, true);
			$json = $pers_request['contents'];
		} else {
			$mdl_url = "https://mydramalist.com/people/".$id;
			$json = mdl_request($mdl_url);
		}
		$actors = [];
		
		if ( isset($aaparser_config['persons']['persons_dorama_page_cache']) && $aaparser_config['persons']['persons_dorama_page_cache'] == 1 && $json != '') kodik_create_cache('persons_'.$newid, json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), false, 'dorama_persons_page');
	}
	
	//Вытаскиваем данные
	$pers_arr1 = explode('<div class="box-body light-b">', $json);
	$pers_arr2 = explode('</div>', $pers_arr1[1]);
	$pers_arr3 = explode('<ul class="list m-b-0">', $pers_arr2[0]);
	$pers_arr4 = explode('<li class="list-item p-a-0">', $pers_arr3[1]);
	$pers_arr4 = array_map('strip_tags', $pers_arr4);
	//Вытаскиваем описание
	$pers_descr1 = explode('<div class="col-sm-8 col-lg-12 col-md-12">', $json);
	$pers_descr2 = explode('</div>', $pers_descr1[1]);
	//Вытаскиваем дополнительные данные
	$pers_descr3 = explode('<ul class="list m-b-0">', $pers_descr2[0]);
	$pers_descr4 = explode('<li class="list-item p-a-0">', $pers_descr3[1]);
	$pers_descr4 = array_map('strip_tags', $pers_descr4);
	
	$big_arr = array_merge($pers_arr4, $pers_descr4);
	
	$keys = [
		"First Name", "Family Name", "Native name", "Also Known as",
		"Nationality", "Gender", "Born", "Age", "Description", "Name"
	];
	$parsed_arr = array_fill_keys($keys, "");
	
	foreach ($big_arr as $item) {
		if (strpos($item, ": ") !== false) {
			list($key, $value) = explode(": ", $item, 2);
			if (isset($parsed_arr[$key])) {
				$parsed_arr[$key] = $value;
			}
		}
	}
	$months = ["January" => "01", "February" => "02", "March" => "03", "April" => "04",
    "May" => "05", "June" => "06", "July" => "07", "August" => "08",
    "September" => "09", "October" => "10", "November" => "11", "December" => "12"];

	if (!empty($parsed_arr["Born"])) {
		$born = trim($parsed_arr["Born"]);
		if (preg_match('/([A-Za-z]+)\s+(\d+),\s*(\d+)/', $born, $matches)) {
			$month = $matches[1];
			$day = str_pad($matches[2], 2, "0", STR_PAD_LEFT);
			$year = $matches[3];
			if (isset($months[$month])) $parsed_arr["Born"] = "$day.{$months[$month]}.$year";
		}
	}
	$pers_descr2 = array_map('strip_tags', $pers_descr2);
	$parsed_arr['Description'] = preg_replace('/\(Source.*/', '', $pers_descr2[1]);
	$parsed_arr['url'] = $protocol. "://mydramalist.com/people/" . $id;
	if (preg_match('/<img[^>]*class=["\']img-responsive inline["\'][^>]*src=["\']([^"\']+)["\']/', $json, $matches)) $parsed_arr['image'] = $matches[1];
	if (!empty($parsed_arr["Gender"])) {
		$parsed_arr["Gender"] = trim($parsed_arr["Gender"]);
		if($parsed_arr["Gender"] == "Female") $parsed_arr["Gender"] = "Женщина";
		if($parsed_arr["Gender"] == "Male") $parsed_arr["Gender"] = "Мужчина";
	}
	// Загрузка в шаблон
	$tpl->load_template( 'characters/persons.tpl' );
	
	change_tags($tpl, $id, "id");
	change_tags($tpl, $parsed_arr['Name'], "name");
	change_tags($tpl, $parsed_arr['First Name'], "first_name");
	change_tags($tpl, $parsed_arr['Family Name'], "family_name");
	change_tags($tpl, $parsed_arr['Native name'], "native_name");
	change_tags($tpl, $parsed_arr['Also Known as'], "other_name");
	change_tags($tpl, $parsed_arr['Nationality'], "nation");
	change_tags($tpl, $parsed_arr['Gender'], "gender");
	change_tags($tpl, $parsed_arr['Age'], "age");
	change_tags($tpl, $parsed_arr['Born'], "birth_on");
	change_tags($tpl, $parsed_arr['url'], 'url');
	change_tags($tpl, $parsed_arr['Description'], "description");
	
	change_tags_img($tpl, $parsed_arr['image'], "image", $aaparser_config['persons']['default_image'], true);
	
	$tpl->compile('content');
	// Карта сайта
	if (isset($aaparser_config['persons']['persons_dorama_sitemap']) && $aaparser_config['persons']['persons_dorama_sitemap']) { 
		$sitemapurl = ltrim($parsed_arr['url'], '/');
		$data = unserialize(file_get_contents(ENGINE_DIR."/mrdeath/aaparser/data/persons.dat"));
		if (!is_array($data)) $data = array();
		$urls = array_column($data, 'url');
		if (!in_array($sitemapurl, $urls) && $sitemapurl != '') {
			$data[] = array(
				'url' => $sitemapurl,
				'date' => (new DateTime())->format('Y-m-d\TH:i:sP')
			);
			$data = serialize($data);
			file_put_contents(ENGINE_DIR."/mrdeath/aaparser/data/persons.dat", $data);
			chmod( ENGINE_DIR . "/mrdeath/aaparser/data/persons.dat", 0777 );
		}
	}
}

if ($type == "people") {
	// Начало
	$dle_module = 'people';
	if ( isset($aaparser_config['persons']['persons_page_cache']) && $aaparser_config['persons']['persons_page_cache'] == 1 ) {
		$json = kodik_cache('people_'.$newid, false, 'personas_characters_page');
		$json = json_decode($json, true);
	} else $json = false;
	
	if ($json === false || $json == '') {
		$json = request($shikimori_api_domain . '/api/people/' . $newid);
		if ( isset($aaparser_config['persons']['persons_page_cache']) && $aaparser_config['persons']['persons_page_cache'] == 1 && $json != '') kodik_create_cache('people_'.$newid, json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), false, 'personas_characters_page');
	}
	
	// Обработка данных
	$al_ids = [];
	if (!is_null($json['roles'])) {
		foreach ($json['roles'] as $role) {
			if (isset($role['animes'])) $al_ids[] = $role['animes']['id'];
		}
	}
	if (!is_null($json['works'])) {
		foreach ($json['works'] as $work) {
			if (isset($work['anime'])) $al_ids[] = $work['anime']['id'];
		}
	}
	$al_ids = array_unique($al_ids);
	$al_id = implode(', ', $al_ids);
	
	if (!is_null($json['birth_on']['day']) && !is_null($json['birth_on']['month']) && !is_null($json['birth_on']['year'])) $birth_on = implode('.', [$json['birth_on']['day'], $json['birth_on']['month'], $json['birth_on']['year']]);
	
	// Загрузка в шаблон
	$tpl->load_template( 'characters/people.tpl' );

	change_tags($tpl, $al_id, "anime-list");
	
	change_tags($tpl, $json['id'], "id");
	change_tags($tpl, $json['name'], "name");
	change_tags($tpl, $json['russian'], "russian");
	change_tags($tpl, $json['japanese'], "japanese");
	change_tags($tpl, $json['job_title'], "job_title");
	change_tags($tpl, $json['url'], 'url', $protocol. "://" . $shikimori_url_domain);
	change_tags($tpl, $json['website'], "website");
	change_tags($tpl, $birth_on, "birth_on");
	// change_tags($tpl, $json['description_source'], "description");
	// Нету описания у авторов
	
	change_tags_img($tpl, $json['image']['original'], "image_orig", $aaparser_config['persons']['default_image']);
	change_tags_img($tpl, $json['image']['preview'], "image_prev", $aaparser_config['persons']['default_image']);
	change_tags_img($tpl, $json['image']['x96'], "image_x96", $aaparser_config['persons']['default_image']);
	change_tags_img($tpl, $json['image']['x48'], "image_x48", $aaparser_config['persons']['default_image']);
	
	$tpl->compile('content');
	
	// Карта сайта
	if (isset($aaparser_config['persons']['persons_sitemap']) && $aaparser_config['persons']['persons_sitemap']) { 
		$sitemapurl = ltrim($json['url'], '/');
		$data = unserialize(file_get_contents(ENGINE_DIR."/mrdeath/aaparser/data/people.dat"));
		if (!is_array($data)) $data = array();
		$urls = array_column($data, 'url');
		if (!in_array($sitemapurl, $urls) && $sitemapurl != '') {
			$data[] = array(
				'url' => $sitemapurl,
				'date' => (new DateTime())->format('Y-m-d\TH:i:sP')
			);
			$data = serialize($data);
			file_put_contents(ENGINE_DIR."/mrdeath/aaparser/data/people.dat", $data);
			chmod( ENGINE_DIR . "/mrdeath/aaparser/data/people.dat", 0777 );
		}
	}
}
if ($type == "characters") {
	// Начало
	$dle_module = 'characters';
	if ( isset($aaparser_config['persons']['persons_page_cache']) && $aaparser_config['persons']['persons_page_cache'] == 1 ) {
		$json = kodik_cache('character_'.$newid, false, 'personas_characters_page');
		$json = json_decode($json, true);
	} else $json = false;

	if ($json === false || $json == '') {
		$json = request($shikimori_api_domain . '/api/characters/' . $newid);
		if ( isset($aaparser_config['persons']['persons_page_cache']) && $aaparser_config['persons']['persons_page_cache'] == 1 && $json != '') kodik_create_cache('character_'.$newid, json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), false, 'personas_characters_page');
	}	
	// Обработка данных
	$al_id = $ml_id = '';
	if (!is_null($json['animes'])) $al_id = implode(', ', array_column($json['animes'], 'id'));
	if (!is_null($json['mangas'])) $ml_id = implode(', ', array_column($json['mangas'], 'id'));
	if (!is_null($json['birth_on']['day']) && !is_null($json['birth_on']['month']) && !is_null($json['birth_on']['year'])) $birth_on = implode('.', [$json['birth_on']['day'], $json['birth_on']['month'], $json['birth_on']['year']]);

	// $json['description'] = preg_replace('/\[\/?character(?:=[^\]]+)?\]/', '', $json['description']);
	$spoil = preg_match('/\[spoiler(?:=[^\]]+)?\](.*?)\[\/spoiler\]/s', $json['description'], $matches) ? $matches[1] : '';
	$spoil = preg_replace('/\[.*?\]/', '', $spoil);
	$descr_without_spoil = preg_replace('/\[spoiler(?:=[^\]]+)?\](.*?)\[\/spoiler\]/s', '', $json['description']);
	$descr_without_spoil = preg_replace('/\[.*?\]/', '', $descr_without_spoil);
	$json['description'] = preg_replace('/\[.*?\]/', '', $json['description']);
	
	// Загрузка в шаблон
	$tpl->load_template( 'characters/characters.tpl' );
	
	change_tags($tpl, $al_id, "anime-list");	
	change_tags($tpl, $ml_id, "manga-list");
	
	change_tags($tpl, $json['id'], "id");	
	change_tags($tpl, $json['name'], "name");		
	change_tags($tpl, $json['russian'], "russian");				
	change_tags($tpl, $json['altname'], "altname");				
	change_tags($tpl, $json['japanese'], "japanese");				
	change_tags($tpl, $json['url'], 'url', $protocol. "://" . $shikimori_url_domain);			
	change_tags($tpl, $json['description'], "description");				
	change_tags($tpl, $descr_without_spoil, "description_no_spoiler");				
	change_tags($tpl, $spoil, "spoiler");				
	
	change_tags_img($tpl, $json['image']['original'], "image_orig", $aaparser_config['persons']['default_image']);
	change_tags_img($tpl, $json['image']['preview'], "image_prev", $aaparser_config['persons']['default_image']);
	change_tags_img($tpl, $json['image']['x96'], "image_x96", $aaparser_config['persons']['default_image']);
	change_tags_img($tpl, $json['image']['x48'], "image_x48", $aaparser_config['persons']['default_image']);
	
	$tpl->compile('content');
	
	// Карта сайта
	if (isset($aaparser_config['persons']['persons_sitemap']) && $aaparser_config['persons']['persons_sitemap']) { 
		$sitemapurl = ltrim($json['url'], '/');
		$data = unserialize(file_get_contents(ENGINE_DIR."/mrdeath/aaparser/data/characters.dat"));
		if (!is_array($data)) $data = array();
		$urls = array_column($data, 'url');
		if (!in_array($sitemapurl, $urls) && $sitemapurl != '') {
			$data[] = array(
				'url' => $sitemapurl,
				'date' => (new DateTime())->format('Y-m-d\TH:i:sP')
			);
			$data = serialize($data);
			file_put_contents(ENGINE_DIR."/mrdeath/aaparser/data/characters.dat", $data);
			chmod( ENGINE_DIR . "/mrdeath/aaparser/data/characters.dat", 0777 );
		}
	}

}
if ($type == "persons") {
		
	// Микроразметка
	$metajson['id'] = isset($id) ? $id : '';
	$metajson['name'] = isset($parsed_arr['Name']) ? $parsed_arr['Name'] : '';
	$metajson['first_name'] = isset($parsed_arr['First Name']) ? $parsed_arr['First Name'] : '';
	$metajson['family_name'] = isset($parsed_arr['Family Name']) ? $parsed_arr['Family Name'] : '';
	$metajson['native_name'] = isset($parsed_arr['Native name']) ? $parsed_arr['Native name'] : '';
	$metajson['other_name'] = isset($parsed_arr['Also Known as']) ? $parsed_arr['Also Known as'] : '';
	$metajson['nation'] = isset($parsed_arr['Nationality']) ? $parsed_arr['Nationality'] : '';
	$metajson['gender'] = isset($parsed_arr['Gender']) ? $parsed_arr['Gender'] : '';
	$metajson['age'] = isset($parsed_arr['Age']) ? $parsed_arr['Age'] : '';
	$metajson['birth_on'] = isset($parsed_arr['Born']) ? $parsed_arr['Born'] : '';
	$metajson['url'] = isset($parsed_arr['url']) ? $parsed_arr['url'] : '';
	$metajson['description'] = isset($parsed_arr['Description']) ? $parsed_arr['Description'] : '';

	if ( isset($aaparser_config['persons']['metatitle_dorama']) && $aaparser_config['persons']['metatitle_dorama'] != '') {
		$metatags['header_title'] = $metatags['title'] = check_if($aaparser_config['persons']['metatitle_dorama'], $metajson);
	}
	if ( isset($aaparser_config['persons']['metadescr_dorama']) && $aaparser_config['persons']['metadescr_dorama'] != '') {
		$description = check_if($aaparser_config['persons']['metadescr_dorama'], $metajson);
		$metatags['description'] = mb_strlen($description) > 170 ? mb_substr($description, 0, 170) . '...' : $description;
	}
	if ( isset($aaparser_config['persons']['metakeyw_dorama']) && $aaparser_config['persons']['metakeyw_dorama'] != '') {
		$metatags['keywords'] = check_if($aaparser_config['persons']['metakeyw_dorama'], $metajson);
	}
	
	$canonical = $siteloc = $protocol . '://' . $site_url_domain .'/persons/'. $id;
	if ($current_url != $siteloc) {
		header("Location: $siteloc", true, 301);
		exit();
	}
} else {
		
	// Микроразметка
	$metajson['id'] = isset($json['id']) ? $json['id'] : '';
	$metajson['name'] = isset($json['name']) ? $json['name'] : '';
	$metajson['russian'] = isset($json['russian']) ? $json['russian'] : '';
	$metajson['altname'] = isset($json['altname']) ? $json['altname'] : '';
	$metajson['japanese'] = isset($json['japanese']) ? $json['japanese'] : '';
	$metajson['url'] = isset($json['url']) ? $json['url'] : '';
	$metajson['description'] = isset($json['description']) ? $json['description'] : '';
	$metajson['description_no_spoiler'] = isset($descr_without_spoil) ? $descr_without_spoil : '';
	$metajson['spoiler'] = isset($spoil) ? $spoil : '';
	$metajson['birth_on'] = isset($json['birth_on']) ? $birth_on : '';
	$metajson['job_title'] = isset($json['job_title']) ? $json['job_title'] : '';
	$metajson['website'] = isset($json['website']) ? $json['website'] : '';

	if ( isset($aaparser_config['persons']['metatitle']) && $aaparser_config['persons']['metatitle'] != '') {
		$metatags['header_title'] = $metatags['title'] = check_if($aaparser_config['persons']['metatitle'], $metajson);
	}
	if ( isset($aaparser_config['persons']['metadescr']) && $aaparser_config['persons']['metadescr'] != '') {
		$description = check_if($aaparser_config['persons']['metadescr'], $metajson);
		$metatags['description'] = mb_strlen($description) > 170 ? mb_substr($description, 0, 170) . '...' : $description;
	}
	if ( isset($aaparser_config['persons']['metakeyw']) && $aaparser_config['persons']['metakeyw'] != '') {
		$metatags['keywords'] = check_if($aaparser_config['persons']['metakeyw'], $metajson);
	}

	$canonical = $siteloc = $protocol . '://' . $site_url_domain . $json['url'];
	if ($current_url != $siteloc) {
		header("Location: $siteloc", true, 301);
		exit();
	}
}
	
?>