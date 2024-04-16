<?php
echo <<<HTML
	<div id="integration" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка интеграции со сторонними модулями</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Конвертация доп. полей в латиницу', 'Включите в случае, если вы используете <a href="https://lazydev.pro/fcode/22-latin-xfield-plugin.html" target="_blank">Плагин для конвертации ссылок в дополнительных полях в латиницу</a>', makeCheckBox('integration[latin_xfields]', $aaparser_config['integration']['latin_xfields']));
showRow('Конвертация тегов в латиницу', 'Включите в случае, если вы используете <a href="https://lazydev.pro/fcode/23-latin-tags-plugin.html" target="_blank">Плагин для конвертации кириллицы в латиницу в тегах</a>', makeCheckBox('integration[latin_tags]', $aaparser_config['integration']['latin_tags']));
showRow('Поддержка модуля Social Posting', 'Включите в случае, если вы используете <a href="https://0-web.ru/dle/mod-dle/467-dle-socialposting-v31.html" target="_blank">модуль SocialPosting</a>. Будет срабатывать в режиме обновления аниме по крону', makeCheckBox('integration[social_posting]', $aaparser_config['integration']['social_posting']));
showRow('Поддержка модуля Telegram Posting', 'Включите в случае, если вы используете <a href="https://devcraft.club/downloads/telegram-posting.11/" target="_blank">модуль Telegram Posting</a>. Будет срабатывать в режиме обновления аниме по крону', makeCheckBox('integration[telegram_posting]', $aaparser_config['integration']['telegram_posting']));
showRow('Поддержка модуля DLE Google Indexing', 'Включите в случае, если вы используете <a href="https://xoo.pw/6-dle-google-indexing.html" target="_blank">модуль DLE Google Indexing</a>. При добавлении или обновлении аниме будет отправлена команда поисковику Google на переобход страницы', makeCheckBox('integration[google_indexing]', $aaparser_config['integration']['google_indexing']));
echo <<<HTML
			</table>
		</div>
