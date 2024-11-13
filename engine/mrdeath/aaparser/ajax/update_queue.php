<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);

require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/module.php'));


$kodik_apikey = isset($aaparser_config['settings']['kodik_api_key']) ? $aaparser_config['settings']['kodik_api_key'] : '9a3a536a8be4b3d3f9f7bd28c1b74071';
$kodik_api_domain = isset($aaparser_config['settings']['kodik_api_domain']) ? $aaparser_config['settings']['kodik_api_domain'] : 'https://kodikapi.com/';
$shikimori_api_domain = isset($aaparser_config['settings']['shikimori_api_domain']) ? $aaparser_config['settings']['shikimori_api_domain'] : 'https://shikimori.one/';

$action = isset($_GET['action']) ? $_GET['action'] : null;

$is_logged = false;

@header('Content-type: text/html; charset=' . $config['charset']);

date_default_timezone_set($config['date_adjust']);
$_TIME = time();

if (!$is_logged) $member_id['user_group'] = 5;

if ($is_logged && $member_id['banned'] == 'yes') die('User banned');

$user_group = get_vars('usergroup');

if ( $action == "update" ) {
	
	$res = $db->query("SELECT * FROM ".PREFIX."_anime_list");
	while ( $row = $db->get_array( $res ) ) {
		$is_post = $db->super_query("SELECT id FROM ".PREFIX."_post where id=".$row['news_id']);
		if (!$is_post['id']) $db->query("DELETE FROM ".PREFIX."_anime_list WHERE news_id=".$row['news_id']);
	}
	
	$db->query("DELETE FROM " . PREFIX . "_anime_list WHERE news_id=0");
	file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/kodik.log', "");
	file_put_contents( ENGINE_DIR .'/mrdeath/aaparser/data/shikimori.log', "");
	die(json_encode(array( 'status' => 'ok' )));
} elseif ( $action == "update_xfields" ) {
	$db->query("UPDATE " . PREFIX . "_anime_list SET news_update=1 WHERE news_id>0");
	die(json_encode(array( 'status' => 'ok' )));
} elseif ( $action == "update_cats" ) {
	$db->query("UPDATE " . PREFIX . "_anime_list SET cat_check=1 WHERE news_id>0");
	die(json_encode(array( 'status' => 'ok' )));
} elseif ( $action == "update_translations" ) {
    if ( !$aaparser_config['settings']['kodik_api_key'] ) {
        die(json_encode(array(
		    'status' => 'error',
		    'error' => 'API ключ пустой',
		    'error_desc' => 'Вы не заполнили поле с api ключом от базы кодик'
	    )));
    }
    
    $kodik = request($kodik_api_domain."translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=anime,anime-serial");
    $translators_name = $translators = [];
    foreach ( $kodik['results'] as $result ) {
        $translators_name[] = $result['title'];
        $translators[$result['id']] = $result['title'];
    }
    
    if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json') ) {
        file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
    } else {
        $fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json', "w+");
  	    fwrite($fp, json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
  	    fclose($fp);
    }
    
    if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json') ) {
        file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
    }  else {
        $fp2 = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json', "w+");
  	    fwrite($fp2, json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
  	    fclose($fp2);
    }
	
	die(json_encode(array( 'status' => 'ok' )));
} elseif ( $action == "update_translations_dorama" ) {
    
    if ( !$aaparser_config['settings']['kodik_api_key'] ) {
        die(json_encode(array(
		    'status' => 'error',
		    'error' => 'API ключ пустой',
		    'error_desc' => 'Вы не заполнили поле с api ключом от базы кодик'
	    )));
    }
    
    $kodik = request($kodik_api_domain."translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=foreign-movie,foreign-serial");
    $translators_name = $translators = [];
    foreach ( $kodik['results'] as $result ) {
        $translators_name[] = $result['title'];
        $translators[$result['id']] = $result['title'];
    }
    
    if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json') ) {
        file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
    } else {
        $fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json', "w+");
  	    fwrite($fp, json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
  	    fclose($fp);
    }
    
    if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json') ) {
        file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
    } else {
        $fp2 = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json', "w+");
  	    fwrite($fp2, json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
  	    fclose($fp2);
    }
	
	die(json_encode(array(
		'status' => 'ok'
	)));
} elseif ( $action == "connect_base_get" ) {
		
		if ( !$aaparser_config['main_fields']['xf_shikimori_id'] && !$aaparser_config['main_fields']['xf_mdl_id'] ) {
	        die(json_encode(array(
		        'status' => 'fail'
	        )));
	    }
	
	    if ( $aaparser_config['main_fields']['xf_shikimori_id'] && $aaparser_config['main_fields']['xf_mdl_id'] ) $where = "xfields LIKE '%|".$aaparser_config['main_fields']['xf_shikimori_id']."|%' OR xfields LIKE '%|".$aaparser_config['main_fields']['xf_mdl_id']."|%'";
	    elseif ( $aaparser_config['main_fields']['xf_shikimori_id'] ) $where = "xfields LIKE '%|".$aaparser_config['main_fields']['xf_shikimori_id']."|%'";
	    else $where = "xfields LIKE '%|".$aaparser_config['main_fields']['xf_mdl_id']."|%'";
	    $news = $db->query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
		
		$news_count = $news->num_rows;
		if($news_count == 0) return;
		$result_connect = array();
		$count = 0;
		
		while($temp_news = $db->get_row($news)) {
			$id = intval($temp_news['id']);
			$xfields = xfieldsdataload($temp_news['xfields']);
			if ( $xfields[$aaparser_config['main_fields']['xf_shikimori_id']] ) $shikimori_id = $xfields[$aaparser_config['main_fields']['xf_shikimori_id']];
			else $shikimori_id = 0;
			if ( $xfields[$aaparser_config['main_fields']['xf_mdl_id']] ) $mdl_id = $xfields[$aaparser_config['main_fields']['xf_mdl_id']];
			else $mdl_id = 0;

			if (!$shikimori_id && !$mdl_id) continue;			
			
			$result_connect[] = array(
				'id' => $id,
				'shikimori_id' => $shikimori_id,
				'mdl_id' => $mdl_id,
			);
			
			$count++;
		}
		if ($count > 0) echo json_encode($result_connect);
		else die(json_encode(array( 'status' => 'fail' )));
} elseif ( $action == "connect_base" ) {
	
	if ( !$aaparser_config['main_fields']['xf_shikimori_id'] && !$aaparser_config['main_fields']['xf_mdl_id'] ) die(json_encode(array( 'status' => 'fail' )));
	    
	$news_id = $_GET['newsid'];
    $news_id = is_numeric($news_id) ? intval($news_id) : false;
    
    if(!$news_id) return;
    
	if ( $_GET['shikiid'] && $_GET['shikiid'] != 0 && $_GET['shikiid'] != '0' ) $shikimori_id = $_GET['shikiid'];
	else $shikimori_id = 0;
	if ( $_GET['mdlid'] && $_GET['mdlid'] != 0 && $_GET['mdlid'] != '0' ) $mdl_id = $_GET['mdlid'];
	else $mdl_id = 0;
	
	if( !$shikimori_id && !$mdl_id ) return;
	    
	if ( $shikimori_id ) {
	    $base_check_db = $db->super_query( "SELECT shikimori_id, news_id FROM " . PREFIX . "_anime_list WHERE shikimori_id='{$shikimori_id}'" );
	
	    if ( $base_check_db['shikimori_id'] == $shikimori_id && $base_check_db['news_id'] != $news_id ) {
	        $db->query("UPDATE " . PREFIX . "_anime_list SET news_id='{$news_id}', started=1 WHERE shikimori_id='{$shikimori_id}'");
	        $result_work = array(
			    'news_id' => $news_id,
			    'status' => 'связано.'
		    );
		    $result = json_encode($result_work);
		    echo $result;
	    } elseif ( $base_check_db['shikimori_id'] == $shikimori_id && $base_check_db['news_id'] == $news_id ) {
	        $result_work = array(
			    'news_id' => $news_id,
			    'status' => 'не требует связывания.'
		    );
		    $result = json_encode($result_work);
		    echo $result;
	    } else {
	        $shikimori = request($shikimori_api_domain.'api/animes/'.$shikimori_id);
	        if ( $shikimori['aired_on'] ) {
		        $aired = explode('-', $shikimori['aired_on']);
		        $year = $aired[0];
	        }
	        else $year = 0;
	        $kind = $shikimori['kind'];
	        $status = $shikimori['status'];
	        
	        $db->query("INSERT INTO " . PREFIX . "_anime_list (shikimori_id, year, type, news_id, tv_status, started) VALUES( '{$shikimori_id}', '{$year}', '{$kind}', '{$news_id}', '{$status}', '1' ) " );

	        $result_work = array(
			    'news_id' => $news_id,
			    'status' => 'добавлено в базу и связано.'
		    );
		    $result = json_encode($result_work);
		    echo $result;
	    }
	} elseif ( $mdl_id ) {
	    
	    $base_check_db = $db->super_query( "SELECT mdl_id, news_id FROM " . PREFIX . "_anime_list WHERE mdl_id='{$mdl_id}'" );
	
	    if ( $base_check_db['mdl_id'] == $mdl_id && $base_check_db['news_id'] != $news_id ) {
	        $db->query("UPDATE " . PREFIX . "_anime_list SET news_id='{$news_id}' WHERE mdl_id='{$mdl_id}'");
	        $result_work = array(
			    'news_id' => $news_id,
			    'status' => 'связано.'
		    );
		    $result = json_encode($result_work);
		    echo $result;
	    } elseif ( $base_check_db['mdl_id'] == $mdl_id && $base_check_db['news_id'] == $news_id ) {
	        $result_work = array(
			    'news_id' => $news_id,
			    'status' => 'не требует связывания.'
		    );
		    $result = json_encode($result_work);
		    echo $result;
	    } else {
	        $kodik = request($kodik_api_domain.'search?token='.$kodik_apikey.'&mdl_id='.$mdl_id.'&with_material_data=true');
	        
	        if ( $kodik['results'][0]['year'] ) $year = $kodik['results'][0]['year'];
	        else $year = 0;
	        
	        $status = $kodik['results'][0]['material_data']['all_status'];
	        $db->query("INSERT INTO " . PREFIX . "_anime_list (mdl_id, year, news_id, tv_status, started) VALUES( '{$mdl_id}', '{$year}', '{$news_id}', '{$status}', '1' ) " );

	        $result_work = array(
			    'news_id' => $news_id,
			    'status' => 'добавлено в базу и связано.'
		    );
		    $result = json_encode($result_work);
		    echo $result;
	    }
	    unset($base_check_db, $news_id, $shikimori_id, $mdl_id);
	}

} elseif ( $action == "clear_player_cache" ) {
    require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));
    kodik_clear_cache('playlist', 'player');
	die(json_encode(array( 'status' => 'ok' )));
} elseif ( $action == "clear_personajes_cache" ) {
    require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));
    kodik_clear_cache('personas', 'personas_characters');
	die(json_encode(array( 'status' => 'ok' )));
} elseif ( $action == "clear_page_cache" ) {
    require_once (DLEPlugins::Check(ENGINE_DIR.'/mrdeath/aaparser/functions/kodik_cache.php'));
    kodik_clear_cache('personas', 'personas_characters_page');
	die(json_encode(array( 'status' => 'ok' )));
} elseif ( $action == "update_module" ) {
	$row = $db->super_query("SHOW TABLE STATUS WHERE Name = '" . PREFIX . "_post'");
	$storage_engine = $row['Engine'];
	
	if( file_exists( ENGINE_DIR . "/mrdeath/aaparser/includes/upgrade/".$_GET['version'].".php" ) ) include ( ENGINE_DIR . "/mrdeath/aaparser/includes/upgrade/".$_GET['version'].".php" );
	else  include ( ENGINE_DIR . "/mrdeath/aaparser/includes/upgrade/universal.php" );
}

?>