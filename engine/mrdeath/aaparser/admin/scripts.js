function hideElement(id) {
	const element = document.getElementById(id);
	if (element) element.style.display = 'none';
}

function ChangeOption(obj, selectedOption) {
    $('#option_menu li').removeClass('active');
    $(obj).parent().addClass('active');
    const sections = [
        'settings', 'grabbing', 'updates', 'update_news', 'integration', 
        'xfields', 'categories', 'images', 'cronik', 'anonsik', 'faq', 'modules'
    ];
    sections.forEach(section => hideElement(section));
    const selectedElement = document.getElementById(selectedOption);
    if (selectedElement) selectedElement.style.display = '';
    return false;
}

function ChangeOptionModules(obj, selectedOption) {
    $('#option_menu_modules li').removeClass('active');
    $(obj).parent().addClass('active');
    const sections = [
        'player', 'calendar', 'updates_block', 'push', 'rooms',
        'gindexing', 'tgposting', 'personajes'
    ];
    sections.forEach(section => hideElement(section));
    const selectedElement = document.getElementById(selectedOption);
    if (selectedElement) selectedElement.style.display = '';
    return false;
}


$(document).ready(function() {
    $( "#connect-base" ).click(function() {
		$.ajax({
			url: '/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear',
			data: {action: "connect_base_get", user_hash: dle_login_hash},
			response: 'json',
			success: function (data) {
				getIdNewsFromBase(data);
			}
		});		
	});
	// Массовое обновление доп полей
	$( "#mass-update" ).click(function() {
	    DLEconfirm("Данное действие необратимо. Сделайте резервную копию базы данных. Вы уверены что хотите запустить массовое проставление данных в доп. поля?", "Подтвердите действие", function YesImReady() {
		    $.ajax({
			    url: '/engine/ajax/controller.php?mod=anime_grabber&module=kodik_mass_update',
			    data: {action: "update_news_get", user_hash: dle_login_hash},
			    response: 'json',
			    success: function (data) {
				    DoNewsUpdate(data);
			    }
		    });
	    });
	});
	// Массовое обновление картинок
	$( "#mass-update-images" ).click(function() {
	    DLEconfirm("Данное действие необратимо. Сделайте резервную копию базы данных. Вы уверены что хотите запустить массовое проставление данных в доп. поля?", "Подтвердите действие", function YesImReady() {
		    $.ajax({
			    url: '/engine/ajax/controller.php?mod=anime_grabber&module=kodik_mass_update',
			    data: {action: "update_news_get", user_hash: dle_login_hash},
			    response: 'json',
			    success: function (data) {
				    DoNewsUpdateImages(data);
			    }
		    });
	    });
	});
	// Массовое обновление метатегов
	$( "#mass-update-metas" ).click(function() {
	    DLEconfirm("Данное действие необратимо. Сделайте резервную копию базы данных. Вы уверены что хотите запустить массовое проставление данных в доп. поля?", "Подтвердите действие", function YesImReady() {
		    $.ajax({
			    url: '/engine/ajax/controller.php?mod=anime_grabber&module=kodik_mass_update',
			    data: {action: "update_news_get", user_hash: dle_login_hash},
			    response: 'json',
			    success: function (data) {
				    DoNewsUpdateMetas(data);
			    }
		    });
	    });
	});
});

function senddata(ind, list_news, current_upd, current) {
	var temp = list_news[ind];
	all_news = list_news.length;

	$.ajax({		
		url: '/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear',
		data: {'newsid': temp['id'], 'shikiid': temp['shikimori_id'], 'mdlid': temp['mdl_id'], action: "connect_base", user_hash: dle_login_hash},
		response: 'text',
	}).then(function(result){
		var good = true;
		if (result != 'error') {
			try {
				result_list = JSON.parse(result);
				current_upd++;
				$('#current-update').html(current_upd);
				$('#result-msg').prepend('NewsID: ' + result_list['news_id'] + ' - ' + result_list['status'] + '<br/>');
			} catch (error) {
				good = false;
			}
		} else {
			$('#result-msg').prepend('Новость ' + temp['id'] + ' не была связана. Возможно в новости указан не существующий id Shikimori/MyDramaList.' + '<br/>');
		}
		if (good) {
			current++;
			current_percent = Math.ceil((current / all_news) * 100, 1);	
			$('#connect-current').html(current_percent + '%');
			$('#connect-bar').css('width', current_percent + '%');
			senddata(ind+1, list_news, current_upd, current);
		} else {
			$('#result-msg').prepend('NewsID: ' + temp['id'] + ' - <font color="red">произошла ошибка, пробуем снова</font><br/>');
			senddata(ind, list_news, current_upd, current)							
		}
	}).fail(function(){
		$('#result-msg').prepend('<font color="red">NewsID: ' + temp['id'] + ' - произошла ошибка, пробуем снова</font><br/>');
		senddata(ind, list_news, current_upd, current)
	})
}

