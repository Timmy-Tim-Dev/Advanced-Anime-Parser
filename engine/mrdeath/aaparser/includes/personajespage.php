<?php

$personajes_cache_size = convert_bytes(dir_size(ENGINE_DIR."/mrdeath/aaparser/cache/personas_characters/"));
$personajes_page_cache_size = convert_bytes(dir_size(ENGINE_DIR."/mrdeath/aaparser/cache/personas_characters_page/"));

echo <<<HTML
	<div id="personajes" class="panel panel-flat" style='display:none'>
	    <div class="panel-body anime-settings" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка вывода персонажей и авторов аниме</div>
		<div class="table-responsive anime-settings">
		<table class="table table-striped">
HTML;
showRow('Включить вывод персонажей и авторов?', 'Включив, модуль будет выводить состав персонажей, главные герои, второстепенные герои, авторы аниме и другие участники аниме', makeCheckBox('persons[personas_on]', $aaparser_config_push['persons']['personas_on']));
showRow('Выводить главных персонажей?', 'Включив, модуль будет выводить главных персонажей, будет активирован тег {kodik_main_characters}', makeCheckBox('persons[main_characters]', $aaparser_config_push['persons']['main_characters']));
showRow('Лимит главных персонажей', 'Вы можете задать лимит количества главных персонажей, для отключеня лимита вставьте 0', showInput(['persons[main_characters_limit]', 'number', $aaparser_config_push['persons']['main_characters_limit']]));
showRow('Выводить второстепенных персонажей?', 'Включив, модуль будет выводить второстепенных персонажей, будет активирован тег {kodik_sub_characters}', makeCheckBox('persons[characters]', $aaparser_config_push['persons']['characters']));
showRow('Лимит второстепенных персонажей', 'Вы можете задать лимит количества второстепенных персонажей, для отключеня лимита вставьте 0', showInput(['persons[characters_limit]', 'number', $aaparser_config_push['persons']['characters_limit']]));
showRow('Выводить персон (актёры, режиссёры  и другие участники аниме)?', 'Включив, модуль будет выводить персон, будет активирован тег {kodik_persons}', makeCheckBox('persons[persons]', $aaparser_config_push['persons']['persons']));
showRow('Лимит персон', 'Вы можете задать лимит количества персон, для отключеня лимита вставьте 0', showInput(['persons[persons_limit]', 'number', $aaparser_config_push['persons']['persons_limit']]));
showRow('Кэшировать данные персонажей и авторов?', 'Включив, модуль будет кэшировать полученные данные, заметно ускоряет обработку страницы<br/><b>Настоятельно рекомендиуем использовать кэширование, значительно ускоряет</b>', makeCheckBox('persons[personas_cache]', $aaparser_config_push['persons']['personas_cache']));
showRow('Общий вес файлов кеша персон и персонажей - <span id="chars-cache-size">'.$personajes_cache_size.'</span>', 'При изменении какой либо опции из данного раздела обязательно очистите кеш', '<button onclick="clear_chars_cache(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-trash position-left"></i>Очистить кеш</button>');
showRow('Постер при отсутствий изображения', 'Укажите путь до картинки заглушки для отсутствующих постеров. <br/><b>Для корректной работы, укажите прямую ссылку до картинки</b><br/>Пример: <i>/templates/Default/dleimages/no_image.jpg</i>', showInput(['persons[default_image]', 'text', $aaparser_config_push['persons']['default_image']]));
showRow('Включить обработку страниц персонажей и авторов аниме?', 'Включить вывод обработки страниц персонажей и авторов аниме взятых из Shikimori', makeCheckBox('persons[persons_page]', $aaparser_config_push['persons']['persons_page']));
showRow('Кэшировать данные страниц персонажей и авторов?', 'Включив, модуль будет кэшировать полученные данные, заметно ускоряет обработку страницы<br/><b>Настоятельно рекомендуем использовать кэширование!</b>', makeCheckBox('persons[persons_page_cache]', $aaparser_config_push['persons']['persons_page_cache']));
showRow('Общий вес файлов страницы кеша персон и персонажей - <span id="chars-cache-size">'.$personajes_page_cache_size.'</span>', 'При изменении какой либо опции из данного раздела обязательно очистите кеш', '<button onclick="clear_page_cache(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-trash position-left"></i>Очистить кеш</button>');

echo <<<HTML
		</table>
