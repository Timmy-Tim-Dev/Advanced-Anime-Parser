<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
 
if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

function Permer($fileperms) {
	if (file_exists($fileperms)) {
		// $permsString = substr(sprintf('%o', fileperms($fileperms)), -4);
		// if (strpos($permsString, '777') != true || !is_writable($fileperms)) {
    if (!is_writable($fileperms)) {
			return true;
		} else {
			return false;
		}
	}
}

function showRow($title = "", $description = "", $field = "", $class = "") {
	echo "<tr>
       <td class=\"col-xs-10 col-sm-6 col-md-7 {$class}\"><h6><b>{$title}:</b></h6><span class=\"note large\">{$description}</span></td>
       <td class=\"col-xs-2 col-md-5 settingstd {$class}\">{$field}</td>
       </tr>";
}

function showInput($data)
{
	$input_elemet = $data[3] ? " placeholder=\"{$data[3]}\"" : '';
	$input_elemet .= $data[4] ? ' disabled' : '';
	if ($data[1] == 'range') {
		$class = ' custom-range';
		$input_elemet .= $data[5] ? " step=\"{$data[5]}\"" : '';
		$input_elemet .= $data[6] ? " min=\"{$data[6]}\"" : '';
		$input_elemet .= $data[7] ? " max=\"{$data[7]}\"" : '';
	} elseif ($data[1] == 'number') {
		$class = ' w-9';
		$input_elemet .= $data[5] ? " min=\"{$data[5]}\"" : '';
		$input_elemet .= $data[6] ? " max=\"{$data[6]}\"" : '';
	}
return <<<HTML
	<input type="{$data[1]}" autocomplete="off" style="float: right;" value="{$data[2]}" class="form-control{$class}" name="{$data[0]}"{$input_elemet}>
HTML;
}

function showtextarea($name)
{
echo <<<HTML
<tr>
	<td>
		<label style="float:left;" class="form-label"><b>{$name}</b></label>
        <textarea id="url-list" style="min-height:150px;max-height:150px;min-width:333px;max-width:100%;border: 1px solid #ddd;padding: 5px;" autocomplete="off" class="form-control" name="url-list" placeholder="Каждая ссылка с новой строки, лимит 100 ссылок за раз"></textarea>
        <button onclick="SendMass(); return false;" class="btn bg-slate-600 btn-raised position-left"><i class="fa fa-envelope-o position-left"></i>Отправить</button>
    </td>
</tr>
HTML;
}

function makeCheckBox($name, $selected, $function_name = false)
{
		$selected = $selected ? "checked" : "";
		if ( $function_name == "ShowOrHidePlayer" ) return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" id=\"player_on_off\" value=\"1\" onchange=\"$function_name();\" {$selected}>";
		elseif ( $function_name == "ShowOrHidePush" ) return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" id=\"push_on_off\" value=\"1\" onchange=\"$function_name();\" {$selected}>";
		elseif ( $function_name == "ShowOrHideRooms" ) return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" id=\"rooms_on_off\" value=\"1\" onchange=\"$function_name();\" {$selected}>";
		elseif ( $function_name == "ShowOrHideGindexing" ) return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" id=\"google_indexing\" value=\"1\" onchange=\"$function_name();\" {$selected}>";
		else return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";
}

function showSelect($name, $value, $check = false)
{
	if(!$check) $multiple = "multiple";
	return "<select data-placeholder=\""."".$phrases_settings['category_chose']."\" name=\"{$name}\" id=\"category\" class=\"valueselect\" {$multiple} style=\"width:100%;max-width:350px;\">{$value}</select>";
}

function makeDropDown($options, $name, $selected, $function_name = false) {
        if ( $function_name ) $output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"$name\" onchange=\"$function_name(this.value)\">\r\n";
        else $output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"$name\" id=\"$name\">\r\n";
        foreach ( $options as $value => $description ) {
            $output .= "<option value=\"$value\"";
            if( $selected == $value ) {
                $output .= " selected ";
            }
            $output .= ">$description</option>\n";
        }
        $output .= "</select>";
        return $output;
    }
    
function makeDropDownAlt($options, $name, $selected) {
	$output = "<select class=\"uniform\" style=\"opacity:0;\" name=\"$name\" id=\"$name\">\r\n";
	$output .= "<option value=''>Выберите файл</option>";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"$description\"";
		if( $selected == $description ) {
			$output .= " selected ";
		}
		$output .= ">$description</option>\n";
	}
	$output .= "</select>";
	return $output;
}

function showTrInline($name, $description, $type, $data)
{
echo <<<HTML
<tr>
	<td>
		<label style="float:left;" class="form-label"><b>{$name}</b></label>
HTML;
	switch ($type) {
		case 'input':
			echo showInput($data);
		break;
		case 'textarea':
			echo textareaForm($data);
		break;
		default:
			echo $data;
		break;
	}
echo <<<HTML
</tr>
HTML;
}
	
