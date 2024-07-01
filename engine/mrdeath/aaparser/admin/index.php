<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
 
if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

$actual_module_version = '4.1.3';
$action = isset($_GET['action']) ? $_GET['action'] : false;

$php_version = intval(str_replace(array(".",","),"",substr(PHP_VERSION,0,3)));
//Проверяем существование системных файлов и файла настроек
if (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/config.php')) {
$text = <<<HTML
<?PHP


//AAParser Settings 

\$aaparser_config = array (
  'settings' => 
  array (
    'kodik_api_key' => '',
    'kodik_api_domain' => 'https://kodikapi.com/',
    'shikimori_api_domain' => 'https://shikimori.me/',
    'parse_authors' => 1,
    'parse_franshise' => 1,
    'parse_similar' => 1,
    'parse_related' => 1,
    'parse_wa' => 1,
    'max_actors' => 10,
    'max_directors' => 5,
    'max_producers' => 5,
    'max_writers' => 5,
    'max_composers' => 5,
    'max_editors' => 5,
    'max_designers' => 5,
    'max_operators' => 5,
    'cron_key' => '
HTML;
$text .= md5(time().$config['http_home_url'] . $_SESSION['user_id']['email']);
$text .= <<<HTML
',
  ),
  'grabbing' => 
  array (
    'author_name' => 'Admin',
    'author_id' => 1,
    'publish' => 1,
    'publish_image' => 1,
    'publish_plot' => 1,
    'publish_main' => 1,
    'allow_rating' => 1,
    'allow_comments' => 1,
    'allow_br' => 1,
    'tv' => 1,
    'movie' => 1,
    'ova' => 1,
    'ona' => 1,
    'special' => 1,
    'music' => 1,
    'if_camrip' => 1,
    'if_lgbt' => 1,
    'this_year' => 1,
  ),
  'update_news' => 
  array (

  ),
  'updates' => 
  array (

  ),
  'fields' => 
  array (

  ),
  'xfields' => 
  array (
    'title' => '[if_kodik_title]{kodik_title}[/if_kodik_title][ifnot_kodik_title]{kodik_title_orig}[/ifnot_kodik_title]',
    'short_story' => '[if_kodik_plot]{kodik_plot}[/if_kodik_plot]',
    'alt_name' => '[if_kodik_title]{kodik_title}[/if_kodik_title][ifnot_kodik_title]{kodik_title_orig}[/ifnot_kodik_title]',
  ),
  'categories' => 
  array (

  ),
  'images' => 
  array (

  ),
  'player' => 
  array (

  ),
  'integration' => 
  array (

  ),
  'settings_anons' => 
  array (
  
  ),
);

?>
HTML;

  	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/config.php', "w+");
	@chmod(ENGINE_DIR.'/mrdeath/aaparser/data/config.php', 0777);
  	fwrite($fp, $text);
  	fclose($fp);
  	unset($text);
  	
  	require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
}

if (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/kodik.log')) {
  	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/kodik.log', "w+");
	@chmod(ENGINE_DIR.'/mrdeath/aaparser/data/kodik.log', 0777);
  	fwrite($fp, "");
  	fclose($fp);
}

if (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/cron.log')) {
  	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/cron.log', "w+");
	@chmod(ENGINE_DIR.'/mrdeath/aaparser/data/cron.log', 0777);
  	fwrite($fp, "");
  	fclose($fp);
}

if (file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/version.php')) {
	$log_module_version = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.php');
} elseif (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/version.php') && file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/version.log')) {
    $log_module_version_old = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.log');
	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/version.php', "w+");
	@chmod(ENGINE_DIR.'/mrdeath/aaparser/data/version.php', 0777);
	fwrite($fp, $log_module_version_old);
	fclose($fp);
	$log_module_version = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.php');
} elseif (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/version.php') && !file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/version.log')) {
	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/version.php', "w+");
	@chmod(ENGINE_DIR.'/mrdeath/aaparser/data/version.php', 0777);
	fwrite($fp, '3.4.1');
	fclose($fp);
	$log_module_version = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/version.php');
}