<div class="rcol-2col anime-settings" style="margin-top:0;float:unset;">
	<div class="rcol-2col-header">
		<span>Вывод главных персонажей аниме</span>
		<div class="show-hide">Show</div>
	</div>
		<div class="rcol-2col-body" style="display: none;">
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component anime-settings">
		1. Если вы хотите выводить главных персонажей аниме, то создаем два файла в Вашем шаблоне под названием <b>main_characters_block.tpl</b> и <b>main_characters_info.tpl</b>.
		<br/>Теги которые работают в файле шаблона <b>main_characters_block.tpl</b>:
		<hr/>
		<b>[main-characters-list]</b>Выводит содержимое если есть хотя-бы один главный персонаж<b>[/main-characters-list]</b>
		<br/><b>{main-characters-list}</b> - Выводит в место вставки тега главных персонажей согласно оформлению в файле <b>main_characters_info.tpl</b>
		<hr/>
		Теги которые работают в файле шаблона <b>main_characters_info.tpl</b>:
		<hr/>
		<b>[characters_id]</b>Выводит содержимое если доступно id Shikimori персонажа<b>[/characters_id]</b>
		<br/><b>{characters_id}</b> - Выводит id Shikimori персонажа
		<br/><b>[characters_name_eng]</b>Выводит содержимое если доступно имя персонажа на английском<b>[/characters_name_eng]</b>
		<br/><b>{characters_name_eng}</b> - Выводит имя персонажа на английском
		<br/><b>[characters_name_rus]</b>Выводит содержимое если доступно имя персонажа на русском<b>[/characters_name_rus]</b>
		<br/><b>{characters_name_rus}</b> - Выводит имя персонажа на русском
		<br/><b>[characters_url]</b>Выводит содержимое если доступна ссылка на персонажа, ведущая на его страницу на Shikimori<b>[/characters_url]</b>
		<br/><b>{characters_url}</b> - Выводит ссылку на персонажа, ведущую на его страницу на Shikimori
		<br/><b>[characters_image_orig]</b>Выводит содержимое если доступно фото в оригинальном формате<b>[/characters_image_orig]</b>
		<br/><b>{characters_image_orig}</b> - Выводит фото в оригинальном формате
		<br/><b>[characters_image_prev]</b>Выводит содержимое если доступно фото в превью формате<b>[/characters_image_prev]</b>
		<br/><b>{characters_image_prev}</b> - Выводит фото в превью формате
		<br/><b>[characters_image_x96]</b>Выводит содержимое если доступно фото в x96 формате<b>[/characters_image_x96]</b>
		<br/><b>{characters_image_x96}</b> - Выводит фото в x96 формате
		<br/><b>[characters_image_x48]</b>Выводит содержимое если доступно фото в x48 формате<b>[/characters_image_x48]</b>
		<br/><b>{characters_image_x48}</b> - Выводит фото в x48 формате
		<br/><b>[characters_role_eng]</b>Выводит содержимое если доступно название роли на английском<b>[/characters_role_eng]</b>
		<br/><b>{characters_role_eng}</b> - Выводит название роли на английском
		<br/><b>[characters_role_rus]</b>Выводит содержимое если доступно название роли на русском<b>[/characters_role_rus]</b>
		<br/><b>{characters_role_rus}</b> - Выводит название роли на русском
		<hr/>
		Список обратных тегов:
		<hr/>
		<b>[not_characters_id]</b>Выводит содержимое если id Shikimori персонажа отсутствует<b>[/not_characters_id]</b>
		<br/><b>[not_characters_name_eng]</b>Выводит содержимое если имя персонажа на английском отсутствует<b>[/not_characters_name_eng]</b>
		<br/><b>[not_characters_name_rus]</b>Выводит содержимое если имя персонажа на русском отсутствует<b>[/not_characters_name_rus]</b>
		<br/><b>[not_characters_url]</b>Выводит содержимое если ссылка на персонажа, ведущая на его страницу на Shikimori отсутствует<b>[/not_characters_url]</b>
		<br/><b>[not_characters_image_orig]</b>Выводит содержимое если фото в оригинальном формате отсутствует<b>[/not_characters_image_orig]</b>
		<br/><b>[not_characters_image_prev]</b>Выводит содержимое если фото в превью формате отсутствует<b>[/not_characters_image_prev]</b>
		<br/><b>[not_characters_image_x96]</b>Выводит содержимое если фото в x96 формате отсутствует<b>[/not_characters_image_x96]</b>
		<br/><b>[not_characters_image_x48]</b>Выводит содержимое если фото в x48 формате отсутствует<b>[/not_characters_image_x48]</b>
		<br/><b>[not_characters_role_eng]</b>Выводит содержимое если название роли на английском отсутствует<b>[/not_characters_role_eng]</b>
		<br/><b>[not_characters_role_rus]</b>Выводит содержимое если название роли на русском отсутствует<b>[/not_characters_role_rus]</b>
		<hr/>
		<i>Примерный файл для main_characters_block.tpl</i>
		<textarea style="width:100%;height:130px;" disabled>
[main-characters-list]
<h3>Главные персонажи</h3>
<div class="cvlist">
   {main-characters-list}
</div>
[/main-characters-list]</textarea>
		<hr/>
		<i>Примерный файл для main_characters_info.tpl</i>
		<textarea style="width:100%;height:230px;" disabled>
<div class="cvitem">
	<div class="cvitempad">
		<div class="cvsubitem cvchar">
			<div class="cvcover">
				<a href="{site_characters_url}">
					<img src="{characters_image_orig}" width="45" height="70" alt="{characters_name_rus}" title="{characters_name_rus}">
				</a>
			</div>
			<div class="cvcontent">
				<a href="{site_characters_url}" class="charname">{characters_name_rus}</a>
				<a href="{site_characters_url}" class="charname">{characters_name_eng}</a>
				<span class="charrole">{characters_role_rus}</span>
			</div>
		</div>
	</div>
