function ChangeOption(obj, selectedOption) {
	$('#option_menu li').removeClass('active');
	$(obj).parent().addClass('active');
	document.getElementById('settings').style.display = 'none';
	document.getElementById('grabbing').style.display = 'none';
	document.getElementById('updates').style.display = 'none';
	document.getElementById('update_news').style.display = 'none';
	document.getElementById('player').style.display = 'none';
	document.getElementById('integration').style.display = 'none';
	document.getElementById('xfields').style.display = 'none';
	document.getElementById('categories').style.display = 'none';
	document.getElementById('images').style.display = 'none';
	document.getElementById('push').style.display = 'none';
	document.getElementById('rooms').style.display = 'none';
	document.getElementById('cronik').style.display = 'none';
	document.getElementById('anonsik').style.display = 'none';
	document.getElementById('gindexing').style.display = 'none';
	document.getElementById('tgposting').style.display = 'none';
	document.getElementById(selectedOption).style.display = '';

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
    var checkbox = document.getElementById("tg_on_off");
	if( checkbox.checked === true ) {
		$("#tgposting-settings").show();
		$("#tgposting-settings-area").show();
		$("#tgposting-templates").show();
		$("#tgposting-templates-area").show();
		$("#tgposting-info").show();
		$("#tgposting-info-area").show();
	} else {
		$("#tgposting-settings").hide();
		$("#tgposting-settings-area").hide();
		$("#tgposting-templates").hide();
		$("#tgposting-templates-area").hide();
		$("#tgposting-info").hide();
		$("#tgposting-info-area").hide();
	}
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

function ShowOrHideMode(value) {
	if( value == '1' ) {
		$(".dorama-settings").show();
		$(".dorama-info").show();
		$(".anime-settings").hide();
		$(".anime-info").hide();
		$(".all-info").hide();
	} else if(value == '2') {
		$(".dorama-settings").show();
		$(".dorama-info").show();
		$(".anime-settings").show();
		$(".anime-info").show();
		$(".all-info").show();
	} else {
	    $(".dorama-settings").hide();
		$(".dorama-info").hide();
		$(".anime-settings").show();
		$(".anime-info").show();
		$(".all-info").hide();
	}
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
    var checkbox = document.getElementById("player_on_off");
	if( checkbox.checked === true ) {
		$("#kodik-player").show();
		$("#kodik-player-settings").show();
		$("#kodik-player-anime").show();
		$("#kodik-player-settings-anime").show();
		$("#kodik-player-dorama").show();
		$("#kodik-player-settings-dorama").show();
	} else {
	    $("#kodik-player").hide();
		$("#kodik-player-settings").hide();
		$("#kodik-player-anime").hide();
		$("#kodik-player-settings-anime").hide();
		$("#kodik-player-dorama").hide();
		$("#kodik-player-settings-dorama").hide();
	}
}

function ShowOrHidePush() {
    var checkbox = document.getElementById("push_on_off");
	if( checkbox.checked === true ) {
		$("#push-settings").show();
		$("#push-settings-area").show();
		$("#push-info").show();
		$("#push-info-area").show();
	} else {
		$("#push-settings").hide();
		$("#push-settings-area").hide();
		$("#push-info").hide();
		$("#push-info-area").hide();
	}
}

function ShowOrHideRooms() {
    var checkbox = document.getElementById("rooms_on_off");
	if( checkbox.checked === true ) {
		$("#rooms-settings").show();
		$("#rooms-settings-area").show();
		$("#rooms-info").show();
		$("#rooms-info-area").show();
	} else {
		$("#rooms-settings").hide();
		$("#rooms-settings-area").hide();
		$("#rooms-info").hide();
		$("#rooms-info-area").hide();
	}
}

function ShowOrHideGindexing() {
    var checkbox = document.getElementById("google_indexing");
	if( checkbox.checked === true ) {
		$("#gindexing-settings").show();
		$("#gindexing-status-info").show();
		$("#gindexing-status").show();
		$("#gindexing-mass-info").show();
		$("#gindexing-mass").show();
		$("#gindexing-guide-info").show();
		$("#gindexing-guide").show();
	} else {
		$("#gindexing-settings").hide();
		$("#gindexing-status-info").hide();
		$("#gindexing-status").hide();
		$("#gindexing-mass-info").hide();
		$("#gindexing-mass").hide();
		$("#gindexing-guide-info").hide();
		$("#gindexing-guide").hide();
	}
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


$(document).ready(function(){
    $(".rcol-2col-header").click (function(){

        $(this).next(".rcol-2col-body").stop().slideToggle(300);
        if ($(this).children('.show-hide').text() == 'Show') {
        $(this).children('.show-hide').text('Hide');
        }
        else {
        $(this).children('.show-hide').text('Show');
        }
    });
});

$(document).ready(function(){
   
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
});