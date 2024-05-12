$(document).ready(function() {
	function personas()	{
		if ( $("#main_characters_block").attr("data-sh_id") ) var sh_id = $("#main_characters_block").attr("data-sh_id");
		else if ( $("#sub_characters_block").attr("data-sh_id") ) var sh_id = $("#sub_characters_block").attr("data-sh_id");
		else if ( $("#persons_block").attr("data-sh_id") ) var sh_id = $("#persons_block").attr("data-sh_id");
		$.ajax({
			url: "/engine/ajax/controller.php?mod=anime_grabber&module=persons",
			type: "POST",
			dataType: "json",
		    cache: false,
			data: {sh_id:sh_id},
			success: function(data) {
				// Проверка на пустой ответ и рекурсивный вызов personas() в случае необходимости
				if (data.main_characters == '' && data.sub_characters == '' && data.persons == '') {
					console.log("Получен пустой ответ. Повторный запрос.");
					personas(); // Рекурсивный вызов
					return false;
				}
			    if ( data.main_characters && data.main_characters != '' && $("#main_characters_block").attr("data-sh_id") ) {
			        $("#main_characters_block").html(data.main_characters);
					console.log("Успешно загружены главные персонажи.");
			    }
			    else if ($("#main_characters_block").attr("data-sh_id")) {
			        $("#main_characters_block").remove();
			    }
			    if ( data.sub_characters && data.sub_characters != '' && $("#sub_characters_block").attr("data-sh_id") ) {
			        $("#sub_characters_block").html(data.sub_characters);
					console.log("Успешно загружены второстепенные персонажи.");
			    }
			    else if ($("#sub_characters_block").attr("data-sh_id")) {
			        $("#sub_characters_block").remove();
			    }
			    if ( data.persons && data.persons != '' && $("#persons_block").attr("data-sh_id") ) {
			        $("#persons_block").html(data.persons);
					console.log("Успешно загружены персоны.");
			    }
			    else if ($("#persons_block").attr("data-sh_id")) {
					
			        $("#persons_block").remove();
			    }
			}
		});
	}
	
	function personas_dorama()	{
		if ( $("#personas_block").attr("data-has_cache") ) var has_cache_persons = $("#personas_block").attr("data-has_cache");
		else var has_cache_persons = 'no';
		if ( has_cache_persons == 'no' ) {
		    var mdl_id = $("#personas_block").attr("data-mdl_id");
		    $.ajax({
			    url: "/engine/ajax/controller.php?mod=anime_grabber&module=persons",
			    type: "POST",
			    dataType: "html",
			    data: {mdl_id:mdl_id},
			    success: function(data) {
			        if (data.trim() === "Shikimori/Mydramalist не вернул ничего") {
					    console.log("Попытаемся ещё раз.");
					    $("#personas_block").html("Загрузка...");
					    personas();
				    } else {
					    $("#personas_block").html(data);
					    console.log("Успешно загружены актёры.");
				    }
			    }
		    });
		}
	}

	if ( $("#main_characters_block").attr("data-sh_id") || $("#sub_characters_block").attr("data-sh_id") || $("#persons_block").attr("data-sh_id") )  {
	    personas();
	}
	if ( $("#personas_block").attr("data-mdl_id") ) {
	    personas_dorama();
	}

	$("#swilly_refresh").click(function(e) {
		if ( $("#main_characters_block").attr("data-sh_id") || $("#sub_characters_block").attr("data-sh_id") || $("#persons_block").attr("data-sh_id") ) {
		    personas();
		}
		if ( $("#personas_block").attr("data-mdl_id") ) {
		    personas_dorama();
		}
	});
});