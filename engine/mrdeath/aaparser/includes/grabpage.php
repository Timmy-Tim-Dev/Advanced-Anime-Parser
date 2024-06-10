<?php
echo <<<HTML
	<div id="grabbing" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройки граббинга</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;

showRow('Включить граббинг?', 'Перед включением данной опции убедитесь что вы настроили доп.поля, категории и в целом сам модуль. Включив, модуль будет автоматически добавлять к вам на сайт аниме и дорамы при помощи крон', makeCheckBox('settings[grab_on]', $aaparser_config['settings']['grab_on']));
showRow('Автор новости', 'Введите логин автора новости, от имени которого будут добавляться материлы. По умолчанию Admin', showInput(['grabbing[author_name]', 'text', $aaparser_config['grabbing']['author_name']]));

showRow('Порядок добавления', 'Вы можете выбрать порядок добавления. по дате или по рейтингу. по возрастанию или убыванию', makeDropDown( $sort_arr_film, "settings[film_sort_by]", $aaparser_config['settings']['film_sort_by'], 'ShowOrHideMode'));

showRow('Id автора новости', 'Введите id автора новости. По умолчанию id = 1', showInput(['grabbing[author_id]', 'text', $aaparser_config['grabbing']['author_id']]));
showRow('Опубликовать новость на сайте', '', makeCheckBox('grabbing[publish]', $aaparser_config['grabbing']['publish']));
showRow('На модерацию без постера', '', makeCheckBox('grabbing[publish_image]', $aaparser_config['grabbing']['publish_image']));
showRow('На модерацию без описания(сюжета)', '', makeCheckBox('grabbing[publish_plot]', $aaparser_config['grabbing']['publish_plot']));
showRow('Публиковать на главной', '', makeCheckBox('grabbing[publish_main]', $aaparser_config['grabbing']['publish_main']));
showRow('Разрешить рейтинг статьи', '', makeCheckBox('grabbing[allow_rating]', $aaparser_config['grabbing']['allow_rating']));
showRow('Разрешить комментарии', '', makeCheckBox('grabbing[allow_comments]', $aaparser_config['grabbing']['allow_comments']));
showRow('Включить автоматический перенос строк в редакторе bbcode?', '', makeCheckBox('grabbing[allow_br]', $aaparser_config['grabbing']['allow_br']));
showRow('Опубликовать новость в RSS потоке', '', makeCheckBox('grabbing[allow_rss]', $aaparser_config['grabbing']['allow_rss']));
showRow('Использовать в Яндекс Турбо', '', makeCheckBox('grabbing[allow_turbo]', $aaparser_config['grabbing']['allow_turbo']));
showRow('Использовать в Яндекс Дзен', '', makeCheckBox('grabbing[allow_zen]', $aaparser_config['grabbing']['allow_zen']));
showRow('Запретить индексацию страницы для поисковиков', '', makeCheckBox('grabbing[dissalow_index]', $aaparser_config['grabbing']['dissalow_index']));
showRow('Исключить из поиска по сайту', '', makeCheckBox('grabbing[dissalow_search]', $aaparser_config['grabbing']['dissalow_search']));
showRow('Максимум актёров', 'Введите максимальное количество актёров. Для снятия ограничения укажите 0', showInput(['settings[max_actors]', 'number', $aaparser_config['settings']['max_actors']]));
showRow('Максимум режисёров', 'Введите максимальное количество режисёров. Для снятия ограничения укажите 0', showInput(['settings[max_directors]', 'number', $aaparser_config['settings']['max_directors']]));
showRow('Максимум продюссеров', 'Введите максимальное количество продюссеров. Для снятия ограничения укажите 0', showInput(['settings[max_producers]', 'number', $aaparser_config['settings']['max_producers']]));
showRow('Максимум сценаристов', 'Введите максимальное количество сценаристов. Для снятия ограничения укажите 0', showInput(['settings[max_writers]', 'number', $aaparser_config['settings']['max_writers']]));
showRow('Максимум композиторов', 'Введите максимальное количество композиторов. Для снятия ограничения укажите 0', showInput(['settings[max_composers]', 'number', $aaparser_config['settings']['max_composers']]));
showRow('Максимум монтажеров', 'Введите максимальное количество монтажеров. Для снятия ограничения укажите 0', showInput(['settings[max_editors]', 'number', $aaparser_config['settings']['max_editors']]));
showRow('Максимум художников', 'Введите максимальное количество художников. Для снятия ограничения укажите 0', showInput(['settings[max_designers]', 'number', $aaparser_config['settings']['max_designers']]));
showRow('Максимум операторов', 'Введите максимальное количество операторов. Для снятия ограничения укажите 0', showInput(['settings[max_operators]', 'number', $aaparser_config['settings']['max_operators']]));


