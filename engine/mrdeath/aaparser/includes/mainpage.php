<?php
echo <<<HTML
	<div id="settings" class="panel panel-flat">
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Общие настройки модуля</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;

showRow('Ваш API-токен в балансере Kodik', 'Обязательное поле, необходимое для работы модуля. Взять api токен можно <a href="https://bd.kodik.biz/users/profile" target="_blank">по ссылке</a>', showInput(['settings[kodik_api_key]', 'text', $aaparser_config['settings']['kodik_api_key']]));
showRow('Домен Kodik для запросов к api', 'По умолчанию <b>https://kodikapi.com/</b>, замените в случае переезда/замены домена Kodik для запросов к api. Указываем с https в начале и со слешем в конце', showInput(['settings[kodik_api_domain]', 'text', $aaparser_config['settings']['kodik_api_domain']]));
showRow('Режим работы модуля', 'Выберите режим работы модуля соответствующий тематике вашего сайта - только аниме, только дорамы, аниме и дорамы', makeDropDown( $working_mode, "settings[working_mode]", $aaparser_config['settings']['working_mode'], 'ShowOrHideMode'));

echo <<<HTML
            </table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Основные доп. поля</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;

showRow('Дополнительное поле с ID Shikimori', 'Выберите дополнительное поле, в котором содержится id аниме с Shikimori. Выберите если добавляете на сайт аниме', makeDropDown( $xfields_list, "main_fields[xf_shikimori_id]", $aaparser_config['main_fields']['xf_shikimori_id']));
showRow('Дополнительное поле с ID MyDramaList', 'Выберите дополнительное поле, в котором содержится id дорам с MyDramaList. Выберите если добавляете на сайт дорамы', makeDropDown( $xfields_list, "main_fields[xf_mdl_id]", $aaparser_config['main_fields']['xf_mdl_id']));
showRow('Дополнительное поле c плеером', 'Выберите дополнительное поле в котором содержится ссылка на плеер с балансера', makeDropDown( $xfields_list, "main_fields[xf_player]", $aaparser_config['main_fields']['xf_player']));
showRow('Доп. поле с постером', 'Выберите дополнительное поле, в котором содержится постер (обложка)', makeDropDown( $xfields_all_list, "main_fields[xf_poster]", $aaparser_config['main_fields']['xf_poster']));
showRow('Ссылка на картинку-заглушку', 'Введите ссылку на картинку-заглушку в случае отсутствия постера', showInput(['main_fields[poster_empty]', 'text', $aaparser_config['main_fields']['poster_empty']]));
showRow('Дополнительное поле c последним вышедшим сезоном (обязательно)', 'Выберите дополнительное поле c последним вышедшим сезоном сериала (целое число)', makeDropDown( $xfields_list, "main_fields[xf_season]", $aaparser_config['main_fields']['xf_season']));
showRow('Дополнительное поле c последней вышедшей серией (обязательно)', 'Выберите дополнительное поле c последней вышедшей серией сериала (целое число)', makeDropDown( $xfields_list, "main_fields[xf_series]", $aaparser_config['main_fields']['xf_series']));
showRow('Дополнительное поле c качеством фильма', 'Выберите дополнительное поле c качеством фильма', makeDropDown( $xfields_list, "main_fields[xf_quality]", $aaparser_config['main_fields']['xf_quality']));
showRow('Дополнительное поле cо всеми доступными озвучками', 'Выберите дополнительное поле в котором содержится перечень озвучек фильма или сериала', makeDropDown( $xfields_list, "main_fields[xf_translation]", $aaparser_config['main_fields']['xf_translation']));
showRow('Дополнительное поле c последней добавленной озвучкой', 'Выберите дополнительное поле в котором содержиться озвучка последней добавленной в базу серии', makeDropDown( $xfields_list, "main_fields[xf_translation_last]", $aaparser_config['main_fields']['xf_translation_last']));

echo <<<HTML
            </table>
		</div>
		<div class="panel-body anime-settings" style="padding: 20px;font-size:20px; font-weight:bold;">Дополнительные настройки парсинга аниме</div>
		<div class="table-responsive anime-settings">
			<table class="table table-striped">
HTML;

