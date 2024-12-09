$(document).ready(function() {
    if ( $("#kodik_player_ajax").attr("data-news_id") ) {
        var news_id = $("#kodik_player_ajax").attr("data-news_id");
        if ( $("#kodik_player_ajax").attr("data-has_cache") ) var has_cache = $("#kodik_player_ajax").attr("data-has_cache");
        else var has_cache = 'no';
        if ( has_cache == 'no' ) {
            $.ajax({
                url: "/engine/ajax/controller.php?mod=anime_grabber&module=kodik_playlist_ajax_screen",
                type: "POST",
                dataType: "html",
                data: {news_id:news_id,action:'load_player'},
                success: function(data) {
                    $("#kodik_player_ajax").html(data);
                },
                complete: function() {
                    var this_translator = $(".b-translator__item.active").attr("data-this_translator");
                    var this_season = $(".b-simple_season__item.active").attr("data-this_season");
                    scroll_to_active();
					setTimeout(initializeTranslatorsList, 100);
					setTimeout(initializeScreensList, 100);
					setTimeout(initializelazyLoad, 100);
                }
            });
        }
        else {
            if ($("#simple-episodes-list").hasClass( "show-flex-grid" )) {
                $("#simple-episodes-list").scrollToSimple( $("#simple-episodes-list > .active") );
            }
            else scroll_to_active();
			setTimeout(initializeTranslatorsList, 100);
			setTimeout(initializeScreensList, 100);
			setTimeout(initializelazyLoad, 100);
        }
	}
	
	
});

function initializelazyLoad() {
    $(".ibox_right .prenext, .ibox_left .prenext").css('height', $("#player_kodik").height());
	$(window).on('resize', function () {
		$(".ibox_right .prenext, .ibox_left .prenext").css('height', $("#player_kodik").height());
	});
	$("#simple-episodes-tabs ul, .prenext, #simple-episodes-tabs").on('scroll', function () {
		$(window).lazyLoadXT();
	});
}

function initializeTranslatorsList() {
    var $translatorsList = $('#translators-list')[0];

    if ($translatorsList) {
        $translatorsList.addEventListener('wheel', function(event) {
            var delta = event.deltaY || event.detail || event.wheelDelta;
            var direction = (delta > 0) ? 1 : -1;
            $translatorsList.scrollLeft += direction * 100;
            event.preventDefault();
        }, { passive: false });
    }
	$('#translators-list').prepend($('#translators-list .active').detach());
	$('#translators-list li').click(function() {
		$('#translators-list').prepend($(this).detach());
		$('#translators-list').scrollLeft(0);
	});
}