echo <<<HTML
			</table>
		</div>
		<div class="panel-body anime-settings" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка зависимостей для аниме</div>
		<div class="table-responsive anime-settings">
			<table class="table table-striped">
HTML;
showRow('Добавлять ТВ-сериалы?', 'Если включено то к вам на сайт будут добавляться ТВ-сериалы', makeCheckBox('grabbing[tv]', $aaparser_config['grabbing']['tv']));
showRow('Добавлять фильмы?', 'Если включено то к вам на сайт будут добавляться фильмы', makeCheckBox('grabbing[movie]', $aaparser_config['grabbing']['movie']));
showRow('Добавлять OVA?', 'Если включено то к вам на сайт будут добавляться OVA', makeCheckBox('grabbing[ova]', $aaparser_config['grabbing']['ova']));
showRow('Добавлять ONA?', 'Если включено то к вам на сайт будут добавляться ONA', makeCheckBox('grabbing[ona]', $aaparser_config['grabbing']['ona']));
showRow('Добавлять спэшлы?', 'Если включено то к вам на сайт будут добавляться спэшлы', makeCheckBox('grabbing[special]', $aaparser_config['grabbing']['special']));
showRow('Добавлять AMV?', 'Если включено то к вам на сайт будут добавляться AMV', makeCheckBox('grabbing[music]', $aaparser_config['grabbing']['music']));
showRow('Добавлять релизы в camrip?', 'Если выключено то на сайт не будут добавляться аниме доступные только в качестве camprip. <b>*Условие будет пропущено, если данного аниме нет в базе Kodik!</b>', makeCheckBox('grabbing[if_camrip]', $aaparser_config['grabbing']['if_camrip']));
showRow('Добавлять аниме с LGBT сценами?', 'Если выключено то на сайт не будут добавляться аниме с LGBT сценами. <b>*Условие будет пропущено, если данного аниме нет в базе Kodik!</b>', makeCheckBox('grabbing[if_lgbt]', $aaparser_config['grabbing']['if_lgbt']));
showRow('Приоритет новинкам?', 'Если включено то сперва будут добавляться новинки текущего года', makeCheckBox('grabbing[this_year]', $aaparser_config['grabbing']['this_year']));
showRow('Добавлять только выбранные года', 'Вы можете выбрать года выхода, в случае если выберете то будут добавляться аниме только этих годов, иначе если оставить пустым то на ваш сайт будут добавляться аниме всех годов', makeSelect( $year_array, "grabbing[years]", $aaparser_config['grabbing']['years'], 'Выберите год/года выхода', 0));
showRow('Добавлять только выбранные жанры', 'Вы можете выбрать жанры, в случае если выберете то будут добавляться аниме только в этих жанрах, иначе если оставить пустым то на ваш сайт будут добавляться аниме всех жанров', makeSelect( $genres_array, "grabbing[genres]", $aaparser_config['grabbing']['genres'], 'Выберите жанр или несколько жанров', 1));
if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json') ) {
echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Обновить озвучки</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
} else {
    echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Получить озвучки</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
}
showRow('Добавлять только в выбранных озвучках *', 'Вы можете выбрать озвучки, в случае если выберете то будут добавляться аниме доступные в этих озвучках, иначе если оставить пустым то на ваш сайт будут добавляться аниме во всех озвучках. <b>*Условие будет пропущено, если данного аниме нет в базе Kodik!</b>', makeSelect( $translator_array, "grabbing[translators]", $aaparser_config['grabbing']['translators'], 'Выберите озвучку или несколько озвучек', 1));
showRow('Не добавлять выбранные года', 'Вы можете выбрать черный список годов выхода, в случае если выберете то не будут добавляться аниме этих годов', makeSelect( $year_array, "grabbing[not_years]", $aaparser_config['grabbing']['not_years'], 'Выберите год/года выхода', 0));
showRow('Не добавлять выбранные жанры', 'Вы можете выбрать черный список жанров, в случае если выберете то не будут добавляться аниме только в одном из выбранных жанров', makeSelect( $genres_array, "grabbing[not_genres]", $aaparser_config['grabbing']['not_genres'], 'Выберите жанр или несколько жанров', 1));
showRow('Не добавлять в выбранных озвучках *', 'Вы можете выбрать черный список озвучек, в случае если выберете то не будут добавляться аниме доступные только в одной из этих озвучкек. <b>*Условие будет пропущено, если данного аниме нет в базе Kodik!</b>', makeSelect( $translator_array, "grabbing[not_translators]", $aaparser_config['grabbing']['not_translators'], 'Выберите озвучку или несколько озвучек', 1));

