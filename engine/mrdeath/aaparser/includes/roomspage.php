<?php
echo <<<HTML
	<div id="rooms" class="panel panel-flat" style='display:none'>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Включить комнаты совместного просмотра?', 'Включив активируется функционал совместного просмотра. Пользователь создаёт комнату, приглашает других пользователей при помощи ссылки на комнату, он как лидер комнаты управляет комнатой, переключает серии, ставит просмотр на паузу и перематывает. Комната может быть публичной - доступной по ссылке /rooms/ для всех, либо же приватной - доступна только по ссылке на саму комнату, которую создатель отправляет друзьям. Инструкция по настройке находится ниже. <b>Внимание! Для использования данного функционала ваш сайт должен распологаться минимум на vps/vds (виртуальный или выделенный сервер), обычный хостинг не подходит, так как имеет большие ограничения в ресурсах. В данной реализации нет и не может быть кеширования, синхронизация между пользователями происходит в режиме реального времени, регулярно задействуется база данных сайта, в которую записываются и считываются данные</b>', makeCheckBox('settings[rooms_enable]', $aaparser_config['settings']['rooms_enable'], 'ShowOrHideRooms'));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;" id="rooms-settings">Настройка совместного просмотра</div>
		<div class="table-responsive" id="rooms-settings-area">
			<table class="table table-striped">
HTML;
showRow('Семейство иконок Font Awesome', 'Выберите семейство иконок Font Awesome, соответствующее иконкам используемым на сайте. Если у вас не подключена библиотека иконок то обязательно подключаем <a href="https://fontawesome.com/" target="_blank">по ссылке</a>', makeDropDown( $fa_icons, "push_notifications[fa_icons_rooms]", $aaparser_config['push_notifications']['fa_icons_rooms']));
showRow('Запретить одному и тому же пользователю создавать больше одной комнаты для одинаковой новости?', 'Включив активируется проверка в момент создания комнаты, если пользователь уже создавал её для новости, то вместо новой комнаты его перенесёт в ранее созданную', makeCheckBox('settings[rooms_limit]', $aaparser_config['settings']['rooms_limit']));
showRow('Выводить приватные комнаты на странице всех активных комнат?', 'Включив на странице /rooms/ будут выводиться не только публичные но и приватные комнаты', makeCheckBox('settings[show_private]', $aaparser_config['settings']['show_private']));
showRow('Сколько времени с момента последнего посещения создателя комнаты считать комнату активной?', 'Укажите время в секундах. Если создатель комнаты в течении заданного вами времени не производил никаких действий с плеером комнаты - запуск, переключение серии, пауза, то комната будет считаться не активной и не будет показана при выводе активных комнат. Рекомендованное время - 30', showInput(['settings[active_time]', 'number', $aaparser_config['settings']['active_time']]));
showRow('Считать комнату активной если её создатель не запустил плеер?', 'Включив данный параметр комната будет считаться активной даже если её создатель находится в ней, но не производит никаких действий - запуск плеера, переключение серий, пауза', makeCheckBox('settings[leader_afk]', $aaparser_config['settings']['leader_afk']));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;" id="rooms-info">Инструкция по настройке</div>
		<div class="table-responsive" id="rooms-info-area">
			<table class="table table-striped">
                <tbody>
                    <tr>
                        <td style="width:100%">
                            1. Если у вас apache, то открываем .htaccess в корне сайта и ниже строчки <b>RewriteEngine On</b> вставляем правила<br>
                            <textarea style="width:100%;height:100px;" disabled>

RewriteRule ^room/([^/]*)(/?)+$ index.php?do=enter_room&hash=$1 [L]
RewriteRule ^rooms/page/([0-9]+)(/?)+$ index.php?do=rooms_list&cstart=$1 [B,L]
RewriteRule ^rooms(/?)+$ index.php?do=rooms_list [L]
                            </textarea>
                            <br>Если у вас сайт работает на nginx то правила такие<br>
                            <textarea style="width:100%;height:100px;" disabled>
rewrite "^/room/([^/]*)(/?)+$" /index.php?do=enter_room&hash=$1 break;
rewrite ^/rooms/page/([0-9]+)(/?)+$ /index.php?do=rooms_list&cstart=$1 last;
rewrite ^/rooms(/?)+$ /index.php?do=rooms_list last;
                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            2. В корне папки с шаблоном создаём файл <b>rooms.tpl</b> с таким содержимым:<br>
                            <textarea style="width:100%;height:330px;" disabled>
