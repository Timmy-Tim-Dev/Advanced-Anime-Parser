$(document).ready(function() {
    if ( $("#kodik_player_ajax").attr("data-news_id") ) {
        var news_id = $("#kodik_player_ajax").attr("data-news_id");
        if ( $("#kodik_player_ajax").attr("data-has_cache") ) var has_cache = $("#kodik_player_ajax").attr("data-has_cache");
        else var has_cache = 'no';
        if ( has_cache == 'no' ) {
            $.ajax({
                url: "/engine/ajax/controller.php?mod=anime_grabber&module=kodik_playlist_ajax_new",
                type: "POST",
                dataType: "html",
                data: {news_id:news_id,action:'load_player'},
                success: function(data) {
                    $("#kodik_player_ajax").html(data);
                },
                complete: function() {
                    if ($("#simple-episodes-list").hasClass( "show-flex-grid" )) {
                        $("#simple-episodes-list").scrollToSimple( $("#simple-episodes-list > .active") );
                        $('.prevpl').remove();
                        $('.nextpl').remove();
                    }
                    else scroll_to_active();
                }
            });
        }
        else {
            if ($("#simple-episodes-list").hasClass( "show-flex-grid" )) {
                $("#simple-episodes-list").scrollToSimple( $("#simple-episodes-list > .active") );
                $('.prevpl').remove();
                $('.nextpl').remove();
            }
            else scroll_to_active();
        }
    }
});
(function ($) {
    'use strict';

    $.fn.scrollToSimple = function ($target) {
        var $container = this.first();

        var pos = $target.position(), height = $target.outerHeight();
        var containerScrollTop = $container.scrollTop(), containerHeight = $container.height();
        var top = pos.top + containerScrollTop;

        var paddingPx = containerHeight * 0.15;

        if (top < containerScrollTop) {
            $container.scrollTop(top - paddingPx);
        }
        else if (top + height > containerScrollTop + containerHeight) {
            $container.scrollTop(top + height - containerHeight + paddingPx);
        }
    };
})(jQuery);
function auto_episodes(season, episode, translator) {
	if(!$('#episode-'+season+'-'+episode).hasClass('active')) {
	    $('.b-translator__item').removeClass('active');
		$('#translation-'+translator).addClass('active');
		$('.b-post__lastepisodeout').remove();
		$('.b-simple_episode__item').removeClass('active');
		$('#episode-'+season+'-'+episode+'').addClass('active');
        if ($("#simple-episodes-list").hasClass( "show-flex-grid" )) $("#simple-episodes-list").scrollToSimple( $("#simple-episodes-list > .active") );
        else scroll_to_active();
	}
    else {
    	$('.b-post__lastepisodeout').remove();
    }
}

