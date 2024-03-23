$('.rooms-invite').on('click', function() {
	if(dle_group == 5) {
  		DLEalert('Открывать комнаты совместного просмотра могут только авторизованные пользователи', 'Внимание');
    	return false;
  	}
    var _self = $(this);
    var news_id = _self.attr("data-news_id");
    var news_title = _self.attr("data-news_title");
    var news_iframe = _self.attr("data-news_iframe");
    var shikimori_id = _self.attr("data-shikimori_id");
    var mdl_id = _self.attr("data-mdl_id");
    var news_poster = document.getElementById("room-poster").src;
    $.get(dle_root + "engine/ajax/controller.php?mod=create_room", { news_id: news_id, iframe: news_iframe, title: news_title, poster: news_poster, shikimori_id: shikimori_id, mdl_id: mdl_id, user_hash: dle_login_hash }, function(data){
		if ( data.status == "created" ) {
			window.location.href = '/room/'+data.link+'/';
		} else {
            DLEalert(data.text, 'Внимание'); 
        }
	}, "json");

    return false;
});

function PushSubscribe(post_id, action)
{	
    $.get(dle_root + "engine/ajax/controller.php?mod=push_subscribe", { post_id: post_id, subaction: action, user_hash: dle_login_hash }, function(data){
		if ( data.status ) {
			if ( action == 'subscribe' ) {
        		$('#push_subscribe').hide();
        		$('#push_unsubscribe').show();
    		} else {
        		$('#push_subscribe').show();
        		$('#push_unsubscribe').hide();
    		}
		} else {
            DLEalert(data.error, 'Внимание'); 
        }
	}, "json");
}