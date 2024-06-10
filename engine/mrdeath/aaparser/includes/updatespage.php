<?php
echo <<<HTML
	<div id="updates" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройки поднятия новостей</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Включить поднятие новостей?', 'Перед включением данной опции убедитесь что вы настроили доп.поля, категории и в целом сам модуль. Включив, модуль будет автоматически проверять материалы на факт выхода новой серии / лучшего качества, будет перезаписывать заданные доп. поля и обновлять дату новости', makeCheckBox('settings[update_on]', $aaparser_config['settings']['update_on']));
showRow('Количество новостей, проверяемых за один раз', 'Введите количество новостей, которые модуль будет проверять за один раз. Рекомендуемое значение 50. Если у вас слабый сервер или шаред хостинг и при обновлении кроном вы видите ошибку mysql "MySQL server has gone away", тогда уменьшите количество новостей до 10-20', showInput(['updates[max_check]', 'number', $aaparser_config['updates']['max_check']]));
showRow('Поднимать сериалы при выходе новой серии или сезона?', 'Если включено и вышла новая серия или сезон на балансере, то новость будет апнута и доп поля перезаписаны', makeCheckBox('updates[new_series]', $aaparser_config['updates']['new_series']));
showRow('Дополнительное поле c последним вышедшим сезоном в озвучке (не обязательно)', 'Выберите дополнительное поле c последним вышедшим сезоном сериала в озвучке (целое число)', makeDropDown( $xfields_list, "updates[xf_season_translated]", $aaparser_config['updates']['xf_season_translated']));
showRow('Дополнительное поле c последним вышедшим сезоном с субтитрами (не обязательно)', 'Выберите дополнительное поле c последним вышедшим сезоном сериала с субтитрами (целое число)', makeDropDown( $xfields_list, "updates[xf_season_subtitles]", $aaparser_config['updates']['xf_season_subtitles']));
showRow('Дополнительное поле c последним вышедшим сезоном с автосубтитрами (не обязательно)', 'Выберите дополнительное поле c последним вышедшим сезоном сериала с автосубтитрами (целое число)', makeDropDown( $xfields_list, "updates[xf_season_autosubtitles]", $aaparser_config['updates']['xf_season_autosubtitles']));
showRow('Дополнительное поле c последней вышедшей серией в озвучке (не обязательно)', 'Выберите дополнительное поле c последней вышедшей серией сериала в озвучке (целое число)', makeDropDown( $xfields_list, "updates[xf_series_translated]", $aaparser_config['updates']['xf_series_translated']));
showRow('Дополнительное поле c последней вышедшей серией с субтитрами (не обязательно)', 'Выберите дополнительное поле c последней вышедшей серией сериала с субтитрами (целое число)', makeDropDown( $xfields_list, "updates[xf_series_subtitles]", $aaparser_config['updates']['xf_series_subtitles']));
showRow('Дополнительное поле c последней вышедшей серией с автосубтитрами (не обязательно)', 'Выберите дополнительное поле c последней вышедшей серией сериала с автосубтитрами (целое число)', makeDropDown( $xfields_list, "updates[xf_series_autosubtitles]", $aaparser_config['updates']['xf_series_autosubtitles']));
showRow('Дополнительное поле c последней вышедшей серией в формате 1-10 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала в формате 1-10 (заполняется тегом {kodik_last_episode_1})', makeDropDown( $xfields_list, "updates[xf_series_1]", $aaparser_config['updates']['xf_series_1']));
showRow('Дополнительное поле c последней вышедшей серией в формате 1-9,10 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала в формате 1-9,10 (заполняется тегом {kodik_last_episode_2})', makeDropDown( $xfields_list, "updates[xf_series_2]", $aaparser_config['updates']['xf_series_2']));
showRow('Дополнительное поле c последней вышедшей серией в формате 1-8,9,10 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала в формате 1-8,9,10 (заполняется тегом {kodik_last_episode_3})', makeDropDown( $xfields_list, "updates[xf_series_3]", $aaparser_config['updates']['xf_series_3']));
showRow('Дополнительное поле c последней вышедшей серией в формате 1,2,3,4,5,6,7,8,9,10 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала в формате 1,2,3,4,5,6,7,8,9,10 (заполняется тегом {kodik_last_episode_4})', makeDropDown( $xfields_list, "updates[xf_series_4]", $aaparser_config['updates']['xf_series_4']));
showRow('Дополнительное поле c последней вышедшей серией +1 в формате 1-11 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала +1 в формате 1-11 (заполняется тегом {kodik_last_episode_5})', makeDropDown( $xfields_list, "updates[xf_series_5]", $aaparser_config['updates']['xf_series_5']));
showRow('Дополнительное поле c последней вышедшей серией +1 в формате 1-10,11 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала +1 в формате 1-10,11 (заполняется тегом {kodik_last_episode_6})', makeDropDown( $xfields_list, "updates[xf_series_6]", $aaparser_config['updates']['xf_series_6']));
showRow('Дополнительное поле c последней вышедшей серией +1 в формате 1-9,10,11 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала +1 в формате 1-9,10,11 (заполняется тегом {kodik_last_episode_7})', makeDropDown( $xfields_list, "updates[xf_series_7]", $aaparser_config['updates']['xf_series_7']));
showRow('Дополнительное поле c последней вышедшей серией +1 в формате 1,2,3,4,5,6,7,8,9,10,11 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последней вышедшей серией сериала +1 в формате 1,2,3,4,5,6,7,8,9,10,11 (заполняется тегом {kodik_last_episode_8})', makeDropDown( $xfields_list, "updates[xf_series_8]", $aaparser_config['updates']['xf_series_8']));
showRow('Дополнительное поле c последним вышедшим сезоном в формате 1-5 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном в формате 1-5 (заполняется тегом {kodik_last_season_1}))', makeDropDown( $xfields_list, "updates[xf_season_1]", $aaparser_config['updates']['xf_season_1']));
showRow('Дополнительное поле c последним вышедшим сезоном в формате 1-4,5 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном в формате 1-4,5 (заполняется тегом {kodik_last_season_2}))', makeDropDown( $xfields_list, "updates[xf_season_2]", $aaparser_config['updates']['xf_season_2']));
showRow('Дополнительное поле c последним вышедшим сезоном в формате 1-3,4,5 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном в формате 1-3,4,5 (заполняется тегом {kodik_last_season_3}))', makeDropDown( $xfields_list, "updates[xf_season_3]", $aaparser_config['updates']['xf_season_3']));
showRow('Дополнительное поле c последним вышедшим сезоном в формате 1,2,3,4,5 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном в формате 1,2,3,4,5 (заполняется тегом {kodik_last_season_4}))', makeDropDown( $xfields_list, "updates[xf_season_4]", $aaparser_config['updates']['xf_season_4']));
showRow('Дополнительное поле c последним вышедшим сезоном +1 в формате 1-6 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном +1 в формате 1-6 (заполняется тегом {kodik_last_season_5}))', makeDropDown( $xfields_list, "updates[xf_season_5]", $aaparser_config['updates']['xf_season_5']));
showRow('Дополнительное поле c последним вышедшим сезоном +1 в формате 1-5,6 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном +1 в формате 1-5,6 (заполняется тегом {kodik_last_season_6}))', makeDropDown( $xfields_list, "updates[xf_season_6]", $aaparser_config['updates']['xf_season_6']));
showRow('Дополнительное поле c последним вышедшим сезоном +1 в формате 1-4,5,6 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном +1 в формате 1-4,5,6 (заполняется тегом {kodik_last_season_7}))', makeDropDown( $xfields_list, "updates[xf_season_7]", $aaparser_config['updates']['xf_season_7']));
showRow('Дополнительное поле c последним вышедшим сезоном +1 в формате 1,2,3,4,5,6 (не обязательно)', 'В случае если такое доп. поле у вас есть и используется, то выберите дополнительное поле c последним вышедшим сезоном +1 в формате 1,2,3,4,5,6 (заполняется тегом {kodik_last_season_8}))', makeDropDown( $xfields_list, "updates[xf_season_8]", $aaparser_config['updates']['xf_season_8']));
showRow('Поднимать сериалы при изменении их статуса?', 'Если включено, у балансера есть информация по статусам сериалов и статус сериала изменился, то новость будет апнута и доп поле перезаписано. Статусы должны быть таких видов - Анонс, Онгоинг и Завершён. Если у вас в ранее добавленных аниме статусы вписаны иначе, то вам нужно воспользоваться разделом DLE "Поиск и замена", например заменить |ожидается|| на |Анонс||, |вышел|| на |Завершён||', makeCheckBox('updates[new_status]', $aaparser_config['updates']['new_status']));

