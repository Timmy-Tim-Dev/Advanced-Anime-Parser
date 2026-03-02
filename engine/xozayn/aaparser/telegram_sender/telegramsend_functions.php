<?php

if (!defined("DATALIFEENGINE")) {
    exit("Hacking attempt!");
}

function telegram_sender($tlg_news_id, $tlg_template) {
  	global $db;
    $check = $db->super_query( "SELECT news_id FROM " . PREFIX . "_telegram_sender WHERE news_id='" .$tlg_news_id ."'");
  	if ( !isset($check['news_id']) && !$check['news_id'] ) $db->query( "INSERT INTO " . PREFIX . "_telegram_sender (news_id, settings) values ('{$tlg_news_id}', '{$tlg_template}')" );
  	return true;
}