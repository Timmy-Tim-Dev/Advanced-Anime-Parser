<?php

$playlist_cache_size = convert_bytes(dir_size(ENGINE_DIR."/mrdeath/aaparser/cache/player/"));

echo <<<HTML
	<div id="player" class="panel panel-flat" style='display:none'>
	    <div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Выводить плеер модулем?', 'Если включено, модуль надо подключать вместо плеера, и он будет выводить плееры во вкладках с озвучками, с запоминанем озвучки и серии', makeCheckBox('player[enable]', $aaparser_config['player']['enable'], 'ShowOrHidePlayer'));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;" id="kodik-player">Настройка вывода плеера</div>
		<div class="table-responsive" id="kodik-player-settings">
			<table class="table table-striped">
HTML;
showRow('Метод формирования плейлиста', 'Выберите метод формирования плейлиста.<br>В обычном методе плейлист формируется в последовательности <b>озвучка=>сезоны=>доступные серии, настройки повышения и понижения приоритета озвучек игнорируются</b>.<br>В новом логика иная: <b>сезон=>серии=>доступные озвучки, новый метод рекомендуется использовать тем, кто использует настройки повышения и понижения приоритета озвучек</b>', makeDropDown( array(0 => 'обычный', 1 => 'новый'), "player[method]", $aaparser_config['player']['method']));
showRow('Активировать постоянный кеш?', 'Если включено, то модуль будет записывать кеш в отдельную папку, такой кеш не будет очищаться движком, активация увеличит расходуемое место на сервере, но уменьшит количество запросов к апи kodik и к бд. Есть старые сериалы, новые серии которых не выходят годами, и нет необходимости регулярно обращаться к апи для получения списка серий. <b>Используйте данную опцию только в случае, если вы используете функционал обновления серий по крону</b>', makeCheckBox('player[custom_cache]', $aaparser_config['player']['custom_cache']));
showRow('Общий вес файлов кеша плейлиста - <span id="pl-cache-size">'.$playlist_cache_size.'</span>', 'При изменении какой либо опции из данного раздела обязательно очистите кеш папки с плейлистами', '<button onclick="clear_player_cache(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-trash position-left"></i>Очистить кеш</button>');
showRow('Отдельные кнопки серий, сезонов и озвучек', 'Если включено, то будут выводиться кнопки выбора серии, сезона и озвучки отдельно от плеера(за его пределами)', makeCheckBox('player[buttons]', $aaparser_config['player']['buttons']));
showRow('По умолчанию будет последняя серия?', 'Если включено, то в плейлисте по умолчанию будет показываться последняя серия. Иначе будет первая серия', makeCheckBox('player[last_episode]', $aaparser_config['player']['last_episode']));
showRow('Скрыть отдельные кнопки выбора серий?', 'Если включено, кнопки выбора серий будут скрыты, сами серии будут выбираться в самом плеере. <b>В новом методе формирования плейлиста данная настройка игнорируется.</b>', makeCheckBox('player[hide_episodes]', $aaparser_config['player']['hide_episodes']));
showRow('Количество серий в сезоне, при достижении которого кнопки серий будут выведены вертикально', 'Вы можете задать количество серий, если количество серий в сезоне больше чем заданное значение, то вместо горизонтального вывода кнопок серий (карусель с кнопками вперед-назад) будет активирован вертикальный. Если выставить 0 то вывод всегда будет горизонтальный', showInput(['player[vertical_eps]', 'number', $aaparser_config['player']['vertical_eps']]));
showRow('Включить автопереключение серий при выводе отдельных кнопок серий?', 'Если включено, то дополнительным скриптом будет срабатывать автопереключение серий при условии что вы выводите кнопки выбора серий отдельно от плеера (параметр настроек выше выключен). Не забудьте активировать автопереключение в настройках кастомизации вашего плеера <a href="https://bd.kodik.biz/users/player-settings" target="_blank">в данном разделе</a>', makeCheckBox('player[auto_next]', $aaparser_config['player']['auto_next']));
showRow('Максимальное количество новостей для запоминания', 'Введите максимальное количество новостей, в которых будет запоминаться сезон, серия и озвучка. Чем выше число - тем больше данных будет записано в бд, это увеличит вес бд. Оптимальное число 50. Для отключения запоминания выставьте 0<br/><b class="faq_find faq_id_3">Подробнее</b>', showInput(['player[max_remembers]', 'number', $aaparser_config['player']['max_remembers']]));
showRow('Доп. поле содержащее дополнительные параметры для плеера', 'Выберите дополнительное поле, в котором вы записываете дополнительные параметры для плеера, например start_from=300 или min_age=18&min_age_confirmation=true', makeDropDown( $xfields_list, "player[add_params]", $aaparser_config['player']['add_params']));
showRow('Доп. поле для геоблокировки по странам', 'Выберите дополнительное поле, в котором вы записываете ISO коды стран для скрытия плеера для определённых стран. Например RU,UA<br/><font style="color:red">Тип доп. поля должен быть Одна строка</font>', makeDropDown( $xfields_list, "player[geoblock]", $aaparser_config['player']['geoblock']));
showRow('Группы пользователей для геоблокировки', 'Выберите группы пользователей для которых будет включаться геоблокировка<br/><font style="color:red">Данный пункт обязателен если пользуетесь блокировкой по странам</font>', makeSelect( $usergroups, "player[geoblock_group]", $aaparser_config['player']['geoblock_group'], 'Выберите группы', 0));
showRow('Доп. поле c id или ссылкой аниме на World-Art', 'Выберите дополнительное поле, в котором вы записываете id или ссылку аниме на World-Art. Актуально если в аниме не существует id Shikimori', makeDropDown( $xfields_list, "player[worldart_anime]", $aaparser_config['player']['worldart_anime']));
showRow('Доп. поле c id или ссылкой сериала на World-Art (раздел cinema)', 'Выберите дополнительное поле, в котором вы записываете id или ссылку сериала (не аниме) на World-Art. Актуально если в аниме не существует id Shikimori', makeDropDown( $xfields_list, "player[worldart_cinema]", $aaparser_config['player']['worldart_cinema']));
showRow('Доп. поле c id Kinopoisk', 'Выберите дополнительное поле, в котором вы записываете id Kinopoisk. Актуально если в новости не существует id Shikimori или MyDramaList', makeDropDown( $xfields_list, "player[kinopoisk_id]", $aaparser_config['player']['kinopoisk_id']));
showRow('Доп. поле c id IMDb', 'Выберите дополнительное поле, в котором вы записываете id IMDb. Актуально если в новости не существует id Shikimori или MyDramaListi', makeDropDown( $xfields_list, "player[imdb_id]", $aaparser_config['player']['imdb_id']));