echo <<<HTML
			</table>
		</div>
		<div class="panel-body dorama-settings" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка зависимостей для дорам</div>
		<div class="table-responsive dorama-settings">
			<table class="table table-striped">
HTML;
showRow('Добавлять ТВ-сериалы?', 'Если включено то к вам на сайт будут добавляться ТВ-сериалы', makeCheckBox('grabbing_doram[tv]', $aaparser_config['grabbing_doram']['tv']));
showRow('Добавлять фильмы?', 'Если включено то к вам на сайт будут добавляться фильмы', makeCheckBox('grabbing_doram[movie]', $aaparser_config['grabbing_doram']['movie']));

showRow('Добавлять дорамы произведённые в Южной Корее?', 'Если включено то к вам на сайт будут добавляться дорамы, которые были сняты в Южной Корее', makeCheckBox('grabbing_doram[skorea]', $aaparser_config['grabbing_doram']['skorea']));
showRow('Добавлять дорамы произведённые в Китае?', 'Если включено то к вам на сайт будут добавляться дорамы, которые были сняты в Китае', makeCheckBox('grabbing_doram[china]', $aaparser_config['grabbing_doram']['china']));
showRow('Добавлять дорамы произведённые в Японии?', 'Если включено то к вам на сайт будут добавляться дорамы, которые были сняты в Японии', makeCheckBox('grabbing_doram[japanese]', $aaparser_config['grabbing_doram']['japanese']));
showRow('Добавлять дорамы произведённые в Таиланде?', 'Если включено то к вам на сайт будут добавляться дорамы, которые были сняты в Таиланде', makeCheckBox('grabbing_doram[tailand]', $aaparser_config['grabbing_doram']['tailand']));
showRow('Добавлять дорамы произведённые в Тайване?', 'Если включено то к вам на сайт будут добавляться дорамы, которые были сняты в Тайване', makeCheckBox('grabbing_doram[taivan]', $aaparser_config['grabbing_doram']['taivan']));
showRow('Добавлять дорамы произведённые в Филиппинах?', 'Если включено то к вам на сайт будут добавляться дорамы, которые были сняты в Филиппинах', makeCheckBox('grabbing_doram[phillipines]', $aaparser_config['grabbing_doram']['phillipines']));

showRow('Добавлять релизы в camrip?', 'Если выключено то на сайт не будут добавляться дорамы доступные только в качестве camrip', makeCheckBox('grabbing_doram[if_camrip]', $aaparser_config['grabbing_doram']['if_camrip']));
showRow('Добавлять дорамы с LGBT сценами?', 'Если выключено то на сайт не будут добавляться дорамы с LGBT сценами', makeCheckBox('grabbing_doram[if_lgbt]', $aaparser_config['grabbing_doram']['if_lgbt']));
showRow('Приоритет новинкам?', 'Если включено то сперва будут добавляться новинки текущего года', makeCheckBox('grabbing_doram[this_year]', $aaparser_config['grabbing_doram']['this_year']));
showRow('Добавлять только выбранные года', 'Вы можете выбрать года выхода, в случае если выберете то будут добавляться дорамы только этих годов, иначе если оставить пустым то на ваш сайт будут добавляться дорамы всех годов', makeSelect( $year_array, "grabbing_doram[years]", $aaparser_config['grabbing_doram']['years'], 'Выберите год/года выхода', 0));
showRow('Добавлять только выбранные жанры', 'Вы можете выбрать жанры, в случае если выберете то будут добавляться дорамы только в этих жанрах, иначе если оставить пустым то на ваш сайт будут добавляться дорамы всех жанров', makeSelect( $genres_array, "grabbing_doram[genres]", $aaparser_config['grabbing_doram']['genres'], 'Выберите жанр или несколько жанров', 1));
//Перенос
showRow('Добавлять теги MyDramaList в нижнем регистре?', 'Если включено, то теги будут вставляться в нижнем регистре, т.е. с маленькой буквы', makeCheckBox('settings[tags_tolower]', $aaparser_config['settings']['tags_tolower']));
showRow('Осуществлять перевод тегов MyDramaList на русский язык?', 'Если включено, то теги будут переводиться с английского на русский язык. Внимание, это экспериментальная функция, качество перевода может быть плохое. Рекомендуется ручная проверка и коррекция вставляемых в доп. поле тегов после их перевода', makeCheckBox('settings[translate_tags]', $aaparser_config['settings']['translate_tags']));
showRow('Добавлять переведённые теги MyDramaList на русский в нижнем регистре?', 'Если включено, то теги переведённые на русский будут вставляться в нижнем регистре, т.е. с маленькой буквы', makeCheckBox('settings[translate_tags_tolower]', $aaparser_config['settings']['translate_tags_tolower']));

