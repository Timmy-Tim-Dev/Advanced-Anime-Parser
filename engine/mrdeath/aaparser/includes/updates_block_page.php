<?php
echo <<<HTML
	<div id="updates_block" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка блока обновления сериалов</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;


showRow('Вести историю для блока обновления серий сериалов?', 'Если включено, будет вестись история обновления серий сериалов, которую вы сможете отобразить на главной странице сайта. Инструкция находится внизу', makeCheckBox('updates_block[enable_history]', $aaparser_config_push['updates_block']['enable_history']));
showRow('Вести историю в случае если в последней вышедшей серии добавлена новая озвучка?', 'Если включено, в историю будет записано каждое добавление новой озвучки в одной и той же серии, следовательно один сериал в блоке будет показан несколько раз. Если выключено, сериал будет показан в блоке лишь раз по факту обновления серии.<br><b>Будет срабатывать только если во вкладке "Поднятия новостей" активирован пункт "Поднимать в случае если в последней вышедшей серии добавлена новая озвучка?" и выбрано доп. поле "Дополнительное поле c историей добавленных озвучек последней доступной серии"</b>', makeCheckBox('updates_block[new_translation_history]', $aaparser_config_push['updates_block']['new_translation_history']));
showRow('За сколько дней вести историю?', 'Введите количество дней, 1 - сегодня, 2 - сегодня и вчера, и так далее', showInput(['updates_block[count_days]', 'number', $aaparser_config_push['updates_block']['count_days']]));
showRow('Лимит записей за день', 'Вы можете задать лимит записей истории обновлений за день. Для отключения лимита выставьте 0', showInput(['updates_block[count_history]', 'number', $aaparser_config_push['updates_block']['count_history']]));

echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Инструкция по выводу на сайте</div>
		<div class="table-responsive">
			<table class="table table-striped">
                <tbody>
                    <tr>
                        <td style="width:100%">
                            1. Создайте в корне папки с шаблоном файл под названием kodik_updates_block.tpl.
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
RewriteRule ^subscribes/page/([0-9]+)(/?)+$ index.php?do=subscribe_page&cstart=$1 [L]
RewriteRule ^subscribes(/?)+$ index.php?do=subscribe_page [L]
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
                            9. В корне папки с вашим шаблоном создайте файл <b>shortstory_subscribes.tpl</b> и перенесите в него содержимое вашего <b>shortstory.tpl</b>, оформите как вам нужно. Данный файл отвечает за показ кратких новостей на странице подписок, в нём работают все стандартные теги краткой новости.<br>
							- Для просмотренного материала <b>[subscribe_viewed]</b> Материал просмотрен <b>[/subscribe_viewed]</b><br>
							- Для не просмотренного материала <b>[subscribe_notviewed]</b> Материал НЕ просмотрен <b>[/subscribe_notviewed]</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            10. Страница всех подписок доступна по ссылке <a href="{$config["http_home_url"]}subscribes/" target="_blank">{$config["http_home_url"]}subscribes/</a> для авторизованного пользователя. Разместите данную ссылку в нужном вам месте, не забудьте обернуть в тег <b>[not-group=5]</b>...<b>[/not-group]</b>. Для визуального оформления страницы в main.tpl используйте тег <b>[available=subscribe_page]</b>...<b>[/available]</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            11. Для вывода колокольчика <a href="https://prnt.sc/zOGW-gRNleVN" target="_blank">(скриншот)</a> на постере/обложке аниме, в шаблонах <b>shortstory.tpl, fullstory.tpl, shortstory_subscribes.tpl</b> а также в шаблонах выводимых тегом <b>{custom}</b> вам доступен тег <b>{user_subscribed}</b>, вставьте его ниже тега вывода постера/обложки - под/ниже img. 
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            12. Для вывода количества подписанных новостей <b>{subscribe-total}</b> у пользователя. А так же, <b>{subscribe-notification-count}</b> для вывода не просмотренных уведомлении пользователя.
                        </td>
                    </tr>
                </tbody>
			</table>
		</div>
	</div>
HTML;
?>