</div></textarea>
		<br/>
        <hr/>
        2. В файле шаблона fullstory.tpl в нужное место где будут выведены главные персонажи вставляем тег <b>{kodik_main_characters}</b>.
		</div>
</div></div>
</br>
<div class="rcol-2col anime-settings" style="margin-top:0;float:unset;">
	<div class="rcol-2col-header">
		<span>Вывод второстепенных персонажей аниме</span>
		<div class="show-hide">Show</div>
	</div>
		<div class="rcol-2col-body" style="display: none;">
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component anime-settings">
		1. Если вы хотите выводить второстепенных персонажей аниме, то создаем два файла в Вашем шаблоне под названием <b>sub_characters_block.tpl</b> и <b>sub_characters_info.tpl</b>.
		<br/>Теги которые работают в файле шаблона <b>sub_characters_block.tpl</b>:
		<hr/>
		<b>[sub-characters-list]</b>Выводит содержимое если есть хотя-бы один второстепенный персонаж<b>[/sub-characters-list]</b>
		<br/><b>{sub-characters-list}</b> - Выводит в место вставки тега главных персонажей согласно оформлению в файле <b>sub_characters_info.tpl</b>
		<hr/>
		Теги которые работают в файле шаблона <b>sub_characters_info.tpl</b>:
		<hr/>
		<b>[characters_id]</b>Выводит содержимое если доступно id Shikimori персонажа<b>[/characters_id]</b>
		<br/><b>{characters_id}</b> - Выводит id Shikimori персонажа
		<br/><b>[characters_name_eng]</b>Выводит содержимое если доступно имя персонажа на английском<b>[/characters_name_eng]</b>
		<br/><b>{characters_name_eng}</b> - Выводит имя персонажа на английском
		<br/><b>[characters_name_rus]</b>Выводит содержимое если доступно имя персонажа на русском<b>[/characters_name_rus]</b>
		<br/><b>{characters_name_rus}</b> - Выводит имя персонажа на русском
		<br/><b>[characters_url]</b>Выводит содержимое если доступна ссылка на персонажа, ведущая на его страницу на Shikimori<b>[/characters_url]</b>
		<br/><b>{characters_url}</b> - Выводит ссылку на персонажа, ведущую на его страницу на Shikimori
		<br/><b>[characters_image_orig]</b>Выводит содержимое если доступно фото в оригинальном формате<b>[/characters_image_orig]</b>
		<br/><b>{characters_image_orig}</b> - Выводит фото в оригинальном формате
		<br/><b>[characters_image_prev]</b>Выводит содержимое если доступно фото в превью формате<b>[/characters_image_prev]</b>
		<br/><b>{characters_image_prev}</b> - Выводит фото в превью формате
		<br/><b>[characters_image_x96]</b>Выводит содержимое если доступно фото в x96 формате<b>[/characters_image_x96]</b>
		<br/><b>{characters_image_x96}</b> - Выводит фото в x96 формате
		<br/><b>[characters_image_x48]</b>Выводит содержимое если доступно фото в x48 формате<b>[/characters_image_x48]</b>
		<br/><b>{characters_image_x48}</b> - Выводит фото в x48 формате
		<br/><b>[characters_role_eng]</b>Выводит содержимое если доступно название роли на английском<b>[/characters_role_eng]</b>
		<br/><b>{characters_role_eng}</b> - Выводит название роли на английском
		<br/><b>[characters_role_rus]</b>Выводит содержимое если доступно название роли на русском<b>[/characters_role_rus]</b>
		<br/><b>{characters_role_rus}</b> - Выводит название роли на русском
		<hr/>
		Список обратных тегов:
		<hr/>
		<b>[not_characters_id]</b>Выводит содержимое если id Shikimori персонажа отсутствует<b>[/not_characters_id]</b>
		<br/><b>[not_characters_name_eng]</b>Выводит содержимое если имя персонажа на английском отсутствует<b>[/not_characters_name_eng]</b>
		<br/><b>[not_characters_name_rus]</b>Выводит содержимое если имя персонажа на русском отсутствует<b>[/not_characters_name_rus]</b>
		<br/><b>[not_characters_url]</b>Выводит содержимое если ссылка на персонажа, ведущая на его страницу на Shikimori отсутствует<b>[/not_characters_url]</b>
		<br/><b>[not_characters_image_orig]</b>Выводит содержимое если фото в оригинальном формате отсутствует<b>[/not_characters_image_orig]</b>
		<br/><b>[not_characters_image_prev]</b>Выводит содержимое если фото в превью формате отсутствует<b>[/not_characters_image_prev]</b>
		<br/><b>[not_characters_image_x96]</b>Выводит содержимое если фото в x96 формате отсутствует<b>[/not_characters_image_x96]</b>
		<br/><b>[not_characters_image_x48]</b>Выводит содержимое если фото в x48 формате отсутствует<b>[/not_characters_image_x48]</b>
		<br/><b>[not_characters_role_eng]</b>Выводит содержимое если название роли на английском отсутствует<b>[/not_characters_role_eng]</b>
		<br/><b>[not_characters_role_rus]</b>Выводит содержимое если название роли на русском отсутствует<b>[/not_characters_role_rus]</b>
		<hr/>
		<i>Примерный файл для sub_characters_block.tpl</i>
		<textarea style="width:100%;height:130px;" disabled>