function getIdNewsFromBase(data) {
	if (!data) 
		return false;
	var list_news = JSON.parse(data), all_news = 0, current_percent = 0 , current = 0, current_upd = 0, result_list;
	if (list_news['error']) {
		alert(list_news['error']);
		return false;
	}
	all_news = list_news.length;
	$('#news-count').html(all_news);

	senddata(0, list_news, 0 ,0);
}

function DoNewsUpdate(data) {
	if (!data) 
		return false;
	var list_news = JSON.parse(data), all_news = 0, current_percent = 0 , current = 0, current_upd = 0, result_list;
	if (list_news['error']) {
		alert(list_news['error']);
		return false;
	}
	all_news = list_news.length;
	$('#news-count-update').html(all_news);
	promise = $.when();
	$.each(list_news, function(index, temp){
		promise = promise.then(function(){
			return $.ajax({			
				url: '/engine/ajax/controller.php?mod=anime_grabber&module=kodik_mass_update',
				data: {'newsid': temp['id'], 'shikiid': temp['shikimori_id'], 'mdlid': temp['mdl_id'], action: "update_news", user_hash: dle_login_hash},
				response: 'text',
			})
		}).then(function(result){
			if (result != 'error') {
				result_list = JSON.parse(result);
				current_upd++;
				$('#current-updated-news').html(current_upd);
				document.getElementById('result-msg-update').innerHTML += '<br/>'+'NewsID: ' + result_list['news_id'] + ' - ' + result_list['status'];				
			} else {
				document.getElementById('result-msg-update').innerHTML += '<br/>'+'Данные в новости ' + temp['id'] + ' не были проставлены. Возможно в новости указан не существующий id Shikimori/MyDramaList.';
			}
			current++;
			current_percent = Math.ceil((current / all_news) * 100, 1);			
			$('#updated-current').html(current_percent + '%');
			$('#updated-bar').css('width', current_percent + '%');
		});	
	});
}

function DoNewsUpdateImages(data) {
	if (!data) 
		return false;
	var list_news = JSON.parse(data), all_news = 0, current_percent = 0 , current = 0, current_upd = 0, result_list;
	if (list_news['error']) {
		alert(list_news['error']);
		return false;
	}
	all_news = list_news.length;
	$('#news-img-count-update').html(all_news);
	promise = $.when();
	$.each(list_news, function(index, temp){
		promise = promise.then(function(){
			return $.ajax({			
				url: '/engine/ajax/controller.php?mod=anime_grabber&module=kodik_mass_update',
				data: {'newsid': temp['id'], 'shikiid': temp['shikimori_id'], 'mdlid': temp['mdl_id'], action: "update_news_img", user_hash: dle_login_hash},
				response: 'text',
			})
		}).then(function(result){
			if (result != 'error') {
				result_list = JSON.parse(result);
				current_upd++;
				$('#current-updated-news-img').html(current_upd);
				document.getElementById('result-msg-update-img').innerHTML += '<br/>'+'NewsID: ' + result_list['news_id'] + ' - ' + result_list['status'];				
			} else {
				document.getElementById('result-msg-update-img').innerHTML += '<br/>'+'Данные в новости ' + temp['id'] + ' не были проставлены. Возможно в новости указан не существующий id Shikimori/MyDramaList.';
			}
			current++;
			current_percent = Math.ceil((current / all_news) * 100, 1);			
			$('#updated-current-img').html(current_percent + '%');
			$('#updated-bar-img').css('width', current_percent + '%');
		});	
	});
}