showRow('Домен Shikimori для запросов к api', 'По умолчанию <b>https://shikimori.me/</b>, замените в случае переезда/замены домена Shikimori для запросов к api. Указываем с https в начале и со слешем в конце', showInput(['settings[shikimori_api_domain]', 'text', $aaparser_config['settings']['shikimori_api_domain']]));
showRow('Парсить ссылки на аниме в разных базах с Шикимори?', 'При отключении не будут использоваться теги {myanimelist_id}, {official_site}, {wikipedia}, {anime_news_network}, {anime_db}, {world_art}, {kinopoisk} и {kage_project}. Отключение ускоряет работу парсинга', makeCheckBox('settings[other_sites]', $aaparser_config['settings']['other_sites']));
showRow('Парсить авторский состав с Шикимори?', 'При отключении не будут использоваться теги {shikimori_director}, {shikimori_producer}, {shikimori_script} и {shikimori_composition}. Отключение ускоряет работу парсинга', makeCheckBox('settings[parse_authors]', $aaparser_config['settings']['parse_authors']));
showRow('Парсить франшизы с Шикимори?', 'При отключении не будет использоваться тег {shikimori_franshise}. Отключение ускоряет работу парсинга<br/><b class="faq_find faq_id_2">Подробнее</b>', makeCheckBox('settings[parse_franshise]', $aaparser_config['settings']['parse_franshise']));
showRow('Выберите сортировку франшизы с Шикимори', 'Работает только с включенным парсингом Франшизы (Настройка выше)', makeDropDown( $franchise_sort, "settings[franchise_sort]", $aaparser_config['settings']['franchise_sort'], 'ShowOrHideMode'));
showRow('Парсить похожие аниме с Шикимори?', 'При отключении не будет использоваться тег {shikimori_similar}. Отключение ускоряет работу парсинга', makeCheckBox('settings[parse_similar]', $aaparser_config['settings']['parse_similar']));
showRow('Парсить связанные аниме с Шикимори?', 'При отключении не будет использоваться тег {shikimori_related}. Отключение ускоряет работу парсинга', makeCheckBox('settings[parse_related]', $aaparser_config['settings']['parse_related']));
showRow('Парсить данные с World-Art?', 'При отключении не будут использоваться теги {worldart_country}, {worldart_plot}, {worldart_tags}, {worldart_rating} и {worldart_votes}, а так же будет отключено проставление категорий-тегов с World-Art. Отключение ускоряет работу парсинга', makeCheckBox('settings[parse_wa]', $aaparser_config['settings']['parse_wa']));
showRow('Парсить постер с неофициального api MyAnimeList?', 'Это экспериментальная опция. Включив будет осуществлён парсинг постера/обложки аниме с помощью api jikan.moe. Shikimori отдаёт в api обложку маленького размера (225x310), данная опция позволит получать обложку большого размера (425x600)', makeCheckBox('settings[parse_jikan]', $aaparser_config['settings']['parse_jikan']));
// showRow('Парсить дату выхода следующей серии аниме?', 'Необходимо обязательно выбрать дополнительное поле в настройке ниже', makeCheckBox('settings[next_episode]', $aaparser_config['settings']['next_episode']));
showRow('Выберите доп поле для следующей серии', 'Необходимо выбрать дополнительное поле в которое будет парсится дата следующей серии, если оно пустое, то парсится не будет', makeDropDown( $xfields_all_list, "settings[next_episode_date_new]", $aaparser_config['settings']['next_episode_date_new'], 'ShowOrHideMode'));
showRow('Включить таймер с отсчётом до выхода новой серии?', 'Включив модуль подгрузит необходимые стили и скрипты, в полной новости будет выведен таймер с отсчётом времени до выхода новой серии <b>Для работы необходимо выбрать доп поле для следующей серии</b>', makeCheckBox('settings[timer_enable]', $aaparser_config['settings']['timer_enable']));

echo <<<HTML
            </table>
            <div class="alert alert-info alert-styled-left alert-arrow-left alert-component">В файле шаблона полной новости fullstory.tpl вставьте код в то место, где будет выводиться таймер <br><br>
			<textarea style="width:100%;height:300px;" disabled="" id="needreplacer">
[xfgiven_NNNNNW]
<div class="pretimer">
	<div class="lt">
		<div class="countdown_title"><p>До выхода новой серии осталось:</p></div>
		<div class="countdown_text">Новая серия аниме выходит на экраны <span>[xfvalue_NNNNNW]</span><br> в соответствии c японским временем</div>
	</div>
	<div class="rt">
		<ul class="countdown_wrp">
			<li class="weeks"><div class="value">0</div><div class="unit">недель</div></li>
			<li class="days"><div class="value">0</div><div class="unit">дней</div></li>
			<li class="hours"><div class="value">0</div><div class="unit">часов</div></li>
			<li class="minutes"><div class="value">0</div><div class="unit">минут</div></li>
			<li class="seconds"><div class="value">0</div><div class="unit">секунды</div></li>
		</ul>
	</div>
	<script type="text/javascript"> var initialDateStr = "[xfvalue_NNNNNW]"; </script>
</div>
[/xfgiven_NNNNNW]</textarea></div>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Системная информация</div>
		<div class="table-responsive">
			<table class="table table-striped">
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>Всего в очереди:</b></h6><span class="note large">{$all['count']} материалов</span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>Обработано:</b></h6><span class="note large">{$done['count']} материалов</span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>Статус базы:</b></h6><span class="note large">{$base_status}</span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_queue(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-trash position-left"></i>Обновить очередь</button><span class="note large"></span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
HTML;
if ( $aaparser_config['update_news']['cat_check'] ) {
echo <<<HTML
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>В очереди на обновление категорий:</b></h6><span class="note large">{$cat_check_db['count']} материалов</span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>Статус:</b></h6><span class="note large">{$cat_check_status}</span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
HTML;
}
if ( $aaparser_config['update_news']['xf_check'] ) {
echo <<<HTML
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>В очереди на обновление доп. полей:</b></h6><span class="note large">{$xf_check_db['count']} материалов</span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
            <tr>
                <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>Статус:</b></h6><span class="note large">{$xf_check_status}</span></td>
                <td class="col-xs-2 col-md-5 settingstd "></td>
            </tr>
HTML;
}
showRow( 'Связывание с новостей с базой модуля', 'В случае установки модуля на уже работающий сайт с добавленными аниме/дорамами, или же после ручного добавления нажмите кнопку справа', '<button type="button" class="btn bg-slate-600 btn-raised legitRipple" id="connect-base"><i class="fa fa-wrench position-left"></i>Связать с базой</button>', "", "" );
	showRow( 'Новостей для связывания', 'Общее кол-во полученных новостей для связывания', '<span id="news-count">0</span>', "", "" );
	showRow( 'Связано новостей с базой', 'Кол-во новостей, которые были связаны с базой модуля', '<span id="current-update">0</span>', "", "" );
echo <<<HTML
			</table>
            <div class="update-status">
	            <div class="update-status__current" id="connect-current">0%</div>
	            <div class="progress progress-success">
		            <div class="bar" id="connect-bar" style="width: 0%;"></div>
	            </div>
	            <div class="update-status__msg" id="result-msg">Запустите связывание...</div>
            </div>
		</div>
	</div>
HTML;
?>