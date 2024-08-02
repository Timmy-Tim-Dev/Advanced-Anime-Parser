<?php

if (!defined("DATALIFEENGINE")) exit("Hacking attempt!");

if ( $aaparser_config['push_notifications']['enable_tgposting'] == 1 && $aaparser_config['push_notifications']['tg_cron_enable'] == 1 ) {
  	$check = $db->super_query( "SELECT news_id FROM " . PREFIX . "_telegram_sender WHERE news_id='" .$tlg_news_id ."'");
  	if ( !isset($check['news_id']) && !$check['news_id'] ) $db->query( "INSERT INTO " . PREFIX . "_telegram_sender (news_id, settings) values ('{$tlg_news_id}', '{$tlg_template}')" );
  	unset($check, $tlg_news_id, $tlg_template);
} elseif ( $aaparser_config['push_notifications']['enable_tgposting'] == 1 ) require (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/telegram_sender/telegramsend.php'));