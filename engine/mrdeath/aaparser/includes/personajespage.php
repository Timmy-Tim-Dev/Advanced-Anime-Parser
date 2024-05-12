<?php

$personajes_cache_size = convert_bytes(dir_size(ENGINE_DIR."/mrdeath/aaparser/cache/personas_characters/"));

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
echo <<<HTML
		</table>
		
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component anime-settings">
		1. Если вы хотите выводить главных персонажей аниме, то создаем два файла в Вашем шаблоне под названием <b>main_characters_block.tpl</b> и <b>main_characters_info.tpl</b>.
		<br/>Теги которые работают в файле шаблона <b>main_characters_block.tpl</b>:
		<hr/>
		<b>[main-characters-list]</b>Выводит содержимое если есть хотя-бы один главный персонаж<b>[/main-characters-list]</b>
		<br/><b>{main-characters-list}</b> - Выводит в место вставки тега главных персонажей согласно оформлению в файле <b>main_characters_info.tpl</b>
		<hr/>
		Теги которые работают в файле шаблона <b>main_characters_info.tpl</b>:
		<hr/>
		<br/><b>[characters_id]</b>Выводит содержимое если доступно id Shikimori персонажа<b>[/characters_id]</b>
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
		<br/><b>[not_characters_id]</b>Выводит содержимое если id Shikimori персонажа отсутствует<b>[/not_characters_id]</b>
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
		<textarea style="width:100%;height:150px;" disabled>
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
               <img src="{characters_image_orig}" width="45" height="70" alt="{characters_name_rus}" title="{characters_name_rus}">
            </div>
            <div class="cvcontent"> <span class="charname">{characters_name_rus}</span> <span class="charname">{characters_name_eng}</span> <span class="charrole">{characters_role_rus}</span></div>
         </div>
      </div>
   </div></textarea>
		<br/><i>Если вы решили использовать html оформление tpl файлов из примеров, то вот стили для оформления</i>
		<textarea style="width:100%;height:300px;" disabled>
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
.cvlist .cvitem .cvitempad .cvsubitem .cvcontent span {
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
</style></textarea>
        <hr/>
        2. В файле шаблона fullstory.tpl в нужное место где будут выведены главные персонажи вставляем тег <b>{kodik_main_characters}</b>.
		</div>
		
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component anime-settings">
		1. Если вы хотите выводить второстепенных персонажей аниме, то создаем два файла в Вашем шаблоне под названием <b>sub_characters_block.tpl</b> и <b>sub_characters_info.tpl</b>.
		<br/>Теги которые работают в файле шаблона <b>sub_characters_block.tpl</b>:
		<hr/>
		<b>[sub-characters-list]</b>Выводит содержимое если есть хотя-бы один второстепенный персонаж<b>[/sub-characters-list]</b>
		<br/><b>{sub-characters-list}</b> - Выводит в место вставки тега главных персонажей согласно оформлению в файле <b>sub_characters_info.tpl</b>
		<hr/>
		Теги которые работают в файле шаблона <b>sub_characters_info.tpl</b>:
		<hr/>
		<br/><b>[characters_id]</b>Выводит содержимое если доступно id Shikimori персонажа<b>[/characters_id]</b>
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
		<br/><b>[not_characters_id]</b>Выводит содержимое если id Shikimori персонажа отсутствует<b>[/not_characters_id]</b>
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
		<textarea style="width:100%;height:150px;" disabled>
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
               <img src="{characters_image_orig}" width="45" height="70" alt="{characters_name_rus}" title="{characters_name_rus}">
            </div>
            <div class="cvcontent"> <span class="charname">{characters_name_rus}</span> <span class="charname">{characters_name_eng}</span> <span class="charrole">{characters_role_rus}</span></div>
         </div>
      </div>
   </div></textarea>
        <hr/>
        2. В файле шаблона fullstory.tpl в нужное место где будут выведены второстепенные персонажи вставляем тег <b>{kodik_sub_characters}</b>.
		</div>
		
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component anime-settings">
		1. Если вы хотите выводить персон (актёры, режиссёры, продюссеры и тд), то создаем два файла в Вашем шаблоне под названием <b>persons_block.tpl</b> и <b>persons_info.tpl</b>.
		<br/>Теги которые работают в файле шаблона <b>persons_block.tpl</b>:
		<hr/>
		<b>[persons-list]</b>Выводит содержимое если есть хотя-бы одна персона<b>[/persons-list]</b>
		<br/><b>{persons-list}</b> - Выводит в место вставки тега персон согласно оформлению в файле <b>persons_info.tpl</b>
		<hr/>
		Теги которые работают в файле шаблона <b>persons_info.tpl</b>:
		<hr/>
		<br/><b>[persons_id]</b>Выводит содержимое если доступно id Shikimori персоны<b>[/persons_id]</b>
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
		<br/><b>[not_persons_id]</b>Выводит содержимое если id Shikimori персоны отсутствует<b>[/not_persons_id]</b>
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
		<textarea style="width:100%;height:150px;" disabled>
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
               <img src="{persons_image_orig}" width="45" height="70" alt="{persons_name_rus}" title="{persons_name_rus}">
            </div>
            <div class="cvcontent"> <span class="charname">{persons_name_rus}</span> <span class="charname">{persons_name_eng}</span> <span class="charrole">{persons_role_rus}</span></div>
         </div>
      </div>
   </div></textarea>
        <hr/>
        2. В файле шаблона fullstory.tpl в нужное место где будут выведены второстепенные персонажи вставляем тег <b>{kodik_persons}</b>.
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
		<div class="alert alert-info alert-styled-left alert-arrow-left alert-component dorama-settings">
		1. Создаем файл в Вашем шаблоне под названием <b>persons_block.tpl</b><br/>
		    <br/>Теги которые работают в этом шаблоне:
		    <hr/>
		    <b>[persons-list]</b>Выводит содержимое если есть хотя-бы один актёр<b>[/persons-list]</b>
		    <br/><b>{persons-list}</b> - Выводит в место вставки тега актёров согласно оформлению в файле <b>persons_info.tpl</b>
		    <hr/>
		<i>Примерный файл для persons_block.tpl</i>
		<textarea style="width:100%;height:150px;" disabled>
[persons-list]
<h3 class="small-title">В главных ролях</h3>
<div class="cvlist">
   {persons-list}
</div>
[/persons-list]</textarea>
		<br/><i>Если вы используете код для шаблонов с примеров, то вот стили под данный html код. <a href="https://prnt.sc/sRac4G2TnhWB" target="_blank">Скриншот как выглядит на выходе</a></i>
		<textarea style="width:100%;height:300px;" disabled>
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
.cvlist .cvitem .cvitempad .cvsubitem .cvcontent span {
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
</style></textarea>
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
	</div>
	</div>
HTML;
?>