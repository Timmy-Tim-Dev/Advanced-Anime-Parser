<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$tableSchema = array();

//Проверяем существование таблицы _subscribe_info
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_subscribe_info" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_subscribe_info (
	    `id` int(11) NOT NULL AUTO_INCREMENT,
	    `user_id` int(11) NOT NULL DEFAULT '0',
	    `post_id` int(11) NOT NULL DEFAULT '0',
	    PRIMARY KEY (`id`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
unset($check);

//Проверяем существование таблицы _shikimori_posts
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_shikimori_posts" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_shikimori_posts (
	    `id` int(11) NOT NULL AUTO_INCREMENT,
        `post_id` int(11) NOT NULL DEFAULT '0',
        `shiki_id` int(11) NOT NULL DEFAULT '0',
	    PRIMARY KEY (`id`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
unset($check);

//Проверяем существование таблицы _anime_list
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_anime_list" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_anime_list (
	    `material_id` int(12) NOT NULL,
        `shikimori_id` varchar(100) NOT NULL,
        `mdl_id` varchar(255) NOT NULL,
        `year` int(6) UNSIGNED NOT NULL,
        `type` varchar(100) NOT NULL,
        `news_id` int(12) UNSIGNED NOT NULL DEFAULT '0',
        `tv_status` varchar(20) NOT NULL,
        `error` int(12) UNSIGNED NOT NULL DEFAULT '0',
        `started` tinyint(1) NOT NULL DEFAULT '0',
        `cat_check` tinyint(1) NOT NULL DEFAULT '0',
        `news_update` tinyint(1) NOT NULL DEFAULT '0',
        `skipped` tinyint(1) NOT NULL DEFAULT '0',
	    PRIMARY KEY (`material_id`),
        KEY `shikimori_id` (`shikimori_id`),
        KEY `mdl_id` (`mdl_id`),
        KEY `year` (`year`),
        KEY `type` (`type`),
        KEY `news_id` (`news_id`),
        KEY `tv_status` (`tv_status`),
        KEY `error` (`error`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
unset($check);

//Проверяем существование таблицы _rooms_list
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_rooms_list" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_rooms_list (
	    `id` int(12) NOT NULL AUTO_INCREMENT,
        `url` varchar(200) NOT NULL,
        `leader` varchar(200) NOT NULL,
        `leader_last_login` int(12) UNSIGNED NOT NULL DEFAULT '0',
        `news_id` int(12) NOT NULL DEFAULT '0',
        `poster` varchar(255) NOT NULL,
        `title` varchar(255) NOT NULL,
        `iframe` varchar(255) NOT NULL,
        `public` tinyint(1) NOT NULL DEFAULT '0',
        `pause` tinyint(1) NOT NULL DEFAULT '1',
        `time` int(5) NOT NULL DEFAULT '0',
        `created` int(12) NOT NULL DEFAULT '0',
        `episode_num` int(5) NOT NULL DEFAULT '0',
        `season_num` int(5) NOT NULL DEFAULT '0',
        `translation` int(5) NOT NULL DEFAULT '0',
        `shikimori_id` varchar(100) NOT NULL,
        `mdl_id` varchar(255) NOT NULL,
        `visitors_iframe` varchar(255) NOT NULL,
	    PRIMARY KEY (`id`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
unset($check);

//Проверяем существование таблицы _rooms_chat
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_rooms_chat" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_rooms_chat (
	    `id` int(12) NOT NULL AUTO_INCREMENT,
        `room_url` varchar(200) NOT NULL,
        `login` varchar(40) NOT NULL,
        `avatar` varchar(255) NOT NULL,
        `time` int(11) NOT NULL DEFAULT '0',
        `message` varchar(500) NOT NULL,
	    PRIMARY KEY (`id`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
unset($check);

//Проверяем существование таблицы _rooms_visitors
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_rooms_visitors" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_rooms_visitors (
	    `id` int(12) NOT NULL AUTO_INCREMENT,
        `room_url` varchar(200) NOT NULL,
        `login` varchar(40) NOT NULL,
        `avatar` varchar(255) NOT NULL,
        `time` int(11) NOT NULL DEFAULT '0',
	    PRIMARY KEY (`id`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
unset($check);

//Проверяем существование таблицы _raspisanie_ongoingov
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_raspisanie_ongoingov" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_raspisanie_ongoingov (
	    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `day_name` varchar(255) NOT NULL,
        `date` varchar(255) NOT NULL,
        `last_update` varchar(255) NOT NULL,
        `anime_list` text NOT NULL,
	    PRIMARY KEY (`id`),
        KEY `day_name` (`day_name`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
    $tableSchema[] = "INSERT INTO `" . PREFIX . "_raspisanie_ongoingov` (`day_name`) VALUES ('monday')";
    $tableSchema[] = "INSERT INTO `" . PREFIX . "_raspisanie_ongoingov` (`day_name`) VALUES ('tuesday')";
    $tableSchema[] = "INSERT INTO `" . PREFIX . "_raspisanie_ongoingov` (`day_name`) VALUES ('wednesday')";
    $tableSchema[] = "INSERT INTO `" . PREFIX . "_raspisanie_ongoingov` (`day_name`) VALUES ('thursday')";
    $tableSchema[] = "INSERT INTO `" . PREFIX . "_raspisanie_ongoingov` (`day_name`) VALUES ('friday')";
    $tableSchema[] = "INSERT INTO `" . PREFIX . "_raspisanie_ongoingov` (`day_name`) VALUES ('saturday')";
    $tableSchema[] = "INSERT INTO `" . PREFIX . "_raspisanie_ongoingov` (`day_name`) VALUES ('sunday')";
}
unset($check);

//Проверяем наличие нужных ячеек в таблице _users
$check = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users LIMIT 1" );
if ( !isset($check['watched_series']) ) $tableSchema[] = "ALTER TABLE `" . USERPREFIX . "_users` ADD `watched_series` MEDIUMTEXT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
if ( !isset($check['push_subscribe']) ) $tableSchema[] = "ALTER TABLE `" . USERPREFIX . "_users` ADD `push_subscribe` MEDIUMTEXT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
unset($check);

if ( count($tableSchema) > 0 ) {
    foreach ($tableSchema as $table) {
	    $db->query($table, false);
    }
}

file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.log', $_GET['version']);

	
die(json_encode(array(
	'status' => 'ok',
	'version' => $_GET['version']
)));

?>