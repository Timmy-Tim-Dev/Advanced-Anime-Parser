<?php
echo <<<HTML
	<div id="anonsik" class="panel panel-flat" style="display: none">
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Страница настроек парсинга Анонсов с Shikimori</div>
		<div class="table-responsive">
			<table class="table table-striped">
				<tbody>
HTML;
showRow('Включить настройки Анонсов?','Включив эту настройку Вы активируете возможность парсить Анонсы', makeCheckBox('settings_anons[anons_on]', $aaparser_config['settings_anons']['anons_on']));
showRow('Сортировка порядка','Выберите порядок сортировки парсинга данных с shikimori', makeDropDown( $anons_sort_arr_film, "settings_anons[anons_film_sort_by]", $aaparser_config['settings_anons']['anons_film_sort_by'], 'ShowOrHideMode'));
showRow('Тип добавляемых материалов','Выберите какие материалы необходимо добавлять на сайт', makeSelect( $anons_kind, "settings_anons[anons_kind]", $aaparser_config['settings_anons']['anons_kind'], 'Выберите Тип', 0));
foreach ($cat_info as $cat_arr) {
	if (intval($aaparser_config['settings_anons']['cat_id']) == $cat_arr['id']) {
		$anons_category_values[] = '<option value="'.$cat_arr['id'].'" selected>'.$cat_arr['name'].'</option>';
	}
	else {
		$anons_category_values[] = '<option value="'.$cat_arr['id'].'">'.$cat_arr['name'].'</option>';
	}
	$anons_category_values[] = $cat_arr['name'];
}
showRow('Выберите категорию', 'Выберите категорию которую необходимо добавить при добавлении материала </br><span style="color:red">При проставлении категориев, в будущем при изменении статуса аниме или же его описания, оно будет проверяться по этой категории</span>', '<select data-placeholder="Выберите Категорию" name="settings_anons[cat_id]" id="settings_anons[cat_id]" class="valuesselect" multiple style="width:100%;max-width:350px;">'.implode('', $anons_category_values).'</select>');
showRow('Опубликовать новость на сайте', '', makeCheckBox('settings_anons[publish]', $aaparser_config['settings_anons']['publish']));
showRow('На модерацию без постера', '', makeCheckBox('settings_anons[publish_image]', $aaparser_config['settings_anons']['publish_image']));
showRow('На модерацию без описания(сюжета)', '', makeCheckBox('settings_anons[publish_plot]', $aaparser_config['settings_anons']['publish_plot']));
showRow('Публиковать на главной', '', makeCheckBox('settings_anons[publish_main]', $aaparser_config['settings_anons']['publish_main']));
showRow('Разрешить рейтинг статьи', '', makeCheckBox('settings_anons[allow_rating]', $aaparser_config['settings_anons']['allow_rating']));
showRow('Разрешить комментарии', '', makeCheckBox('settings_anons[allow_comments]', $aaparser_config['settings_anons']['allow_comments']));
showRow('Включить автоматический перенос строк в редакторе bbcode?', '', makeCheckBox('settings_anons[allow_br]', $aaparser_config['settings_anons']['allow_br']));
showRow('Опубликовать новость в RSS потоке', '', makeCheckBox('settings_anons[allow_rss]', $aaparser_config['settings_anons']['allow_rss']));
showRow('Использовать в Яндекс Турбо', '', makeCheckBox('settings_anons[allow_turbo]', $aaparser_config['settings_anons']['allow_turbo']));
showRow('Использовать в Яндекс Дзен', '', makeCheckBox('settings_anons[allow_zen]', $aaparser_config['settings_anons']['allow_zen']));
showRow('Запретить индексацию страницы для поисковиков', '', makeCheckBox('settings_anons[dissalow_index]', $aaparser_config['settings_anons']['dissalow_index']));
showRow('Исключить из поиска по сайту', '', makeCheckBox('settings_anons[dissalow_search]', $aaparser_config['settings_anons']['dissalow_search']));
showRow('Обновлять описание', 'Обновление заданное или пустое описание анонса на полученную с API?<br/><span style="color:red">При обновлении проверяет наличие категории анонса и проверяет с заданное описание анонса, если оно совпадает или же оно пустое, то записывается новое описание с API</span><br/><b class="faq_find faq_id_1">Подробнее</b>', makeCheckBox('settings_anons[description_update]', $aaparser_config['settings_anons']['description_update']));
echo <<<HTML
					<tr>
						<td colspan="2">
						Заданное описание материала:<br/>
						<b>Это описание будет подтсавляться в краткое и полное содержание материала если при парсинге было пустое.</b>
						<p>Этот параметр не обязателен к заполнению</br><span style="color:red">Можно использовать теги со вкладки "Основные и доп поля" и так как анонс идёт с <b>Shikimori</b>, то крайне рекомендуется использовать именно теги <b>Shikimori</b></span></p>
						<textarea style="min-height:150px;max-height:150px;min-width:333px;max-width:100%;border: 1px solid #ddd;padding: 5px;" autocomplete="off" class="form-control" name="settings_anons[descript]">{$aaparser_config['settings_anons']['descript']}</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						Ссылка для запуска крона:<br/>
						<b>Желательно ставить на каждые 10 минут.</b>
						<p>Добавляет на сайт материалы со статусом Анонс (будущие материалы)</p>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=anons_shiki&key={$cron_key}</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						Очищение списка очереди добавленных анонсов.<br/>
						<b>Нужно запускать после удаления записей с сайта</b>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=anons_clean&key={$cron_key}</textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
HTML;
?>