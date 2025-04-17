<?php

/*
=====================================================
 Copyright (c) 2022-2024 MrDeath && Timmy
=====================================================
 This code is protected by copyright
=====================================================
*/
 
if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if ( $aaparser_config['calendar_settings']['enable_schedule'] && $aaparser_config['calendar_settings']['schedule_main'] ) {
	$raspisanie_ongoingov = dle_cache( "raspisanie_ongoingov", false );
	if( $raspisanie_ongoingov === false ) {
		$raspisanie_ongoingov = [];
		$sql_select = "SELECT * FROM " . PREFIX . "_raspisanie_ongoingov ORDER BY date ASC";
		$sql_result = $db->query( $sql_select );
		while ( $schedule_row = $db->get_row( $sql_result ) ) {
			$raspisanie_ongoingov[] = $schedule_row;
		}
  		$db->free( $sql_result );
		if ( $raspisanie_ongoingov ) {
			create_cache( "raspisanie_ongoingov", json_encode($raspisanie_ongoingov, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ), false );
		}
	} else $raspisanie_ongoingov = json_decode($raspisanie_ongoingov, true);

	if ( $member_id['favorites'] ) $my_list = explode(',', $member_id['favorites']);
	else $my_list = [];

	$calendar_mas = [];
	$anime_list_today = $anime_list_tomorrow = '';
	$today_name = strtolower(date("l", $_TIME));
	$tomorrow_name = strtolower(date("l", strtotime('+1 day', $_TIME)));
	foreach ( $raspisanie_ongoingov as $rnum => $raspisanie ) {
  		if (!in_array(strtolower($raspisanie['day_name']), [$today_name, $tomorrow_name])) continue;
  		$position = 1;
  		$anime_list_mas = json_decode($raspisanie['anime_list'], true);
  		foreach ( $anime_list_mas as $anime_item ) {
			$anime_day_name = strtolower(date("l", strtotime($anime_item['next_date'])));
      		if ( $anime_item['image'] ) {
          		$temp_poster = explode('|', $anime_item['image']);
          		if ( strpos($temp_poster[0], '/uploads/posts/') == false ) $poster = '/uploads/posts/'.$temp_poster[0];
          		else $poster = $temp_poster[0];
        	} else $poster = '';
      		if ( in_array($anime_item['news_id'], $my_list) ) $add_to_list = '<div id="watchlist-on-'.$anime_item['news_id'].'" style="display:none;" onclick="FastList(\''.$anime_item['news_id'].'\', \'plus\'); return false;" class="calendar__item-bookmark watchlist_btn">
            	<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="none">
                	<path d="M8.96173 18.9109L9.42605 18.3219L8.96173 18.9109ZM12 5.50063L11.4596 6.02073C11.601 6.16763 11.7961 6.25063 12 6.25063C12.2039 6.25063 12.399 6.16763 12.5404 6.02073L12 5.50063ZM15.0383 18.9109L15.5026 19.4999L15.0383 18.9109ZM9.42605 18.3219C7.91039 17.1271 6.25307 15.9603 4.93829 14.4798C3.64922 13.0282 2.75 11.3345 2.75 9.1371H1.25C1.25 11.8026 2.3605 13.8361 3.81672 15.4758C5.24723 17.0866 7.07077 18.3752 8.49742 19.4999L9.42605 18.3219ZM2.75 9.1371C2.75 6.98623 3.96537 5.18252 5.62436 4.42419C7.23607 3.68748 9.40166 3.88258 11.4596 6.02073L12.5404 4.98053C10.0985 2.44352 7.26409 2.02539 5.00076 3.05996C2.78471 4.07292 1.25 6.42503 1.25 9.1371H2.75ZM8.49742 19.4999C9.00965 19.9037 9.55954 20.3343 10.1168 20.6599C10.6739 20.9854 11.3096 21.25 12 21.25V19.75C11.6904 19.75 11.3261 19.6293 10.8736 19.3648C10.4213 19.1005 9.95208 18.7366 9.42605 18.3219L8.49742 19.4999ZM15.5026 19.4999C16.9292 18.3752 18.7528 17.0866 20.1833 15.4758C21.6395 13.8361 22.75 11.8026 22.75 9.1371H21.25C21.25 11.3345 20.3508 13.0282 19.0617 14.4798C17.7469 15.9603 16.0896 17.1271 14.574 18.3219L15.5026 19.4999ZM22.75 9.1371C22.75 6.42503 21.2153 4.07292 18.9992 3.05996C16.7359 2.02539 13.9015 2.44352 11.4596 4.98053L12.5404 6.02073C14.5983 3.88258 16.7639 3.68748 18.3756 4.42419C20.0346 5.18252 21.25 6.98623 21.25 9.1371H22.75ZM14.574 18.3219C14.0479 18.7366 13.5787 19.1005 13.1264 19.3648C12.6739 19.6293 12.3096 19.75 12 19.75V21.25C12.6904 21.25 13.3261 20.9854 13.8832 20.6599C14.4405 20.3343 14.9903 19.9037 15.5026 19.4999L14.574 18.3219Z" fill="#1C274C"/>
                </svg>
         		</div><div id="watchlist-off-'.$anime_item['news_id'].'" onclick="FastList(\''.$anime_item['news_id'].'\', \'minus\'); return false;" class="calendar__item-bookmark watchlist_btn watchlist_btn_success">
            	<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="none">
                    <path d="M2 9.1371C2 14 6.01943 16.5914 8.96173 18.9109C10 19.7294 11 20.5 12 20.5C13 20.5 14 19.7294 15.0383 18.9109C17.9806 16.5914 22 14 22 9.1371C22 4.27416 16.4998 0.825464 12 5.50063C7.50016 0.825464 2 4.27416 2 9.1371Z" fill="#1C274C"/>
                </svg>
         		</div>';
     		else $add_to_list = '<div id="watchlist-on-'.$anime_item['news_id'].'" onclick="FastList(\''.$anime_item['news_id'].'\', \'plus\'); return false;" class="calendar__item-bookmark watchlist_btn">
            	<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="none">
                	<path d="M8.96173 18.9109L9.42605 18.3219L8.96173 18.9109ZM12 5.50063L11.4596 6.02073C11.601 6.16763 11.7961 6.25063 12 6.25063C12.2039 6.25063 12.399 6.16763 12.5404 6.02073L12 5.50063ZM15.0383 18.9109L15.5026 19.4999L15.0383 18.9109ZM9.42605 18.3219C7.91039 17.1271 6.25307 15.9603 4.93829 14.4798C3.64922 13.0282 2.75 11.3345 2.75 9.1371H1.25C1.25 11.8026 2.3605 13.8361 3.81672 15.4758C5.24723 17.0866 7.07077 18.3752 8.49742 19.4999L9.42605 18.3219ZM2.75 9.1371C2.75 6.98623 3.96537 5.18252 5.62436 4.42419C7.23607 3.68748 9.40166 3.88258 11.4596 6.02073L12.5404 4.98053C10.0985 2.44352 7.26409 2.02539 5.00076 3.05996C2.78471 4.07292 1.25 6.42503 1.25 9.1371H2.75ZM8.49742 19.4999C9.00965 19.9037 9.55954 20.3343 10.1168 20.6599C10.6739 20.9854 11.3096 21.25 12 21.25V19.75C11.6904 19.75 11.3261 19.6293 10.8736 19.3648C10.4213 19.1005 9.95208 18.7366 9.42605 18.3219L8.49742 19.4999ZM15.5026 19.4999C16.9292 18.3752 18.7528 17.0866 20.1833 15.4758C21.6395 13.8361 22.75 11.8026 22.75 9.1371H21.25C21.25 11.3345 20.3508 13.0282 19.0617 14.4798C17.7469 15.9603 16.0896 17.1271 14.574 18.3219L15.5026 19.4999ZM22.75 9.1371C22.75 6.42503 21.2153 4.07292 18.9992 3.05996C16.7359 2.02539 13.9015 2.44352 11.4596 4.98053L12.5404 6.02073C14.5983 3.88258 16.7639 3.68748 18.3756 4.42419C20.0346 5.18252 21.25 6.98623 21.25 9.1371H22.75ZM14.574 18.3219C14.0479 18.7366 13.5787 19.1005 13.1264 19.3648C12.6739 19.6293 12.3096 19.75 12 19.75V21.25C12.6904 21.25 13.3261 20.9854 13.8832 20.6599C14.4405 20.3343 14.9903 19.9037 15.5026 19.4999L14.574 18.3219Z" fill="#1C274C"/>
                </svg>
         		</div><div id="watchlist-off-'.$anime_item['news_id'].'" style="display:none;" onclick="FastList(\''.$anime_item['news_id'].'\', \'minus\'); return false;" class="calendar__item-bookmark watchlist_btn watchlist_btn_success">
            	<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="none">
                    <path d="M2 9.1371C2 14 6.01943 16.5914 8.96173 18.9109C10 19.7294 11 20.5 12 20.5C13 20.5 14 19.7294 15.0383 18.9109C17.9806 16.5914 22 14 22 9.1371C22 4.27416 16.4998 0.825464 12 5.50063C7.50016 0.825464 2 4.27416 2 9.1371Z" fill="#1C274C"/>
                </svg>
         		</div>';
      		if ( ($position+1) > 6 ) $tdhid = ' style="display:none"';
        	else $tdhid = '';
      		if ( $anime_day_name == $today_name ) $anime_list_today .= '<div class="calendar__item-anime watchlist_parent list_today"'.$tdhid.'>
         		<div class="calendar__item-number">'.$position.'.</div>
         		<a href="'.$anime_item['full_link'].'" class="calendar__item-img">
         		<img src="'.$poster.'" alt="Постер '.$anime_item['russian'].'">
         		</a>
         		<div class="calendar__item-names">
            		<a href="'.$anime_item['full_link'].'" class="calendar__item-name">'.$anime_item['russian'].'</a>
            		<div class="calendar__item-english-name">'.$anime_item['original'].'</div>
            		<div class="calendar__item-english-name">Ожидается: '.$anime_item['next_episode'].' серия</div>
         		</div>
         		<div class="calendar__item-date">
            		<div class="calendar__item-day">'.date('j', strtotime($anime_item['next_date'])).'</div>
            		<div class="calendar__item-month">'.langdate('F', strtotime($anime_item['next_date'])).'</div>
         		</div>
         		'.$add_to_list.'
      		</div>';
      		else $anime_list_tomorrow .= '<div class="calendar__item-anime watchlist_parent list_tomorrow"'.$tdhid.'>
         		<div class="calendar__item-number">'.$position.'.</div>
         		<a href="'.$anime_item['full_link'].'" class="calendar__item-img">
         		<img src="'.$poster.'" alt="Постер '.$anime_item['russian'].'">
         		</a>
         		<div class="calendar__item-names">
            		<a href="'.$anime_item['full_link'].'" class="calendar__item-name">'.$anime_item['russian'].'</a>
            		<div class="calendar__item-english-name">'.$anime_item['original'].'</div>
            		<div class="calendar__item-english-name">Ожидается: '.$anime_item['next_episode'].' серия</div>
         		</div>
         		<div class="calendar__item-date">
            		<div class="calendar__item-day">'.date('j', strtotime($anime_item['next_date'])).'</div>
            		<div class="calendar__item-month">'.langdate('F', strtotime($anime_item['next_date'])).'</div>
         		</div>
         		'.$add_to_list.'
      		</div>';

      		$position++;
    	}
  		$anime_list .= '</div>';
  		if ($raspisanie['anime_list']) {
          if ( $rnum == 0 && count($anime_list_mas) > 5 ) $anime_list_today .= '<span id="show-more-today" class="show-more-schedules">Показать все</span>';
          elseif ( $rnum == 1 && count($anime_list_mas) > 5 ) $anime_list_tomorrow .= '<span id="show-more-tomorrow" class="show-more-schedules">Показать все</span>';
			}
    }

	$tpl->set ( '{today-ongoings}', $anime_list_today );
	$tpl->set ( '{tomorrow-ongoings}', $anime_list_tomorrow );
}
else {
  	$tpl->set ( '{today-ongoings}', '' );
	$tpl->set ( '{tomorrow-ongoings}', '' );
}