[sub-characters-list]
<h3>Второстепенные персонажи</h3>
<div class="cvlist">
   {sub-characters-list}
</div>
[/sub-characters-list]</textarea>
		<hr/>
		<i>Примерный файл для sub_characters_info.tpl</i>
		<textarea style="width:100%;height:230px;" disabled>
<div class="cvitem">
	<div class="cvitempad">
		<div class="cvsubitem cvchar">
			<div class="cvcover">
				<a href="{site_characters_url}">
					<img src="{characters_image_orig}" width="45" height="70" alt="{characters_name_rus}" title="{characters_name_rus}">
				</a>
			</div>
			<div class="cvcontent">
				<a href="{site_characters_url}" class="charname">{characters_name_rus}</a>
				<a href="{site_characters_url}" class="charname">{characters_name_eng}</a>
				<span class="charrole">{characters_role_rus}</span>
			</div>
		</div>
	</div>
</div></textarea>
        <hr/>
        2. В файле шаблона fullstory.tpl в нужное место где будут выведены второстепенные персонажи вставляем тег <b>{kodik_sub_characters}</b>.
		</div>
</div></div>
</br>
<div class="rcol-2col anime-settings" style="margin-top:0;float:unset;">
	<div class="rcol-2col-header">
		<span>Вывод авторов аниме</span>
		<div class="show-hide">Show</div>
	</div>
		<div class="rcol-2col-body" style="display: none;">
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component anime-settings">
		1. Если вы хотите выводить персон (актёры, режиссёры, продюссеры и тд), то создаем два файла в Вашем шаблоне под названием <b>persons_block.tpl</b> и <b>persons_info.tpl</b>.
		<br/>Теги которые работают в файле шаблона <b>persons_block.tpl</b>:
		<hr/>
		<b>[persons-list]</b>Выводит содержимое если есть хотя-бы одна персона<b>[/persons-list]</b>
		<br/><b>{persons-list}</b> - Выводит в место вставки тега персон согласно оформлению в файле <b>persons_info.tpl</b>
		<hr/>
		Теги которые работают в файле шаблона <b>persons_info.tpl</b>:
		<hr/>
		<b>[persons_id]</b>Выводит содержимое если доступно id Shikimori персоны<b>[/persons_id]</b>
		<br/><b>{persons_id}</b> - Выводит id Shikimori персоны
		<br/><b>[persons_name_eng]</b>Выводит содержимое если доступно имя персоны на английском<b>[/persons_name_eng]</b>
		<br/><b>{persons_name_eng}</b> - Выводит имя персоны на английском
		<br/><b>[persons_name_rus]</b>Выводит содержимое если доступно имя персоны на русском<b>[/persons_name_rus]</b>
		<br/><b>{persons_name_rus}</b> - Выводит имя персоны на русском
		<br/><b>[persons_url]</b>Выводит содержимое если доступна ссылка на персону, ведущая на его страницу на Shikimori<b>[/persons_url]</b>
		<br/><b>{persons_url}</b> - Выводит ссылку на персону, ведущую на его страницу на Shikimori
		<br/><b>[persons_image_orig]</b>Выводит содержимое если доступно фото в оригинальном формате<b>[/persons_image_orig]</b>
		<br/><b>{persons_image_orig}</b> - Выводит фото в оригинальном формате
		<br/><b>[persons_image_prev]</b>Выводит содержимое если доступно фото в превью формате<b>[/persons_image_prev]</b>
		<br/><b>{persons_image_prev}</b> - Выводит фото в превью формате
		<br/><b>[persons_image_x96]</b>Выводит содержимое если доступно фото в x96 формате<b>[/persons_image_x96]</b>
		<br/><b>{persons_image_x96}</b> - Выводит фото в x96 формате
		<br/><b>[persons_image_x48]</b>Выводит содержимое если доступно фото в x48 формате<b>[/persons_image_x48]</b>
		<br/><b>{persons_image_x48}</b> - Выводит фото в x48 формате
		<br/><b>[persons_role_eng]</b>Выводит содержимое если доступно название роли на английском<b>[/persons_role_eng]</b>
		<br/><b>{persons_role_eng}</b> - Выводит название роли на английском
		<br/><b>[persons_role_rus]</b>Выводит содержимое если доступно название роли на русском<b>[/persons_role_rus]</b>
		<br/><b>{persons_role_rus}</b> - Выводит название роли на русском
		<hr/>
		Список обратных тегов:
		<hr/>
		<b>[not_persons_id]</b>Выводит содержимое если id Shikimori персоны отсутствует<b>[/not_persons_id]</b>
		<br/><b>[not_persons_name_eng]</b>Выводит содержимое если имя персоны на английском отсутствует<b>[/not_persons_name_eng]</b>
		<br/><b>[not_persons_name_rus]</b>Выводит содержимое если имя персоны на русском отсутствует<b>[/not_persons_name_rus]</b>
		<br/><b>[not_persons_url]</b>Выводит содержимое если ссылка на персону, ведущая на его страницу на Shikimori отсутствует<b>[/not_persons_url]</b>
		<br/><b>[not_persons_image_orig]</b>Выводит содержимое если фото в оригинальном формате отсутствует<b>[/not_persons_image_orig]</b>
		<br/><b>[not_persons_image_prev]</b>Выводит содержимое если фото в превью формате отсутствует<b>[/not_persons_image_prev]</b>
		<br/><b>[not_persons_image_x96]</b>Выводит содержимое если фото в x96 формате отсутствует<b>[/not_persons_image_x96]</b>
		<br/><b>[not_persons_image_x48]</b>Выводит содержимое если фото в x48 формате отсутствует<b>[/not_persons_image_x48]</b>
		<br/><b>[not_persons_role_eng]</b>Выводит содержимое если название роли на английском отсутствует<b>[/not_persons_role_eng]</b>
		<br/><b>[not_persons_role_rus]</b>Выводит содержимое если название роли на русском отсутствует<b>[/not_persons_role_rus]</b>
		<hr/>
		<i>Примерный файл для persons_block.tpl</i>
		<textarea style="width:100%;height:130px;" disabled>
