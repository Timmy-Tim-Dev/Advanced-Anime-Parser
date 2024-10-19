<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
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