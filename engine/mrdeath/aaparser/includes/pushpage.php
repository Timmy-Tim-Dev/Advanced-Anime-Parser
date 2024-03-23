<?php
echo <<<HTML
	<div id="push" class="panel panel-flat" style='display:none'>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Включить Push уведомления?', 'Включив активируется система подписок на уведомления через сервис OneSignal. При выходе новой серии аниме или лучшего качества подписавшиеся пользователи будут получать уведомления на своём смартфоне или пк. <b>Перед включением следуйте инструкции ниже, вам нужно создать приложение в сервисе OneSignal и указать OneSignal App ID и Rest API Key в поля ниже</b>', makeCheckBox('push_notifications[enable]', $aaparser_config_push['push_notifications']['enable'], 'ShowOrHidePush'));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;" id="push-settings">Настройка Push Уведомлений</div>
		<div class="table-responsive" id="push-settings-area">
			<table class="table table-striped">
HTML;
showRow('Семейство иконок Font Awesome', 'Выберите семейство иконок Font Awesome, соответствующее иконкам используемым на сайте. Если у вас не подключена библиотека иконок то обязательно подключаем <a href="https://fontawesome.com/" target="_blank">по ссылке</a>', makeDropDown( $fa_icons, "push_notifications[fa_icons]", $aaparser_config_push['push_notifications']['fa_icons']));
showRow('OneSignal App ID', 'Укажите OneSignal App ID вашего приложение', showInput(['push_notifications[app_id]', 'text', $aaparser_config_push['push_notifications']['app_id']]));
showRow('Rest API Key', 'Укажите Rest API Key вашего приложения', showInput(['push_notifications[rest_api]', 'text', $aaparser_config_push['push_notifications']['rest_api']]));
showRow('Доп. поле с постером', 'Выберите дополнительное поле, в котором содержится постер', makeDropDown( $xfields_all_list, "push_notifications[poster]", $aaparser_config_push['push_notifications']['poster']));
showRow('Ссылка на картинку-заглушку', 'Введите ссылку на картинку-заглушку в случае отсутствия постера', showInput(['push_notifications[poster_empty]', 'text', $aaparser_config_push['push_notifications']['poster_empty']]));
showRow('Доп. поле с последней серией', 'Выберите дополнительное поле, в котором содержится номер последней вышедшей серии', makeDropDown( $xfields_all_list, "push_notifications[episode]", $aaparser_config_push['push_notifications']['episode']));
showRow('Доп. поле с последним сезоном', 'Выберите дополнительное поле, в котором содержится номер последнего вышедшего сезона', makeDropDown( $xfields_all_list, "push_notifications[season]", $aaparser_config_push['push_notifications']['season']));
showRow('Доп. поле с последней вышедшей озвучкой', 'Выберите дополнительное поле, в котором содержится одна озвучка последней вышедшей серии. Не выбирайте поле со всеми озвучками', makeDropDown( $xfields_all_list, "push_notifications[translation]", $aaparser_config_push['push_notifications']['translation']));
showRow('Заголовок уведомления для сериалов', 'Введите заголовок уведомления про обновление сериала.<br>Например: Обновился аниме сериал<br>Или: MySite(имя вашего сайта)', showInput(['push_notifications[tv_title]', 'text', $aaparser_config_push['push_notifications']['tv_title']]));
showRow('Формат вывода уведомления для сериалов', 'Используйте теги: {episode}, {season}, {translation} и {title}<br>Например: Вышла {episode} серия {season} сезона аниме {title} в озвучке {translation}<br>Если оставить пустым - уведомление по сериалам отправляться не будет', showInput(['push_notifications[tv_text]', 'text', $aaparser_config_push['push_notifications']['tv_text']]));
showRow('Доп. поле с качеством фильма', 'Выберите дополнительное поле, в котором содержится качество фильма', makeDropDown( $xfields_all_list, "push_notifications[quality]", $aaparser_config_push['push_notifications']['quality']));
showRow('Заголовок уведомления для фильмов', 'Введите заголовок уведомления про обновление фильма.<br>Например: Обновился аниме фильм<br>Или: MySite(имя вашего сайта)', showInput(['push_notifications[movie_title]', 'text', $aaparser_config_push['push_notifications']['movie_title']]));
showRow('Формат вывода уведомления для фильмов', 'Используйте теги: {quality} и {title}<br>Обновилось качество аниме фильма {title} до {quality}<br>Если оставить пустым - уведомление по смене качества аниме фильмов отправляться не будет', showInput(['push_notifications[movie_text]', 'text', $aaparser_config_push['push_notifications']['movie_text']]));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;" id="push-info">Инструкция по регистрации приложения и настройке</div>
		<div class="table-responsive" id="push-info-area">
			<table class="table table-striped">
                <tbody>
                    <tr>
                        <td style="width:100%">
                            1. Регистрируемся на сайте <a href="https://onesignal.com/" target="_blank">OneSignal</a> и авторизуемся.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            2. Переходим в раздел <a href="https://dashboard.onesignal.com/apps" target="_blank">управления приложениями</a>.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            3. Нажимаем кнопку <b>New App/Website</b>. <a href="https://prnt.sc/N1X8bGwrLPS-" target="_blank">(скриншот)</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            4. Вводим любое название в поле <b>Name of your app or website</b> и выбираем <b>Web</b> в <b>Set up web push or mobile push. You can set up more later</b>.  <a href="https://prnt.sc/Depu23wPK9KN" target="_blank">(скриншот)</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            5. На следующем шаге создания приложения в пункте 1 выбираем <b>Typical Site</b> <a href="https://prnt.sc/xNU08XgX_Y_h" target="_blank">(скриншот)</a><br>
                             - В пункте 2 заполняем имя сайта, ссылку на сайт а так же ссылку на лого сайта <a href="https://prnt.sc/doTi3zbMimWA" target="_blank">(скриншот)</a><br>
                             - В пункте 3 жмём на три точки, выбираем Edit <a href="https://prnt.sc/DFzfpYonZ_z3" target="_blank">(скриншот)</a> . Далее активируем чекбокс Customize и меняем текст-предложение подписаться на свой <a href="https://prnt.sc/ixbnzz1-_0uD" target="_blank">(скриншот)</a><br>
                             - Пункт 4 не обязательный, тут вы можете настроить приветственное уведомление с благодарностью за подписку. Активируем чекбокс, вводим название сайта и сообщение, или же деактивируем чекбокс <a href="https://prnt.sc/oSxIW1w75O6g" target="_blank">(скриншот)</a><br>
                             - В пункте 5 ничего не меняем, жмём Save.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            6. На следующем шаге скачиваем архив <b>OneSignal SDK Files</b>, распаковываем файл <b>OneSignalSDKWorker.js</b> и загружаем его <b>в корень вашего сайта</b>, так чтобы он был доступен по ссылке ваш.сайт/OneSignalSDKWorker.js . После этого нажимаем Finish. Приложение создано. Нажимаем на пункт меню <b>Keys & IDs</b> и копируем-вставляем в админке OneSignal App ID и Rest API Key <a href="https://prnt.sc/-bhnqRTJ0Av3" target="_blank">(скриншот)</a>. После всех этих действий вы можете активировать чекбокс включения Push уведомления 
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            7. В файл шаблона полной новости fullstory.tpl в нужное место, где будет выведена кнопка (например под постером) вставляем тег:<br>
                            <textarea style="width:100%;height:50px;" disabled>