function DoNewsUpdateMetas(data) {
	if (!data) 
		return false;
	var list_news = JSON.parse(data), all_news = 0, current_percent = 0 , current = 0, current_upd = 0, result_list;
	if (list_news['error']) {
		alert(list_news['error']);
		return false;
	}
	all_news = list_news.length;
	$('#news-metas-count-update').html(all_news);
	promise = $.when();
	$.each(list_news, function(index, temp){
		promise = promise.then(function(){
			return $.ajax({			
				url: '/engine/ajax/controller.php?mod=anime_grabber&module=kodik_mass_update',
				data: {'newsid': temp['id'], 'shikiid': temp['shikimori_id'], 'mdlid': temp['mdl_id'], action: "update_news_metas", user_hash: dle_login_hash},
				response: 'text',
			})
		}).then(function(result){
			if (result != 'error') {
				result_list = JSON.parse(result);
				current_upd++;
				$('#current-updated-news-metas').html(current_upd);
				document.getElementById('result-msg-update-metas').innerHTML += '<br/>'+'NewsID: ' + result_list['news_id'] + ' - ' + result_list['status'];				
			} else {
				document.getElementById('result-msg-update-metas').innerHTML += '<br/>'+'Данные в новости ' + temp['id'] + ' не были проставлены. Возможно в новости указан не существующий id Shikimori/MyDramaList.';
			}
			current++;
			current_percent = Math.ceil((current / all_news) * 100, 1);			
			$('#updated-current-metas').html(current_percent + '%');
			$('#updated-bar-metas').css('width', current_percent + '%');
		});	
	});
}

function update_queue() {
	DLEconfirm("Данное действие необратимо. Вы уверены что хотите очистить базу данных, обновив очередь на граббинг?", "Подтвердите действие", function YesImReady() {
		ShowLoading('');
		$.ajax({
			url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
			data:{action: "update", user_hash: dle_login_hash},
			dataType: "json",
			cache: false,
			success: function(data) {
				if ( data.status == "ok" ) {
                	Growl.info({
						title: 'Очередь обновлена',
						text: 'Теперь вам потребуется добавить аниме в очередь на граббинг'
					});
					HideLoading('');
					return false;
				}
			}
		});
	});
}

function update_all_xfields() {
	DLEconfirm("Вы уверены что хотите дать крону команду на перезапись доп полей в аниме?", "Подтвердите действие", function YesImReady() {
		ShowLoading('');
		$.ajax({
			url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
			data:{action: "update_xfields", user_hash: dle_login_hash},
			dataType: "json",
			cache: false,
			success: function(data) {
				if ( data.status == "ok" ) {
                	Growl.info({
						title: 'Команда отдана',
						text: 'Не забудьте добавить задачу в крон'
					});
					HideLoading('');
					return false;
				}
			}
		});
	});
}

function update_all_cats() {
	DLEconfirm("Вы уверены что хотите дать крону команду на полную перезапись категорий в аниме?", "Подтвердите действие", function YesImReady() {
		ShowLoading('');
		$.ajax({
			url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
			data:{action: "update_cats", user_hash: dle_login_hash},
			dataType: "json",
			cache: false,
			success: function(data) {
				if ( data.status == "ok" ) {
                	Growl.info({
						title: 'Команда отдана',
						text: 'Не забудьте добавить задачу в крон'
					});
					HideLoading('');
					return false;
				}
			}
		});
	});
}

function update_translations() {
	ShowLoading('');
	$.ajax({
		url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
		data:{action: "update_translations", user_hash: dle_login_hash},
		dataType: "json",
		cache: false,
		success: function(data) {
			if ( data.status == "ok" ) {
                Growl.info({
					title: 'Озвучки обновлены',
					text: 'Список озвучек был обновлён. Перезагрузите страницу'
				});
				HideLoading('');
				return false;
			}
			else {
			    Growl.error({
					title: data.error,
					text: data.error_desc
				});
				HideLoading('');
				return false;
			}
		}
	});
}

function update_translations_dorama() {
	ShowLoading('');
	$.ajax({
		url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
		data:{action: "update_translations_dorama", user_hash: dle_login_hash},
		dataType: "json",
		cache: false,
		success: function(data) {
			if ( data.status == "ok" ) {
                Growl.info({
					title: 'Озвучки обновлены',
					text: 'Список озвучек был обновлён. Перезагрузите страницу'
				});
				HideLoading('');
				return false;
			}
			else {
			    Growl.error({
					title: data.error,
					text: data.error_desc
				});
				HideLoading('');
				return false;
			}
		}
	});
}