function textareaForm($data)
{
	$input_elemet = $data[2] ? " placeholder=\"{$data[2]}\"" : '';
	$input_elemet .= $data[3] ? ' disabled' : '';
return <<<HTML
	<textarea style="min-height:150px;max-height:150px;min-width:333px;max-width:100%;border: 1px solid #ddd;padding: 5px;" autocomplete="off" class="form-control" name="{$data[0]}"{$input_elemet}>{$data[1]}</textarea>
HTML;
}

function ShowSelected($data)
{
	foreach ($data[1] as $key => $val) {
		if ($data[2]) {
			$output .= "<option value=\"{$key}\"";
		} else {
			$output .= "<option value=\"{$val}\"";
		}
		if (is_array($data[3])) {
			foreach ($data[3] as $element) {
				if ($data[2] && $element == $key) {
					$output .= " selected ";
				} elseif (!$data[2] && $element == $val) {
					$output .= " selected ";
				}
			}
		} elseif ($data[2] && $data[3] == $key) {
			$output .= " selected ";
		} elseif (!$data[2] && $data[3] == $val) {
			$output .= " selected ";
		}
		$output .= ">{$val}</option>\n";
	}
	$input_elemet = $data[5] ? ' disabled' : '';
	$input_elemet .= $data[4] ? ' multiple' : '';
	$input_elemet .= $data[6] ? " data-placeholder=\"{$data[6]}\"" : '';
return <<<HTML
<select name="{$data[0]}" class="form-control custom-select" {$input_elemet}>
	{$output}
</select>
HTML;
}

function makeSelect($array, $name, $data, $placeholder, $mode)
{
    $ar_ray = explode(',', $data);
    $options = [];
    foreach ($array as $key => $value) {
        if ( $mode == 1 ) $key = $value;
	    if (in_array($key, $ar_ray)) {
	    	$options[] = '<option value="'.$key.'" selected>'.$value.'</option>';
	    }
	    else {
	    	$options[] = '<option value="'.$key.'">'.$value.'</option>';
	    }
    }
    if ( $options ) return '<select data-placeholder="'.$placeholder.'" name="'.$name.'[]" id="'.$name.'" class="valuesselect" multiple style="width:100%;max-width:350px;">'.implode('', $options).'</select>';
    else return '<select data-placeholder="'.$placeholder.'" name="'.$name.'[]" id="'.$name.'" class="valuesselect" multiple style="width:100%;max-width:350px;"></select>';
}

$data_list = array( "shikimori_id", "shikimori_name", "shikimori_russian", "shikimori_english", "shikimori_japanese", "shikimori_synonyms", "shikimori_license_name_ru", "shikimori_kind", "shikimori_kind_ru", "shikimori_score", "shikimori_votes", "shikimori_status", "shikimori_status_ru", "shikimori_episodes", "shikimori_episodes_aired", "shikimori_episodes_aired_1", "shikimori_episodes_aired_2", "shikimori_episodes_aired_3", "shikimori_episodes_aired_4", "shikimori_episodes_aired_5", "shikimori_episodes_aired_6", "shikimori_episodes_aired_7", "shikimori_episodes_aired_8", "shikimori_aired_on", "shikimori_aired_on_2", "shikimori_aired_on_3", "shikimori_aired_on_4", "shikimori_released_on", "shikimori_released_on_2", "shikimori_released_on_3", "shikimori_released_on_4", "shikimori_year", "shikimori_season", "shikimori_rating", "shikimori_duration", "shikimori_duration_2", "shikimori_duration_3", "shikimori_duration_4", "shikimori_plot", "shikimori_genres", "shikimori_studios", "shikimori_videos", "myanimelist_id", "official_site", "wikipedia", "anime_news_network", "anime_db", "world_art", "kinopoisk", "kage_project", "shikimori_director", "shikimori_producer", "shikimori_script", "shikimori_composition", "shikimori_franshise", "shikimori_similar", "shikimori_related" );
$data_list_kodik = array( "shikimori_id", "mydramalist_id", "image", "kadr_1", "kadr_2", "kadr_3", "kadr_4", "kadr_5", "kodik_title", "kodik_title_orig", "kodik_other_title", "kodik_year", "kodik_worldart_link", "kodik_mydramalist_tags", "kodik_status_en", "kodik_status_ru", "kodik_status_en_voice", "kodik_status_ru_voice", "kodik_status_en_sub", "kodik_status_ru_sub", "kodik_status_en_autosub", "kodik_status_ru_autosub", "kodik_premiere_ru", "kodik_premiere_ru_2", "kodik_premiere_ru_3", "kodik_premiere_ru_4", "kodik_premiere_world", "kodik_premiere_world_2", "kodik_premiere_world_3", "kodik_premiere_world_4", "kodik_iframe", "kodik_quality", "kodik_kinopoisk_id", "kodik_imdb_id", "kodik_translation", "kodik_translation_last", "kodik_translation_types", "kodik_translation_types_ru", "kodik_tagline", "kodik_plot", "kodik_duration", "kodik_duration_2", "kodik_duration_3", "kodik_duration_4", "kodik_video_type", "kodik_countries", "kodik_genres", "kodik_kinopoisk_rating", "kodik_kinopoisk_votes", "kodik_imdb_rating", "kodik_imdb_votes", "kodik_mydramalist_rating", "kodik_mydramalist_votes", "kodik_minimal_age", "kodik_rating_mpaa", "kodik_actors", "kodik_directors", "kodik_producers", "kodik_writers", "kodik_composers", "kodik_editors", "kodik_designers", "kodik_operators", "kodik_last_season", "kodik_last_season_1", "kodik_last_season_2", "kodik_last_season_3", "kodik_last_season_4", "kodik_last_season_5", "kodik_last_season_6", "kodik_last_season_7", "kodik_last_season_8", "kodik_last_season_translated", "kodik_last_season_subtitles", "kodik_last_season_autosubtitles", "kodik_last_episode", "kodik_last_episode_1", "kodik_last_episode_2", "kodik_last_episode_3", "kodik_last_episode_4", "kodik_last_episode_5", "kodik_last_episode_6", "kodik_last_episode_7", "kodik_last_episode_8", "kodik_last_episode_translated", "kodik_last_episode_subtitles", "kodik_last_episode_autosubtitles", "kodik_episodes_total", "kodik_episodes_aired", "catalog_rus", "catalog_eng" );

