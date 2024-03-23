<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

	$fran_name = $aaparser_config['fields']['xf_shikimori_id'];

	$chekering = $db->query("SHOW COLUMNS FROM ".PREFIX."_post LIKE 'franchise_aap'");
	if ($chekering->num_rows == "0") {
		$db->query("ALTER TABLE ".PREFIX."_post add `franchise_aap` varchar(700) NOT NULL DEFAULT ''");
	}
	
	if ($fran_name == "") die("Не может найти доп поле с франшизой");
	
	$res = $db->query("SELECT * FROM ".PREFIX."_post WHERE xfields like '%$fran_name%'");
	while ( $row = $db->get_row($res) ) {
		$xfieldsdata = xfieldsdataload( $row['xfields'] );
		
		$data = $xfieldsdata[$fran_name];
		$db->query("update ".PREFIX."_post set franchise_aap='$data'  where id=".$row['id']);
	}
	die("Обновление поля франшизы выполнено");
?>