<?php
echo <<<HTML
	<div id="debugger" class="panel panel-flat" style='display:none'>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Включить Дебагинг?', 'Включив активируется дебагинг системы, при использовании крона, Вам будет видны количество времени запрашиваемое на те или иные действия<br/><b style="color:red">Если у Вас включено кэширование, то при каждом редактировании этого раздела, обязательно очищайте кэш DLE<br/>Включать только для дебага, так как сайт может работать не корректно! Особенно массовое проставление!</b>', makeCheckBox('debugger[enable]', $aaparser_config['debugger']['enable'], 'ShowOrHideDebugger'));
echo <<<HTML
			</table>
		</div>
		<div id="show-hide-debugger">
		    <div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка Дебагинга</div>
		    <div class="table-responsive">
			    <table class="table table-striped">
HTML;
showRow('Включить Дебагинг запросов', 'При включении активируется вывод информации о потраченного времени на выполняемый запрос', makeCheckBox('debugger[requests]', $aaparser_config['debugger']['requests']));
showRow('Включить Дебагинг загрузок картинки', 'При включении активируется вывод информации о потраченного времени на загрузку, обработку и сохранения картинки', makeCheckBox('debugger[images]', $aaparser_config['debugger']['images']));
showRow('Включить Дебагинг доноров', 'При включении активируется вывод информации о потраченного времени на каждый этап при инициализации донора', makeCheckBox('debugger[donors]', $aaparser_config['debugger']['donors']));
showRow('Включить Дебагинг по крону (Добавление материала)', 'При включении активируется вывод информации о потраченного времени на каждый этап при добавлении материала', makeCheckBox('debugger[add_material]', $aaparser_config['debugger']['add_material']));
showRow('Включить Дебагинг по крону (Обновление материала)', 'При включении активируется вывод информации о потраченного времени на каждый этап при обновлении материала', makeCheckBox('debugger[update_material]', $aaparser_config['debugger']['update_material']));
showRow('Включить Дебагинг по крону (Обновление категориев материала)', 'При включении активируется вывод информации о потраченного времени на каждый этап при обновлении категориев материала', makeCheckBox('debugger[category_material]', $aaparser_config['debugger']['category_material']));
showRow('Включить Дебагинг по крону (Обновление доп. полей материала)', 'При включении активируется вывод информации о потраченного времени на каждый этап при обновлении доп. полей материала', makeCheckBox('debugger[xfield_material]', $aaparser_config['debugger']['xfield_material']));
showRow('Включить Дебагинг по крону (Обновление расписания и совместного просмотра)', 'При включении активируется вывод информации о потраченного времени на каждый этап при расписания и совместного просмотра', makeCheckBox('debugger[other_material]', $aaparser_config['debugger']['other_material']));
showRow('Включить Дебагинг по крону (Добавление анонса)', 'При включении активируется вывод информации о потраченного времени на каждый этап при добавлении анонсируемых материалов', makeCheckBox('debugger[anons_material]', $aaparser_config['debugger']['anons_material']));
echo <<<HTML
			    </table>
		    </div>
		</div>
	</div>
HTML;
?>