if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/indexing.php') ) {
	if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json') ) {
		$mod_settings = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json');
		$mod_settings = json_decode($mod_settings, true);
	}
	else {
		$mod_settings = [];
		$mod_settings['today_date'] = date('Y-m-d', time());
		$mod_settings['today_limit']['1.json'] = 0;
		$mod_settings['account'] = "";
		$mod_settings['all'] = 0;
		$mod_settings['updated'] = 0;
		$mod_settings['deleted'] = 0;
		$mod_settings['logs'][] = '';
		$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', "w+");
		@chmod(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', 0777);
		fwrite($fp, json_encode($mod_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		fclose($fp);
	}

    
    if ( !isset($mod_settings['today_limit'][$mod_settings['account']]) ) {
        $mod_settings['today_limit'][$mod_settings['account']] = 0;
    }
    if ( date('Y-m-d', time()) != $mod_settings['today_date'] ) {
        foreach ( $mod_settings['today_limit'] as $num => $acc_name ) {
            $mod_settings['today_limit'][$num] = 0;
        }
        $mod_settings['today_date'] = date('Y-m-d', time());
    }
    
    if ( !$mod_settings['account'] || !isset($mod_settings['today_limit'][$mod_settings['account']]) ) $today_limit = 0;
    else $today_limit = $mod_settings['today_limit'][$mod_settings['account']];
    $all = $mod_settings['all'];
    $updated = $mod_settings['updated'];
    $deleted = $mod_settings['deleted'];

    $inform = $all.". Из них: добавлено/обновлено - ".$mod_settings['updated'].", удалено - ".$mod_settings['deleted'];

    $acdir = opendir(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/accounts/');
    $aclist = [];
    while($file = readdir($acdir)){
        if( $file == '.htaccess' || !mb_stripos( $file,'.json' ) ){
            continue;
        }
        $aclist[] = $file;
    }
    if ( !$aclist ) $aclist[] = 'Пусто';
}

require_once ENGINE_DIR.'/mrdeath/aaparser/functions/admin.php';

if ( !file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/indexing.php') ) {
	if (isset($aaparser_config['settings_gindexing']['account'])){
		$mod_settings['account'] = $aaparser_config['settings_gindexing']['account'];
	}
}

if($is_loged_in AND version_compare($log_module_version , $actual_module_version , '<') && $action != 'dbupgrade' ) {

	if( $member_id['user_group'] == 1 ) {
		
		header( "Location: ?mod=aap&action=dbupgrade" );
		die();
		
	} else msg("error", $lang['addnews_denied'], $lang['upgr_notadm']);
	
}


echoheader('<b>Advanced Kodik Parser v'.$actual_module_version.'</b>', 'Настройки модуля Advanced Kodik Parser');

if ( !$action ) {

$all = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_anime_list" );
$done = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_anime_list WHERE started>0" );
if ( $all['count'] != $done['count'] ) $base_status = '<div style="color:#f44336">База обрабатывается</div>';
elseif ( $all['count'] ) $base_status = '<div style="color:#009688">База обработана</div>';
else $base_status = '<div style="color:#ef5350">База пустая</div>';
if ( $aaparser_config['settings']['cron_key'] ) $cron_key = $aaparser_config['settings']['cron_key'];
else $cron_key = 'ваш_ключ_с_настроек_крона';

if ($aaparser_config['settings']['cron_key'] == "mrd3ath" || $aaparser_config['settings']['cron_key'] == "y0urcr0nk3y") {
	$oldkey = $aaparser_config['settings']['cron_key'];
	$cron_key = md5(time().$config['http_home_url'] . $_SESSION['user_id']['email']);
	$aaparser_config['settings']['cron_key'] = md5(time().$config['http_home_url'] . $_SESSION['user_id']['email']);
	$config_content = "<?php\n\$aaparser_config = " . var_export($aaparser_config, true) . ";\n?>";
    file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/config.php', $config_content);
}

if ( $aaparser_config['update_news']['cat_check'] ) {
    $cat_check_db = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_anime_list WHERE cat_check=1" );
    if ( $cat_check_db['count'] > 0 ) $cat_check_status = '<div style="color:#f44336">Категории новостей обновляются</div>';
    else $cat_check_status = '<div style="color:#009688">Категории новостей обновлены</div>';
}

if ( $aaparser_config['update_news']['xf_check'] ) {
    $xf_check_db = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_anime_list WHERE news_update=1" );
    if ( $xf_check_db['count'] > 0 ) $xf_check_status = '<div style="color:#f44336">Доп. поля новостей обновляются</div>';
    else $xf_check_status = '<div style="color:#009688">Доп. поля новостей обновлены</div>';
}
if ( $aaparser_config['main_fields']['xf_shikimori_id'] && $aaparser_config['main_fields']['xf_mdl_id'] ) {
    $added_news = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE xfields LIKE '%".$aaparser_config['main_fields']['xf_shikimori_id']."|%' OR xfields LIKE '%".$aaparser_config['main_fields']['xf_mdl_id']."|%'" );
    if ( $done['count'] && $added_news['count'] && $done['count'] < $added_news['count'] ) {
        $base_status = '<div style="color:#ef5350"><i class="fa fa-wrench position-left"></i>Требуется связывание, нажмите кнопку ниже</div>';
    }
}
elseif ( $aaparser_config['main_fields']['xf_shikimori_id'] ) {
    $added_news = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE xfields LIKE '%".$aaparser_config['main_fields']['xf_shikimori_id']."|%'" );
    if ( $done['count'] && $added_news['count'] && $done['count'] < $added_news['count'] ) {
        $base_status = '<div style="color:#ef5350"><i class="fa fa-wrench position-left"></i>Требуется связывание, нажмите кнопку ниже</div>';
    }
}
elseif ( $aaparser_config['main_fields']['xf_mdl_id'] ) {
    $added_news = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE xfields LIKE '%".$aaparser_config['main_fields']['xf_mdl_id']."|%'" );
    if ( $done['count'] && $added_news['count'] && $done['count'] < $added_news['count'] ) {
        $base_status = '<div style="color:#ef5350"><i class="fa fa-wrench position-left"></i>Требуется связывание, нажмите кнопку ниже</div>';
    }
}

if ( isset($aaparser_config['push_notifications']['fa_icons_rooms']) ) $fa_icons_rooms = $aaparser_config['push_notifications']['fa_icons_rooms'];
else $fa_icons_rooms = 'fa';

$now_year = date('Y');

echo <<<HTML
<style>
HTML;
require_once ENGINE_DIR .'/mrdeath/aaparser/admin/styles.css';
echo <<<HTML
</style>

<script>
HTML;
require_once ENGINE_DIR .'/mrdeath/aaparser/admin/scripts.js';
echo <<<HTML
</script>

HTML;
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/navbar.php'; //Навигация

// Проверка доступа к Data и его элементам
$data_items = glob( ENGINE_DIR . "/mrdeath/aaparser/data/*");
foreach ($data_items as $data_item_f) {
	if (Permer($data_item_f)) echo "<div class='alert alert-danger'>Выставьте права 777 для {$data_item_f}</div>";
}
$perm_f = ENGINE_DIR.'/mrdeath/aaparser/data/';
if (Permer($perm_f)) echo "<div class='alert alert-danger'>Выставьте права 777 для {$perm_f}</div>";

echo <<<HTML
<form action="" method="post" class="systemsettings">
HTML;
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/mainpage.php'; //Основные настройки
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/grabpage.php'; //Настройки граббинга
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/updatenewspage.php'; //Обновление новостей
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/updatespage.php'; //Поднятие новостей
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/xfieldspage.php'; //Основные и доп поля
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/catspage.php'; //Категории
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/imagespage.php'; //Изображения
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/integrationpage.php'; //Интеграция
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/cronpage.php'; //Настройки планировщика
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/anonspage.php'; //Настройки Анонса
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/faqpage.php'; //Часто задаваемые вопросы
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/modulespage.php'; //Модули
echo <<<HTML
    <button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</form>
HTML;
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/scripts.php'; // Скрипты 
echo <<<HTML
<div class="panel" style="margin-top: 20px;">
	<div class="panel-content">
		<div class="panel-body">
			Разработано в 2022-{$now_year} году
		</div>
	</div>
</div>
HTML;
echofooter();
}
elseif ( $action == 'dbupgrade' ) {
    
        $versions = array();
		$files = glob( ENGINE_DIR . "/mrdeath/aaparser/includes/upgrade/*");
		
		foreach ($files as $file) {
			$version = basename( $file, ".php" );
			
			if( version_compare( $log_module_version, $version, '<') ) {
				$versions[] = $version;
			}
			
		}
		
		$total = count($versions);
		
		//$versions[] = $actual_module_version;
		
		sort($versions, SORT_NUMERIC);
		
		$versions = "['".implode("','", $versions)."']";
    
echo <<<HTML
<script>

	var actualversion = '{$actual_module_version}';
	var total = {$total};
	var versions = {$versions};
	var step = 0;
	var versions_info = '{$lang['upgr_db_ver']}';

	function db_upgrade()  {
	
		var version = versions[step];
		step ++;
		
		$('#button').attr("disabled", "disabled");
		$('#wconvert').html(versions_info + ' <b>' + version + '</b>');
		$('#ajaxerror').html('');
		
		$.ajax({
		    url: '/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear',
		    data: {action: "update_module", version: version, user_hash: dle_login_hash},
			dataType: "json",
			cache: false,
		    success: function (data) {
				console.log(data);
				if ( data.status == "ok" ) {
				    var proc = Math.round( (100 * step) / total );
				    if ( proc > 100 ) proc = 100;
				    $('#progressbar').css( "width", proc + '%' );
				    if (data.version == actualversion) {
				        setTimeout("window.location = '?mod=aap'", 1000 );
				    }
				    else {
				        setTimeout("db_upgrade()", 1000 );
				    }
				}
				else {
				    //var proc = Math.round( (100 * step) / total );
				    //if ( proc > 100 ) proc = 100;
				    //$('#progressbar').css( "width", proc + '%' );
				}
			}
		});
	
		return false;
	
	}
	
	$(function() {
		
		$('#button').click(function() {
			$('#button').attr("disabled", "disabled");
			db_upgrade();
			return false;
		});
		
		{$autostart}

	});

</script>

	<div class="panel panel-default">
	  <div class="panel-heading">
		Мастер обновления модуля Advanced Anime Parser
	  </div>
		<div class="panel-body">
			Сейчас будет произведено обновление вашей базы данных до текущей версии модуля. Ваша база данных будет пошагово обновлена с версии {$log_module_version} до {$actual_module_version}.
		</div>
		<div class="panel-body">
			<div class="progress"><div id="progressbar" class="progress-bar progress-blue" style="width:0%;"><span></span></div></div>
			<div class="text-size-small"><span id="wconvert"></span> <span id="status"></span></div>
		</div>
		<div class="panel-body">
			<div id="ajaxerror"></div>
			<div class="text-muted text-size-small">{$lang['upgr_noclose_2']}</div>
		</div>	
		<div class="panel-footer">
			<button id="button" type="button" class="btn bg-teal btn-sm btn-raised"><i class="fa fa-forward position-left"></i>{$lang['upgr_next']}</button>
		</div>
	</div>
HTML;
    
echofooter();
}
?>