if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json') ) {
echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations_dorama(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Обновить озвучки дорам</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
} else {
    echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations_dorama(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Получить озвучки дорам</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
}
showRow('Добавлять только в выбранных озвучках *', 'Вы можете выбрать озвучки, в случае если выберете то будут добавляться дорамы доступные в этих озвучках, иначе если оставить пустым то на ваш сайт будут добавляться дорамы во всех озвучках', makeSelect( $translator_array_dorama, "grabbing_doram[translators]", $aaparser_config['grabbing_doram']['translators'], 'Выберите озвучку или несколько озвучек', 1));
showRow('Не добавлять выбранные года', 'Вы можете выбрать черный список годов выхода, в случае если выберете то не будут добавляться дорамы этих годов', makeSelect( $year_array, "grabbing_doram[not_years]", $aaparser_config['grabbing_doram']['not_years'], 'Выберите год/года выхода', 0));
showRow('Не добавлять выбранные жанры', 'Вы можете выбрать черный список жанров, в случае если выберете то не будут добавляться дорамы только в одном из выбранных жанров', makeSelect( $genres_array, "grabbing_doram[not_genres]", $aaparser_config['grabbing_doram']['not_genres'], 'Выберите жанр или несколько жанров', 1));
showRow('Не добавлять в выбранных озвучках *', 'Вы можете выбрать черный список озвучек, в случае если выберете то не будут добавляться дорамы доступные только в одной из этих озвучкек', makeSelect( $translator_array_dorama, "grabbing_doram[not_translators]", $aaparser_config['grabbing_doram']['not_translators'], 'Выберите озвучку или несколько озвучек', 1));

echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка черного списка</div>
		<div class="table-responsive">
			<table class="table table-striped">
			    <tbody>
			        <tr class="anime-settings">
			            <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>Список из id Shikimori:</b></h6><span class="note large">Жмём на + и вводим id Shikimori</span>
                        <td class="col-xs-2 col-md-5 settingstd" id="dynamic_field_shiki">
	                        <div class="field">
			                    <button type="button" name="add_shiki" id="add_shiki" class="btn btn-primary">+</button>
		                    </div>
HTML;
if ( isset( $aaparser_config['blacklist_shikimori'] ) ) {
    foreach ( $aaparser_config['blacklist_shikimori'] as $num => $black_id ) {
        $i = $num+1;
echo <<<HTML
        <div style="display: flex;height: 40px;margin-bottom: 5px;" id="row{$i}">
            <input type="text" value="{$black_id}" autocomplete="off" style="float: right;height: 40px;" name="blacklist_shikimori[]" placeholder="id Shikimori" class="form-control">
            <button type="button" name="remove_shiki" id="{$i}" class="btn btn-danger btn_remove_shiki">X</button>
        </div>
HTML;
    } 
}
echo <<<HTML
                        </td>
			        </tr>
			        <tr class="dorama-settings">
			            <td class="col-xs-10 col-sm-6 col-md-7 "><h6><b>Список из id MyDramaList:</b></h6><span class="note large">Жмём на + и вводим id MyDramaList</span>
                        <td class="col-xs-2 col-md-5 settingstd" id="dynamic_field_mdl">
	                        <div class="field">
			                    <button type="button" name="add_mdl" id="add_mdl" class="btn btn-primary">+</button>
		                    </div>
HTML;
if ( isset( $aaparser_config['blacklist_mdl'] ) ) {
    foreach ( $aaparser_config['blacklist_mdl'] as $num => $black_mdl ) {
        $i = $num+1;
echo <<<HTML
        <div style="display: flex;height: 40px;margin-bottom: 5px;" id="rowmdl{$i}">
            <input type="text" value="{$black_mdl}" autocomplete="off" style="float: right;height: 40px;" name="blacklist_mdl[]" placeholder="id MyDramaList" class="form-control">
            <button type="button" name="remove_mdl" id="{$i}" class="btn btn-danger btn_remove_mdl">X</button>
        </div>
HTML;
    } 
}
echo <<<HTML
                        </td>
			        </tr>
			    </tbody>
            </table>
		</div>
	</div>
HTML;
?>