<?php
echo <<<HTML
<div id="modules" style="display:none">
    <div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	    <div class="navbar-collapse" id="option_menu_modules">
		    <ul class="nav navbar-nav">
			    <li class="active"><a onclick="ChangeOptionModules(this, 'player');" class="tip" title="Настройка вывода плеера"><i class="fa fa-play"></i> Вывод плеера</a></li>
			    <li class="anime-settings"><a onclick="ChangeOptionModules(this, 'calendar');" class="tip" title="Настройка расписания выхода серий аниме"><i class="fa fa-calendar"></i> Расписание серий</a></li>
			    <li><a onclick="ChangeOptionModules(this, 'updates_block');" class="tip" title="Настройка блока обновления сериалов"><i class="fa fa-list-ul"></i> Блок обновления сериалов</a></li>
			    <li><a onclick="ChangeOptionModules(this, 'push');" class="tip" title="Настройка Push уведомлений"><i class="fa fa-bell"></i> Push уведомления</a></li>
			    <li><a onclick="ChangeOptionModules(this, 'rooms');" class="tip" title="Настройка функционала совместного просмотра"><i class="fa fa-eye"></i> Совместный просмотр</a></li>
			    <li><a onclick="ChangeOptionModules(this, 'gindexing');" class="tip" title="Настройка Google Indexing Api"><i class="fa fa-google"></i> Google Indexing</a></li>
			    <li><a onclick="ChangeOptionModules(this, 'tgposting');" class="tip" title="Настройка постинга в телеграм"><i class="fa fa-telegram"></i> Постинг в Telegram</a></li>
			    <li><a onclick="ChangeOptionModules(this, 'personajes');" class="tip" title="Настройка парсинга актёров и персонажей"><i class="fa fa-users"></i> Актёры и персонажи</a></li>
		    </ul>
	    </div>
    </div>
HTML;
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/playerpage.php'; //Вывод плеера
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/calendar.php'; //Расписание серий
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/updates_block_page.php'; //Блок обновления сериалов
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/pushpage.php'; //Push уведомления
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/roomspage.php'; //Совместный просмотр
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/gindexpage.php'; //Google indexing
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/tgpostingpage.php'; //Постинг в Telegram
include_once ENGINE_DIR.'/mrdeath/aaparser/includes/personajespage.php'; //Актёры и персонажи
echo <<<HTML
</div>
HTML;
?>