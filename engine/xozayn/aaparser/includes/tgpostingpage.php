<?php
echo <<<HTML
	<div id="tgposting" class="panel panel-flat" style='display:none'>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Включить постинг в Telegram?', 'Включив активируется система отправки сообщений в телеграм при редактировании, добавлении и обновлении новостей. <b>Перед включением следуйте инструкции ниже, вам нужно создать бота в Telegram и подключить его к вашей группе, указать bot token и id группы</b>', makeCheckBox('push_notifications[enable_tgposting]', $aaparser_config['push_notifications']['enable_tgposting'], 'ShowOrHideTg'));
echo <<<HTML
			</table>
		</div>
		<div id="show-hide-tgposting">
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка постинга в Telegram</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Bot token', 'Введите токен вашего бота, полученный после его создания', showInput(['push_notifications[tg_bot_token]', 'text', $aaparser_config['push_notifications']['tg_bot_token']]));
showRow('Имя канала с символом @', 'Введите имя канала в формате <b>@имяканала</b>, оно же является частью основной ссылки на приглашение - https://t.me/<b>имяканала</b>', showInput(['push_notifications[tg_chanel]', 'text', $aaparser_config['push_notifications']['tg_chanel']]));
showRow('Прикреплять картинку-постер к посту телеграм?', '', makeCheckBox('push_notifications[tg_enable_poster]', $aaparser_config['push_notifications']['tg_enable_poster']));
showRow('Источник постера', 'Выберите откуда модуль будет брать постер', makeDropDown( ['xfields' => 'доп. поле', 'short_story' => 'краткое описание', 'full_story' => 'полное описание'], "push_notifications[tg_source_poster]", $aaparser_config['push_notifications']['tg_source_poster']));
showRow('Отправлять посты в телеграм при помощи крон?', 'Данная настройка влияет только на ручное добавление или редактирование новостей через админку. Если выключено, посты в телеграм будут отправлены сразу же в момент добавления или редактирования новости. Если включено, посты будут добавляться в очередь и отправляться при помощи крон задачи', makeCheckBox('push_notifications[tg_cron_enable]', $aaparser_config['push_notifications']['tg_cron_enable']));

showRow('Отправлять посты в телеграм при выходе нового эпизода?', '', makeCheckBox('push_notifications[updatetg_new_episode]', $aaparser_config['push_notifications']['updatetg_new_episode']));
showRow('Отправлять посты в телеграм при выходе нового сезона?', '', makeCheckBox('push_notifications[updatetg_new_season]', $aaparser_config['push_notifications']['updatetg_new_season']));
showRow('Отправлять посты в телеграм при выходе новой озвучки?', '', makeCheckBox('push_notifications[updatetg_new_voice]', $aaparser_config['push_notifications']['updatetg_new_voice']));
showRow('Отправлять посты в телеграм при изменений качества материала?', '', makeCheckBox('push_notifications[updatetg_new_quality]', $aaparser_config['push_notifications']['updatetg_new_quality']));
showRow('Отправлять посты в телеграм при изменений статуса материала?', '', makeCheckBox('push_notifications[updatetg_new_status]', $aaparser_config['push_notifications']['updatetg_new_status']));

showRow('Укажите время с которого и по которое посты не будут отправляться в telegram', 'Для того, чтобы посты в telegram не отправлялись в ночное время, вы можете указать время начиная с которого и заканчивая которым посты перестанут отправляться. <b>Для отключения оставьте оба поля пустыми или же укажите 00:00 в обоих полях</b>', '<input type="time" autocomplete="off" style="float: right;width:100px" value="'.$aaparser_config['push_notifications']['stop_send_from'].'" class="form-control" name="push_notifications[stop_send_from]"><input type="time" autocomplete="off" style="float: right;width:100px" value="'.$aaparser_config['push_notifications']['stop_send_to'].'" class="form-control" name="push_notifications[stop_send_to]">');
showRow('Включить отправку постов в Telegram при ручном добавлении новостей через админку?', '', makeCheckBox('push_notifications[tg_addnews]', $aaparser_config['push_notifications']['tg_addnews']));
showRow('Сделать чекбокс по умолчанию включенным при ручном добавлении новостей через админку?', '', makeCheckBox('push_notifications[tg_addnews_cheched]', $aaparser_config['push_notifications']['tg_addnews_cheched']));
showRow('Включить отправку постов в Telegram при ручном редактировании новостей через админку?', '', makeCheckBox('push_notifications[tg_editnews]', $aaparser_config['push_notifications']['tg_editnews']));
showRow('Сделать чекбокс по умолчанию включенным при ручном редактировании новостей через админку?', '', makeCheckBox('push_notifications[tg_editnews_checked]', $aaparser_config['push_notifications']['tg_editnews_checked']));
showRow('Включить отправку постов в Telegram при добавлении новостей модулем через крон?', '', makeCheckBox('push_notifications[tg_cron_modadd]', $aaparser_config['push_notifications']['tg_cron_modadd']));
showRow('Включить отправку постов в Telegram при обновлении новостей модулем через крон?', '', makeCheckBox('push_notifications[tg_cron_modupdate]', $aaparser_config['push_notifications']['tg_cron_modupdate']));
echo <<<HTML
			</table>
			<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">Постинг в Telegram при добавлении или обновлении новостей модулем работает только при помощи крон. Это сделано для того, чтобы не отправлялось одновременно 10, 20, 50 постов. Если вы используете функционал автоматического добавления (граббинга) и/или обновления новостей модулем, то не забудьте добавить задачу в крон<br><b>{$config['http_home_url']}index.php?controller=ajax&mod=anime_grabber&module=telegram_sender&key={$cron_key}</b> - время открытия ссылки выбирайте на своё усмотрение, оно же частота отправки одного поста в telegram</div>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка постинга в Telegram</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Шаблон отправки сообщения в телеграм в момент ручного добавления новости через админку', '', textareaForm(['push_notifications[addnews]', $aaparser_config['push_notifications']['addnews'], 'Введите текст']));