function ShowOrHideTg() {
    var elements = [
        'tgposting-settings', 'tgposting-settings-area', 'tgposting-templates',
        'tgposting-templates-area', 'tgposting-info', 'tgposting-info-area'
    ];
    var action = document.getElementById("tg_on_off").checked ? 'show' : 'hide';
    elements.forEach(id => {$("#" + id)[action]();});
}


function ShowOrHideCatStatus(value) {
	if( value == '1' ) {
		$("#cat_check_status").show();
		$("#cat_check_all").hide();
	} else if(value == '2') {
		$("#cat_check_status").hide();
		$("#cat_check_all").show();
	} else {
	    $("#cat_check_status").hide();
		$("#cat_check_all").hide();
	}
}

function ShowOrHideAnime() {
	$(".dorama-settings").hide();
	$(".dorama-info").hide();
	$(".anime-settings").show();
	$(".anime-info").show();
	$(".all-info").hide();
}

function ShowOrHideDorama() {
	$(".dorama-settings").show();
	$(".dorama-info").show();
	$(".anime-settings").hide();
	$(".anime-info").hide();
	$(".all-info").hide();
}

function ShowOrHideAll() {
	$(".dorama-settings").show();
	$(".dorama-info").show();
	$(".anime-settings").show();
	$(".anime-info").show();
	$(".all-info").show();
}

function ShowOrHideMode(value) {
	
	if( value == '1' ) ShowOrHideDorama();
	else if(value == '2') ShowOrHideAll();
	else ShowOrHideAnime();
	
	setTimeout(function () {
		if( value == '1' ) ShowOrHideDorama();
		else if(value == '2') ShowOrHideAll();
		else ShowOrHideAnime();
	},300);
	
	setTimeout(function () {
		if( value == '1' ) ShowOrHideDorama();
		else if(value == '2') ShowOrHideAll();
		else ShowOrHideAnime();
	},600);
	
}

function ShowOrHideXfStatus(value) {
	if( value == '1' ) {
		$("#xf_check_status").show();
		$("#xf_check_all").hide();
	} else if(value == '2') {
		$("#xf_check_status").hide();
		$("#xf_check_all").show();
	} else {
	    $("#xf_check_status").hide();
		$("#xf_check_all").hide();
	}
}

function ShowOrHidePlayer() {
    var elements = [
        'kodik-player', 'kodik-player-settings', 'kodik-player-anime',
        'kodik-player-settings-anime', 'kodik-player-dorama', 'kodik-player-settings-dorama'
    ];
    var action = document.getElementById("player_on_off").checked ? 'show' : 'hide';
    elements.forEach(id => {$("#" + id)[action]();});
}


function ShowOrHidePush() {
    var elements = ['push-settings', 'push-settings-area', 'push-info', 'push-info-area'];
    var action = document.getElementById("push_on_off").checked ? 'show' : 'hide';
    elements.forEach(id => {$("#" + id)[action]();});
}


function ShowOrHideRooms() {
    var elements = ['rooms-settings', 'rooms-settings-area', 'rooms-info','rooms-info-area'];
    var action = document.getElementById("rooms_on_off").checked ? 'show' : 'hide';
    elements.forEach(id => {$("#" + id)[action]();});
}


function ShowOrHideGindexing() {
    var elements = [
        'gindexing-settings', 'gindexing-status-info', 'gindexing-status', 'gindexing-mass-info',
        'gindexing-mass', 'gindexing-guide-info', 'gindexing-guide'
    ];
    var action = document.getElementById("google_indexing").checked ? 'show' : 'hide';
    elements.forEach(id => {$("#" + id)[action]();});
}

function saveAcc(acc) {
	$.post('/engine/ajax/controller.php?mod=anime_grabber&module=gindexing', {acc: acc, action: 'save', user_hash: dle_login_hash}, function(data) {
	    data = jQuery.parseJSON(data);
		if (!data.success) {
			Growl.error({
				title: 'Ошибка смены сервисного аккаунта!',
				text: 'Повторите позже'
			});
		} else {
			Growl.info({
				title: 'Изменения сохранены!',
				text: 'Сервисный аккаунт изменён',
				icon: 'success'
			});
		}
	});
	return false;
}

