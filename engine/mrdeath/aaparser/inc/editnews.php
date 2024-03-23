<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
 
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( $config['allow_admin_wysiwyg'] == 0 ) {
	$short_st = "$('#short_story').val(";
	$full_st = "$('#full_story').val(";
}
elseif( $config['allow_admin_wysiwyg'] == 1 ) {
	$short_st = "$('#short_story').froalaEditor('html.set', ";
	$full_st = "$('#full_story').froalaEditor('html.set', ";
}
elseif( $config['allow_admin_wysiwyg'] == 2 ) {
	$short_st = "tinymce.get('short_story').setContent(";
	$full_st = "tinymce.get('full_story').setContent(";
}

include_once ENGINE_DIR . '/mrdeath/aaparser/data/config.php';

?>
<style>
ol {
    counter-reset: i;
    list-style: none;
    font: 14px 'trebuchet MS', 'lucida sans';
    padding: 0;
    margin-bottom: 0em;
    text-shadow: 0 1px 0 rgba(255,255,255,.5);
	overflow: auto;
    width: 100%;
    max-height: 200px;
}

ol ol {
    margin: 0 0 0 2em;
}

.rounded-list i{

    position: relative;
    display: block;
    padding: .4em .4em .4em 3em;
    *padding: .4em;
    margin: .5em 0;
    background: #ddd;
    color: #444;
    text-decoration: none;
    border-radius: .3em;
    transition: all .3s ease-out;   
}

.rounded-list i:hover{
    background: #eee;
}

.rectangle-list i{
    position: relative;
    display: block;
    padding: .4em .4em .4em .8em;
    *padding: .4em;
    margin: .5em 0 .5em 2.5em;
    background: #ddd;
    color: #444;
    text-decoration: none;
    transition: all .3s ease-out;   
}

.rectangle-list i:hover{
    background: #eee;
}   

.rectangle-list i:before{
    content: counter(i);
    counter-increment: i;
    position: absolute; 
    left: -2.5em;
    top: 50%;
    margin-top: -1em;
    background: #fa8072;
    height: 2em;
    width: 2em;
    line-height: 2em;
    text-align: center;
    font-weight: bold;
}

.rectangle-list i:after{
    position: absolute; 
    content: '';
    border: .5em solid transparent;
    left: -1em;
    top: 50%;
    margin-top: 5em;
    transition: all .3s ease-out;               
}

.rectangle-list i:hover:after{
    left: -.5em;
    border-left-color: #fa8072;             
}

.btn-rights {
	float: right;
	margin: -5px 0 0 5px;
	
	background: #87ceeb;
}

.parser_grabber_style__dialog {
    position: static;
    max-height: 70vh;
}
.parser_grabber_style__dialogs {
    margin-top: 23px;
}