showRow('Дополнительное поле cо статусом сериала на английском', 'Выберите дополнительное поле cо статусом сериалов на английском <br/><span style="color:red">Данные с API</span>', makeDropDown( $xfields_list, "updates[xf_status]", $aaparser_config['updates']['xf_status']));
showRow('Дополнительное поле cо статусом сериала на русском', 'Выберите дополнительное поле cо статусом сериалов на русском <br/><span style="color:red">Данные с API</span>', makeDropDown( $xfields_list, "updates[xf_status_ru]", $aaparser_config['updates']['xf_status_ru']));
showRow('Дополнительное поле cо статусом сериала на английском для озвучки', 'Выберите дополнительное поле cо статусом сериалов на английском для озвучки <br/><span style="color:red">Сравнение последний озвученной серии с количеством общих серии</span>', makeDropDown( $xfields_list, "updates[xf_status_voice]", $aaparser_config['updates']['xf_status_voice']));
showRow('Дополнительное поле cо статусом сериала на русском для озвучки', 'Выберите дополнительное поле cо статусом сериалов на русском для озвучки <br/><span style="color:red">Сравнение последний озвученной серии с количеством общих серии</span>', makeDropDown( $xfields_list, "updates[xf_status_ru_voice]", $aaparser_config['updates']['xf_status_ru_voice']));
showRow('Дополнительное поле cо статусом сериала на английском для субтитров', 'Выберите дополнительное поле cо статусом сериалов на английском для субтитров <br/><span style="color:red">Сравнение последний серии субтитрами с количеством общих серии</span>', makeDropDown( $xfields_list, "updates[xf_status_sub]", $aaparser_config['updates']['xf_status_sub']));
showRow('Дополнительное поле cо статусом сериала на русском для субтитров', 'Выберите дополнительное поле cо статусом сериалов на русском для субтитров <br/><span style="color:red">Сравнение последний серии субтитрами с количеством общих серии</span>', makeDropDown( $xfields_list, "updates[xf_status_ru_sub]", $aaparser_config['updates']['xf_status_ru_sub']));
showRow('Дополнительное поле cо статусом сериала на английском для автосубтитров', 'Выберите дополнительное поле cо статусом сериалов на английском для автосубтитров <br/><span style="color:red">Сравнение последний серии автосубтитрами с количеством общих серии</span>', makeDropDown( $xfields_list, "updates[xf_status_autosub]", $aaparser_config['updates']['xf_status_autosub']));
showRow('Дополнительное поле cо статусом сериала на русском для автосубтитров', 'Выберите дополнительное поле cо статусом сериалов на русском для автосубтитров <br/><span style="color:red">Сравнение последний серии автосубтитрами с количеством общих серии</span>', makeDropDown( $xfields_list, "updates[xf_status_ru_autosub]", $aaparser_config['updates']['xf_status_ru_autosub']));

