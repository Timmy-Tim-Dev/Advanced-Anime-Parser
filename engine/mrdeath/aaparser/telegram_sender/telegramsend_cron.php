<?php

if( !defined('DATALIFEENGINE' ) ) {
	die('Hacking attempt!');
}

ini_set("memory_limit","512M");
ini_set('max_execution_time',600);
ignore_user_abort(true);
set_time_limit(600);
session_write_close();

include_once ENGINE_DIR . '/mrdeath/aaparser/data/config_push.php';
include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';

@header('Content-type: text/html; charset=' . $config['charset']);

if ( isset($_GET['key']) && $_GET['key'] != $aaparser_config['settings']['cron_key'] ) die('Cron secret key is wrong');
elseif ( !isset($_GET['key']) ) die('Cron secret key is empty');

if ( !$aaparser_config_push['push_notifications']['enable_tgposting'] ) die('Постинг в телеграм отключён в админке модуля');

$working_mode = 'cron';

require_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/telegram_sender/telegramsend.php'));

?>