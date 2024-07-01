<?php
echo <<<HTML
	<div id="gindexing" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройки Google Indexing Api</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
if ($php_version >= 74 && file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/indexing.php')) {
	// Проверяем есть ли файл GOOGLE INDEXING и версия PHP старше 7.4
	showRow('Включить ускоренную индексацию при помощи Google Indexing Api?', 'Включив, модуль будет отправлять созданные, изменённые и удалённые страницы на индексацию при помощи Google Indexing Api. Прочесть о том что это вы можете <a href="https://developers.google.com/search/apis/indexing-api/v3/quickstart?hl=ru" target="_blank">по ссылке</a>', makeCheckBox('push_notifications[google_indexing]', $aaparser_config['push_notifications']['google_indexing'], 'ShowOrHideGindexing'));
	echo <<<HTML
				</table>
			</div>
			<div id="gindexing-settings" class="panel panel-flat">
			<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Информация</div>
			<div class="table-responsive">
				<table class="table table-striped">
HTML;

	showRow('Всего отправлено URL', '', $inform);
	showRow('Отправлено URL сегодня', '', $today_limit." из 200");
	showRow('Выберите сервисный аккаунт', 'В случае достижения лимита в 200 ссылок на аккаунте вы можете переключиться на другой аккаунт, тем самым увеличив лимит', makeDropDownAlt( $aclist, "settings_gindexing[account]", $aaparser_config['settings_gindexing']['account']));

	echo <<<HTML
				</table>
			</div>
			<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Логи модуля <button onclick="ClearLogs(); return false;" type="submit" class="btn bg-teal btn-raised position-right" style="right:0;"><i class="fa fa-floppy-o position-left"></i>Очистить логи</button></div>
			<div class="table-responsive" id="logs-list">
HTML;
	if ( isset($mod_settings['logs'][0]['url']) ) {
	$logs_count = count($mod_settings['logs']);
	if ( $logs_count > 20 ) $mod_settings['logs'] = array_slice($mod_settings['logs'], 0, 20);
	echo <<<HTML
				<table class="table table-striped table-xs table-hover">
					<thead>
						<tr>
							<th class="hidden-xs hidden-sm text-center" style="width: 60px;text-align:center;">Код</th>
							<th class="hidden-xs hidden-sm text-center">Дата</th>
							<th class="hidden-xs hidden-sm text-center">Ссылка</th>
							<th class="hidden-xs hidden-sm text-center">Тип</th>
						</tr>
					</thead>
					<tbody id="logs-result">
HTML;
	$logs_list = [];
	foreach ( $mod_settings['logs'] as $log_num => $log_data ) {
		if ( $log_data ) $logs_list[] = '<tr>
							<td class="hidden-xs text-nowrap text-center">200</td>
							<td class="hidden-xs text-nowrap text-center">'.date('d.m.Y h:i', strtotime($log_data['notifyTime'])).'</td>
							<td class="cursor-pointer text-center"><a>'.$log_data['url'].'</a></td>
							<td class="hidden-xs text-nowrap text-center">'.$log_data['type'].'</td>
						</tr>';
	}
	$logs_list = implode('', $logs_list);
	echo <<<HTML
					{$logs_list}
					</tbody>
				</table>
HTML;
	}
	else {
	echo <<<HTML
				<div style="display: table;min-height:100px;width:100%;">
					<div class="text-center" style="display: table-cell;vertical-align:middle;">Список логов пустой!</div>
				</div>
HTML;
	}
	echo <<<HTML
			</div>
HTML;
	$logs_page = '';
	if ( isset($mod_settings['logs'][0]['url']) && $logs_count > 20 ) {
		$pages_count = intval($logs_count/20);
		for ($i = 1; $i <= $pages_count; $i++) {
			if ( $i == 1 ) $active = ' class="active"';
			else $active = '';
			$logs_page .= '<li'.$active.' onclick="LogsPage(\''.$i.'\', this); return(false);"><a class="legitRipple">'.$i.'</a></li>';
		}
		$logs_page = '<ul class="pagination pagination-sm mb-20" style="margin-top: 20px!important;">'.$logs_page.'</ul>';
	}
	echo <<<HTML
				{$logs_page}
			</div>
			<div id="gindexing-status-info" class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Проверка статуса индексации</div>
			<div id="gindexing-status" class="form-group panel-body">
				<label class="control-label col-sm-2">Ссылка:</label>
				<div class="col-sm-10">
					<input type="text" class="form-control width-550 position-left" name="single_link" id="single_link" value="" maxlength="250">
					<button onclick="CheckSingle(); return false;" class="btn bg-info-800 btn-sm btn-raised legitRipple position-left">Получить статус</button>
					<div id="check_single" style="width: 100%;background-color:#aaa;"></div>
				</div>
			</div>
			<div id="gindexing-mass-info" class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Массовая отправка ссылок</div>
			<div id="gindexing-mass" class="table-responsive">
				<table class="table table-striped">
HTML;

	showRow('Выберите тип действия', 'URL_UPDATED - ссылка создана или обновлена. URL_DELETED - ссылка удалена', makeDropDown( ['URL_UPDATED','URL_DELETED'], "indexing-kind", $ashdi_config['main']['acc'], '', 0));
	showtextarea('Список ссылок');


	echo <<<HTML
				</table>
			</div>
			<div id="gindexing-guide-info" class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Инструкция по регистрации и добавлению сервисных аккаунтов</div>
			<div id="gindexing-guide" class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td style="width:100%">
								1. Создаём <a href="https://console.cloud.google.com/projectselector2/iam-admin/serviceaccounts?supportedpurview=project" target="_blank">проект</a> на сайте Google Cloud Console <a href="https://prnt.sc/iD23C4Cff4Zq" target="_blank">(скриншот)</a>.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								2. Вводим название проекта, поле "Location" можно оставить пустым. Нажимаем "Create" <a href="https://prnt.sc/VUhBz-gX7rB7" target="_blank">(скриншот)</a>.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								3. Переходим в раздел "Service Accounts" и нажимаем кнопку "Create service account". <a href="https://prnt.sc/1KLD1kl1rbNK" target="_blank">(скриншот)</a>.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								4. В первой строке указываете название вашего аккаунта на латинице (например, название сайта). Во второй строке данные подтянутся благодаря тому, что вы введете в первой. Третью строку заполняете по желанию. Нажимаем кноку "Create and continue" <a href="https://prnt.sc/Kg-Wx1MS0Coj" target="_blank">(скриншот)</a>.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								5. Далее увидите окно, в котором ничего не нужно указывать, просто жмём кнопку “Done”. <a href="https://prnt.sc/EWojgYpOI7m0" target="_blank">(скриншот)</a>.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								6. Обратите внимание на Email, который был создан под проект, скопируйте его <a href="https://prnt.sc/sfxkfdZFsZ-C" target="_blank">(скриншот)</a>. Этот email нам понадобится в пункте 8. Нажимаем на "три точки" и кликаем "Manage keys" <a href="https://prnt.sc/jyFUMZE33i1d" target="_blank">(скриншот)</a>. 
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								7. Кликаем на кнопку "Add key", выбираем "Create new key" <a href="https://prnt.sc/U2WF-HrTVNmo" target="_blank">(скриншот)</a>. Во всплывающем окне выбираем тип ключа "Json" и кликаем "Create" <a href="https://prnt.sc/szLPyfEVTBeK" target="_blank">(скриншот)</a>. Браузер предложит задать имя файла и сохранить его на пк. Для удобства называем наш первый файл 1.json. Скачанный файл загружаем при помощи ftp в папку /engine/mrdeath/aaparser/google_indexing/accounts/ на вашем сайте.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								8. Теперь нам необходимо в Google Search Console добавить электронный адрес сервисного аккаунта, который мы создали на предыдущих этапах, в качестве владельца. Для этого заходим в <a href="https://search.google.com/search-console/welcome?utm_source=about-page" target="_blank">Google Search Console</a> на аккаунт связанный с вашим сайтом.  Нажимаем "Настройки", кликаем "Пользователи и разрешения" <a href="https://prnt.sc/mdNfl_5gUNoU" target="_blank">(скриншот)</a>. Нажимаем кнопку "Добавить пользователя", во всплывающем окне вводим email сервисного аккаунта из пункта 6, выбираем в поле "Разрешение" опцию "Владелец" (можно выбрать опцию "Полный доступ"), кликаем "Добавить" <a href="https://prnt.sc/nGV2iJhzlw0S" target="_blank">(скриншот)</a>.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								9. Теперь нужно включить Indexing API в нашем проекте. Переходим по <a href="https://console.developers.google.com/apis/api/indexing.googleapis.com/overview?pli=1" target="_blank">ссылке</a> и выбираем сервисный аккаунт, включая API <a href="https://prnt.sc/AvjCgidnOQdA" target="_blank">(скриншот)</a>.
							</td>
						</tr>
						<tr>
							<td style="width:100%">
								10. Настройка завершена. В админке модуля выбираем в поле "Выберите сервисный аккаунт" файл аккаунта, который мы загрузили в пункте 7. Модуль будет отправлять ссылки на индексацию в гугл при помощи этого аккаунта. Лимит обращений к апи составляет 200 запросов в день. Следить за лимитом можно в админке, обратите внимание на поле "Отправлено URL сегодня". Если по какой то причине для вас мало 200 запросов в день и вы исчерпываете лимит, достаточно создать ещё один сервисный аккаунт повторив пункты 1-9, при этом в 7 пункте сохраняемый файл называем 2.json, третий файл назваем 3.json и тд. Количество аккаунтов не ограничено, для создания используем любую гугл почту, не обязательно связанную с вашим сайтом. 
							</td>
						</tr>
					</tbody>
HTML;
} else {
	
	if ($php_version < 74) showRow('Не поддерживаемая версия PHP', 'Модуль не поддерживается версией PHP ниже 7.4', '(Ваша версия php '.PHP_VERSION.')');
	else {
		if ( file_exists(ENGINE_DIR.'/xoopw/indexing/init.php') ) {
			showRow('У Вас имеется модуль Google Indexing от Xoo.Pw', 'Необходимо его выключить в плагигах если собираетесь пользоваться Google Indexing от этого модуля и удалить папку /engine/xoopw/', '<a href="'. $config["http_home_url"] .'admin?mod=plugins" class="btn bg-slate-600 btn-raised legitRipple">Плагины</a>');
		}
		echo <<<HTML
			<tr>
				<td class="col-xs-10 col-sm-6 col-md-10 ">
					<h6><b>Для того чтобы включить модуль Вам необходимо:</b></h6>
					<span class="note large">Скачать файлы по ссылке и закинуть в папку "{$config['http_home_url']}mrdeath/aaparser/" содержимое Архива GOOGLE INDEXING</span>
				</td>
				<td class="col-xs-2 col-sm-6 col-md-2">
					<a href="https://storage.kodik.biz/files/advanced-anime-parser/google-indexing-1.0.0.zip" class="btn bg-slate-600 btn-raised legitRipple">Скачать с базы</a>
				</td>
			</tr>
			<tr>
				<td class="col-xs-10 col-sm-6 col-md-10 ">
					<h6><b>Если удалили модуль:</b></h6>
					<span class="note large">Если по какой-то причине Вы удалили Google Indexing, не забудьте затем сохранить настройки для перезаписи состояния модуля</span>
				</td>
			</tr>

HTML;
	}
}
echo <<<HTML
			</table>
		</div>
	</div>	
HTML;
?>