[persons-list]
<h3>Персонажи</h3>
<div class="cvlist">
   {persons-list}
</div>
[/persons-list]</textarea>
		<hr/>
		<i>Примерный файл для persons_info.tpl</i>
		<textarea style="width:100%;height:230px;" disabled>
<div class="cvitem">
	<div class="cvitempad">
		<div class="cvsubitem cvchar">
			<div class="cvcover">
				<a href="{site_persons_url}">
					<img src="{persons_image_orig}" width="45" height="70" alt="{persons_name_rus}" title="{persons_name_rus}">
				</a>
			</div>
			<div class="cvcontent">
				<a href="{site_persons_url}" class="charname">{persons_name_rus}</a>
				<a href="{site_persons_url}" class="charname">{persons_name_eng}</a>
				<span class="charrole">{persons_role_rus}</span>
			</div>
		</div>
	</div>
</div></textarea>
        <hr/>
        2. В файле шаблона fullstory.tpl в нужное место где будут выведены второстепенные персонажи вставляем тег <b>{kodik_persons}</b>.
		</div>
</div></div>
	</div>
	<br/>
<div class="rcol-2col anime-settings" style="margin-top:0;float:unset;">
	<div class="rcol-2col-header">
		<span>Вывод страницы персонажей и авторов аниме</span>
		<div class="show-hide">Show</div>
	</div>
		<div class="rcol-2col-body" style="display: none;">
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component anime-settings">
			1. Для того чтобы работали страницы персонажей и авторов аниме, необходимо включить ползунок выше.
			<br/>2. Для того чтобы попасть на страницы персонажей или авторов используйте теги <b>{site_characters_url},{site_persons_url}</b>
			<br/>3. Создайте файл по пути <b>/templates/Ваш шаблон/characters/characters.tpl и people.tpl</b>
			<br/>4. Для всех тегов есть обратный вывод,  пример: <b>[not_name]</b>Тут будет текст при отсутствий данных тега {name}<b>[/not_name]</b>
			<hr/>
			Теги которые работают в файле шаблона <b>characters.tpl</b>:
			<br/><b>{id}</b> - выводит Shikimori ID персонажа.
			<br/><b>[name]</b>Выводит содержимое если есть имя персонажа на Английском<b>[/name]</b>
			<br/><b>{name}</b>Выводит имя персонажа на Английском
			<br/><b>[russian]</b>Выводит содержимое если есть имя персонажа на Русском<b>[/russian]</b>
			<br/><b>{russian}</b>Выводит имя персонажа на Русском
			<br/><b>[altname]</b>Выводит содержимое если есть прозвище персонажа<b>[/altname]</b>
			<br/><b>{altname}</b>Выводит прозвище персонажа
			<br/><b>[japanese]</b>Выводит содержимое если есть имя персонажа на Японском<b>[/japanese]</b>
			<br/><b>{japanese}</b>Выводит имя персонажа на Японском
			<br/><b>[url]</b>Выводит содержимое если есть ссылка персонажа на Shikimori<b>[/url]</b>
			<br/><b>{url}</b>Выводит ссылку персонажа на Shikimori
			<br/><b>[description]</b>Выводит содержимое если есть описание персонажа<b>[/description]</b>
			<br/><b>{description}</b>Выводит описание персонажа
			<br/><b>[description_no_spoiler]</b>Выводит содержимое если есть описание персонажа без спойлера<b>[/description_no_spoiler]</b>
			<br/><b>{description_no_spoiler}</b>Выводит описание персонажа без спойлера
			<br/><b>[spoiler]</b>Выводит содержимое если есть спойлер в описании персонажа<b>[/spoiler]</b>
			<br/><b>{spoiler}</b>Выводит спойлер описание персонажа
			<br/><b>[image_orig]</b>Выводит содержимое если есть фото персонажа в оригинальном формате <b>[/image_orig]</b>
			<br/><b>{image_orig}</b>Выводит фото персонажа в оригинальном формате
			<br/><b>[image_prev]</b>Выводит содержимое если есть фото персонажа в превью формате <b>[/image_prev]</b>
			<br/><b>{image_prev}</b>Выводит фото персонажа в превью формате
			<br/><b>[image_x96]</b>Выводит содержимое если есть фото персонажа в x96 формате <b>[/image_x96]</b>
			<br/><b>{image_x96}</b>Выводит фото персонажа в x96 формате
			<br/><b>[image_x48]</b>Выводит содержимое если есть фото персонажа в x48 формате <b>[/image_x48]</b>
			<br/><b>{image_x48}</b>Выводит фото персонажа в x48 формате
			<br/><b>[anime-list]</b>Выводит содержимое если есть аниме в котором участвовал персонаж <b>[/anime-list]</b>
			<br/><b>{anime-list}</b>Выводит список Shikimori ID где участвовал персонаж
			<br/><b>[manga-list]</b>Выводит содержимое если есть манга в котором участвовал персонаж <b>[/manga-list]</b>
			<br/><b>{manga-list}</b>Выводит список Manga ID где участвовал персонаж
			<hr/>
			<i>Примерный файл для <b>characters.tpl</b></i>
			<textarea style="width:100%;height:200px;" disabled>
