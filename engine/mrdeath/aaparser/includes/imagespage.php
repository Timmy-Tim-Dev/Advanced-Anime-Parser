<?php
echo <<<HTML
	<div id="images" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка постера</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Загружать постер к вам на сайт при создании новостей?', 'Если включено, то при создании новостей будет загружаться постер к вам на сервер', makeCheckBox('images[poster]', $aaparser_config['images']['poster']));
showRow('Загружать постер к вам на сайт при редактировании новостей?', 'Если включено, то при редактировании новостей будет принудительно загружаться постер к вам на сервер. Не рекомендую включать, ведь он может заменить уже загруженный раннее постер', makeCheckBox('images[poster_edit]', $aaparser_config['images']['poster_edit']));
showRow('Дополнительное поле "загружаемое изображение"', 'Выберите для постера дополнительное поле типа "загружаемое изображение", если у вас такое поле есть. Если у вас доп поле под постер текстовое, то на вкладке настроек доп полей используйте тег {image}', makeDropDown( $xfield_image, "images[xf_poster]", $aaparser_config['images']['xf_poster']));
showRow('Дополнительное поле постера "одна строка"', 'Выберите для постера дополнительное поле типа "одна строка", если у вас такое поле есть. <font style="color:red"><b>Не выбирайте то же поле что и выше!</b> Это необходимо лишь для массового проставления, так как по другому не узнать какое доп поле, обязательно перепроверьте корректность.</font>', makeDropDown( $xfields_list, "images[xf_poster_text]", $aaparser_config['images']['xf_poster_text']));
showRow('Максимально допустимые размеры постера для сжатия (если у вас нет доп поля "загружаемое изображение")', 'Вы можете задать размер только одной стороны, например: 200, либо можете задать размеры сразу двух сторон, например: 150x100. Если выставить 0 то сжаимать не будет', showInput(['images[poster_max_up_side]', 'text', $aaparser_config['images']['poster_max_up_side']]));
echo <<<HTML
			</table>
		</div>
				<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Массовое проставление картинок</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow( 'Активация массового проставления картинок', 'Перед тем как запустить проставление убедитесь что вы корректно настроили модуль. Обращаем ваше внимание на то, что <font style="color:red">старые картинки будут заменены на новые.</font> ', '<button type="button" class="btn bg-slate-600 btn-raised legitRipple" id="mass-update-images"><i class="fa fa-wrench position-left"></i>Запуск проставления</button>', "", "" );
showRow( 'Новостей для проставления', 'Общее кол-во полученных новостей для проставления', '<span id="news-img-count-update">0</span>', "", "" );
showRow( 'Обработано новостей', 'Кол-во новостей, которые были обработаны', '<span id="current-updated-news-img">0</span>', "", "" );
echo <<<HTML
			</table>
			<div class="update-status">
	            <div class="update-status__current" id="updated-current-img">0%</div>
	            <div class="progress progress-success">
		            <div class="bar" id="updated-bar-img" style="width: 0%;"></div>
	            </div>
	            <div class="update-status__msg" id="result-msg-update-img">Запустите проставление...После запуска не закрывайте данную страницу пока проставление не будет полностью готово!</div>
            </div>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка скриншотов</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Загружать скриншоты к вам на сайт при создании новостей?', 'Если включено, то модуль будет парсить и заливать к вам кадры в момент создания новости', makeCheckBox('images[screens]', $aaparser_config['images']['screens']));
showRow('Загружать скриншоты к вам на сайт при редактировании новостей?', 'Если включено, то модуль будет парсить и заливать к вам кадры в момент редактирования новости. Внимание, если у вас уже были загружены ранее кадры то модуль их принудительно заменит', makeCheckBox('images[screens_edit]', $aaparser_config['images']['screens_edit']));
showRow('Количество скриншотов', 'Выберите желаемое количество скриншотов для загрузки, от 1 до 5. Рекомендуемое количество - 5', makeDropDown( $screens_count, "images[screens_count]", $aaparser_config['images']['screens_count']));
showRow('Дополнительное поле "загружаемая галерея изображений"', 'Выберите для скриншотов дополнительное поле типа "загружаемая галерея изображений", если у вас такое поле есть. Если у вас доп поля под кадры текстовые, то на вкладке настроек доп полей используйте теги {kadr_x}', makeDropDown( $xfield_gallery, "images[xf_screens]", $aaparser_config['images']['xf_screens']));
showRow('Максимально допустимые размеры кадров для сжатия (если у вас нет доп поля "загружаемая галерея изображений")', 'Вы можете задать размер только одной стороны, например: 200, либо можете задать размеры сразу двух сторон, например: 150x100. Если выставить 0 то сжаимать не будет', showInput(['images[screens_max_up_side]', 'text', $aaparser_config['images']['screens_max_up_side']]));
echo <<<HTML
			
			</table>
		</div>
	</div>
HTML;
?>