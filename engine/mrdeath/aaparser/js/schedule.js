$('#show-more-today').on('click', function() {
    $('.list_today').show();
    $('#show-more-today').remove();
    return false;
});

$('#show-more-tomorrow').on('click', function() {
    $('.list_tomorrow').show();
    $('#show-more-tomorrow').remove();
    return false;
});

function ScheduleChange(day) {	
	$('.calendar-date__item1').removeClass('calendar-active');
    $('#calendar-'+day).addClass('calendar-active');
    $('.calendar__item').hide();
    $('#calendar-list-'+day).show();
}
function FastList(animeId, type) {
	
	if(dle_group == 5) {
  		DLEalert('Доступно только авторизованным пользователям!', 'Внимание');
    	return false;
  	}
    
    $.get(dle_root + "engine/ajax/controller.php?mod=favorites", { 'fav_id': animeId, 'action': type, 'skin': dle_skin, 'user_hash': dle_login_hash }, function(data){
		if ( data.success === true ) {
			if ( type == 'minus' ) {
                $('#watchlist-off-'+animeId).hide();
                $('#watchlist-on-'+animeId).show();
                DLEalert('Аниме удалено из ваших закладок', 'Успешно');
            } else {
                $('#watchlist-on-'+animeId).hide();
                $('#watchlist-off-'+animeId).show();
                DLEalert('Аниме добавлено в ваши закладки', 'Успешно');
            }
            
		} else {
			DLEalert('Ошибка добавления аниме в закладки', 'Ошибка');
        }
	}, "json");
}