<div class="row">
   <div class="col s12 l9">
      <div class="loader-wrapper">
         <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
         </div>
      </div>
      <div class="room" id="room-data" data-id="{id}" data-leader="{leader}" data-shikimori_id="{shikimori_id}" data-mdl_id="{mdl_id}">
         <div class="room-anime room--visible">
            <div class="room-anime__img">
               <img src="{poster}" alt="Постер">
            </div>
            <div class="room-anime__wrapper">
               <a href="#" class="room-anime__name">{title}</a>
               <div class="room-anime__info">Создатель комнаты: <a href="/user/{leader}/">{leader}</a></div>
            </div>
            <div class="room-anime__controls">
               <div class="room-anime__status"><i class="{$fa_icons_rooms} fa-clock"></i> <span id="room-status">На паузе</span></div>
               <div class="room-anime__episode">Серия <span id="room-episode">{episode_num}</span></div>
            </div>
         </div>
         <div class="room__player">
            <div class="iframe-container"><iframe id="room-player" src="{iframe}" frameborder="0" webkitallowfullscreen="true" mozallowfullscreen="true" scrolling="no" allowfullscreen allow="autoplay *; fullscreen *"></iframe></div>
         </div>
         <div class="anime-player__controls">
            <button class="anime-player__fullscreen-btn" aria-label="Развернуть на весь экран"><i class="{$fa_icons_rooms} fa-expand-arrows-alt"></i> <span>На весь экран</span></button>
            <button class="anime-player__info-btn" style="display:none;"><i class="{$fa_icons_rooms} fa-info"></i> <span>Нажмите кнопку плей</span></button>
         </div>
      </div>
   </div>
   <div class="col s12 l3 sidebar">
      <div class="room-chat">
         <div>
            <div class="small-title">Чат участников</div>
            <button class="anime-player__fullscreen-btn-close" aria-label="Свернуть"><i class="{$fa_icons_rooms} fa-expand-arrows-alt"></i> <span>Свернуть</span></button>
            <button class="anime-player__info2-btn" style="display:none;"><i class="{$fa_icons_rooms} fa-info"></i> <span>Нажмите кнопку плей</span></button>
         </div>
         <div class="room-chat__messages scroll" id="room-chat-scroll">
            {chat}
         </div>
         <div class="room-chat__send-form">
            &lt;textarea name="room-chat" id="room-chat" cols="30" rows="10" placeholder="Написать в чат"&gt;&lt;/textarea&gt;
            <button class="room-chat__send-message-btn blue-btn button-auto"><i class="{$fa_icons_rooms} fa-paper-plane"></i></button>
         </div>
      </div>
      <div class="small-title">Настройки</div>
      <div class="room-settings">
         <div class="room-settings__item">
            <div class="room-settings__item-title">Пригласить друзей</div>
            <div class="room-settings__item-link">
               <div id="copy-room-link">{link}</div> <button onclick="CopyRoomLink(); return false;"><i class="{$fa_icons_rooms} fa-copy"></i></button>
            </div>
         </div>
         <div class="room-settings__item">
            <div class="room-settings__item-title">Звук новых сообщений</div>
            <div class="switch">
               <input class="switch-input" id="soundSwitch" type="checkbox" checked>
               <label class="switch-for" for="soundSwitch"></label>
            </div>
         </div>
         [if_leader]<div class="room-settings__item">
            <div class="room-settings__item-title">Приватная комната</div>
            <div class="switch">
               <input class="switch-input" id="isPublic" type="checkbox" {public}>
               <label class="switch-for" for="isPublic"></label>
            </div>
         </div>[/if_leader]
      </div>
      <div class="small-title">Пользователи</div>
      <div class="room-users">
         {online}
      </div>
   </div>