function ClearLogs() {
    DLEconfirm( 'Вы уверены что хотите очистить логи?', 'Подтвердите', function () {
    	$.post('/engine/ajax/controller.php?mod=anime_grabber&module=gindexing', {action: 'clear_logs', user_hash: dle_login_hash}, function(data) {
	        data = jQuery.parseJSON(data);
		    if (!data.success) {
			    Growl.error({
				    title: 'Ошибка очистки логов!',
				    text: 'Повторите позже'
			    });
		    } else {
		        var new_logs = '<div style="display: table;min-height:100px;width:100%;"><div class="text-center" style="display: table-cell;vertical-align:middle;">Список логов пустой!</div></div>';
		        $('#logs-list').html(new_logs);
			    Growl.info({
				    title: 'Успешно!',
				    text: 'Логи были успешно очищены',
				    icon: 'success'
			    });
		    }
	    });
    });
	return false;
}

function CheckSingle() {
    var single_link = document.getElementById("single_link").value;
    if (single_link == "") return false;
	$.post('/engine/ajax/controller.php?mod=anime_grabber&module=gindexing', {url: single_link, action: 'check', user_hash: dle_login_hash}, function(data) {
	    data = jQuery.parseJSON(data);
		if (!data.success) {
			Growl.error({
				title: 'Ошибка!',
				text: 'Ошибка получения статуса ссылки, скорей всего она не отправлялась на индексацию в Google Indexing или ещё не была проиндексирована'
			});
		} else {
		    var result_check = '<br>Статус: '+data.result.status+'<br>Ссылка: '+data.result.link+'<br>Дата: '+data.result.date+'<br>Тип: '+data.result.type+'<br>';
		    $('#check_single').html(result_check);
		}
	});
	return false;
}

function SendMass(acc) {
    var e = document.getElementById("indexing-kind");
    var kind = e.options[e.selectedIndex].text;
    var textArea = document.getElementById("url-list").value;
    if (textArea == "") return false;
	$.post('/engine/ajax/controller.php?mod=anime_grabber&module=gindexing', {kind: kind, urls: textArea, action: 'mass', user_hash: dle_login_hash}, function(data) {
	    data = jQuery.parseJSON(data);
		if (!data.success) {
			Growl.error({
				title: 'Ошибка отправки ссылок!',
				text: 'Повторите позже'
			});
		} else {
			Growl.info({
				title: 'Успешная отправка!',
				text: 'Обновите страницу для просмотра логов',
				icon: 'success'
			});
		}
	});
	return false;
}

function LogsPage(page, el) {
    if ( el.classList.contains('active') ) {
        return false;
    }
	$.post('/engine/ajax/controller.php?mod=anime_grabber&module=gindexing', {page: page, action: 'logspage', user_hash: dle_login_hash}, function(data) {
	    data = jQuery.parseJSON(data);
	    $('#logs-result').html(data.result);
	    $("li").removeClass("active");
	    $(el).addClass( "active" );
	});
	return false;
}

function clear_player_cache() {
	DLEconfirm( 'Вы уверены что хотите очистить кеш плейлистов?', 'Подтвердите', function () {
	$.ajax({
		url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
		data:{action: "clear_player_cache", user_hash: dle_login_hash},
		dataType: "json",
		cache: false,
		success: function(data) {
			if ( data.status == "ok" ) {
			    $('#pl-cache-size').html('0 КБ');
                Growl.info({
					title: 'Успешно!',
				    text: 'Кеш был успешно очищен',
				    icon: 'success'
				});
				return false;
			}
			else {
			    Growl.error({
					title: 'Ошибка очистки кеша!',
				    text: 'Повторите позже'
				});
				return false;
			}
		}
	});
	});
}

function clear_chars_cache() {
	DLEconfirm( 'Вы уверены что хотите очистить кеш персонажей и персон?', 'Подтвердите', function () {
	$.ajax({
		url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
		data:{action: "clear_personajes_cache", user_hash: dle_login_hash},
		dataType: "json",
		cache: false,
		success: function(data) {
			if ( data.status == "ok" ) {
			    $('#chars-cache-size').html('0 КБ');
                Growl.info({
					title: 'Успешно!',
				    text: 'Кеш был успешно очищен',
				    icon: 'success'
				});
				return false;
			}
			else {
			    Growl.error({
					title: 'Ошибка очистки кеша!',
				    text: 'Повторите позже'
				});
				return false;
			}
		}
	});
	});
}

