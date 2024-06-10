<?php

if( !defined('DATALIFEENGINE') ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$dle_module = 'schedule';

$url_page = $config['http_home_url'] . "schedule";
$user_query = "do=schedule";

$metatags['title'] = 'Расписание выхода аниме';

$page_description = $metatags_description = $metatags['title'];

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

$day_of_weeks = [
	'monday' => 'Понедельник',
	'tuesday' => 'Вторник',
	'wednesday' => 'Среда',
	'thursday' => 'Четверг',
	'friday' => 'Пятница',
	'saturday' => 'Суббота',
	'sunday' => 'Воскресенье',
];

if ( $member_id['favorites'] ) $my_list = explode(',', $member_id['favorites']);
else $my_list = [];

$tpl2 = new dle_template();
$tpl2->dir = TEMPLATE_DIR;

$tpl2->load_template( 'schedule.tpl' );

$calendar_mas = [];
$anime_list = '';

foreach ( $raspisanie_ongoingov as $rnum => $raspisanie ) {
  	if ( $rnum == 0 ) $active_day = ' calendar-active';
  	else $active_day = '';
  	$calendar_mas[] = '<div class="calendar-date__item1'.$active_day.'" id="calendar-'.$raspisanie['day_name'].'" onclick="ScheduleChange(\''.$raspisanie['day_name'].'\'); return false;">
         <div class="calendar-date__item-day1">'.$day_of_weeks[$raspisanie['day_name']].'</div>
         <div class="calendar-date__item-month1">'.langdate('j F', strtotime($raspisanie['date'])).'</div>
      </div>';
  	if ( $active_day ) $anime_list .= '<div class="calendar__item" style="display: block;" id="calendar-list-'.$raspisanie['day_name'].'">';
  	else $anime_list .= '<div class="calendar__item" style="display: none;" id="calendar-list-'.$raspisanie['day_name'].'">';
  	$position = 1;
  	$anime_list_mas = json_decode($raspisanie['anime_list'], true);
  	foreach ( $anime_list_mas as $anime_item ) {
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
      	$anime_list .= '<div class="calendar__item-anime">
         <div class="calendar__item-number">'.$position.'.</div>
         <a href="'.$anime_item['full_link'].'" class="calendar__item-img">
         <img src="'.$poster.'" alt="Постер '.$anime_item['russian'].'">
         </a>
         <div class="calendar__item-names">
            <a href="'.$anime_item['full_link'].'" class="calendar__item-name">'.$anime_item['russian'].'</a>
            <div class="calendar__item-english-name">'.$anime_item['original'].'</div>
         </div>
         <div class="calendar__item-episode">
            <div class="calendar__item-serie">'.$anime_item['next_episode'].' серия</div>
            <div class="calendar__item-time">В '.date('H:i', strtotime($anime_item['next_date'])).'</div>
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
}

$tpl2->set( '{calendar-date}', implode('', $calendar_mas) );
$tpl2->set( '{anime-list}', $anime_list );

$tpl2->compile( 'schedule', true, false );

$tpl->result['content'] = $tpl2->result['schedule'].$tpl->result['content'];