<div class="swblock">
	<div class="swtop">
		<div class="swcard">
			<div class="swimg">
				<img src="{image_orig}" alt="{name}">
			</div>
			<div class="swcardinfo">
				<div class="swcardhead">Карточка персонажа:</div>
				[name]<div class="swcardrow">Имя: <span>{name}</span></div>[/name]
				[russian]<div class="swcardrow">Имя на Русском: <span>{russian}</span></div>[/russian]
				[japanese]<div class="swcardrow">Имя на Японском: <span>{japanese}</span></div>[/japanese]
				[altname]<div class="swcardrow">Прозвище: <span>{altname}</span></div>[/altname]
				[url]<div class="swcardrow">Ссылка на источник: <span><a href="{url}">{url}</a></span></div>[/url]
			</div>
		</div>
		<div class="swabout">
			<h2>Описание</h2>
			[not_description]
			<div class="swdescr">
				У данного персонажа в данный момент нету описания
			</div>
			[/not_description]
			[description_no_spoiler]
			<div class="swdescr">
				{description_no_spoiler}
			</div>
			[/description_no_spoiler]
			[spoiler]
			<details class="swspoil">
				<summary class="swspoilbtn">
					Спойлер!
				</summary>
				<p>{spoiler}</p>
			</details>
			[/spoiler]
		</div>
	</div>
	<div class="swbot">
		[anime-list]
		<h2>Персонаж в аниме</h2>
		<div class="swcontent">
			{custom idshiki="{anime-list}" template="shortstory"}
		</div>
		[/anime-list]
	</div>
</div></textarea>
			<hr/>
			Теги которые работают в файле шаблона <b>people.tpl</b>:
			<br/><b>{id}</b> - выводит Shikimori ID деятеля.
			<br/><b>[name]</b>Выводит содержимое если есть имя деятеля на Английском<b>[/name]</b>
			<br/><b>{name}</b>Выводит имя деятеля на Английском
			<br/><b>[russian]</b>Выводит содержимое если есть имя деятеля на Русском<b>[/russian]</b>
			<br/><b>{russian}</b>Выводит имя деятеля на Русском
			<br/><b>[japanese]</b>Выводит содержимое если есть имя деятеля на Японском<b>[/japanese]</b>
			<br/><b>{japanese}</b>Выводит имя деятеля на Японском
			<br/><b>[job_title]</b>Выводит содержимое если есть должность деятеля<b>[/job_title]</b>
			<br/><b>{job_title}</b>Выводит должность деятеля
			<br/><b>[birth_on]</b>Выводит содержимое если есть день рождение деятеля<b>[/birth_on]</b>
			<br/><b>{birth_on}</b>Выводит день рождение деятеля
			<br/><b>[url]</b>Выводит содержимое если есть ссылка деятеля на Shikimori<b>[/url]</b>
			<br/><b>{url}</b>Выводит ссылку деятеля на Shikimori
			<br/><b>[website]</b>Выводит содержимое если есть вебсайт деятеля на<b>[/website]</b>
			<br/><b>{website}</b>Выводит вебсайт деятеля
			<br/><b>[image_orig]</b>Выводит содержимое если есть фото деятеля в оригинальном формате <b>[/image_orig]</b>
			<br/><b>{image_orig}</b>Выводит фото деятеля в оригинальном формате
			<br/><b>[image_prev]</b>Выводит содержимое если есть фото деятеля в превью формате <b>[/image_prev]</b>
			<br/><b>{image_prev}</b>Выводит фото деятеля в превью формате
			<br/><b>[image_x96]</b>Выводит содержимое если есть фото деятеля в x96 формате <b>[/image_x96]</b>
			<br/><b>{image_x96}</b>Выводит фото деятеля в x96 формате
			<br/><b>[image_x48]</b>Выводит содержимое если есть фото деятеля в x48 формате <b>[/image_x48]</b>
			<br/><b>{image_x48}</b>Выводит фото деятеля в x48 формате
			<br/><b>[anime-list]</b>Выводит содержимое если есть аниме в котором участвовал деятель <b>[/anime-list]</b>
			<br/><b>{anime-list}</b>Выводит список Shikimori ID где участвовал деятель
			<hr/>
			<i>Примерный файл для <b>people.tpl</b></i>
			<textarea style="width:100%;height:200px;" disabled>
