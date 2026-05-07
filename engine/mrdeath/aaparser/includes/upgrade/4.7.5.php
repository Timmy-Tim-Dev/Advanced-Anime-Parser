<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// 1 update file
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=foreign-movie,foreign-serial");
	$cont = json_decode($cont, true);
	$translators_name = $translators = [];
	if (isset($cont['results'])) {
		foreach ($cont['results'] as $result) {
			$translators_name[] = $result['title'];
			$translators[$result['id']] = $result['title'];
		}
	}
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	unset ($translators_name, $translators, $cont);

// 2 update file
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=foreign-movie,foreign-serial");
	$cont = json_decode($cont, true);
	$translators_name = $translators = [];
	if (isset($cont['results'])) {
		foreach ($cont['results'] as $result) {
			$translators_name[] = $result['title'];
			$translators[$result['id']] = $result['title'];
		}
	}
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	unset ($translators_name, $translators, $cont);

// 3 update file
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=anime,anime-serial");
	$cont = json_decode($cont, true);
	$translators_name = $translators = [];
	if (isset($cont['results'])) {
		foreach ($cont['results'] as $result) {
			$translators_name[] = $result['title'];
			$translators[$result['id']] = $result['title'];
		}
	}
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	unset ($translators_name, $translators, $cont);
		
// 4 update file		
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=anime,anime-serial");
	$cont = json_decode($cont, true);
	$translators_name = $translators = [];
	if (isset($cont['results'])) {
		foreach ($cont['results'] as $result) {
			$translators_name[] = $result['title'];
			$translators[$result['id']] = $result['title'];
		}
	}
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	unset ($translators_name, $translators, $cont);

// 5 update file	
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/countries?token=".$aaparser_config['settings']['kodik_api_key']);	
	$cont = json_decode($cont, true);
	$countries_name = [];
	if (isset($cont['results'])) {
		foreach ($cont['results'] as $result) {
			$countries_name[] = $result['title'];
		}
	}
	file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/countries_name.json', json_encode($countries_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	unset ($countries_name, $cont);
	
// 6 update file	
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/genres?token=".$aaparser_config['settings']['kodik_api_key']."&genres_type=mydramalist");
	file_put_contents(ENGINE_DIR."/mrdeath/aaparser/data/mydramalist.json", $cont);
	unset ($cont);
	
// 7 update file	
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/genres?token=".$aaparser_config['settings']['kodik_api_key']."&genres_type=shikimori");
	file_put_contents(ENGINE_DIR."/mrdeath/aaparser/data/shikimori.json", $cont);	
	unset ($cont);

// 8 update file	
	$cont = file_get_contents($protocol . $aaparser_config['settings']['kodik_api_domain']."/genres?token=".$aaparser_config['settings']['kodik_api_key']."&genres_type=kinopoisk");
	file_put_contents(ENGINE_DIR."/mrdeath/aaparser/data/kinopoisk.json", $cont);	
	unset ($cont);

// Файл лога версий
$filename = ENGINE_DIR.'/mrdeath/aaparser/data/version.php';

if ($handle = fopen($filename, 'w')) {
    @chmod($filename, 0777);
    $data = htmlspecialchars($_GET['version']);
    fwrite($handle, $data);
    fclose($handle);
} else file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.log', $_GET['version']);
// Конец файла лога версий

// Ответ сервера
die(json_encode(array(
	'status' => 'ok',
	'version' => $_GET['version']
)));
// Конец ответа сервера
?>