</div>
                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            3. В корне папки с шаблоном создаём файл <b>rooms_shortstory.tpl</b> и копируем сюда содержимое вашего shortstory.tpl. Данный файл отвечает за краткий вывод списка активных комнат на странице /rooms/. В данном файле доступны абсолютно все теги, что и в краткой новости, а также следующие теги:<br>
                            <textarea style="width:100%;height:265px;" disabled>
{room-url} - выводит ссылку на комнату
{leader-login} - выводит логин/никнейм создателя комнаты
{leader-link} - выводит ссылку на профиль создателя комнаты
{leader-avatar} - выводит ссылку на аватарку создателя комнаты
[room-episode]...[/room-episode] - выводит содержимое тега в случае если аниме является сериалом и номер запущенной создателем комнаты серии больше нуля
{room-episode} - выводит номер запущенной создателем комнаты серии
[room-season]...[/room-season] - выводит содержимое тега в случае если аниме является сериалом и номер запущенного создателем комнаты сезона больше нуля
{room-season} - выводит номер запущенного создателем комнаты сезона
[public]...[/public] - выводит содержимое тегов в случае если комната является приватной. К примеру вы можете добавить к ссылке скрипт предупреждение о том, что комната приватная таким образом [public]onclick="DLEalert('Комната приватная, попросите пользователя выслать вам приглашение',dle_info );return false;"[/public]
{created} - выводит дату создания комнаты
{created=формат даты} - выводит дату в заданном в теге формате. Тем самым вы можете выводить не только дату целиком но и ее отдельные части. Формат даты задается задается согласно формату принятому в PHP. Например тег {created=d} выведет день месяца создания комнаты, а тег {created=F} выведет название месяца, а тег {created=d-m-Y H:i} выведет полную дату и время

                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            4. В файл шаблона полной новости fullstory.tpl в нужное место, где будет выведена кнопка (например под описанием/сюжетом) вставляем код:<br>
                            <textarea style="width:100%;height:220px;" disabled>
[not-group=5]
[xfgiven_iframe_url]
<div class="rooms-invite" data-news_id="{news-id}" data-news_title="{title}" data-news_iframe="[xfvalue_iframe_url]" data-shikimori_id="[xfvalue_shikimori_id]" data-mdl_id="[xfvalue_mdl_id]">
	<div class="room-invite__image">
		<img id="room-poster" src="[xfvalue_image_url_poster]" alt="Постер">
	</div>
	<div>
		<div class="room-invite__title">Совместный просмотр</div>
		<div class="room-invite__desc">Смотри это и любое другое аниме вместе с друзьями<span>, с помощью функции совместного просмотра</span></div>
	</div>
	<div class="room-invite__label">Новинка</div>
</div>
[/xfgiven_iframe_url]
[/not-group]
                            </textarea>
                            <br>В данном коде заменяем в тегах [xfvalue_iframe_url], [xfgiven_iframe_url] и [/xfgiven_iframe_url] iframe_url на латинское название вашего доп. поля со ссылкой на плеер Kodik.<br>Заменяем в теге [xfvalue_image_url_poster] image_url_poster на латинское название вашего доп. поля со ссылкой на постер к аниме.<br>Заменяем в теге [xfvalue_shikimori_id] shikimori_id на латинское название вашего доп. поля, содержащее id аниме в базе Shikimori. В случае если у вас на сайте не публикуются аниме то удалите код data-shikimori_id="[xfvalue_shikimori_id]" из файла.<br>Заменяем в теге [xfvalue_mdl_id] mdl_id на латинское название вашего доп. поля, содержащее id дорамы в базе MyDramaList. В случае если у вас на сайте не публикуются дорамы то удалите код data-mdl_id="[xfvalue_mdl_id]" из файла. 
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            5. В нужном вам месте сайта разместите ссылку на список активных комнат просмотра, пример ссылки:
                            <textarea style="width:100%;height:80px;" disabled>
[not-group=5]<a rel=”nofollow” href="/rooms/">Совместный просмотр</a>[/not-group]
                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            6. Для вывода списка активных комнат в любом месте сайта используйте специальный тег {roomscustom...}. Описание параметров:<br><br>
