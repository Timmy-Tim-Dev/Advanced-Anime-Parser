<?php

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../' );
	die( "Hacking attempt!" );
}
$res_shiki = $db->query("SELECT * FROM ".PREFIX."_shikimori_posts ");
while ( $shiki_row = $db->get_row ($res_shiki) ) {
	$is_post = $db->super_query("SELECT id FROM ".PREFIX."_post where id=".$shiki_row['post_id']);
	$ls_post = $db->super_query("SELECT news_id FROM ".PREFIX."_anime_list where shikimori_id=".$shiki_row['shiki_id']);
	
	if ($is_post['id'] > 0 || $ls_post['news_id'] > 0) {
		$db->query("INSERT INTO " . PREFIX . "_anime_list (shikimori_id, year, news_id, tv_status) VALUES( '".$shiki_row['shiki_id']."', '0', '".$shiki_row['post_id']."', 'anons' ) " );
		continue;
	} else $db->query("DELETE FROM ".PREFIX."_shikimori_posts WHERE id=".$shiki_row['id']);
}

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