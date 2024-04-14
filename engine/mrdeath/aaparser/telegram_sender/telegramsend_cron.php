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

if ( isset($aaparser_config_push['push_notifications']['stop_send_from']) && $aaparser_config_push['push_notifications']['stop_send_from'] ) $stop_send_from = strtotime(date('Y-m-d') ." ".$aaparser_config_push['push_notifications']['stop_send_from']);
else $stop_send_from = false;

if ( isset($aaparser_config_push['push_notifications']['stop_send_to']) && $aaparser_config_push['push_notifications']['stop_send_to'] ) $stop_send_to = strtotime(date('Y-m-d') ." ".$aaparser_config_push['push_notifications']['stop_send_to']);
else $stop_send_to = false;

if ( isset($aaparser_config_push['push_notifications']['stop_send_from']) && isset($aaparser_config_push['push_notifications']['stop_send_to']) && $aaparser_config_push['push_notifications']['stop_send_from'] == $aaparser_config_push['push_notifications']['stop_send_to'] ) $stop_send_from = $stop_send_to = false;

if ( $stop_send_from && $stop_send_to && $stop_send_from < $stop_send_to ) {
    if ( $_TIME >= $stop_send_from && $_TIME <= $stop_send_to ) die('Отправка постов приостановлена с учётом указанного в настройках времени');
}

$working_mode = 'cron';

require_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/telegram_sender/telegramsend.php'));

?>