function clear_actors_cache() {
	DLEconfirm( 'Вы уверены что хотите очистить кеш актёров?', 'Подтвердите', function () {
	$.ajax({
		url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
		data:{action: "clear_personajes_cache", user_hash: dle_login_hash},
		dataType: "json",
		cache: false,
		success: function(data) {
			if ( data.status == "ok" ) {
			    $('#actors-cache-size').html('0 КБ');
                Growl.info({
					title: 'Успешно!',
				    text: 'Кеш был успешно очищен',
				    icon: 'success'
				});
				return false;
			}
			else {
			    Growl.error({
					title: 'Ошибка очистки кеша!',
				    text: 'Повторите позже'
				});
				return false;
			}
		}
	});
	});
}

function clear_page_cache() {
	DLEconfirm( 'Вы уверены что хотите очистить кеш страниц персонажей и авторов?', 'Подтвердите', function () {
	$.ajax({
		url: "/engine/ajax/controller.php?mod=anime_grabber&module=aaparser_clear",
		data:{action: "clear_page_cache", user_hash: dle_login_hash},
		dataType: "json",
		cache: false,
		success: function(data) {
			if ( data.status == "ok" ) {
			    $('#page-cache-size').html('0 КБ');
                Growl.info({
					title: 'Успешно!',
				    text: 'Кеш был успешно очищен',
				    icon: 'success'
				});
				return false;
			}
			else {
			    Growl.error({
					title: 'Ошибка очистки кеша!',
				    text: 'Повторите позже'
				});
				return false;
			}
		}
	});
	});
}

$(document).ready(function(){
	$(".rcol-2col-header").click (function(){
		$(this).next(".rcol-2col-body").stop().slideToggle(300);
		if ($(this).children('.show-hide').text() == 'Show') {
			$(this).children('.show-hide').text('Hide');
		} else {
			$(this).children('.show-hide').text('Show');
		}
	});
   
	var shiki = $('#dynamic_field_shiki').find('div').length;
	$("#add_shiki").click(function(){
		shiki++;
		$('#dynamic_field_shiki').append('<div style="display: flex;height: 40px;margin-bottom: 5px;" id="row'+shiki+'"><input type="text" autocomplete="off" style="float: right;height: 40px;" name="blacklist_shikimori[]" placeholder="id Shikimori" class="form-control"/><button type="button" name="remove_shiki" id="'+shiki+'" class="btn btn-danger btn_remove_shiki">X</button></div>');  
	});

	$(document).on('click', '.btn_remove_shiki', function(){  
	  var button_id = $(this).attr("id");     
	  $('#row'+button_id+'').remove();  
	});

	var mdl = $('#dynamic_field_mdl').find('div').length;
	$("#add_mdl").click(function(){
		mdl++;
		$('#dynamic_field_mdl').append('<div style="display: flex;height: 40px;margin-bottom: 5px;" id="rowmdl'+mdl+'"><input type="text" autocomplete="off" style="float: right;height: 40px;" name="blacklist_mdl[]" placeholder="id MyDramaList" class="form-control"/><button type="button" name="remove_mdl" id="'+mdl+'" class="btn btn-danger btn_remove_mdl">X</button></div>');  
	});

	$(document).on('click', '.btn_remove_mdl', function(){  
	  var button_id = $(this).attr("id");     
	  $('#rowmdl'+button_id+'').remove();  
	});
	
	$('.faq-quest').click(function(){
		if ($(this).parent().hasClass('faq-open')) {
			$(this).parent().removeClass('faq-open');
			$(this).parent().children('.faq-answer').slideUp(200);
		} else {
			$(this).parent().addClass('faq-open');
			$(this).parent().children('.faq-answer').slideDown(200);
		}
	});
	
	$(".faq_find").click(function () {
		ChangeOption($("ul.nav.navbar-nav li[data-original-title='FAQ']"), 'faq');
		var targetId = "#" + $(this).attr('class').split(' ')[1];
		if ($(targetId).hasClass('faq-open')) {
			$(targetId).removeClass('faq-open');
			$(targetId).children('.faq-answer').slideUp(200);
		} else {
			$(targetId).addClass('faq-open');
			$(targetId).children('.faq-answer').slideDown(200);
		}
	});
});