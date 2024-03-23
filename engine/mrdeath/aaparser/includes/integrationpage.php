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
			<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">Инструкция по настройке вывода расписания на главной:<br>В файле main.tpl в нужное место вставьте код вывода расписания<br><textarea style="width:100%;height:300px;" disabled><div class="calendar box-body-s">
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
		</div></textarea>
		    </div>
		</div>
HTML;
}
echo <<<HTML
	</div>
HTML;
?>