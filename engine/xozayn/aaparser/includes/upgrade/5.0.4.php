<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

// путь к конфигу
$configFile = ENGINE_DIR . '/xozayn/aaparser/data/config.php';

// функция очистки домена
function clearDomain($url) {
    $url = preg_replace('#^https?://#', '', $url);
    $url = preg_replace('#/.*$#', '', $url);
    return trim($url);
}

// нужные ключи
$kodikKeys = [
    'kodik_site' => 'bd.kodikres.com',
    'kodik_api_player' => 'kodikplayer.com',
    'kodik_api_domain' => 'kodik-api.com'
];

if (file_exists($configFile)) {

    include $configFile;

    if (!isset($aaparser_config['settings'])) $aaparser_config['settings'] = [];
	
    foreach ($kodikKeys as $key => $default) {
        if (empty($aaparser_config['settings'][$key])) $aaparser_config['settings'][$key] = $default;
        else $aaparser_config['settings'][$key] = clearDomain($aaparser_config['settings'][$key]);
    }

    // сохраняем обратно
    $handler = fopen($configFile, "w");
    fwrite($handler, "<?PHP \n\n//AAParser Settings \n\n\$aaparser_config = ");
    fwrite($handler, var_export($aaparser_config, true));
    fwrite($handler, ";\n\n?>");
    fclose($handler);
}

// Файл лога версий
$filename = ENGINE_DIR.'/xozayn/aaparser/data/version.php';

if ($handle = fopen($filename, 'w')) {
    @chmod($filename, 0777);
    $data = htmlspecialchars($_GET['version']);
    fwrite($handle, $data);
    fclose($handle);
} else file_put_contents(ENGINE_DIR.'/xozayn/aaparser/data/version.log', $_GET['version']);
// Конец файла лога версий

// Ответ сервера
die(json_encode(array(
	'status' => 'ok',
	'version' => $_GET['version']
)));
// Конец ответа сервера
?>