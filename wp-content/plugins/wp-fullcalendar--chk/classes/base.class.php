<?php
/*
 * DP Pro Event Calendar
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: http://www.dpereyra.com
 * @Email: info@dpereyra.com
 *
 * Base Class
 */

require_once('dates.class.php');

class DpProEventCalendar {
	
	var $nonce;
	var $is_admin = false;
	var $type = 'calendar';
	var $limit = 0;
	var $limit_description = 0;
	var $category = "";
	var $event_id = "";
	var $event = "";
	var $author = "";
	var $columns = 3;
	var $from = "";
	var $view = "";
	var $id_calendar = null;
	var $default_date = null;
	var $calendar_obj;
	var $wpdb = null;
	var $eventsByCurrDate = null;
	var $opts = array();
	var $datesObj;
	var $widget;
	
	var $translation;
	var $translation_orig;
	
	
	var $table_calendar;
	var $table_subscribers_calendar;
	
	function DpProEventCalendar( $is_admin = false, $id_calendar = null, $defaultDate = null, $translation = null, $widget = '', $category = '', $event_id = '', $author = '', $event = "", $columns = "", $from = "", $view = "", $limit_description = 0, $opts = array() ) 
	{
		global $table_prefix;
		
		$this->table_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		$this->table_events = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		$this->table_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
		$this->table_special_dates = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
		$this->table_special_dates_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
		$this->table_subscribers_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SUBSCRIBERS_CALENDAR;

		$this->translation = array( 
			'TXT_NO_EVENTS_FOUND' 		=> __('No Events were found.','dpProEventCalendar'),
			'TXT_ALL_DAY' 				=> __('All Day','dpProEventCalendar'),
			'TXT_REFERENCES' 			=> __('References','dpProEventCalendar'),
			'TXT_VIEW_ALL_EVENTS'		=> __('View all events','dpProEventCalendar'),
			'TXT_ALL_CATEGORIES'		=> __('All Categories','dpProEventCalendar'),
			'TXT_MONTHLY'				=> __('Monthly','dpProEventCalendar'),
			'TXT_DAILY'					=> __('Daily','dpProEventCalendar'),
			'TXT_WEEKLY'				=> __('Weekly','dpProEventCalendar'),
			'TXT_ALL_WORKING_DAYS'		=> __('All working days','dpProEventCalendar'),
			'TXT_SEARCH' 				=> __('Search...','dpProEventCalendar'),
			'TXT_RESULTS_FOR' 			=> __('Results: ','dpProEventCalendar'),
			'TXT_BY' 					=> __('By','dpProEventCalendar'),
			'TXT_CURRENT_DATE'			=> __('Current Date','dpProEventCalendar'),
			'TXT_BOOK_EVENT'			=> __('Book Event','dpProEventCalendar'),
			'TXT_BOOK_EVENT_REMOVE'		=> __('Remove Booking','dpProEventCalendar'),
			'TXT_BOOK_EVENT_SAVED'		=> __('Booking saved successfully.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_REMOVED'	=> __('Booking removed successfully.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_SELECT_DATE'=> __('Select Date:','dpProEventCalendar'),
			'TXT_BOOK_EVENT_PICK_DATE'	=> __('Click to book on this date.','dpProEventCalendar'),
			'TXT_BOOK_TICKETS_REMAINING'=> __('Tickets Remaining','dpProEventCalendar'),
			'TXT_BOOK_ALREADY_BOOKED'	=> __('You have already booked this event date.','dpProEventCalendar'),
			'TXT_BOOK_EVENT_COMMENT'	=> __('Leave a comment (optional):','dpProEventCalendar'),
			'TXT_CATEGORY'				=> __('Category','dpProEventCalendar'),
			'TXT_SUBSCRIBE'				=> __('Subscribe','dpProEventCalendar'),
			'TXT_SUBSCRIBE_SUBTITLE'	=> __('Receive new events notifications in your email.','dpProEventCalendar'),
			'TXT_YOUR_NAME'				=> __('Your Name','dpProEventCalendar'),
			'TXT_YOUR_EMAIL'			=> __('Your Email','dpProEventCalendar'),
			'TXT_FIELDS_REQUIRED'		=> __('All fields are required.','dpProEventCalendar'),
			'TXT_INVALID_EMAIL'			=> __('The Email is invalid.','dpProEventCalendar'),
			'TXT_SUBSCRIBE_THANKS'		=> __('Thanks for subscribing.','dpProEventCalendar'),
			'TXT_SENDING'				=> __('Sending...','dpProEventCalendar'),
			'TXT_SEND'					=> __('Send','dpProEventCalendar'),
			'TXT_ADD_EVENT'				=> __('+ Add Event','dpProEventCalendar'),
			'TXT_EDIT_EVENT'			=> __('Edit Event','dpProEventCalendar'),
			'TXT_REMOVE_EVENT'			=> __('Remove Event','dpProEventCalendar'),
			'TXT_REMOVE_EVENT_CONFIRM'	=> __('Are you sure that you want to delete this event?','dpProEventCalendar'),
			'TXT_CANCEL'				=> __('Cancel','dpProEventCalendar'),
			'TXT_YES'					=> __('Yes','dpProEventCalendar'),
			'TXT_NO'					=> __('No','dpProEventCalendar'),
			'TXT_EVENT_LOGIN'			=> __('You must be logged in to submit an event.','dpProEventCalendar'),
			'TXT_EVENT_THANKS'			=> __('Thanks for your event submission. It will be reviewed soon.','dpProEventCalendar'),
			'TXT_EVENT_TITLE'			=> __('Title','dpProEventCalendar'),
			'TXT_EVENT_DESCRIPTION'		=> __('Event Description','dpProEventCalendar'),
			'TXT_EVENT_IMAGE'			=> __('Upload an Image (optional)','dpProEventCalendar'),
			'TXT_EVENT_LINK'			=> __('Link (optional)','dpProEventCalendar'),
			'TXT_EVENT_SHARE'			=> __('Text to share in social networks (optional)','dpProEventCalendar'),
			'TXT_EVENT_LOCATION'		=> __('Location (optional)','dpProEventCalendar'),
			'TXT_EVENT_PHONE'			=> __('Phone (optional)','dpProEventCalendar'),
			'TXT_EVENT_GOOGLEMAP'		=> __('Google Map (optional)','dpProEventCalendar'),
			'TXT_EVENT_START_DATE'		=> __('Start Date','dpProEventCalendar'),
			'TXT_EVENT_ALL_DAY'			=> __('Set if the event is all the day.','dpProEventCalendar'),
			'TXT_EVENT_START_TIME'		=> __('Start Time','dpProEventCalendar'),
			'TXT_EVENT_HIDE_TIME'		=> __('Hide Time','dpProEventCalendar'),
			'TXT_EVENT_END_TIME'		=> __('End Time','dpProEventCalendar'),
			'TXT_EVENT_FREQUENCY'		=> __('Frequency','dpProEventCalendar'),
			'TXT_NONE'					=> __('None','dpProEventCalendar'),
			'TXT_EVENT_DAILY'			=> __('Daily','dpProEventCalendar'),
			'TXT_EVENT_WEEKLY'			=> __('Weekly','dpProEventCalendar'),
			'TXT_EVENT_MONTHLY'			=> __('Monthly','dpProEventCalendar'),
			'TXT_EVENT_YEARLY'			=> __('Yearly','dpProEventCalendar'),
			'TXT_EVENT_END_DATE'		=> __('End Date','dpProEventCalendar'),
			'TXT_SUBMIT_FOR_REVIEW'		=> __('Submit for Review','dpProEventCalendar'),
			'PREV_MONTH' 				=> __('Prev Month','dpProEventCalendar'),
			'NEXT_MONTH'				=> __('Next Month','dpProEventCalendar'),
			'PREV_DAY' 					=> __('Prev Day','dpProEventCalendar'),
			'NEXT_DAY'					=> __('Next Day','dpProEventCalendar'),
			'PREV_WEEK'					=> __('Prev Week','dpProEventCalendar'),
			'NEXT_WEEK'					=> __('Next Week','dpProEventCalendar'),
			'DAY_SUNDAY' 				=> __('Sunday','dpProEventCalendar'),
			'DAY_MONDAY' 				=> __('Monday','dpProEventCalendar'),
			'DAY_TUESDAY' 				=> __('Tuesday','dpProEventCalendar'),
			'DAY_WEDNESDAY' 			=> __('Wednesday','dpProEventCalendar'),
			'DAY_THURSDAY' 				=> __('Thursday','dpProEventCalendar'),
			'DAY_FRIDAY' 				=> __('Friday','dpProEventCalendar'),
			'DAY_SATURDAY' 				=> __('Saturday','dpProEventCalendar'),
			'MONTHS' 					=> array(
											__('January','dpProEventCalendar'),
											__('February','dpProEventCalendar'),
											__('March','dpProEventCalendar'),
											__('April','dpProEventCalendar'),
											__('May','dpProEventCalendar'),
											__('June','dpProEventCalendar'),
											__('July','dpProEventCalendar'),
											__('August','dpProEventCalendar'),
											__('September','dpProEventCalendar'),
											__('October','dpProEventCalendar'),
											__('November','dpProEventCalendar'),
											__('December','dpProEventCalendar')
										)
	   );
	   
	   $this->translation_orig = $this->translation;


		$this->widget = $widget;
		if($is_admin) { $this->is_admin = true; }
		if($view != "") { $this->view = $view; }
		if(is_numeric($id_calendar)) { $this->setCalendar($id_calendar); }
		if(!isset($defaultDate)) { $defaultDate = $this->getDefaultDate(); }
		$this->defaultDate = $defaultDate;
		if(isset($translation)) { $this->translation = $translation; }
		if(isset($category)) { $this->category = $category; }
		if(isset($event_id)) { $this->event_id = $event_id; }
		if(isset($event)) { $this->event = $event; }
		if(isset($columns)) { $this->columns = $columns; }
		if(isset($from)) { $this->from = $from; }
		if(isset($author)) { $this->author = $author; }
		if(isset($limit_description)) { $this->limit_description = $limit_description; }
		$this->opts = $opts;
		
		$this->nonce = rand();
		
		$this->datesObj = new DPPEC_Dates($defaultDate);
		
		//die(print_r($this->datesObj));
    }
	
	function setCalendar($id) {
		$this->id_calendar = $id;	
		
		$this->getCalendarData();
		
		if(!$this->calendar_obj->enable_wpml) {
			
			$translation_fields = unserialize($this->calendar_obj->translation_fields);
			if(is_array($translation_fields)) {
				
				$this->translation = array();

				foreach ($translation_fields as $key => $value) {

					$this->translation[strtoupper($key)] = $value;

				}
			}
			
	   } else {
		   
			//echo get_locale().__('View all events','dpProEventCalendar');
			//die();   
	   }
	}
	
	function getNonce() {
		if(!is_numeric($this->id_calendar)) { return false; }
		
		return $this->nonce;
	}
	
	function getDefaultDate() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return time(); }
		
		$default_date;
		$querystr = "
		SELECT default_date
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	
		if(!empty($calendar_obj)) {
			foreach($calendar_obj as $key=>$value) { $$key = $value; }
		}

		if($default_date == "" || $default_date == "0000-00-00") { $default_date = current_time('timestamp'); } else { $default_date = strtotime($default_date); }
		return $default_date;
	}
	
