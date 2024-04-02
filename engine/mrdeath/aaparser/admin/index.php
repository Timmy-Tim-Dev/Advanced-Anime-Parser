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
  	fwrite($fp, $text);
  	fclose($fp);
  	unset($text);
}

if (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/config_push.php')) {
$text = <<<HTML
<?PHP

//AAParser Push Settings 

\$aaparser_config_push = array (
  'push_notifications' => 
  array (

  )
);

?>
HTML;

  	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/config_push.php', "w+");
  	fwrite($fp, $text);
  	fclose($fp);
  	unset($text);
}

if (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/kodik.log')) {
  	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/kodik.log', "w+");
  	fwrite($fp, "");
  	fclose($fp);
}

if (!file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/cron.log')) {
  	$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/data/cron.log', "w+");
  	fwrite($fp, "");
  	fclose($fp);
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
require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
require_once ENGINE_DIR.'/mrdeath/aaparser/data/config_push.php';
require_once ENGINE_DIR.'/mrdeath/aaparser/functions/admin.php';

if ( !file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/indexing.php') ) {
	if (isset($aaparser_config['settings_gindexing']['account'])){
		$mod_settings['account'] = $aaparser_config['settings_gindexing']['account'];
	}
} 

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
if ( $aaparser_config['fields']['xf_shikimori_id'] && $aaparser_config['fields']['xf_mdl_id'] ) {
    $added_news = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE xfields LIKE '%".$aaparser_config['fields']['xf_shikimori_id']."|%' OR xfields LIKE '%".$aaparser_config['fields']['xf_mdl_id']."|%'" );
    if ( $done['count'] && $added_news['count'] && $done['count'] < $added_news['count'] ) {
        $base_status = '<div style="color:#ef5350"><i class="fa fa-wrench position-left"></i>Требуется связывание, нажмите кнопку ниже</div>';
    }
}
elseif ( $aaparser_config['fields']['xf_shikimori_id'] ) {
    $added_news = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE xfields LIKE '%".$aaparser_config['fields']['xf_shikimori_id']."|%'" );
    if ( $done['count'] && $added_news['count'] && $done['count'] < $added_news['count'] ) {
        $base_status = '<div style="color:#ef5350"><i class="fa fa-wrench position-left"></i>Требуется связывание, нажмите кнопку ниже</div>';
    }
}
elseif ( $aaparser_config['fields']['xf_mdl_id'] ) {
    $added_news = $db->super_query( "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE xfields LIKE '%".$aaparser_config['fields']['xf_mdl_id']."|%'" );
    if ( $done['count'] && $added_news['count'] && $done['count'] < $added_news['count'] ) {
        $base_status = '<div style="color:#ef5350"><i class="fa fa-wrench position-left"></i>Требуется связывание, нажмите кнопку ниже</div>';
    }
}

if ( isset($aaparser_config_push['push_notifications']['fa_icons_rooms']) ) $fa_icons_rooms = $aaparser_config_push['push_notifications']['fa_icons_rooms'];
else $fa_icons_rooms = 'fa';

$now_year = date('Y');

echoheader('<b>Advanced Kodik Parser v4.0.0</b>', 'Настройки модуля Advanced Kodik Parser');

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
$perm_f = ENGINE_DIR.'/mrdeath/aaparser/data/';
$perm_c = ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
$perm_cp = ENGINE_DIR.'/mrdeath/aaparser/data/config_push.php';
if (Permer($perm_f)) echo "<div class='alert alert-danger'>Выставьте права 777 для {$perm_f}</div>";
if (Permer($perm_c)) echo "<div class='alert alert-danger'>Выставьте права 777 для {$perm_c}</div>";
if (Permer($perm_cp)) echo "<div class='alert alert-danger'>Выставьте права 777 для {$perm_cp}</div>";

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
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/playerpage.php'; //Вывод плеера
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/integrationpage.php'; //Интеграция
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/pushpage.php'; //Push уведомления
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/roomspage.php'; //Совместный просмотр
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/cronpage.php'; //Настройки планировщика
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/anonspage.php'; //Настройки Анонса
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/gindexpage.php'; //Google indexing
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/tgpostingpage.php'; //Постинг в Telegram
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
?>