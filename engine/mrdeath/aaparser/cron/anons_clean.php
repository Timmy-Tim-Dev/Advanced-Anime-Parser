<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

	$res_shiki = $db->query("SELECT id, post_id FROM ".PREFIX."_shikimori_posts ");
	while ( $shiki_row = $db->get_row ($res_shiki) ) {
		$is_post = $db->super_query("SELECT id FROM ".PREFIX."_post where id=".$shiki_row['post_id']);
		
		if ($is_post['id'] > 0)
		{
			continue;
		}
		else
		{
			$db->query("DELETE FROM ".PREFIX."_shikimori_posts WHERE id=".$shiki_row['id']);
		}
	}
	
	echo "Очистка базы завершена";
?>