if (file_exists(ENGINE_DIR.'/mrdeath/aaparser/data/updates_history.json') && $aaparser_config['updates_block']['enable_history'] == 1) {
    $updates_history = json_decode( file_get_contents( ENGINE_DIR .'/mrdeath/aaparser/data/updates_history.json' ), true );
    if ( is_array($updates_history) && $updates_history ) {
        $kodik_updates_block = '';
        $first_block_styles = 'show';
        $inum = 1;
        $today_date_is = date('Y-m-d', $_TIME);
        $tempdate = strtotime($today_date_is);
        $yesterday_date_is = date('Y-m-d', strtotime("-1 day", $tempdate));
        foreach ( $updates_history as $upd_day => $upd_items ) {
            if ( $first_block_styles ) $first_block_text = 'Свернуть';
            else $first_block_text = 'Развернуть';
            if ( $upd_day == $today_date_is ) $kodik_updates_block .= '<div class="card-header last-update-header di-flex align-items-center">
                    <div class="h6 di-flex mr-auto mb-0">
                        <span>
                            <span class="mr-1">Сегодня</span>
                            <span class="d-none d-xl-inline">('.date('d.m.Y', strtotime($upd_day)).')</span>
                        </span>
                    </div>
                    <a class="bb-dashed-1" onclick="kodik_block_collapse(\''.$inum.'\', this);">'.$first_block_text.'</a>
                    </div>
                    <div id="kodik_block_day_'.$inum.'" style="max-height: 649px;" class="last-update-container scroll collapse '.$first_block_styles.'">';
            elseif ( $upd_day == $yesterday_date_is ) $kodik_updates_block .= '<div class="card-header last-update-header di-flex align-items-center">
                    <div class="h6 di-flex mr-auto mb-0">
                        <span>
                            <span class="mr-1">Вчера</span>
                            <span class="d-none d-xl-inline">('.date('d.m.Y', strtotime($upd_day)).')</span>
                        </span>
                    </div>
                    <a class="bb-dashed-1" onclick="kodik_block_collapse(\''.$inum.'\', this);">'.$first_block_text.'</a>
                    </div>
                    <div id="kodik_block_day_'.$inum.'" style="max-height: 649px;" class="last-update-container scroll collapse '.$first_block_styles.'">';
            else $kodik_updates_block .= '<div class="card-header last-update-header di-flex align-items-center">
                    <div class="h6 di-flex mr-auto mb-0">
                        <span>
                            <span class="mr-1">'.date('d.m.Y', strtotime($upd_day)).'</span>
                        </span>
                    </div>
                    <a class="bb-dashed-1" onclick="kodik_block_collapse(\''.$inum.'\', this);">'.$first_block_text.'</a>
                    </div>
                    <div id="kodik_block_day_'.$inum.'" style="max-height: 649px;" class="last-update-container scroll collapse '.$first_block_styles.'">';
            $first_block_styles = '';
            foreach ( $upd_items as $upd_item ) {
                $kodik_updates_block .= '<div class="last-update-item list-group-item list-group-item-action border-left-0 border-right-0 border-bottom-0 border-top-0 cursor-pointer" onclick="location.href=\''.$upd_item['link'].'\'" tabindex="-1">
                  <div class="media w-100 align-items-center">
                     <div class="media-left last-update-img mr-2">
                        <div class="img-square lazy br-50" style="background-image: url('.$upd_item['image'].');"></div>
                     </div>
                     <div class="media-body">
                        <div class="di-flex align-items-center">
                           <div class="di-flex mr-auto"><span class="card-link list-group-item-action bg-transparent"><span class="last-update-title font-weight-600">'.$upd_item['title'].'</span></span></div>
                           <div class="ml-3 text-right">
                              <div class="font-weight-600 text-truncate"><span class="season-info">'.$upd_item['season'].' сезон </span>'.$upd_item['episode'].' серия</div>
                              <div class="text-gray-dark-6">('.$upd_item['translation'].')</div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>';
            }
            $kodik_updates_block .= '</div>';
            $inum++;
        }
        $tpl->set ( '{kodik_updates_block}', $kodik_updates_block );
        $tpl->set ( '[kodik_updates_block]', "" );
        $tpl->set ( '[/kodik_updates_block]', "" );
    } else {
        $tpl->set ( '{kodik_updates_block}', "" );
        $tpl->set_block( "'\\[kodik_updates_block\\](.*?)\\[/kodik_updates_block\\]'si", "" );
    }
} else {
    $tpl->set ( '{kodik_updates_block}', "" );
    $tpl->set_block( "'\\[kodik_updates_block\\](.*?)\\[/kodik_updates_block\\]'si", "" );
}