echo <<<HTML
			
			</table>
		</div>
		<div class="panel-body anime-settings" style="padding: 20px;font-size:20px; font-weight:bold;" id="kodik-player-anime">Настройка для аниме</div>
		<div class="table-responsive anime-settings" id="kodik-player-settings-anime">
			<table class="table table-striped">
HTML;
if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name.json') ) {
echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Обновить озвучки аниме</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
}
else {
    echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Получить озвучки аниме</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
}
showRow('Повысить приоритет озвучек', 'Приоритет выбранных студий переводов будет повышен. <b>Данная настройка работает только с новым методом формирования плейлиста, на старом игнорируется</b>', makeSelect( $translators_array, "player[translations_priority]", $aaparser_config['player']['translations_priority'], 'Выберите озвучку или несколько озвучек', 0));
showRow('Понизить приоритет озвучек', 'Приоритет выбранных студий переводов будет понижен. <b>Данная настройка работает только с новым методом формирования плейлиста, на старом игнорируется</b>', makeSelect( $translators_array, "player[translations_unpriority]", $aaparser_config['player']['translations_unpriority'], 'Выберите озвучку или несколько озвучек', 0));
showRow('Отключить вывод озвучек', 'Вывод выбранных студий озвучек будет отключен в плеере', makeSelect( $translators_array, "player[translations_hide]", $aaparser_config['player']['translations_hide'], 'Выберите озвучку или несколько озвучек', 0));

echo <<<HTML
			
			</table>
		</div>
		<div class="panel-body dorama-settings" style="padding: 20px;font-size:20px; font-weight:bold;" id="kodik-player-dorama">Настройка для дорам</div>
		<div class="table-responsive dorama-settings" id="kodik-player-settings-dorama">
			<table class="table table-striped">