HTML;
if ( $aaparser_config['settings']['working_mode'] == 0 ) {
echo <<<HTML
    <div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка расписания выхода серий аниме</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Включить функционал расписания выхода серий аниме?', 'Включив, модуль будет собирать данные про расписание выхода онгоингов аниме с Shikimori и выводить его на отдельной странице <a href="'. $config["http_home_url"] .'schedule/" target="_blank">'. $config["http_home_url"] .'schedule/</a>. Инструкция по настройке находится ниже', makeCheckBox('calendar_settings[enable_schedule]', $aaparser_config_push['calendar_settings']['enable_schedule']));
showRow('Доп. поле с постером', 'Выберите дополнительное поле, в котором содержится постер', makeDropDown( $xfields_all_list, "settings[poster]", $aaparser_config['settings']['poster']));
echo <<<HTML
            </table>
            <div class="alert alert-info alert-styled-left alert-arrow-left alert-component">Инструкция по настройке расписания:<br>1. <b>Если ваш сервер работает на apache</b>, то добавляем следующее правило ниже строчки <b>RewriteEngine On</b><br><textarea style="width:100%;height:50px;" disabled>RewriteRule ^schedule(/?)+$ index.php?do=schedule [L]</textarea><br><b>Если ваш сервер работает на nginx</b>, то добавляем следующее правило<br><textarea style="width:100%;height:50px;" disabled>rewrite "^/schedule(/?)+$" /index.php?do=schedule break;</textarea><br>
            2. Создайте в корне папки с шаблоном файл <b>schedule.tpl</b> с таким содержимым:<br><textarea style="width:100%;height:300px;" disabled><div>
	<h1 class="main-title">Расписание выхода аниме</h1>
	<div class="top-description">
   		Интересует когда выйдет новая серия любимого сериала? На этой страничке предоставлено расписание выхода всех сериалов на ближайшее время. Для использования календаря, просто кликнете по числу и найдите интересующий вас сериал.
	</div>
	<div class="calendar-date">
   		<div class="calendar-date__list">
      		{calendar-date}
   		</div>
	</div>
	<div class="calendar calendar-full">
   		{anime-list}
	</div>
</div></textarea><br>
            3. Запустите один раз в строке браузера ссылку ниже, это необходимо для первоначального парсинга расписания, далее модуль будет сам обновлять его при помощи крон<br><textarea style="width:100%;height:50px;" disabled>{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=other&key={$cron_key}</textarea>
		    </div>
            <table class="table table-striped">
HTML;
showRow('Включить вывод расписания на главной странице сайта?', 'Включив, модуль будет выводить расписание выхода онгоингов за сегодня и вчера на главной странице вашего сайта', makeCheckBox('calendar_settings[schedule_main]', $aaparser_config_push['calendar_settings']['schedule_main']));
echo <<<HTML
			</table>
			<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">Инструкция по настройке вывода расписания на главной:<br>В файле main.tpl в нужное место вставьте код вывода расписания<br>
		<textarea style="width:100%;height:300px;" disabled>
		<div class="calendar box-body-s">
   			<div class="calendar__item">
      			<div class="calendar__item-weekday">
         			<h3>Сегодня</h3>
      			</div>
      			{today-ongoings}
   			</div>
   			<div class="calendar__item">
      			<div class="calendar__item-weekday">
         			<h3>Завтра</h3>
      			</div>
      			{tomorrow-ongoings}
   			</div>
		</div>
		</textarea>
		    </div>
		</div>
	<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка вывода персонажев и авторов аниме</div>
		<div class="table-responsive">
		<table class="table table-striped">
HTML;
showRow('Включить вывод персонажей и авторов (только для Аниме)?', 'Включив, модуль будет выводить состав персонажей, главные герои, второстепенные герои, авторы аниме и другие участники аниме', makeCheckBox('integration[personas_on]', $aaparser_config['integration']['personas_on']));
showRow('Кэшировать данные персонажей и авторов?', 'Включив, модуль будет кэшировать полученные данные, заметно ускоряет обработку страницы<br/><b>Настоятельно рекомендиуем использовать кэширование, значительно ускоряет</b>', makeCheckBox('integration[personas_cache]', $aaparser_config['integration']['personas_cache']));
showRow('Постер при отсутствий изображения', 'Укажите путь до картинки заглушки для отсутствующих постеров. <br/><b>Для корректной работы, укажите прямую ссылку до картинки</b><br/>Пример: <i>/templates/Default/dleimages/no_image.jpg</i>', showInput(['integration[default_image]', 'text', $aaparser_config['integration']['default_image']]));
echo <<<HTML
		</table>
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">
		Создаем файл в Вашем шаблоне под названием <b>persons_info.tpl</b>
		<br/>Выводим тегом <b><div id="personas_block" data-sh_id="[xfvalue_shikimori_id]">Загрузка...</div></b>в любом месте полной страницы <i>(fullstory.tpl), [xfvalue_shikimori_id] - укажите свое доп поле которое содержит Shikimori ID</i>
		<br/><b><div id="swilly_refresh">Перезагрузить</div></b> Этой кнопкой Вы можете перезагрузить блок если оно не загрузилось <i>Желательно скрыть от пользователей</i>.
		<br/>Теги которые работают в этом шаблоне:
		<br/><i>Все теги имеют инвертированную вариацию, пример <b>[not_main_characters_name_eng][/not_main_characters_name_eng]</b></i>
		<hr/>
		<b>[main_characters]</b>Выводит содержимое если есть хотя-бы один главный персонаж<b>[/main_characters]</b>
		<br/><b>[main_characters_item]</b>Выводит персонажей дублируя код внутри этого тега<b>[/main_characters_item]</b>
		<br/><b>[main_characters_name_eng]</b>Выводит имя главного персонажа в оригинале<b>[/main_characters_name_eng]</b>
		<br/><b>[main_characters_name_rus]</b>Выводит имя главного персонажа на Русском<b>[/main_characters_name_rus]</b>
		<br/><b>[main_characters_role]</b>Выводит роль главного персонажа<b>[/main_characters_role]</b>
		<br/><b>[main_characters_id]</b>Выводит id главного персонажа который указан в Shikimori<b>[/main_characters_id]</b>
		<br/><b>[main_characters_url]</b>Выводит url главного персонажа который указан в Shikimori<b>[/main_characters_url]</b>
		<br/><b>[main_characters_image_orig]</b>Выводит постер главного персонажа в оригинальном формате<b>[/main_characters_image_orig]</b>
		<br/><b>[main_characters_image_prev]</b>Выводит постер главного персонажа в превью формате<b>[/main_characters_image_prev]</b>
		<br/><b>[main_characters_image_x96]</b>Выводит постер главного персонажа в x96 формате<b>[/main_characters_image_x96]</b>
		<br/><b>[main_characters_image_x48]</b>Выводит постер главного персонажа в x48 формате<b>[/main_characters_image_x48]</b>
		<hr/>
		<b>[sub_characters]</b>Выводит содержимое если есть хотя-бы один второстепенный персонаж<b>[/sub_characters]</b>
		<br/><b>[sub_characters_item]</b>Выводит персонажей дублируя код внутри этого тега<b>[/sub_characters_item]</b>
		<br/><b>[sub_characters_name_eng]</b>Выводит имя второстепенного персонажа в оригинале<b>[/sub_characters_name_eng]</b>
		<br/><b>[sub_characters_name_rus]</b>Выводит имя второстепенного персонажа на Русском<b>[/sub_characters_name_rus]</b>
		<br/><b>[sub_characters_role]</b>Выводит роль второстепенного персонажа<b>[/sub_characters_role]</b>
		<br/><b>[sub_characters_id]</b>Выводит id второстепенного персонажа который указан в Shikimori<b>[/sub_characters_id]</b>
		<br/><b>[sub_characters_url]</b>Выводит url второстепенного персонажа который указан в Shikimori<b>[/sub_characters_url]</b>
		<br/><b>[sub_characters_image_orig]</b>Выводит постер второстепенного персонажа в оригинальном формате<b>[/sub_characters_image_orig]</b>
		<br/><b>[sub_characters_image_prev]</b>Выводит постер второстепенного персонажа в превью формате<b>[/sub_characters_image_prev]</b>
		<br/><b>[sub_characters_image_x96]</b>Выводит постер второстепенного персонажа в x96 формате<b>[/sub_characters_image_x96]</b>
		<br/><b>[sub_characters_image_x48]</b>Выводит постер второстепенного персонажа в x48 формате<b>[/sub_characters_image_x48]</b>
		<hr/>
		<b>[all_personas]</b>Выводит содержимое если есть хотя-бы один деятель<b>[/all_personas]</b>
		<br/><b>[all_personas_item]</b>Выводит деятелей дублируя код внутри этого тега<b>[/all_personas_item]</b>
		<br/><b>[all_personas_name_eng]</b>Выводит имя деятеля в оригинале<b>[/all_personas_name_eng]</b>
		<br/><b>[all_personas_name_rus]</b>Выводит имя деятеля на Русском<b>[/all_personas_name_rus]</b>
		<br/><b>[all_personas_role]</b>Выводит роль деятеля<b>[/all_personas_role]</b>
		<br/><b>[all_personas_id]</b>Выводит id деятеля который указан в Shikimori<b>[/all_personas_id]</b>
		<br/><b>[all_personas_url]</b>Выводит url деятеля который указан в Shikimori<b>[/all_personas_url]</b>
		<br/><b>[all_personas_image_orig]</b>Выводит постер деятеля в оригинальном формате<b>[/all_personas_image_orig]</b>
		<br/><b>[all_personas_image_prev]</b>Выводит постер деятеля в превью формате<b>[/all_personas_image_prev]</b>
		<br/><b>[all_personas_image_x96]</b>Выводит постер деятеля в x96 формате<b>[/all_personas_image_x96]</b>
		<br/><b>[all_personas_image_x48]</b>Выводит постер деятеля в x48 формате<b>[/all_personas_image_x48]</b>
		<hr/>
		<i>Примерный файл для persons_info.tpl</i>
		<textarea style="width:100%;height:300px;" disabled>
<div class="swilly_box">
	[main_characters]
	<div class="swilly_bl">
		[main_characters_item]
		<div class="swilly_item">
			<img src="{main_characters_image_orig}" class="swilly_poster" />
			
			[main_characters_name_eng]
				<a href="{main_characters_url}" class="swilly_title">{main_characters_name_eng}</a>
			[/main_characters_name_eng]
			[not_main_characters_name_eng]
				<a href="{main_characters_url}" class="swilly_title">{main_characters_name_rus}</a>
			[/not_main_characters_name_eng]
			[main_characters_role]
				<p class="swilly_role">{main_characters_role}</p>
			[/main_characters_role]
		</div>
		[/main_characters_item]
	</div>
	[/main_characters]
	[sub_characters]
	<div class="swilly_bl">
		[sub_characters_item]
		<div class="swilly_item">
			<img src="{sub_characters_image_orig}" class="swilly_poster" />
			
			[sub_characters_name_eng]
				<a href="{sub_characters_url}" class="swilly_title">{sub_characters_name_eng}</a>
			[/sub_characters_name_eng]
			[not_sub_characters_name_eng]
				<a href="{sub_characters_url}" class="swilly_title">{sub_characters_name_rus}</a>
			[/not_sub_characters_name_eng]
			[sub_characters_role]
				<p class="swilly_role">{sub_characters_role}</p>
			[/sub_characters_role]
		</div>
		[/sub_characters_item]
	</div>
	[/sub_characters]
	
	[all_personas]
	<div class="swilly_bl">
		[all_personas_item]
		<div class="swilly_item">
			<img src="{all_personas_image_orig}" class="swilly_poster" />
			
			[all_personas_name_eng]
				<a href="{all_personas_url}" class="swilly_title">{all_personas_name_eng}</a>
			[/all_personas_name_eng]
			[not_all_personas_name_eng]
				<a href="{all_personas_url}" class="swilly_title">{all_personas_name_rus}</a>
			[/not_all_personas_name_eng]
			[all_personas_role]
				<p class="swilly_role">{all_personas_role}</p>
			[/all_personas_role]
		</div>
		[/all_personas_item]
	</div>
	[/all_personas]
</div></textarea>
		<br/><i>Примерные стили для persons_info.tpl</i>
		<textarea style="width:100%;height:300px;" disabled>
<style>
.swilly_box {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.swilly_bl {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
}

.swilly_item {
    display: flex;
    flex-direction: column;
    width: 19%;
    aspect-ratio: 2 / 3;
    overflow: hidden;
    margin-right: .5%;
    margin-left: .5%;
    margin-top: .5%;
    margin-bottom: .5%;
	position: relative;
	border-top: 2px solid #cb4b4b;
	border-bottom: 2px solid #cb4b4b;
}

.swilly_poster {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: .3s all;
    display: block;
}

.swilly_item:hover .swilly_poster {
    transform: scale(1.05);
}

.swilly_title {
    position: absolute;
    bottom: 0;
    color: black;
    font-size: calc(1px + 9*(100vw / 1280));
    font-weight: bold;
    padding: 3px 40px 3px 10px;
    overflow: hidden;
    width: fit-content;
	white-space: nowrap;
    z-index: 0;
	text-decoration: none;
	margin-top: 0;
    margin-bottom: 0;
    margin-left: 0;
    margin-right: 0;
}

.swilly_role {
    position: absolute;
    top: 0;
    color: black;
    font-size: calc(1px + 9*(100vw / 1280));
    font-weight: bold;
    padding: 3px 40px 3px 10px;
    overflow: hidden;
    width: fit-content;
    z-index: 0;
	margin-top: 0;
    margin-bottom: 0;
    margin-left: 0;
    margin-right: 0;
	white-space: nowrap;
}

.swilly_role::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 31px;
    left: -20px;
    top: 0;
    transform: skewX(315deg);
    z-index: -1;
    background: #cb4b4b;
}

.swilly_title::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 31px;
    left: -20px;
    top: 0;
    transform: skewX(45deg);
    z-index: -1;
    background: #cb4b4b;
}

@media screen and (max-width: 768px) {
	.swilly_item {
		width: 24%;
	}
}

@media screen and (max-width: 492px) {
	.swilly_item {
		width: 32%;
	}
}
</style></textarea>
		</div>
	</div>
HTML;
}
echo <<<HTML
	</div>
HTML;
?>