<b>newsid="x"</b> - выведет комнаты созданные с новостями с id x, разделять можно запятой без пробелов. Также вы можете указывать диапазон ID новостей при помощи тире. Например 1,4-8,11 выведет комнаты с новостью c ID 1, новостями имеющими id c 4 по 8, а также с ID 11<br>
<b>newsidexclude="x"</b> - выведет комнаты id которых не равно x, разделять можно запятой без пробелов. Также вы можете указывать диапазон ID новостей при помощи тире.<br>
<b>leader="x"</b> - выведет комнаты созданные пользователем с логином x, разделять можно запятой без пробелов.<br>
<b>leaderexclude="x"</b> - выведет комнаты созданные всеми пользователем, кроме пользователя с логином x, разделять можно запятой без пробелов.<br>
<b>template="x"</b> - файл шаблона, при помощи которого будет выводиться краткая новость комнат. Вы должны создать файл x.tpl в папке с шаблоном. Список доступных тегов будет указан ниже.<br>
<b>withcount="yes"</b> - данным параметром мы включаем подсчёт количества пользователей, находящихся в комнате, активируется тег {count}. Внимание, данный параметр добавляет +1 запрос в бд для каждого вызова {roomscustom...}<br>
<b>limit="x"</b> - x это максимальное количество комнат для вывода.<br>
<b>hideprivate="no"</b> - по умолчанию приватные комнаты скрыты при выводе. Данным параметром вы уберете ограничение и они будут показаны. Вам нужно будет повешать скрипт-событие на ссылку, чтобы при клике пользователь не мог попасть в такую комнату, например вывести информацию о том, что комната приватна и доступна только по ссылке.<br>
<b>activetime="x"</b> - x - время в секундах. Если создатель комнаты в течении заданного вами времени не производил никаких действий с плеером комнаты - запуск, переключение серии, пауза, то комната будет считаться не активной и не будет показана при выводе активных комнат. Рекомендованное время - 30<br>
<br>
                            Список тегов, доступных в шаблоне при выводе через {roomscustom...}:<br><br>
<b>{room-link}</b> - выводит прямую ссылку на страницу комнаты<br>
<b>{id}</b> - выводит id комнаты<br>
<b>{leader}</b> - выводит логин создателя комнаты<br>
<b>{leader_avatar}</b> - выводит аватарку создателя комнаты<br>
<b>{news_id}</b> - выводит id новости, для которой создана комната<br>
<b>{poster}</b> - выводит ссылку на постер новости, для которой создана комната<br>
<b>{title}</b> - выводит тайтл новости, для которой создана комната<br>
<b>{episode_num}</b> - выводит номер серии, которая запущена в плеере создателем<br>
<b>[episode_num]...[/episode_num]</b> - выводит содержимое тегов в случае если это сериал, номер серии больше нуля<br>
<b>{season_num}</b> - выводит номер сезона, который запущен в плеере создателем<br>
<b>[season_num]...[/season_num]</b> - выводит содержимое тегов в случае если это сериал, номер сезона больше нуля<br>
<b>[public]...[/public]</b> - выводит содержимое тегов в случае если комната является приватной, полезно использовать в связке с параметром <b>hideprivate="no"</b> в теге {roomscustom...}.  К примеру вы можете добавить к ссылке скрипт предупреждение о том, что комната приватная таким образом [public]onclick="DLEalert('Комната приватная, попросите пользователя выслать вам приглашение',dle_info );return false;"[/public]<br>
<b>{count}</b> - выводит количество пользователей, которые в данный момент находятся в комнате. Для активации этого тега вам нужно указать параметр <b>withcount="yes"</b> в теге {roomscustom...}<br>
<b>{created}</b> - выводит дату создания комнаты<br>
<b>{created=формат даты}</b> - выводит дату в заданном в теге формате. Тем самым вы можете выводить не только дату целиком но и ее отдельные части. Формат даты задается задается согласно формату принятому в PHP. Например тег {created=d} выведет день месяца создания комнаты, а тег {created=F} выведет название месяца, а тег {created=d-m-Y H:i} выведет полную дату и время<br>
Так же для ситуаций когда в данный момент нет активных комнат в файле шаблона можно использовать тег<br>
<b>[no-rooms]...[/no-rooms]</b> - выводит содержимое тега, в случае если выборка {roomscustom...} не дала результатов, комнат не найдено<br><br>
Примеры использования тега {roomscustom...}. Для вывода комнат на странице полной новости в fullstory.tpl вставляем тег<br>
<textarea style="width:100%;height:50px;" disabled>
{roomscustom newsid="{news-id}" template="roomscustom" withcount="yes" limit="5"}
</textarea>
Для вывода комнат на главной странице в main.tpl вставляем тег<br>
<textarea style="width:100%;height:50px;" disabled>
{roomscustom template="roomscustom" hideprivate="no" limit="10"}
</textarea>
Для вывода созданных комнат пользователем на его странице в файле userinfo.tpl вставляем тег<br>
<textarea style="width:100%;height:50px;" disabled>
{roomscustom leader="{usertitle}" template="roomscustom"}
</textarea>

                        </td>
                    </tr>
                    
                </tbody>
			</table>
		</div>
	</div>
HTML;
?>