function kodik_seasons() {
    $('#simple-seasons-tabs').on('click','.b-simple_season__item',function() {
        var _self = $(this);
        if(!_self.hasClass('active')) {
            $('.b-simple_season__item').removeClass('active');
            _self.addClass('active');
            var this_season = _self.attr("data-this_season");
            
            $('.b-simple_episodes__list').hide();
            $('.season-tab-'+this_season).show();
            
            $('.b-simple_episode__item').removeClass('active');
            $('.season-tab-'+this_season+' > li:first').addClass('active');
            
            var this_episode = $('.season-tab-'+this_season+' > li:first').attr("data-this_episode");
            
            $('.b-translators__list').hide();
            $('.translation-tab-'+this_season+'-'+this_episode).show();
            
            var active_translator = $(".b-translator__item.active").attr("data-this_translator_id");
            
            $('.b-translator__item').removeClass('active');
            
            if ( $('#translation-'+this_season+'-'+this_episode+'-'+active_translator).length > 0 ) {
                $('#translation-'+this_season+'-'+this_episode+'-'+active_translator).addClass('active');
                var this_link = $('#translation-'+this_season+'-'+this_episode+'-'+active_translator).attr("data-this_link");
            } else {
                $('.translation-tab-'+this_season+'-'+this_episode+' > li:first').addClass('active');
                var this_link = $('.translation-tab-'+this_season+'-'+this_episode+' > li:first').attr("data-this_link");
            }
            
            $('#player_kodik').html('<iframe src="'+this_link+'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe>');
            
            if ($("#simple-episodes-list").hasClass( "show-flex-grid" )) $("#simple-episodes-list").scrollToSimple( $("#simple-episodes-list > .active") );
            else scroll_to_active();
        }
    });
}
function kodik_episodes() {
    $('.b-simple_episodes__list').on('click','.b-simple_episode__item',function() {
        var _self = $(this);
        if(!_self.hasClass('active')) {
            $('.b-simple_episode__item').removeClass('active');
            _self.addClass('active');
            var this_season = _self.attr("data-this_season");
            var this_episode = _self.attr("data-this_episode");
            
            $('.b-translators__list').hide();
            $('.translation-tab-'+this_season+'-'+this_episode).show();
            
            var active_translator = $(".b-translator__item.active").attr("data-this_translator_id");
            
            $('.b-translator__item').removeClass('active');
            
            if ( $('#translation-'+this_season+'-'+this_episode+'-'+active_translator).length > 0 ) {
                $('#translation-'+this_season+'-'+this_episode+'-'+active_translator).addClass('active');
                var this_link = $('#translation-'+this_season+'-'+this_episode+'-'+active_translator).attr("data-this_link");
            } else {
                $('.translation-tab-'+this_season+'-'+this_episode+' > li:first').addClass('active');
                var this_link = $('.translation-tab-'+this_season+'-'+this_episode+' > li:first').attr("data-this_link");
            }
                
            $('#player_kodik').html('<iframe src="'+this_link+'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe>');
            
            if ($("#simple-episodes-list").hasClass( "show-flex-grid" )) $("#simple-episodes-list").scrollToSimple( $("#simple-episodes-list > .active") );
            else scroll_to_active();
        }
    });
}
function kodik_translates() {
    $('.b-translators__list').on('click','.b-translator__item',function() {
        var _self = $(this);
        if(!_self.hasClass('active')) {
            $('.b-translator__item').removeClass('active');
            _self.addClass('active');
            var this_link = _self.attr("data-this_link");
            $('#player_kodik').html('<iframe src="'+this_link+'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe>');
            
            if ($("#simple-episodes-list").hasClass( "show-flex-grid" )) $("#simple-episodes-list").scrollToSimple( $("#simple-episodes-list > .active") );
            else scroll_to_active();
        }
    });
}

