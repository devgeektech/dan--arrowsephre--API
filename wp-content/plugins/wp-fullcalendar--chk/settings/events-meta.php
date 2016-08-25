<?php

/**
 * Adds meta boxes to the events
 *
 */
function dpProEventCalendar_meta_box_add() {
	add_meta_box( 'dpProEventCalendar_events_meta', __('Event Data', 'dpProEventCalendar'), 'dpProEventCalendar_events_display', 'pec-events', 'normal', 'high' );
	add_meta_box( 'dpProEventCalendar_booking_meta', __('Booking', 'dpProEventCalendar'), 'dpProEventCalendar_booking_display', 'pec-events', 'normal', 'high' );
	add_meta_box( 'dpProEventCalendar_events_side_meta', __('Event Date Info', 'dpProEventCalendar'), 'dpProEventCalendar_events_side_display', 'pec-events', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'dpProEventCalendar_meta_box_add' );

function dpProEventCalendar_meta_box_save( $post_id ) {
	global $wpdb, $dpProEventCalendar, $table_prefix;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;

	// now we can actually save the data
	$allowed = array(
		'a' => array( // on allow a tags
			'href' => array() // and those anchors can only have href attribute
		)
	);

	// make sure data is set, if author has removed the field or not populated it, delete it
	if( isset( $_POST['pec_all_day'] ) && $_POST['pec_all_day'] != '' ) {
		update_post_meta( $post_id, 'pec_all_day', wp_kses( $_POST['pec_all_day'], $allowed ) );
	} else {
		delete_post_meta($post_id, 'pec_all_day');
	};
	if( isset( $_POST['pec_hide_time'] ) && $_POST['pec_hide_time'] != '' ) {
		update_post_meta( $post_id, 'pec_hide_time', wp_kses( $_POST['pec_hide_time'], $allowed ) );
	} else {
		delete_post_meta($post_id, 'pec_hide_time');
	};
	if( isset( $_POST['pec_id_calendar'] ) && $_POST['pec_id_calendar'] != '' ) {
		update_post_meta( $post_id, 'pec_id_calendar', wp_kses( $_POST['pec_id_calendar'], $allowed ) );
	} else {
		delete_post_meta($post_id, 'pec_id_calendar');
	};
	
	update_post_meta( $post_id, 'pec_enable_booking', wp_kses( $_POST['pec_enable_booking'], $allowed ) );
	update_post_meta( $post_id, 'pec_show_limit', wp_kses( $_POST['pec_show_limit'], $allowed ) );
	update_post_meta( $post_id, 'pec_booking_limit', wp_kses( $_POST['pec_booking_limit'], $allowed ) );
	update_post_meta( $post_id, 'pec_booking_price', wp_kses( $_POST['pec_booking_price'], $allowed ) );
	
	if( isset( $_POST['pec_recurring_frecuency'] ) && $_POST['pec_recurring_frecuency'] != '' ) {
		update_post_meta( $post_id, 'pec_recurring_frecuency', wp_kses( $_POST['pec_recurring_frecuency'], $allowed ) );
	} else {
		delete_post_meta($post_id, 'pec_recurring_frecuency');
	};
	
	if(is_array($dpProEventCalendar['custom_fields_counter'])) {
		$counter = 0;
		
		foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
			update_post_meta( $post_id, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], wp_kses( $_POST['pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter]], $allowed ) );
			
			$counter++;		
		}
	}
	update_post_meta( $post_id, 'pec_link', wp_kses( $_POST['pec_link'], $allowed ) );
	update_post_meta( $post_id, 'pec_use_link', wp_kses( $_POST['pec_use_link'], $allowed ) );
	update_post_meta( $post_id, 'pec_color', wp_kses( $_POST['pec_color'], $allowed ) );
	update_post_meta( $post_id, 'pec_fb_event', wp_kses( $_POST['pec_fb_event'], $allowed ) );
	update_post_meta( $post_id, 'pec_share', wp_kses( $_POST['pec_share'], $allowed ) );
	update_post_meta( $post_id, 'pec_location', wp_kses( $_POST['pec_location'], $allowed ) );
	update_post_meta( $post_id, 'pec_phone', wp_kses( $_POST['pec_phone'], $allowed ) );
	update_post_meta( $post_id, 'pec_map', wp_kses( $_POST['pec_map'], $allowed ) );
	update_post_meta( $post_id, 'pec_map_lnlat', wp_kses( $_POST['pec_map_lnlat'], $allowed ) );
	update_post_meta( $post_id, 'pec_user_rate', wp_kses( $_POST['pec_user_rate'], $allowed ) );
	update_post_meta( $post_id, 'pec_rate', wp_kses( $_POST['pec_rate'], $allowed ) );
	update_post_meta( $post_id, 'pec_date', wp_kses( $_POST['pec_date'] . " " . $_POST['pec_time_hours'] . ":" . $_POST['pec_time_minutes'] . ":00", $allowed ) );
	update_post_meta( $post_id, 'pec_end_time_hh', wp_kses( $_POST['pec_end_time_hh'], $allowed ) );
	update_post_meta( $post_id, 'pec_end_time_mm', wp_kses( $_POST['pec_end_time_mm'], $allowed ) );
	update_post_meta( $post_id, 'pec_end_date', wp_kses( $_POST['pec_end_date'], $allowed ) );
	update_post_meta( $post_id, 'pec_exceptions', wp_kses( $_POST['pec_exceptions'], $allowed ) );
	update_post_meta( $post_id, 'pec_extra_dates', wp_kses( $_POST['pec_extra_dates'], $allowed ) );
	update_post_meta( $post_id, 'pec_daily_every', wp_kses( $_POST['pec_daily_every'], $allowed ) );
	update_post_meta( $post_id, 'pec_daily_working_days', wp_kses( $_POST['pec_daily_working_days'], $allowed ) );
	update_post_meta( $post_id, 'pec_weekly_day', wp_kses( $_POST['pec_weekly_day'], $allowed ) );
	update_post_meta( $post_id, 'pec_weekly_every', wp_kses( $_POST['pec_weekly_every'], $allowed ) );
	update_post_meta( $post_id, 'pec_monthly_every', wp_kses( $_POST['pec_monthly_every'], $allowed ) );
	update_post_meta( $post_id, 'pec_monthly_position', wp_kses( $_POST['pec_monthly_position'], $allowed ) );
	update_post_meta( $post_id, 'pec_monthly_day', wp_kses( $_POST['pec_monthly_day'], $allowed ) );
	
	if(is_array($_POST['pec_booking_status'])) {
		foreach($_POST['pec_booking_status'] as $booking_id => $booking_status) {
			
			$wpdb->update( 
				$table_name_booking, 
				array( 
					'status'		=> $booking_status
				), 
				array( 'id' => $booking_id ), 
				array( 
					'%s'
				),
				array( 
					'%d'
				) 
			);
		}
	}
	
}
add_action( 'save_post', 'dpProEventCalendar_meta_box_save' );

function dpProEventCalendar_events_display( $post ) {
	global $dpProEventCalendar, $wpdb, $table_prefix;
	
	$table_name_sd = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
	
	$values = get_post_custom( $post->ID );
	$pec_link = isset( $values['pec_link'] ) ? $values['pec_link'][0] : '';
	$pec_color = isset( $values['pec_color'] ) ? $values['pec_color'][0] : '';
	$pec_fb_event = isset( $values['pec_fb_event'] ) ? $values['pec_fb_event'][0] : '';
	$pec_share = isset( $values['pec_share'] ) ? $values['pec_share'][0] : '';
	$pec_location = isset( $values['pec_location'] ) ? $values['pec_location'][0] : '';
	$pec_phone = isset( $values['pec_phone'] ) ? $values['pec_phone'][0] : '';
	$pec_map = isset( $values['pec_map'] ) ? $values['pec_map'][0] : '';
	$pec_map_lnlat = isset( $values['pec_map_lnlat'] ) ? $values['pec_map_lnlat'][0] : '';
	$pec_user_rate = isset( $values['pec_user_rate'] ) ? $values['pec_user_rate'][0] : '';
	$pec_rate = isset( $values['pec_rate'] ) ? $values['pec_rate'][0] : '';
	$pec_use_link = isset( $values['pec_use_link'] ) ? $values['pec_use_link'][0] : '';
	
	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	
	?>
    <p class="misc-pub-section">
		<label for="pec_color"><?php _e('Color (optional)', 'dpProEventCalendar'); ?></label>
		<select name="pec_color">
        	<option value=""><?php _e('None', 'dpProEventCalendar')?></option>
             <?php 
			$counter = 0;
			$querystr = "
			SELECT *
			FROM $table_name_sd 
			ORDER BY title ASC
			";
			$sp_dates_obj = $wpdb->get_results($querystr, OBJECT);
			foreach($sp_dates_obj as $sp_dates) {
			?>
            
            	<option value="<?php echo $sp_dates->id?>" <?php echo ($pec_color == $sp_dates->id ? 'selected="selected"' : '')?>><?php echo $sp_dates->title?></option>
            
            <?php }?>
        </select>
        <label class="dp_ui_pec_content_desc"><?php _e('Select a color. To create a new one, go to the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-special' ).'" target="_blank">special dates</a> section','dpProEventCalendar'); ?></label>
	</p>
    <div class="clear"></div>
    <p class="misc-pub-section">
		<label for="pec_link" style="height:50px;"><?php _e('Link (optional)', 'dpProEventCalendar'); ?></label>
		<input type="text" name="pec_link" size="80" id="pec_link" value="<?php echo $pec_link; ?>" placeholder="<?php _e('Introduce a URL','dpProEventCalendar'); ?>" />
		<br />
		<input type="checkbox" value="1" name="pec_use_link" id="pec_use_link" <?php if($pec_use_link) {?> checked="checked"<?php }?> /> 
		<span class="pec_data_desc"><?php _e('Use this url instead of the event single page?','dpProEventCalendar'); ?></span>
	</p>
    <div class="clear"></div>
    <p class="misc-pub-section">
		<label for="pec_fb_event"><?php _e('Facebook Event URL (optional)', 'dpProEventCalendar'); ?></label>
		<input type="text" name="pec_fb_event" size="80" id="pec_fb_event" value="<?php echo $pec_fb_event; ?>" placeholder="<?php _e('Introduce a Facebook Page URL','dpProEventCalendar'); ?>" />
	</p>
    <div class="clear"></div>
    <p class="misc-pub-section">
		<label for="pec_share"><?php _e('Share Text (optional)', 'dpProEventCalendar'); ?></label>
		<input type="text" name="pec_share" size="80" id="pec_share" value="<?php echo $pec_share; ?>" placeholder="<?php _e('Introduce a text to be shared through social networks. i.e: "Event 123 on 14 May, 14:30."','dpProEventCalendar'); ?>" /><br />
	</p>
    <div class="clear"></div>
    <p class="misc-pub-section">
		<label for="pec_location"><?php _e('Location (optional)', 'dpProEventCalendar'); ?></label>
		<input type="text" name="pec_location" size="80" id="pec_location" value="<?php echo $pec_location; ?>" placeholder="<?php _e('Location to be displayed in the event list','dpProEventCalendar'); ?>" />
	</p>
    <div class="clear"></div>
    <p class="misc-pub-section">
		<label for="pec_phone"><?php _e('Phone (optional)', 'dpProEventCalendar'); ?></label>
		<input type="text" name="pec_phone" size="80" id="pec_phone" value="<?php echo $pec_phone; ?>" placeholder="<?php _e('Introduce the Phone number','dpProEventCalendar'); ?>" />
	</p>
    <div class="clear"></div>
    <p class="misc-pub-section">
		<label for="pec_map"><?php _e('Google Map (optional)', 'dpProEventCalendar'); ?></label>
		<input type="text" name="pec_map" size="80" id="pec_map" value="<?php echo $pec_map; ?>" placeholder="<?php _e('Introduce the country, city, address of the event. i.e: Spain, Madrid, Street x','dpProEventCalendar'); ?>" />
        <label for="pec_map_lnlat"><?php _e('Drag the marker to set a specific position (Lat, Lng)', 'dpProEventCalendar'); ?></label>
        <input type="text" name="pec_map_lnlat" size="80" id="pec_map_lnlat" value="<?php echo $pec_map_lnlat; ?>" readonly="readonly" />
        <div class="dp_pec_date_event_map_overlay" onclick="style.pointerEvents='none'" style="height:400px; margin-top: -400px; top: 400px;"></div>
		<div id="mapCanvas" style="height: 400px;"></div>
	</p>
    <div class="clear"></div>
    <p class="misc-pub-section">
		<label for="pec_user_rate"><?php _e('Rating (optional)', 'dpProEventCalendar'); ?></label>
        
		<select name="pec_rate" id="pec_rate">
        	<option value=""><?php _e('None', 'dpProEventCalendar'); ?></option>
            <option value="1" <?php echo $pec_rate == 1 ? 'selected="selected"' : ''?>><?php _e('1 Star', 'dpProEventCalendar'); ?></option>
            <option value="1.5" <?php echo $pec_rate == 1.5 ? 'selected="selected"' : ''?>><?php _e('1.5 Stars', 'dpProEventCalendar'); ?></option>
            <option value="2" <?php echo $pec_rate == 2 ? 'selected="selected"' : ''?>><?php _e('2 Stars', 'dpProEventCalendar'); ?></option>
            <option value="2.5" <?php echo $pec_rate == 2.5 ? 'selected="selected"' : ''?>><?php _e('2.5 Stars', 'dpProEventCalendar'); ?></option>
            <option value="3" <?php echo $pec_rate == 3 ? 'selected="selected"' : ''?>><?php _e('3 Stars', 'dpProEventCalendar'); ?></option>
            <option value="3.5" <?php echo $pec_rate == 3.5 ? 'selected="selected"' : ''?>><?php _e('3.5 Stars', 'dpProEventCalendar'); ?></option>
            <option value="4" <?php echo $pec_rate == 4 ? 'selected="selected"' : ''?>><?php _e('4 Stars', 'dpProEventCalendar'); ?></option>
            <option value="4.5" <?php echo $pec_rate == 4.5 ? 'selected="selected"' : ''?>><?php _e('4.5 Stars', 'dpProEventCalendar'); ?></option>
            <option value="5" <?php echo $pec_rate == 5 ? 'selected="selected"' : ''?>><?php _e('5 Stars', 'dpProEventCalendar'); ?></option>
        </select> 
    
    </p>    
    
    <div class="clear"></div>
    
    <p class="misc-pub-section">    
        <input type="checkbox" value="1" name="pec_user_rate" id="pec_user_rate" <?php if($pec_user_rate) {?> checked="checked"<?php }?> /> <?php _e('Allow logged in users to rate events. (The manual rating will be disabled)','dpProEventCalendar'); ?>
        <br />
        <label class="dp_ui_pec_content_desc"><?php _e('The rating will be displayed in the event description.','dpProEventCalendar'); ?></label>
	</p>
    <div class="clear"></div>
    
    <?php 
	if(is_array($dpProEventCalendar['custom_fields_counter'])) {
		$counter = 0;
		foreach($dpProEventCalendar['custom_fields_counter'] as $key) {
	?>
    <p class="misc-pub-section">
		<label for="pec_map"><?php echo $dpProEventCalendar['custom_fields']['name'][$counter]?></label>
        <?php if($dpProEventCalendar['custom_fields']['type'][$counter] == "checkbox") {?>
        <input type="checkbox" name="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" id="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" value="1" <?php if(get_post_meta($post->ID, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true)) { ?> checked="checked" <?php }?> /> <?php echo $dpProEventCalendar['custom_fields']['placeholder'][$counter]?>
        <?php } else { ?>
		<input type="text" name="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" size="80" id="pec_custom_<?php echo $dpProEventCalendar['custom_fields']['id'][$counter]?>" value="<?php echo get_post_meta($post->ID, 'pec_custom_'.$dpProEventCalendar['custom_fields']['id'][$counter], true); ?>" placeholder="<?php echo $dpProEventCalendar['custom_fields']['placeholder'][$counter]?>" />
        <?php }?>
	</p>
    <div class="clear"></div>
	<?php
			$counter++;
		}
	}
}

function dpProEventCalendar_booking_display( $post ) {
	global $wpdb, $table_prefix, $dp_pec_payments, $dpProEventCalendar;
	
	$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
	
	$values = get_post_custom( $post->ID );
	$pec_enable_booking = isset( $values['pec_enable_booking'] ) ? $values['pec_enable_booking'][0] : '';
	$pec_show_limit = isset( $values['pec_show_limit'] ) ? $values['pec_show_limit'][0] : '';
	$pec_booking_limit = isset( $values['pec_booking_limit'] ) ? $values['pec_booking_limit'][0] : '';
	$pec_booking_price = isset( $values['pec_booking_price'] ) ? $values['pec_booking_price'][0] : '';

	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	?>
    <p class="misc-pub-section">
		<input type="checkbox" value="1" name="pec_enable_booking" id="pec_enable_booking" <?php if($pec_enable_booking) {?> checked="checked"<?php }?> />  <?php _e('Enable Booking', 'dpProEventCalendar'); ?>
	</p>
    
    <p class="misc-pub-section">
		<label for="pec_booking_limit"><?php _e('Limit (optional)', 'dpProEventCalendar'); ?></label>
		<input type="number" min="0" max="999999" name="pec_booking_limit" size="50" id="pec_booking_limit" value="<?php echo $pec_booking_limit; ?>" placeholder="e.g 30" /><br />
        <label class="dp_ui_pec_content_desc"><?php _e('Introduce the maximum number of bookings allowed for this event.','dpProEventCalendar'); ?></label>
	</p>
    
    <div class="clear"></div>
    <?php if ( is_plugin_active( 'dp-pec-payments/dp-pec-payments.php' ) ) {?>
    <p class="misc-pub-section">
		<label for="pec_booking_limit"><?php _e('Price (optional)', 'dpProEventCalendar'); ?></label>
		<input type="number" min="0" max="999999" name="pec_booking_price" size="50" id="pec_booking_price" value="<?php echo $pec_booking_price; ?>" placeholder="e.g 100" /> &nbsp; <?php echo $dp_pec_payments['currency']?><br />
        <label class="dp_ui_pec_content_desc"><?php _e('Introduce the booking price. Integer numbers only.','dpProEventCalendar'); ?></label>
	</p>
    <div class="clear"></div>
    <?php } else { ?>
    <div class="misc-pub-section">
		<label for="pec_booking_limit"><?php _e('Price (optional)', 'dpProEventCalendar'); ?></label>
        
		<input type="number" min="0" max="999999" name="pec_booking_price" size="50" id="pec_booking_price" value="" disabled="disabled" /><br />
        <div class="pec_admin_errorCustom" style="float: left;"><p><?php _e('Notice: This feature requires the <a href="'.DP_PRO_EVENT_CALENDAR_PAYMENTS_URL.'" target="_blank">
Payments Extension</a>.','dpProEventCalendar'); ?></p></div>
        <label class="dp_ui_pec_content_desc"><?php _e('Introduce the booking price. Leave blank to allow bookings for free.','dpProEventCalendar'); ?></label>
	</div>
    <div class="clear"></div>
    <?php }?>
    
    <h2><?php _e('List of Bookings', 'dpProEventCalendar'); ?> 
    
    <?php if(current_user_can('edit_others_posts')) {?>
    <a class="button" style="float: right;" href="<?php echo dpProEventCalendar_plugin_url('includes/export_bookings.php?event_id='.$post->ID)?>"><?php _e('Export to Excel', 'dpProEventCalendar'); ?></a>
    <?php }?>
    
    </h2>
    
    <p><?php _e('Add extra fields from the <a href="'.admin_url( 'admin.php?page=dpProEventCalendar-settings' ).'" target="_blank"> General settings</a>.','dpProEventCalendar'); ?></p>

    
    <table class="widefat" cellpadding="0" cellspacing="0" id="pec-sort-table">
        <thead>
            <tr style="cursor:default !important;">
                <th><?php _e('Name','dpProEventCalendar'); ?></th>
                <th><?php _e('Email','dpProEventCalendar'); ?></th>
                <th><?php _e('Booking Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Event Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Quantity','dpProEventCalendar'); ?></th>
                <th><?php _e('Phone','dpProEventCalendar'); ?></th>
                <th><?php _e('Comment','dpProEventCalendar'); ?></th>
                <th><?php _e('Extra Fields','dpProEventCalendar'); ?></th>
                <th><?php _e('Status','dpProEventCalendar'); ?></th>
                <th></th>
             </tr>
        </thead>
        <tbody id="pec-booking-list">
            <?php
			$querystr = "
            SELECT COUNT(*) as count
            FROM $table_name_booking
			WHERE id_event = ".$post->ID;
            $counter = $wpdb->get_row($querystr, OBJECT);
			
			$booking_count = 0;
            $querystr = "
            SELECT *
            FROM $table_name_booking
			WHERE id_event = ".$post->ID."
            ORDER BY id DESC
			LIMIT 30
            ";
            $bookings_obj = $wpdb->get_results($querystr, OBJECT);
            foreach($bookings_obj as $booking) {
				if(is_numeric($booking->id_user) && $booking->id_user > 0) {
					//$userdata = FALSE;
					//$userdata = WP_User::get_data_by( 'id', $booking->id_user );

					$userdata = get_userdata($booking->id_user);
				} else {
					$userdata = new stdClass();
					$userdata->display_name = $booking->name;
					$userdata->user_email = $booking->email;	
				}
                ?>
            <tr>
                <td><?php echo $userdata->display_name?></td>
                <td><?php echo $userdata->user_email?></td>
                <td><?php echo date_i18n(get_option('date_format') . ' '. get_option('time_format'), strtotime($booking->booking_date))?></td>
                <td><?php echo date_i18n(get_option('date_format'), strtotime($booking->event_date))?></td>
                <td><?php echo $booking->quantity?></td>
                <td><?php echo $booking->phone?></td>
                <td><?php echo nl2br($booking->comment)?></td>
                <td>
                	<?php
                	$extra_fields = unserialize($booking->extra_fields);
                	if(!is_array($extra_fields)) 
                		$extra_fields = array();

                	$html = '';

                	foreach($extra_fields as $key=>$value) {

                		$field_index = array_keys($dpProEventCalendar['booking_custom_fields']['id'], str_replace('pec_custom_', '', $key));
                		
                		if(is_array($field_index)) {
	                		$field_index = $field_index[0];
	                	} else {
	                		$field_index = '';
	                	}

                		if($value != "" && is_numeric($field_index)) {
                			if($dpProEventCalendar['booking_custom_fields']['type'][$field_index] == 'checkbox') {
                				$value = __('Yes', 'dpProEventCalendar');
                			}
							$html .= '<div class="pec_event_page_custom_fields">
										<strong>'.$dpProEventCalendar['booking_custom_fields']['name'][$field_index].': </strong>'.$value;
							$html .= '</div>';
						}

                	}
                	
                	echo $html;
					?>
                </td>
                <td>
                	<select name="pec_booking_status[<?php echo $booking->id?>]">
						<option value=""><?php echo __( 'Completed', 'dpProEventCalendar' )?></option>
                        <option value="pending" <?php if($booking->status == "pending") {?> selected="selected" <?php }?>><?php echo __( 'Pending', 'dpProEventCalendar' )?></option>
                    </select>
                </td>
                <td><input type="button" value="<?php echo __( 'Delete', 'dpProEventCalendar' )?>" name="delete_booking" class="button-primary" onclick="if(confirm('<?php echo __( 'Are you sure that you want to remove this booking?', 'dpProEventCalendar' )?>')) { pec_removeBooking(<?php echo $booking->id?>, this); }" /></td>
            </tr>
            <?php 
				$booking_count++;
			}
			
			if($booking_count == 0) {
				echo '<tr><td colspan="5"><p>'.__( 'No Booking Found.', 'dpProEventCalendar' ).'</p></td></tr>';	
			}?>
        </tbody>
        <tfoot>
            <tr style="cursor:default !important;">
                <th><?php _e('Name','dpProEventCalendar'); ?></th>
                <th><?php _e('Email','dpProEventCalendar'); ?></th>
                <th><?php _e('Booking Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Event Date','dpProEventCalendar'); ?></th>
                <th><?php _e('Quantity','dpProEventCalendar'); ?></th>
                <th><?php _e('Phone','dpProEventCalendar'); ?></th>
                <th><?php _e('Comment','dpProEventCalendar'); ?></th>
                <th><?php _e('Extra Fields','dpProEventCalendar'); ?></th>
                <th><?php _e('Status','dpProEventCalendar'); ?></th>
                <th></th>
             </tr>
        </tfoot>
    </table>
    <?php 
	if($counter->count > 30) {?>
        <a class="button pec-load-more" data-dppec-offset="30" data-dppec-eventid="<?php echo $post->ID?>" href="javascript:void(0);"><?php _e('Load More','dpProEventCalendar'); ?> (<span><?php echo ($counter->count - 30)?></span>)</a>
	<?php
	}
}

function dpProEventCalendar_events_side_display( $post ) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
	$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
	
	require_once (dirname (__FILE__) . '/../classes/base.class.php');
	
	$values = get_post_custom( $post->ID );
	$pec_all_day = isset( $values['pec_all_day'] ) ? $values['pec_all_day'][0] : '0';
	$pec_hide_time = isset( $values['pec_hide_time'] ) ? $values['pec_hide_time'][0] : '0';
	$pec_id_calendar = isset( $values['pec_id_calendar'] ) ? $values['pec_id_calendar'][0] : '';
	$pec_end_time_hh = isset( $values['pec_end_time_hh'] ) ? $values['pec_end_time_hh'][0] : '';
	$pec_end_time_mm = isset( $values['pec_end_time_mm'] ) ? $values['pec_end_time_mm'][0] : '';
	$pec_date = isset( $values['pec_date'] ) ? $values['pec_date'][0] : '';
	$pec_end_date = isset( $values['pec_end_date'] ) ? $values['pec_end_date'][0] : '';
	$pec_exceptions = isset( $values['pec_exceptions'] ) ? $values['pec_exceptions'][0] : '';
	$pec_extra_dates = isset( $values['pec_extra_dates'] ) ? $values['pec_extra_dates'][0] : '';
	$pec_recurring_frecuency = isset( $values['pec_recurring_frecuency'] ) ? $values['pec_recurring_frecuency'][0] : '0';
	$pec_daily_every = isset( $values['pec_daily_every'] ) ? $values['pec_daily_every'][0] : '1';
	$pec_daily_working_days = isset( $values['pec_daily_working_days'] ) ? $values['pec_daily_working_days'][0] : '0';
	$pec_weekly_day = isset( $values['pec_weekly_day'] ) ? unserialize($values['pec_weekly_day'][0]) : array();
	$pec_weekly_every = isset( $values['pec_weekly_every'] ) ? $values['pec_weekly_every'][0] : '1';
	$pec_monthly_every = isset( $values['pec_monthly_every'] ) ? $values['pec_monthly_every'][0] : '1';
	$pec_monthly_position = isset( $values['pec_monthly_position'] ) ? $values['pec_monthly_position'][0] : '';
	$pec_monthly_day = isset( $values['pec_monthly_day'] ) ? $values['pec_monthly_day'][0] : '';
	
	if(!is_array($pec_weekly_day)) { $pec_weekly_day = array(); }
	
	do_action( 'pec_enqueue_admin', 1);
	$dpProEventCalendar_class = new DpProEventCalendar( true, (is_numeric($pec_id_calendar) ? $pec_id_calendar : null) );
		
	$dpProEventCalendar_class->addScripts(true);
		
	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
	?>
    <div id="misc-publishing-actions">
        <div class="misc-pub-section">
            <label for="pec_id_calendar"><?php _e('Calendar', 'dpProEventCalendar'); ?></label><br />
            <input type="hidden" name="pec_id_calendar" id="pec_id_calendar" value="<?php echo $pec_id_calendar?>" />
            <select name="pec_id_calendar_tmp[]" id="pec_id_calendar_tmp" multiple="multiple" style="width:100%;" onchange="pec_update_cal_list(this);">
                <?php
				$count = 0;
                $querystr = "
                SELECT *
                FROM $table_name_calendars
                ORDER BY title ASC
                ";
                $calendars_obj = $wpdb->get_results($querystr, OBJECT);
                foreach($calendars_obj as $calendar) {
                ?>
                    <option value="<?php echo $calendar->id?>" <?php if((in_array($calendar->id, explode(',', $pec_id_calendar))) || (empty($pec_id_calendar) && count($calendars_obj) == 1 && $count == 0)) { ?> selected="selected"<?php }?>><?php echo $calendar->title?></option>
                <?php 
					$count++;
				}?>
            </select>
            <script type="text/javascript">
            function pec_update_cal_list(el) {
				var option_all = jQuery("#pec_id_calendar_tmp option:selected").map(function () {
					return jQuery(this).val();
				}).get().join(',');
				
				jQuery('#pec_id_calendar').val(option_all);
				
			}
			<?php if(empty($pec_id_calendar) && count($calendars_obj) == 1) {?>
			pec_update_cal_list(jQuery('#pec_id_calendar_tmp'));
			<?php }?>
            </script>
            <label class="dp_ui_pec_content_desc"><?php _e('Assign this event to one or more calendars. Select multiple calendars pressing "ctrl".', 'dpProEventCalendar'); ?></label>
        </div>
        <div class="misc-pub-section">
            <label for="pec_date"><?php _e('Date', 'dpProEventCalendar'); ?></label><br />
            <input type="text" readonly="readonly" name="pec_date" maxlength="10" id="pec_date" class="large-text" value="<?php echo $pec_date != '' ? date("Y-m-d", strtotime($pec_date)) : ''?>" style="width:100px;" />
            <button type="button" class="dpProEventCalendar_btn_getEventDate">
                <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ); ?>" alt="Calendar" title="Calendar">
            </button>
        </div>
        <div class="misc-pub-section">
            <label for="pec_time_hours"><?php _e('Start Time', 'dpProEventCalendar'); ?></label><br />
            <select name="pec_time_hours" id="pec_time_hours" style="width:80px;">
                <?php for($i = 0; $i <= 23; $i++) {
					if(strpos(get_option('time_format'), "A")  !== false) {
						$hour = str_pad(($i > 12 ? $i - 12 : ($i == '00' ? '12' : $i)), 2, "0", STR_PAD_LEFT). ' '.date('A', mktime($i, 0));
					} else {
						$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
					}
					?>
                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if(date("H", strtotime($pec_date)) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo $hour?></option>
                <?php }?>
            </select>
            <span>:</span>
            <select name="pec_time_minutes" id="pec_time_minutes" style="width:50px;">
                <?php for($i = 0; $i <= 59; $i += 5) {?>
                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if(date("i", strtotime($pec_date)) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></option>
                <?php }?>
            </select>
            &nbsp; <input type="checkbox" name="pec_hide_time" class="checkbox" id="pec_hide_time" value="1" <?php if($pec_hide_time) {?> checked="checked" <?php }?> /> <?php _e('Hide Time','dpProEventCalendar'); ?>
        </div>
        <div class="misc-pub-section">
            <label for="pec_end_time_hh"><?php _e('End Time', 'dpProEventCalendar'); ?></label><br />
            <select name="pec_end_time_hh" id="pec_end_time_hh" style="width:80px;">
            	<option value="">--</option>
                <?php for($i = 0; $i <= 23; $i++) {
					if(strpos(get_option('time_format'), "A")  !== false) {
						$hour = str_pad(($i > 12 ? $i - 12 : ($i == '00' ? '12' : $i)), 2, "0", STR_PAD_LEFT). ' '.date('A', mktime($i, 0));
					} else {
						$hour = str_pad($i, 2, "0", STR_PAD_LEFT);
					}
					?>
                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if($pec_end_time_hh != "" & str_pad($pec_end_time_hh, 2, "0", STR_PAD_LEFT) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo $hour?></option>
                <?php }?>
            </select>
            <span>:</span>
            <select name="pec_end_time_mm" id="pec_end_time_mm" style="width:50px;">
            	<option value="">--</option>
                <?php for($i = 0; $i <= 59; $i += 5) {?>
                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>" <?php if($pec_end_time_mm != "" & str_pad($pec_end_time_mm, 2, "0", STR_PAD_LEFT) == str_pad($i, 2, "0", STR_PAD_LEFT)) {?> selected="selected" <?php }?>><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></option>
                <?php }?>
            </select>
        </div>
        <div class="misc-pub-section">
            <input type="checkbox" name="pec_all_day" id="pec_all_day" value="1" <?php echo ($pec_all_day ? 'checked="checked"' : ''); ?> />
            <label for="pec_all_day"><?php _e('Set if the event is all the day.', 'dpProEventCalendar'); ?></label>
        </div>
        <div class="misc-pub-section">
            <select name="pec_recurring_frecuency" id="pec_recurring_frecuency" onchange="pec_update_frequency(this.value);">
                <option value="0" <?php if($pec_recurring_frecuency == 0) {?> selected="selected" <?php }?>><?php _e('None','dpProEventCalendar'); ?></option>
                <option value="1" <?php if($pec_recurring_frecuency == 1) {?> selected="selected" <?php }?>><?php _e('Daily','dpProEventCalendar'); ?></option>
                <option value="2" <?php if($pec_recurring_frecuency == 2) {?> selected="selected" <?php }?>><?php _e('Weekly','dpProEventCalendar'); ?></option>
                <option value="3" <?php if($pec_recurring_frecuency == 3) {?> selected="selected" <?php }?>><?php _e('Monthly','dpProEventCalendar'); ?></option>
                <option value="4" <?php if($pec_recurring_frecuency == 4) {?> selected="selected" <?php }?>><?php _e('Yearly','dpProEventCalendar'); ?></option>
            </select>
            <label for="pec_recurring_frecuency"><?php _e('Select a frequency', 'dpProEventCalendar'); ?></label>
        </div>
        
        <div class="misc-pub-section pec_daily_frequency" style="display:none;">
			<div id="pec_daily_every_div"><?php _e('Every','dpProEventCalendar'); ?> <input type="number" min="1" max="99" style="width:50px;" maxlength="2" name="pec_daily_every" id="pec_daily_every" value="<?php echo $pec_daily_every?>" /> <?php _e('days','dpProEventCalendar'); ?></div>
            <div id="pec_daily_working_days_div"><input type="checkbox" name="pec_daily_working_days" id="pec_daily_working_days" onclick="pec_check_daily_working_days(this);" <?php if($pec_daily_working_days == 1) {?> checked="checked"<?php }?> value="1" /><?php _e('All working days','dpProEventCalendar'); ?></div>
        </div>
        <div class="misc-pub-section pec_weekly_frequency" style="display:none;">
			<?php _e('Repeat every','dpProEventCalendar'); ?> <input type="number" min="1" max="99" style="width:50px;" maxlength="2" name="pec_weekly_every" value="<?php echo $pec_weekly_every?>" /> <?php _e('week(s) on:','dpProEventCalendar'); ?>
            <br /><br />
            <input type="checkbox" value="1" name="pec_weekly_day[]" <?php if(in_array(1, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Mon','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="2" name="pec_weekly_day[]" <?php if(in_array(2, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Tue','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="3" name="pec_weekly_day[]" <?php if(in_array(3, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Wed','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="4" name="pec_weekly_day[]" <?php if(in_array(4, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Thu','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="5" name="pec_weekly_day[]" <?php if(in_array(5, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Fri','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="6" name="pec_weekly_day[]" <?php if(in_array(6, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Sat','dpProEventCalendar'); ?><br />
            <input type="checkbox" value="7" name="pec_weekly_day[]" <?php if(in_array(7, $pec_weekly_day)) {?> checked="checked" <?php }?> /> &nbsp; <?php _e('Sun','dpProEventCalendar'); ?>
        </div>
        <div class="misc-pub-section pec_monthly_frequency" style="display:none;">
			<?php _e('Repeat every','dpProEventCalendar'); ?> <input type="number" min="1" max="99" style="width:50px;" maxlength="2" name="pec_monthly_every" value="<?php echo $pec_monthly_every?>" /> <?php _e('month(s) on:','dpProEventCalendar'); ?>
            <br /><br />
            <select name="pec_monthly_position" id="pec_monthly_position" style="width:90px;">
	            <option value=""><?php _e('','dpProEventCalendar'); ?></option>
                <option value="first" <?php if($pec_monthly_position == 'first') {?> selected="selected" <?php }?>><?php _e('First','dpProEventCalendar'); ?></option>
                <option value="second" <?php if($pec_monthly_position == 'second') {?> selected="selected" <?php }?>><?php _e('Second','dpProEventCalendar'); ?></option>
                <option value="third" <?php if($pec_monthly_position == 'third') {?> selected="selected" <?php }?>><?php _e('Third','dpProEventCalendar'); ?></option>
                <option value="fourth" <?php if($pec_monthly_position == 'fourth') {?> selected="selected" <?php }?>><?php _e('Fourth','dpProEventCalendar'); ?></option>
                <option value="last" <?php if($pec_monthly_position == 'last') {?> selected="selected" <?php }?>><?php _e('Last','dpProEventCalendar'); ?></option>
            </select>
            
            <select name="pec_monthly_day" id="pec_monthly_day" style="width:150px;">
            <option value=""><?php _e('','dpProEventCalendar'); ?></option>
	            <option value="monday" <?php if($pec_monthly_day == 'monday') {?> selected="selected" <?php }?>><?php _e('Monday','dpProEventCalendar'); ?></option>
                <option value="tuesday" <?php if($pec_monthly_day == 'tuesday') {?> selected="selected" <?php }?>><?php _e('Tuesday','dpProEventCalendar'); ?></option>
                <option value="wednesday" <?php if($pec_monthly_day == 'wednesday') {?> selected="selected" <?php }?>><?php _e('Wednesday','dpProEventCalendar'); ?></option>
                <option value="thursday" <?php if($pec_monthly_day == 'thursday') {?> selected="selected" <?php }?>><?php _e('Thursday','dpProEventCalendar'); ?></option>
                <option value="friday" <?php if($pec_monthly_day == 'friday') {?> selected="selected" <?php }?>><?php _e('Friday','dpProEventCalendar'); ?></option>
                <option value="saturday" <?php if($pec_monthly_day == 'saturday') {?> selected="selected" <?php }?>><?php _e('Saturday','dpProEventCalendar'); ?></option>
                <option value="sunday" <?php if($pec_monthly_day == 'sunday') {?> selected="selected" <?php }?>><?php _e('Sunday','dpProEventCalendar'); ?></option>
            </select>
        </div>
        
        <div class="misc-pub-section pec_frequency_options" style="display:none;">
			<?php _e('Exceptions','dpProEventCalendar'); ?> <input type="text" name="pec_exceptions" id="pec_exceptions" value="<?php echo $pec_exceptions?>" />
            <label class="dp_ui_pec_content_desc"><?php _e('Add dates to exclude from the recurring event frequency. Format YYYY-MM-DD. i.e: 2014-12-24,2014-12-25,2014-12-31','dpProEventCalendar'); ?></label>
        </div>
        
        <div class="misc-pub-section">
            <label for="pec_end_date"><?php _e('End Date', 'dpProEventCalendar'); ?></label><br />
            <input type="text" readonly="readonly" name="pec_end_date" maxlength="10" id="pec_end_date" class="large-text" value="<?php echo $pec_end_date != '0000-00-00' ? $pec_end_date : ''?>" style="width:100px;" />
            <button type="button" class="dpProEventCalendar_btn_getEventEndDate">
                <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/calendar.png' ); ?>" alt="Calendar" title="Calendar">
            </button>
            <button type="button" onclick="jQuery('#pec_end_date').val('');">
                <img src="<?php echo dpProEventCalendar_plugin_url( 'images/admin/clear.png' ); ?>" alt="Clear" title="Clear">
            </button>
            <label class="dp_ui_pec_content_desc"><?php _e('Select the end date. A frequency option must be selected.','dpProEventCalendar'); ?></label>
        </div>
        
        <div class="misc-pub-section">
			<?php _e('Extra Dates','dpProEventCalendar'); ?> <input type="text" name="pec_extra_dates" id="pec_extra_dates" value="<?php echo $pec_extra_dates?>" />
            <label class="dp_ui_pec_content_desc"><?php _e('Add extra dates to include in the calendar. Format YYYY-MM-DD. i.e: 2014-12-24,2014-12-25,2014-12-31','dpProEventCalendar'); ?></label>
        </div>
    </div>
    <script type="text/javascript">
		function pec_check_daily_working_days(chk) {
			if(jQuery(chk).is(':checked')) {
				jQuery('#pec_daily_every_div').hide();
			} else {
				jQuery('#pec_daily_every_div').show();
			}
		}
		
		function pec_update_frequency(val) {
			jQuery('.pec_frequency_options').hide();
			jQuery('.pec_daily_frequency').hide();
			jQuery('.pec_weekly_frequency').hide();
			jQuery('.pec_monthly_frequency').hide();
			
			switch(val) {
				case "1":
					jQuery('.pec_daily_frequency').show();
					jQuery('.pec_weekly_frequency').hide();
					jQuery('.pec_monthly_frequency').hide();
					jQuery('.pec_frequency_options').show();
					break;	
				case "2":
					jQuery('.pec_daily_frequency').hide();
					jQuery('.pec_weekly_frequency').show();
					jQuery('.pec_monthly_frequency').hide();
					jQuery('.pec_frequency_options').show();
					break;	
				case "3":
					jQuery('.pec_daily_frequency').hide();
					jQuery('.pec_weekly_frequency').hide();
					jQuery('.pec_monthly_frequency').show();
					jQuery('.pec_frequency_options').show();
					break;	
				case "4":
					jQuery('.pec_daily_frequency').hide();
					jQuery('.pec_weekly_frequency').hide();
					jQuery('.pec_monthly_frequency').hide();
					jQuery('.pec_frequency_options').show();
					break;	
			}
		}
		pec_update_frequency("<?php echo $pec_recurring_frecuency?>");
		pec_check_daily_working_days(jQuery('#pec_daily_working_days'));
	</script>

    <?php $dpProEventCalendar_class->output(true);?>
	<?php
}

/*
function dpProEventcalendar_price_column_register( $columns ) {
	$columns['start_date'] = __( 'Date' );
 
	return $columns;
}
add_filter( 'manage_edit-post_columns', 'dpProEventcalendar_price_column_register' );
*/

add_action('manage_posts_columns', 'dpProEventcalendar_add_column_to_events_list');
function dpProEventcalendar_add_column_to_events_list( $posts_columns ) {
    global $typenow;
    if ($typenow != 'pec-events') return $posts_columns;

	if (!isset($posts_columns['author'])) {
        $new_posts_columns = $posts_columns;
    } else {
        $new_posts_columns = array();
        $index = 0;
        foreach($posts_columns as $key => $posts_column) {
            if ($key=='author')
                $new_posts_columns['calendar'] = null;
            $new_posts_columns[$key] = $posts_column;
        }
    }
    $new_posts_columns['calendar'] = __('Calendar', 'dpProEventCalendar');
	$new_posts_columns['start_date'] = __('Date', 'dpProEventCalendar');
	$new_posts_columns['end_date'] = __('End Date', 'dpProEventCalendar');
	$new_posts_columns['frequency'] = __('Frequency', 'dpProEventCalendar');
	$new_posts_columns['bookings'] = __('Bookings', 'dpProEventCalendar');
    return $new_posts_columns;
}

add_action('manage_posts_custom_column', 'dpProEventcalendar_column_for_events_list',10,2);
function dpProEventcalendar_column_for_events_list( $column_id,$post_id ) {
    global $typenow, $current_user, $wpdb, $dpProEventCalendar, $table_prefix;
    if ($typenow=='pec-events') {
		
        switch ($column_id) {
			case 'calendar':
				$id_calendar = get_post_meta($post_id, 'pec_id_calendar', true);
				
				if (isset($id_calendar)) {
					
					$cal_list = explode(",", $id_calendar);
					require_once (dirname (__FILE__) . '/../classes/base.class.php');
					
					$calendar = "";
					
					$count = 0;
					foreach($cal_list as $key) {
						$dpProEventCalendar_class = new DpProEventCalendar( true, $key );
						
						if($count > 0) {
							$calendar .= ' - ';	
						}
						
						$calendar .= '<a href="'.admin_url('admin.php?page=dpProEventCalendar-admin&edit='.$key).'">' . $dpProEventCalendar_class->getCalendarName() . '</a>';
						$count++;
					}
					echo $calendar;
				}
				break;
			
			case 'start_date':
				$pec_date = get_post_meta($post_id, 'pec_date', true);
				echo '<abbr title="'.date_i18n(get_option('date_format'), strtotime($pec_date)).' '.date_i18n(get_option('time_format'), strtotime($pec_date)).'">'.date_i18n(get_option('date_format'), strtotime($pec_date)).' '.date_i18n(get_option('time_format'), strtotime($pec_date)).'</abbr><br>'.ucfirst(get_post_status($post_id));
				break;
			
			case 'end_date':
				$end_time_hh = get_post_meta($post_id, 'pec_end_time_hh', true);
				$end_time_mm = get_post_meta($post_id, 'pec_end_time_mm', true);
				
				if(empty($end_time_hh)) $end_time_hh = '00';
				if(empty($end_time_mm)) $end_time_mm = '00';
				
				$pec_end_date = get_post_meta($post_id, 'pec_end_date', true);
				$pec_date = $pec_end_date . ' ' . $end_time_hh . ':' . $end_time_mm . ':00';

				if($pec_end_date != "" && $pec_end_date != "0000-00-00") {
					echo '<abbr title="'.date_i18n(get_option('date_format'), strtotime($pec_date)).' '.($end_time_hh != "" ? date_i18n(get_option('time_format'), strtotime($pec_date)) : '' ).'">'.date_i18n(get_option('date_format'), strtotime($pec_date)).' '.($end_time_hh != "" ? date_i18n(get_option('time_format'), strtotime($pec_date)) : '' ).'</abbr>';
				}
				break;
			
			case 'frequency':
				$frequency = get_post_meta($post_id, 'pec_recurring_frecuency', true);
				if($frequency != "" && $frequency > 0) {
					switch($frequency) {
						case 1: 
							echo __('Daily', 'dpProEventCalendar');
							break;	
						case 2: 
							echo __('Weekly', 'dpProEventCalendar');
							break;	
						case 3: 
							echo __('Monthly', 'dpProEventCalendar');
							break;	
						case 4: 
							echo __('Yearly', 'dpProEventCalendar');
							break;	
						
					}
				}
				break;
			case 'bookings':
				$table_name_booking = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_BOOKING;
				
				$querystr = "
					SELECT SUM(quantity) as counter
					FROM ".$table_name_booking."
					WHERE id_event = ".$post_id." AND status <> 'pending'
					";
				$bookings_obj = $wpdb->get_row($querystr, OBJECT);
				
				echo '<abbr>'.$bookings_obj->counter.'</abbr>';

				break;
        }		
    }
}

function dpProEventcalendar_price_column_register_sortable( $columns ) {
	global $typenow;
    if ($typenow != 'pec-events') return $columns;

	$columns['start_date'] = 'start_date';
 
	return $columns;
}
add_filter( 'manage_edit-pec-events_sortable_columns', 'dpProEventcalendar_price_column_register_sortable' );

function dpProEventcalendar_price_column_orderby( $vars ) {
	global $typenow;
	if ($typenow != 'pec-events') return $vars;
	if ( isset( $vars['orderby'] ) && 'start_date' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'pec_date',
			'orderby' => 'meta_value_num meta_value'
		) );
	}
 
	return $vars;
}
add_filter( 'request', 'dpProEventcalendar_price_column_orderby' );

function dpProEventcalendar_manage_columns($columns) {
	global $typenow, $wpdb;

	if ($typenow=='pec-events') {
		unset($columns['comments']);
		unset($columns['date']);
	}
    return $columns;
}
add_filter('manage_posts_columns' , 'dpProEventcalendar_manage_columns');

add_action('restrict_manage_posts','dpProEventcalendar_restrict_events_by_calendar');
function dpProEventcalendar_restrict_events_by_calendar() {
    global $typenow;
    global $wp_query;
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
	$table_name_calendars = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
	
    if ($typenow=='pec-events') {
        ?>
        <select name="pec_id_calendar" id="pec_id_calendar">
            <option value=""><?php _e('Show all calendars...','dpProEventCalendar'); ?></option>
            <?php
            $querystr = "
            SELECT *
            FROM $table_name_calendars
            ORDER BY title ASC
            ";
            $calendars_obj = $wpdb->get_results($querystr, OBJECT);
			if(is_array($calendars_obj)) {
				foreach($calendars_obj as $calendar) {
            ?>
                <option value="<?php echo $calendar->id?>" <?php if($calendar->id == $_GET['pec_id_calendar']) { ?> selected="selected"<?php }?>><?php echo $calendar->title?></option>
            <?php }
			}?>
        </select>
        <?php
    }
}

add_filter('parse_query','dpProEventcalendar_convert_filter');
function dpProEventcalendar_convert_filter($query) {
    global $pagenow;
    $qv = &$query->query_vars;
    if ($pagenow=='edit.php' &&
            isset($qv['post_type']) && $qv['post_type']=='pec-events' &&
            isset($_GET['pec_id_calendar']) && is_numeric($_GET['pec_id_calendar'])) {
		$query->query_vars['meta_query'] = array( 
			relation => 'OR',
			array('key' => "pec_id_calendar", "value" => '(,'.$_GET['pec_id_calendar'].',)', 'compare' => 'REGEXP'),
			array('key' => "pec_id_calendar", "value" => '(,'.$_GET['pec_id_calendar'].')$', 'compare' => 'REGEXP'),
			array('key' => "pec_id_calendar", "value" => '^('.$_GET['pec_id_calendar'].',)', 'compare' => 'REGEXP'),
			array('key' => "pec_id_calendar", "value" => $_GET['pec_id_calendar'])
		);
        //$query->query_vars['meta_value'] = $_GET['pec_id_calendar'];
		
		if ( isset( $_GET['orderby'] ) && 'start_date' == $_GET['orderby'] ) {
			$query->query_vars['meta_key'] = "pec_date";
			unset($query->query_vars['meta_value']);
			$query->query_vars['orderby'] = 'meta_value_num meta_value';
		}
    }
	
    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_NAME']) && $_GET['ADMIN_FILTER_FIELD_NAME'] != '') {
        $query->query_vars['meta_key'] = $_GET['ADMIN_FILTER_FIELD_NAME'];
    if (isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '')
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
}