<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}

$tableSchema = array();

$check = $db->super_query( "CHECK TABLE " . PREFIX . "_telegram_sender" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_telegram_sender (
	    `id` int(11) NOT NULL AUTO_INCREMENT,
	    `news_id` int(11) NOT NULL DEFAULT '0',
	    `settings` varchar(255) NOT NULL,
	    `error` tinyint(1) NOT NULL DEFAULT '0',
	    PRIMARY KEY (`id`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
unset($check);
$check = $db->super_query( "CHECK TABLE " . PREFIX . "_subscribe_info" );
if ( $check['Msg_type'] == 'Error' ) {
    $tableSchema[] = "CREATE TABLE " . PREFIX . "_subscribe_info (
	    `id` int(11) NOT NULL AUTO_INCREMENT,
	    `user_id` int(11) NOT NULL DEFAULT 0,
	    `post_id` int(11) NOT NULL DEFAULT 0,
	    PRIMARY KEY (`id`)
    ) ENGINE=" . $storage_engine . " AUTO_INCREMENT=0 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci";
}
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