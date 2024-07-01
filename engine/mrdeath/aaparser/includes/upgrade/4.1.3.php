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

//Пересохранение настроек модуля start
if ( file_exists(ENGINE_DIR . '/mrdeath/aaparser/data/config_push.php') ) {
    include_once ENGINE_DIR . '/mrdeath/aaparser/data/config_push.php';
    $aaparser_config['settings']['version'] = $_GET['version'];

    $new_array = [];
    foreach ($aaparser_config as $index => $item) {
	    foreach ($item as $key => $value) {
		    if ($value != '' && $value != '-') {
			    if (is_numeric($value)) {
				    $value = intval($value);
			    }
			    elseif (is_array($value)) {
				    $value = implode(',', $value);
			    }
			    else {
				    $value = strip_tags(stripslashes($value), '<li><br><p>');
			    }
			    $new_array[$index][$key] = $value;
		    }
	    }
    }

    foreach ($aaparser_config_push as $index => $item) {
	    foreach ($item as $key => $value) {
		    if ($value != '' && $value != '-') {
			    if (is_numeric($value)) {
				    $value = intval($value);
			    }
			    elseif (is_array($value)) {
				    $value = implode(',', $value);
			    }
			    else {
				    $value = strip_tags(stripslashes($value), '<li><br><p>');
			    }
			    $new_array[$index][$key] = $value;
		    }
	    }
    }
    $handler = fopen(ENGINE_DIR . '/mrdeath/aaparser/data/config.php', "w");
    fwrite($handler, "<?PHP \n\n//AAParser Settings \n\n\$aaparser_config = ");
    fwrite($handler, var_export($new_array, true));
    fwrite($handler, ";\n\n?>");
    fclose($handler);
}
else {
    $aaparser_config['settings']['version'] = $_GET['version'];
    
    $new_array = [];
    foreach ($aaparser_config as $index => $item) {
	    foreach ($item as $key => $value) {
		    if ($value != '' && $value != '-') {
			    if (is_numeric($value)) {
				    $value = intval($value);
			    }
			    elseif (is_array($value)) {
				    $value = implode(',', $value);
			    }
			    else {
				    $value = strip_tags(stripslashes($value), '<li><br><p>');
			    }
			    $new_array[$index][$key] = $value;
		    }
	    }
    }

    $handler = fopen(ENGINE_DIR . '/mrdeath/aaparser/data/config.php', "w");
    fwrite($handler, "<?PHP \n\n//AAParser Settings \n\n\$aaparser_config = ");
    fwrite($handler, var_export($new_array, true));
    fwrite($handler, ";\n\n?>");
    fclose($handler);
}

//Пересохранение настроек модуля end

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