<?php
echo <<<HTML
	<div id="cronik" class="panel panel-flat" style="display: none">
		<div class="table-responsive">
			<table class="table table-striped">
				<tbody>
HTML;
showRow("Секретный ключ для работы крона", "Введите ключ-пароль для работы крона. Данный ключ вы будете использовать в качестве параметра key в крон-ссылках<br>Можете скопировать такой ключ: <b>".md5(time().$config['http_home_url'] . $_SESSION['user_id']['email'])."</b>", showInput(['settings[cron_key]', 'text', $aaparser_config['settings']['cron_key']]));
showRow('Защита крона от повторного запуска в секундах', 'Введите кол-во секунд, в течении заданного времени крон будет заблокирован для повторного запуска. Рекомендуется указать 10', showInput(['settings[cron_time]', 'number', $aaparser_config['settings']['cron_time']]));
echo <<<HTML
						
				</tbody>
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Крон всё в одном (мэйн)</div>
		<div class="table-responsive">
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>
						<b>Внимание!</b> Этот крон включает в себя все кроны, Вам необходимо правильно настроить модуль и затем добавить эту задачу в крон с интервалом в 1 минуту. Крон автоматически будет обновлять материалы на сайте, обновлять базу очереди материалов, а так же будет добавлять материал на сайт.
					
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&key={$cron_key}</textarea>
						<br><b>Убедитесь в том, что часовой пояс на вашем сервере настроен на московское время. В случае необходимости обратитесь в тех. поддержку хостинга с просьбой корректно настроить время.</b>
						<br><br>
						<p>Режим работы этого крона (мэйн):</p>
						<p><b>Каждый день с 01:00 до 01:04</b> - Происходит выполнение крона календаря и совместного просмотра</p>
						<p><b>Каждый час в с 5 по 9 минуту</b> - Происходит заполнение списка для будущего граббинга</p>
						<p><b>Каждый час в 20-ую минуту</b> - Происходит обновление записей</p>
						<p><b>Каждый час в 35-ую минуту</b> - Происходит обновление Категории записей</p>
						<p><b>Каждый час в 50-ую минуту</b> - Происходит обновление Дополнительных полей записей</p>
						<p class="anime-settings"><b>Каждый час в 11, 21, 31, 41, 51 минуты</b> - Происходит Добавление Анонсов (Anime)</p>
						<p><b>Каждую минуту</b> - Происходит добавление материала на сайт</p>
					</td>
					</tr>
				</tbody>
			</table>
		</div>
		<br/><br/>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Тонкая настройка крона</div>
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>
						<b>Внимание!</b> Вы можете вызвать крон-задачу по необходимости. Например, если вам не нужно обновлять материалы на сайте, а только добавлять их, вы можете сделать это, выполнив только необходимую крон-задачу.
						</td>
					</tr>
					<tr>
						<td>
						Записывать аниме в очередь на добавление:<br/>
						<b>Желательно ставить на каждые пол часа или час.</b>
						<p>Добавляет в очередь материалы для будущего парсинга, если материалов тут нет, то ничего не добавится на сайт при добавлении кроном раз в минуту.</p>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=grabbing&key={$cron_key}</textarea>
						</td>
					</tr>
					<tr>
						<td>
						Проверка выхода новых серий:<br/>
						<b>Желательно ставить раз в минуту.</b>
						<p>Если необходимо проверять обновление новостей, выход новой серии, поднятие при выходе новой серии т.д. То это то что Вам нужно.
						<br/><span style="color:red">При обновлении задействуются лишь указанные параметры во вкладке "Поднятие новостей" и обновляет описание анонсов (если включено).</span></p>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=update&key={$cron_key}</textarea>
						</td>
					</tr>
					<tr>
						<td>
						Проверка обновлений категорий:<br/>
						<b>Желательно ставить раз в миниту.</b>
						<b>Крон не работает пока Вы его не включите в настройках.</b>
						<p>Крон проверяет наличие расхождений по базе данных у первоисточника. Если у первоисточника новые данные, то крон выполнит корректно задачу.
						<br/><span style="color:red">Крон проверяет не те категории которые указаны в новостях. Проверяет по таблице anime_list в базе данных. Поэтому не рекомендую тестить его ручным образом. Данный крон не поднимает новость при обновлении.</span></p>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=category_updating&key={$cron_key}</textarea>
						</td>
					</tr>
					<tr>
						<td>
						Проверка обновлений доп полей:<br/>	
						<b>Желательно ставить раз в миниту.</b>
						<b>Крон не работает пока Вы его не включите в настройках.</b>
						<p>Крон проверяет наличие расхождений по базе данных у первоисточника. 
						<br/><span style="color:red">Данный крон не поднимает новость при обновлении.</span></p>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=xfields_updating&key={$cron_key}</textarea>
						</td>
					</tr>
					<tr>
						<td>
						Добавление новой записи:<br/>	
						<b>Желательно ставить с интервалом 1 или 2 раза в минуту.</b>
						<br/><p><span style="color:red">Обязательно необходимо чтобы у Вас были материалы в очереди, иначе ничего не добавится.</span></p>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=add&key={$cron_key}</textarea>
						</td>
					</tr>
					<tr>
						<td>
						Обновление расписания и совместного просмотра:<br/>	
						<b>Желательно ставить на каждый час</b><br/>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=other&key={$cron_key}</textarea>
						</td>
					</tr>
					<tr class="anime-settings">
						<td>
						Добавление анонсов на сайт:<br/>	
						<b>Желательно ставить на каждые 10 минут.</b><br/>
						<p>Добавляет на сайт материалы со статусом Анонс (будущие материалы)</p>
						<textarea style="width:100%;height:50px;" disabled="">{$config['http_home_url']}engine/ajax/controller.php?mod=anime_grabber&action=anons_shiki&key={$cron_key}</textarea>
						</td>
					</tr>
				</tbody>
			</table>
	</div>
HTML;
?>