showRow('Шаблон отправки сообщения в телеграм в момент ручного редактирования новости через админку', '', textareaForm(['push_notifications[editnews]', $aaparser_config['push_notifications']['editnews'], 'Введите текст']));
showRow('Шаблон отправки сообщения в телеграм в момент автоматического добавления новости модулем', '', textareaForm(['push_notifications[addnews_cron]', $aaparser_config['push_notifications']['addnews_cron'], 'Введите текст']));
showRow('Шаблон отправки сообщения в телеграм в момент автоматического обновления новости модулем', '', textareaForm(['push_notifications[editnews_cron]', $aaparser_config['push_notifications']['editnews_cron'], 'Введите текст']));
echo <<<HTML
			</table>
			<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">
			    В шаблонах вам доступны следующие html теги - &lt;b&gt;, &lt;strong&gt;, &lt;i&gt;, &lt;em&gt;, &lt;a&gt;, &lt;code&gt; и &lt;pre&gt;</br>
			    Так же вам доступны стандартные шаблонные теги полной новости, а именно:</br>
			    {title} - Заголовок новости</br>
			    {title limit="x"} - Выводится урезанный до X количества символов, заголовок новости</br>
			    {short-story} - Краткая версия новости</br>
			    {short-story limit="x"} - Выводит только текст краткой новости без HTML форматирования, при этом сам текст публикации сокращается до указанного X количества символов</br>
			    {full-story} - Полная версия</br>
			    {full-story limit="x"} - Выводит только текст полной новости без HTML форматирования, при этом сам текст публикации сокращается до указанного X количества символов</br>
			    {full_link} - Для вывода полного постоянного адреса новости</br>
			    {category} - Список категорий, к которым относится статья</br>
			    [xfvalue_x] - Значение дополнительного поля "x", где "x" название дополнительного поля</br>
			    [xfgiven_x] [xfvalue_x] [/xfgiven_x] - Выводится дополнительное поле "x", если поле не пустое</br>
			    [xfnotgiven_X] [/xfnotgiven_X] - Выводят текст указанный в них если дополнительное поле не было задано при публикации новости, где "х" это имя дополнительного поля</br>
			    [xfvalue_X limit="X2"] - Выводит текст дополнительного поля, при этом сам текст сокращается до указанного X2 количества символов. При этом сокращение текста происходит до последнего логического слова. Например [xfvalue_test limit="50"] выведет только первые 50 символов значения дополнительного поля c именем test</br>
			    [ifxfvalue tagname="tagvalue"] Текст [/ifxfvalue] - Выводят текст заключенный в них, если значение дополнительного поля совпадает с указанным. Где tagname это имя дополнительного поля, а tagvalue это его значение. Значения tagvalue можно перечислять через запятую</br>
			    [ifxfvalue tagname!="tagvalue"] Текст [/ifxfvalue] - Выводят текст заключенный в них, если значение поля не совпадает с указанным. Где tagname это имя дополнительного поля, а tagvalue это его значение. Значения tagvalue можно перечислять через запятую</br>
			    Также для удобства оформления вам доступны не стандартные теги:</br>
			    [xfvalue_x_hashtag] - Значение дополнительного поля "x", где "x" название дополнительного поля, при этом вывод будет осуществляться в виде хештегов, с возможностью навигации по ним при клике</br>
			    {title_tag} - Заголовок новости в виде хештегов, с возможностью навигации по ним при клике</br>
			    {main_category_link} - Выводит ссылку на первую по списку категорию, основную</br>
			    {category_hashtag} - Список категорий, к которым относится статья в виде хештегов, с возможностью навигации по ним при клике</br>
			    [button=x]y[/button] - Добавляет в конец поста фирменную кнопку, где x - ссылка на страницу, y - текст ссылки. Например [button={full_link}]Смотреть онлайн[/button]. Таких ссылок может быть несколько. Для наглядности выглядят они так - <a href="https://prnt.sc/b9PGtmKDVxir" target="_blank">скрин</a></br>
			    При условии что кодировка вашей бд utf8_mb4 вы можете в шаблонах использовать emoji для визуального оформления</br>
			    Вот наглядный пример оформления шаблона отправки в тг с результатом. Мой шаблон:
			    <textarea style="width:100%;height:300px;" disabled="">
