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
	</div>
HTML;
?>