function del(news_id) {
    $.get(dle_root + "engine/ajax/controller.php?mod=anime_grabber&module=kodik_watched", { 'news_id': news_id, 'action': 'delete_watched' }, function(data) {
		if ( data.status ) {
            $('.b-post__lastepisodeout').remove();
		}
	}, "json");
}
function scroll_to_active() {
	if (document.getElementById('simple-episodes-tabs')) {
		var _pw = parseInt($('#kodik_player_ajax').width());
		var _ew = document.getElementById('simple-episodes-tabs').scrollWidth;
		var _cw1 = Math.abs(_pw - 60 - _ew) <= 1;
		var _cw2 = Math.abs(_pw - 10 - _ew) <= 1;
	}

    if ($("div").is("#simple-episodes-tabs")) {
        if (!_cw1 && !_cw2) {
            $('#simple-episodes-tabs').scrollTo($("#simple-episodes-list > .active"), 300);
        } else {
            $('.prevpl').hide();
            $('.nextpl').hide();
            $('#simple-episodes-tabs').css({'margin':'0px 5px'});
        }
    }
}
function prevpl(){
    var scroll = $('#kodik_player_ajax').width()/2;
    $('#simple-episodes-tabs').scrollTo("-=" + scroll + "px", 300);
}
function nextpl(){
    var scroll = $('#kodik_player_ajax').width()/2;
    $('#simple-episodes-tabs').scrollTo("+=" + scroll + "px", 300);
}

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') {
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { 
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

function voicerate_take (news_take) {
	$.ajax({
		url: "/engine/ajax/controller.php?mod=anime_grabber&module=kodik_watched",
		type: "POST",
		dataType: "json",
		data: {news_id:news_take,action:'voicerate_take'},
		success: function(data) {
			if (data != 'Нету кэша') {
				var total = 0;
				var sortedKeys = Object.keys(data).sort(function(a, b) {
					return data[b] - data[a];
				});
				
				var sortedData = {};
				sortedKeys.forEach(function(key) {
					sortedData[key] = data[key];
				});
				
				$("#voicerate_mod").empty();

				Object.values(data).forEach(function(value) {
					total += value;
				});

				sortedKeys.forEach(function(key) {
					var percentage = (data[key] / total * 100).toFixed(1);
					var listItem = $(`
						<div class='voicerate_item'>
							<div class='voicerate_title'>${key}</div>
							<div class='voicerate_prgbar'>
								<div class='voicerate_prgbar_width' style='width: ${percentage}%'></div>
							</div>
							<div class='voicerate_count'>${data[key]} (${percentage}%)</div>
						</div>
					`);
					$("#voicerate_mod").append(listItem);
				});
			} else {
				$("#voicerate_mod").html("В данный момент нет рейтинга озвучек");
			}
		}


	});
};

if ($("#voicerate_mod").attr("data-news_id")) {
	voicerate_take ($("#voicerate_mod").attr("data-news_id"));
}

function kodikMessageListener(message) {
    if ( message.data.key == 'kodik_player_current_episode' ) {
        var news_id = $("#kodik_player_ajax").attr("data-news_id");
        $.get(dle_root + "engine/ajax/controller.php?mod=anime_grabber&module=kodik_watched", { 'news_id': news_id, 'kodik_data': message.data.value }, function(data) {
			if ( data.status ) {
				if ($("#kodik_player_ajax #player").attr('data-autonext') == 'yes') {
					auto_episodes(data.season, data.episode, data.translator);
				} else {
					$('.b-post__lastepisodeout').remove();
				}
			}
		}, "json");
		$.get(dle_root + "engine/ajax/controller.php?mod=anime_grabber&module=kodik_watched", { 'news_id': news_id, 'kodik_data': message.data.value, 'action': 'voicerate' }, function(data) {
			if ( data ) {
				voicerate_take (news_id);
			}
		}, "json");
    }
}
if (window.addEventListener) {
	window.addEventListener('message', kodikMessageListener);
} else {
	window.attachEvent('onmessage', kodikMessageListener);
}

;(function(f){"use strict";"function"===typeof define&&define.amd?define(["jquery"],f):"undefined"!==typeof module&&module.exports?module.exports=f(require("jquery")):f(jQuery)})(function($){"use strict";function n(a){return!a.nodeName||-1!==$.inArray(a.nodeName.toLowerCase(),["iframe","#document","html","body"])}function h(a){return $.isFunction(a)||$.isPlainObject(a)?a:{top:a,left:a}}var p=$.scrollTo=function(a,d,b){return $(window).scrollTo(a,d,b)};p.defaults={axis:"xy",duration:0,limit:!0};$.fn.scrollTo=function(a,d,b){"object"=== typeof d&&(b=d,d=0);"function"===typeof b&&(b={onAfter:b});"max"===a&&(a=9E9);b=$.extend({},p.defaults,b);d=d||b.duration;var u=b.queue&&1<b.axis.length;u&&(d/=2);b.offset=h(b.offset);b.over=h(b.over);return this.each(function(){function k(a){var k=$.extend({},b,{queue:!0,duration:d,complete:a&&function(){a.call(q,e,b)}});r.animate(f,k)}if(null!==a){var l=n(this),q=l?this.contentWindow||window:this,r=$(q),e=a,f={},t;switch(typeof e){case "number":case "string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(e)){e= h(e);break}e=l?$(e):$(e,q);case "object":if(e.length===0)return;if(e.is||e.style)t=(e=$(e)).offset()}var v=$.isFunction(b.offset)&&b.offset(q,e)||b.offset;$.each(b.axis.split(""),function(a,c){var d="x"===c?"Left":"Top",m=d.toLowerCase(),g="scroll"+d,h=r[g](),n=p.max(q,c);t?(f[g]=t[m]+(l?0:h-r.offset()[m]),b.margin&&(f[g]-=parseInt(e.css("margin"+d),10)||0,f[g]-=parseInt(e.css("border"+d+"Width"),10)||0),f[g]+=v[m]||0,b.over[m]&&(f[g]+=e["x"===c?"width":"height"]()*b.over[m])):(d=e[m],f[g]=d.slice&& "%"===d.slice(-1)?parseFloat(d)/100*n:d);b.limit&&/^\d+$/.test(f[g])&&(f[g]=0>=f[g]?0:Math.min(f[g],n));!a&&1<b.axis.length&&(h===f[g]?f={}:u&&(k(b.onAfterFirst),f={}))});k(b.onAfter)}})};p.max=function(a,d){var b="x"===d?"Width":"Height",h="scroll"+b;if(!n(a))return a[h]-$(a)[b.toLowerCase()]();var b="client"+b,k=a.ownerDocument||a.document,l=k.documentElement,k=k.body;return Math.max(l[h],k[h])-Math.min(l[b],k[b])};$.Tween.propHooks.scrollLeft=$.Tween.propHooks.scrollTop={get:function(a){return $(a.elem)[a.prop]()}, set:function(a){var d=this.get(a);if(a.options.interrupt&&a._last&&a._last!==d)return $(a.elem).stop();var b=Math.round(a.now);d!==b&&($(a.elem)[a.prop](b),a._last=this.get(a))}};return p});
