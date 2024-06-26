<?php
echo <<<HTML
<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#option_menu"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="option_menu">
		<ul class="nav navbar-nav">
			<li class="active"><a onclick="ChangeOption(this, 'settings');" class="tip" title="Основные настройки модуля"><i class="fa fa-cog"></i> Основные настройки</a></li>
			<li><a onclick="ChangeOption(this, 'grabbing');" class="tip" title="Настройки граббинга"><i class="fa fa-cogs"></i> Настройки граббинга</a></li>
			<li><a onclick="ChangeOption(this, 'updates');" class="tip" title="Настройки поднятия новостей"><i class="fa fa-rss"></i> Поднятие новостей</a></li>
			<li><a onclick="ChangeOption(this, 'xfields');" class="tip" title="Настройка проставления основных и доп полей"><i class="fa fa-file-text-o"></i> Основные и доп поля</a></li>
			<li><a onclick="ChangeOption(this, 'categories');" class="tip" title="Настройка проставления категорий"><i class="fa fa-tasks"></i> Категории</a></li>
			<li><a onclick="ChangeOption(this, 'images');" class="tip" title="Настройка изображений"><i class="fa fa-image"></i> Изображения</a></li>
			<li><a onclick="ChangeOption(this, 'update_news');" class="tip" title="Настройки обновления новостей"><i class="fa fa-spinner"></i> Обновление новостей</a></li>
			<li><a onclick="ChangeOption(this, 'integration');" class="tip" title="Настройка интеграции с другими модулями"><i class="fa fa-plug"></i> Интеграция</a></li>
			<li><a onclick="ChangeOption(this, 'cronik');" class="tip" title="Тонкая настройка планировщика"><i class="fa fa-link"></i> Настройка планировщика</a></li>
			<li><a onclick="ChangeOption(this, 'anonsik');" class="tip anime-settings" title="Настройка парсинга Анонсов с Shikimori"><i class="fa fa-leaf"></i> Настройка Анонса</a></li>
			<li><a onclick="ChangeOption(this, 'modules');" class="tip" title="Раздел с дополнительными модулями"><i class="fa fa-puzzle-piece"></i> Модули</a></li>
			<li><a onclick="ChangeOption(this, 'faq');" class="tip" title="FAQ"><i class="fa fa-info"></i> FAQ</a></li>
		</ul>
	</div>
</div>
HTML;
?>