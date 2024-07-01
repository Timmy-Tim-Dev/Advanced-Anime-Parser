<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

//Пересохранение настроек модуля start
include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';
include_once ENGINE_DIR . '/mrdeath/aaparser/data/config_push.php';

if ( isset($aaparser_config['fields']['xf_shikimori_id']) && $aaparser_config['fields']['xf_shikimori_id'] )
	$aaparser_config['main_fields']['xf_shikimori_id'] = $aaparser_config['fields']['xf_shikimori_id'];

if ( isset($aaparser_config['fields']['xf_mdl_id']) && $aaparser_config['fields']['xf_mdl_id'] )
	$aaparser_config['main_fields']['xf_mdl_id'] = $aaparser_config['fields']['xf_mdl_id'];

if ( isset($aaparser_config['updates']['xf_player']) && $aaparser_config['updates']['xf_player'] )
	$aaparser_config['main_fields']['xf_player'] = $aaparser_config['updates']['xf_player'];

if ( isset($aaparser_config['push_notifications']['poster']) && $aaparser_config['push_notifications']['poster'] )
	$aaparser_config['main_fields']['xf_poster'] = $aaparser_config['push_notifications']['poster'];
elseif ( isset($aaparser_config['push_notifications']['tg_xf_poster']) && $aaparser_config['push_notifications']['tg_xf_poster'] )
	$aaparser_config['main_fields']['xf_poster'] = $aaparser_config['push_notifications']['tg_xf_poster'];
elseif ( isset($aaparser_config['settings']['poster']) && $aaparser_config['settings']['poster'] )
	$aaparser_config['main_fields']['xf_poster'] = $aaparser_config['settings']['poster'];

if ( isset($aaparser_config['push_notifications']['poster_empty']) && $aaparser_config['push_notifications']['poster_empty'] )
	$aaparser_config['main_fields']['poster_empty'] = $aaparser_config['push_notifications']['poster_empty'];
elseif ( isset($aaparser_config['push_notifications']['tg_poster_empty']) && $aaparser_config['push_notifications']['tg_poster_empty'] )
	$aaparser_config['main_fields']['poster_empty'] = $aaparser_config['push_notifications']['tg_poster_empty'];

if ( isset($aaparser_config['updates']['xf_season']) && $aaparser_config['updates']['xf_season'] )
	$aaparser_config['main_fields']['xf_season'] = $aaparser_config['updates']['xf_season'];
elseif ( isset($aaparser_config['push_notifications']['season']) && $aaparser_config['push_notifications']['season'] )
	$aaparser_config['main_fields']['xf_season'] = $aaparser_config['push_notifications']['season'];

if ( isset($aaparser_config['updates']['xf_series']) && $aaparser_config['updates']['xf_series'] )
	$aaparser_config['main_fields']['xf_series'] = $aaparser_config['updates']['xf_series'];
elseif ( isset($aaparser_config['push_notifications']['episode']) && $aaparser_config['push_notifications']['episode'] )
	$aaparser_config['main_fields']['xf_series'] = $aaparser_config['push_notifications']['episode'];

if ( isset($aaparser_config['updates']['xf_quality']) && $aaparser_config['updates']['xf_quality'] )
	$aaparser_config['main_fields']['xf_quality'] = $aaparser_config['updates']['xf_quality'];
elseif ( isset($aaparser_config['push_notifications']['quality']) && $aaparser_config['push_notifications']['quality'] )
	$aaparser_config['main_fields']['xf_quality'] = $aaparser_config['push_notifications']['quality'];

if ( isset($aaparser_config['updates']['xf_translation_last']) && $aaparser_config['updates']['xf_translation_last'] )
	$aaparser_config['main_fields']['xf_translation_last'] = $aaparser_config['updates']['xf_translation_last'];
elseif ( isset($aaparser_config['push_notifications']['translation']) && $aaparser_config['push_notifications']['translation'] )
	$aaparser_config['main_fields']['xf_translation_last'] = $aaparser_config['push_notifications']['translation'];
	
if ( isset($aaparser_config['updates']['xf_translation']) && $aaparser_config['updates']['xf_translation'] )
	$aaparser_config['main_fields']['xf_translation'] = $aaparser_config['updates']['xf_translation'];


if ( isset($aaparser_config['player']) && $aaparser_config['player'] ) {
    foreach ( $aaparser_config['player'] as $key => $value ) {
        $aaparser_config['player'][$key] = $value;
    }
}
	
$new_array = [];
foreach ($aaparser_config as $index => $item) {
	foreach ($item as $key => $value) {
		if ($value != '' && $value != '-') {
			if (is_numeric($value)) $value = intval($value);
			elseif (is_array($value)) $value = implode(',', $value);
			else $value = strip_tags(stripslashes($value), '<li><br><p>');
			$new_array[$index][$key] = $value;
		}
	}
}
$handler = fopen(ENGINE_DIR . '/mrdeath/aaparser/data/config_push.php', "w");
fwrite($handler, "<?PHP \n\n//AAParser Push Settings \n\n\$aaparser_config = ");
fwrite($handler, var_export($new_array, true));
fwrite($handler, ";\n\n?>");
fclose($handler);

//Пересохранение настроек модуля end

//Очистка кеша персонажей и людей

if ( is_dir(ENGINE_DIR.'/mrdeath/aaparser/cache/personas_characters/') ) {
	require_once ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php';
	kodik_clear_cache('personas', 'personas_characters');
}

//Очистка кеша персонажей и людей

file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.log', $_GET['version']);

	
die(json_encode(array(
	'status' => 'ok',
	'version' => $_GET['version']
)));

?>