showRow('Поднимать фильмы при выходе лучшего качества?', 'Если включено и появилось другое качество фильма на балансере, то новость будет апнута и доп поле качества перезаписаны', makeCheckBox('updates[new_quality]', $aaparser_config['updates']['new_quality']));
showRow('Поднимать в случае появления новой озвучки?', 'Включите только если не используете добавление определённых озвучек по списку. При появлении новой озвучки будет апнута новость и перезаписано поле озвучек', makeCheckBox('updates[new_translation]', $aaparser_config['updates']['new_translation']));
showRow('Поднимать в случае если в последней вышедшей серии добавлена новая озвучка?', 'Работает только если основной источник граббинга выбран Kodik. В случае если в последней вышедшей серии добавлена новая озвучка то новость с аниме будет апнута', makeCheckBox('updates[new_translation_last]', $aaparser_config['updates']['new_translation_last']));
showRow('Дополнительное поле c историей добавленных озвучек последней доступной серии', 'Данное доп. поле нужно в служебных целях, в него записывается история добавления озвучек в последней актуальной серии аниме. Создайте его и выберите, это обязательно для нормальной работы обновления последней добавленной озвучки', makeDropDown( $xfields_list, "updates[xf_translation_last_names]", $aaparser_config['updates']['xf_translation_last_names']));
showRow('Дополнительное поле c рейтингом Shikimori', 'Выберите дополнительное поле c рейтингом Shikimori', makeDropDown( $xfields_list, "updates[xf_rating_sh]", $aaparser_config['updates']['xf_rating_sh']));
showRow('Дополнительное поле c количеством голосов Shikimori', 'Выберите дополнительное поле c количеством голосов Shikimori', makeDropDown( $xfields_list, "updates[xf_golos_sh]", $aaparser_config['updates']['xf_golos_sh']));
showRow('Дополнительное поле c рейтингом IMDB', 'Выберите дополнительное поле c рейтингом IMDB', makeDropDown( $xfields_list, "updates[xf_rating_imdb]", $aaparser_config['updates']['xf_rating_imdb']));
showRow('Дополнительное поле c количеством голосов IMDB', 'Выберите дополнительное поле c количеством голосов IMDB', makeDropDown( $xfields_list, "updates[xf_golos_imdb]", $aaparser_config['updates']['xf_golos_imdb']));
showRow('Дополнительное поле c рейтингом Kinopoisk', 'Выберите дополнительное поле c рейтингом Kinopoisk', makeDropDown( $xfields_list, "updates[xf_rating_kp]", $aaparser_config['updates']['xf_rating_kp']));
showRow('Дополнительное поле c количеством голосов Kinopoisk', 'Выберите дополнительное поле c количеством голосов Kinopoisk', makeDropDown( $xfields_list, "updates[xf_golos_kp]", $aaparser_config['updates']['xf_golos_kp']));
showRow('Дополнительное поле c рейтингом MyDramaList (Дорама)', 'Выберите дополнительное поле c рейтингом MyDramaList (Дорама)', makeDropDown( $xfields_list, "updates[xf_rating_md]", $aaparser_config['updates']['xf_rating_md']));
showRow('Дополнительное поле c количеством голосов MyDramaList (Дорама)', 'Выберите дополнительное поле c количеством голосов MyDramaList (Дорама)', makeDropDown( $xfields_list, "updates[xf_golos_md]", $aaparser_config['updates']['xf_golos_md']));

echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Заполнение полей при обновлении</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;

$avaiable_tags = '<br>
<b>{title}</b> - название аниме<br>
<b>{title_ru}</b> - название аниме на русском<br>
<b>{episode}</b> - последняя серия<br>
<b>{episode_1}</b> - последняя серия в формате 1-10<br>
<b>{episode_2}</b> - последняя серия в формате 1-9,10<br>
<b>{episode_3}</b> - последняя серия в формате 1-8,9,10<br>
<b>{episode_4}</b> - последняя серия в формате 1,2,3,4,5,6,7,8,9,10<br>
<b>{episode_5}</b> - последняя серия +1 в формате 1-11<br>
<b>{episode_6}</b> - последняя серия +1 в формате 1-10,11<br>
<b>{episode_7}</b> - последняя серия +1 в формате 1-9,10,11<br>
<b>{episode_8}</b> - последняя серия +1 в формате 1,2,3,4,5,6,7,8,9,10,11<br>
<b>{season}</b> - последний сезон<br>
<b>{season_1}</b> - последний сезон в формате 1-5<br>
<b>{season_2}</b> - последний сезон в формате 1-4,5<br>
<b>{season_3}</b> - последний сезон в формате 1-3,4,5<br>
<b>{season_4}</b> - последний сезон в формате 1,2,3,4,5<br>
<b>{season_5}</b> - последний сезон +1 в формате 1-6<br>
<b>{season_6}</b> - последний сезон +1 в формате 1-5,6<br>
<b>{season_7}</b> - последний сезон +1 в формате 1-4,5,6<br>
<b>{season_8}</b> - последний сезон +1 в формате 1,2,3,4,5,6<br>
<b>{status}</b> - статус аниме на английском<br>
<b>{status_ru}</b> - статус аниме на русском<br>
<b>{quality}</b> - качество<br>
<b>{translation}</b> - список переводов<br>
<b>{translation_type}</b> - тип перевода на английском<br>
<b>{translation_type_ru}</b> - тип перевода на русском<br>
Для каждого тега доступны конструкции [if_x]...[/if_x], а так же [ifnot_x]...[/ifnot_x], где x - тег.
';

showRow('Менять заголовок новости в случае обновления?', 'Если включено то к вам на сайт будут добавляться сериалы', makeCheckBox('updates[change_title]', $aaparser_config['updates']['change_title']));
showRow('Шаблон заголовка новости', 'Доступные теги: '.$avaiable_tags, showInput(['updates[title]', 'text', $aaparser_config['updates']['title']]));
showRow('Менять чпу новости в случае обновления?', 'Если включено то к вам на сайт будут добавляться сериалы', makeCheckBox('updates[change_cpu]', $aaparser_config['updates']['change_cpu']));
showRow('Шаблон чпу новости', 'Доступные теги: '.$avaiable_tags, showInput(['updates[cpu]', 'text', $aaparser_config['updates']['cpu']]));
showRow('Менять метатег title в случае обновления?', 'Если включено то к вам на сайт будут добавляться сериалы', makeCheckBox('updates[change_metatitle]', $aaparser_config['updates']['change_metatitle']));
showRow('Шаблон метатега title новости', 'Доступные теги: '.$avaiable_tags, showInput(['updates[metatitle]', 'text', $aaparser_config['updates']['metatitle']]));
showRow('Менять метатег description в случае обновления?', 'Если включено то к вам на сайт будут добавляться сериалы', makeCheckBox('updates[change_metadescr]', $aaparser_config['updates']['change_metadescr']));
showRow('Шаблон метатега description новости', 'Доступные теги: '.$avaiable_tags, showInput(['updates[metadescr]', 'text', $aaparser_config['updates']['metadescr']]));
showRow('Менять метатег keywords новости в случае обновления?', 'Если включено то к вам на сайт будут добавляться сериалы', makeCheckBox('updates[change_metakeywords]', $aaparser_config['updates']['change_metakeywords']));
showRow('Шаблон метатега keywords новости', 'Доступные теги: '.$avaiable_tags, showInput(['updates[metakeywords]', 'text', $aaparser_config['updates']['metakeywords']]));
echo <<<HTML
			</table>
		</div>
	</div>
HTML;
?>