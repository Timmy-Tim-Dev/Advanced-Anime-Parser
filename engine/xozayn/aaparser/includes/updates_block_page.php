<?php
echo <<<HTML
	<div id="updates_block" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка блока обновления сериалов</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Вести историю для блока обновления серий сериалов?', 'Если включено, будет вестись история обновления серий сериалов, которую вы сможете отобразить на главной странице сайта. Инструкция находится внизу', makeCheckBox('updates_block[enable_history]', $aaparser_config['updates_block']['enable_history'], 'ShowOrHideUpdblock'));
echo <<<HTML
            </table>
        </div>
        <div id="show-hide-updblock">
            <div class="table-responsive">
			    <table class="table table-striped">
HTML;
showRow('Вести историю в случае если в последней вышедшей серии добавлена новая озвучка?', 'Если включено, в историю будет записано каждое добавление новой озвучки в одной и той же серии, следовательно один сериал в блоке будет показан несколько раз. Если выключено, сериал будет показан в блоке лишь раз по факту обновления серии.<br><b>Будет срабатывать только если во вкладке "Поднятия новостей" активирован пункт "Поднимать в случае если в последней вышедшей серии добавлена новая озвучка?" и выбрано доп. поле "Дополнительное поле c историей добавленных озвучек последней доступной серии"</b>', makeCheckBox('updates_block[new_translation_history]', $aaparser_config['updates_block']['new_translation_history']));
showRow('За сколько дней вести историю?', 'Введите количество дней, 1 - сегодня, 2 - сегодня и вчера, и так далее', showInput(['updates_block[count_days]', 'number', $aaparser_config['updates_block']['count_days']]));
showRow('Лимит записей за день', 'Вы можете задать лимит записей истории обновлений за день. Для отключения лимита выставьте 0', showInput(['updates_block[count_history]', 'number', $aaparser_config['updates_block']['count_history']]));
echo <<<HTML
			    </table>
		    </div>
		    <div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Инструкция по выводу на сайте</div>
		    <div class="table-responsive">
			    <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td style="width:100%">
                                1. В подключённый файл стилей css добавьте следующие стили:
                                <textarea style="width:100%;height:300px;" disabled>
.card {
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, .125);
    border-radius: 2px;
    position: relative;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
}
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, .125);
    margin-bottom: 0;
    padding: .75rem 1rem;
    background-color: rgba(0, 0, 0, .03);
}
.card-header:first-child {
    border-radius: calc(2px - 1px) calc(2px - 1px) 0 0;
}
.card-title {
    margin-bottom: 0;
}
.list-group {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    flex-direction: column;
    padding-left: 0;
    margin-bottom: 0;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
}
.last-update-header {
    background-color: rgba(0, 0, 0, .15);
}
.align-items-center {
    -webkit-box-align: center !important;
    -ms-flex-align: center !important;
    align-items: center !important;
}
.di-flex {
    display: -webkit-box !important;
    display: -ms-flexbox !important;
    display: flex !important;
}
.mr-1 {
    margin-right: .25rem !important;
}
.d-none {
    display: none !important;
}
@media (min-width: 1200px) {
    .d-xl-inline {
        display: inline !important;
    }
}
.bb-dashed-1 {
    border-bottom: 1px dashed;
    color: #ff5c57;
    text-decoration: none;
    background-color: transparent;
    -webkit-text-decoration-skip: objects;
}
.bb-dashed-1:before {
    content: attr(data-effect-close);
}
.collapse {
    display: none;
}
.collapse.show {
    display: block;
}
.scroll {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    overflow: hidden;
}
.list-group-item {
    position: relative;
    display: block;
    padding: .75rem 1.25rem;
    margin-bottom: -1px;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, .125);
}
.list-group-item-action {
    width: 100%;
    color: #212529;
    text-align: inherit;
}
.list-group-item:first-child {
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
}
.cursor-pointer {
    cursor: pointer;
}
.border-left-0 {
    border-left: 0 !important;
}
.border-bottom-0 {
    border-bottom: 0 !important;
}
.border-right-0 {
    border-right: 0 !important;
}
.border-top-0 {
    border-top: 0 !important;
}
.media {
    display: flex;
    -webkit-box-align: start;
    -ms-flex-align: start;
    align-items: flex-start;
}
.w-100 {
    width: 100% !important;
}
.last-update-img {
    width: 48px;
    margin-right: .5rem !important;
}
.img-square {
    padding-bottom: 100%;
    width: 100%;
    height: 0;
    background-repeat: no-repeat;
    background-position: 50% 50%;
    background-size: cover;
    border-radius: 50%;
}
.media-body {
    min-width: 0;
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
}
.mr-auto {
    margin-right: auto !important;
}
.bg-transparent {
    background-color: transparent !important;
}
.last-update-title {
    max-height: 46px;
    overflow: hidden;
    font-weight: 600;
}
.text-right {
    text-align: right !important;
}
.text-truncate {
    -o-text-overflow: ellipsis;
    text-overflow: ellipsis;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
}
.text-gray-dark-6 {
    color: rgba(0, 0, 0, .6);
}
.media-body .season-info {
    display:none;
}
                                </textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100%">
                                2. В подключённый к шаблону файл js скриптов добавьте следующий скрипты:
                                <textarea style="width:100%;height:300px;" disabled>
function kodik_block_collapse(id, parent)
{	
    var _self = $(this);
    if ( $("#kodik_block_day_"+id).hasClass("show") ) {
        $("#kodik_block_day_"+id).removeClass('show');
        $(parent).html('Развернуть');
    }
    else {
        $("#kodik_block_day_"+id).addClass('show');
        $(parent).html('Свернуть');
    }
}
                                </textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100%">
                                3. В любом месте сайта, в файле шаблона tpl, там где будет выведен блок обновлений сериалов вставьте код:
                                <textarea style="width:100%;height:300px;" disabled>
                     [kodik_updates_block]
						<div class="anime-updates">
   							<div class="card">
      							<div class="card-header">
         							<h3 class="card-title">Обновления аниме</h3>
      							</div>
      							<div class="last-update">
         							<div class="list-group">
            							{kodik_updates_block}
         							</div>
      							</div>
   							</div>
						</div>
                     [/kodik_updates_block]
                                </textarea>
                            </td>
                        </tr>
                    </tbody>
			    </table>
		    </div>
		</div>
	</div>
HTML;
?>