function initializeScreensList() {
    var $translatorsList = $('.b-simple_episodes__list_swilly');

    if ($translatorsList.length > 0) {
        $translatorsList.each(function() {
            this.addEventListener('wheel', function(event) {
                var delta = event.deltaY || event.detail || event.wheelDelta;
                var direction = (delta > 0) ? 1 : -1;
                this.scrollLeft += direction * 100;
                event.preventDefault();
            }, { passive: false });
        });
    }
}
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
	if(!$('#episode-'+season+'-'+episode+'-'+translator).hasClass('active')) {
		$('.b-post__lastepisodeout').remove();
		$('.b-simple_episode__item_swilly').removeClass('active');
		$('#episode-'+season+'-'+episode+'-'+translator).addClass('active');
        if ($(".b-simple_episodes__list_swilly").hasClass( "show-flex-grid" )) $(".b-simple_episodes__list_swilly").scrollToSimple( $(".b-simple_episodes__list_swilly > .active") );
        else scroll_to_active();
	}
    else {
    	$('.b-post__lastepisodeout').remove();
    }
	$(window).lazyLoadXT();
}
function kodik_translates() {
    $('#translators-list').on('click','.b-translator__item',function() {
        var _self = $(this);
        if(!_self.hasClass('active')) {
            
            $('.b-translator__item').removeClass('active');
            _self.addClass('active');
            var this_translator = _self.attr("data-this_translator");
            
            $('.b-simple_seasons__list').hide();
            
            var active_season = $(".b-simple_season__item.active").attr("data-this_season");
            $('.b-simple_season__item').removeClass('active');
            var hide_seasons = $("#player_kodik").attr("data-hide_seasons");
            
            if ( $('#season-'+this_translator+'-'+active_season).length > 0 ) {
                $('#season-'+this_translator+'-'+active_season).addClass('active');
                var this_season = $('#season-'+this_translator+'-'+active_season).attr("data-this_season");
            } else {
                $('.season-tab-'+this_translator+' > li:first').addClass('active');
                var this_season = $('.season-tab-'+this_translator+' > li:first').attr("data-this_season");
            }
            
            if ( hide_seasons == 'no' ) $('.season-tab-'+this_translator).show();
            
            $('.b-simple_episodes__list_swilly').hide();
            $('.episode-tab-'+this_translator+'-'+this_season).show();
            
            var active_episode = $(".b-simple_episode__item_swilly.active").attr("data-this_episode");
            
            $('.b-simple_episode__item_swilly').removeClass('active');
            
            if ( $('#episode-'+this_season+'-'+active_episode+'-'+this_translator).length > 0 ) {
                $('#episode-'+this_season+'-'+active_episode+'-'+this_translator).addClass('active');
                var this_link = $('#episode-'+this_season+'-'+active_episode+'-'+this_translator).attr("data-this_link");
            } else {
                $('.episode-tab-'+this_translator+'-'+this_season+' > li:first').addClass('active');
                var this_link = $('.episode-tab-'+this_translator+'-'+this_season+' > li:first').attr("data-this_link");
            }
            
            $('#player_kodik').html('<iframe src="'+this_link+'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe>');
            
            if ($(".b-simple_episodes__list_swilly").hasClass( "show-flex-grid" )) $(".b-simple_episodes__list_swilly").scrollToSimple( $(".b-simple_episodes__list_swilly > .active") );
            else scroll_to_active();

        }
    });
	$(window).lazyLoadXT();
}
function kodik_translates_alt() {
    $('#translators-list').on('click','.b-translator__item',function() {
        var _self = $(this);
        if(!_self.hasClass('active')) {
            
            $('.b-translator__item').removeClass('active');
            _self.addClass('active');
            
            var this_translator = _self.attr("data-this_translator");
			var active_episode = $(".b-simple_episode__item_swilly.active").attr("data-this_episode");
			var this_season = $(".b-simple_episode__item_swilly.active").attr("data-this_season");
			$(".b-simple_episode__item_swilly.active").removeClass("active");
			$(".b-simple_episodes__list_swilly").hide();
			$(".simple-episodes-tabs-swilly [id^='episodes-tab-" + this_translator + "']").show();
			if ( $('#episode-'+this_season+'-'+active_episode+'-'+this_translator).length > 0 ) {
                $('#episode-'+this_season+'-'+active_episode+'-'+this_translator).addClass('active');
                var this_link = $('#episode-'+this_season+'-'+active_episode+'-'+this_translator).attr("data-this_link");
            } else {
                $('.episode-tab-'+this_translator+'-'+this_season+' > li:first').addClass('active');
                var this_link = $('.episode-tab-'+this_translator+'-'+this_season+' > li:first').attr("data-this_link");
            }

            $('#player_kodik').html('<iframe src="'+this_link+'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe>');

        }
    });
	$(window).lazyLoadXT();
}
function kodik_seasons() {
    $('.b-simple_seasons__list').on('click','.b-simple_season__item',function() {
        var _self = $(this);
        if(!_self.hasClass('active')) {
            $('.b-simple_season__item').removeClass('active');
            _self.addClass('active');
            var this_season = _self.attr("data-this_season");
            var this_translator = _self.attr("data-this_translator");
            
            $('.b-simple_episodes__list_swilly').hide();
            $('.episode-tab-'+this_translator+'-'+this_season).show();
            
            $('.b-simple_episode__item_swilly').removeClass('active');
            
            $('.episode-tab-'+this_translator+'-'+this_season+' > li:first').addClass('active');
            var this_link = $('.episode-tab-'+this_translator+'-'+this_season+' > li:first').attr("data-this_link");
            
            $('#player_kodik').html('<iframe src="'+this_link+'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe>');
            
            if ($(".b-simple_episodes__list_swilly").hasClass( "show-flex-grid" )) $(".b-simple_episodes__list_swilly").scrollToSimple( $(".b-simple_episodes__list_swilly > .active") );
            else scroll_to_active();
            
        }
    });
	$(window).lazyLoadXT();
}
function kodik_episodes() {
    $('.b-simple_episodes__list_swilly').on('click','.b-simple_episode__item_swilly',function() {
        var _self = $(this);
        if(!_self.hasClass('active')) {
            $('.b-simple_episode__item_swilly').removeClass('active');
            _self.addClass('active');
            var this_link = _self.attr("data-this_link");
            var this_season = _self.attr("data-this_season");
            var this_translator = _self.attr("data-this_translator");
            $('#player_kodik').html('<iframe src="'+this_link+'" width="724" height="460" frameborder="0" allowfullscreen=""></iframe>');
            
            
            if ($(".b-simple_episodes__list_swilly").hasClass( "show-flex-grid" )) $(".b-simple_episodes__list_swilly").scrollToSimple( $(".b-simple_episodes__list_swilly > .active") );
            else scroll_to_active();
        }
    });
	$(window).lazyLoadXT();
}
function del(news_id) {
    $.get(dle_root + "engine/ajax/controller.php?mod=anime_grabber&module=kodik_watched", { 'news_id': news_id, 'action': 'delete_watched' }, function(data) {
		if ( data.status ) {
            $('.b-post__lastepisodeout').remove();
		}
	}, "json");
}
function scroll_to_active() {
    let $horItem = $(".ibox_top .b-simple_episode__item_swilly.active, .ibox_bottom .b-simple_episode__item_swilly.active");
    let $verItem = $(".ibox_left .b-simple_episode__item_swilly.active, .ibox_right .b-simple_episode__item_swilly.active");
    let $horCont = $(".b-simple_episodes__list_swilly .active").parent();
    let $verCont = $(".prenext");

    // Горизонтальная прокрутка
    if ($horItem.length && $horCont.length) {
        let activeOffsetLeft = $horItem[0].offsetLeft;
        let containerOffsetLeft = $horCont.scrollLeft();
        let scrollLeft = activeOffsetLeft - ($horCont.width() / 2) + ($horItem[0].scrollWidth / 2);
		
        $horCont.each(function() {
            $(this).scrollLeft(scrollLeft);
        });
    }

    // Вертикальная прокрутка
    if ($verItem.length && $verCont.length) {
        const activeOffsetTop = $verItem.position().top;
        const containerOffsetTop = $verCont.position().top;
        const scrollTop = activeOffsetTop - containerOffsetTop - ($verCont.height() / 2) + ($verItem.outerHeight() / 2);
        $verCont.each(function() {
            $(this).scrollTop(scrollTop);
        });
    }
	$(window).lazyLoadXT();
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
var news_id = 0;
var kodik_current_episode = 0;
var kodik_current_episode_duration = 0;
function kodikMessageListener(message) {
	if ( message.data.key == 'kodik_player_current_episode' ) {
		news_id = $("#kodik_player_ajax").attr("data-news_id");
		kodik_current_episode = message.data.value.episode;
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
	if ($("#kodik_player_ajax").attr("data-player_cookie") == '1') {
		if ( message.data.key == 'kodik_player_duration_update' ) {
			kodik_current_episode_duration = message.data.value;
		}
		if ( message.data.key == 'kodik_player_time_update' ) {
			const episodeData = {
				time: message.data.value,
				duration: kodik_current_episode_duration
			};
			jQuery.cookie("kodik_newsid_" + news_id + "_episode_" + kodik_current_episode, JSON.stringify(episodeData), { expires: 365, path: "/" });
		}
	}
}
if (window.addEventListener) {
	window.addEventListener('message', kodikMessageListener);
} else {
	window.attachEvent('onmessage', kodikMessageListener);
}

;(function(f){"use strict";"function"===typeof define&&define.amd?define(["jquery"],f):"undefined"!==typeof module&&module.exports?module.exports=f(require("jquery")):f(jQuery)})(function($){"use strict";function n(a){return!a.nodeName||-1!==$.inArray(a.nodeName.toLowerCase(),["iframe","#document","html","body"])}function h(a){return $.isFunction(a)||$.isPlainObject(a)?a:{top:a,left:a}}var p=$.scrollTo=function(a,d,b){return $(window).scrollTo(a,d,b)};p.defaults={axis:"xy",duration:0,limit:!0};$.fn.scrollTo=function(a,d,b){"object"=== typeof d&&(b=d,d=0);"function"===typeof b&&(b={onAfter:b});"max"===a&&(a=9E9);b=$.extend({},p.defaults,b);d=d||b.duration;var u=b.queue&&1<b.axis.length;u&&(d/=2);b.offset=h(b.offset);b.over=h(b.over);return this.each(function(){function k(a){var k=$.extend({},b,{queue:!0,duration:d,complete:a&&function(){a.call(q,e,b)}});r.animate(f,k)}if(null!==a){var l=n(this),q=l?this.contentWindow||window:this,r=$(q),e=a,f={},t;switch(typeof e){case "number":case "string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(e)){e= h(e);break}e=l?$(e):$(e,q);case "object":if(e.length===0)return;if(e.is||e.style)t=(e=$(e)).offset()}var v=$.isFunction(b.offset)&&b.offset(q,e)||b.offset;$.each(b.axis.split(""),function(a,c){var d="x"===c?"Left":"Top",m=d.toLowerCase(),g="scroll"+d,h=r[g](),n=p.max(q,c);t?(f[g]=t[m]+(l?0:h-r.offset()[m]),b.margin&&(f[g]-=parseInt(e.css("margin"+d),10)||0,f[g]-=parseInt(e.css("border"+d+"Width"),10)||0),f[g]+=v[m]||0,b.over[m]&&(f[g]+=e["x"===c?"width":"height"]()*b.over[m])):(d=e[m],f[g]=d.slice&& "%"===d.slice(-1)?parseFloat(d)/100*n:d);b.limit&&/^\d+$/.test(f[g])&&(f[g]=0>=f[g]?0:Math.min(f[g],n));!a&&1<b.axis.length&&(h===f[g]?f={}:u&&(k(b.onAfterFirst),f={}))});k(b.onAfter)}})};p.max=function(a,d){var b="x"===d?"Width":"Height",h="scroll"+b;if(!n(a))return a[h]-$(a)[b.toLowerCase()]();var b="client"+b,k=a.ownerDocument||a.document,l=k.documentElement,k=k.body;return Math.max(l[h],k[h])-Math.min(l[b],k[b])};$.Tween.propHooks.scrollLeft=$.Tween.propHooks.scrollTop={get:function(a){return $(a.elem)[a.prop]()}, set:function(a){var d=this.get(a);if(a.options.interrupt&&a._last&&a._last!==d)return $(a.elem).stop();var b=Math.round(a.now);d!==b&&($(a.elem)[a.prop](b),a._last=this.get(a))}};return p});