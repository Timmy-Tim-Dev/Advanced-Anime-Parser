<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

dle_session();

require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));

date_default_timezone_set($config['date_adjust']);
$_TIME = time();

$_POST['user_hash'] = trim($_POST['user_hash']);
if ($_POST['user_hash'] == '' OR $_POST['user_hash'] != $dle_login_hash) {
	die('error');
}

if (!$is_logged && $member_id['user_group'] != 1) {
	die();
}

$action = isset($_POST['action']) ? trim(strip_tags($_POST['action'])) : false;

if ($action == 'options') {
	$data_form = isset($_POST['data_form']) ? $_POST['data_form'] : false;
	if ($data_form) {
		parse_str($data_form, $array_post);
	}
	$new_array = [];
	foreach ($array_post as $index => $item) {
	    if ( $index == 'push_notifications' || $index == 'calendar_settings' ) continue;
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
	$new_array = [];
	foreach ($array_post as $index => $item) {
	    if ( $index != 'push_notifications' && $index != 'calendar_settings' ) continue;
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
	$handler = fopen(ENGINE_DIR . '/mrdeath/aaparser/data/config_push.php', "w");
	fwrite($handler, "<?PHP \n\n//AAParser Push Settings \n\n\$aaparser_config_push = ");
	fwrite($handler, var_export($new_array, true));
	fwrite($handler, ";\n\n?>");
	fclose($handler);
	echo json_encode(['success' => 'Ok']);
}
$php_version = intval(str_replace(array(".",","),"",substr(PHP_VERSION,0,3)));
if ($php_version >= 74 && file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/indexing.php') && $aaparser_config['push_notifications']['google_indexing'] = "1") {

	require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
	if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json') ) {
		$mod_settings = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json');
		$mod_settings = json_decode($mod_settings, true);
		if (isset($aaparser_config['settings_gindexing']['account'])){
			$mod_settings['account'] = $aaparser_config['settings_gindexing']['account'];
		} else {
			$mod_settings['account'] = $_POST['acc'];
		}
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', json_encode($mod_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	}
	else {
		$mod_settings = [];
		$mod_settings['today_date'] = date('Y-m-d', time());
		if (isset($aaparser_config['settings_gindexing']['account'])){
			$mod_settings['today_limit'][$aaparser_config['settings_gindexing']['account']] = 0;
			$mod_settings['account'] = $aaparser_config['settings_gindexing']['account'];
		} else {
			$mod_settings['today_limit'][$_POST['acc']] = 0;
			$mod_settings['account'] = $_POST['acc'];
		}
		$mod_settings['all'] = 0;
		$mod_settings['updated'] = 0;
		$mod_settings['deleted'] = 0;
		$mod_settings['logs'][] = '';
		$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', "w+");
		fwrite($fp, json_encode($mod_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		fclose($fp);
	}
}
?>