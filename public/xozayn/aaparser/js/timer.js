function timer(timestamp){
    if (timestamp < 0) timestamp = 0;
 
    var week = Math.floor( ((timestamp/60/60) / 168));
    var day = Math.floor( ((timestamp/60/60) / 24));
    var lday = Math.floor( ((timestamp/60/60) / 24) - (7*week));
    var hour = Math.floor(timestamp/60/60);
    var mins = Math.floor((timestamp - hour*60*60)/60);
    var secs = Math.floor(timestamp - hour*60*60 - mins*60); 
    var lhour = Math.floor( (timestamp - day*24*60*60) / 60 / 60 );
  
    if(String(week).length == 1){week="0" + week;}
    $('.countdown_wrp .weeks .value').text(week);
  
	if(String(lday).length == 1){lday="0" + lday;}
	$('.countdown_wrp .days .value').text(lday);
  
	if(String(lhour).length == 1){lhour="0" + lhour;}
	$('.countdown_wrp .hours .value').text(lhour);
 
	if(String(mins).length == 1){mins="0" + mins;}
	$('.countdown_wrp .minutes .value').text(mins);

	if(String(secs).length == 1){secs="0" + secs;}
	$('.countdown_wrp .seconds .value').text(secs);
  
	$('.countdown_wrp .weeks .unit').text(numpf(week, "неделя", "недели", "недель"));
	$('.countdown_wrp .days .unit').text(numpf(lday, "день", "дня", "дней"));
	$('.countdown_wrp .hours .unit').text(numpf(lhour, "час", "часа", "часов"));
	$('.countdown_wrp .minutes .unit').text(numpf(mins, "минута", "минуты", "минут"));
	$('.countdown_wrp .seconds .unit').text(numpf(secs, "секунда", "секунды", "секунд")); 
}
 
$(document).ready(function(){
	if ( typeof initialDateStr !== 'undefined' ) {
		var initialDate = new Date(initialDateStr.replace(/(\d{2}).(\d{2}).(\d{4}) (\d{2}):(\d{2}):(\d{2})/, '$3-$2-$1T$4:$5:$6'));
		var unixTimestampNew = Math.floor(initialDate.getTime() / 1000);
		var currentUnixTimestamp = Math.floor(new Date().getTime() / 1000);
		var timeDifference = unixTimestampNew - currentUnixTimestamp;
		var timer_timestamp = timeDifference > 0 ? timeDifference : 0;
		if (typeof timer_timestamp !== "undefined" && timer_timestamp > 0) {
			setInterval(function(){
				timer_timestamp = timer_timestamp - 1;
				timer(timer_timestamp);
			}, 1000);
		}
	}
});

function numpf(t, e, i, n) {
    var o = t % 10;
    return 1 == o && (1 == t || t > 20) ? e : o > 1 && 5 > o && (t > 20 || 10 > t) ? i : n;
}