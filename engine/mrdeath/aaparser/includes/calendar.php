<?php
echo <<<HTML
<div id="calendar" class="panel panel-flat" style='display:none'>
    <div class="panel-body anime-settings" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка расписания выхода серий аниме</div>
		<div class="table-responsive anime-settings">
			<table class="table table-striped">
HTML;
showRow('Включить функционал расписания выхода серий аниме?', 'Включив, модуль будет собирать данные про расписание выхода онгоингов аниме с Shikimori и выводить его на отдельной странице <a href="'. $config["http_home_url"] .'schedule/" target="_blank">'. $config["http_home_url"] .'schedule/</a>. Инструкция по настройке находится ниже', makeCheckBox('calendar_settings[enable_schedule]', $aaparser_config_push['calendar_settings']['enable_schedule']));
showRow('Доп. поле с постером', 'Выберите дополнительное поле, в котором содержится постер', makeDropDown( $xfields_all_list, "settings[poster]", $aaparser_config_push['main_fields']['xf_poster']));
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
</div>
HTML;
?>