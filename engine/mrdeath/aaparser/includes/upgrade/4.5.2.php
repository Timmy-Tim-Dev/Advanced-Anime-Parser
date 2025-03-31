<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

require_once ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php';

if ( is_dir(ENGINE_DIR.'/mrdeath/aaparser/cache/personas_characters/') ) kodik_clear_cache('personas', 'personas_characters');
else {
	@mkdir( ENGINE_DIR . "/mrdeath/aaparser/cache/personas_characters/", 0777 );
    @chmod( ENGINE_DIR . "/mrdeath/aaparser/cache/personas_characters/", 0777 );
}
if ( is_dir(ENGINE_DIR.'/mrdeath/aaparser/cache/dorama_persons_page/') ) kodik_clear_cache('persons', 'dorama_persons_page');
else {
	@mkdir( ENGINE_DIR . "/mrdeath/aaparser/cache/dorama_persons_page/", 0777 );
    @chmod( ENGINE_DIR . "/mrdeath/aaparser/cache/dorama_persons_page/", 0777 );
}
if ( is_dir(ENGINE_DIR.'/mrdeath/aaparser/cache/personas_characters_page/') ) kodik_clear_cache('people', 'personas_characters_page');
else {
	@mkdir( ENGINE_DIR . "/mrdeath/aaparser/cache/personas_characters_page/", 0777 );
    @chmod( ENGINE_DIR . "/mrdeath/aaparser/cache/personas_characters_page/", 0777 );
}

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