	function getCalendarName() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return ""; }
		
		$default_date;
		$querystr = "
		SELECT title
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	
		
		if(!empty($calendar_obj)) {
			foreach($calendar_obj as $key=>$value) { $$key = $value; }
		}
		return $title;
	}
	
	function getCalendarData() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return time(); }
		
		$querystr = "
		SELECT *
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	

		$this->calendar_obj = $calendar_obj;
		
		if($this->view != "") {
			$this->calendar_obj->view = $this->view;
		}
		
	}
	
	function getCalendarByEvent($event_id) {
		global $wpdb;
		
		$calendar_id;
		$meta_key = "SELECT meta_value FROM ".$wpdb->postmeta." WHERE post_id = p.ID AND meta_key";
		$querystr = "
		SELECT (".$meta_key." = 'pec_id_calendar') as id_calendar
		FROM ".$wpdb->posts." p
		WHERE p.ID = ".$event_id;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	
		foreach($calendar_obj as $key=>$value) { $$key = $value; }
		
		$id_calendar = explode(',', $id_calendar);
		$id_calendar = $id_calendar[0];
		
		if($id_calendar == "") { $calendar_id = false; } else { $calendar_id = $id_calendar; }
		return $calendar_id;
	}
	
	function getEventData($event_id) {
		global $wpdb;
		$meta_key = "SELECT meta_value FROM ".$wpdb->postmeta." WHERE post_id = p.ID AND meta_key";
		$querystr = "
		SELECT 	p.ID as id, 
				p.post_title as title, 
				p.post_content as description, 
				(".$meta_key." = 'pec_id_calendar' LIMIT 1) as id_calendar, 
				(".$meta_key." = 'pec_date' LIMIT 1) as date, 
				(".$meta_key." = 'pec_all_day' LIMIT 1) as all_day, 
				(".$meta_key." = 'pec_daily_working_days' LIMIT 1) as pec_daily_working_days, 
				(".$meta_key." = 'pec_daily_every' LIMIT 1) as pec_daily_every, 
				(".$meta_key." = 'pec_weekly_every' LIMIT 1) as pec_weekly_every,
				(".$meta_key." = 'pec_weekly_day' LIMIT 1) as pec_weekly_day, 
				(".$meta_key." = 'pec_monthly_every' LIMIT 1) as pec_monthly_every,
				(".$meta_key." = 'pec_monthly_position' LIMIT 1) as pec_monthly_position,
				(".$meta_key." = 'pec_monthly_day' LIMIT 1) as pec_monthly_day,
(".$meta_key." = 'pec_exceptions' LIMIT 1) as pec_exceptions,
				(".$meta_key." = 'pec_recurring_frecuency' LIMIT 1) as recurring_frecuency, 
				(".$meta_key." = 'pec_end_date' LIMIT 1) as end_date, 
				(".$meta_key." = 'pec_link' LIMIT 1) as link, 
				(".$meta_key." = 'pec_share' LIMIT 1) as share, 
				(".$meta_key." = 'pec_map' LIMIT 1) as map, 
				(".$meta_key." = 'pec_end_time_hh' LIMIT 1) as end_time_hh, 
				(".$meta_key." = 'pec_end_time_mm' LIMIT 1) as end_time_mm, 
				(".$meta_key." = 'pec_hide_time' LIMIT 1) as hide_time, 
				(".$meta_key." = 'pec_location' LIMIT 1) as location, 
				(".$meta_key." = 'pec_phone' LIMIT 1) as phone
		FROM ".$wpdb->posts." p
		WHERE p.ID = ".$event_id;
		
		$event_obj = $wpdb->get_results($querystr, OBJECT);
		$event_obj = $event_obj[0];	
		
		return $event_obj;
	}
	
	function getFormattedEventData($get = "") {
		global $wpdb, $post, $dp_pec_payments, $dpProEventCalendar;
		$post_id = $post->ID;

		$return = "";
		$event_data = $this->getEventData($post_id);
		
		$pec_fb_event = get_post_meta($post_id, 'pec_fb_event', true);
		
		switch($get) {
			case 'location':
				if($event_data->location != "") {
					$return = '<div class="pec_event_page_location"><p><i class="fa fa-map-marker"></i>'.$event_data->location.'</p></div>';
				}
				break;
			case 'phone':
				if($event_data->phone != "") {
					$return = '<div class="pec_event_page_phone"><p><i class="fa fa-phone"></i>'.$event_data->phone.'</p></div>';
				}
				break;
			case 'facebook_url':
				if($pec_fb_event != "") {
					$return = '<div class="pec_event_page_facebook_url"><p><i class="fa fa-facebook"></i><a href="'.$pec_fb_event.'" target="_blank">'.str_replace(array('http://', 'https://'), '', $pec_fb_event).'</a></p></div>';
				}
				break;
			case 'custom_fields':
				if(is_array($dpProEventCalendar['custom_fields_counter'])) {
					$counter = 0;
					
					foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
						$field_value = get_post_meta($post_id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
						if($field_value != "") {
							$return .= '<div class="pec_event_page_custom_fields"><strong>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</strong>';
							if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
								$return .= '<i class="fa fa-check"></i>';
							} else {
								$return .= '<p>'.$field_value.'</p>';
							}
							$return .= '</div>';
						}
						$counter++;		
					}
				}
				break;
			case 'link':
				if($event_data->link != "") {
					$formated_link = str_replace(array('http://', 'https://'), '', $event_data->link);
					if(strlen($formated_link) > 25) {
						$formated_link = substr(str_replace(array('http://', 'https://'), '', $event_data->link), 0, 25).'...';	
					}
					$return = '<div class="pec_event_page_link"><p><i class="fa fa-link"></i><a href="'.$event_data->link.'" target="_blank" rel="nofollow">'.$formated_link.'</a></p></div>';
				}
				break;
			case 'categories':
				$category = get_the_terms( $post_id, 'pec_events_category' ); 
				$html = "";
				if(!empty($category)) {
					$category_count = 0;
					$html .= '
					<div class="pec_event_page_categories">
						<p>';
					foreach ( $category as $cat){
						if($category_count > 0) {
							$html .= " / ";	
						}
						$html .= $cat->name;
						$category_count++;
					}
					$html .= '
						</p>
					</div>';
				}
				$return = $html;
				break;
			case 'frequency':
				if($event_data->recurring_frecuency != "") {
					switch($event_data->recurring_frecuency) {
						case 1:
							$return = $this->translation['TXT_EVENT_DAILY'];
							break;	
						case 2:
							$return = $this->translation['TXT_EVENT_WEEKLY'];
							break;	
						case 3:
							$return = $this->translation['TXT_EVENT_MONTHLY'];
							break;	
						case 4:
							$return = $this->translation['TXT_EVENT_YEARLY'];
							break;	
					}
				}
				break;
			case 'map':
				if($event_data->map != "") {
					
					if(get_post_meta($post_id, 'pec_map_lnlat', true) != "") {
						//$event_data->map = str_replace(" ", "", get_post_meta($post_id, 'pec_map_lnlat', true));
					}
					$return = '
					<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>
					<iframe class="dp_pec_date_event_map_iframe" width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_q&q='.urlencode($event_data->map).'&ie=UTF8&output=embed"></iframe>';
				}
				break;
			case 'rating':
				$rate = get_post_meta($post_id, 'pec_rate', true);
				if($rate != '') {
					$return .= '
					<ul class="dp_pec_rate">
						<li><a href="javascript:void(0);" '.($rate >= 1 ? 'class="dp_pec_rate_full"' : '').'></a></li>
						<li><a href="javascript:void(0);" '.($rate >= 2 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 1 && $rate < 2 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="javascript:void(0);" '.($rate >= 3 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 2 && $rate < 3 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="javascript:void(0);" '.($rate >= 4 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 3 && $rate < 4 ? 'class="dp_pec_rate_h"' : '').'></a></li>
						<li><a href="javascript:void(0);" '.($rate >= 5 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 4 && $rate < 5 ? 'class="dp_pec_rate_h"' : '').'></a></li>
					</ul>
					<div class="dp_pec_clear"></div>';
				}
				break;
			case 'ical':
				$return = "";
				if($this->calendar_obj->ical_active) {
					if(is_numeric($_GET['event_date'])) {
						$ical_date = $_GET['event_date'];
					} else {
						$ical_date = strtotime($event_data->date);	
					}
					$return .= "<a class='dpProEventCalendar_single_head_btn' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$post_id.'&date='.$ical_date ) . "'>iCal</a>
					<div class='dp_pec_clear'></div>";
				}
				break;
			case 'date':
				
				if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event_data->date));
				} else {
					$time = date('H:i', strtotime($event_data->date));				
				}
				
				if(is_numeric($_GET['event_date'])) {
					$event_data->date = date('Y-m-d H:i:s', $_GET['event_date']);
				}
				
				$end_date = '';
				$end_year = '';
				if($event_data->end_date != "" && $event_data->end_date != "0000-00-00" && $event_data->recurring_frecuency == 1) {
					$end_day = date('d', strtotime($event_data->end_date));
					$end_month = date('n', strtotime($event_data->end_date));
					$end_year = date('Y', strtotime($event_data->end_date));
					
					//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
					$end_date = ' / '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_data->end_date));
				}
									
				$end_time = "";
				if($event_data->end_time_hh != "" && $event_data->end_time_mm != "") { $end_time = str_pad($event_data->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event_data->end_time_mm, 2, "0", STR_PAD_LEFT); }
				
				if($end_time != "") {
					
					if($this->calendar_obj->format_ampm) {
						$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
					} else {
						$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
					}
					$end_time = " / ".$end_time_tmp;
					if($end_time_tmp == $time) {
						$end_time = "";	
					}
				}

				if($event_data->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
					$end_time = "";
				}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
					$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
				}

				$return .= '<div class="pec_event_page_date">'.
								'<p><i class="fa fa-clock-o"></i>'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event_data->date)).$end_date.' - 
								'.$all_working_days.' '.((($this->calendar_obj->show_time && !$event_data->hide_time) || $event_data->all_day) ? $time.$end_time : '').'</p>'.
						   '</div>';
				
				$return .= $this->getBookingButton($post_id, date('Y-m-d', strtotime($event_data->date)));
				
				$return .= '
						   <div class="dp_pec_clear"></div>';

				break;
			case 'time':
				if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event_data->date));
				} else {
					$time = date('H:i', strtotime($event_data->date));				
				}
													
				$end_time = "";
				if($event_data->end_time_hh != "" && $event_data->end_time_mm != "") { $end_time = str_pad($event_data->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event_data->end_time_mm, 2, "0", STR_PAD_LEFT); }
				
				if($end_time != "") {
					
					if($this->calendar_obj->format_ampm) {
						$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
					} else {
						$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
					}
					$end_time = " / ".$end_time_tmp;
					if($end_time_tmp == $time) {
						$end_time = "";	
					}
				}

				if($event_data->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$return .= '<div class="pec_event_page_date">'.
								'<p>'.$all_working_days.' '.((($this->calendar_obj->show_time && !$event_data->hide_time) || $event_data->all_day) ? $time.$end_time : '').'</p>'.
						   '</div>
						   <div class="dp_pec_clear"></div>';

				break;
		}
		
		return $return;
	}
	
	function getBookingsByUser($user_id) {
		global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
		
		$querystr = "
            SELECT *
            FROM ".$this->table_booking."
			WHERE id_user = ".$user_id." AND event_date >= CURRENT_DATE AND status <> 'pending'
			ORDER BY event_date ASC
            ";
		if(is_numeric($this->limit) && $this->limit > 0) {
			$querystr .= "LIMIT ".$this->limit;
		}
        $bookings_obj = $wpdb->get_results($querystr, OBJECT);
		
		return  $bookings_obj;
	}
	
	function getBookingsCount($post_id, $date) {
		global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
		
		if(!is_numeric($post_id)) {
			return 0;	
		}
		
		$querystr = "
            SELECT SUM(quantity) as counter
            FROM ".$this->table_booking."
			WHERE id_event = ".$post_id." AND event_date = '".$date."' AND status <> 'pending'
            ";
        $bookings_obj = $wpdb->get_results($querystr, OBJECT);
		
		return $bookings_obj[0]->counter;
	}
	
	function getBookingButton($post_id, $date = '') {
		global $dp_pec_payments, $dpProEventCalendar;
		
		$hide_buttons = false;
		$return = '';
		
		if(
			(is_user_logged_in() || $this->calendar_obj->booking_non_logged) 
			&& 
			(
				(get_post_meta($post_id, 'pec_enable_booking', true) || $this->calendar_obj->booking_enable) 
				&& 
				( 
					get_post_meta($post_id, 'pec_recurring_frecuency', true) > 0 
					|| 
					strtotime(get_post_meta($post_id, 'pec_date', true)) > (int)current_time( 'timestamp' ) 
				)
			) 
			&&
			(strtotime($date. ' '.date('H:i:s', strtotime(get_post_meta($post_id, 'pec_date', true)))) >= (int)current_time( 'timestamp' ) )
		) {
			$calendar = $this->id_calendar;

			if(!is_numeric($calendar) || $calendar == 0) {
				$calendar = explode(",", get_post_meta($post_id, 'pec_id_calendar', true));
				$calendar = $calendar[0];
			}
			
			$event_single = "false";

			$this->event_id = $post_id;
			
			$max_upcoming_dates = $this->calendar_obj->booking_max_upcoming_dates;
			
			$event_dates = $this->upcomingCalendarLayout( true, (is_numeric($max_upcoming_dates) && $max_upcoming_dates > 0 ? $max_upcoming_dates : 10) );
			
			$autoselected_date = "";
			
			$default_date = null;
			
			if(count($event_dates) == 1) {
				
				if(!is_array($event_dates)) {
					$autoselected_date = substr(get_post_meta($post_id, 'pec_date', true), 0, 10);
					
				} else {
					$autoselected_date = substr($event_dates[0]->date, 0, 10);
				}
				
				$event_single = "true";
				
			}
			
			if(count($event_dates) > 0 || $date != '') {
				
				if(!is_array($event_dates) || $date != '') {
					if($date != "") {
						$default_date = $date;
					} else {
						$default_date = substr(get_post_meta($post_id, 'pec_date', true), 0, 10);
					}
				} else {
					$default_date = substr($event_dates[0]->date, 0, 10);
				}
				
			}
			
			$return .= '<div class="pec_event_page_book_wrapper"><a href="javascript:void(0);" class="pec_event_page_book" data-event-id="'.$post_id.'" data-event-single="'.$event_single.'"><i class="fa fa-calendar"></i><strong>'.$this->translation['TXT_BOOK_EVENT'].'</strong>' . (get_post_meta($post_id, 'pec_booking_price', true) > 0 && $dp_pec_payments['currency'] != "" ? ' <div class="pec_booking_price">'.get_post_meta($post_id, 'pec_booking_price', true). ' ' .$dp_pec_payments['currency'].'</div>' : '').'</a>
			';
			$return .= '
			<div class="pec_book_select_date">
				<div class="pec_modal_wrap_content">	
			';
			if(!is_user_logged_in()) {
				$return .= '<input type="text" value="" class="dpProEventCalendar_input dpProEventCalendar_from_name" id="pec_event_page_book_name" placeholder="'.$this->translation['TXT_YOUR_NAME'].'" />';	
				$return .= '<input type="text" value="" class="dpProEventCalendar_input dpProEventCalendar_from_email" id="pec_event_page_book_email" placeholder="'.$this->translation['TXT_YOUR_EMAIL'].'" />';	
			}
			
			if($this->calendar_obj->booking_show_phone) {
				$return .= '<input type="text" value="" class="dpProEventCalendar_input dpProEventCalendar_from_phone" id="pec_event_page_book_phone" placeholder="'.__('Your Phone', 'dpProEventCalendar').'" />';		
			}

			if(is_array($dpProEventCalendar['booking_custom_fields_counter'])) {
				$counter = 0;
				
				foreach($dpProEventCalendar['booking_custom_fields_counter'] as $key) {
	
					if($dpProEventCalendar['booking_custom_fields']['type'][$counter] == "checkbox") {

						$return .= '
						<div class="dp_pec_wrap_checkbox">
						<input type="checkbox" class="checkbox pec_event_page_book_extra_fields" value="1" id="" name="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['booking_custom_fields']['placeholder'][$counter].'
						</div>';
			
					} else {

						$return .= '
						<input type="text" class="dpProEventCalendar_input pec_event_page_book_extra_fields" value="" placeholder="'.$dpProEventCalendar['booking_custom_fields']['placeholder'][$counter].'" id="" name="pec_custom_'.$dpProEventCalendar['booking_custom_fields']['id'][$counter].'" />';
						
					}
					$counter++;		
				}
			}
			
			//$dpProEventCalendar_class = new DpProEventCalendar( false, (is_null($this->id_calendar) ? $calendar[0] : $this->id_calendar), strtotime($default_date), null, '', '', $post_id);
						
			if($event_single == "true") {
				
				if($this->userHasBooking($autoselected_date, $post_id)) {
					
					$return .= '<p>'.$this->translation['TXT_BOOK_ALREADY_BOOKED'].'</p>';
					
					$hide_buttons = true;
					
				} else {
					
					$return .= '<p>'.sprintf( __( 'Booking of %s' , 'dpProEventCalendar'), get_the_title($post_id) . ' <strong>('.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($autoselected_date)).')</strong>' ).'</p>';
					
				}
	
				$return .= '<div style="display:none;">';
			
			}
			
			/*$return .= '
			<span class="dp_pec_form_desc dp_pec_form_main_desc">'.$this->translation['TXT_BOOK_EVENT_SELECT_DATE'].'</span>
			';*/
						
			//$return .= $dpProEventCalendar_class->output();
		
			if($event_single == "true") {
				
				$return .= '</div>';
				
			}
			
			//$return .= $dpProEventCalendar_class->addScripts(false, true);
			
			$return .= '<style type="text/css">'.$dpProEventCalendar['custom_css'].'</style>';
			
			$return .= '
	
				<input type="hidden" name="pec_event_page_book_event_id" id="pec_event_page_book_event_id" value="'.$post_id.'" />
				<input type="hidden" name="pec_event_page_book_calendar" id="pec_event_page_book_calendar" value="'.$calendar.'" />
				';
			
			$this->event_id = "";
			
			if($this->calendar_obj->booking_comment) {
				$return .= '
				<span class="dp_pec_form_desc">'.$this->translation['TXT_BOOK_EVENT_COMMENT'].'</span>
				<textarea name="pec_event_page_book_comment" id="pec_event_page_book_comment" class="dpProEventCalendar_textarea"></textarea>';
			}
			$return .= '
				<div class="dp_pec_clear"></div>
				';
			
			$return .= '
				</div>';
				
			if(!$hide_buttons) {
				
				$return .= '
					<p class="pec_booking_date">
						'.$this->translation['TXT_BOOK_EVENT_SELECT_DATE'].'
						<select name="pec_event_page_book_date" id="pec_event_page_book_date" onchange="if(this.value == 0) { jQuery(\'.pec_event_page_send_booking\').prop(\'disabled\', true); } else { jQuery(\'.pec_event_page_send_booking\').prop(\'disabled\', false); }"> 
						';
				
				$booking_limit = get_post_meta($post_id, 'pec_booking_limit', true);
				$booking_available_first = 0;
				
				$count_event_dates = 0;
				if(!is_array($event_dates)) {
					
					$return .= '<option value="">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</option>';	
					
				} else {
					
					foreach($event_dates as $upcoming_date) {
						if($upcoming_date->id == "") {
							$upcoming_date->id = $upcoming_date->ID;
						}
						
						$booking_count = $this->getBookingsCount($upcoming_date->id, substr($upcoming_date->date, 0, 10));
						
						$booking_available = $booking_limit - $booking_count;
						
						if($booking_available < 0 || !is_numeric($booking_available)) {
							$booking_available = 0;	
						}
						
						if($count_event_dates == 0) {
							$booking_available_first = $booking_available;	
						}
						
						$option_value = ($booking_limit > 0 && $booking_available == 0 ? '0' : substr($upcoming_date->date, 0, 10));
						
						$return .= '<option value="'.$option_value.'" '.($default_date == $option_value ? 'selected="selected"' : '').'>'.dpProEventCalendar_date_i18n(get_option('date_format') . ($this->calendar_obj->show_time && !$upcoming_date->hide_time ? ' ' . get_option('time_format') : ''), strtotime($upcoming_date->date)).' '.($booking_limit > 0 ? '('.$booking_available.' '.$this->translation['TXT_BOOK_TICKETS_REMAINING'].')' : '').'</option>';	
						
						$count_event_dates++;
					
					}
				}
				$return .= '
						</select></p>
					
					<div class="dp_pec_clear"></div>
				';
				
				$return .= '
					<p class="pec_booking_quantity">
						<select name="pec_event_page_book_quantity" id="pec_event_page_book_quantity"> 
						';
				$booking_max_quantity = $this->calendar_obj->booking_max_quantity;

				if($this->calendar_obj->booking_max_quantity > $booking_available && is_numeric($booking_available) && $booking_available > 0) {
					$booking_max_quantity = $booking_available;
				}
				for($i = 1; $i <= $booking_max_quantity; $i++) {
					$return .= '<option value="'.$i.'">'.$i.'</option>';	
				}
				$return .= '
						</select>
						'.__('Quantity', 'dpProEventCalendar').'</p>
					
					<div class="dp_pec_clear"></div>
				';
				
				if(is_numeric($dpProEventCalendar['terms_conditions'])) {
					$return .= '
						<p><input type="checkbox" name="pec_event_page_book_terms_conditions" id="pec_event_page_book_terms_conditions" value="1" /> '.sprintf(__('I\'ve read and accept the %s terms & conditions %s', 'dpProEventCalendar'), '<a href="'.dpProEventCalendar_get_permalink($dpProEventCalendar['terms_conditions']).'" target="_blank">', '</a>').'</p>
						
						<div class="dp_pec_clear"></div>
					';
				}

				$return .= '
					<div class="pec-add-footer">
						<button class="pec_event_page_send_booking" '.($booking_limit > 0 && $booking_available_first == 0 ? 'disabled="disabled"' : '').'>'.(get_post_meta($post_id, 'pec_booking_price', true) ? apply_filters('pec_payments_send', $this->translation['TXT_SEND']) : $this->translation['TXT_SEND'] ).'</button>
						<div class="dp_pec_clear"></div>
					</div>';
			}
			$return .= '
				</div>
			</div>';
			
			$return .= '
			<div class="dp_pec_clear"></div>';
			
			// Attendees Counter
						
			if($this->calendar_obj->booking_display_attendees) {

				/*if(is_numeric($_GET['event_date'])) {
					$event_data->date = date('Y-m-d', $_GET['event_date']);
				}*/
				
				$attendees = $this->getEventBookings(true, $date, $post_id);
				
				//print_r($booking_obj);
				
				$return .= "<div class='dp_pec_attendees_counter dp_pec_attendees_counter_".$post_id."'><span>" . $attendees . '</span> ' . __('Attendees', 'dpProEventCalendar') . "</div>";
				
				$return .= '
				<div class="dp_pec_clear"></div>';

			}

		}	
		
		return $return;
	}
	
	/*function getCountEventsByDate($date) {
		global $wpdb;

		if(!is_numeric($this->id_calendar)) { return false; }
		if($this->is_admin) { return 0; }
		
		$event_count = 0;
		
		$events = $this->getEventsByDate($date);
		
		foreach($events as $event) {

			if($date != "" && $event->pec_exceptions != "") {
				$exceptions = explode(',', $event->pec_exceptions);
				
				if($event->recurring_frecuency != "" && in_array($date, $exceptions)) {
					continue;
				}
			}
							
			if($date != "" && $event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($date)) == "0" || date('w', strtotime($date)) == "6")) {
				continue;
			}
			
			if($date != "" && !$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
				( ((strtotime($date) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
			) {
				continue;
			}
						
			if($date != "" && $event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
				( ((strtotime($date) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
			) {
				continue;
			}
			
			if($date != "" && $event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
				( !is_int (((date('m', strtotime($date)) - date('m', strtotime(substr($event->date,0,11))))) / ($event->pec_monthly_every)) )
			) {
				continue;
			}

			$event_count++;
		}

		//return $result[0]->counter;
		return $event_count;
	}*/
	
	// ************************************* //
	// ****** Monthly Calendar Layout ****** //
	//************************************** //
	
	function monthlyCalendarLayout($compact = false) 
	{
		global $dpProEventCalendar_cache;

		$month_search = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT);

		$layoutCache = 'monthlyLayout';
		if($compact) {
			$layoutCache = 'monthlyLayoutCompact';
		}

		if(!$this->is_admin &&
			isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]) && 
			isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$layoutCache][$month_search]) && 
			$this->calendar_obj->cache_active &&
			empty($this->category) &&
			empty($this->event_id) &&
			empty($this->author) &&
			$this->limit_description == 0) {
				
			$html = $dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$layoutCache][$month_search]['html'];
			//echo '<pre>';
			//print_r($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]);
			//echo '</pre>';
		} else {
		//die();
			$html = '';
			
			if($this->calendar_obj->first_day == 1) {
				if($this->datesObj->firstDayNum == 0) { $this->datesObj->firstDayNum == 7;  }
				$this->datesObj->firstDayNum--;
				
				$html .= '
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_MONDAY'].'</span>
					 </div>';
			} else {
				$html .= '
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_SUNDAY'].'</span>
					 </div>
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_MONDAY'].'</span>
					 </div>';
			}
			$html .= '
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_TUESDAY'].'</span>
					 </div>
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_WEDNESDAY'].'</span>
					 </div>
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_THURSDAY'].'</span>
					 </div>
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_FRIDAY'].'</span>
					 </div>
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_SATURDAY'].'</span>
					 </div>
					 ';
			if($this->calendar_obj->first_day == 1) {
				$html .= '
					 <div class="dp_pec_dayname">
							<span>'.$this->translation['DAY_SUNDAY'].'</span>
					 </div>';
			}
			
			$general_count = 0;
			
			if($general_count == 0) {
				$html .= '<div class="dp_pec_monthly_row">';		
			}
			
			if( $this->datesObj->firstDayNum != 6 ) {
				
				for($i = ($this->datesObj->daysInPrevMonth - $this->datesObj->firstDayNum); $i <= $this->datesObj->daysInPrevMonth; $i++) 
				{
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
							<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
								<div class="dp_date_head"><span>'.$i.'</span></div>
							</div>';
					
					$general_count++;
				}
				
			}
			
			for($i = 1; $i <= $this->datesObj->daysInCurrentMonth; $i++) 
			{
				$curDate = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($i, 2, "0", STR_PAD_LEFT);
				$countEvents = 0;
				$eventsCurrDate = array();
				
				if(!$this->is_admin) {
					$result = $this->getEventsByDate($curDate);
					
					if(is_array($result)) {
						foreach($result as $event) {
			
							if($event->id == "") 
								$event->id = $event->ID;
			
							if($event->pec_exceptions != "") {
								$exceptions = explode(',', $event->pec_exceptions);
								
								if($event->recurring_frecuency != "" && in_array($curDate, $exceptions)) {
									continue;
								}
							}

							if($event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($curDate)) == "0" || date('w', strtotime($curDate)) == "6")) {
								continue;
							}
							
							if(!$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
								( ((strtotime($curDate) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
							) {
								continue;
							}
							
							if($event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
								( ((strtotime($curDate) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
							) {
								continue;
							}
							
							if($event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
								( !is_int (((date('m', strtotime($curDate)) - date('m', strtotime(substr($event->date,0,11))))) / ($event->pec_monthly_every)) )
							) {
								continue;
							}
							
							$eventsCurrDate[] = $event;
							
							$countEvents++;
						}
					}
				}
				
				//$countEvents = $this->getCountEventsByDate($curDate);
				if($this->calendar_obj->hide_old_dates || !empty($this->event_id)) {
					@$this->calendar_obj->date_range_start = date('Y-m-d');	
				}
				if(($this->calendar_obj->date_range_start != '0000-00-00' && $this->calendar_obj->date_range_start != NULL && (strtotime($curDate) < strtotime($this->calendar_obj->date_range_start)) || ( $this->calendar_obj->date_range_end != '0000-00-00' && $this->calendar_obj->date_range_end != NULL && strtotime($curDate) > strtotime($this->calendar_obj->date_range_end))) && !$this->is_admin) {
					
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
						<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
							<div class="dp_date_head"><span>'.$i.'</span></div>
						</div>';
				} else {
					$special_date = "";
					$special_date_obj = $this->getSpecialDates($curDate);
					$booked_date = false;
					$booking_remain = true;					
					
					if(!empty($this->event_id)) {
						
						$booking_limit = get_post_meta($this->event_id, 'pec_booking_limit', true);
						$booking_count = $this->getBookingsCount($this->event_id, $curDate);
						
						if($booking_limit > 0 && ($booking_limit - $booking_count) <= 0) {
							$booking_remain = false;
						}

						if($this->userHasBooking($curDate, $this->event_id)) {
							$special_date = "style='background-color: ".$this->calendar_obj->booking_event_color.";' ";
							$booked_date = true;
						}
						
					} else {
						
						if($special_date_obj->color) {
							$special_date = "style='background-color: ".$special_date_obj->color.";' ";
						}
						
						if($curDate == date("Y-m-d", current_time('timestamp'))) {
							//$special_date = "style='background-color: ".$this->calendar_obj->current_date_color.";' ";
						}
						
					}
					
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
						<div class="dp_pec_date '.($general_count % 7 == 0 ? 'first-child' : '').' '.($special_date != "" ? 'dp_pec_special_date' : '').'" data-dppec-date="'.$curDate.'" '.$special_date.'>
							<div class="dp_date_head"><span>'.$i.'</span></div>
							';

					if($this->calendar_obj->show_titles_monthly && empty($this->event_id) && !$compact) {
						$html .= '<div class="pec_monthlyDisplayEvents">';
						$count_monthly_title = 0;
						foreach($eventsCurrDate as $event) {
			
							if($event->id == "") 
								$event->id = $event->ID;
			
							if($this->calendar_obj->format_ampm) {
								$time = date('h:i A', strtotime($event->date));
							} else {
								$time = date('H:i', strtotime($event->date));				
							}
							
							$end_time = "";
							if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
							
							if($end_time != "") {
								
								if($this->calendar_obj->format_ampm) {
									$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
								} else {
									$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
								}
								$end_time = " - ".$end_time_tmp;
								if($end_time_tmp == $time) {
									$end_time = "";	
								}
							}
													
							if($event->all_day) {
								$time = $this->translation['TXT_ALL_DAY'];
								$end_time = "";
							}
							
							
							$event_color = $this->getSpecialDatesById(get_post_meta($event->id, 'pec_color', true));
							
							$title = wp_trim_words($event->title, 5);

							if($this->calendar_obj->show_time && !$event->hide_time) {
								//$html .= '<span>'.$time.'</span>';
								$title = '<strong>'.$time.$end_time.'</strong><br>'.$title;
							}
							
							$html .= '<span class="dp_daily_event" style="'.($event_color != "" ? 'background-color:'.$event_color.';' : '').($count_monthly_title >= 3 ? 'display:none;' : '').'" data-dppec-event="'.$event->id.'" data-dppec-event-url="'.($this->calendar_obj->link_post ? dpProEventCalendar_get_permalink($event->id).(strpos(dpProEventCalendar_get_permalink($event->id), "?") === false ? "?" : "&").'event_date='.strtotime($event->date) : '').'" data-dppec-date="'.$curDate.'">'.$title.'</span>';
							
							$count_monthly_title++;
							
						}

						if($count_monthly_title > 3) {

							$html .= '<span class="dp_daily_event dp_daily_event_show_more"> + '.($count_monthly_title - 3).' '.__('More', 'dpProEventCalendar').'</span>';

						}

						$html .= '</div>';
						
					}
					
					$html .= '
							'.($countEvents > 0 && !$booked_date && $booking_remain ? ($this->calendar_obj->show_x || !empty($this->event_id) ? (!empty($this->event_id) ? '<span class="dp_book_event_radio"></span>' : '<span class="dp_count_events" '.($this->calendar_obj->show_titles_monthly && empty($this->event_id) && !$compact ? 'style="display:none"' : '').'>X</span>') : '<span class="dp_count_events" '.($this->calendar_obj->show_titles_monthly && empty($this->event_id) && !$compact ? 'style="display:none"' : '').'>'.$countEvents.'</span>') : '').'
							';
						
					
					if($this->is_admin) {
						$html .= '
							<div class="dp_manage_special_dates" style="display: none;">
								<div class="dp_manage_sd_head">Special Date</div>
								<select>
									<option value="">None</option>';
									foreach($this->getSpecialDatesList() as $key) {
										$html .= '<option value="'.$key->id.','.$key->color.'" '.($key->id == $special_date_obj->id ? 'selected' : '').'>'.$key->title.'</option>';
									}
						$html .= '
								</select>	
							</div>';
					}
					if($countEvents > 0 && ($this->calendar_obj->show_preview || !empty($this->event_id))) {
						$html .= '
							<div class="eventsPreview">
								<ul>
							';
							if(!empty($this->event_id)) {
								if($booked_date) {
									$html .= '<li>'.$this->translation['TXT_BOOK_ALREADY_BOOKED'].'</li>';
								} else {
									$html .= '<li>';
									
									if($booking_remain) {
									
										$html .= $this->translation['TXT_BOOK_EVENT_PICK_DATE'];
									
									}
									
									if($booking_limit > 0) {
										
										if($booking_remain) {
									
											$html .= '<br>';
										
										}
									
										$html .= '<strong>'.($booking_limit - $booking_count).' '.$this->translation['TXT_BOOK_TICKETS_REMAINING'].'.</strong>';
										
									}
									$html .= '</li>';
								}
								
							} else {
								//$result = $this->getEventsByDate($curDate);
								foreach($eventsCurrDate as $event) {
									
									if($event->id == "") 
										$event->id = $event->ID;
						
									if($this->calendar_obj->format_ampm) {
										$time = date('h:i A', strtotime($event->date));
									} else {
										$time = date('H:i', strtotime($event->date));				
									}
															
									$html .= '<li data-dppec-event="'.$event->id.'">';
									if($event->all_day) {
										$time = $this->translation['TXT_ALL_DAY'];
										$end_time = "";
									}
									if($this->calendar_obj->show_time && !$event->hide_time) {
										$html .= '<span>'.$time.'</span>';
									}
			
									$html .= $event->title;
									$html .= '<div class="dp_pec_clear"></div>';
									$html .= get_the_post_thumbnail($event->id, 'medium');
									
									$html .= '</li>';
								}
							}
						$html .= '
								</ul>
							</div>';
					}
					
					$html .= '
						</div>';
				}
				
				$general_count++;
			}
			
			if( $this->datesObj->lastDayNum != ($this->calendar_obj->first_day == 1 ? 7 : 6) ) {
				
				for($i = 1; $i <= ( ($this->calendar_obj->first_day == 1 ? 7 : 6) - $this->datesObj->lastDayNum ); $i++) 
				{
					if($general_count > 0 && $general_count % 7 == 0) {
						$html .= '</div><div class="dp_pec_monthly_row_space"></div><div class="dp_pec_monthly_row">';	
					}

					$html .= '
							<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
								<div class="dp_date_head"><span>'.$i.'</span></div>
							</div>';
					
					$general_count++;
					
				}
				
			}
			$html .= '</div>
				<div class="dp_pec_monthly_row_space"></div>
				<div class="dp_pec_clear"></div>';
		}
		
		if(!$this->is_admin &&
			empty($this->category) &&
			empty($this->event_id) &&
			empty($this->author) &&
			$this->limit_description == 0) {

			$cache = array(
				'calendar_id_'.$this->id_calendar => array(
					$layoutCache => array(
						$month_search => array(
							'html'		  => $html,
							'lastUpdate'  => time()	
						)
					)
				)
			);
			
			if(!$dpProEventCalendar_cache) {
				update_option( 'dpProEventCalendar_cache', $cache);
			} else if($html != "") {
					
				//$dpProEventCalendar_cache[] = $cache;
				$dpProEventCalendar_cache['calendar_id_'.$this->id_calendar][$layoutCache][$month_search] =  array(
						'html'		  => $html,
						'lastUpdate'  => time()	
					);
					//print_r($dpProEventCalendar_cache);
				update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
			}
		}
		return $html;
	}
	
	// ************************************* //
	// ****** Daily Calendar Layout ****** //
	//************************************** //
	
	function dailyCalendarLayout($curDate = null) 
	{
		$html = "";
		if(is_null($curDate)) {
			$curDate = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($this->datesObj->currentDate, 2, "0", STR_PAD_LEFT);
		}
		
		
		if($this->calendar_obj->daily_weekly_layout == 'schedule') {

			$result = $this->getEventsByDate($curDate);
			
			if(!is_array($result)) {
				$result = array();				
			}

			for($i = $this->calendar_obj->limit_time_start; $i <= $this->calendar_obj->limit_time_end; $i++) {
				$min = '00';
				$hour = $i;
				if($this->calendar_obj->format_ampm) {
					$min = ($i >= 12 ? 'PM' : 'AM');
					$hour = ($i > 12 ? $i - 12 : $i);
				}
				$html .= '<div class="dp_pec_date first-child">
							<div class="dp_date_head"><span>'.$hour.'</span><span class="dp_pec_minutes">'.$min.'</span></div>';
				
				foreach($result as $event) {
					
					if($event->id == "") 
						$event->id = $event->ID;
						
					$event_hour = date('G', strtotime($event->date));
					$event_hour_end = ltrim($event->end_time_hh, "0");
	
					if($event_hour_end <= $event_hour) {
						$event_hour_end = $event_hour + 1;
					}
					
					if($event_hour > $i || $event_hour_end <= $i) { continue; }
					
					if($this->calendar_obj->format_ampm) {
						$time = date('h:i A', strtotime($event->date));
					} else {
						$time = date('H:i', strtotime($event->date));				
					}
					if($this->calendar_obj->show_time && !$event->hide_time && !$event->all_day) {
						//$html .= '<span>'.$time.'</span>';
					}
					$title = $event->title;
					
					$event_color = $this->getSpecialDatesById(get_post_meta($event->id, 'pec_color', true));
					
					$html .= '<span class="dp_daily_event" '.($event_color != "" ? 'style="background-color:'.$event_color.'"' : '').' data-dppec-event="'.$event->id.'" data-dppec-event-url="'.($this->calendar_obj->link_post ? dpProEventCalendar_get_permalink($event->id).(strpos(dpProEventCalendar_get_permalink($event->id), "?") === false ? "?" : "&").'event_date='.strtotime($event->date) : '').'" data-dppec-date="'.$curDate.'">'.$title.'</span>';
				}
				$html .= '
					</div>';
			}
		} else {
			
			$html .= $this->eventsListLayout($curDate, false);
				
		}
		
		$html .= '<div class="dp_pec_clear"></div>';
		return $html;
	}
	
	// ************************************* //
	// ****** Weekly Calendar Layout ****** //
	//************************************** //
	
	function weeklyCalendarLayout($curDate = null) 
	{
		$html = "";
		if(is_null($curDate)) {
			$curDate = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($this->datesObj->currentDate, 2, "0", STR_PAD_LEFT);
		}
		
		if($this->calendar_obj->first_day == 1) {
			$weekly_first_date = strtotime('last monday', ($this->defaultDate + (24* 60 * 60)));
			$weekly_last_date = strtotime('next sunday', ($this->defaultDate - (24* 60 * 60)));
		} else {
			$weekly_first_date = strtotime('last sunday', ($this->defaultDate + (24* 60 * 60)));
			$weekly_last_date = strtotime('next saturday', ($this->defaultDate - (24* 60 * 60)));
		}
		
		$week_days = array();
		
		$week_days[0] = $weekly_first_date;
		$weekly_one = dpProEventCalendar_date_i18n('d', $weekly_first_date);
		$week_days[1] = strtotime('+1 day', $weekly_first_date);
		$weekly_two = dpProEventCalendar_date_i18n('d', $week_days[1]);
		$week_days[2] = strtotime('+2 day', $weekly_first_date);
		$weekly_three = dpProEventCalendar_date_i18n('d', $week_days[2]);
		$week_days[3] = strtotime('+3 day', $weekly_first_date);
		$weekly_four = dpProEventCalendar_date_i18n('d', $week_days[3]);
		$week_days[4] = strtotime('+4 day', $weekly_first_date);
		$weekly_five = dpProEventCalendar_date_i18n('d', $week_days[4]);
		$week_days[5] = strtotime('+5 day', $weekly_first_date);
		$weekly_six = dpProEventCalendar_date_i18n('d', $week_days[5]);
		$week_days[6] = strtotime('+6 day', $weekly_first_date);
		$weekly_seven = dpProEventCalendar_date_i18n('d', $week_days[6]);
		
		
		if($this->calendar_obj->first_day == 1) {
			if($this->datesObj->firstDayNum == 0) { $this->datesObj->firstDayNum == 7;  }
			$this->datesObj->firstDayNum--;
			
			$html .= '
				 <div class="dp_pec_dayname">
						<span>'.$weekly_one.' '.mb_substr($this->translation['DAY_MONDAY'], 0,3, 'UTF-8').'</span>
				 </div>';
		} else {
			$html .= '
				 <div class="dp_pec_dayname">
						<span>'.$weekly_one.' '.mb_substr($this->translation['DAY_SUNDAY'], 0,3, 'UTF-8').'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.$weekly_two.' '.mb_substr($this->translation['DAY_MONDAY'], 0,3, 'UTF-8').'</span>
				 </div>';
		}
		$html .= '
				 <div class="dp_pec_dayname">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_two : $weekly_three).' '.mb_substr($this->translation['DAY_TUESDAY'], 0,3, 'UTF-8').'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_three : $weekly_four).' '.mb_substr($this->translation['DAY_WEDNESDAY'], 0,3, 'UTF-8').'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_four : $weekly_five).' '.mb_substr($this->translation['DAY_THURSDAY'], 0,3, 'UTF-8').'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_five : $weekly_six).' '.mb_substr($this->translation['DAY_FRIDAY'], 0,3, 'UTF-8').'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.($this->calendar_obj->first_day == 1 ? $weekly_six : $weekly_seven).' '.mb_substr($this->translation['DAY_SATURDAY'], 0,3, 'UTF-8').'</span>
				 </div>
				 ';
		if($this->calendar_obj->first_day == 1) {
			$html .= '
				 <div class="dp_pec_dayname">
						<span>'.$weekly_seven.' '.mb_substr($this->translation['DAY_SUNDAY'], 0,3, 'UTF-8').'</span>
				 </div>';
		}
		
		$weekly_events_view = array();
		
		if($this->calendar_obj->daily_weekly_layout == 'list') {
			$html .= '<div class="dp_pec_row">';
		}
		
		for($x = 1; $x <= 7; $x++) {
			
			$html_tmp = "";
			$html_list = "";
			$disabled = "";
			$curDate = date('Y-m-d', $week_days[$x - 1]);
			
			
				if(!$this->calendar_obj->hide_old_dates || $curDate >= current_time('Y-m-d')) {
					$result = $this->getEventsByDate($curDate);
				}

				if($this->calendar_obj->hide_old_dates && $curDate < current_time('Y-m-d')) {
					$disabled = "disabled";
				}

				if(!is_array($result)) {
					$result = array();	
				}
				
				foreach($result as $event) {
	
					if($event->id == "") 
						$event->id = $event->ID;
	
					//if(date('G', strtotime($event->date)) != $i) { continue; }
					$event_hour = date('G', strtotime($event->date));
					$event_hour_end = ltrim($event->end_time_hh, "0");
	
					if($event_hour_end <= $event_hour) {
						$event_hour_end = $event_hour + 1;
					}
					
					if($this->calendar_obj->format_ampm) {
						$time = date('h:i A', strtotime($event->date));
					} else {
						$time = date('H:i', strtotime($event->date));				
					}
					
					$end_time = "";
					if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
					
					if($end_time != "") {
						
						if($this->calendar_obj->format_ampm) {
							$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
						} else {
							$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
						}
						$end_time = " - ".$end_time_tmp;
						if($end_time_tmp == $time) {
							$end_time = "";	
						}
					}

					$title = $event->title;
					
					if($this->calendar_obj->daily_weekly_layout == 'list' && $this->calendar_obj->show_time && !$event->hide_time) {
						$title = '<strong>'.$time.$end_time.'</strong><br>'.$title;
					}
					
					$event_color = $this->getSpecialDatesById(get_post_meta($event->id, 'pec_color', true));
					
					$html_tmp = '<span class="dp_daily_event" '.($event_color != "" ? 'style="background-color:'.$event_color.'"' : '').' data-dppec-event="'.$event->id.'" data-dppec-event-url="'.($this->calendar_obj->link_post ? dpProEventCalendar_get_permalink($event->id).(strpos(dpProEventCalendar_get_permalink($event->id), "?") === false ? "?" : "&").'event_date='.strtotime($event->date) : '').'" data-dppec-date="'.$curDate.'">'.$title.'</span>';
					
					if($this->calendar_obj->daily_weekly_layout == 'schedule') {
						
						for($z = $event_hour; $z < $event_hour_end; $z++) {
						
							$weekly_events_view[$x][$z][] = $html_tmp;

						}

						
					}	
					
					$html_list .= $html_tmp;
					
				}
				
			
			// Responsive Layout
			$html .= '<div class="dp_pec_responsive_weekly">';
			$html .= '<div class="dp_pec_clear"></div>
						<div class="dp_pec_dayname">
							<span>'.date('d', strtotime($curDate)).'</span>
					 </div>
					 <div class="dp_pec_clear"></div>';
			 $html .= '<div class="dp_pec_date '.$disabled.'">';
			if($html_list != "") {
				
					$html .= $html_list;
			
			} else {
				
				$html .= '<span class="pec_no_events_found">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span>';
				
			}
			$html .= '</div>';
			$html .= '</div>';
				
			if($this->calendar_obj->daily_weekly_layout == 'list') {
				
				$html .= '<div class="dp_pec_date '.$disabled.'" '.($x == 1 ? 'style="margin-left: 0;"' : '').'>';
				
				if($html_list != "") {
				
					$html .= $html_list;
				
				} else {
					
					$html .= '<span class="pec_no_events_found">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span>';
					
				}
				
				$html .= '</div>';
			}
				
		
		}
		
		if($this->calendar_obj->daily_weekly_layout == 'list') {
			$html .= '</div>';
		}
		
		if($this->calendar_obj->daily_weekly_layout == 'schedule') {
			for($i = $this->calendar_obj->limit_time_start; $i <= $this->calendar_obj->limit_time_end; $i++) {
				$min = '00';
				$hour = $i;
				if($this->calendar_obj->format_ampm) {
					$min = ($i >= 12 ? 'PM' : 'AM');
					$hour = ($i > 12 ? $i - 12 : $i);
				}
				$html .= '
				<div class="dp_pec_row">
					<div class="dp_pec_date first-child">
						<div class="dp_date_head"><span>'.$hour.'</span><span class="dp_pec_minutes">'.$min.'</span></div>
					</div>
					';
					for($x = 1; $x <= 7; $x++) {
						$disabled = "";
						$curDate = date('Y-m-d', $week_days[$x - 1]);
						
						if($this->calendar_obj->hide_old_dates && $curDate < current_time('Y-m-d')) {
							$disabled = "disabled";
						}

						if(isset($weekly_events_view[$x][$i])) {
							$html .= '<div class="dp_pec_date '.$disabled.'" '.($x == 1 ? 'style="margin-left: 0;"' : '').'>';
							foreach($weekly_events_view[$x][$i] as $z) {
								$html .= $z;
							}
							$html .= '</div>';
						} else {
		
							$html .= '
							<div class="dp_pec_date '.$disabled.'" '.($x == 1 ? 'style="margin-left: 0;"' : '').'></div>';
			
						}
					
					}
				$html .= '</div>';
			}
		}
		$html .= '<div class="dp_pec_clear"></div>';
		return $html;
	}
	
	function getSpecialDates($date) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = "
		SELECT sp.id, sp.color 
		FROM ". $this->table_special_dates ." sp
		INNER JOIN ". $this->table_special_dates_calendar ." spc ON spc.special_date = sp.`id`
		WHERE spc.calendar = ".$this->id_calendar." AND spc.`date` = '".$date."' ";
		$result = $wpdb->get_results($querystr, OBJECT);
		
		return $result[0];
	}
	
	function setSpecialDates( $sp, $date ) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = "DELETE FROM ". $this->table_special_dates_calendar ." WHERE calendar = ".$this->id_calendar." AND date = '".$date."'; ";
		$result = $wpdb->query($querystr, OBJECT);
		
		if(is_numeric($sp)) {
			$querystr = "INSERT INTO ". $this->table_special_dates_calendar ." (special_date, calendar, date) VALUES ( ".$sp.", ".$this->id_calendar.", '".$date."' );";
			$result = $wpdb->query($querystr, OBJECT);
		}
		
		return;
	}
	
	function getSpecialDatesList() {
		global $wpdb;
		
		$querystr = "
		SELECT * 
		FROM ". $this->table_special_dates ." sp ";
		$result = $wpdb->get_results($querystr, OBJECT);
		
		return $result;
	}
	
	function getSpecialDatesById($id) {
		global $wpdb;
		
		if(!is_numeric($id)) { return ""; }
		
		$querystr = "
		SELECT * 
		FROM ". $this->table_special_dates ." sp 
		WHERE sp.id = ".$id."
		LIMIT 1";
		$result = $wpdb->get_row($querystr, OBJECT);
		
		if(empty($result)) { return ""; }
		
		return $result->color;
	}
	
	function getEventsByDate($date, $count = false) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$event_list = $this->upcomingCalendarLayout( true, 50, '', $date . ' 00:00:00', $date . ' 23:59:59' );
		
		return $event_list;
		
		
		//print_r($event_list);
		//die();
		
		$events = array();
		
		foreach($events_obj as $event) {
			
			if($event->id == "") 
				$event->id = $event->ID;
				

			$pec_weekly_day = @unserialize($event->pec_weekly_day);
			
			if($event->recurring_frecuency == 2 && is_array($pec_weekly_day)) {
				$original_date = $event->date;
				foreach($pec_weekly_day as $week_day) {
					$day = "";
					
					switch($week_day) {
						case 1:
							$day = "Monday";
							break;	
						case 2:
							$day = "Tuesday";
							break;	
						case 3:
							$day = "Wednesday";
							break;	
						case 4:
							$day = "Thursday";
							break;	
						case 5:
							$day = "Friday";
							break;	
						case 6:
							$day = "Saturday";
							break;	
						case 7:
							$day = "Sunday";
							break;	
					}
					
					if(date('l', strtotime($date)) == $day) {
						$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
						$event->date = date("Y-m-d", strtotime("next ".$day, strtotime($original_date))). ' '.date("H:i:s", strtotime($original_date));
						$events[] = $event;
						
					}
					
				}
			} elseif($event->recurring_frecuency == 3 && $event->pec_monthly_day != "" && $event->pec_monthly_position != "") {
				$original_date = $event->date;
				//echo date('l', strtotime($date));

				if(strtolower(date('Y-m-d', strtotime($date))) == date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date))))) {
					//$event->date = date("Y-m", strtotime($original_date)). '-'.date("d", strtotime($date)). ' '.date("H:i:s", strtotime($original_date));
					//die("OKKK");
					//$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
					//$event->date = date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date)))). ' '.date("H:i:s", strtotime($original_date));
					//echo date('Y-m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date)))). ' '.date("H:i:s", strtotime($original_date))." ".$event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($date));
					$events[] = $event;
					
				}
			} else {
				$events[] = $event;
			}
		}
		
		return $events;
	}
	
	function getEventByID($event) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($event)) { return false; }
		$meta_key = "SELECT meta_value FROM ".$wpdb->postmeta." WHERE post_id = p.ID AND meta_key";
		$querystr = "
		SELECT 	p.ID as id, 
				p.post_title as title, 
				p.post_content as description, 
				(".$meta_key." = 'pec_id_calendar' LIMIT 1) as id_calendar, 
				(".$meta_key." = 'pec_date' LIMIT 1) as date, 
				(".$meta_key." = 'pec_all_day' LIMIT 1) as all_day, 
				(".$meta_key." = 'pec_daily_working_days' LIMIT 1) as pec_daily_working_days, 
				(".$meta_key." = 'pec_daily_every' LIMIT 1) as pec_daily_every, 
				(".$meta_key." = 'pec_weekly_every' LIMIT 1) as pec_weekly_every,
				(".$meta_key." = 'pec_weekly_day' LIMIT 1) as pec_weekly_day, 
				(".$meta_key." = 'pec_monthly_every' LIMIT 1) as pec_monthly_every,
				(".$meta_key." = 'pec_monthly_position' LIMIT 1) as pec_monthly_position,
				(".$meta_key." = 'pec_monthly_day' LIMIT 1) as pec_monthly_day,
				(".$meta_key." = 'pec_exceptions' LIMIT 1) as pec_exceptions,
				(".$meta_key." = 'pec_recurring_frecuency' LIMIT 1) as recurring_frecuency, 
				(".$meta_key." = 'pec_end_date' LIMIT 1) as end_date, 
				(".$meta_key." = 'pec_link' LIMIT 1) as link, 
				(".$meta_key." = 'pec_share' LIMIT 1) as share, 
				(".$meta_key." = 'pec_map' LIMIT 1) as map, 
				(".$meta_key." = 'pec_end_time_hh' LIMIT 1) as end_time_hh, 
				(".$meta_key." = 'pec_end_time_mm' LIMIT 1) as end_time_mm, 
				(".$meta_key." = 'pec_hide_time' LIMIT 1) as hide_time, 
				(".$meta_key." = 'pec_location' LIMIT 1) as location, 
				(".$meta_key." = 'pec_phone' LIMIT 1) as phone
		FROM ".$wpdb->posts." p
		WHERE p.ID = ".$event." AND ((".$meta_key." = 'pec_id_calendar' LIMIT 1) = ".$this->id_calendar." OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '%,".$this->id_calendar."' OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '%,".$this->id_calendar.",%' OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '".$this->id_calendar.",%') AND p.post_status = 'publish'
		ORDER BY DATE_FORMAT((".$meta_key." = 'pec_date'), '%T') ASC
		";
		
		return $wpdb->get_results($querystr, OBJECT);
	}
	
	function eventsListLayout($date, $return_btn = true) {
		global $wpdb;

		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = " SET time_zone = '+00:00'";
		$wpdb->query($querystr);
		
		$start_date = $this->parseMysqlDate($date);
		/*$html = '
			<div class="dp_pec_date_event_head dp_pec_date_event_daily dp_pec_isotope">
				<i class="fa fa-calendar"></i>
				<span>'.$this->parseMysqlDate($date).'</span>';
				if($return_btn) {
					$html .= '<a href="javascript:void(0);" class="dp_pec_date_event_back"><i class="fa fa-angle-double-left"></i></a>';
				}
		$html .= '
			</div>';*/
			
		$start_date_year = date('Y', strtotime($date));
		$start_date_formatted = str_replace(array(',', '.', '/', '|'), '', $start_date);
		$start_date_formatted = str_replace($start_date_year, '', $start_date_formatted);
		
		if(is_numeric($this->columns) && $this->columns > 1) {
			$html .= '
			<div class="'.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'"></div>';
		}
		
		$html .= '
		<div class="dp_pec_columns_1 dp_pec_isotope dp_pec_date_event_wrap dp_pec_date_block_wrap">
			<span class="fa fa-calendar-o"></span>
			<div class="dp_pec_date_block">'.$start_date_formatted.'<span>'.$start_date_year.'</span></div>
			';
			if($return_btn) {
				$html .= '<a href="javascript:void(0);" class="dp_pec_date_event_back dp_pec_btnright"><i class="fa fa-angle-double-left"></i> <span>'.__('Back', 'dpProEventCalendar').'</span></a>';
			}
		$html .= '
		</div>
		<div class="dp_pec_clear"></div>';	
	
		
		$result = $this->getEventsByDate($date);

		if(count($result) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
		} else {
			
			$html .= $this->singleEventLayout($result, false, $date);
			
		}
		
		return $html;
	}
	
	function getSearchResults($key, $type = '') {
		global $wpdb;

		if(!is_numeric($this->id_calendar) || !isset($key)) { return false; }
		
		if($type == '') {
			$html = '
			<div class="dp_pec_date_block_wrap dp_pec_date_event_search dp_pec_isotope">
				<i class="fa fa-search"></i>
				<div class="dp_pec_date_block">'.$this->translation['TXT_RESULTS_FOR'].'</div>
				
				<a href="javascript:void(0);" class="dp_pec_date_event_back dp_pec_btnright"><i class="fa fa-angle-double-left"></i> <span>'.__('Back', 'dpProEventCalendar').'</span></a>
			</div>
			<div class="dp_pec_clear"></div>';
		}
		$meta_key = "SELECT meta_value FROM ".$wpdb->postmeta." WHERE post_id = p.ID AND meta_key";
		$querystr = "
		SELECT	p.ID as id, 
				p.post_title as title, 
				p.post_content as description, 
				(".$meta_key." = 'pec_id_calendar' LIMIT 1) as id_calendar, 
				(".$meta_key." = 'pec_date' LIMIT 1) as date, 
				(".$meta_key." = 'pec_all_day' LIMIT 1) as all_day, 
				(".$meta_key." = 'pec_daily_working_days' LIMIT 1) as pec_daily_working_days, 
				(".$meta_key." = 'pec_daily_every' LIMIT 1) as pec_daily_every, 
				(".$meta_key." = 'pec_weekly_every' LIMIT 1) as pec_weekly_every,
				(".$meta_key." = 'pec_weekly_day' LIMIT 1) as pec_weekly_day, 
				(".$meta_key." = 'pec_monthly_every' LIMIT 1) as pec_monthly_every,
				(".$meta_key." = 'pec_monthly_position' LIMIT 1) as pec_monthly_position,
				(".$meta_key." = 'pec_monthly_day' LIMIT 1) as pec_monthly_day,
(".$meta_key." = 'pec_exceptions' LIMIT 1) as pec_exceptions,
				(".$meta_key." = 'pec_recurring_frecuency' LIMIT 1) as recurring_frecuency, 
				(".$meta_key." = 'pec_end_date' LIMIT 1) as end_date, 
				(".$meta_key." = 'pec_link' LIMIT 1) as link, 
				(".$meta_key." = 'pec_share' LIMIT 1) as share, 

				(".$meta_key." = 'pec_map' LIMIT 1) as map, 
				(".$meta_key." = 'pec_end_time_hh' LIMIT 1) as end_time_hh, 
				(".$meta_key." = 'pec_end_time_mm' LIMIT 1) as end_time_mm, 
				(".$meta_key." = 'pec_hide_time' LIMIT 1) as hide_time, 
				(".$meta_key." = 'pec_location' LIMIT 1) as location, 
				(".$meta_key." = 'pec_phone' LIMIT 1) as phone
		FROM ".$wpdb->posts." p
		WHERE (p.post_title LIKE '%".$key."%' OR (".$meta_key." = 'pec_location' LIMIT 1) LIKE '%".$key."%' OR p.post_content LIKE '%".$key."%') AND ((".$meta_key." = 'pec_id_calendar' LIMIT 1) = ".$this->id_calendar." OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '%,".$this->id_calendar."' OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '".$this->id_calendar.",%') AND p.post_status = 'publish' AND p.post_type = 'pec-events'";
		if(!empty($this->author)) {
			$querystr .= "
			AND p.post_author = ".$this->author;
		}
		$querystr .= "
		ORDER BY (".$meta_key." = 'pec_date') ASC";

		$result = $wpdb->get_results($querystr, OBJECT);
		if(count($result) == 0) {
			if($type == 'accordion') {
				$html .= '<div class="dp_pec_accordion_event dp_pec_accordion_no_events"><span>'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span></div>';	
			} else {
				$html .= '
				<div class="dp_pec_date_event dp_pec_isotope">
					<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
				</div>';
			}
		} else {
				$html .= $this->singleEventLayout($result, true, null, true, $type);
		}
		
		return $html;
	}
	
	function getCategoryResults($key) {
		global $wpdb;

		if(!is_numeric($this->id_calendar) || !is_numeric($key)) { return false; }
		
		$html = '
			<div class="dp_pec_date_event_head dp_pec_date_event_search dp_pec_isotope">
				<i class="fa fa-search"></i>
				<span>'.$this->translation['TXT_RESULTS_FOR'].'</span><a href="javascript:void(0);" class="dp_pec_date_event_back dp_pec_btnright"><i class="fa fa-angle-double-left"></i> <span>'.__('Back', 'dpProEventCalendar').'</span></a>
			</div>';
		$meta_key = "SELECT meta_value FROM ".$wpdb->postmeta." WHERE post_id = p.ID AND meta_key";
		$querystr = "
		SELECT	p.ID as id, 
				p.post_title as title, 
				p.post_content as description, 
				(".$meta_key." = 'pec_id_calendar' LIMIT 1) as id_calendar, 
				(".$meta_key." = 'pec_date' LIMIT 1) as date, 
				(".$meta_key." = 'pec_all_day' LIMIT 1) as all_day, 
				(".$meta_key." = 'pec_daily_working_days' LIMIT 1) as pec_daily_working_days, 
				(".$meta_key." = 'pec_daily_every' LIMIT 1) as pec_daily_every, 
				(".$meta_key." = 'pec_weekly_every' LIMIT 1) as pec_weekly_every,
				(".$meta_key." = 'pec_weekly_day' LIMIT 1) as pec_weekly_day, 
				(".$meta_key." = 'pec_monthly_every' LIMIT 1) as pec_monthly_every,
				(".$meta_key." = 'pec_monthly_position' LIMIT 1) as pec_monthly_position,
				(".$meta_key." = 'pec_monthly_day' LIMIT 1) as pec_monthly_day,
				(".$meta_key." = 'pec_exceptions' LIMIT 1) as pec_exceptions,
				(".$meta_key." = 'pec_recurring_frecuency' LIMIT 1) as recurring_frecuency, 
				(".$meta_key." = 'pec_end_date' LIMIT 1) as end_date, 
				(".$meta_key." = 'pec_link' LIMIT 1) as link, 
				(".$meta_key." = 'pec_share' LIMIT 1) as share, 
				(".$meta_key." = 'pec_map' LIMIT 1) as map, 
				(".$meta_key." = 'pec_end_time_hh' LIMIT 1) as end_time_hh, 
				(".$meta_key." = 'pec_end_time_mm' LIMIT 1) as end_time_mm, 
				(".$meta_key." = 'pec_hide_time' LIMIT 1) as hide_time, 
				(".$meta_key." = 'pec_location' LIMIT 1) as location, 
				(".$meta_key." = 'pec_phone' LIMIT 1) as phone
		FROM ".$wpdb->posts." p
		INNER JOIN ".$wpdb->term_relationships." tr ON tr.object_id = p.ID
		WHERE (tr.term_taxonomy_id = ".$key.") AND ((".$meta_key." = 'pec_id_calendar' LIMIT 1) = ".$this->id_calendar." OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '%,".$this->id_calendar."' OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '%,".$this->id_calendar.",%' OR (".$meta_key." = 'pec_id_calendar' LIMIT 1) LIKE '".$this->id_calendar.",%') AND p.post_status = 'publish'
		ORDER BY (".$meta_key." = 'pec_date') ASC";

		$result = $wpdb->get_results($querystr, OBJECT);
		if(count($result) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
		} else {
			
			$html .= $this->singleEventLayout($result, true);
			
		}
		
		return $html;
	}
	
	function singleEventLayout($result, $search = false, $selected_date = null, $show_end_date = true, $type = '') {
		global $dpProEventCalendar;
		
		$html = "";
		
		$pagination = (is_numeric($dpProEventCalendar['pagination']) && $dpProEventCalendar['pagination'] > 0 ? $dpProEventCalendar['pagination'] : 10);
		if(is_numeric($this->opts['pagination']) && $this->opts['pagination'] > 0) {
			$pagination = $this->opts['pagination'];
		}
		$i = 0;
		
		if(!is_array($result)) {
			$result = array();	
		}
		
		foreach($result as $event) {
			
			if($event->id == "") 
				$event->id = $event->ID;
					
			if($selected_date != "" && $event->pec_exceptions != "") {
				$exceptions = explode(',', $event->pec_exceptions);
				
				if($event->recurring_frecuency != "" && in_array($selected_date, $exceptions)) {
					continue;
				}
			}
			
			if($selected_date != "" && $event->pec_daily_working_days && $event->recurring_frecuency == 1 && (date('w', strtotime($selected_date)) == "0" || date('w', strtotime($selected_date)) == "6")) {
				continue;
			}
			
			if($selected_date != "" && !$event->pec_daily_working_days && $event->recurring_frecuency == 1 && $event->pec_daily_every > 1 && 
				( ((strtotime($selected_date) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % $event->pec_daily_every != 0 )
			) {
				continue;
			}
			
			if($selected_date != "" && $event->recurring_frecuency == 2 && $event->pec_weekly_every > 1 && 
				( ((strtotime($selected_date) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0)) {
				continue;
			}
			
			if($selected_date != "" && $event->recurring_frecuency == 3 && $event->pec_monthly_every > 1 && 
				( !is_int (((date('m', strtotime($selected_date)) - date('m', strtotime(substr($event->date,0,11))))) / ($event->pec_monthly_every)) )
			) {
				continue;
			}

			$i++;
			
			if($this->limit < $i && $this->limit > 0) { break;}
			
			if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event->date));
			} else {
				$time = date('H:i', strtotime($event->date));				
			}
			$start_day = date('d', strtotime($event->date));
			$start_month = date('n', strtotime($event->date));
			$start_year = date('Y', strtotime($event->date));
			
			$end_date = '';
			$end_year = '';
			if($event->end_date != "" && $event->end_date != "0000-00-00" && $event->recurring_frecuency == 1) {
				$end_day = date('d', strtotime($event->end_date));
				$end_month = date('n', strtotime($event->end_date));
				$end_year = date('Y', strtotime($event->end_date));
				
				//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
				$end_date = ' - '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
			}
			
			//$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
			$start_date = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date));
			
			$end_time = "";
			if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
			
			if($end_time != "" && $show_end_date) {
				
				if($this->calendar_obj->format_ampm) {
					$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
				} else {
					$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
				}
				$end_time = " - ".$end_time_tmp;
				if($end_time_tmp == $time) {
					$end_time = "";	
				}
			}
			
			
			if($start_year != $end_year) {
				$start_date .= ', '.$start_year;
			}
			
			if($event->all_day) {
				$time = $this->translation['TXT_ALL_DAY'];
				$end_time = "";
			}
			
			$post_thumbnail_id = get_post_thumbnail_id( $event->id );
			$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
			
			$title = '<span class="dp_pec_event_title_sp">'.$event->title.'</span>';
			if($this->calendar_obj->link_post) {

				if($selected_date != "") {
					$title = '<a href="'.dpProEventCalendar_get_permalink($event->id).(strpos(dpProEventCalendar_get_permalink($event->id), "?") === false ? "?" : "&").'event_date='.strtotime($selected_date).'">'.$title.'</a>';	
				} else {
					$title = '<a href="'.dpProEventCalendar_get_permalink($event->id).'">'.$title.$selected_date.'</a>';					
				}
			}
			
			$all_working_days = '';
			if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
				$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
			}
			
			if($type == 'accordion') {
				$edit_button = $this->getEditRemoveButtons($event);
				
				$category = get_the_terms( $event->id, 'pec_events_category' ); 
				$category_list_html = '';
				$category_slug = '';
				if(!empty($category)) {
					$category_count = 0;
					$category_list_html .= '
						<span class="dp_pec_event_categories">';
					foreach ( $category as $cat){
						if($category_count > 0) {
							$category_list_html .= " / ";	
						}
						$category_list_html .= $cat->name;
						$category_slug .= 'category_'.$cat->slug.' ';
						$category_count++;
					}
					$category_list_html .= '
						</span>';
				}
				
				$html .= '
					<div class="dp_pec_accordion_event '.$category_slug.'">';
				
				$html .= '
					<span class="dp_pec_date_time"><i class="fa fa-clock-o"></i>'.$all_working_days.' ';
				if(($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) {
					$html .= $time.$end_time.$end_date;
				}
				$html .= '</span>'.$edit_button.'
				<div class="dp_pec_clear"></div>';
				
					$html .= '
						<h2>' . $title . '</h2>
						<div class="dp_pec_clear"></div>';
						
						if($this->calendar_obj->show_author) {
							$author = get_userdata(get_post_field( 'post_author', $event->id ));
							$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
						}
						
						$html .= '
						<span class="pec_time">'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date)).$end_date . '<br>'. $all_working_days. ' '. ((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time : '').'</span>
						';
						
						if($event->location != '') {
							$html .= '
							<span class="dp_pec_event_location"><i class="fa fa-map-marker"></i>'.$event->location.'</span>';
						}
						
						if($event->phone != '') {
							$html .= '
							<span class="dp_pec_event_phone"><i class="fa fa-phone"></i>'.$event->phone.'</span>';
						}
					
						if($category_list_html != "") {
							$html .= $category_list_html;
						}
				
						if(is_array($dpProEventCalendar['custom_fields_counter'])) {
							$counter = 0;
							
							foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
								$field_value = get_post_meta($event->id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
								if($field_value != "") {
									$html .= '<div class="pec_event_page_custom_fields"><strong>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</strong>';
									if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
										$html .= '<i class="fa fa-check"></i>';
									} else {
										$html .= '<p>'.$field_value.'</p>';
									}
									$html .= '</div>';
								}
								$counter++;		
							}
							
							$html .= '<div class="dp_pec_clear"></div>';
						}
						
				$html .= '
						<div class="pec_description">';
						$post_thumbnail_id = get_post_thumbnail_id( $event->id );
						$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
						if($post_thumbnail_id) {
							$html .= '<div class="dp_pec_event_photo">';
							$html .= '<img src="'.$image_attributes[0].'" alt="" />';
							$html .= '</div>';
						}
				
				if($this->limit_description > 0) {
					
					$event->description = force_balance_tags(html_entity_decode(wp_trim_words(htmlentities($event->description), $this->limit_description)));
					
				}
				
				$html .= '
							<p>'.do_shortcode(nl2br(strip_tags(str_replace("</p>", "<br />", preg_replace("/<p[^>]*?>/", "", $event->description)), '<iframe><strong><h1><h2><h3><h4><h5><h6><h7><b><i><em><pre><code><del><ins><img><a><ul><li><ol><blockquote><br><hr><span><p>'))).'</p>';
						
						$html .= $this->getRating($event->id);
						
						$html .= $this->getBookingButton($event->id, date('Y-m-d', strtotime($event->date)));
						
						if($event->map != '') {
							$map_lnlat = get_post_meta($event->id, 'pec_map_lnlat', true);
							if($map_lnlat != "") {
								//$event->map = str_replace(" ", "", $map_lnlat);
							}
							$html .= '
							<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>
							<iframe class="dp_pec_date_event_map_iframe" width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_q&q='.urlencode($event->map).'&ie=UTF8&output=embed"></iframe>';
						}
						$html .= '
						<div class="dp_pec_date_event_icons">';
					
					$html .= $this->getEventShare($event);
					
					if($event->link != '') {
						$html .= '
						<a class="dp_pec_date_event_link" rel="nofollow" href="'.$event->link.'" target="_blank"><i class="fa fa-link"></i></a>';
					}
					if($this->calendar_obj->ical_active) {
						$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime($event->date) ) . "'>iCal</a><br class='clear' />";
					}
					$html .= '
						<a class="dp_pec_date_event_close" href="javascript:void(0);"><i class="fa fa-close"></i></a>';
					$html .= '
						</div>';
				$html .= '
						</div>
					</div>';				
			} else {
			
				$html .= '
				<div class="dp_pec_date_event '.($search ? 'dp_pec_date_eventsearch' : '').' dp_pec_isotope" data-event-number="'.$i.'" '.($i > $pagination ? 'style="display:none;"' : '').'>';
				if($post_thumbnail_id) {
					$html .= '<div class="dp_pec_event_photo">';
					$html .= '<img src="'.$image_attributes[0].'" alt="" />';
					$html .= '</div>';
				}
				
				$edit_button = $this->getEditRemoveButtons($event);
				
				if($search) {	
						$html .= '<span class="dp_pec_date_time"><i class="fa fa-calendar-o"></i>'.$start_date.'</span>';
					}
					
				$html .= '
					<span class="dp_pec_date_time"><i class="fa fa-clock-o"></i>'.$all_working_days.' ';
				if(($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) {
					$html .= $time.$end_time.$end_date;
				}
				$html .= '</span>'.$edit_button.'
				<div class="dp_pec_clear"></div>';
				
				$html .= '
					<h2 class="dp_pec_event_title">'.$title.'</h2><div class="dp_pec_clear"></div><div class="dp_pec_clear"></div>
					';
						
					if($this->calendar_obj->show_author) {
						$author = get_userdata(get_post_field( 'post_author', $event->id ));
						$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
					}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
					$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
				}
				
					
				if($event->location != '') {
					$html .= '
					<span class="dp_pec_event_location"><i class="fa fa-map-marker"></i>'.$event->location.'</span>';
				}
				
				if($event->phone != '') {
					$html .= '
					<span class="dp_pec_event_phone"><i class="fa fa-phone"></i>'.$event->phone.'</span>';
				}
				
				$category = get_the_terms( $event->id, 'pec_events_category' ); 
				if(!empty($category)) {
					$category_count = 0;
					$html .= '
						<span class="dp_pec_event_categories">';
					foreach ( $category as $cat){
						if($category_count > 0) {
							$html .= " / ";	
						}
						$html .= $cat->name;
						$category_count++;
					}
					$html .= '
						</span>';
				}
				
				if(is_array($dpProEventCalendar['custom_fields_counter'])) {
					$counter = 0;
					
					foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
						$field_value = get_post_meta($event->id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
						if($field_value != "") {
							$html .= '<div class="pec_event_page_custom_fields"><strong>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</strong>';
							if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
								$html .= '<i class="fa fa-check"></i>';
							} else {
								$html .= '<p>'.$field_value.'</p>';
							}
							$html .= '</div>';
						}
						$counter++;		
					}
				}
				
				$html .= $this->getBookingButton($event->id, ($selected_date != null ? $selected_date : date('Y-m-d', strtotime($event->date))));
				
				$event_desc = nl2br(strip_tags(str_replace("</p>", "<br />", preg_replace("/<p[^>]*?>/", "", $event->description)), '<iframe><strong><h1><h2><h3><h4><h5><h6><h7><b><i><em><pre><code><del><ins><img><a><ul><li><ol><blockquote><br><hr><span><p>'));
				
				//if($this->limit_description > 0) {
					if($this->limit_description == 0) {
						if($this->widget) {
							$this->limit_description = 60;
						} else {
							$this->limit_description = 150;	
						}
					}
					$event_desc_short = force_balance_tags(html_entity_decode(wp_trim_words(htmlentities($event_desc), $this->limit_description)));
						
					//}
					
					$html .= '
						<div class="dp_pec_event_description">
							<div class="dp_pec_event_description_short" '.(strlen($event_desc) > $this->limit_description ? 'style="display:block"' : '').'>
								'.do_shortcode($event_desc_short).'
								<a href="javascript:void(0);" class="dp_pec_event_description_more">'.__('More', 'dpProEventCalendar').'</a>
							</div>
							<div class="dp_pec_event_description_full" '.(strlen($event_desc) > $this->limit_description ? 'style="display:none"' : '').'>
								'.do_shortcode($event_desc).'
							</div>
						</div>';
						
				if($event->map != '') {
					$map_lnlat = get_post_meta($event->id, 'pec_map_lnlat', true);
					if($map_lnlat != "") {
						//$event->map = str_replace(" ", "", $map_lnlat);
					}
					$html .= '
					<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>
					<iframe class="dp_pec_date_event_map_iframe" width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_q&q='.urlencode($event->map).'&ie=UTF8&output=embed"></iframe>';
				}
					
					$html .= $this->getRating($event->id);
					$html .= '
						<div class="dp_pec_date_event_icons">';
					$html .= $this->getEventShare($event);
					if($event->link != '') {
						$html .= '
						<a class="dp_pec_date_event_link" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i></a>';
					}
					/*
					if($event->map != '') {
						$html .= '
						
						<a class="dp_pec_date_event_map" href="javascript:void(0);"></a>';
					}
					*/
					$html .= '
						</div>';
					if($this->calendar_obj->ical_active && !$search) {
						$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime($selected_date) ) . "'>iCal</a><br class='clear' />";
					}
	
					$html .= '
				</div>';
			}
		}
		
		if($i > $pagination) {
			$html .= '<a href="javascript:void(0);" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.$i.'" data-pagination="'.$pagination.'">'.__('More', 'dpProEventCalendar').'</a>';
		}
		
		return $html;
	}
	
	function upcomingCalendarLayout( $return_data = false, $limit = '', $limit_description = '', $events_month = null, $events_month_end = null, $show_end_date = true, $filter_author = false, $auto_limit = true, $filter_map = false, $past = false ) {
		global $wpdb, $dpProEventCalendar;
		
		$html = "";
		
		$list_limit = $this->limit;
		if(is_numeric($limit)) {
			$list_limit = $limit;	
		}
		
		if(is_numeric($limit_description)) {
			$this->trim_words = $limit_description;	
		}
		
		$args = array( 
			'posts_per_page' 	=> -1, 
			'post_type'			=> 'pec-events',
			'meta_key'			=> 'pec_date',
			'order'				=> 'ASC',
			'lang'				=> substr(get_locale(),0,2),
			'orderby'			=> 'meta_value',
			'suppress_filters'  => false
		);
		
		if(!empty($this->category)) {
			
			$category_slug = get_term_by('term_id', (int)$this->category, 'pec_events_category');
			
			$args['pec_events_category'] = $category_slug->slug;
			
			/*$args['tax_query'] = array(
				array(
					'taxonomy' => 'pec_events_category',
					'field' => 'term_id',
					'term' => $this->category
				)
			);*/
		}

		
		if(!is_null($events_month)) {
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_date',
					'value'   => array($events_month, $events_month_end),
					'compare' => 'BETWEEN',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC'
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '!='
				),
				
			);
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_end_date',
					'value'   => substr($events_month, 0, 10),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '0000-00-00',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '',
					'compare' => '=',
				)
			);
			
		} elseif($past) {

			$args['meta_query'][] = array(
				'key'     => 'pec_date',
				'value'   => current_time('mysql'),
				'compare' => '<=',
				'type'    => 'DATETIME'
			);
			
		} else {
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_date',
					'value'   => current_time('mysql'),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_extra_dates',
					'value'   => '',
					'compare' => '!='
				),
				array(
					'key'     => 'pec_recurring_frecuency',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC'
				)
			);
			
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => 'pec_end_date',
					'value'   => substr(current_time('mysql'),0, 10),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '0000-00-00',
					'compare' => '=',
				),
				array(
					'key'     => 'pec_end_date',
					'value'   => '',
					'compare' => '=',
				)
			);
		}
		
		
		if(!empty($this->event_id)) {
			
			$args['p'] = $this->event_id;
		}
		
		if(!empty($this->author)) {
			
			$args['author'] = $this->author;
		}
		
		if($filter_author) {
			
			if(is_author()) {
				global $author_name, $author;
				$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
			} else {
				$curauth = get_userdata(intval($author));
			}
			
			if(is_numeric($this->author) && $this->author > 0) {			

				$args['author'] = $this->author;

			} else {

				$args['author'] = $curauth->ID;

			}
		}
		if($filter_map) {

			$args['meta_query'][] = array(
				'key'     => 'pec_map',
				'value'   => '',
				'compare' => '!=',
			);
			
		}
		
		// Check Calendar ID
		
		// XXXXXXXXXX
		$args['meta_query'][] = array(
			'relation' => 'OR',
			array(
				'key'     => 'pec_id_calendar',
				'value'   => $this->id_calendar,
				'compare' => 'LIKE'
			)
		);
		
		//$events_obj = $wpdb->get_results($querystr);
		$events_obj = get_posts( $args );
		//echo '<!--';
		//echo '<pre>';
		//print_r($args);
		//echo '</pre>';
		//print_r($events_obj);
		//echo $GLOBALS['wp_query']->request;
		//echo '-->';
		if(count($events_obj) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
			
		} else {
			
			$order_events = array();
			$daily_events_total = 0;

			foreach($events_obj as $event) {
				
				$event->id_calendar = get_post_meta($event->ID, 'pec_id_calendar', true);
				$event->date = get_post_meta($event->ID, 'pec_date', true);
				$event->all_day = get_post_meta($event->ID, 'pec_all_day', true);
				$event->pec_daily_working_days = get_post_meta($event->ID, 'pec_daily_working_days', true);
				$event->pec_daily_every = get_post_meta($event->ID, 'pec_daily_every', true);
				$event->pec_weekly_every = get_post_meta($event->ID, 'pec_weekly_every', true);
				$event->pec_weekly_day = get_post_meta($event->ID, 'pec_weekly_day', true);
				$event->pec_monthly_every = get_post_meta($event->ID, 'pec_monthly_every', true);
				$event->pec_monthly_position = get_post_meta($event->ID, 'pec_monthly_position', true);
				$event->pec_monthly_day = get_post_meta($event->ID, 'pec_monthly_day', true);
				$event->pec_exceptions = get_post_meta($event->ID, 'pec_exceptions', true);
				$event->pec_extra_dates = get_post_meta($event->ID, 'pec_extra_dates', true);
				$event->end_date = get_post_meta($event->ID, 'pec_end_date', true);
				$event->link = get_post_meta($event->ID, 'pec_link', true);
				$event->share = get_post_meta($event->ID, 'pec_share', true);
				$event->map = get_post_meta($event->ID, 'pec_map', true);
				$event->end_time_hh = get_post_meta($event->ID, 'pec_end_time_hh', true);
				$event->end_time_mm = get_post_meta($event->ID, 'pec_end_time_mm', true);
				$event->hide_time = get_post_meta($event->ID, 'pec_hide_time', true);
				$event->location = get_post_meta($event->ID, 'pec_location', true);
				$event->phone = get_post_meta($event->ID, 'pec_phone', true);
				$event->recurring_frecuency = get_post_meta($event->ID, 'pec_recurring_frecuency', true);
				$event->title = $event->post_title;
				$event->description = $event->post_content;
				

				$event->pec_extra_dates = explode(',', $event->pec_extra_dates);	
				if(!is_array($event->pec_extra_dates)) {
					$event->pec_extra_dates = array();
				}
				
				if($event->recurring_frecuency > 0) {
					
					$enddate_orig = $event->end_date." 23:59:59";
					if($event->all_day) {
						$startdate_orig = date('Y-m-d'). " 00:00:00";
					} else {
						$startdate_orig = current_time('mysql');	
					}
					if(!is_null($events_month)) {
						$startdate_orig = $events_month;
						$enddate_orig = $events_month_end;
					}
					if($past) {
						//$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
						$enddate_orig =  current_time('mysql');
						$startdate_orig = $enddate_orig;
					}

					switch($event->recurring_frecuency) {
						case 1:
							$k = 1;
							
							$startdate = $event->date;
							
							if(strtotime($startdate) < strtotime($startdate_orig)) {
								
								$startdate = date('Y-m-d', strtotime($startdate_orig)). ' ' .date('H:i:s', strtotime($event->date));
									
							}
							
							for($i = 1; $i <= $list_limit; $i++) {
													
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)) - 1 +$k, date("y", strtotime($startdate))));
								//echo "DONE DAILY";
								if($eventdate != "" && $event->pec_exceptions != "") {
									$exceptions = explode(',', $event->pec_exceptions);
									
									if(in_array(substr($eventdate, 0, 10), $exceptions)) {
										$i--;
										continue;
									}
								}
								
								/*echo $event->title.'<br>';
								echo $eventdate.'<br>';
								echo $startdate_orig.'<br>';
								die();*/
								
								if(
									(
										(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")
									) 
									|| $event->end_date == '0000-00-00') 
									&& (strtotime($eventdate) >= strtotime($startdate_orig) && strtotime($eventdate) <= strtotime($enddate_orig))
								) {
									//&& (strtotime(substr($eventdate,0, 10)) >= strtotime(substr($startdate_orig,0, 10)) && strtotime($eventdate) <= strtotime($enddate_orig))
									$order_events[strtotime($eventdate).$event->ID] = new stdClass;
									$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
									$order_events[strtotime($eventdate).$event->ID]->recurring_frecuency = $event->recurring_frecuency;
									$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;
									$order_events[strtotime($eventdate).$event->ID]->orig_date = $event->date;
									$order_events[strtotime($eventdate).$event->ID]->start_date = $event->date;
									$order_events[strtotime($eventdate).$event->ID]->end_date = $event->end_date;
									$order_events[strtotime($eventdate).$event->ID]->end_time_hh = $event->end_time_hh;
									$order_events[strtotime($eventdate).$event->ID]->end_time_mm = $event->end_time_mm;
									$order_events[strtotime($eventdate).$event->ID]->title = $event->title;
									$order_events[strtotime($eventdate).$event->ID]->description = $event->description;
									$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
									$order_events[strtotime($eventdate).$event->ID]->hide_time = $event->hide_time;
									$order_events[strtotime($eventdate).$event->ID]->location = $event->location;
									$order_events[strtotime($eventdate).$event->ID]->phone = $event->phone;
									$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
									$order_events[strtotime($eventdate).$event->ID]->map = $event->map;
									$order_events[strtotime($eventdate).$event->ID]->share = $event->share;
									$order_events[strtotime($eventdate).$event->ID]->all_day = $event->all_day;
									$order_events[strtotime($eventdate).$event->ID]->pec_daily_working_days = $event->pec_daily_working_days;
									$order_events[strtotime($eventdate).$event->ID]->pec_daily_every = $event->pec_daily_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_weekly_every = $event->pec_weekly_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_weekly_day = $event->pec_weekly_day;
									$order_events[strtotime($eventdate).$event->ID]->pec_exceptions = $event->pec_exceptions;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_every = $event->pec_monthly_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_position = $event->pec_monthly_position;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_day = $event->pec_monthly_day;

									$daily_events_total++;
								} elseif(strtotime($eventdate) < strtotime($startdate_orig)) {
									$i--;
								}
								$k++;
							}
							break;
						case 2:
							$k = 1;
							$startdate = $event->date;
							$weeksdiff = 0;
							
							if(strtotime($startdate) < strtotime($startdate_orig)) {
								
								$weeksdiff = dpProEventCalendar_datediffInWeeks($startdate, $startdate_orig);
								//echo "weeksdiff : ".$weeksdiff;
								$startdate = date("Y-m-d H:i:s", strtotime('+'.($weeksdiff - 1).' weeks', strtotime($startdate)));
										
							}
							
							$pec_weekly_day = $event->pec_weekly_day;
							
							if(is_array($pec_weekly_day)) {
								
								$last_day = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($event->date)), date("d", strtotime($event->date)), date("y", strtotime($event->date))) - 86400);
								$original_date = $event->date;
								//echo "XXXX".$original_date.'<br>';
								$original_date = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($original_date)));
								
								for($i = 1; $i <= $list_limit; $i++) {
								
									//echo "DONE WEEKLY 1";
									foreach($pec_weekly_day as $week_day) {
										$day = "";
										
										switch($week_day) {
											case 1:
												$day = "Monday";
												break;	
											case 2:
												$day = "Tuesday";
												break;	
											case 3:
												$day = "Wednesday";
												break;	
											case 4:
												$day = "Thursday";
												break;	
											case 5:
												$day = "Friday";
												break;	
											case 6:
												$day = "Saturday";
												break;	
											case 7:
												$day = "Sunday";
												break;	
										}
										
										if($weeksdiff == 0 && $week_day > 1 && $week_day < date('N', strtotime($original_date))) {
											//$i--;
											continue;	
										}
										
										$event->date = date("Y-m-d H:i:s", strtotime("next ".$day, strtotime($original_date)));
										
										$eventdate = date("Y-m-d", strtotime($last_day.' next '.date("l", strtotime($event->date))));
										
										$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($last_day)), date("i", strtotime($last_day)), 0, date("m", strtotime($eventdate)), date("d", strtotime($eventdate)), date("y", strtotime($eventdate))));
										$last_day = $eventdate;
										
										if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
											break;	
										}
										
										if($eventdate != "" && $event->pec_exceptions != "") {
											$exceptions = explode(',', $event->pec_exceptions);
											if(in_array(substr($eventdate, 0, 10), $exceptions)) {
												$i--;
												continue;
											}
										}
										
										if(strtotime(($eventdate)) < strtotime($startdate_orig)) {
											$i--;
											continue;	
										}
										
										if($event->pec_weekly_every > 1 && 
											( ((strtotime(substr($eventdate,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
										) {
											//$i--;
											continue;
										}
										
										
										if(
											(
												(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
												|| $event->end_date == '0000-00-00' 
												|| $event->end_date == ''
											) 
											&& (strtotime($eventdate) >= strtotime($startdate_orig))
										) {
											$order_events[strtotime($eventdate).$event->ID] = new stdClass;
											$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
											$order_events[strtotime($eventdate).$event->ID]->recurring_frecuency = $event->recurring_frecuency;
											$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;
											$order_events[strtotime($eventdate).$event->ID]->end_date = $event->end_date;
											$order_events[strtotime($eventdate).$event->ID]->end_time_hh = $event->end_time_hh;
											$order_events[strtotime($eventdate).$event->ID]->end_time_mm = $event->end_time_mm;
											$order_events[strtotime($eventdate).$event->ID]->title = $event->title;
											$order_events[strtotime($eventdate).$event->ID]->description = $event->description;
											$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
											$order_events[strtotime($eventdate).$event->ID]->hide_time = $event->hide_time;
											$order_events[strtotime($eventdate).$event->ID]->location = $event->location;
											$order_events[strtotime($eventdate).$event->ID]->phone = $event->phone;
											$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
											$order_events[strtotime($eventdate).$event->ID]->map = $event->map;
											$order_events[strtotime($eventdate).$event->ID]->share = $event->share;
											$order_events[strtotime($eventdate).$event->ID]->all_day = $event->all_day;
											$order_events[strtotime($eventdate).$event->ID]->pec_daily_working_days = $event->pec_daily_working_days;
											$order_events[strtotime($eventdate).$event->ID]->pec_daily_every = $event->pec_daily_every;
											$order_events[strtotime($eventdate).$event->ID]->pec_weekly_every = $event->pec_weekly_every;
											$order_events[strtotime($eventdate).$event->ID]->pec_weekly_day = $event->pec_weekly_day;
											$order_events[strtotime($eventdate).$event->ID]->pec_exceptions = $event->pec_exceptions;
											$order_events[strtotime($eventdate).$event->ID]->pec_monthly_every = $event->pec_monthly_every;
											$order_events[strtotime($eventdate).$event->ID]->pec_monthly_position = $event->pec_monthly_position;
											$order_events[strtotime($eventdate).$event->ID]->pec_monthly_day = $event->pec_monthly_day;
										}
										
									}
								}
								$k++;
							} else {
								$last_day = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate)), date("d", strtotime($startdate)), date("y", strtotime($startdate))) - 86400);
								for($i = 1; $i <= $list_limit; $i++) {
								//echo "DONE WEEKLY 2";
									$eventdate = date("Y-m-d", strtotime($last_day.' next '.date("l", strtotime($startdate))));
									$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($last_day)), date("i", strtotime($last_day)), 0, date("m", strtotime($eventdate)), date("d", strtotime($eventdate)), date("y", strtotime($eventdate))));
									$last_day = $eventdate;
									
											
									if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
										break;	
									}
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										$exceptions = explode(',', $event->pec_exceptions);
										
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											$i--;
											continue;
										}
									}
	
									if(strtotime(($eventdate)) < strtotime($startdate_orig)) {
										$i--;
										continue;	
									}
									
									if($event->pec_weekly_every > 1 && 
										( ((strtotime(substr($eventdate,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24)) % ($event->pec_weekly_every * 7) != 0 )
									) {
										//echo "DATEDIFF: ".(strtotime(substr($eventdate,0,11)) - strtotime(substr($event->date,0,11))) / (60 * 60 * 24);
										//$i--;
										continue;
									}
	
									if(
										(
											(strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) 
											|| $event->end_date == '0000-00-00' 
											|| $event->end_date == ''
										) 
										&& (strtotime($eventdate) >= strtotime($startdate_orig))
									) {
										$order_events[strtotime($eventdate).$event->ID] = new stdClass;
										$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
										$order_events[strtotime($eventdate).$event->ID]->recurring_frecuency = $event->recurring_frecuency;
										$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;
										$order_events[strtotime($eventdate).$event->ID]->end_date = $event->end_date;
										$order_events[strtotime($eventdate).$event->ID]->end_time_hh = $event->end_time_hh;
										$order_events[strtotime($eventdate).$event->ID]->end_time_mm = $event->end_time_mm;
										$order_events[strtotime($eventdate).$event->ID]->title = $event->title;
										$order_events[strtotime($eventdate).$event->ID]->description = $event->description;
										$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
										$order_events[strtotime($eventdate).$event->ID]->hide_time = $event->hide_time;
										$order_events[strtotime($eventdate).$event->ID]->location = $event->location;
										$order_events[strtotime($eventdate).$event->ID]->phone = $event->phone;
										$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
										$order_events[strtotime($eventdate).$event->ID]->map = $event->map;
										$order_events[strtotime($eventdate).$event->ID]->share = $event->share;
										$order_events[strtotime($eventdate).$event->ID]->all_day = $event->all_day;
										$order_events[strtotime($eventdate).$event->ID]->pec_daily_working_days = $event->pec_daily_working_days;
										$order_events[strtotime($eventdate).$event->ID]->pec_daily_every = $event->pec_daily_every;
										$order_events[strtotime($eventdate).$event->ID]->pec_weekly_every = $event->pec_weekly_every;
										$order_events[strtotime($eventdate).$event->ID]->pec_weekly_day = $event->pec_weekly_day;
										$order_events[strtotime($eventdate).$event->ID]->pec_exceptions = $event->pec_exceptions;
										$order_events[strtotime($eventdate).$event->ID]->pec_monthly_every = $event->pec_monthly_every;
										$order_events[strtotime($eventdate).$event->ID]->pec_monthly_position = $event->pec_monthly_position;
										$order_events[strtotime($eventdate).$event->ID]->pec_monthly_day = $event->pec_monthly_day;
									}
								}
								$k++;
							}
							break;
						case 3:
							$k = 1;
							$startdate = $event->date;
							
							if(strtotime($startdate) < strtotime($startdate_orig)) {
								
								$startdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($startdate_orig)), date("d", strtotime($event->date)), date("y", strtotime($startdate_orig))));
									
							}
							
							$counter_m = 1;
							if(isset($events_month) || ($event->pec_monthly_day != "" && $event->pec_monthly_position != "")) {
								$counter_m = 0;	
							}
							for($i = 1; $i <= $list_limit; $i++) {
								//echo "DONE MONTHLY";
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($startdate)), date("i", strtotime($startdate)), 0, date("m", strtotime($startdate))+((strtotime($startdate) < time() && !isset($events_month)) || $k > 1 ? $counter_m : 0), date("d", strtotime($startdate)), date("y", strtotime($startdate))));
								
								if($eventdate != "" && $event->pec_exceptions != "") {
									
									$exceptions = explode(',', $event->pec_exceptions);
									
									if(in_array(substr($eventdate, 0, 10), $exceptions)) {
										//X NO $i--;
										continue;
									}
									
								}
								
								//$html .= $event->pec_monthly_day. " - " .$event->pec_monthly_position;
								if($event->pec_monthly_day != "" && $event->pec_monthly_position != "") {
									
									$eventdate = str_replace(substr($eventdate, 5, 5), date('m-d', strtotime($event->pec_monthly_position.' '.$event->pec_monthly_day.' of '.date("F Y", strtotime($eventdate)))), $eventdate);
									
									if($eventdate != "" && $event->pec_exceptions != "") {
										
										if(in_array(substr($eventdate, 0, 10), $exceptions)) {
											// X NO $i--;
											$counter_m++;
											continue;
										}
										
									}
									
									if(strtotime(($eventdate)) > strtotime($enddate_orig) && ($event->end_date != '0000-00-00' && $event->end_date != '')) {
										break;	
									}
									//$html .= $eventdate."XXX";

								}
								
								if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
									break;	
								}
								
								if($event->pec_monthly_every > 1 && 
									( !is_int (((date('m', strtotime($eventdate)) - date('m', strtotime(substr($event->date,0,11))))) / ($event->pec_monthly_every)) )
								) {
									//No
									//$i--;
									$counter_m++;
									continue;
								}
								
								if(strtotime($startdate) < current_time('timestamp') || $i > 1) {
									$counter_m++;
								}
								if(((strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) || $event->end_date == '0000-00-00' || $event->end_date == '') && (strtotime($eventdate) >= strtotime($startdate_orig))) {
									$order_events[strtotime($eventdate).$event->ID] = new stdClass;
									$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
									$order_events[strtotime($eventdate).$event->ID]->recurring_frecuency = $event->recurring_frecuency;									
									$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;
									$order_events[strtotime($eventdate).$event->ID]->end_date = $event->end_date;
									$order_events[strtotime($eventdate).$event->ID]->end_time_hh = $event->end_time_hh;
									$order_events[strtotime($eventdate).$event->ID]->end_time_mm = $event->end_time_mm;
									$order_events[strtotime($eventdate).$event->ID]->title = $event->title;
									$order_events[strtotime($eventdate).$event->ID]->description = $event->description;
									$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
									$order_events[strtotime($eventdate).$event->ID]->hide_time = $event->hide_time;
									$order_events[strtotime($eventdate).$event->ID]->location = $event->location;
									$order_events[strtotime($eventdate).$event->ID]->phone = $event->phone;
									$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
									$order_events[strtotime($eventdate).$event->ID]->map = $event->map;
									$order_events[strtotime($eventdate).$event->ID]->share = $event->share;
									$order_events[strtotime($eventdate).$event->ID]->all_day = $event->all_day;
									$order_events[strtotime($eventdate).$event->ID]->pec_daily_working_days = $event->pec_daily_working_days;
									$order_events[strtotime($eventdate).$event->ID]->pec_daily_every = $event->pec_daily_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_weekly_every = $event->pec_weekly_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_weekly_day = $event->pec_weekly_day;
									$order_events[strtotime($eventdate).$event->ID]->pec_exceptions = $event->pec_exceptions;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_every = $event->pec_monthly_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_position = $event->pec_monthly_position;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_day = $event->pec_monthly_day;
								}
								$k++;
							}
							break;	
						case 4:
							$k = 1;
							$counter_y = 1;
							if(isset($events_month)) {
								$counter_y = 0;	
							}
							for($i = 1; $i <= $list_limit; $i++) {
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($event->date)), date("d", strtotime($event->date)), date("y")+((strtotime($event->date) < time() && !isset($events_month)) || $k > 1 ? $counter_y : 0)));

								if($eventdate != "" && $event->pec_exceptions != "") {
									$exceptions = explode(',', $event->pec_exceptions);
									
									if(in_array(substr($eventdate, 0, 10), $exceptions)) {
										$i--;
										continue;
									}
								}
								
								if(strtotime(($eventdate)) > strtotime($enddate_orig)) {
									break;	
								}
								
								if((!is_null($events_month) || $past) && strtotime(($eventdate)) > strtotime($enddate_orig)) {
									break;	
								}
								
								if(strtotime($event->date) < time() || $i > 1) {
									$counter_y++;
								}
								if(((strtotime($eventdate) <= strtotime($enddate_orig) && strtotime($eventdate) <= strtotime($event->end_date." 23:59:59")) || $event->end_date == '0000-00-00' || $event->end_date == '') && (strtotime($eventdate) >= strtotime($startdate_orig))) {
									$order_events[strtotime($eventdate).$event->ID] = new stdClass;
									$order_events[strtotime($eventdate).$event->ID]->id = $event->ID;
									$order_events[strtotime($eventdate).$event->ID]->recurring_frecuency = $event->recurring_frecuency;									
									$order_events[strtotime($eventdate).$event->ID]->date = $eventdate;
									$order_events[strtotime($eventdate).$event->ID]->end_date = $event->end_date;
									$order_events[strtotime($eventdate).$event->ID]->end_time_hh = $event->end_time_hh;
									$order_events[strtotime($eventdate).$event->ID]->end_time_mm = $event->end_time_mm;
									$order_events[strtotime($eventdate).$event->ID]->title = $event->title;
									$order_events[strtotime($eventdate).$event->ID]->description = $event->description;
									$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
									$order_events[strtotime($eventdate).$event->ID]->hide_time = $event->hide_time;
									$order_events[strtotime($eventdate).$event->ID]->location = $event->location;
									$order_events[strtotime($eventdate).$event->ID]->phone = $event->phone;
									$order_events[strtotime($eventdate).$event->ID]->link = $event->link;
									$order_events[strtotime($eventdate).$event->ID]->map = $event->map;
									$order_events[strtotime($eventdate).$event->ID]->share = $event->share;
									$order_events[strtotime($eventdate).$event->ID]->all_day = $event->all_day;
									$order_events[strtotime($eventdate).$event->ID]->pec_daily_working_days = $event->pec_daily_working_days;
									$order_events[strtotime($eventdate).$event->ID]->pec_daily_every = $event->pec_daily_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_weekly_every = $event->pec_weekly_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_weekly_day = $event->pec_weekly_day;
									$order_events[strtotime($eventdate).$event->ID]->pec_exceptions = $event->pec_exceptions;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_every = $event->pec_monthly_every;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_position = $event->pec_monthly_position;
									$order_events[strtotime($eventdate).$event->ID]->pec_monthly_day = $event->pec_monthly_day;
								}
								$k++;
							}
							break;
					}
					
				} else {
					
					$enddate_orig = $event->end_date." 23:59:59";
					if($event->all_day) {
						$startdate_orig = date('Y-m-d'). " 00:00:00";
					} else {
						$startdate_orig = current_time('mysql');	
					}
					if(!is_null($events_month)) {
						$startdate_orig = $events_month;
						$enddate_orig = $events_month_end;
					}
					
					$continue = 0;
					
					if($past) {
						//$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
						$enddate_orig =  current_time('mysql');
						$startdate_orig = $enddate_orig;
						
						if(strtotime(($event->date)) > strtotime($startdate_orig)) {
							$continue = 1;	
						}

					} else {
						
						if(!is_null($events_month)) {
							if(substr($event->date,0,10) < substr($startdate_orig,0,10) || substr($event->date,0,10) > substr($enddate_orig,0,10)) {
								//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
								$continue = 1;	
							}
						} else {
							if(strtotime(($event->date)) < strtotime($startdate_orig)) {
								//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
								$continue = 1;	
							}
						}

					}
					
					if(!$continue) {
						$order_events[strtotime($event->date).$event->ID] = $event;
					}
				}
				
				if(!empty($event->pec_extra_dates)) {
					foreach($event->pec_extra_dates as $extra_date) {
						if($extra_date == "") {
							continue;
						}
						if(strlen(trim($extra_date)) <= 12) {
							$extra_date = $extra_date . ' ' . date('H:i:s', strtotime($event->date));
						}
						
						$enddate_orig = $event->end_date." 23:59:59";
						if($event->all_day) {
							$startdate_orig = date('Y-m-d'). " 00:00:00";
						} else {
							$startdate_orig = current_time('mysql');	
						}
						if(!is_null($events_month)) {
							$startdate_orig = $events_month;
							$enddate_orig = $events_month_end;
						}
						
						if($past) {
							//$startdate_orig = date('Y-m-d H:i:s', strtotime('-30 days'));
							$enddate_orig =  current_time('mysql');
							$startdate_orig = $enddate_orig;
							
							if(strtotime(($extra_date)) > strtotime($startdate_orig)) {
								continue;	
							}
	
						} else {
							
							if(!is_null($events_month)) {
								
								if(substr($extra_date,0,10) < substr($startdate_orig,0,10) || substr($extra_date,0,10) > substr($enddate_orig,0,10)) {
									//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
									continue;	
								}
								
							} else {
								if(strtotime(($extra_date)) < strtotime($startdate_orig)) {
									//if(strtotime(substr($event->date, 0, 10)) < strtotime(substr($startdate_orig, 0, 10))) {
									continue;	
								}	
							}
	
						}
						
						$order_events[strtotime($extra_date).$event->ID] = new stdClass;
						$order_events[strtotime($extra_date).$event->ID]->id = $event->ID;
						$order_events[strtotime($extra_date).$event->ID]->recurring_frecuency = $event->recurring_frecuency;									
						$order_events[strtotime($extra_date).$event->ID]->date = $extra_date;
						$order_events[strtotime($extra_date).$event->ID]->end_date = $event->end_date;
						$order_events[strtotime($extra_date).$event->ID]->end_time_hh = $event->end_time_hh;
						$order_events[strtotime($extra_date).$event->ID]->end_time_mm = $event->end_time_mm;
						$order_events[strtotime($extra_date).$event->ID]->title = $event->title;
						$order_events[strtotime($extra_date).$event->ID]->description = $event->description;
						$order_events[strtotime($extra_date).$event->ID]->link = $event->link;
						$order_events[strtotime($extra_date).$event->ID]->hide_time = $event->hide_time;
						$order_events[strtotime($extra_date).$event->ID]->location = $event->location;
						$order_events[strtotime($extra_date).$event->ID]->phone = $event->phone;
						$order_events[strtotime($extra_date).$event->ID]->link = $event->link;
						$order_events[strtotime($extra_date).$event->ID]->map = $event->map;
						$order_events[strtotime($extra_date).$event->ID]->share = $event->share;
						$order_events[strtotime($extra_date).$event->ID]->all_day = $event->all_day;
						$order_events[strtotime($extra_date).$event->ID]->pec_daily_working_days = $event->pec_daily_working_days;
						$order_events[strtotime($extra_date).$event->ID]->pec_daily_every = $event->pec_daily_every;
						$order_events[strtotime($extra_date).$event->ID]->pec_weekly_every = $event->pec_weekly_every;
						$order_events[strtotime($extra_date).$event->ID]->pec_weekly_day = $event->pec_weekly_day;
						$order_events[strtotime($extra_date).$event->ID]->pec_exceptions = $event->pec_exceptions;
						$order_events[strtotime($extra_date).$event->ID]->pec_monthly_every = $event->pec_monthly_every;
						$order_events[strtotime($extra_date).$event->ID]->pec_monthly_position = $event->pec_monthly_position;
						$order_events[strtotime($extra_date).$event->ID]->pec_monthly_day = $event->pec_monthly_day;		
					}
				}
			}
			
			if(!function_exists('dp_pec_cmp')) {
				function dp_pec_cmp($a, $b) {
					$a = strtotime($a->date);
					$b = strtotime($b->date);
					if ($a == $b) {
						return 0;
					}
					return ($a < $b) ? -1 : 1;
				}
			}
			
			if(!function_exists('dp_pec_cmp_reverse')) {
				function dp_pec_cmp_reverse($a, $b) {
					$a = strtotime($a->date);
					$b = strtotime($b->date);
					if ($a == $b) {
						return 0;
					}
					return ($a < $b) ? 1 : -1;
				}
			}
			
			if($past) {
				usort($order_events, "dp_pec_cmp_reverse");
			} else {
				usort($order_events, "dp_pec_cmp");
			}
				
			//ksort($order_events, SORT_NUMERIC);
			if($return_data) {
				if($limit != '' && $auto_limit) { $order_events = array_slice($order_events, 0, ($limit + $daily_events_total)); }
				
				return $order_events;
			}
			
			$pagination = (is_numeric($dpProEventCalendar['pagination']) && $dpProEventCalendar['pagination'] > 0 ? $dpProEventCalendar['pagination'] : 10);
			if(is_numeric($this->opts['pagination']) && $this->opts['pagination'] > 0) {
				$pagination = $this->opts['pagination'];
			}

			$event_counter = 1;
			$event_columns_counter = 0;
			
			if(empty($order_events)) {
				$html .= '
				<div class="dp_pec_date_event dp_pec_isotope">
					<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
				</div>';
			} else {
				$event_reg = array();
				
				$html .= "<div class='dp_pec_clear'></div>
				<div class='".(is_numeric($this->columns) && $this->columns > 1 ? 'pec_upcoming_layout' : '')."'>";
				
				$last_date = "";
				
				foreach($order_events as $event) {
					
					if($event->id == "") 
						$event->id = $event->ID;
					
					if($event_counter > $list_limit) { break; }
					
					
					$all_working_days = '';
					if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
						$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
						$event->date = $event->orig_date;
					}
					
					if($this->columns == "")  {
						
						$html .= "<div class='clear'></div>";
						
						$event_columns_counter = 0;
						
					}
					
					if($this->calendar_obj->format_ampm) {
						$time = date('h:i A', strtotime($event->date));
					} else {
						$time = date('H:i', strtotime($event->date));				
					}
					$start_day = date('d', strtotime($event->date));
					$start_month = date('n', strtotime($event->date));
					
					$end_date = '';
					if($event->end_date != "" && $event->end_date != "0000-00-00" && $show_end_date) {
						$end_day = date('d', strtotime($event->end_date));
						$end_month = date('n', strtotime($event->end_date));
						
						//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3);
						$end_date = ' - '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
					}
					
					//$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
					$start_date = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date));
					
					if($start_date == $end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3)) { $end_date = ""; }
					if($event->recurring_frecuency != 1) {
						$end_date = "";
					} elseif(in_array($event->id, $event_reg)) {
						continue;	
					}
					
					$end_time = "";
					if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
					
					if($end_time != "") {
						
						if($this->calendar_obj->format_ampm) {
							$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
						} else {
							$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
						}
						$end_time = " - ".$end_time_tmp;
						if($end_time_tmp == $time) {
							$end_time = "";	
						}
					}
					
					if($event->all_day) {
						$time = $this->translation['TXT_ALL_DAY'];
						$end_time = "";
					}
					
					if(date('Y-m-d', strtotime($event->date)) != $last_date) {
						$last_date = date('Y-m-d', strtotime($event->date));
						$start_date_year = date('Y', strtotime($event->date));
						$start_date_formatted = str_replace(array(',', '.', '/', '|'), '', $start_date);
						$start_date_formatted = str_replace($start_date_year, '', $start_date_formatted);
						
						if(is_numeric($this->columns) && $this->columns > 1 && $event_counter == 1 && false) {
							$html .= '
							<div class="'.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'"></div>';
						}
						
						if(!is_numeric($this->columns) || $this->columns <= 1) {
							$html .= '
							<div class="dp_pec_columns_1 dp_pec_isotope dp_pec_date_event_wrap dp_pec_date_block_wrap" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>
								<span class="fa fa-calendar-o"></span>
								<div class="dp_pec_date_block">'.$start_date_formatted.'<span>'.$start_date_year.'</span></div>
							</div>
							<div class="dp_pec_clear"></div>';	
						}
					}
					
					$html .= '
					<div class="dp_pec_isotope '.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'" data-event-number="'.$event_counter.'" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>
						<div class="dp_pec_date_event dp_pec_upcoming">';
					
					$post_thumbnail_id = get_post_thumbnail_id( $event->id );
					$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
					if($post_thumbnail_id) {
						$html .= '<div class="dp_pec_event_photo">';
						$html .= '<img src="'.$image_attributes[0].'" alt="" />';
						$html .= '</div>';
					}
					
					$edit_button = $this->getEditRemoveButtons($event);
					
					//<a href="'.dpProEventCalendar_get_permalink($event->id).'"></a>
					if($end_date == ' - '.$start_date) {
						$end_date = '';	
					}
					
					if(is_numeric($this->columns) && $this->columns > 1) {
						$html .= '
						<span class="dp_pec_date_time"><i class="fa fa-calendar-o"></i>'.$start_date.$end_time.$end_date.'</span>';
					}
					
					$html .= '
						<span class="dp_pec_date_time"><i class="fa fa-clock-o"></i>'.$all_working_days.' ';
					if(($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) {
						$html .= $time;
					}
					$html .= '</span>'.$edit_button.'
					<div class="dp_pec_clear"></div>';
					
					$title = '<span class="dp_pec_event_title_sp">'.$event->title.'</span>';
					if($this->calendar_obj->link_post) {
						$title = '<a href="'.dpProEventCalendar_get_permalink($event->id).'">'.$title.'</a>';	
					}
					
					$html .= '
						<h2 class="dp_pec_event_title">'.$title.'</h2><div class="dp_pec_clear"></div>';
				
					if($this->calendar_obj->show_author) {
						$author = get_userdata(get_post_field( 'post_author', $event->id ));
						$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
					}
					
					if($event->location != '') {
						$html .= '
						<span class="dp_pec_event_location"><i class="fa fa-map-marker"></i>'.$event->location.'</span>';
					}
					
					if($event->phone != '') {
						$html .= '
						<span class="dp_pec_event_phone"><i class="fa fa-phone"></i>'.$event->phone.'</span>';
					}
					
					$category = get_the_terms( $event->id, 'pec_events_category' ); 
					if(!empty($category)) {
						$category_count = 0;
						$html .= '
							<span class="dp_pec_event_categories">';
							
						foreach ( $category as $cat){
							if($category_count > 0) {
								$html .= " / ";	
							}
							$html .= $cat->name;
							$category_count++;
						}
						$html .= '
							</span>';
					}
					
					if(is_array($dpProEventCalendar['custom_fields_counter'])) {
						$counter = 0;
						
						foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
							$field_value = get_post_meta($event->id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
							if($field_value != "") {
								$html .= '<div class="pec_event_page_custom_fields"><strong>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</strong>';
								if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
									$html .= '<i class="fa fa-check"></i>';
								} else {
									$html .= '<p>'.$field_value.'</p>';
								}
								$html .= '</div>';
							}
							$counter++;		
						}
					}
					
					$event_desc = nl2br(strip_tags(str_replace("</p>", "<br />", preg_replace("/<p[^>]*?>/", "", $event->description)), '<iframe><strong><h1><h2><h3><h4><h5><h6><h7><b><i><em><pre><code><del><ins><img><a><ul><li><ol><blockquote><br><hr><span><p>'));
					
					$html .= $this->getBookingButton($event->id, date('Y-m-d', strtotime($event->date)));
					
					//if($this->limit_description > 0) {
					if($this->limit_description == 0) {
						$this->limit_description = 30;
					}
					$event_desc_short = force_balance_tags(html_entity_decode(wp_trim_words(htmlentities($event_desc), $this->limit_description)));
						
					//}
					
					$html .= '
						<div class="dp_pec_event_description">
							<div class="dp_pec_event_description_short" '.(strlen($event_desc) > $this->limit_description ? 'style="display:block"' : '').'>
								'.do_shortcode($event_desc_short).'
								<a href="javascript:void(0);" class="dp_pec_event_description_more">'.__('More', 'dpProEventCalendar').'</a>
							</div>
							<div class="dp_pec_event_description_full" '.(strlen($event_desc) > $this->limit_description ? 'style="display:none"' : '').'>
								'.do_shortcode($event_desc).'
							</div>
						</div>';

					if($event->map != '') {
						$map_lnlat = get_post_meta($event->id, 'pec_map_lnlat', true);
						if($map_lnlat != "") {
							//$event->map = str_replace(" ", "", $map_lnlat);
						}
						$html .= '
						<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>
						<iframe class="dp_pec_date_event_map_iframe" width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_q&q='.urlencode($event->map).'&ie=UTF8&output=embed"></iframe>';
					}
						
					$html .= $this->getRating($event->id);
					
					$html .= '
						<div class="dp_pec_date_event_icons">';
					
					$html .= $this->getEventShare($event);
					
					if($event->link != '') {
						$html .= '
						<a class="dp_pec_date_event_link" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i></a>';
					}
					$html .= '
							</div>';
					if($this->calendar_obj->ical_active) {
						$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime($event->date) ) . "'>iCal</a><br class='clear' />";
					}
					$html .= '
						</div>
					</div>';
					$event_reg[] = $event->id;
					$event_counter++;
				}
				
				$html .= "</div>";	
				
				if(($event_counter - 1) > $pagination) {
					$html .= '<a href="javascript:void(0);" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.__('More', 'dpProEventCalendar').'</a>
						<div class="dp_pec_clear"></div>
					';
				}
			}
		}

		return $html;
	}
	
	function parseMysqlDate($date) {
		
		$dateArr = explode("-", $date);
		//$newDate = $dateArr[2] . " " . $this->translation['MONTHS'][($dateArr[1] - 1)] . ", " . $dateArr[0];
		$newDate = dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($date));
		return $newDate;
	}
	
	function addScripts( $print = false, $commented = false, $hidden = false ) 
	{
		global $dpProEventCalendar;
		
		$script = '';
		if($commented) {
			$script .= $this->addScripts(false, false, true);	
		}
		$script .= '<script type="text/javascript">
		// <![CDATA[
		';
		if($commented) {
			$script .= ' /* PEC Commented Script';	
		}
		$script .= '
		jQuery(document).ready(function() {
			
			function startProEventCalendar() {
				
				jQuery("#dp_pec_id'.$this->nonce.'").dpProEventCalendar({
					nonce: "dp_pec_id'.$this->nonce.'", 
					draggable: false,
					monthNames: new Array("'.$this->translation['MONTHS'][0].'", "'.$this->translation['MONTHS'][1].'", "'.$this->translation['MONTHS'][2].'", "'.$this->translation['MONTHS'][3].'", "'.$this->translation['MONTHS'][4].'", "'.$this->translation['MONTHS'][5].'", "'.$this->translation['MONTHS'][6].'", "'.$this->translation['MONTHS'][7].'", "'.$this->translation['MONTHS'][8].'", "'.$this->translation['MONTHS'][9].'", "'.$this->translation['MONTHS'][10].'", "'.$this->translation['MONTHS'][11].'"), ';
				if($this->is_admin) {
					$script .= '
					draggable: false,
					isAdmin: true,
					';
				}
				if(!empty($this->event_id)) {
					$script .= '
					draggable: false,
					';
				}
				if(is_numeric($this->id_calendar)) {
					$script .= '
					calendar: '.$this->id_calendar.',
					';	
				}
				if(isset($this->calendar_obj->date_range_start) && $this->calendar_obj->date_range_start != NULL && !$this->is_admin && empty($this->event_id)) {
					$script .= '
					dateRangeStart: "'.$this->calendar_obj->date_range_start.'",
					';	
				}
				if(isset($this->calendar_obj->date_range_end) && $this->calendar_obj->date_range_end != NULL && !$this->is_admin && empty($this->event_id)) {
					$script .= '
					dateRangeEnd: "'.$this->calendar_obj->date_range_end.'",
					';	
				}
				if(isset($this->calendar_obj->skin) && $this->calendar_obj->skin != "" && !$this->is_admin && empty($this->event_id)) {
					$script .= '
					skin: "'.$this->calendar_obj->skin.'",
					';	
				}
				if(isset($this->type)) {
					$script .= '
					type: "'.$this->type.'",
					';	
				}
				
				if($hidden) {
					$script .= '
					selectric: false,
					';	
				}
				
				if($commented || $hidden) {
					$script .= '
					show_current_date: false,
					';	
				}
				
				if($dpProEventCalendar['recaptcha_enable'] && $dpProEventCalendar['recaptcha_site_key'] != "") {
					$script .= '
					recaptcha_enable: true,
					recaptcha_site_key: "'.$dpProEventCalendar['recaptcha_site_key'].'",
					';	
				}
				
				$script .= '
					allow_user_add_event: "'.$this->calendar_obj->allow_user_add_event.'",
					actualMonth: '.$this->datesObj->currentMonth.',
					actualYear: '.$this->datesObj->currentYear.',
					actualDay: '.$this->datesObj->currentDate.',
					defaultDate: "'.$this->defaultDate.'",
					defaultDateFormat: "'.date('Y-m-d', $this->defaultDate).'",
					current_date_color: "'.$this->calendar_obj->current_date_color.'",
					category: "'.($this->category != "" ? $this->category : '').'",
					event_id: "'.($this->event_id != "" ? $this->event_id : '').'",
					author: "'.($this->author != "" ? $this->author : '').'",
					lang_sending: "'.$this->translation['TXT_SENDING'].'",
					lang_subscribe: "'.$this->translation['TXT_SUBSCRIBE'].'",
					lang_subscribe_subtitle: "'.$this->translation['TXT_SUBSCRIBE_SUBTITLE'].'",
					lang_edit_event: "'.$this->translation['TXT_EDIT_EVENT'].'",
					lang_remove_event: "'.$this->translation['TXT_REMOVE_EVENT'].'",
					lang_your_name: "'.$this->translation['TXT_YOUR_NAME'].'",
					lang_your_email: "'.$this->translation['TXT_YOUR_EMAIL'].'",
					lang_fields_required: "'.$this->translation['TXT_FIELDS_REQUIRED'].'",
					lang_invalid_email: "'.$this->translation['TXT_INVALID_EMAIL'].'",
					lang_txt_subscribe_thanks: "'.$this->translation['TXT_SUBSCRIBE_THANKS'].'",
					lang_book_event: "'.$this->translation['TXT_BOOK_EVENT'].'",
					view: "'.($this->is_admin || $this->type == "upcoming" || !empty($this->event_id) ? 'monthly' : $this->calendar_obj->view).'"
				});
				';
				if(($this->calendar_obj->allow_user_add_event || $this->type == 'add-event') && !$this->is_admin && empty($this->event_id)) {
					
					$min_sunday = dpProEventCalendar_str_split_unicode($this->translation['DAY_SUNDAY'], 3);
					$min_monday = dpProEventCalendar_str_split_unicode($this->translation['DAY_MONDAY'], 3);
					$min_tuesday = dpProEventCalendar_str_split_unicode($this->translation['DAY_TUESDAY'], 3);
					$min_wednesday = dpProEventCalendar_str_split_unicode($this->translation['DAY_WEDNESDAY'], 3);
					$min_thursday = dpProEventCalendar_str_split_unicode($this->translation['DAY_THURSDAY'], 3);
					$min_friday = dpProEventCalendar_str_split_unicode($this->translation['DAY_FRIDAY'], 3);
					$min_saturday = dpProEventCalendar_str_split_unicode($this->translation['DAY_SATURDAY'], 3);
					
					$script .= '
					jQuery( ".dp_pec_date_input, .dp_pec_end_date_input", "#dp_pec_id'.$this->nonce.'" ).datepicker({
						beforeShow: function(input, inst) {
						   jQuery("#ui-datepicker-div").removeClass("dp_pec_datepicker");
						   jQuery("#ui-datepicker-div").addClass("dp_pec_datepicker");
					   },
						showOn: "button",
						buttonImage: "'.dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ).'",
						buttonImageOnly: false,
						dateFormat: "yy-mm-dd",
						monthNames: new Array("'.$this->translation['MONTHS'][0].'", "'.$this->translation['MONTHS'][1].'", "'.$this->translation['MONTHS'][2].'", "'.$this->translation['MONTHS'][3].'", "'.$this->translation['MONTHS'][4].'", "'.$this->translation['MONTHS'][5].'", "'.$this->translation['MONTHS'][6].'", "'.$this->translation['MONTHS'][7].'", "'.$this->translation['MONTHS'][8].'", "'.$this->translation['MONTHS'][9].'", "'.$this->translation['MONTHS'][10].'", "'.$this->translation['MONTHS'][11].'"),
						dayNamesMin: new Array("'.$min_sunday[0].'", "'.$min_monday[0].'", "'.$min_tuesday[0].'", "'.$min_wednesday[0].'", "'.$min_thursday[0].'", "'.$min_friday[0].'", "'.$min_saturday[0].'")
					});
					
					jQuery( document ).on("click", ".pec_edit_event", function() {
						
						jQuery(".dp_pec_date_input_modal, .dp_pec_end_date_input_modal", ".dpProEventCalendarModalEditEvent").datepicker({
							beforeShow: function(input, inst) {
							   jQuery("#ui-datepicker-div").removeClass("dp_pec_datepicker");
							   jQuery("#ui-datepicker-div").addClass("dp_pec_datepicker");
						   },
							showOn: "button",
							buttonImage: "'.dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ).'",
							buttonImageOnly: false,
							minDate: 0,
							dateFormat: "yy-mm-dd",
							monthNames: new Array("'.$this->translation['MONTHS'][0].'", "'.$this->translation['MONTHS'][1].'", "'.$this->translation['MONTHS'][2].'", "'.$this->translation['MONTHS'][3].'", "'.$this->translation['MONTHS'][4].'", "'.$this->translation['MONTHS'][5].'", "'.$this->translation['MONTHS'][6].'", "'.$this->translation['MONTHS'][7].'", "'.$this->translation['MONTHS'][8].'", "'.$this->translation['MONTHS'][9].'", "'.$this->translation['MONTHS'][10].'", "'.$this->translation['MONTHS'][11].'"),
							dayNamesMin: new Array("'.substr($this->translation['DAY_SUNDAY'], 0, 2).'", "'.substr($this->translation['DAY_MONDAY'], 0, 2).'", "'.substr($this->translation['DAY_TUESDAY'], 0, 2).'", "'.substr($this->translation['DAY_WEDNESDAY'], 0, 2).'", "'.substr($this->translation['DAY_THURSDAY'], 0, 2).'", "'.substr($this->translation['DAY_FRIDAY'], 0, 2).'", "'.substr($this->translation['DAY_SATURDAY'], 0, 2).'")
						});
						
					});
					';
				}
				if(!$this->is_admin && empty($this->event_id)) {
					$script .= '
					jQuery("input, textarea", "#dp_pec_id'.$this->nonce.'").placeholder();';
				}
				$script .= '
			}
			
			';
			if(!$hidden) {
				$script .= '
				if(jQuery("#dp_pec_id'.$this->nonce.'").parent().css("display") == "none") {
					jQuery("#dp_pec_id'.$this->nonce.'").parent().onShowProCalendar(function(){
						startProEventCalendar();
					});
					return;
				}';
			}
			
			$script .= '
			startProEventCalendar();
		});
		
		jQuery(window).resize(function(){
			if(jQuery(".dp_pec_layout", "#dp_pec_id'.$this->nonce.'").width() != null) {
	
				var instance = jQuery("#dp_pec_id'.$this->nonce.'");
				
				if(instance.width() < 500) {
					jQuery(instance).addClass("dp_pec_400");
	
					jQuery(".dp_pec_dayname span", instance).each(function(i) {
						jQuery(this).html(jQuery(this).html().substr(0,3));
					});
					
					jQuery(".prev_month strong", instance).hide();
					jQuery(".next_month strong", instance).hide();
					jQuery(".prev_day strong", instance).hide();
					jQuery(".next_day strong", instance).hide();
					
				} else {
					jQuery(instance).removeClass("dp_pec_400");
					jQuery(".prev_month strong", instance).show();
					jQuery(".next_month strong", instance).show();
					jQuery(".prev_day strong", instance).show();
					jQuery(".next_day strong", instance).show();
					
				}
			}
		});
		';

		if(!empty($this->event_id)) {
			$script .= '
			jQuery(".dp_pec_layout", "#dp_pec_id'.$this->nonce.'").hide();
			jQuery(".dp_pec_options_nav", "#dp_pec_id'.$this->nonce.'").hide();
			jQuery(".dp_pec_add_nav", "#dp_pec_id'.$this->nonce.'").hide();
			';
		}
		
		if($commented) {
			$script .= ' PEC Commented Script */';	
		}
		$script .= '
		
		//]]>
		</script>';
		
		if($print)
			echo $script;	
		else
			return $script;
		
	}
	
	function outputEvent($event) {
		
		$result = $this->getEventByID($event);	
		
		$html = '';
		
		$html .= '
				<div class="'.$this->calendar_obj->skin.' dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'">
					<div class="dp_pec_content">';
		
		$html .= $this->singleEventLayout($result);
		
		$html .= '		
					</div>
				</div>';
		
		return $html;
		
	}
	
	function output( $print = false ) 
	{
		global $dpProEventCalendar, $dp_pec_payments, $wpdb;
		
		$width = "";
		$html = "";

		$skin = "";
			
		if($this->opts['skin'] != "") {
			
			$skin = 'pec_skin_'.$this->opts['skin'];
		}

		if($this->type == 'calendar') {
			
			if(isset($this->calendar_obj->width) && !$this->is_admin && empty($this->event_id) && !$this->widget) { $width = 'style="width: '.$this->calendar_obj->width.$this->calendar_obj->width_unity.' " '; }
			
			if($this->is_admin) {
				$html .= '
				<div class="dpProEventCalendar_ModalCalendar">';
			}
			
				$html .= '
				<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' dp_pec_'.($this->is_admin || !empty($this->event_id) ? 'monthly' : $this->calendar_obj->view).' '.$skin.' '.$this->calendar_obj->skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>';

			if(!$this->is_admin && ($this->calendar_obj->ical_active || $this->calendar_obj->rss_active || $this->calendar_obj->subscribe_active || $this->calendar_obj->show_view_buttons)) {
				$html .= '
					<div class="dp_pec_options_nav yumi">';
				if($this->calendar_obj->show_view_buttons) {
					$html .= '
						<a href="javascript:void(0);" class="dp_pec_view dp_pec_view_action '.($this->calendar_obj->view == "monthly" || $this->calendar_obj->view == "monthly-all-events" ? "active" : "").'" data-pec-view="monthly">'.$this->translation['TXT_MONTHLY'].'</a>
						
						<a href="javascript:void(0);" class="dp_pec_view dp_pec_view_action '.($this->calendar_obj->view == "weekly" ? "active" : "").'" data-pec-view="weekly">'.$this->translation['TXT_WEEKLY'].'</a>
						
						<a href="javascript:void(0);" class="dp_pec_view dp_pec_view_action '.($this->calendar_obj->view == "daily" ? "active" : "").'" data-pec-view="daily">'.$this->translation['TXT_DAILY'].'</a>
					';
				}
				$html .= '
					<div class="dp_pec_options_nav_divider"></div>';
				if($this->calendar_obj->ical_active) {
					$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$this->id_calendar ) . "'>iCal</a>";
				}
				if($this->calendar_obj->rss_active) {
					$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/rss.php?calendar_id='.$this->id_calendar ) . "'>RSS</a>";
				}
				if($this->calendar_obj->subscribe_active) {
					$html .= "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='javascript:void(0);'>".$this->translation['TXT_SUBSCRIBE']."</a>";
				}
				$html .= '
						<div class="dp_pec_clear"></div>
					</div>
				';
			}
			
			$allow_user_add_event_roles = explode(',', $this->calendar_obj->allow_user_add_event_roles);
			$allow_user_add_event_roles = array_filter($allow_user_add_event_roles);
			
			if(!is_array($allow_user_add_event_roles) || empty($allow_user_add_event_roles) || $allow_user_add_event_roles == "") {
				$allow_user_add_event_roles = array('all');	
			}
			
			if($this->calendar_obj->allow_user_add_event && 
				!$this->is_admin && 
					(in_array(dpProEventCalendar_get_user_role(), $allow_user_add_event_roles) || 
					 in_array('all', $allow_user_add_event_roles) || 
					 (!is_user_logged_in() && !$this->calendar_obj->assign_events_admin)
				    )
			) {
				
				$html .= '
					<div class="dp_pec_add_nav">';
					$html .= '
						<a href="javascript:void(0);" class="dp_pec_view dp_pec_add_event pec_action_btn dp_pec_btnright">'.$this->translation['TXT_ADD_EVENT'].'</a>
						<a href="javascript:void(0);" class="dp_pec_view dp_pec_cancel_event pec_action_btn dp_pec_btnright">'.$this->translation['TXT_CANCEL'].'</a>
						<div class="dp_pec_clear"></div>
						';
					$html .= '
						<div class="dp_pec_add_form">';
					if(!is_user_logged_in() && !$this->calendar_obj->assign_events_admin) {
						$html .= '
							<div class="dp_pec_notification_box dp_pec_visible">
							'.$this->translation['TXT_EVENT_LOGIN'].'
							</div>';
					} else {
						$html .= '
							<div class="dp_pec_notification_box dp_pec_notification_event_succesfull">
							'.$this->translation['TXT_EVENT_THANKS'].'
							</div>';
						$html .= '
							<form name="dp_pec_event_form" class="add_new_event_form" enctype="multipart/form-data" method="post">
								<div class="pec-add-body">
									<div class="">
										<div class="dp_pec_row">
											<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_TITLE'].'" id="" class="dp_pec_form_title" name="title" />
										</div>
										';
										if($this->calendar_obj->form_show_description) {
											$html .= '
										<div class="dp_pec_row">';
											if($this->calendar_obj->form_text_editor) {
												// Turn on the output buffer
												ob_start();
												
												// Echo the editor to the buffer
												wp_editor('', $this->nonce.'_event_description', array('media_buttons' => false, 'textarea_name' => 'description', 'quicktags' => false, 'textarea_rows' => 5, 'teeny' => true));
												
												// Store the contents of the buffer in a variable
												$editor_contents = ob_get_clean();
												
												$html .= '<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_DESCRIPTION'].'</span>'.$editor_contents;
													
											} else {
												$html .= '<textarea placeholder="'.$this->translation['TXT_EVENT_DESCRIPTION'].'" id="" name="description" cols="50" rows="5"></textarea>';	
											}
											
											$html .= '
										</div>
										';
										}
										$html .= '
										
										<div class="dp_pec_row dp_pec_cal_new_sub">
											<div class="dp_pec_col6">
												';
												if($this->calendar_obj->form_show_category) {
													$cat_args = array(
															'taxonomy' => 'pec_events_category',
															'hide_empty' => 0
														);
													if($this->calendar_obj->category_filter_include != "") {
														$cat_args['include'] = $this->calendar_obj->category_filter_include;
													}
													$categories = get_categories($cat_args); 
													if(count($categories) > 0) {
														$html .= '
														<div class="dp_pec_row">
															<div class="dp_pec_col12">
																<span class="dp_pec_form_desc">'.$this->translation['TXT_CATEGORY'].'</span>
																';
																foreach ($categories as $category) {
																	$html .= '<div class="pec_checkbox_list">';
																	$html .= '<input type="checkbox" name="category-'.$category->term_id.'" class="checkbox" value="'.$category->term_id.'" />';
																	$html .= $category->cat_name;
																	$html .= '</div>';
																  }
																$html .= '	
																<div class="dp_pec_clear"></div>
															</div>
														</div>
														';
													}
												}
												$html .= '
												<div class="dp_pec_row">
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_DATE'].'</span>
														<input type="text" readonly="readonly" name="date" maxlength="10" id="" class="large-text dp_pec_date_input" value="'.date('Y-m-d').'" style="width:80px;" />
													</div>
													
													<div class="dp_pec_col6 dp_pec_end_date_form">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_DATE'].'</span>
														<input type="text" readonly="readonly" name="end_date" maxlength="10" id="" class="large-text dp_pec_end_date_input" value="" style="width:80px;" />
														<button type="button" class="dp_pec_clear_end_date">
															<img src="'.dpProEventCalendar_plugin_url( 'images/admin/clear.png' ).'" alt="Clear" title="Clear">
														</button>
													</div>
													<div class="dp_pec_clear"></div>
												</div>
												<div class="dp_pec_row">
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_TIME'].'</span>
														<select name="time_hours" class="dp_pec_new_event_time" id="" style="width:'.($this->calendar_obj->format_ampm ? '70' : '50').'px;">';
														
															for($i = 0; $i <= 23; $i++) {
																$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
																if($this->calendar_obj->format_ampm) {
																	$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));
																}
																$html .= '
																<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
															}
														$html .= '
														</select>
														<select name="time_minutes" class="dp_pec_new_event_time" id="pec_time_minutes" style="width:50px;">';
															for($i = 0; $i <= 59; $i += 5) {
																$html .= '
																<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
															}
														$html .= '
														</select>
													</div>
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_TIME'].'</span>
														<select name="end_time_hh" class="dp_pec_new_event_time" id="" style="width:'.($this->calendar_obj->format_ampm ? '70' : '50').'px;">
															<option value="">--</option>';
															for($i = 0; $i <= 23; $i++) {
																$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
																if($this->calendar_obj->format_ampm) {
																	$hour = ($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour)) . ' ' . date('A', mktime($hour, 0));
																}
																$html .= '
																<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
															}
														$html .= '
														</select>
														<select name="end_time_mm" class="dp_pec_new_event_time" id="" style="width:50px;">
															<option value="">--</option>';
															for($i = 0; $i <= 59; $i += 5) {
																$html .= '
																<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
															}
														$html .= '
														</select>
													</div>
													<div class="dp_pec_clear"></div>
												</div>
												
												<div class="dp_pec_row">
												
												';
												
												if($this->calendar_obj->form_show_hide_time) {
													$html .= '
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_HIDE_TIME'].'</span>
														<select name="hide_time">
															<option value="0">'.$this->translation['TXT_NO'].'</option>
															<option value="1">'.$this->translation['TXT_YES'].'</option>
														</select>
														
														<div class="dp_pec_clear"></div>
														';
														
														if($this->calendar_obj->form_show_all_day) {
															
															$html .= '
															<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
															<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>';
																
														}
														
													$html .= '
													</div>';
												} elseif($this->calendar_obj->form_show_all_day) {
													
													$html .= '
													<div class="dp_pec_col6">
														<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
														<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>
													</div>';	
					
												}
												
												if($this->calendar_obj->form_show_frequency) {
													$html .= '
													<div class="dp_pec_col6">
														<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_FREQUENCY'].'</span>
														<select name="recurring_frecuency" id="pec_recurring_frecuency" class="pec_recurring_frequency">
															<option value="0">'.$this->translation['TXT_NONE'].'</option>
															<option value="1">'.$this->translation['TXT_EVENT_DAILY'].'</option>
															<option value="2">'.$this->translation['TXT_EVENT_WEEKLY'].'</option>
															<option value="3">'.$this->translation['TXT_EVENT_MONTHLY'].'</option>
															<option value="4">'.$this->translation['TXT_EVENT_YEARLY'].'</option>
														</select>
													';
								
														$html .= '
														<div class="pec_daily_frequency" style="display:none;">
														
															<div class="dp_pec_clear"></div>
														
															<div id="pec_daily_every_div">' . __('Every','dpProEventCalendar') . ' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" maxlength="2" class="dp_pec_new_event_text" name="pec_daily_every" id="pec_daily_every" value="1" /> '.__('days','dpProEventCalendar') . ' </div>
															
															<div class="dp_pec_clear"></div>
															
															<div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" class="checkbox" onclick="pec_check_daily_working_days(this);" value="1" />'. __('All working days','dpProEventCalendar') . '</div>
														</div>';
														
														$html .= '
														<div class="pec_weekly_frequency" style="display:none;">
															
															<div class="dp_pec_clear"></div>
															
															'. __('Repeat every','dpProEventCalendar').' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_weekly_every" value="1" /> '. __('week(s) on:','dpProEventCalendar').'
															
															<div class="dp_pec_clear"></div>
															
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="1" name="pec_weekly_day[]" /> &nbsp; '. __('Mon','dpProEventCalendar') . '
															</div>
															<div class="pec_checkbox_list">	
																<input type="checkbox" class="checkbox" value="2" name="pec_weekly_day[]" /> &nbsp; '. __('Tue','dpProEventCalendar') . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="3" name="pec_weekly_day[]" /> &nbsp; '. __('Wed','dpProEventCalendar') . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="4" name="pec_weekly_day[]" /> &nbsp; '. __('Thu','dpProEventCalendar') . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="5" name="pec_weekly_day[]" /> &nbsp; '. __('Fri','dpProEventCalendar') . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="6" name="pec_weekly_day[]" /> &nbsp; '. __('Sat','dpProEventCalendar') . '
															</div>
															<div class="pec_checkbox_list">
																<input type="checkbox" class="checkbox" value="7" name="pec_weekly_day[]" /> &nbsp; '. __('Sun','dpProEventCalendar') . '
															</div>
															
														</div>';
														
														$html .= '
														<div class="pec_monthly_frequency" style="display:none;">
															
															<div class="dp_pec_clear"></div>
															
															'. __('Repeat every','dpProEventCalendar').' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_monthly_every" value="1" /> ' . __('month(s) on:','dpProEventCalendar') . '
															
															<div class="dp_pec_clear"></div>
															
															<select name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
																<option value=""> ' . __('Recurring Option','dpProEventCalendar') . '</option>
																<option value="first"> ' . __('First','dpProEventCalendar') . '</option>
																<option value="second"> ' . __('Second','dpProEventCalendar') . '</option>
																<option value="third"> ' . __('Third','dpProEventCalendar') . '</option>
																<option value="fourth"> ' . __('Fourth','dpProEventCalendar') . '</option>
																<option value="last"> ' . __('Last','dpProEventCalendar') . '</option>
															</select>
															
															<select name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
															<option value=""> ' . __('Recurring Option','dpProEventCalendar') . '</option>
																<option value="monday"> ' . __('Monday','dpProEventCalendar') . '</option>
																<option value="tuesday"> ' . __('Tuesday','dpProEventCalendar') . '</option>
																<option value="wednesday"> ' . __('Wednesday','dpProEventCalendar') . '</option>
																<option value="thursday"> ' . __('Thursday','dpProEventCalendar') . '</option>
																<option value="friday"> ' . __('Friday','dpProEventCalendar') . '</option>
																<option value="saturday"> ' . __('Saturday','dpProEventCalendar') . '</option>
																<option value="sunday"> ' . __('Sunday','dpProEventCalendar') . '</option>
															</select>
														</div>
													</div>';
													
												}
												
												if($this->calendar_obj->form_show_booking_enable ) {
													$html .= '
														<input type="checkbox" class="checkbox" name="booking_enable" id="" value="1" />
														<span class="dp_pec_form_desc dp_pec_form_desc_left">'.__('Allow Bookings?', 'dpProEventCalendar').'</span>';
												}
												
												$html .= '
												<div class="dp_pec_row">';
												if($this->calendar_obj->form_show_booking_price && is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
													$html .= '
													<div class="dp_pec_col6">
														<input type="number" min="0" value="" style="width: 120px;" placeholder="'.__('Price', 'dpProEventCalendar').'" id="" name="price" /> <span class="dp_pec_form_desc dp_pec_form_desc_left">'.$dp_pec_payments['currency'].'</span>
													</div>';
												}
												
												if($this->calendar_obj->form_show_booking_limit ) {
													$html .= '
													<div class="dp_pec_col6">
														<input type="number" min="0" value="" style="width: 120px;" placeholder="'.__('Booking Limit', 'dpProEventCalendar').'" id="" name="limit" />
													</div>';
												}
												$html .= '
												</div>';
												
												$html .= '
													<div class="dp_pec_clear"></div>
												</div>
												
												';
												$html .= '
												<div class="dp_pec_clear"></div>
											</div>
											<div class="dp_pec_col6">
												';
												if($this->calendar_obj->form_show_image) {
													$rand_image = rand();
													$html .= '
													<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_IMAGE'].'</span>
													<div class="dp_pec_add_image_wrap">
														<label for="event_image_'.$this->nonce.'_'.$rand_image.'"><span class="dp_pec_add_image"></span></label><input type="text" class="dp_pec_new_event_text" value="" readonly="readonly" id="event_image_lbl" name="" />
													</div><input type="file" name="event_image" id="event_image_'.$this->nonce.'_'.$rand_image.'" class="event_image" style="visibility:hidden; position: absolute;" />							
													';
												}
												if($this->calendar_obj->form_show_color) {
													$html .= '
													<span class="dp_pec_form_desc">'.__('Select a color', 'dpProEventCalendar').'</span>
													<select name="color">
														<option value="">'.__('None', 'dpProEventCalendar').'</option>
														 ';
														 
														$counter = 0;
														$querystr = "
														SELECT *
														FROM ". $this->table_special_dates ." 
														ORDER BY title ASC
														";
														$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
														foreach($sp_dates_obj as $sp_dates) {
														
														$html .= '<option value="'.$sp_dates->id.'">'.$sp_dates->title.'</option>';
														
														}
                                                    $html .= ' 
													</select>';
												}
												if($this->calendar_obj->form_show_link) {
													$html .= '
													<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_LINK'].'" id="" name="link" />';
												}
												
												if($this->calendar_obj->form_show_share) {
													$html .= '
													<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_SHARE'].'" id="" name="share" />';
												}
												if($this->calendar_obj->form_show_location) {
													$html .= '
													<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_LOCATION'].'" id="" name="location" />';
												}
												if($this->calendar_obj->form_show_phone) {
													$html .= '
													<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_PHONE'].'" id="" name="phone" />';
												}
												if($this->calendar_obj->form_show_map) {
													$html .= '
													<input type="text" value="" placeholder="'.$this->translation['TXT_EVENT_GOOGLEMAP'].'" id="pec_map_address" name="googlemap" />
													<input type="hidden" value="" id="map_lnlat" name="map_lnlat" />
													<div class="map_lnlat_wrap" style="display:none;">
														<span class="dp_pec_form_desc">'.__('Drag the marker to set a specific position', 'dpProEventCalendar').'</span>
														<div id="pec_mapCanvas" style="height: 400px;"></div>
													</div>
													';
												}
												
												if(is_array($dpProEventCalendar['custom_fields_counter'])) {
													$counter = 0;
													
													foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
										
														if($dpProEventCalendar['custom_fields']['type'][$counter] == "checkbox") {
				
															$html .= '
															<div class="dp_pec_wrap_checkbox">
															<input type="checkbox" class="checkbox" value="1" id="" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'
															</div>';
												
														} else {

															$html .= '
															<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'" id="" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" />';
															
														}
														$counter++;		
													}
												}
													$html .= '
												
											</div>
											<div class="dp_pec_clear"></div>
										</div>
									</div>
									<div class="dp_pec_clear"></div>
								</div>
								<div class="pec-add-footer">
									<a href="javascript:void(0);" class="dp_pec_view dp_pec_submit_event pec_action_btn dp_pec_btnright">'.$this->translation['TXT_SUBMIT_FOR_REVIEW'].'</a>
								</div>
							</form>';
					}
				$html .= '
						</div>';
				$html .= '
						<div class="dp_pec_clear"></div>
					</div>
				';
			}

			$html .= '
				<div class="dp_pec_nav dp_pec_nav_monthly" '.($this->calendar_obj->view == "monthly" || $this->is_admin || !empty($this->event_id) ? "" : "style='display:none;'").'>
				<div class="clageek">
					<span class="next_month"><strong>'.$this->translation['NEXT_MONTH'].'</strong></span>
					<span class="prev_month"><strong>'.$this->translation['PREV_MONTH'].'</strong></span>
					</div>
					<select class="pec_switch_month">
						';
						foreach($this->translation['MONTHS'] as $key) {
							$html .= '
								<option value="'.$key.'" '.($key == $this->translation['MONTHS'][($this->datesObj->currentMonth - 1)] ? 'selected="selected"':'').'>'.$key.'</option>';
						}
			$html .= '
					</select>
					<select class="pec_switch_year">
						';
						for($i = date('Y') - 2; $i <= date('Y') + 3; $i++) {
							$html .= '
								<option value="'.$i.'" '.($i == $this->datesObj->currentYear ? 'selected="selected"':'').'>'.$i.'</option>';
						}
			$html .= '
					</select>
					<div class="dp_pec_clear"></div>
				</div>
			';
			
			$html .= '
				<div class="dp_pec_nav dp_pec_nav_daily" '.($this->calendar_obj->view == "daily" && !$this->is_admin && empty($this->event_id) ? "" : "style='display:none;'").'>
					<span class="next_day"><strong>'.$this->translation['NEXT_DAY'].'</strong> &raquo;</span>
					<span class="prev_day">&laquo; <strong>'.$this->translation['PREV_DAY'].'</strong></span>
					<span class="actual_day">'.dpProEventCalendar_date_i18n(get_option('date_format'), $this->defaultDate).'</span>
					<div class="dp_pec_clear"></div>
				</div>
			';
			
			if($this->calendar_obj->first_day == 1) {
				$weekly_first_date = strtotime('last monday', ($this->defaultDate + (24* 60 * 60)));
				$weekly_last_date = strtotime('next sunday', ($this->defaultDate - (24* 60 * 60)));
			} else {
				$weekly_first_date = strtotime('last sunday', ($this->defaultDate + (24* 60 * 60)));
				$weekly_last_date = strtotime('next saturday', ($this->defaultDate - (24* 60 * 60)));
			}
			
			$weekly_format = get_option('date_format');
			$weekly_format = 'd F, Y';
			
			$weekly_txt = dpProEventCalendar_date_i18n('d F', $weekly_first_date).' - '.dpProEventCalendar_date_i18n($weekly_format, $weekly_last_date);
	
			if(date('m', $weekly_first_date) == date('m', $weekly_last_date)) {
			
				$weekly_txt = date('d', $weekly_first_date) . ' - ' . dpProEventCalendar_date_i18n($weekly_format, $weekly_last_date);
				
			}
			
			if(date('Y', $weekly_first_date) != date('Y', $weekly_last_date)) {
					
				$weekly_txt = dpProEventCalendar_date_i18n($weekly_format, $weekly_first_date).' - '.dpProEventCalendar_date_i18n($weekly_format, $weekly_last_date);
				
			}
			
			$html .= '
				<div class="dp_pec_nav dp_pec_nav_weekly" '.($this->calendar_obj->view == "weekly" && !$this->is_admin && empty($this->event_id) ? "" : "style='display:none;'").'>
					<span class="next_week"><strong>'.$this->translation['NEXT_WEEK'].'</strong> &raquo;</span>
					<span class="prev_week">&laquo; <strong>'.$this->translation['PREV_WEEK'].'</strong></span>
					<span class="actual_week">'.$weekly_txt.'</span>
					<div class="dp_pec_clear"></div>
				</div>
			';
			
			if(!$this->is_admin) {
				$specialDatesList = $this->getSpecialDatesList();
				$html .= '
				<div class="dp_pec_layout">';
				
				if($this->calendar_obj->show_references) {
					$html .= '
					<a href="javascript:void(0);" class="dp_pec_references dp_pec_btnleft">'.$this->translation['TXT_REFERENCES'].'</a>
					<div class="dp_pec_references_div">
						<a href="javascript:void(0);" class="dp_pec_references_close">x</a>';
						$html .= '
						<div class="dp_pec_references_div_sp">
							<div class="dp_pec_references_color" style="background-color: '.$this->calendar_obj->current_date_color.'"></div>
							<div class="dp_pec_references_title">'.$this->translation['TXT_CURRENT_DATE'].'</div>
							<div style="clear:both;"></div>
						</div>';
				
					if(count($specialDatesList) > 0) {
						foreach($specialDatesList as $key) {
							$html .= '
							<div class="dp_pec_references_div_sp">
								<div class="dp_pec_references_color" style="background-color: '.$key->color.'"></div>
								<div class="dp_pec_references_title">'.$key->title.'</div>
								<div style="clear:both;"></div>
							</div>';
						}
					}
					$html .= '
						</div>';
				}
				$html .= '
					<a href="javascript:void(0);" class="dp_pec_view_all dp_pec_btnleft">'.$this->translation['TXT_VIEW_ALL_EVENTS'].'</a>';
				if($this->calendar_obj->show_category_filter && empty($this->category)) {
					$html .= '<select name="pec_categories" class="pec_categories_list">
							<option value="">'.$this->translation['TXT_ALL_CATEGORIES'].'</option>';
							$cat_args = array(
									'taxonomy' => 'pec_events_category',
									'hide_empty' => 0
								);
							if($this->calendar_obj->category_filter_include != "") {
								$cat_args['include'] = $this->calendar_obj->category_filter_include;
							}
							$categories = get_categories($cat_args); 
						  foreach ($categories as $category) {
							$html .= '<option value="'.$category->term_id.'">';
							$html .= $category->cat_name;
							$html .= '</option>';
						  }
					$html .= '
						</select>';
				}
				if($this->calendar_obj->show_search) {
					$html .= '
						
						<form method="post" class="dp_pec_search_form">
							<input type="text" class="dp_pec_search" value="" placeholder="'.$this->translation['TXT_SEARCH'].'">
							<input type="submit" class="no-replace dp_pec_search_go" value="">
						</form>
						<div style="clear:both;"></div>';
				}
				$html .= '
					</div>
					';
			}
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
					
			if($this->calendar_obj->view == "monthly" || $this->is_admin || !empty($this->event_id)) {
				$html .= $this->monthlyCalendarLayout();
			}
			
			if($this->calendar_obj->view == "daily" && !$this->is_admin && empty($this->event_id)) {
				$html .= $this->dailyCalendarLayout();
			}
			
			if($this->calendar_obj->view == "weekly" && !$this->is_admin && empty($this->event_id)) {
				$html .= $this->weeklyCalendarLayout();
			}
			
			$html .= '
								
				</div>
			</div>';
			
			if($this->is_admin) {
				$html .= '
				</div>';
			}
		} elseif($this->type == 'upcoming') {
			
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '<div class="dp_pec_options_nav">';
				
			if($this->calendar_obj->ical_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$this->id_calendar ) . "'>iCal</a>";
			}
			if($this->calendar_obj->rss_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/rss.php?calendar_id='.$this->id_calendar ) . "'>RSS</a>";
			}
			if($this->calendar_obj->subscribe_active) {
				$html .= "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='javascript:void(0);'>".$this->translation['TXT_SUBSCRIBE']."</a>";
			}
			
			$html .= '<div class="dp_pec_clear"></div>
			</div>';

			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
						
			$html .= $this->upcomingCalendarLayout();
			
			$html .= '
								
				</div>
			</div>';
			
		} elseif($this->type == 'past') {
			
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
					
			if(empty($this->from)) {
				$this->from = "1970-01-01";
			}
			
			$html .= $this->upcomingCalendarLayout(false, $this->limit, '', null, null, true, false, true, false, true);
			
			$html .= '
								
				</div>
			</div>';
			
		} elseif($this->type == 'accordion') {
			
			$html .= '
			<div class="dp_pec_accordion_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			if($this->calendar_obj->ical_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical.php?calendar_id='.$this->id_calendar ) . "'>iCal</a>";
			}
			if($this->calendar_obj->rss_active) {
				$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/rss.php?calendar_id='.$this->id_calendar ) . "'>RSS</a>";
			}
			if($this->calendar_obj->subscribe_active) {
				$html .= "<a class='dpProEventCalendar_feed dpProEventCalendar_subscribe' href='javascript:void(0);'>".$this->translation['TXT_SUBSCRIBE']."</a>";
			}
			
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content_header">
					<span class="events_loading"><i class="fa fa-cog fa-spin"></i></span>
					<h2 class="actual_month">'.$this->translation['MONTHS'][($this->datesObj->currentMonth - 1)].' '.$this->datesObj->currentYear.'</h2>
					<div class="month_arrows">
						<span class="prev_month"><i class="fa fa-angle-left"></i></span>
						<span class="next_month"><i class="fa fa-angle-right"></i></span>
					</div>
					<span class="return_layout"><i class="fa fa-angle-double-left"></i></span>
				</div>
				';
			if($this->calendar_obj->show_search) {
				$html .= '
				<div class="dp_pec_content_search">
					<input type="text" class="dp_pec_content_search_input" placeholder="'.$this->translation['TXT_SEARCH'].'" />
					<a href="javascript:void(0);" class="dp_pec_icon_search" data-results_lang="'.addslashes($this->translation['TXT_RESULTS_FOR']).'"><i class="fa fa-search"></i></a>
				</div>';
			}
				$html .= '
				
				<div class="dp_pec_content">
				';
				$year = $this->datesObj->currentYear;
				$next_month_days = cal_days_in_month(CAL_GREGORIAN, str_pad(($this->datesObj->currentMonth), 2, "0", STR_PAD_LEFT), $year);
				$month_number = str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT);
				
				global $dpProEventCalendar_cache;
				
				if(!$this->is_admin &&
					isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]) && 
					isset($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]['accordionLayout'][$year."-".$month_number]) && 
					$this->calendar_obj->cache_active &&
					empty($this->category) &&
					empty($this->event_id) &&
					empty($this->author) &&
					$this->limit_description == 0) {
					$html .= $dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]['accordionLayout'][$year."-".$month_number]['html'];
					//echo '<pre>';
					//print_r($dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]);
					//echo '</pre>';
				} else {
					$html_month_list = "";
					$html_month_list = $this->eventsMonthList($year."-".$month_number."-01 00:00:00", $year."-".$month_number."-".$next_month_days." 23:59:59");
					$html .= $html_month_list;
					
					if(!$this->is_admin &&
						empty($this->category) &&
						empty($this->event_id) &&
						empty($this->author) &&
						$this->limit_description == 0) {
					
						$cache = array(
							'calendar_id_'.$this->id_calendar => array(
								'accordionLayout' => array(
									$year."-".$month_number => array(
										'html'		  => $html_month_list,
										'lastUpdate'  => time()	
									)
								)
							)
						);
						
						if(!$dpProEventCalendar_cache) {
							update_option( 'dpProEventCalendar_cache', $cache);
						} else if($html_month_list != "") {
								
							//$dpProEventCalendar_cache[] = $cache;
							$dpProEventCalendar_cache['calendar_id_'.$this->id_calendar]['accordionLayout'][$year."-".$month_number] =  array(
									'html'		  => $html_month_list,
									'lastUpdate'  => time()	
								);
								//print_r($dpProEventCalendar_cache);
							update_option( 'dpProEventCalendar_cache', $dpProEventCalendar_cache );
						}
					}
					
				}
			$html .= '
				</div>
			</div>';
			
		} elseif($this->type == 'accordion-upcoming') {
			
			$html .= '
			<div class="dp_pec_accordion_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
				';
				
				$html .= $this->eventsMonthList(null, null, $this->limit);
			$html .= '
				</div>
			</div>';
		
		} elseif($this->type == 'grid-upcoming') {
			
			$html .= '
			<div class="dp_pec_grid_wrapper" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					<ul>
				';
				
				$html .= $this->gridMonthList(null, null, $this->limit);
			$html .= '
					</ul>
				</div>
			</div>';

		} elseif($this->type == 'compact') {

			$html .= '
			<div class="dp_pec_compact_wrapper dp_pec_wrapper '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div class="dp_pec_nav">
					<span class="next_month"><i class="fa fa-chevron-right"></i></span>
					<span class="prev_month"><i class="fa fa-chevron-left"></i></span>
					<div class="dp_pec_wrap_month_year">
						<select class="pec_switch_month">
							';
							for($i = date('Y') - 2; $i <= date('Y') + 3; $i++) {
								foreach($this->translation['MONTHS'] as $key) {
									$html .= '
										<option value="'.$key.'-'.$i.'" '.($key.'-'.$i == $this->translation['MONTHS'][($this->datesObj->currentMonth - 1)].'-'.$this->datesObj->currentYear ? 'selected="selected"':'').'>'.$key.' '.$i.'</option>';
								}
							}
				$html .= '
						</select>
						<select class="pec_switch_year">
							';
							for($i = date('Y') - 2; $i <= date('Y') + 3; $i++) {
								$html .= '
									<option value="'.$i.'" '.($i == $this->datesObj->currentYear ? 'selected="selected"':'').'>'.$i.'</option>';
							}
				$html .= '
						</select>
					</div>
					<div class="dp_pec_clear"></div>
				</div>
			';
			$html .= '
				<div style="clear:both;"></div>
				<div class="dp_pec_content">
				';
				$html .= $this->monthlyCalendarLayout(true);
			$html .= '
				</div>
			</div>';
			
		} elseif($this->type == 'gmap-upcoming') {
			$html .= '
			<div class="dp_pec_gmap_wrapper" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$event_list = $this->upcomingCalendarLayout( true, $this->limit, '', null, null, true, false, false, true );
			$unique_events = array();
			$event_marker = "";
			$first_loc = "";
			
			if(is_array($event_list)) {
				
			
				foreach ($event_list as $obj) {
					if($obj->id == "") {
						$obj->id = $obj->ID;
					}
					
					if(!is_object($unique_events[$obj->id])) {
						$unique_events[$obj->id] = $obj;
						if($this->calendar_obj->format_ampm) {
							$time = date('h:i A', strtotime($obj->date));
						} else {
							$time = date('H:i', strtotime($obj->date));				
						}
		
						$end_date = '';
						$end_year = '';
						if($obj->end_date != "" && $obj->end_date != "0000-00-00" && $obj->recurring_frecuency == 1) {
							$end_day = date('d', strtotime($obj->end_date));
							$end_month = date('n', strtotime($obj->end_date));
							$end_year = date('Y', strtotime($obj->end_date));
							
							//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
							$end_date = ' / '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($obj->end_date));
						}
											
						$end_time = "";
						if($obj->end_time_hh != "" && $obj->end_time_mm != "") { $end_time = str_pad($obj->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($obj->end_time_mm, 2, "0", STR_PAD_LEFT); }
						
						if($end_time != "") {
							
							if($this->calendar_obj->format_ampm) {
								$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
							} else {
								$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
							}
							$end_time = " / ".$end_time_tmp;
							if($end_time_tmp == $time) {
								$end_time = "";	
							}
						}
		
						if($event->all_day) {
							$time = $this->translation['TXT_ALL_DAY'];
							$end_time = "";
						}
						
						$title = $obj->title;
						if($this->calendar_obj->link_post) {
							$title = '<a href="'.dpProEventCalendar_get_permalink($obj->id).'">'.addslashes($title).'</a>';	
						}
						
						$map_lnlat = get_post_meta($obj->id, 'pec_map_lnlat', true);
						
						$event_marker .= 'pec_codeAddress("'.$obj->map.'", \''.addslashes($title).'\', \''.get_the_post_thumbnail($obj->id, 'medium').'\', \''.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($obj->date)).$end_date.((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ? ' - '.$time.$end_time : '').'\', "'.$map_lnlat.'"); ';
					}
					if($first_loc == "") {
						$first_loc = $obj->map;
					}
				}
				

			$html .= '
				<div style="clear:both;"></div>
				<div class="dp_pec_map_canvas" id="dp_pec_map_canvas'.$this->nonce.'"></div>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					var geocoder;
					var map;
					function initialize'.$this->nonce.'() {
					 geocoder = new google.maps.Geocoder();
					 geocoder.geocode( { "address": "'.$first_loc.'"}, function(results, status) {
						  var latlng = results[0].geometry.location;
						  var mapOptions = {
							zoom: '.($dpProEventCalendar['google_map_zoom'] == "" ? 10 : $dpProEventCalendar['google_map_zoom']).',
							center: latlng
						  }
						  map = new google.maps.Map(document.getElementById("dp_pec_map_canvas'.$this->nonce.'"), mapOptions);
				';
				$html .= $event_marker;
				$html .= '
					 });
					  ';
				
				$html .= '
					}
					
					var infowindow = new google.maps.InfoWindow();
					
					function getInfoWindowEvent(marker, content) {
						infowindow.close();
						infowindow.setContent(content);
						infowindow.open(map, marker);
					}

					var counter_run = 0;
					
					function pec_codeAddress(address, title, image, eventdate, latlng) {
						
						setTimeout(function() { 
						  if(latlng != "") {
							  latlng = latlng.split(",");
							  
							  var myLatlng = new google.maps.LatLng(latlng[0],latlng[1]);
							  var marker = new google.maps.Marker({
								  map: map,
								  position: myLatlng
							  });
							  
							  google.maps.event.addListener(marker, "click", function() {
								  infowindow.close();
								  
								  var content = \'<div class="dp_pec_map_infowindow">\'
											+\'<span class="dp_pec_map_title">\'+title+\'</span><span class="dp_pec_map_date">\'+eventdate+\'</span><span class="dp_pec_map_location">\'+address+\'</span><div class="dp_pec_clear"></div>\'+image+\'\'
										+\'</div>\';
								  getInfoWindowEvent(marker, content);
							  });
						  } else {
						  geocoder.geocode( { "address": address}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
							  //map.setCenter(results[0].geometry.location);
							  var marker = new google.maps.Marker({
								  map: map,
								  position: results[0].geometry.location
							  });
							  
							  google.maps.event.addListener(marker, "click", function() {
								  infowindow.close();
								  
								  var content = \'<div class="dp_pec_map_infowindow">\'
											+\'<span class="dp_pec_map_title">\'+title+\'</span><span class="dp_pec_map_date">\'+eventdate+\'</span><span class="dp_pec_map_location">\'+address+\'</span><div class="dp_pec_clear"></div>\'+image+\'\'
										+\'</div>\';
								  getInfoWindowEvent(marker, content);
							  });
							} else {
								console.log("Geocode was not successful for the following reason: " + status);
							}
							
						  });
						  
						  }
						  }, (counter_run < 10 ? 0 : (1000 * counter_run)) );
						  
						  counter_run++;
					}
					
					
					
					google.maps.event.addDomListener(window, "load", initialize'.$this->nonce.');
				});
				</script>';
			} else {
				$html .= '<div class="dp_pec_accordion_event dp_pec_accordion_no_events"><span>'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span></div>';	
			}
				
			$html .= '
			</div>';
		} elseif($this->type == 'add-event') {
			
			$allow_user_add_event_roles = explode(',', $this->calendar_obj->allow_user_add_event_roles);
			$allow_user_add_event_roles = array_filter($allow_user_add_event_roles);

			if(!is_array($allow_user_add_event_roles) || empty($allow_user_add_event_roles) || $allow_user_add_event_roles == "") {
				$allow_user_add_event_roles = array('all');	
			}
			
			if( 
				(in_array(dpProEventCalendar_get_user_role(), $allow_user_add_event_roles) || 
				 in_array('all', $allow_user_add_event_roles) || 
				 (!is_user_logged_in() && !$this->calendar_obj->assign_events_admin)
				)
			) {

			
				$html .= '
				<div class="dp_pec_new_event_wrapper '.$skin.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
				';
				$html .= '
					<div style="clear:both;"></div>
					
					<div class="dp_pec_content_header">
						<span class="events_loading"></span>
						<h2 class="actual_month">'.$this->translation['TXT_ADD_EVENT'].'</h2>
					</div>
					
					<div class="dp_pec_content">
						';
					if(!is_user_logged_in() && !$this->calendar_obj->assign_events_admin) {
						$html .= '<div class="dp_pec_new_event_login"><span>'.$this->translation['TXT_EVENT_LOGIN'].'</span></div>';	
					} else {
						$html .= '
						<div class="dp_pec_new_event_login dp_pec_notification_event_succesfull">
						'.$this->translation['TXT_EVENT_THANKS'].'
						</div>';
						$html .= '
						<form enctype="multipart/form-data" method="post" class="add_new_event_form">
						<input type="text" class="dp_pec_new_event_text dp_pec_form_title" placeholder="'.$this->translation['TXT_EVENT_TITLE'].'" name="title" />
						';
						if($this->calendar_obj->form_show_description) {
							if($this->calendar_obj->form_text_editor) {
								// Turn on the output buffer
								ob_start();
								
								// Echo the editor to the buffer
								wp_editor('', $this->nonce.'_event_description', array('media_buttons' => false, 'textarea_name' => 'description', 'quicktags' => false, 'textarea_rows' => 5, 'teeny' => true));
								
								// Store the contents of the buffer in a variable
								$editor_contents = ob_get_clean();
								
								$html .= '<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_DESCRIPTION'].'</span>'.$editor_contents;
							} else {
								$html .= '<textarea placeholder="'.$this->translation['TXT_EVENT_DESCRIPTION'].'" class="dp_pec_new_event_text" id="" name="description" cols="50" rows="5"></textarea>';
							}
							
						}
						$html .= '
						<div class="dp_pec_row">
							<div class="dp_pec_col6">
								';
								if($this->calendar_obj->form_show_category) {
									$cat_args = array(
											'taxonomy' => 'pec_events_category',
											'hide_empty' => 0
										);
									if($this->calendar_obj->category_filter_include != "") {
										$cat_args['include'] = $this->calendar_obj->category_filter_include;
									}
									$categories = get_categories($cat_args); 
									if(count($categories) > 0) {
										$html .= '
										<div class="dp_pec_row">
											<div class="dp_pec_col12">
												<span class="dp_pec_form_desc">'.$this->translation['TXT_CATEGORY'].'</span>
												';
												foreach ($categories as $category) {
													$html .= '<div class="pec_checkbox_list">';
													$html .= '<input type="checkbox" name="category-'.$category->term_id.'" class="checkbox" value="'.$category->term_id.'" />';
													$html .= $category->cat_name;
													$html .= '</div>';
												  }
												$html .= '	
												<div class="dp_pec_clear"></div>	
											</div>
										</div>
										';
									}
								}
								$html .= '
								<div class="dp_pec_row">
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_DATE'].'</span>
										<div class="dp_pec_clear"></div>
										<input type="text" readonly="readonly" name="date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_date_input" value="'.date('Y-m-d').'" />
									</div>
									
									<div class="dp_pec_col6 dp_pec_end_date_form">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_DATE'].'</span>
										<div class="dp_pec_clear"></div>
										<input type="text" readonly="readonly" name="end_date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_end_date_input" value="" />
										<button type="button" class="dp_pec_clear_end_date">
											<img src="'.dpProEventCalendar_plugin_url( 'images/admin/clear.png' ).'" alt="Clear" title="Clear">
										</button>
									</div>
									<div class="dp_pec_clear"></div>
								</div>
								<div class="dp_pec_row">
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_TIME'].'</span>
										<div class="dp_pec_clear"></div>
										<select class="dp_pec_new_event_time" name="time_hours" id="" style="width:'.($this->calendar_obj->format_ampm ? '70' : '50').'px;">';
											for($i = 0; $i <= 23; $i++) {
												$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
												if($this->calendar_obj->format_ampm) {
													$hour = date('A', mktime($hour, 0)).' '.($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour));
												}
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
											}
										$html .= '
										</select>
										<select class="dp_pec_new_event_time" name="time_minutes" id="pec_time_minutes" style="width:50px;">';
											for($i = 0; $i <= 59; $i += 5) {
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
											}
										$html .= '
										</select>
									</div>
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_TIME'].'</span>
										<div class="dp_pec_clear"></div>
										<select class="dp_pec_new_event_time" name="end_time_hh" id="" style="width:'.($this->calendar_obj->format_ampm ? '70' : '50').'px;">
											<option value="">--</option>';
											for($i = 0; $i <= 23; $i++) {
												$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
												if($this->calendar_obj->format_ampm) {
													$hour = date('A', mktime($hour, 0)).' '.($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour));
												}
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.$hour.'</option>';
											}
										$html .= '
										</select>
										<select class="dp_pec_new_event_time" name="end_time_mm" id="" style="width:50px;">
											<option value="">--</option>';
											for($i = 0; $i <= 59; $i += 5) {
												$html .= '
												<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
											}
										$html .= '
										</select>
									</div>
									<div class="dp_pec_clear"></div>
								</div>
								
								<div class="dp_pec_row">
								
								';
								if($this->calendar_obj->form_show_hide_time) {
									$html .= '
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_HIDE_TIME'].'</span>
										<select name="hide_time">
											<option value="0">'.$this->translation['TXT_NO'].'</option>
											<option value="1">'.$this->translation['TXT_YES'].'</option>
										</select>
										';
										
										if($this->calendar_obj->form_show_all_day) {
											
											$html .= '
											<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
											<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>';
												
										}
										
									$html .= '
									</div>';
								} elseif($this->calendar_obj->form_show_all_day) {
									
									$html .= '
									<div class="dp_pec_col6">
										<input type="checkbox" class="checkbox" name="all_day" id="" value="1" />
										<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>
									</div>';	
	
								}
								
								if($this->calendar_obj->form_show_frequency) {
									$html .= '
									<div class="dp_pec_col6">
										<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_FREQUENCY'].'</span>
										<select name="recurring_frecuency" id="pec_recurring_frecuency" class="pec_recurring_frequency">
											<option value="0">'.$this->translation['TXT_NONE'].'</option>
											<option value="1">'.$this->translation['TXT_EVENT_DAILY'].'</option>
											<option value="2">'.$this->translation['TXT_EVENT_WEEKLY'].'</option>
											<option value="3">'.$this->translation['TXT_EVENT_MONTHLY'].'</option>
											<option value="4">'.$this->translation['TXT_EVENT_YEARLY'].'</option>
										</select>
									';
									
										$html .= '
										<div class="pec_daily_frequency" style="display:none;">
											<div id="pec_daily_every_div">' . __('Every','dpProEventCalendar') . ' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" maxlength="2" class="dp_pec_new_event_text" name="pec_daily_every" id="pec_daily_every" value="1" /> '.__('days','dpProEventCalendar') . ' </div>
											<div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" class="checkbox" onclick="pec_check_daily_working_days(this);" value="1" />'. __('All working days','dpProEventCalendar') . '</div>
										</div>';
										
										$html .= '
										<div class="pec_weekly_frequency" style="display:none;">
											
											<div class="dp_pec_clear"></div>
											
											'. __('Repeat every','dpProEventCalendar').' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_weekly_every" value="1" /> '. __('week(s) on:','dpProEventCalendar').'
											
											<div class="dp_pec_clear"></div>
											
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="1" name="pec_weekly_day[]" /> &nbsp; '. __('Mon','dpProEventCalendar') . '
											</div>
											<div class="pec_checkbox_list">	
												<input type="checkbox" class="checkbox" value="2" name="pec_weekly_day[]" /> &nbsp; '. __('Tue','dpProEventCalendar') . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="3" name="pec_weekly_day[]" /> &nbsp; '. __('Wed','dpProEventCalendar') . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="4" name="pec_weekly_day[]" /> &nbsp; '. __('Thu','dpProEventCalendar') . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="5" name="pec_weekly_day[]" /> &nbsp; '. __('Fri','dpProEventCalendar') . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="6" name="pec_weekly_day[]" /> &nbsp; '. __('Sat','dpProEventCalendar') . '
											</div>
											<div class="pec_checkbox_list">
												<input type="checkbox" class="checkbox" value="7" name="pec_weekly_day[]" /> &nbsp; '. __('Sun','dpProEventCalendar') . '
											</div>
											
										</div>';
										
										$html .= '
										<div class="pec_monthly_frequency" style="display:none;">
											
											<div class="dp_pec_clear"></div>
											
											'. __('Repeat every','dpProEventCalendar').' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_monthly_every" value="1" /> ' . __('month(s) on:','dpProEventCalendar') . '
											
											<div class="dp_pec_clear"></div>
											
											<select name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
												<option value=""> ' . __('Recurring Option','dpProEventCalendar') . '</option>
												<option value="first"> ' . __('First','dpProEventCalendar') . '</option>
												<option value="second"> ' . __('Second','dpProEventCalendar') . '</option>
												<option value="third"> ' . __('Third','dpProEventCalendar') . '</option>
												<option value="fourth"> ' . __('Fourth','dpProEventCalendar') . '</option>
												<option value="last"> ' . __('Last','dpProEventCalendar') . '</option>
											</select>
											
											<select name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
											<option value=""> ' . __('Recurring Option','dpProEventCalendar') . '</option>
												<option value="monday"> ' . __('Monday','dpProEventCalendar') . '</option>
												<option value="tuesday"> ' . __('Tuesday','dpProEventCalendar') . '</option>
												<option value="wednesday"> ' . __('Wednesday','dpProEventCalendar') . '</option>
												<option value="thursday"> ' . __('Thursday','dpProEventCalendar') . '</option>
												<option value="friday"> ' . __('Friday','dpProEventCalendar') . '</option>
												<option value="saturday"> ' . __('Saturday','dpProEventCalendar') . '</option>
												<option value="sunday"> ' . __('Sunday','dpProEventCalendar') . '</option>
											</select>
										</div>
									</div>';
								}
								
								if($this->calendar_obj->form_show_booking_enable ) {
									$html .= '
										<input type="checkbox" class="checkbox" name="booking_enable" id="" value="1" />
										<span class="dp_pec_form_desc dp_pec_form_desc_left">'.__('Allow Bookings?', 'dpProEventCalendar').'</span>';
								}
								
								$html .= '
								<div class="dp_pec_row">';
								if($this->calendar_obj->form_show_booking_price && is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
									$html .= '
									<div class="dp_pec_col6">
										<input type="number" min="0" value="" class="dp_pec_new_event_text" style="width: 120px;" placeholder="'.__('Price', 'dpProEventCalendar').'" id="" name="price" /> <span class="dp_pec_form_desc dp_pec_form_desc_left">'.$dp_pec_payments['currency'].'</span>
									</div>';
								}
								
								if($this->calendar_obj->form_show_booking_limit ) {
									$html .= '
									<div class="dp_pec_col6">
										<input type="number" min="0" value="" class="dp_pec_new_event_text" style="width: 120px;" placeholder="'.__('Booking Limit', 'dpProEventCalendar').'" id="" name="limit" />
									</div>';
								}
								$html .= '
								</div>';

								$html .= '
									<div class="dp_pec_clear"></div>
								</div>
								';
								
								$html .= '
								<div class="dp_pec_clear"></div>
							</div>
							<div class="dp_pec_col6">
								';
								if($this->calendar_obj->form_show_image) {
									$rand_image = rand();
									$html .= '
									<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_IMAGE'].'</span>
									<div class="dp_pec_add_image_wrap">
										<label for="event_image_'.$this->nonce.'_'.$rand_image.'"><span class="dp_pec_add_image"></span></label><input type="text" class="dp_pec_new_event_text" value="" readonly="readonly" id="event_image_lbl" name="" />
									</div><input type="file" name="event_image" id="event_image_'.$this->nonce.'_'.$rand_image.'" class="event_image" style="visibility:hidden; position: absolute;" />							
									';
								}
								if($this->calendar_obj->form_show_color) {
									$html .= '
									<span class="dp_pec_form_desc">'.__('Select a color', 'dpProEventCalendar').'</span>
									<select name="color">
										<option value="">'.__('None', 'dpProEventCalendar').'</option>
										 ';
										 
										$counter = 0;
										$querystr = "
										SELECT *
										FROM ". $this->table_special_dates ." 
										ORDER BY title ASC
										";
										$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
										foreach($sp_dates_obj as $sp_dates) {
										
										$html .= '<option value="'.$sp_dates->id.'">'.$sp_dates->title.'</option>';
										
										}
									$html .= ' 
									</select>';
								}
								if($this->calendar_obj->form_show_link) {
									$html .= '
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_LINK'].'" id="" name="link" />';
								}
								
								if($this->calendar_obj->form_show_share) {
									$html .= '
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_SHARE'].'" id="" name="share" />';
								}
								if($this->calendar_obj->form_show_location) {
									$html .= '
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_LOCATION'].'" id="" name="location" />';
								}
								if($this->calendar_obj->form_show_phone) {
									$html .= '
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_PHONE'].'" id="" name="phone" />';
								}
								if($this->calendar_obj->form_show_map) {
									$html .= '
									<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$this->translation['TXT_EVENT_GOOGLEMAP'].'" id="pec_map_address" name="googlemap" />
									<input type="hidden" value="" id="map_lnlat" name="map_lnlat" />
									<div class="map_lnlat_wrap" style="display:none;">
										<span class="dp_pec_form_desc">'.__('Drag the marker to set a specific position', 'dpProEventCalendar').'</span>
										<div id="pec_mapCanvas" style="height: 400px;"></div>
									</div>
									';
								}
								
								if(is_array($dpProEventCalendar['custom_fields_counter'])) {
									$counter = 0;
									
									foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
										
										if($dpProEventCalendar['custom_fields']['type'][$counter] == "checkbox") {

											$html .= '
											<div class="dp_pec_wrap_checkbox">
											<input type="checkbox" class="checkbox" value="1" id="" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'
											</div>';
								
										} else {

										$html .= '
											<input type="text" class="dp_pec_new_event_text" value="" placeholder="'.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'" id="" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" />';
											
										}
										$counter++;		
									}
								}
									$html .= '
							</div>
						</div>
						<div class="dp_pec_clear"></div>
						<div class="pec-add-footer">
							<button class="dp_pec_submit_event">'.$this->translation['TXT_SUBMIT_FOR_REVIEW'].'</button>
							<div class="dp_pec_clear"></div>
						</div>
						</form>';
					}
						$html .= '
					</div>
					<div class="dp_pec_clear"></div>
				</div>';

			}
			
		} elseif($this->type == 'list-author') {
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
						
			$html .= $this->upcomingCalendarLayout(false, 10, '', null, null, true, true);

			$html .= '
								
				</div>
			</div>';
		} elseif($this->type == 'bookings-user') {
			global $current_user;
			
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.'" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
			
			$bookings_list = $this->getBookingsByUser($current_user->ID);
			if(!is_array($bookings_list) || empty($bookings_list)) {
				$html .= '
				<div class="dp_pec_date_event dp_pec_isotope">
					<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
				</div>';
			} else {
				foreach($bookings_list as $key) {
					$title = '<span class="dp_pec_event_title_sp">'.get_the_title($key->id_event).'</span>';
					if($this->calendar_obj->link_post) {
						$title = '<a href="'.dpProEventCalendar_get_permalink($key->id_event).'">'.$title.'</a>';	
					}
	
					$html .= '<div class="dp_pec_date_event">
						<h2 class="dp_pec_event_title">
							'.$title.'
						</h2>
						<div class="dp_pec_clear"></div>
						<span><i class="fa fa-clock-o"></i>'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($key->event_date)). ' ' .dpProEventCalendar_date_i18n(get_option('time_format'), strtotime(get_post_meta($key->id_event, 'pec_date', true))).'</span>
						
						';
						$map = get_post_meta($key->id_event, 'pec_map', true);
						if($map != '') {
							$map_lnlat = get_post_meta($event->id, 'pec_map_lnlat', true);
							if($map_lnlat != "") {
								//$map = str_replace(" ", "", $map_lnlat);
							}
							$html .= '
							<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>
							<iframe class="dp_pec_date_event_map_iframe" width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_q&q='.urlencode($map).'&ie=UTF8&output=embed"></iframe>';
						}
					$html .= '
					</div>';
					
				}
			}
			

			$html .= '
								
				</div>
			</div>';
		} elseif($this->type == 'today-events') {
			$html .= '
			<div class="dp_pec_wrapper dp_pec_calendar_'.$this->calendar_obj->id.' dp_pec_today_events" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
			
			//$html .= date('Y-m-d H:i:s', $this->defaultDate).'<br>';
			//date_default_timezone_set( get_option('timezone_string')); // set the PHP timezone to match WordPress
			//$html .= date('Y-m-d H:i:s', $this->defaultDate).'<br>';
			
			$html .= $this->eventsListLayout(date('Y-m-d', $this->defaultDate), false);
			
			$html .= '
								
				</div>
			</div>';
		}
		
		
		if($print)
			echo $html;	
		else
			return $html;
		
	}
	
	function eventsMonthList($start_search = null, $end_search = null, $limit = 40) {
		
		global $dpProEventCalendar;
		
		$html = "";
		$daily_events = array();
		
		$pagination = (is_numeric($dpProEventCalendar['pagination']) && $dpProEventCalendar['pagination'] > 0 ? $dpProEventCalendar['pagination'] : 10);
		if(is_numeric($this->opts['pagination']) && $this->opts['pagination'] > 0) {
			$pagination = $this->opts['pagination'];
		}
		$event_counter = 1;
		
		$event_list = $this->upcomingCalendarLayout( true, $limit, '', $start_search, $end_search );
		
		if(is_array($event_list) && count($event_list) > 0) {
			
			$html .= "<div class='".(is_numeric($this->columns) && $this->columns > 1 ? 'pec_upcoming_layout' : '')."'>";
			
			foreach($event_list as $event) {
				
				if($event->id == "") 
					$event->id = $event->ID;
				
				if($event_counter > $limit) { break; }
				
				if($event->recurring_frecuency == 1){
					
					if(in_array($event->id, $daily_events)) {
						continue;	
					}
					
					$daily_events[] = $event->id;
				}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
					$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
					$event->date = $event->orig_date;
				}
					
				if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event->date));
				} else {
					$time = date('H:i', strtotime($event->date));				
				}

				$end_date = '';
				$end_year = '';
				if($event->end_date != "" && $event->end_date != "0000-00-00" && $event->recurring_frecuency == 1) {
					$end_day = date('d', strtotime($event->end_date));
					$end_month = date('n', strtotime($event->end_date));
					$end_year = date('Y', strtotime($event->end_date));
					
					//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
					$end_date = ' / '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
				}
									
				$end_time = "";
				if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
				
				if($end_time != "") {
					
					if($this->calendar_obj->format_ampm) {
						$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
					} else {
						$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
					}
					$end_time = " - ".$end_time_tmp;
					if($end_time_tmp == $time) {
						$end_time = "";	
					}
				}

				if($event->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$title = $event->title;
				if($this->calendar_obj->link_post) {
					$title = '<a href="'.dpProEventCalendar_get_permalink($event->id).'">'.$title.'</a>';	
				}
				
				$edit_button = $this->getEditRemoveButtons($event);
				
				$category = get_the_terms( $event->id, 'pec_events_category' ); 
				$category_list_html = '';
				$category_slug = '';
				if(!empty($category)) {
					$category_count = 0;
					$category_list_html .= '
						<span class="dp_pec_event_categories">';
					foreach ( $category as $cat){
						if($category_count > 0) {
							$category_list_html .= " / ";	
						}
						$category_list_html .= $cat->name;
						$category_slug .= 'category_'.$cat->slug.' ';
						$category_count++;
					}
					$category_list_html .= '
						</span>';
				}
					
				$html .= '
				<div class="dp_pec_isotope '.(is_numeric($this->columns) && $this->columns > 1 ? 'dp_pec_date_event_wrap dp_pec_columns_'.$this->columns : '').'"  data-event-number="'.$event_counter.'" '.($event_counter > $pagination ? 'style="display:none;"' : '').'>
				
					<div class="dp_pec_accordion_event '.$category_slug.'">
						<h2>'.$title.'</h2>
						<div class="dp_pec_clear"></div>
						'.$edit_button;
							
						if($this->calendar_obj->show_author) {
							$author = get_userdata(get_post_field( 'post_author', $event->id ));
							$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
						}
						
						$html .= '
						<span class="pec_time">'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date)).$end_date . '<br>'. $all_working_days.' '.((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time : '').'</span>
						';
						
						if($event->location != '') {
							$html .= '
							<span class="dp_pec_event_location">'.$event->location.'</span>';
						}
						if($event->phone != '') {
							$html .= '
							<span class="dp_pec_event_phone">'.$event->phone.'</span>';
						}
	
						if($category_list_html != "") {
							$html .= $category_list_html;
						}
					
						if(is_array($dpProEventCalendar['custom_fields_counter'])) {
							$counter = 0;
							
							foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
								$field_value = get_post_meta($event->id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true);
								if($field_value != "") {
									$html .= '<div class="pec_event_page_custom_fields"><strong>'.$dpProEventCalendar['custom_fields']['name'][$counter].'</strong>';
									if($dpProEventCalendar['custom_fields']['type'][$counter] == 'checkbox') { 
										$html .= '<i class="fa fa-check"></i>';
									} else {
										$html .= '<p>'.$field_value.'</p>';
									}
									$html .= '</div>';
								}
								$counter++;		
							}
							
							$html .= '<div class="dp_pec_clear"></div>';
						}
						
				$html .= '
						<div class="pec_description">';
				
						$post_thumbnail_id = get_post_thumbnail_id( $event->id );
						$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
						if($post_thumbnail_id) {
							$html .= '<div class="dp_pec_event_photo">';
							$html .= '<img src="'.$image_attributes[0].'" alt="" />';
							$html .= '</div>';
						}
				
						$html .= $this->getBookingButton($event->id, date('Y-m-d', strtotime($event->date)));
				
				if($this->limit_description > 0) {
					
					$event->description = force_balance_tags(html_entity_decode(wp_trim_words(htmlentities($event->description), $this->limit_description)));
					
				}
				
				$html .= '
							<p>'.do_shortcode(nl2br(strip_tags(str_replace("</p>", "<br />", preg_replace("/<p[^>]*?>/", "", $event->description)), '<iframe><strong><h1><h2><h3><h4><h5><h6><h7><b><i><em><pre><code><del><ins><img><a><ul><li><ol><blockquote><br><hr><span><p>'))).'</p>';
	
				if($event->map != '') {
					$map_lnlat = get_post_meta($event->id, 'pec_map_lnlat', true);
					if($map_lnlat != "") {
						//$event->map = str_replace(" ", "", $map_lnlat);
					}
					$html .= '
					<div class="dp_pec_date_event_map_overlay" onClick="style.pointerEvents=\'none\'"></div>
					<iframe class="dp_pec_date_event_map_iframe" width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_q&q='.urlencode($event->map).'&ie=UTF8&output=embed"></iframe>';
				}
						
						$html .= $this->getRating($event->id);
											
						$html .= '
							<div class="dp_pec_date_event_icons">';
						
						$html .= $this->getEventShare($event);
						
						if($event->link != '') {
							$html .= '
							<a class="dp_pec_date_event_link" href="'.$event->link.'" rel="nofollow" target="_blank"><i class="fa fa-link"></i></a>';
						}
						if($this->calendar_obj->ical_active && !$search) {
							$html .= "<a class='dpProEventCalendar_feed' href='".dpProEventCalendar_plugin_url( 'includes/ical_event.php?event_id='.$event->id.'&date='.strtotime($event->date) ) . "'>iCal</a><br class='clear' />";
						}
						$html .= '
							<a class="dp_pec_date_event_close" href="javascript:void(0);"><i class="fa fa-close"></i></a>';
						$html .= '
							</div>';
				$html .= '
						</div>
					</div>
				</div>';
				
				
				$event_counter++;
			}
			
			
			$html .= '</div>';
			
			if(($event_counter - 1) > $pagination) {
				$html .= '<a href="javascript:void(0);" class="pec_action_btn dpProEventCalendar_load_more" data-total="'.($event_counter - 1).'" data-pagination="'.$pagination.'">'.__('More', 'dpProEventCalendar').'</a>
					<div class="dp_pec_clear"></div>
				';
			}
		} else {
			$html .= '<div class="dp_pec_accordion_event dp_pec_accordion_no_events"><span>'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span></div>';	
		}
		
		return $html;
	}
	
	function gridMonthList($start_search = null, $end_search = null, $limit = 20) {
		
		$html = "";
		$daily_events = array();
		$event_counter = 1;
		
		$event_list = $this->upcomingCalendarLayout( true, $limit, '', $start_search, $end_search, true, false, false );
		
		if(is_array($event_list) && count($event_list) > 0) {

			foreach($event_list as $event) {
				if($event->id == "") 
					$event->id = $event->ID;
					
				if($event_counter > $limit) { break; }
				
				if($event->recurring_frecuency == 1){
					
					if(in_array($event->id, $daily_events)) {
						continue;	
					}
					
					$daily_events[] = $event->id;
				}
				
				$all_working_days = '';
				if($event->pec_daily_working_days && $event->recurring_frecuency == 1) {
					$all_working_days = $this->translation['TXT_ALL_WORKING_DAYS'];
					$event->date = $event->orig_date;
				}
					
				if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event->date));
				} else {
					$time = date('H:i', strtotime($event->date));				
				}

				$end_date = '';
				$end_year = '';
				if($event->end_date != "" && $event->end_date != "0000-00-00" && $event->recurring_frecuency == 1) {
					$end_day = date('d', strtotime($event->end_date));
					$end_month = date('n', strtotime($event->end_date));
					$end_year = date('Y', strtotime($event->end_date));
					
					//$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
					$end_date = ' / '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->end_date));
				}
									
				$end_time = "";
				if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
				
				if($end_time != "") {
					
					if($this->calendar_obj->format_ampm) {
						$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
					} else {
						$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
					}
					$end_time = " / ".$end_time_tmp;
					if($end_time_tmp == $time) {
						$end_time = "";	
					}
				}

				if($event->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
					$end_time = "";
				}

				$title = $event->title;
				
				$post_thumbnail_id = get_post_thumbnail_id( $event->id );
				$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
				$event_bg = "";
				$no_bg = false;
				
				if($post_thumbnail_id) {
					$event_bg = $image_attributes[0];
				} else {
					$no_bg = true;	
				}
				
				if($end_date == ' / '.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date))) {
					$end_date = '';	
				}
					
				$html .= '
				<li class="dp_pec_grid_event '.$category_slug.' dp_pec_grid_columns_'.$this->columns.' '.($no_bg ? 'dp_pec_grid_no_img' : '').'">
					<a href="'.dpProEventCalendar_get_permalink($event->id).(strpos(dpProEventCalendar_get_permalink($event->id), "?") === false ? "?" : "&").'event_date='.strtotime($event->date).'" class="dp_pec_grid_link_image" title="" style="background-image:url('.$event_bg.');"></a>
					
					<span class="pec_date">'.dpProEventCalendar_date_i18n(get_option('date_format'), strtotime($event->date)).$end_date.'</span>

					<div class="dp_pec_grid_text_wrap">
						<div class="dp_pec_grid_meta_data">
						';
							
						if($this->calendar_obj->show_author) {
							$author = get_userdata(get_post_field( 'post_author', $event->id ));
							$html .= '<span class="pec_author">'.$this->translation['TXT_BY'].' '.$author->display_name.'</span>';
						}
						
						$html .= '
						<span class="pec_time">'. $all_working_days.' '.((($this->calendar_obj->show_time && !$event->hide_time) || $event->all_day) ?  $time.$end_time : '').'</span>
						';
						
						if($event->location != '') {
							$html .= '
							<span class="dp_pec_event_location">'.$event->location.'</span>';
						}
						if($event->phone != '') {
							$html .= '
							<span class="dp_pec_event_phone">'.$event->phone.'</span>';
						}
					$html .= '
							<span class="dp_pec_grid_title">'.$title.'</span>
						</div>';					
					$html .= '
					</div>';
			$html .= '
				</li>';
				
				$event_counter++;
			}
		} else {
			$html .= '<li class="dp_pec_grid_no_events"><span>'.$this->translation['TXT_NO_EVENTS_FOUND'].'</span></li>';	
		}
		
		return $html;
	}
	
	function switchCalendarTo($type, $limit = 5, $limit_description = 0, $category = 0, $author = 0, $event_id = 0) {
		if(!is_numeric($limit)) { $limit = 5; }
		$this->type = $type;
		$this->limit = $limit;
		$this->limit_description = $limit_description;
		$this->category = $category;
		$this->event_id = $event_id;
		$this->author = $author;
	}
	
	function calendarSubscription($email, $name) {
		global $wpdb;
		
		require_once (dirname (__FILE__) . '/../mailchimp/miniMCAPI.class.php');

		if(stripslashes($email) != '' ){
			
			$exists = $wpdb->get_row("SELECT COUNT(*) as counter FROM ".$this->table_subscribers_calendar." WHERE calendar = ".$this->id_calendar." AND email = '".$email."'");

			if($exists->counter == 0) {
				$wpdb->insert( 
					$this->table_subscribers_calendar, 
					array( 
						'name' => $name, 
						'email' => $email, 
						'calendar' => $this->id_calendar, 
						'subscription_date' => current_time('mysql')
					), 
					array( 
						'%s', 
						'%s', 
						'%d', 
						'%s' 
					) 
				);
			}
				
			if($this->calendar_obj->mailchimp_api != "" && $this->calendar_obj->subscribe_active) {
				$mailchimp_class = new mailchimpSF_MCAPI($this->calendar_obj->mailchimp_api);
				
				$retval = $mailchimp_class->listSubscribe( $this->calendar_obj->mailchimp_list, $email );
		
				if(!$mailchimp_class->errorCode){	
					die('ok');
				}else{
					die('0');
				}
			}
		}
	}
	
	function getRating($eventid) {
		$rate 		= get_post_meta($eventid, 'pec_rate', true);
		$user_rate 	= get_post_meta($eventid, 'pec_user_rate', true);
		
		if($user_rate != "") {
			$star1 = count(get_post_meta($eventid, 'pec_user_rate_1star'));
			$star2 = count(get_post_meta($eventid, 'pec_user_rate_2star'));
			$star3 = count(get_post_meta($eventid, 'pec_user_rate_3star'));
			$star4 = count(get_post_meta($eventid, 'pec_user_rate_4star'));
			$star5 = count(get_post_meta($eventid, 'pec_user_rate_5star'));
			
			$total_votes = $star1 + $star2 + $star3 + $star4 + $star5;
			
			if($total_votes == 0) {
				$rate = 0;
			} else {
				$rate = ((
					$star1 +
					($star2 * 2) +
					($star3 * 3) +
					($star4 * 4) +
					($star5 * 5)) /
						$total_votes);
			}
		}
		
		if($rate != '' || $rate === 0) {
			$html = '
			<ul class="dp_pec_rate '.($user_rate != "" && is_user_logged_in() ? 'dp_pec_user_rate' : '').'">
				<li><a href="javascript:void(0);" data-rate-val="1" data-event-id="'.$eventid.'" '.($rate >= 1 ? 'class="dp_pec_rate_full"' : '').'></a></li>
				<li><a href="javascript:void(0);" data-rate-val="2" data-event-id="'.$eventid.'" '.($rate >= 2 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 1 && $rate < 2 ? 'class="dp_pec_rate_h"' : '').'></a></li>
				<li><a href="javascript:void(0);" data-rate-val="3" data-event-id="'.$eventid.'" '.($rate >= 3 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 2 && $rate < 3 ? 'class="dp_pec_rate_h"' : '').'></a></li>
				<li><a href="javascript:void(0);" data-rate-val="4" data-event-id="'.$eventid.'" '.($rate >= 4 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 3 && $rate < 4 ? 'class="dp_pec_rate_h"' : '').'></a></li>
				<li><a href="javascript:void(0);" data-rate-val="5" data-event-id="'.$eventid.'" '.($rate >= 5 ? 'class="dp_pec_rate_full"' : '').' '.($rate > 4 && $rate < 5 ? 'class="dp_pec_rate_h"' : '').'></a></li>
			</ul>';
		}
		
		return $html;
	}
	
	function rateEvent($eventid, $rate) {
		
		if(is_user_logged_in() && is_numeric($eventid)) {
			global $current_user;
			get_currentuserinfo();
			
			delete_post_meta($eventid, 'pec_user_rate_1star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_2star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_3star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_4star', $current_user->ID);
			delete_post_meta($eventid, 'pec_user_rate_5star', $current_user->ID);
			
			add_post_meta($eventid, 'pec_user_rate_'.$rate.'star', $current_user->ID);
								
			return $this->getRating($eventid);
		}
		
	}
	
	function getEventShare($event) {
		$html = "";
		
		if($this->calendar_obj->article_share) {
			if(shortcode_exists('dpArticleShare')) {
				global $dpArticleShare;
				
				if($dpArticleShare['support_pro_event_calendar']) {
					require_once (dirname (__FILE__) . '/../../dpArticleShare/classes/base.class.php');
					$dpArticleShare_class = new DpArticleShare( false, '', $event->id );
					$html .= str_replace('dpas-icon dpas-icon-dpShareIcon-more dpas-fa"><i class="fa fa-plus"></i>', 'dpas-icon dpas-icon-dpShareIcon-more dpas-fa"><i class="fa fa-plus"></i><span>'.$dpArticleShare['i18n_share_on'].'</span>', $dpArticleShare_class->output(true));
				}
			}
		} else {
			if($event->share != '') {
				$html .= '
				<a class="dp_pec_date_event_twitter" href="http://twitter.com/home?status='.urlencode($event->share).'" target="_blank"></a>';
			}
		}	
		
		return $html;
	}
	
	function getEditRemoveButtons($event) {
		$edit_button = "";
		if($this->opts['allow_user_edit_remove'] == "") {
			$this->opts['allow_user_edit_remove'] = 1;
		}
		
		if(is_user_logged_in() && (($this->calendar_obj->allow_user_edit_event || $this->calendar_obj->allow_user_remove_event) && $this->opts['allow_user_edit_remove'])) {
			global $current_user;
			get_currentuserinfo();
			
			if(($current_user->ID == get_post_field( 'post_author', $event->id) || current_user_can('switch_themes') ) && ($this->calendar_obj->allow_user_edit_event && $this->opts['allow_user_edit_remove'])) {
				$edit_button .= '<a href="javascript:void(0);" class="pec_edit_event">'.$this->translation['TXT_EDIT_EVENT'].'</a><div style="display:none;">'.$this->getAddForm($event->id).'</div>';		
			}
			
			if(($current_user->ID == get_post_field( 'post_author', $event->id) || current_user_can('switch_themes' )) && ($this->calendar_obj->allow_user_remove_event && $this->opts['allow_user_edit_remove'])) {
				$edit_button .= '<a href="javascript:void(0);" class="pec_remove_event">'.$this->translation['TXT_REMOVE_EVENT'].'</a><div style="display:none;">
				<form enctype="multipart/form-data" method="post" class="add_new_event_form remove_event_form">
			
				<input type="hidden" value="'.$this->id_calendar.'" name="remove_event_calendar">
				<input type="hidden" value="'.$event->id.'" name="remove_event">
				<p>'.$this->translation['TXT_REMOVE_EVENT_CONFIRM'].'</p>
				<div class="dp_pec_clear"></div>
				<div class="pec-add-footer">
					<button class="dp_pec_remove_event">'.$this->translation['TXT_REMOVE_EVENT'].'</button>
					<div class="dp_pec_clear"></div>
				</div>
				</form>
				</div>';		
			}
		}
		
		return $edit_button;
	}
	
	function getAddForm($edit = false) {
		global $dpProEventCalendar, $dp_pec_payments, $wpdb;
		
		$html = '';
		
		$post_category_ids = array();
		
		if(is_numeric($edit)) {
			$id_calendar = get_post_meta($edit, 'pec_id_calendar', true);
			$title = get_the_title($edit);
			$description = get_post_field('post_content', $edit);
			$post_category = get_the_terms($edit, 'pec_events_category');
			
			if(is_array($post_category)) {
				foreach($post_category as $category) {
					$post_category_ids[] = $category->term_id;
				}
			}
			$date = get_post_meta($edit, 'pec_date', true);
			$start_date = substr($date, 0, 11);
			$start_time_hh = substr($date, 11, 2);
			$start_time_mm = substr($date, 14, 2);
			$all_day = get_post_meta($edit, 'pec_all_day', true);
			$recurring_frecuency = get_post_meta($edit, 'pec_recurring_frecuency', true);
			$end_date = get_post_meta($edit, 'pec_end_date', true);
			$link = get_post_meta($edit, 'pec_link', true);
			$share = get_post_meta($edit, 'pec_share', true);
			$color = get_post_meta($edit, 'pec_color', true);
			$map = get_post_meta($edit, 'pec_map', true);
			$end_time_hh = get_post_meta($edit, 'pec_end_time_hh', true);
			$end_time_mm = get_post_meta($edit, 'pec_end_time_mm', true);
			$hide_time = get_post_meta($edit, 'pec_hide_time', true);
			$location = get_post_meta($edit, 'pec_location', true);
			$phone = get_post_meta($edit, 'pec_phone', true);
			$booking_enable = get_post_meta($edit, 'pec_enable_booking', true);
			$limit = get_post_meta($edit, 'pec_booking_limit', true);
			$price = get_post_meta($edit, 'pec_booking_price', true);
		}
		
		$html .= '
			<form enctype="multipart/form-data" method="post" class="add_new_event_form edit_event_form">
			';
		if(is_numeric($id_calendar)) {
			$html .= '
			<div class="pec_modal_wrap_content">
			<input type="hidden" value="'.$id_calendar.'" name="edit_calendar" />
			<input type="hidden" value="'.$edit.'" name="edit_event" />';
		}
		$html .= '
			<input type="text" class="dp_pec_new_event_text dp_pec_form_title" value="'.$title.'" placeholder="'.$this->translation['TXT_EVENT_TITLE'].'" name="title" />
			';
			if($this->calendar_obj->form_show_description) {
				
				if($this->calendar_obj->form_text_editor) {
					
					// Turn on the output buffer
					ob_start();
					
					// Echo the editor to the buffer
					wp_editor($description, $this->nonce.'_event_description', array('media_buttons' => false, 'textarea_name' => 'description', 'quicktags' => false, 'textarea_rows' => 5, 'teeny' => true));
					
					// Store the contents of the buffer in a variable
					$editor_contents = ob_get_clean();
					
					$html .= '<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_DESCRIPTION'].'</span>'.$editor_contents;
					
				} else {
					$html .= '<textarea placeholder="'.$this->translation['TXT_EVENT_DESCRIPTION'].'" class="dp_pec_new_event_text" id="" name="description" cols="50" rows="5">'.$description.'</textarea>';	
				}
				
			}
			$html .= '
			<div class="dp_pec_row">
				<div class="dp_pec_col6">
					';
					if($this->calendar_obj->form_show_category) {
						$cat_args = array(
								'taxonomy' => 'pec_events_category',
								'hide_empty' => 0
							);
						if($this->calendar_obj->category_filter_include != "") {
							$cat_args['include'] = $this->calendar_obj->category_filter_include;
						}
						$categories = get_categories($cat_args); 
						if(count($categories) > 0) {
							$html .= '
							<div class="dp_pec_row">
								<div class="dp_pec_col12">
									<span class="dp_pec_form_desc">'.$this->translation['TXT_CATEGORY'].'</span>
									';
									foreach ($categories as $category) {
										$html .= '<div class="pec_checkbox_list">';
										$html .= '<input type="checkbox" name="category-'.$category->term_id.'" class="checkbox" value="'.$category->term_id.'" '.(in_array($category->term_id, $post_category_ids) ? 'checked="checked"' : '').' />';
										$html .= $category->cat_name;
										$html .= '</div>';
									  }
									$html .= '	
									<div class="dp_pec_clear"></div>							
								</div>
							</div>
							';
						}
					}
					$html .= '
					<div class="dp_pec_row">
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_DATE'].'</span>
							<div class="dp_pec_clear"></div>
							<input type="text" readonly="readonly" name="date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_date_input_modal" value="'.$start_date.'" />
						</div>
						
						<div class="dp_pec_col6 dp_pec_end_date_form">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_DATE'].'</span>
							<div class="dp_pec_clear"></div>
							<input type="text" readonly="readonly" name="end_date" maxlength="10" id="" class="dp_pec_new_event_text dp_pec_end_date_input_modal" value="'.$end_date.'" />
							<button type="button" class="dp_pec_clear_end_date">
								<img src="'.dpProEventCalendar_plugin_url( 'images/admin/clear.png' ).'" alt="Clear" title="Clear">
							</button>
						</div>
						<div class="dp_pec_clear"></div>
					</div>
					<div class="dp_pec_row">
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_START_TIME'].'</span>
							<div class="dp_pec_clear"></div>
							<select class="dp_pec_new_event_time" name="time_hours" id="" style="width:'.($this->calendar_obj->format_ampm ? '70' : '50').'px;">';
								for($i = 0; $i <= 23; $i++) {
									$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
									if($this->calendar_obj->format_ampm) {
										$hour = date('A', mktime($hour, 0)).' '.($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour));
									}
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($start_time_hh == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.$hour.'</option>';
								}
							$html .= '
							</select>
							<select class="dp_pec_new_event_time" name="time_minutes" id="pec_time_minutes" style="width:50px;">';
								for($i = 0; $i <= 59; $i += 5) {
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($start_time_mm == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
								}
							$html .= '
							</select>
						</div>
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_END_TIME'].'</span>
							<div class="dp_pec_clear"></div>
							<select class="dp_pec_new_event_time" name="end_time_hh" id="" style="width:'.($this->calendar_obj->format_ampm ? '70' : '50').'px;">
								<option value="">--</option>';
								for($i = 0; $i <= 23; $i++) {
									$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
									if($this->calendar_obj->format_ampm) {
										$hour = date('A', mktime($hour, 0)).' '.($hour > 12 ? $hour - 12 : ($hour == '00' ? '12' : $hour));
									}
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($end_time_hh == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.$hour.'</option>';
								}
							$html .= '
							</select>
							<select class="dp_pec_new_event_time" name="end_time_mm" id="" style="width:50px;">
								<option value="">--</option>';
								for($i = 0; $i <= 59; $i += 5) {
									$html .= '
									<option value="'.str_pad($i, 2, "0", STR_PAD_LEFT).'" '.($end_time_mm == str_pad($i, 2, "0", STR_PAD_LEFT) ? 'selected="selected"' : '').'>'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
								}
							$html .= '
							</select>
						</div>
						<div class="dp_pec_clear"></div>
					</div>
					
					<div class="dp_pec_row">
					
					';
					if($this->calendar_obj->form_show_hide_time) {
						$html .= '
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_HIDE_TIME'].'</span>
							<select name="hide_time">
								<option value="0" '.($hide_time == 0 ? 'selected="selected"' : '').'>'.$this->translation['TXT_NO'].'</option>
								<option value="1" '.($hide_time == 1 ? 'selected="selected"' : '').'>'.$this->translation['TXT_YES'].'</option>
							</select>
						';
							
							if($this->calendar_obj->form_show_all_day) {
								
								$html .= '
								<input type="checkbox" class="checkbox" name="all_day" id="" value="1" '.($all_day ? 'checked="checked"' : '').' />
								<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>';
									
							}
							
						$html .= '
						</div>';
					} elseif($this->calendar_obj->form_show_all_day) {
						
						$html .= '
						<div class="dp_pec_col6">
							<input type="checkbox" class="checkbox" name="all_day" id="" value="1" '.($all_day ? 'checked="checked"' : '').' />
							<span class="dp_pec_form_desc dp_pec_form_desc_left">'.$this->translation['TXT_EVENT_ALL_DAY'].'</span>
						</div>';	

					}
					
					if($this->calendar_obj->form_show_frequency) {
						$html .= '
						<div class="dp_pec_col6">
							<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_FREQUENCY'].'</span>
							<select name="recurring_frecuency" id="pec_recurring_frecuency" class="pec_recurring_frequency">
								<option value="0">'.$this->translation['TXT_NONE'].'</option>
								<option value="1" '.($recurring_frecuency == 1 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_DAILY'].'</option>
								<option value="2" '.($recurring_frecuency == 2 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_WEEKLY'].'</option>
								<option value="3" '.($recurring_frecuency == 3 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_MONTHLY'].'</option>
								<option value="4" '.($recurring_frecuency == 4 ? 'selected="selected"' : '').'>'.$this->translation['TXT_EVENT_YEARLY'].'</option>
							</select>
						';
								
							$html .= '
							<div class="pec_daily_frequency" style="display:none;">
								<div id="pec_daily_every_div">' . __('Every','dpProEventCalendar') . ' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" maxlength="2" class="dp_pec_new_event_text" name="pec_daily_every" id="pec_daily_every" value="1" /> '.__('days','dpProEventCalendar') . ' </div>
								<div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" class="checkbox" onclick="pec_check_daily_working_days(this);" value="1" />'. __('All working days','dpProEventCalendar') . '</div>
							</div>';
							
							$html .= '
							<div class="pec_weekly_frequency" style="display:none;">
								
								<div class="dp_pec_clear"></div>
								
								'. __('Repeat every','dpProEventCalendar').' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_weekly_every" value="1" /> '. __('week(s) on:','dpProEventCalendar').'
								
								<div class="dp_pec_clear"></div>
								
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="1" name="pec_weekly_day[]" /> &nbsp; '. __('Mon','dpProEventCalendar') . '
								</div>
								<div class="pec_checkbox_list">	
									<input type="checkbox" class="checkbox" value="2" name="pec_weekly_day[]" /> &nbsp; '. __('Tue','dpProEventCalendar') . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="3" name="pec_weekly_day[]" /> &nbsp; '. __('Wed','dpProEventCalendar') . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="4" name="pec_weekly_day[]" /> &nbsp; '. __('Thu','dpProEventCalendar') . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="5" name="pec_weekly_day[]" /> &nbsp; '. __('Fri','dpProEventCalendar') . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="6" name="pec_weekly_day[]" /> &nbsp; '. __('Sat','dpProEventCalendar') . '
								</div>
								<div class="pec_checkbox_list">
									<input type="checkbox" class="checkbox" value="7" name="pec_weekly_day[]" /> &nbsp; '. __('Sun','dpProEventCalendar') . '
								</div>
								
							</div>';
							
							$html .= '
							<div class="pec_monthly_frequency" style="display:none;">
								
								<div class="dp_pec_clear"></div>
								
								'. __('Repeat every','dpProEventCalendar').' <input type="number" min="1" max="99" style="width:60px;padding: 5px 10px;margin-bottom: 10px !important;" class="dp_pec_new_event_text" maxlength="2" name="pec_monthly_every" value="1" /> ' . __('month(s) on:','dpProEventCalendar') . '
								
								<div class="dp_pec_clear"></div>
								
								<select name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
									<option value=""> ' . __('Recurring Option','dpProEventCalendar') . '</option>
									<option value="first"> ' . __('First','dpProEventCalendar') . '</option>
									<option value="second"> ' . __('Second','dpProEventCalendar') . '</option>
									<option value="third"> ' . __('Third','dpProEventCalendar') . '</option>
									<option value="fourth"> ' . __('Fourth','dpProEventCalendar') . '</option>
									<option value="last"> ' . __('Last','dpProEventCalendar') . '</option>
								</select>
								
								<select name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
								<option value=""> ' . __('Recurring Option','dpProEventCalendar') . '</option>
									<option value="monday"> ' . __('Monday','dpProEventCalendar') . '</option>
									<option value="tuesday"> ' . __('Tuesday','dpProEventCalendar') . '</option>
									<option value="wednesday"> ' . __('Wednesday','dpProEventCalendar') . '</option>
									<option value="thursday"> ' . __('Thursday','dpProEventCalendar') . '</option>
									<option value="friday"> ' . __('Friday','dpProEventCalendar') . '</option>
									<option value="saturday"> ' . __('Saturday','dpProEventCalendar') . '</option>
									<option value="sunday"> ' . __('Sunday','dpProEventCalendar') . '</option>
								</select>
							</div>
						</div>';
					}
					
					if($this->calendar_obj->form_show_booking_enable ) {
						$html .= '
							<input type="checkbox" '.($booking_enable ? 'checked="checked"' : '').' class="checkbox" name="booking_enable" id="" value="1" />
							<span class="dp_pec_form_desc dp_pec_form_desc_left">'.__('Allow Bookings?', 'dpProEventCalendar').'</span>';
					}
					
					$html .= '
					<div class="dp_pec_row">';
					if($this->calendar_obj->form_show_booking_price && is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {
						$html .= '
						<div class="dp_pec_col6">
							<input type="number" min="0" value="'.$price.'" class="dp_pec_new_event_text" style="width: 120px;" placeholder="'.__('Price', 'dpProEventCalendar').'" id="" name="price" /> <span class="dp_pec_form_desc dp_pec_form_desc_left">'.$dp_pec_payments['currency'].'</span>
						</div>';
					}
					
					if($this->calendar_obj->form_show_booking_limit ) {
						$html .= '
						<div class="dp_pec_col6">
							<input type="number" min="0" value="'.$limit.'" class="dp_pec_new_event_text" style="width: 120px;" placeholder="'.__('Booking Limit', 'dpProEventCalendar').'" id="" name="limit" />
						</div>';
					}
					$html .= '
					</div>';

					$html .= '
						<div class="dp_pec_clear"></div>
					</div>
					';
					$html .= '
					<div class="dp_pec_clear"></div>
				</div>
				<div class="dp_pec_col6">
					';
					if($this->calendar_obj->form_show_image || false) {
						$rand_image = rand();
						$html .= '
						<span class="dp_pec_form_desc">'.$this->translation['TXT_EVENT_IMAGE'].'</span>
						<div class="dp_pec_add_image_wrap">
							<label for="event_image_'.$this->nonce.'_'.$rand_image.(is_numeric($edit) ? '_pecremoveedit' : '').'"><span class="dp_pec_add_image"></span></label><input type="text" class="dp_pec_new_event_text" value="" readonly="readonly" id="event_image_lbl" name="" />
						</div><input type="file" name="event_image" id="event_image_'.$this->nonce.'_'.$rand_image.(is_numeric($edit) ? '_pecremoveedit' : '').'" class="event_image" style="visibility:hidden; position: absolute;" />							
						';
					}
					if($this->calendar_obj->form_show_color) {
						$html .= '
						<span class="dp_pec_form_desc">'.__('Select a color', 'dpProEventCalendar').'</span>
						<select name="color">
							<option value="">'.__('None', 'dpProEventCalendar').'</option>
							 ';
							 
							$counter = 0;
							$querystr = "
							SELECT *
							FROM ". $this->table_special_dates ." 
							ORDER BY title ASC
							";
							$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
							foreach($sp_dates_obj as $sp_dates) {
							
							$html .= '<option value="'.$sp_dates->id.'" '.($color == $sp_dates->id ? 'selected="selected"' : '').'>'.$sp_dates->title.'</option>';
							
							}
						$html .= ' 
						</select>';
					}
					if($this->calendar_obj->form_show_link) {
						$html .= '
						<input type="text" class="dp_pec_new_event_text" value="'.$link.'" placeholder="'.$this->translation['TXT_EVENT_LINK'].'" id="" name="link" />';
					}
					
					if($this->calendar_obj->form_show_share) {
						$html .= '
						<input type="text" class="dp_pec_new_event_text" value="'.$share.'" placeholder="'.$this->translation['TXT_EVENT_SHARE'].'" id="" name="share" />';
					}
					if($this->calendar_obj->form_show_location) {
						$html .= '
						<input type="text" class="dp_pec_new_event_text" value="'.$location.'" placeholder="'.$this->translation['TXT_EVENT_LOCATION'].'" id="" name="location" />';
					}
					if($this->calendar_obj->form_show_phone) {
						$html .= '
						<input type="text" class="dp_pec_new_event_text" value="'.$phone.'" placeholder="'.$this->translation['TXT_EVENT_PHONE'].'" id="" name="phone" />';
					}
					if($this->calendar_obj->form_show_map) {
						$html .= '
						<input type="text" class="dp_pec_new_event_text" value="'.$map.'" placeholder="'.$this->translation['TXT_EVENT_GOOGLEMAP'].'" id="pec_map_address" name="googlemap" />
						<input type="hidden" value="" id="map_lnlat" name="map_lnlat" />
						<div class="map_lnlat_wrap" style="display:none;">
							<span class="dp_pec_form_desc">'.__('Drag the marker to set a specific position', 'dpProEventCalendar').'</span>
							<div id="pec_mapCanvas" style="height: 400px;"></div>
						</div>
						';
					}
					if(is_array($dpProEventCalendar['custom_fields_counter'])) {
						$counter = 0;
						
						foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
							if($dpProEventCalendar['custom_fields']['type'][$counter] == "checkbox") {

								$html .= '
								<div class="dp_pec_wrap_checkbox">
								<input type="checkbox" class="checkbox" value="1" id="" '.(is_numeric($edit) && get_post_meta($edit, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true) ? 'checked="checked"' : '').' name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" /> '.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'
								</div>';
								
							} else {
								
								$html .= '
								<input type="text" class="dp_pec_new_event_text" value="'.(is_numeric($edit) ? get_post_meta($edit, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true) : ''). '" placeholder="'.$dpProEventCalendar['custom_fields']['placeholder'][$counter].'" id="" name="pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter].'" />';
							}
							
							$counter++;		
						}
					}
						$html .= '
				</div>
				
				<div class="dp_pec_clear"></div>
				
			</div>
			';
			if(is_numeric($id_calendar)) {
				$html .= '
				</div>';
			}
			$html .= '
			<div class="dp_pec_clear"></div>
			<div class="pec-add-footer">
				<button class="dp_pec_submit_event">'.$this->translation['TXT_SEND'].'</button>
				<div class="dp_pec_clear"></div>
			</div>
			</form>';
		return $html;	
	}
	
	function userHasBooking($date, $event_id) {
		global $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
		
		if(!is_user_logged_in()) {
			return false;	
		}
		
		$querystr = "
            SELECT count(*) as counter
            FROM ".$this->table_booking."
			WHERE id_event = ".$event_id." AND id_user = ".$current_user->ID." AND event_date = '".$date."' AND status <> 'pending'
            ";
        $bookings_obj = $wpdb->get_results($querystr, OBJECT);
		
		return $bookings_obj[0]->counter;
	}
	
	function getEventBookings($counter = false, $date = "", $event_id) {
		global $wpdb, $dpProEventCalendar, $table_prefix;
		
		if($counter) {
			$querystr = "
            SELECT quantity";
		} else {
		$querystr = "
            SELECT *";
		}
		$querystr .= "
            FROM ".$this->table_booking."
			WHERE id_event = ".$event_id." AND status <> 'pending'
            ";
		
		if(!empty($date)) {
			$querystr .= "AND event_date = '".$date."'";	
		}
		
		if($counter) {
			$bookings_obj = $wpdb->get_results($querystr, OBJECT);
			$counter = 0;
			foreach($bookings_obj as $booking) {
				$counter += $booking->quantity;
			}
			return $counter;
		} else {
			$bookings_obj = $wpdb->get_results($querystr, OBJECT);
			
			return $bookings_obj;
		}
	}
	
}
?>