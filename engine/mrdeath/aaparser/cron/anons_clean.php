<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
$res_shiki = $db->query("SELECT * FROM ".PREFIX."_shikimori_posts ");
while ( $shiki_row = $db->get_row ($res_shiki) ) {
	$is_post = $db->super_query("SELECT id FROM ".PREFIX."_post where id=".$shiki_row['post_id']);
	$ls_post = $db->super_query("SELECT news_id FROM ".PREFIX."_anime_list where shikimori_id=".$shiki_row['shiki_id']);
	
	if ($is_post['id'] > 0 || $ls_post['news_id'] > 0) {
		$db->query("INSERT INTO " . PREFIX . "_anime_list (shikimori_id, year, news_id, tv_status) VALUES( '".$shiki_row['shiki_id']."', '0', '".$shiki_row['post_id']."', 'anons' ) " );
		continue;
	} else $db->query("DELETE FROM ".PREFIX."_shikimori_posts WHERE id=".$shiki_row['id']);
}

echo "Очистка базы завершена";
?>