[push_subscribe]{push_subscribe}[/push_subscribe]
                            </textarea>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width:100%">
                            8. <b>Далее не обязательные пункты</b>, проделайте их если вы хотите добавить страницу всех аниме, на которые подписан пользователь. Данная страница удобна тем, что можно визуально увидеть свои подписки и отписаться от того, на что не хочется больше получать уведомления. Приступим<br>Если у вас apache, то открываем .htaccess в корне сайта и ниже строчки <b>RewriteEngine On</b> вставляем правила<br>
                            <textarea style="width:100%;height:80px;" disabled>
RewriteRule ^subscribes/page/([0-9]+)(/?)+$ index.php?do=notifications_news&cstart=$1 [L]
RewriteRule ^subscribes(/?)+$ index.php?do=notifications_news [L]
                            </textarea>
                            <br>Если у вас сайт работает на nginx то правила такие<br>
                            <textarea style="width:100%;height:80px;" disabled>
rewrite ^/subscribes/page/([0-9]+)(/?)+$ /index.php?do=subscribe_page&cstart=$1 last;
rewrite ^/subscribes/*$ /index.php?do=subscribe_page last;
                            </textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            9. В корне папки с вашим шаблоном создайте файл shortstory_subscribes.tpl и перенесите в него содержимое вашего shortstory.tpl, оформите как вам нужно. Данный файл отвечает за показ кратких новостей на странице подписок, в нём работают все стандартные теги краткой новости.<br>
							- Для просмотренного материала [subscribe_viewed] Материал просмотрен [/subscribe_viewed]<br>
							- Для не просмотренного материала [subscribe_notviewed] Материал НЕ просмотрен [/subscribe_notviewed]
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            10. Страница всех подписок доступна по ссылке вашсайт.ру/subscribes/ для авторизованного пользователя. Разместите данную ссылку в нужном вам месте, не забудьте обернуть в тег [not-group=5]...[/not-group]. Для визуального оформления страницы в main.tpl используйте тег [available=subscribe_page]...[/available]
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            11. Для вывода колокольчика <a href="https://prnt.sc/zOGW-gRNleVN" target="_blank">(скриншот)</a> на постере/обложке аниме, в шаблонах shortstory.tpl, fullstory.tpl, shortstory_subscribes.tpl а также в шаблонах выводимых тегом {custom} вам доступен тег {user_subscribed}, вставьте его ниже тега вывода постера/обложки - под/ниже img. 
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            12. Для вывода количества подписанных новостей {subscribe-total} у пользователя. А так же, {subscribe-notification-count} для вывода не просмотренных уведомлении пользователя.
                        </td>
                    </tr>
                </tbody>
			</table>
		</div>
	</div>
HTML;
?>