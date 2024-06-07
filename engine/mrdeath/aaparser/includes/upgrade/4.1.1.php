<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

//Очистка кеша персонажей и людей
if ( is_dir(ENGINE_DIR.'/mrdeath/aaparser/cache/personas_characters/') ) {
	require_once ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php';
	kodik_clear_cache('personas', 'personas_characters');
}

file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.log', $_GET['version']);

die(json_encode(array(
	'status' => 'ok',
	'version' => $_GET['version']
)));

?>