$xfield_list = xfieldsload();

$xfields_list = ['-' => '-'];
$xfield_image = ['-' => '-'];
$xfield_gallery = ['-' => '-'];
$xfield_yesorno = ['-' => '-'];
$xfield_select = ['-' => '-'];
$xfields_all_list = ['-' => '-'];
$main_fields = [
    'title' => 'Заголовок',
    'short_story' => 'Краткое описание',
    'full_story' => 'Полное описание',
    'alt_name' => 'ЧПУ URL статьи',
    'meta_title' => 'Метатег Title',
    'meta_description' => 'Метатег Description',
    'meta_keywords' => 'Метатег Keywords',
    'tags' => 'Ключевые слова для облака тегов',
    'catalog' => 'Буквенный каталог'
];

for ($i = 0; $i < count($xfield_list); $i++) {
    $xfields_all_list[$xfield_list[$i][0]] = $xfield_list[$i][1];
	if ( $xfield_list[$i][3] == "text" OR $xfield_list[$i][3] == "textarea" OR $xfield_list[$i][3] == "htmljs" ) {
	    $main_fields[$xfield_list[$i][0]] = $xfield_list[$i][1];
	    $xfields_list[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "image" ) {
	    $xfield_image[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "imagegalery" ) {
	    $xfield_gallery[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "yesorno" ) {
	    $xfield_yesorno[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "select" ) {
	    $xfield_select[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
}

$cat_options = [];
$cat_options[0] = '-';
foreach ($cat_info as $cat) {
  	$cat_options[$cat['id']] = $cat['name'];
}

$screens_count = array("1" => "1 скриншот","2" => "2 скриншота","3" => "3 скриншота","4" => "4 скриншота","5" => "5 скриншотов");
$cat_status_upd = array(0 => "выключено","1" => "менять категории-статусы","2" => "полная замена всех категорий");
$xf_fields_upd = array(0 => "выключено","1" => "перезаписывать только пустые(дополнять)","2" => "полная перезапись полей");
$working_mode = array(0 => "аниме","1" => "дорамы","2" => "аниме и дорамы");
$fa_icons = array('fa' => 'fa','fas' => 'fas (Solid Style)','far' => 'far (Regular Style)','fal' => 'fal (Light Style)','fad' => 'fad (Duotone Style)');

$year_array = array();
$years_array = array();
for ($i = 1910; $i <= (date("Y", time())+1); $i++) {
    $year_array[$i] = $i.' год';
    $years_array[] = $i;
}
krsort($year_array);
krsort($years_array);

$c_time = time()-604800;
if ($aaparser_config['settings']['working_mode'] == 1) {
	if (!file_exists(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name_dorama.json") || filectime(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name_dorama.json") < $c_time) {
		//dorama
		$cont = file_get_contents($aaparser_config['settings']['kodik_api_domain']."translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=foreign-movie,foreign-serial");
		$cont = json_decode($cont, true);
		$translators_name = $translators = [];
		if (isset($cont['results'])) {
			foreach ($cont['results'] as $result) {
				$translators_name[] = $result['title'];
				$translators[$result['id']] = $result['title'];
			}
		}
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		unset ($translators_name);
		unset ($translators);
		unset ($cont);
	}
} elseif ($aaparser_config['settings']['working_mode'] == 2) {
	if (!file_exists(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name.json") || !file_exists(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name_dorama.json") || filectime(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name.json") < $c_time || filectime(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name_dorama.json") < $c_time) {
		//dorama
		$cont = file_get_contents($aaparser_config['settings']['kodik_api_domain']."translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=foreign-movie,foreign-serial");
		$cont = json_decode($cont, true);
		$translators_name = $translators = [];
		if (isset($cont['results'])) {
			foreach ($cont['results'] as $result) {
				$translators_name[] = $result['title'];
				$translators[$result['id']] = $result['title'];
			}
		}
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		unset ($translators_name);
		unset ($translators);
		unset ($cont);
		//anime
		$cont = file_get_contents($aaparser_config['settings']['kodik_api_domain']."translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=anime,anime-serial");
		$cont = json_decode($cont, true);
		$translators_name = $translators = [];
		if (isset($cont['results'])) {
			foreach ($cont['results'] as $result) {
				$translators_name[] = $result['title'];
				$translators[$result['id']] = $result['title'];
			}
		}
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		unset ($translators_name);
		unset ($translators);
		unset ($cont);
	}
} else {
	if (!file_exists(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name.json") || filectime(ROOT_DIR."/engine/mrdeath/aaparser/data/translators_name.json") < $c_time) {
		//anime
		$cont = file_get_contents($aaparser_config['settings']['kodik_api_domain']."translations/v2?token=".$aaparser_config['settings']['kodik_api_key']."&types=anime,anime-serial");
		$cont = json_decode($cont, true);
		$translators_name = $translators = [];
		if (isset($cont['results'])) {
			foreach ($cont['results'] as $result) {
				$translators_name[] = $result['title'];
				$translators[$result['id']] = $result['title'];
			}
		}
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json', json_encode($translators_name, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json', json_encode($translators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		unset ($translators_name);
		unset ($translators);
		unset ($cont);
	}
}

if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json') ) {
    $translator_array = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json');
    $translator_array = json_decode($translator_array, true);
	$translator_array = preg_replace('/"([^"]*?)"(?=[^"]*?"|$)/', '\"$1\"', $translator_array);
}
else $translator_array = [];

if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json') ) {
    $translators_array = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators.json');
    $translators_array = json_decode($translators_array, true);
	$translators_array = preg_replace('/"([^"]*?)"(?=[^"]*?"|$)/', '\"$1\"', $translators_array);
}
else $translators_array = [];

if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json') ) {
    $translator_array_dorama = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json');
    $translator_array_dorama = json_decode($translator_array_dorama, true);
	$translator_array_dorama = preg_replace('/"([^"]*?)"(?=[^"]*?"|$)/', '\"$1\"', $translator_array_dorama);
}
else $translator_array_dorama = [];

if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json') ) {
    $translators_array_dorama = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/data/translators_dorama.json');
    $translators_array_dorama = json_decode($translators_array_dorama, true);
	$translators_array_dorama = preg_replace('/"([^"]*?)"(?=[^"]*?"|$)/', '\"$1\"', $translators_array_dorama);
}
else $translators_array_dorama = [];



if (!file_exists(ROOT_DIR."/engine/mrdeath/aaparser/data/mydramalist.json") || filectime(ROOT_DIR."/engine/mrdeath/aaparser/data/mydramalist.json") < $c_time)
{
	$cont = file_get_contents($aaparser_config['settings']['kodik_api_domain']."genres?token=".$aaparser_config['settings']['kodik_api_key']."&genres_type=mydramalist");
	file_put_contents(ROOT_DIR."/engine/mrdeath/aaparser/data/mydramalist.json", $cont);
}
if (!file_exists(ROOT_DIR."/engine/mrdeath/aaparser/data/shikimori.json") || filectime(ROOT_DIR."/engine/mrdeath/aaparser/data/shikimori.json") < $c_time)
{
	$cont = file_get_contents($aaparser_config['settings']['kodik_api_domain']."genres?token=".$aaparser_config['settings']['kodik_api_key']."&genres_type=shikimori");
	file_put_contents(ROOT_DIR."/engine/mrdeath/aaparser/data/shikimori.json", $cont);	
}
if (!file_exists(ROOT_DIR."/engine/mrdeath/aaparser/data/kinopoisk.json") || filectime(ROOT_DIR."/engine/mrdeath/aaparser/data/kinopoisk.json") < $c_time)
{
	$cont = file_get_contents($aaparser_config['settings']['kodik_api_domain']."genres?token=".$aaparser_config['settings']['kodik_api_key']."&genres_type=kinopoisk");
	file_put_contents(ROOT_DIR."/engine/mrdeath/aaparser/data/kinopoisk.json", $cont);	
}


$genres_array_dorama = array();
$genres_array = array();

$mydramalist = file_get_contents(ROOT_DIR."/engine/mrdeath/aaparser/data/mydramalist.json");
$mydramalist = json_decode($mydramalist, true);
foreach ($mydramalist['results'] as $item)
{
	$genres_array_dorama[] = trim($item['title']);
}

$shikimori = file_get_contents(ROOT_DIR."/engine/mrdeath/aaparser/data/shikimori.json");
$shikimori = json_decode($shikimori, true);
foreach ($shikimori['results'] as $item)
{
	$genres_array[] = trim($item['title']);
}


$kinopoisk = file_get_contents(ROOT_DIR."/engine/mrdeath/aaparser/data/kinopoisk.json");
$kinopoisk = json_decode($kinopoisk, true);
foreach ($kinopoisk['results'] as $item)
{
	$genres_array[] = trim($item['title']);
	$genres_array_dorama[] = trim($item['title']);
}



$type_array = array("аниме", "ТВ-сериал", "OVA", "Фильм", "Полнометражный фильм", "Короткометражный фильм", "Спэшл", "ONA", "AMV", "Анонс", "Онгоинг", "Завершён", "Озвучка", "Субтитры");

$type_array_dorama = array("дорама", "сериал", "фильм", "Анонс", "Онгоинг", "Завершён", "Озвучка", "Субтитры");

//$genres_array = ["безумие","боевые искусства","вампиры","военное","гарем","гурман","демоны","детектив","детское","дзёсей","драма","игры","исторический","комедия","космос","магия","машины","меха","музыка","пародия","повседневность","полиция","приключения","психологическое","работа","романтика","самураи","сверхъестественное","спорт","супер сила","сэйнэн","сёдзё","сёдзё-ай","сёнен","сёнен-ай","триллер","ужасы","фантастика","фэнтези","школа","экшен","этти"];

//$genres_array_dorama = ["sci-fi","бизнес","боевик","боевые искусства","взрослая жизнь","военное","война","восточные единоборства","документальный","драма","гурман","закон","исторический","комедия","криминал","медицина","мелодрама","мистика","молодость","музыка","повседневность","политика","приключения","психологическое","романтика","сверхъестественное","семейный","ситком","спорт","супер сила","триллер","ужасы","фэнтези"];

$collections_array = ["футбол", "автогонки", "баскетбол", "бейсбол", "бокс", "теннис", "волейбол", "велоспорт", "маджонг", "фигурное катание", "карточные игры", "сёги", "регби", "спортивная борьба", "кэндо", "мотогонки", "плавание", "наше время", "будущее", "период Ямато", "период Нара", "период Хэйан", "период Камакура", "период Муромати", "период Сэнгоку", "период Эдо", "период Бакумацу", "период Мэйдзи", "первая половина 20 века", "вторая мировая война", "вторая половина 20 века", "Викторианская эпоха", "взрослый герой", "взрослая героиня", "школьница", "школьник", "студенты", "ребёнок и взрослый", "дети во взрослом мире", "сильная героиня", "сильный герой", "бисёнэны", "антигерой", "супергерой", "яндэрэ", "цундэрэ", "кудэрэ", "андрогин", "готическая лолита", "раздвоение личности", "дандэрэ", "генки", "брат и сестра", "братья", "сёстры", "близнецы", "сироты", "отаку", "хикикомори", "хулиганы", "лётчики", "полицейские", "военные", "айдолы", "морские пираты", "космические авантюристы", "мангаки", "шпионы", "учителя", "ниндзя", "самураи", "врачи", "оммёдзи", "горничная", "рыцари", "фермеры", "инопланетяне", "андроиды", "киборги", "вампиры", "драконы", "призраки", "зомби", "демоны", "зверолюди", "эльфы", "божества", "русалки", "ангелы", "ёкаи", "феи", "кентавры", "паропанк", "киберпанк", "космическая опера", "пилотируемые роботы", "экзоскелет", "дизельпанк", "тёмное фэнтези", "технофэнтези", "классическое фэнтези", "реверс-гарем", "гарем", "любовный треугольник", "сёдзё-ай", "сёнэн-ай", "ангст", "сюрреализм", "сатира / пародия", "притча", "кайдан / японские городские легенды", "нуар", "постапокалиптический мир", "вымышленный мир", "альтернативная история", "антиутопия", "mmorpg", "вестерн", "параллельные миры", "Европа: Россия", "Европа: Франция", "Европа: Италия", "Европа: Англия", "Европа: Греция", "Европа: Германия", "Европа: вымышленная страна", "Америка: США / Канада", "Китай", "Марс", "постапокалиптический мир", "вымышленный мир", "альтернативная история", "антиутопия", "mmorpg", "вестерн", "параллельные миры", "море", "под одной крышей", "космический корабль", "глубинка", "тюрьма", "театр кабуки", "академия магии", "университет", "музыкальная группа", "аниме-индустрия", "школьный клуб", "школа", "школьный совет", "3D-графика", "кукольная анимация", "сплошной позитив", "депрессивная атмосфера", "трагические сцены", "жестокие сцены", "политические интриги", "путешествие в другой мир", "кулинария", "дружба", "путешествие во времени", "суперсила", "достижение цели", "работа и карьера", "восстание", "поиск родителей", "гендерная интрига", "битва умов", "потеря памяти", "обмен телами", "турнир", "дилемма", "спасение мира", "компьютерные технологии", "преступные организации", "семейные отношения", "классическая музыка", "моэ / кавай", "королевская битва", "выживание", "месть", "хэнсин", "японская классическая литература", "роуд-муви", "животные и люди", "по произведениям западных авторов", "прокси-битвы", "внутренние монологи", "взросление", "только девушки", "только парни", "маньяки", "кроссовер", "откровенные сцены", "садомазохизм", "тяжелая болезнь", "разница в возрасте", "травля", "несколько сюжетных линий", "noitamina", "comicfesta", "супердеформ", "world masterpiece theater"];

$country_array = ["Австралия", "Австрия", "Азербайджан", "Албания", "Алжир", "Американское Самоа", "Ангилья", "Англия", "Ангола", "Андорра", "Антигуа и Барбуда", "Аргентина", "Армения", "Аруба", "Афганистан", "Багамы", "Бангладеш", "Барбадос", "Бахрейн", "Бейкер", "Белиз", "Белоруссия", "Бельгия", "Бенилюкс", "Бенин", "Болгария", "Боливия", "Бонэйр", "Бопутатсвана", "Босния и Герцеговина", "Ботсвана", "Бразилия", "Бруней", "Буркина-Фасо", "Бурунди", "Бутан", "Вануату", "Ватикан", "Великобритания", "Венгрия", "Венда", "Венесуэла", "Вьетнам", "Габон", "Гаити", "Гайана", "Гамбия", "Гана", "Гватемала", "Гвинея", "Гвинея-Бисау", "Германия", "Гернси", "Гибралтар", "Гондурас", "Гонконг", "Сомали", "Гренада", "Греция", "Грузия", "Гуам", "Дания", "Конго", "Косово", "Джибути", "Джонстон", "Джубаленд", "Доминика", "Доминикана", "Египет", "Замбия", "Зимбабве", "Израиль", "Имамат Оман", "Индия", "Индонезия", "Иордания", "Ирак", "Иран", "Ирландия", "Исландия", "Испания", "Италия", "Йемен", "Султанат Касири", "Кабо-Верде", "Казахстан", "Камбоджа", "Камерун", "Канада", "Катар", "Кашубия", "Кенедугу", "Кения", "Киргизия", "Кирибати", "Китай", "Колумбия", "Коморы", "Конго", "Корея Северная", "Корея Южная", "Нидерланды", "Конго", "Коста-Рика", "Куба", "Кувейт", "Кюрасао", "Лаос", "Латвия", "Лесото", "Либерия", "Ливан", "Ливия", "Литва", "Лихтенштейн", "Люксембург", "Маврикий", "Мавритания", "Мадагаскар", "Малави", "Малайзия", "Мали", "Мальдивы", "Мальта", "Марокко", "Мартиазо", "Мексика", "Мидуэй", "Мозамбик", "Молдавия", "Молдова", "Монако", "Монголия", "Монтсеррат", "Мьянма", "Намибия", "Науру", "Непал", "Нигер", "Нигерия", "Нидерланды", "Никарагуа", "Ниуэ", "Новая Зеландия", "Новая Каледония", "Норвегия", "Остров Норфолк", "ОАЭ", "Оман", "Пакистан", "Палау", "Панама", "Парагвай", "Перу", "Польша", "Португалия", "Пуэрто Рико", "Ангилья", "Закистан", "Кипр", "Логон", "Россия", "Руанда", "Румыния", "Сальвадор", "Самоа", "Сан-Марино", "Саудовская Аравия", "Северная Ирландия", "Северная Македония", "Сейшельские Острова ", "Сенегал", "Сент-Люсия", "Сербия", "Силенд", "Сингапур", "Синт-Мартен", "Синт-Эстатиус", "Сирия", "Сискей", "Словакия", "Словения", "Соломоновы Острова", "Сомали", "Сомалиленд", "Судан", "Суринам", "СССР", "США", "Сьерра-Леоне", "Таджикистан", "Таиланд", "Тайвань", "Танзания", "Того", "Токелау", "Тонга", "Торо", "Транскей", "Тринидад", "Тобаго", "Тувалу", "Тунис", "Туркмения", "Турция", "Уганда", "Узбекистан", "Украина", "Уругвай", "Уэйк", "Уэльс", "ФШМ", "Фиджи", "Филиппины", "Финляндия", "Фландренсис", "Фолклендские острова", "Франция", "Французская Полинезия", "Хауленд", "Хиршабелле", "Хорватия", "Центральноафриканская Республика", "Чад", "Черногория", "Чехия", "Чили", "Швейцария", "Швеция", "Шотландия", "Шри-Ланка", "Эквадор", "Экваториальная Гвинея", "Эритрея", "Эсватини", "Эстония", "Эфиопия", "Южная Георгия", "ЮАР", "Южный Судан", "Ямайка", "Япония"];

$category_values = array_unique(array_merge($type_array,$genres_array));
$category_values = array_unique(array_merge($category_values,$years_array));
$category_values = array_unique(array_merge($category_values,$translator_array));
$category_values = array_unique(array_merge($category_values,$collections_array));

if ( $aaparser_config['settings']['working_mode'] == 1 ) {
    $category_values = array_unique(array_merge($type_array_dorama,$genres_array_dorama));
    $category_values = array_unique(array_merge($category_values,$years_array));
    $category_values = array_unique(array_merge($category_values,$country_array));
    $category_values = array_unique(array_merge($category_values,$translator_array_dorama));
}
elseif ( $aaparser_config['settings']['working_mode'] == 2 ) {
    $category_values = array_unique(array_merge($type_array,$type_array_dorama));
    $category_values = array_unique(array_merge($category_values,$genres_array));
    $category_values = array_unique(array_merge($category_values,$genres_array_dorama));
    $category_values = array_unique(array_merge($category_values,$years_array));
    $category_values = array_unique(array_merge($category_values,$translator_array_dorama));
    $category_values = array_unique(array_merge($category_values,$translator_array));
    if ( $aaparser_config['settings']['parse_wa'] == 1 ) $category_values = array_unique(array_merge($category_values,$collections_array));
}
else {
    $category_values = array_unique(array_merge($type_array,$genres_array));
    $category_values = array_unique(array_merge($category_values,$years_array));
    $category_values = array_unique(array_merge($category_values,$translator_array));
    if ( $aaparser_config['settings']['parse_wa'] == 1 ) $category_values = array_unique(array_merge($category_values,$collections_array));
}

if( !$user_group ) $user_group = get_vars( "usergroup" );

$usergroups = [];
foreach ( $user_group as $key => $value ) {
    $usergroups[$value['id']] = $value['group_name'];
}


$fields_description = [
    'id аниме на Shikimori',
    'Оригинальное название аниме',
    'Название аниме на русском',
    'Название аниме на английском',
    'Название аниме на японском',
    'Другие названия аниме',
    'Лицензионное название',
    'Тип аниме на английском',
    'Тип аниме на русском',
    'Рейтинг Shikimori',
    'Количество голосов за аниме на Shikimori',
    'Статус аниме на английском',
    'Статус аниме на русском',
    'Количество серий в сезоне',
    'Последняя вышедшая серия',
    'Последняя вышедшая серия в формате 1-10',
    'Последняя вышедшая серия в формате 1-9,10',
    'Последняя вышедшая серия в формате 1-8,9,10',
    'Последняя вышедшая серия в формате 1,2,3,4,5,6,7,8,9,10',
    'Последняя вышедшая серия +1 в формате 1-11',
    'Последняя вышедшая серия +1 в формате 1-10,11',
    'Последняя вышедшая серия +1 в формате 1-9,10,11',
    'Последняя вышедшая серия +1 в формате 1,2,3,4,5,6,7,8,9,10,11',
    'Дата выхода аниме в формате 2022-04-10',
    'Дата выхода аниме в формате 10.04.2022',
    'Дата выхода аниме в формате 10 апреля 2022',
    'Дата выхода аниме в формате 10-04-2022',
    'Дата завершения выхода в формате 2022-04-10',
    'Дата завершения выхода в формате 10.04.2022',
    'Дата завершения выхода в формате 10 апреля 2022',
    'Дата завершения выхода в формате 10-04-2022',
    'Год выхода',
    'Сезон аниме (например Лето 2022)',
    'Рейтинг по версии MPAA',
    'Дительность аниме в минутах',
    'Дительность аниме в секундах',
    'Дительность аниме в формате 23 мин.',
    'Дительность аниме в формате 23:00',
    'Сюжет аниме',
    'Жанры аниме',
    'Аниме студии',
    'Опенинги и эдинги аниме (ссылки через запятую)',
    'ID на MyAnimeList',
    'Ссылка на официальный сайт',
    'Ссылка на википедию',
    'Ссылка на AnimeNewsNetwork',
    'Ссылка на AnimeDB',
    'Ссылка на World-Art',
    'Ссылка на КиноПоиск',
    'Ссылка на KageProject',
    'Режиссёры аниме',
    'Продюсеры аниме',
    'Сценаристы аниме',
    'Композиторы аниме',
    'ID франшиз',
    'ID похожих аниме',
    'ID связанных аниме',
    'Страны, выпустившие аниме взятые с World-Art',
    'Сюжет аниме взятый с World-Art',
    'Аниме теги взятые с World-Art',
    'Рейтинг аниме на World-Art',
    'Количество голосов на World-Art',
];

$fields_description_kodik = [
    'id аниме на Shikimori',
    'id дорамы на MyDramaList',
    'Обложка',
    'Первый кадр',
    'Второй кадр',
    'Третий кадр',
    'Четвертый кадр',
    'Пятый кадр',
    'Название на русском',
    'Оригинальное название',
    'Другие названия',
    'Год выхода',
    'Ссылка на World-Art',
    'Теги MyDramaList',
    'Статус релиза на английском c базы Kodik',
    'Статус релиза на русском c базы Kodik',
	'Статус релиза на английском только озвучка',
    'Статус релиза на русском только озвучка',
	'Статус релиза на английском только субтитры',
    'Статус релиза на русском только субтитры',
	'Статус релиза на английском только автосубтитры',
    'Статус релиза на русском только автосубтитры',
    'Дата выхода в России в формате 2022-04-10',
    'Дата выхода в России в формате 10.04.2022',
    'Дата выхода в России в формате 10 апреля 2022',
    'Дата выхода в России в формате 10-04-2022',
    'Дата выхода в мире в формате 2022-04-10',
    'Дата выхода в мире в формате 10.04.2022',
    'Дата выхода в мире в формате 10 апреля 2022',
    'Дата выхода в мире в формате 10-04-2022',
    'Ссылка на плеер с базы Kodik',
    'Качество взятое с Kodik',
    'ID КиноПоиск взятое с Kodik',
    'ID IMDB взятое с Kodik',
    'Список озвучек взятый с Kodik',
    'Последняя добавленная в базу озвучка взятая с Kodik',
    'Типы озвучек на английском взятые с Kodik',
    'Типы озвучек на русском взятые с Kodik',
    'Слоган взятый с Kodik',
    'Сюжет взятый с Kodik',
    'Дительность в минутах взятая с Kodik',
    'Дительность в секундах взятая с Kodik',
    'Дительность в формате 23 мин. взятая с Kodik',
    'Дительность в формате 23:00 взятая с Kodik',
    'Тип на основе данных Kodik (фильм или сериал)',
    'Страны, выпустившие сериал/фильм взятый с Kodik',
    'Жанры взятые с Kodik',
    'Рейтинг на КиноПоиске взятый с Kodik',
    'Количество голосов на КиноПоиске взятое с Kodik',
    'Рейтинг на IMDB взятый с Kodik',
    'Количество голосов на IMDB взятое с Kodik',
    'Рейтинг на MyDramaList взятый с Kodik',
    'Количество голосов на MyDramaList взятое с Kodik',
    'Минимальный возраст взятый с Kodik',
    'Рейтинг MPAA взятый с Kodik',
    'Актёры взятые с Kodik',
    'Режиссёры взятые с Kodik',
    'Продюсеры взятые с Kodik',
    'Сценаристы взятые с Kodik',
    'Композиторы взятые с Kodik',
    'Монтажеры взятые с Kodik',
    'Художники взятые с Kodik',
    'Операторы взятые с Kodik',
    'Последний вышедший сезон взятый с Kodik',
    'Последний вышедший сезон взятый с Kodik в формате 1-5',
    'Последний вышедший сезон взятый с Kodik 1-4,5',
    'Последний вышедший сезон взятый с Kodik 1-3,4,5',
    'Последний вышедший сезон взятый с Kodik 1,2,3,4,5',
    'Последний вышедший сезон +1 взятый с Kodik в формате 1-6',
    'Последний вышедший сезон +1 взятый с Kodik 1-5,6',
    'Последний вышедший сезон +1 взятый с Kodik 1-4,5,6',
    'Последний вышедший сезон +1 взятый с Kodik 1,2,3,4,5,6',
    'Последний вышедший сезон в озвучке взятый с Kodik',
    'Последний вышедший сезон с субтитрами взятый с Kodik',
    'Последний вышедший сезон с автосубтитрами взятый с Kodik',
    'Последняя вышедшая серия взятая с Kodik',
    'Последняя вышедшая серия взятая с Kodik в формате 1-10',
    'Последняя вышедшая серия взятая с Kodik в формате 1-9,10',
    'Последняя вышедшая серия взятая с Kodik в формате 1-8,9,10',
    'Последняя вышедшая серия взятая с Kodik в формате 1,2,3,4,5,6,7,8,9,10',
    'Последняя вышедшая серия +1 взятая с Kodik в формате 1-11',
    'Последняя вышедшая серия +1 взятая с Kodik в формате 1-10,11',
    'Последняя вышедшая серия +1 взятая с Kodik в формате 1-9,10,11',
    'Последняя вышедшая серия +1 взятая с Kodik в формате 1,2,3,4,5,6,7,8,9,10,11',
    'Последняя вышедшая серия в озвучке взятая с Kodik',
    'Последняя вышедшая серия с субтитрами взятая с Kodik',
    'Последняя вышедшая серия с автосубтитрами взятая с Kodik',
    'Общее количество серий взятое с Kodik',
    'Количество уже вышедших эпизодов по данным Shikimori/MyDramaList',
    'Первая буква названия на русском для буквенного каталога',
    'Первая буква оригинального названия для буквенного каталога',
];

$sort_arr_film = array(
  'year&order=desc' => 'по году, по убыванию', 
  'year&order=asc' => 'по году, по возрастанию',
  'created_at&order=desc' => 'по дате добавления, по убыванию', 
  'created_at&order=asc' => 'по дате добавления, по возрастанию',
  'updated_at&order=desc' => 'по дате обновления, по убыванию', 
  'updated_at&order=asc' => 'по дате обновления, по возрастанию',
  'kinopoisk_rating&order=desc'  => 'по kinopoisk_rating, по убыванию',
  'kinopoisk_rating&order=asc'  => 'по kinopoisk_rating, по возрастанию',
  'imdb_rating&order=desc'  => 'по imdb_rating, по убыванию',
  'imdb_rating&order=asc'  => 'по imdb_rating, по возрастанию',
  'shikimori_rating&order=desc'  => 'по shikimori_rating, по убыванию',
  'shikimori_rating&order=asc'  => 'по shikimori_rating, по возрастанию'
);

$anons_sort_arr_film = array(
  'id' => 'По ID (по возрастанию)',
  'ranked' => 'По рейтингу (по возрастанию)',
  'popularity' => 'По популярности (по возрастанию)',
  'name' => 'По названию (по алфавиту)',
  'aired_on' => 'По дате добавления (по убыванию)',
  'random' => 'Случайно'
);

$anons_kind = array(
  'movie' => 'Movie', 
  'special' => 'Special',
  'ova' => 'OVA',
  'ona' => 'ONA',
  'tv' => 'TV'
);