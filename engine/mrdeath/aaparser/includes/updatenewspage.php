<?php
echo <<<HTML
	<div id="update_news" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка обновления категорий</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Замена категорий', 'Выберите нужный вам пункт', makeDropDown($cat_status_upd, 'update_news[cat_check]', $aaparser_config['update_news']['cat_check'], 'ShowOrHideCatStatus'));
echo <<<HTML
			</table>
            <div class="alert alert-info alert-styled-left alert-arrow-left alert-component" id="cat_check_status">Вы выбрали пункт - <b>обновлять категории со статусами</b><br>Убедитесь что на вкладке настроек категорий вы корректно выбрали категории, соответствующие статусам - Онгоинг, Завершённые и Анонсы. Важно использование всех трёх. Модуль сам следит за изменением статуса и будет переназначать вам категории.</div>
            <div class="alert alert-info alert-styled-left alert-arrow-left alert-component" id="cat_check_all">Вы выбрали пункт - <b>полная замена всех категорий</b><br>Убедитесь что на вкладке настроек категорий вы корректно задали теги соответствующие всем вашим категориям. Модуль пройдётся по каждому аниме и полностью перезапишет список категорий в них. Данную опцию полезно включать тогда, когда вы хотите изменить структуру категорий на уже работающем сайте, например создали новые категории и хотите добавить их к уже имеющимся.<br>Если вы только недавно установили модуль себе на сайт дождитесь того, когда он полностью свяжет базу кодика с вашими новостями на сайте. Понять это можно увидев на первой вкладке надпись "База обработана".<br>Нажмите данную кнопку один раз: <button onclick="update_all_cats(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-spinner position-left"></i>Крон, обнови мне категории</button><br/> <b> Внимание!</b> Если вы используете отдельные кроны, там все равно придется нажимать кнопку</div>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка обновления доп. полей</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Замена доп. полей', 'Выберите нужный вам пункт', makeDropDown($xf_fields_upd, 'update_news[xf_check]', $aaparser_config['update_news']['xf_check'], 'ShowOrHideXfStatus'));
echo <<<HTML
			</table>
            <div class="alert alert-info alert-styled-left alert-arrow-left alert-component" id="xf_check_status">Вы выбрали пункт - <b>дополнять пустые доп. поля</b><br>Убедитесь что на вкладке настроек дополнительных полей вы всё правильно настроили, каждому доп. полю присвоили соответствующий тег. Модуль пройдётся по каждому аниме и дополнит информацию только в пустых доп. полях. <br>Если вы только недавно установили модуль себе на сайт дождитесь того, когда он полностью свяжет базу кодика с вашими новостями на сайте. Понять это можно увидев на первой вкладке надпись "База обработана".<br>Постер и скриншоты в целях безопасности в данном режиме не будут загружаться. Модуль работает только с текстовыми доп. полями<br>Нажмите данную кнопку один раз: <button onclick="update_all_xfields(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-spinner position-left"></i>Крон, обнови мне доп поля</button><br/> <b> Внимание!</b> Если вы используете отдельные кроны, там все равно придется нажимать кнопку</div>
            <div class="alert alert-info alert-styled-left alert-arrow-left alert-component" id="xf_check_all">
HTML;
showRow('Не обновлять выбранные доп поля', 'Обязательно укажите доп поле с постером (обложкой) и кадрами (скриншотами, если используете), а так же те доп поля, которые вам обновлять не нужно! Все доп. поля, которых нет в списке будут стёрты и перезаписаны', makeSelect( $xfields_all_list, "update_news[not_xfields]", $aaparser_config['update_news']['not_xfields'], 'Выберите доп поля', 0));
echo <<<HTML
                <br>Вы выбрали пункт - <b>полная перезапись доп. полей. Обязательно заполните поле - какие доп поля не будут обновляться в данном режиме (выше)</b><br>Убедитесь что на вкладке настроек дополнительных полей вы всё правильно настроили, каждому доп. полю присвоили соответствующий тег. Модуль пройдётся по каждой новости и полностью перезапишет все поля не зависимо есть в них данные или нет. Будьте внимательны, данное действие необратимо. <br>Если вы только недавно установили модуль себе на сайт дождитесь того, когда он полностью свяжет базу кодика с вашими новостями на сайте. Понять это можно увидев на первой вкладке надпись "База обработана".<br>Постер и скриншоты в целях безопасности в данном режиме не будут загружаться или перезаписываться. Модуль работает только с текстовыми доп. полями<br>Нажмите данную кнопку один раз: <button onclick="update_all_xfields(); return false;" class="btn bg-danger btn-raised legitRipple"><i class="fa fa-spinner position-left"></i>Крон, обнови мне доп поля</button><br/> <b> Внимание!</b> Если вы используете отдельные кроны, там все равно придется нажимать кнопку</div>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Массовое проставление доп. полей</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow( 'Активация массового проставления данных в доп. поля', 'Перед тем как запустить проставление убедитесь что вы корректно настроили теги во вкладке "Основные и доп. поля". Обращаем ваше внимание на то, что старые данные во всех доп. полях будут заменены на новые данные. Внимание: загрузка постера и кадров а также парсинг с World-art в данном режиме отключены, проставление будет только по текстовым полям.', '<button type="button" class="btn bg-slate-600 btn-raised legitRipple" id="mass-update"><i class="fa fa-wrench position-left"></i>Запуск проставления</button>', "", "" );
showRow( 'Новостей для проставления', 'Общее кол-во полученных новостей для проставления', '<span id="news-count-update">0</span>', "", "" );
showRow( 'Обработано новостей', 'Кол-во новостей, которые были обработаны', '<span id="current-updated-news">0</span>', "", "" );
echo <<<HTML
			</table>
            <div class="update-status">
	            <div class="update-status__current" id="updated-current">0%</div>
	            <div class="progress progress-success">
		            <div class="bar" id="updated-bar" style="width: 0%;"></div>
	            </div>
	            <div class="update-status__msg" id="result-msg-update">Запустите проставление...После запуска не закрывайте данную страницу пока проставление не будет полностью готово!</div>
            </div>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Массовое проставление метатегов</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow( 'Активация массового проставления метатегов', 'Перед тем как запустить проставление убедитесь что вы корректно настроили метатеги во вкладке "Поднятие новостей". Обращаем ваше внимание на то, что старые метатеги будут заменены на новые. <br/><i>Заполняет те метатеги которые не пустые, соответсвенно если не хотите менять какой либо метатег, оставьте пустым.</i>', '<button type="button" class="btn bg-slate-600 btn-raised legitRipple" id="mass-update-metas"><i class="fa fa-wrench position-left"></i>Запуск проставления</button>', "", "" );
showRow( 'Новостей для проставления', 'Общее кол-во полученных новостей для проставления', '<span id="news-metas-count-update">0</span>', "", "" );
showRow( 'Обработано новостей', 'Кол-во новостей, которые были обработаны', '<span id="current-updated-news-metas">0</span>', "", "" );
echo <<<HTML
			</table>
			<div class="update-status">
	            <div class="update-status__current" id="updated-current-metas">0%</div>
	            <div class="progress progress-success">
		            <div class="bar" id="updated-bar-metas" style="width: 0%;"></div>
	            </div>
	            <div class="update-status__msg" id="result-msg-update-metas">Запустите проставление...После запуска не закрывайте данную страницу пока проставление не будет полностью готово!</div>
            </div>
		</div>
	</div>
HTML;
?>