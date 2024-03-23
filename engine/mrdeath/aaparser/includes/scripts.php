<?php
echo <<<HTML
<script>
    ShowOrHideCatStatus({$aaparser_config['update_news']['cat_check']});
    ShowOrHideXfStatus({$aaparser_config['update_news']['xf_check']});
    ShowOrHideMode({$aaparser_config['settings']['working_mode']});
    ShowOrHidePlayer();
    ShowOrHidePush();
    ShowOrHideRooms();
	
HTML;
if ($php_version >= 74 && file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/indexing.php')) {
	echo "ShowOrHideGindexing();";
}
echo <<<HTML
	$(function() {
		$( '#needreplacer' ).val( $('#needreplacer').val().replace( /NNNNNW/g,  $('select[name="settings[next_episode_date_new]"]').val() ) );
		$('.valueselect').chosen({allow_single_deselect:true, no_results_text: 'Ничего не найдено', max_selected_options: 1});
		$('.valuesselect').chosen({allow_single_deselect:true, no_results_text: 'Ничего не найдено'});
		var oldValueSelect = $('select[name="settings[next_episode_date_new]"]').val();
		var newValueSelect = '';
		var positionSelect = 1;

		$('select[name="settings[next_episode_date_new]"').prev().children('ul').on('click', 'li', function () {
			var selectedIndex = $(this).index();
			var supselect = $('select[name="settings[next_episode_date_new]"]');

			if (positionSelect === 1) {
				newValueSelect = supselect.find('option').eq(selectedIndex).val();
				var currentVal = $('#needreplacer').val();
				var regex = new RegExp(oldValueSelect, 'g');
				var newVal = currentVal.replace(regex, newValueSelect);
				$('#needreplacer').val(newVal);
				positionSelect++;
			} else {
				oldValueSelect = newValueSelect;
				newValueSelect = supselect.find('option').eq(selectedIndex).val();
				var currentVal = $('#needreplacer').val();
				var regex = new RegExp(oldValueSelect, 'g');
				var newVal = currentVal.replace(regex, newValueSelect);
				$('#needreplacer').val(newVal);
			}
		});

		function aaparser_save_option() {
			console.log('Нажал сохранить');
			ShowLoading("");
			$('body button[type=submit]:last').css("pointer-events","none").css("background","#517fa4").html('<i class="fa fa-floppy-o position-left"></i> Загружаем');
		    if ( document.querySelector('[name="settings[kodik_api_key]"]').value == "" ) {
		        Growl.error({
					title: 'Внимание!',
					text: 'Поле с вашим api токеном от базы Kodik не может быть пустым. Заполните его'
				});
				HideLoading("");
				$('body button[type=submit]').css("pointer-events","unset").css("background","#009688").html('<i class="fa fa-floppy-o position-left"></i> Сохранить');
				return false;
		    }
		    if ( document.querySelector('[name="xfields[title]"]').value == "" ) {
		        Growl.error({
					title: 'Внимание!',
					text: 'Поле с заголовком новости не может быть пустым. Заполните его'
				});
				HideLoading("");
				$('body button[type=submit]').css("pointer-events","unset").css("background","#009688").html('<i class="fa fa-floppy-o position-left"></i> Сохранить');
				return false;
		    }
		    if ( document.querySelector('[name="xfields[alt_name]"]').value == "" ) {
		        Growl.error({
					title: 'Внимание!',
					text: 'Поле с ЧПУ URL статьи не может быть пустым. Заполните его'
				});
				HideLoading("");
				$('body button[type=submit]').css("pointer-events","unset").css("background","#009688").html('<i class="fa fa-floppy-o position-left"></i> Сохранить');
				return false;
		    }
			
			var data_form = $('form').serialize();
			$.post('/engine/ajax/controller.php?mod=aaparser_save', {data_form: data_form, action: 'options', user_hash: '{$dle_login_hash}'}, function(data) {
				data = jQuery.parseJSON(data);
				
				if (!data.success) {
					Growl.error({
						title: 'Ошибка сохранения!',
						text: 'Проверьте права доступа к файлу настроек'
					});
				} else {
					setTimeout(function () {
						$.post({
							url: "engine/ajax/controller.php",
							data: {
								mod: "adminfunction",
								action: "clearcache",
								user_hash: "{$dle_login_hash}",
								cache_areas: ["news_", "full_"]  // передача параметра массивом
							},
							success: function(data) {
								// Обработка успешного ответа (можно убрать если не нужен лог)
								console.log("Кэш был очищен");
							}
						});
					},200);
					Growl.info({
						title: 'Настройки применены!',
						text: 'Настройки модуля были успешно сохранены',
						icon: 'success'
					});
					console.log("Настройки сохранены");
				}
				HideLoading("");
				$('body button[type=submit]').css("pointer-events","unset").css("background","#009688").html('<i class="fa fa-floppy-o position-left"></i> Сохранить');
			});
			return false;
		}
		
		$('body').on('submit', 'form', function(e) {
			e.preventDefault();
			aaparser_save_option();
			return false;
		});
		if ("{$oldkey}" !== "") {
			DLEalert('Ваш крон с "{$oldkey}" изменился на "{$cron_key}". Если у вас уже был настроен крон, Вам нужно будет обновить ссылки', 'Внимание! Секретный код изменён');
		}
	});
</script>
HTML;
?>