<div class="swblock">
	<div class="swtop">
		<div class="swcard">
			<div class="swimg">
				<img src="{image_orig}" alt="{name}">
			</div>
			<div class="swcardinfo">
				<div class="swcardhead">Карточка деятеля:</div>
				[name]<div class="swcardrow">Имя: <span>{name}</span></div>[/name]
				[russian]<div class="swcardrow">Имя на Русском: <span>{russian}</span></div>[/russian]
				[japanese]<div class="swcardrow">Имя на Японском: <span>{japanese}</span></div>[/japanese]
				[job_title]<div class="swcardrow">Должность: <span>{job_title}</span></div>[/job_title]
				[birth_on]<div class="swcardrow">День рождение: <span>{birth_on}</span></div>[/birth_on]
				[url]<div class="swcardrow">Ссылка на источник: <span><a href="{url}">{url}</a></span></div>[/url]
			</div>
		</div>
	</div>
	<div class="swbot">
		[anime-list]
		<h2>Участвовал(а) в аниме</h2>
		<div class="swcontent">
			{custom idshiki="{anime-list}" template="shortstory"}
		</div>
		[/anime-list]
	</div>
</div></textarea>
			<hr/>
			<i>Правила для <b>apache</b></i>
			<textarea style="width:100%;height:50px;" disabled>
RewriteRule ^characters/([^/]*)(/?)+$ index.php?do=characters&type=characters&id=$1 [L]
RewriteRule ^people/([^/]*)(/?)+$ index.php?do=characters&type=people&id=$1 [L]</textarea>
			<hr/>
			<i>Правила для <b>NGINX</b></i>
			<textarea style="width:100%;height:50px;" disabled>
rewrite ^/characters/([^/]*)(/?)+$ /index.php?do=characters&type=characters&id=$1 last;
rewrite ^/people/([^/]*)(/?)+$ /index.php?do=characters&type=people&id=$1 last;</textarea>
		</div>
	</div>
</div>
	<div class="panel-body dorama-settings" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка вывода актёров дорам</div>
		<div class="table-responsive dorama-settings">
		<table class="table table-striped">
HTML;
showRow('Включить вывод актёров дорам?', 'Включив, модуль будет выводить блок с шестью актёрами в главной роли дорамы', makeCheckBox('persons[personas_on_dorama]', $aaparser_config_push['persons']['personas_on_dorama']));
showRow('Кэшировать данные блока с актёрами?', 'Включив, модуль будет кэшировать полученные данные, заметно ускоряет обработку страницы<br/><b>Настоятельно рекомендуем использовать кэширование, значительно ускоряет</b>', makeCheckBox('persons[personas_cache_dorama]', $aaparser_config_push['persons']['personas_cache_dorama']));
showRow('Общий вес файлов кеша актёров - <span id="actors-cache-size">'.$personajes_cache_size.'</span>', 'При изменении какой либо опции из данного раздела обязательно очистите кеш', '<button onclick="clear_actors_cache(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-trash position-left"></i>Очистить кеш</button>');
showRow('Постер при отсутствий изображения', 'Укажите путь до картинки заглушки для отсутствующих постеров. <br/><b>Для корректной работы, укажите прямую ссылку до картинки</b><br/>Пример: <i>/templates/Default/dleimages/no_image.jpg</i>', showInput(['persons[default_image_dorama]', 'text', $aaparser_config_push['persons']['default_image_dorama']]));
echo <<<HTML
		</table>
<div class="rcol-2col dorama-settings" style="margin-top:0;float:unset;">
	<div class="rcol-2col-header">
		<span>Вывод персонажей дорамы</span>
		<div class="show-hide">Show</div>
	</div>
		<div class="rcol-2col-body" style="display: none;">
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component dorama-settings">
		1. Создаем файл в Вашем шаблоне под названием <b>persons_block.tpl</b><br/>
		    <br/>Теги которые работают в этом шаблоне:
		    <hr/>
		    <b>[persons-list]</b>Выводит содержимое если есть хотя-бы один актёр<b>[/persons-list]</b>
		    <br/><b>{persons-list}</b> - Выводит в место вставки тега актёров согласно оформлению в файле <b>persons_info.tpl</b>
		    <hr/>
		<i>Примерный файл для persons_block.tpl</i>
		<textarea style="width:100%;height:130px;" disabled>
[persons-list]
<h3 class="small-title">В главных ролях</h3>
<div class="cvlist">
   {persons-list}