🆕Новинка на сайте:
[ifxfvalue video_type="сериал"]Сериал[/ifxfvalue][ifxfvalue video_type="фильм"]Фильм[/ifxfvalue] «{title}»
[xfgiven_year]🎥Год: [xfvalue_year_hashtag]год[/xfgiven_year]
[xfgiven_countries]🌏Страна: [xfvalue_countries_hashtag][/xfgiven_countries]
🎞️Жанры: {category_hashtag}
[xfgiven_director]🧑‍💻Режиссёр: [xfvalue_director][/xfgiven_director]
[xfgiven_actor]🤵Актёры: [xfvalue_actor][/xfgiven_actor]
[xfgiven_last_episode]🔥Добавлено: [xfvalue_last_season] сезон [xfvalue_last_episode] серия[/xfgiven_last_episode]

📃Сюжет: {short-story limit="150"}...
[button={full_link}]Смотреть [ifxfvalue video_type="сериал"]сериал[/ifxfvalue][ifxfvalue video_type="фильм"]фильм[/ifxfvalue] ▶️[/button]
[button={main_category_link}]Все [ifxfvalue video_type="сериал"]сериалы[/ifxfvalue][ifxfvalue video_type="фильм"]фильмы[/ifxfvalue] 🎥[/button]
			    </textarea></br>
			    Результат - <a href="https://prnt.sc/VBg9iuOwYmhj" target="_blank">скрин</a>
			</div>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Инструкция по созданию бота</div>
		<div class="table-responsive">
			<table class="table table-striped">
                <tbody>
                    <tr>
                        <td style="width:100%">
                            1. Создаём канал, если у вас его ещё нет, задаем ему имя-ссылку, @nazvaniekanala.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            2. В поиске telegram ищем бота <b>@BotFather</b> и входим в диалог с ним</a>.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            3. Отправляем боту команду <b>/newbot</b>.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            4. В ответ видим текст "Alright, a new bot. How are we going to call it? Please choose a name for your bot.". Теперь придумайте и отправьте ему имя бота. Например имя вашего сайта.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            5. В ответ видим текст "Good. Now let's choose a username for your bot. It must end in `bot`. Like this, for example: TetrisBot or tetris_bot.". Теперь придумайте и отправьте ему логин бота. Обращаю ваше внимание на обязательное условие, логин бота должно заканчиваться на _bot. Например myfirst_bot.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            6. Бот создан, в ответ вам прийдет ваш бот токен (текст ниже строчки "Use this token to access the HTTP API"). Скопируйте его и вставьте в админке, в поле "Bot token". Также в поле "Имя канала с символом @" вставьте имя-ссылку вашего канала, например @nazvaniekanala. Внесите остальные настройки в модуле.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            7. Теперь нам нужно добавить бота в канал с правами администратора канала. Заходим в канал, нажимаем на три точки, edit. Нажимаем на Administrators, далее на +, в поле поиска вводим имя бота, которого создали в пунктах 4 и 5. Жмем на галочку. Видим что теперь администраторы это вы и ваш бот.
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%">
                            8. Готово. Для проверки работы постинга в телеграм канал активируйте пункт <b>"Включить отправку постов в Telegram при ручном редактировании новостей через админку?"</b>, отключите пункт <b>"Отправлять посты в телеграм при помощи крон?"</b>, откройте редактирование любой опубликованной новости, отметьте внизу галочку <b>"Отправить пост в Telegram?"</b> и сохраните новость. При условии правильных настроек вы увидите пост в вашем телеграм канале.
                        </td>
                    </tr>
                </tbody>
			</table>
		</div>
		</div>
	</div>
HTML;
?>