caption, table, tbody, td, tfoot, th, thead, tr {
    margin: 0;
    padding: 0;
    border: 0;
    outline: 0;
    border-collapse: collapse;
    border-spacing: 0;
}
span.material-info {
    display: block;
    width: 16px;
    height: 16px;
    cursor: pointer;
    margin: 2px 0;
    background-image: url(data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 438.533 438.533'%3E%3Cpath fill='%23000' d='M409.133 109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736 9.801 259.058 0 219.273 0c-39.781 0-76.47 9.801-110.063 29.407-33.595 19.604-60.192 46.201-79.8 79.796C9.801 142.8 0 179.489 0 219.267c0 39.78 9.804 76.463 29.407 110.062 19.607 33.592 46.204 60.189 79.799 79.798 33.597 19.605 70.283 29.407 110.063 29.407s76.47-9.802 110.065-29.407c33.593-19.602 60.189-46.206 79.795-79.798 19.603-33.596 29.403-70.284 29.403-110.062.001-39.782-9.8-76.472-29.399-110.064zM255.82 356.309c0 2.662-.862 4.853-2.573 6.563-1.704 1.711-3.895 2.567-6.557 2.567h-54.823c-2.664 0-4.854-.856-6.567-2.567-1.714-1.711-2.57-3.901-2.57-6.563v-54.823c0-2.662.855-4.853 2.57-6.563 1.713-1.708 3.903-2.563 6.567-2.563h54.823c2.662 0 4.853.855 6.557 2.563 1.711 1.711 2.573 3.901 2.573 6.563v54.823zm69.518-168.735c-2.382 7.043-5.044 12.804-7.994 17.275-2.949 4.473-7.187 9.042-12.709 13.703-5.51 4.663-9.891 7.996-13.135 9.998-3.23 1.995-7.898 4.713-13.982 8.135-6.283 3.613-11.465 8.326-15.555 14.134-4.093 5.804-6.139 10.513-6.139 14.126 0 2.67-.862 4.859-2.574 6.571-1.707 1.711-3.897 2.566-6.56 2.566h-54.82c-2.664 0-4.854-.855-6.567-2.566-1.715-1.712-2.568-3.901-2.568-6.571v-10.279c0-12.752 4.993-24.701 14.987-35.832 9.994-11.136 20.986-19.368 32.979-24.698 9.13-4.186 15.604-8.47 19.41-12.847 3.812-4.377 5.715-10.188 5.715-17.417 0-6.283-3.572-11.897-10.711-16.849-7.139-4.947-15.27-7.421-24.409-7.421-9.9 0-18.082 2.285-24.555 6.855-6.283 4.565-14.465 13.322-24.554 26.263-1.713 2.286-4.093 3.431-7.139 3.431-2.284 0-4.093-.57-5.424-1.709L121.35 145.89c-4.377-3.427-5.138-7.422-2.286-11.991 24.366-40.542 59.672-60.813 105.922-60.813 16.563 0 32.744 3.903 48.541 11.708 15.796 7.801 28.979 18.842 39.546 33.119 10.554 14.272 15.845 29.787 15.845 46.537-.014 8.374-1.208 16.079-3.58 23.124z'/%3E%3C/svg%3E);
    opacity: .2;
}
.table>tbody>tr>td.short-td {
    padding: 8px 0;
}
.episodes-data {
    padding: 0 13px;
    font-weight: 700;
    color: #68a2d5;
    cursor: pointer;
}
.episodes-data-2 {
    padding: 0 5px;
}
.id-link, .player-copy-buttons {
    display: -webkit-flex;
    display: flex;
}
.id-link a {
    border-radius: 2px 0 0 2px;
    height: 22px;
    line-height: 22px;
    padding: 0 5px;
    border: 0;
}
.id-link .copy-id, .id-link .search-id {
    border-left: 1px solid #529438;
    border-radius: 0 2px 2px 0;
    height: 22px;
    cursor: pointer;
}
.player-button svg {
    cursor: pointer;
}
.table-icon svg {
    width: 20px;
    margin: -5px 0;
}
.id-link .copy-id{border-left:1px solid #529438;border-radius:0 2px 2px 0;height:22px;cursor:pointer}
.id-link .copy-id{background:#649f4d url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' height='15px' viewBox='0 0 352.253 352.253'%3E%3Cpath d='M257.815 48.693V0H44.64v303.543l49.798.006v48.687l152.113.018 61.062-60.449V48.693h-49.798zM65.269 20.642h171.896v215.947h-46.669v46.321H65.269V20.642zM286.97 285.288h-46.682v46.321H115.067v-28.055l81.674.012 61.074-60.456V69.334h29.154v215.954z' fill='white' /%3E%3C/svg%3E") center center no-repeat;width:22px}
.id-link .copy-id:hover{background-color:#529438}
.id-link .copy-id:active{box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}
.id-link .copy-id.middle{border-radius:0}
.id-link .copy-id.copied{background-color:#9f9f9f}
.pntr{cursor:pointer}
</style>
<link rel="stylesheet" href="https://unpkg.com/balloon-css/balloon.min.css">
<script type="text/javascript">

    function parser_search(){

            ShowLoading('');
            $.ajax({
                url: "/engine/ajax/controller.php?mod=anime_parser",
                data:{action: 'parser_search',  title: document.addnews.title.value},
                dataType: "json",
                cache: false,
                success: function(data) {
					if ( data.status == "results" ) {

                        HideLoading('');
                        var results = "<div style=\"width: auto; min-height: 39px; height: auto;\" class=\"parser_grabber_style__dialog ui-dialog-content ui-widget-content\" scrolltop=\"0\" scrollleft=\"0\"><table class=\"table table-hover content-table serials\"><thead><tr><th>Тип</th><th>Название</th><th><span>Оригинальное</span></th><th>Год</th><th><span class=\"season-data-header\">S</span></th><th><span class=\"episode-data-header\">E</span></th><th><span>MDL</span></th><th><span>Shiki</span></th><th></th><th></th><th></th><th><span class=\"text-grey pntr\" data-balloon-length=\"medium\" aria-label=\"Закрыть результаты\" data-balloon-pos=\"up\" onclick=\"$('#parser_result').html('')\"><b><i class=\"fa fa-close\"></i></b></span></th></tr></thead><tbody>";
                        $.each(data.result, function(key, item) {
							
							results += "<tr><td class=\"created_at\"><span>" + item.kind + "</span></td>";
							results += "<td class=\"material-title\">" + item.title + "</td>";
							results += "<td>" + item.orig_title + "</td>";
							results += "<td class=\"year\">" + item.year + "</td>";
							results += "<td class=\"short-td\"><span class=\"episodes-data-2 one-season\">" + item.last_season + "</span></td>";
							results += "<td><span class=\"episodes-data-2\">" + item.last_episode + "</span></td>";
							if ( item.mdl_id ) results += "<td><div class=\"id-link\"><a title=\"MyDramaList ID\" class=\"btn btn-success btn-xs\" target=\"_blank\" href=\"https://mydramalist.com/" + item.mdl_id + "/\">" + item.mdl_id + "</a><div class=\"copy-id\" title=\"Копировать\" onclick=\"CopyItForMe('" + item.mdl_id + "')\"></div></div></td>";
							else results += "<td> </td>";
							if ( item.shiki_id && item.shiki_id != 0 ) results += "<td><div class=\"id-link\"><a title=\"Shikimori ID\" class=\"btn btn-success btn-xs\" target=\"_blank\" href=\"" + item.shiki_link + "\">" + item.shiki_id + "</a><div class=\"copy-id\" title=\"Копировать\" onclick=\"CopyItForMe('" + item.shiki_id + "')\"></div></div></td>";
							else results += "<td> </td>";
							if ( item.kodik_exists ) results += "<td><span class=\"pntr\" data-balloon-length=\"medium\" aria-label=\"Есть в базе Kodik\" data-balloon-pos=\"up\"><b><img src=\"https://bd.kodik.biz/packages/images/favicon.png\"></b></span></td>";
							else results += "<td> </td>";
							if (item.find_id == 'est') {
							    results += "<td><span class=\"text-success pntr\" data-balloon-length=\"medium\" aria-label=\"Есть на сайте\" data-balloon-pos=\"up\"><b><i class=\"fa fa-check-circle\"></i></b></span></td>";
							    results += "<td><span class=\"text-grey pntr\" data-balloon-length=\"medium\" aria-label=\"Редактировать новость\" data-balloon-pos=\"up\" onclick=\"window.open('" + item.edit_link + "')\"><b><i class=\"fa fa-edit\"></i></b></span></td>";
							}
							else {
							    results += "<td><span class=\"text-danger pntr\" data-balloon-length=\"medium\" aria-label=\"Нет на сайте\" data-balloon-pos=\"up\"><b><i class=\"fa fa-exclamation-circle\"></i></b></span></td>";
							    results += "<td> </td>";
							}
							results += "<td><a type=\"button\" class=\"btn btn-info btn-xs copycode\" onclick=\"parser_get('" + item.shiki_id + "', '" + item.mdl_id + "')\">Парсить</a></td>";
							
                        });
                        results += "</tbody></table></div>";
                        $("#parser_result").html(results);

                    } else if ( data.status == "paste" ) {

                        parser_get(data.result);
                        HideLoading('');

                    } else {

                        HideLoading('');
                        alert('Ничего не найдено!');

                    }

                }
            });

    }

    function parser_get( shiki_id, mdl_id ){

        if( !shiki_id && !mdl_id ){

            alert('Error!');

        } else {
            
            var id_news = '<?php echo $row['id']; ?>';
            var mode = 'editnews';

            ShowLoading('');
            $.ajax({
                url: "/engine/ajax/controller.php?mod=anime_parser",
                data:{action: 'parser_get', shiki_id: shiki_id, mdl_id: mdl_id, id_news: id_news, mode: mode},
                dataType: "json",
                cache: false,
                success: function(data) {

                    if ( data.status == "paste" ) {
                        
                        HideLoading('');
                        parser_paste(data.result);

                    } else {

                        HideLoading('');
                        alert('Error!');

                    }

                }
            });

        }

    }

    function parser_paste(data){

        $.each(data, function(name, value) {
            
            if ( name == 'xf_poster' && value != '' ) {
                $('#uploadedfile_'+data.xf_poster_name+'').html(value);
                $('#xf_'+data.xf_poster_name+'').val(data.xf_poster_url);
                $('#xfupload_'+data.xf_poster_name+' .qq-upload-button, #xfupload_'+data.xf_poster_name+' .qq-upload-button input').attr("disabled","disabled");
            } else if ( name == 'xf_screens' && value != '' ) {
                $('#uploadedfile_'+data.xf_screens_name+'').html(value);
                $('#xf_'+data.xf_screens_name+'').val(data.xf_screens_url);
            } else if ( name == 'is_camrip' && value != '' ) {
				var checkbox = $('#xfield_holder_'+data.is_camrip_field+'').find("input[type='checkbox']");
                checkbox.prop("checked",true);
            } else if ( name == 'is_lgbt' && value != '' ) {
				var checkbox2 = $('#xfield_holder_'+data.is_lgbt_field+'').find("input[type='checkbox']");
                checkbox2.prop("checked",true);
            } else if ( $('#xf_'+name).attr('data-rel') == 'links' ){
                $('#xf_'+name).tokenfield('setTokens', value);
            } else if ( value != '' ) {
                $('#xf_'+name).val(value);
            }
                
        });

        if( data.title ) $("input[name=title]").val(data.title);
        if( data.alt_name ) $("input[name=alt_name]").val(data.alt_name);
        if( data.tags ) $('[name=tags]').tokenfield('setTokens', data.tags);

        if( data.meta_titles ) $('input[name=meta_title]').val(data.meta_titles);
        if( data.meta_descrs ) $('input[name=descr]').val(data.meta_descrs);
        if( data.meta_keywords ) $('[name=keywords]').tokenfield('setTokens', data.meta_keywords);
		if( data.catalog ) $('input[name=catalog_url]').val(data.catalog);
        
        if( data.short_story ) {
            var shortstory = $("#short_story").val();
            if ( !shortstory ) {
                <?php echo $short_st; ?>data.short_story<?php echo ");\n"; ?>
            }
        }
        if( data.full_story ) {
            var fullstory = $("#full_story").val();
            if ( !fullstory ) {
                <?php echo $full_st; ?>data.full_story<?php echo ");\n"; ?>
            }
        }
		
		if( data.parse_cat_list ) {
		
			$.each(data.parse_cat_list.split(','), function(index, value) {
				$('.categoryselect option[value='+ value +']' ).prop("selected", true);
			});

            $('.categoryselect').trigger('chosen:updated');
			$('#category_custom_sort').val(data.parse_cat_list.split(',').join('::'));
		}

        return;

    }
    
    function CopyItForMe (text) {
        var copyTextarea = document.createElement("textarea");
        copyTextarea.style.position = "fixed";
        copyTextarea.style.opacity = "0";
        copyTextarea.textContent = text;
 
        document.body.appendChild(copyTextarea);
        copyTextarea.select();
        document.execCommand("copy");
        document.body.removeChild(copyTextarea);
    }

</script>