</div>
[/persons-list]</textarea>
		<br/>
        2. Создаем файл в Вашем шаблоне под названием <b>persons_info.tpl</b><br/>
		    <br/>Теги которые работают в этом шаблоне:
		    <hr/>
		    <b>[personas_image_orig]</b>Выводит содержимое если есть фото актёра<b>[/personas_image_orig]</b>
		    <br/><b>{personas_image_orig}</b> - Выводит ссылку на фото актёра
		    <br/><b>[personas_name_eng]</b>Выводит содержимое если доступно имя актёра на английском<b>[/personas_name_eng]</b>
		    <br/><b>{personas_name_eng}</b> - Выводит имя актёра на английском
		    <hr/>
		    <b>[not_personas_image_orig]</b>Выводит содержимое если фото актёра отсутствует<b>[/not_personas_image_orig]</b>
		    <br/><b>[not_personas_name_eng]</b>Выводит содержимое если имя актёра на английском отсутствует<b>[/not_personas_name_eng]</b>
		    <hr/>
		<i>Примерный файл для persons_info.tpl</i>
		<textarea style="width:100%;height:230px;" disabled>
   <div class="cvitem">
      <div class="cvitempad">
         <div class="cvsubitem cvchar">
            <div class="cvcover">
               <img src="{personas_image_orig}" width="45" height="70" alt="{personas_name_eng}" title="{personas_name_eng}">
            </div>
            <div class="cvcontent"> <span class="charname">{personas_name_eng}</span> <span class="charrole">Актёр</span></div>
         </div>
      </div>
   </div></textarea>
        3. В нужное место в fullstory.tpl вставляем тег <b>{kodik_persons_dorama}</b><br/>
		</div>
</div></div>
	</div>
	<br/>
	<div class="rcol-2col" style="margin-top:0;float:unset;">
	<div class="rcol-2col-header">
		<span>Стили</span>
		<div class="show-hide">Show</div>
	</div>
		<div class="rcol-2col-body" style="display: none;">
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component">
		<i>Если вы используете код для шаблонов с примеров, то вот стили для персонажей и авторов под данный html код. <a href="https://prnt.sc/sRac4G2TnhWB" target="_blank">Скриншот как выглядит на выходе</a></i>
		<textarea style="width:100%;height:300px;" disabled>
<!-- Стили для персонажей и авторов -->
<style>
.cvlist {
    overflow: hidden;
    margin: 10px;
}
.cvlist .cvitem {
    float: left;
    width: 25%;
}
@media only screen and (max-width: 650px) {
    .cvlist .cvitem {
        width: 50%;
    }
}
.cvlist .cvitem .cvitempad {
    overflow: hidden;
    margin: 5px;
    background: #efefef;
    border-radius: 5px;
}
.cvlist .cvitem .cvitempad .cvsubitem {
    float: left;
}
.cvlist .cvitem .cvitempad .cvsubitem.cvchar .cvcover {
    float: left;
    margin-right: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.cvlist .cvitem .cvitempad .cvsubitem .cvcover img {
    min-height: 70px;
    object-fit: cover;
}
.cvlist .cvitem .cvitempad .cvsubitem .cvcontent {
    overflow: hidden;
    padding: 5px;
}
.cvlist .cvitem .cvitempad .cvsubitem .cvcontent span, .cvlist .cvitem .cvitempad .cvsubitem .cvcontent .charname {
    display: block;
    font-size: 13px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.cvlist .cvitem .cvitempad .cvsubitem .cvcontent .charrole {
    margin-top: 3px;
    font-size: 11px;
}
</style>
</textarea>
<br/>
<i>Если вы используете код для шаблонов с примеров, то вот стили для страниц персонажей и авторов под данный html код.</i>
<textarea style="width:100%;height:300px;" disabled>
<!-- Стили для страниц персонажей и авторов -->
<style>
.swblock {
    display: flex;
    flex-direction: column;
}

.swtop {
    display: flex;
    flex-direction: column;
}

.swcard {
    display: flex;
    background: #e5e5e5;
    border-radius: 8px;
	justify-content: space-between;
	margin-bottom: 15px;
}

.swimg {
    width: 15%;
}

.swimg img {
    object-fit: cover;
    width: 100%;
    height: 100%;
    border-radius: 8px;
    margin-bottom: 10px;
}

.swcardinfo {
    width: 84%;
    padding: 15px;
}

.swcardhead {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.swcardrow {
    display: flex;
    align-items: center;
    font-weight: bold;
    font-size: 15px;
	margin-bottom: 4px;
}

.swcardrow span {
    font-weight: 100;
	margin-left: 5px;
}

.swabout {
    background: #e5e5e5;
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 15px;
}

.swspoilbtn {
    color: red;
    font-weight: bold;
    cursor: pointer;
    margin-top: 5px;
    margin-bottom: 2px;
	transition: margin 150ms ease-out;
}

.swspoil[open] .swspoilbtn ~ * {
	animation: sweep .5s ease-in-out;
}

@keyframes sweep {
	0%    {opacity: 0; margin-left: -10px}
	100%  {opacity: 1; margin-left: 0px}
}

@media screen and (max-width: 768px) {

	.swimg {
		width: 25%;
	}
	
	.swcardinfo {
		width: 74%;
		padding: 10px;
	}

}

@media screen and (max-width: 578px) {

	.swcard {
		flex-direction: column;
	}
	
	.swimg {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 10px;
	}
	
	.swimg img {
		width: 70%;
	}
	
	.swcardinfo {
		width: 100%;
		padding: 10px;
	}

}

@media screen and (max-width: 420px) {

	.swcardrow {
		margin-bottom: 10px;
		flex-direction: column;
		align-items: flex-start;
	}

}
</style>
</textarea>
		
	</div></div>
	</div>
	</div>
HTML;
?>