HTML;
if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/translators_name_dorama.json') ) {
echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations_dorama(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Обновить озвучки дорам</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
}
else {
    echo <<<HTML
    <tr>
        <td class="col-xs-10 col-sm-6 col-md-7 "><button onclick="update_translations_dorama(); return false;" class="btn bg-slate-600 btn-raised legitRipple"><i class="fa fa-microphone position-left"></i>Получить озвучки дорам</button><span class="note large"></span></td>
        <td class="col-xs-2 col-md-5 settingstd "></td>
    </tr>
HTML;
}
showRow('Повысить приоритет озвучек', 'Приоритет выбранных студий переводов будет повышен. <b>Данная настройка работает только с новым методом формирования плейлиста, на старом игнорируется</b>', makeSelect( $translators_array_dorama, "player[translations_priority_dorama]", $aaparser_config['player']['translations_priority_dorama'], 'Выберите озвучку или несколько озвучек', 0));
showRow('Понизить приоритет озвучек', 'Приоритет выбранных студий переводов будет понижен. <b>Данная настройка работает только с новым методом формирования плейлиста, на старом игнорируется</b>', makeSelect( $translators_array_dorama, "player[translations_unpriority_dorama]", $aaparser_config['player']['translations_unpriority_dorama'], 'Выберите озвучку или несколько озвучек', 0));
showRow('Отключить вывод озвучек', 'Вывод выбранных студий озвучек будет отключен в плеере', makeSelect( $translators_array_dorama, "player[translations_hide_dorama]", $aaparser_config['player']['translations_hide_dorama'], 'Выберите озвучку или несколько озвучек', 0));

echo <<<HTML
			
			</table>
			<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">Для вывода плеера в файле шаблона полной новости fullstory.tpl вставьте в то место где плеер будет выводиться код<br><b>&lt;div id="kodik_player_ajax" data-news_id="{news-id}"&gt;&lt;/div&gt;</b><br>Не забудьте обернуть тегом [xfgiven_x] или [catlist=1,2....]</div>
			<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">Вы можете выводить плеер с анимированным прелоадером в момент загрузки плейлиста плеера, <a href="https://prnt.sc/CEMEEe3A3kjp" target="_blank">скриншот</a> как выглядит в действии. Для этого добавьте следующие стили в подключенный к шаблону файл стилей<textarea style="width:100%;height:300px;" disabled="">
.loading-kodik {
  	position: absolute;
    width: 5rem;
    height: 5rem;
    transform-style: preserve-3d;
    perspective: 800px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.loading-kodik .arc {
  position: absolute;
  content: "";
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border-bottom: 3px solid var(--primary-color);
}
.loading-kodik .arc:nth-child(1) {
  animation: rotate1 1.15s linear infinite;
  animation-delay: -0.8s;
}
.loading-kodik .arc:nth-child(2) {
  animation: rotate2 1.15s linear infinite;
  animation-delay: -0.4s;
}
.loading-kodik .arc:nth-child(3) {
  animation: rotate3 1.15s linear infinite;
  animation-delay: 0s;
}

@keyframes rotate1 {
  from {
    transform: rotateX(35deg) rotateY(-45deg) rotateZ(0);
  }
  to {
    transform: rotateX(35deg) rotateY(-45deg) rotateZ(1turn);
  }
}
@keyframes rotate2 {
  from {
    transform: rotateX(50deg) rotateY(10deg) rotateZ(0);
  }
  to {
    transform: rotateX(50deg) rotateY(10deg) rotateZ(1turn);
  }
}
@keyframes rotate3 {
  from {
    transform: rotateX(35deg) rotateY(55deg) rotateZ(0);
  }
  to {
    transform: rotateX(35deg) rotateY(55deg) rotateZ(1turn);
  }
}
			    </textarea></br>В файле шаблона полной новости fullstory.tpl вставьте в то место где плеер будет выводиться код<br><textarea style="width:100%;height:180px;" disabled="">
<div id="kodik_player_ajax" data-news_id="{news-id}">
	<div class="loading-kodik">
  		<div class="arc"></div>
  		<div class="arc"></div>
  		<div class="arc"></div>
	</div>
</div>
			    </textarea></div>
		</div>
	</div>
HTML;
?>