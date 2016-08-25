jQuery(function($) {
	
	var geocoder = new google.maps.Geocoder();
	var	map;
	var marker;
	
	function geocodePosition(pos) {
	  geocoder.geocode({
		latLng: pos
	  }, function(responses) {
		if (responses && responses.length > 0) {
		  updateMarkerAddress(responses[0].formatted_address);
		} else {
		  //updateMarkerAddress("Cannot determine address at this location.");
		}
	  });
	}
	
	function updateMarkerPosition(latLng) {
		if(latLng.lat() == 0 && latLng.lng() == 0) return;
		
	  document.getElementById("pec_map_lnlat").value = [
		latLng.lat(),
		latLng.lng()
	  ].join(", ");
	}
	
	function updateMarkerAddress(str) {
	  document.getElementById("pec_map").value = str;
	}
	
	function initialize() {
	  var latLng = new google.maps.LatLng(0,0);
	  map = new google.maps.Map(document.getElementById("mapCanvas"), {
		zoom: 3,
		center: latLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	  });
	  marker = new google.maps.Marker({
		position: latLng,
		title: "Location",
		map: map,
		draggable: true
	  });
	  
	  // Update current position info.
	  updateMarkerPosition(latLng);
	  geocodePosition(latLng);
	  
	  // Add dragging event listeners.
	  google.maps.event.addListener(marker, "dragstart", function() {
		updateMarkerAddress("");
	  });
	  
	  google.maps.event.addListener(marker, "drag", function() {
		updateMarkerPosition(marker.getPosition());
	  });
	  
	  google.maps.event.addListener(marker, "dragend", function() {
		geocodePosition(marker.getPosition());
	  });
	  
	  if($('#pec_map').val() != "") {
		$('#pec_map').trigger('keyup');
	  }
	}
	
	var timeout;
	
	$('#pec_map').on('keyup', function () {
	  clearTimeout( timeout );
	  timeout = setTimeout(function() {
		  geocoder.geocode( { "address": $('#pec_map').val()}, function(results, status) {
			  if(status != "OVER_QUERY_LIMIT") {
				  var latlng = results[0].geometry.location;
				  marker.setPosition(latlng);
				  
				 // var listener = google.maps.event.addListener(map, "idle", function() { 
					  if (map.getZoom() < 12) map.setZoom(12); 
					  map.setCenter(latlng);
					  //google.maps.event.removeListener(listener); 
					//});
					
					updateMarkerPosition(latlng);
			  }
		 });
	 }, 1000);
	});
	
	// Onload handler to fire off the app.
	google.maps.event.addDomListener(window, "load", initialize);
	
	$overlay = $('<div>').addClass('dpProEventCalendar_Overlay').click(function(){
		hideOverlay();
	});
	
	$('#dp_ui_content select[multiple!="multiple"], #dpProEventCalendar_events_meta select[multiple!="multiple"]').not('.dp_manage_special_dates select, #custom_field_new select').selectric();
	
	if($("#dpProEventCalendar_SpecialDates").length) {
		var $specialDates = $("#dpProEventCalendar_SpecialDates");
		$specialDates.dialog({                   
			'dialogClass'   : 'wp-dialog',           
			'modal'         : true,
			'height'		: 220,
			'width'			: 400,
			'autoOpen'      : false, 
			'closeOnEscape' : true
		});
		
		jQuery('.btn_add_special_date').live('click', function(event) {
			$specialDates.dialog('open');
		});
	}
	
	if($("#dpProEventCalendar_SpecialDatesEdit").length) {
		var $specialDatesEdit = $("#dpProEventCalendar_SpecialDatesEdit");
		$specialDatesEdit.dialog({                   
			'dialogClass'   : 'wp-dialog',           
			'modal'         : true,
			'height'		: 220,
			'width'			: 400,
			'autoOpen'      : false, 
			'closeOnEscape' : true
		});
		
		jQuery('.btn_edit_special_date').live('click', function(event) {
			$('#dpPEC_special_id').val($(this).data('special-date-id'));
			$('#dpPEC_special_title').val($(this).data('special-date-title'));
			$('#dpPEC_special_color').val($(this).data('special-date-color'));
			$('#specialDate_colorSelector_Edit div').css('backgroundColor', $(this).data('special-date-color'));

			$specialDatesEdit.dialog('open');
		});
	}
	
	if($(".pec-load-more").length) {	
		
		$('.pec-load-more').click(function(event) {
			var $btn = $(this);
			
			$.post(ProEventCalendarAjax.ajaxurl, { offset: $(this).data('dppec-offset'), eventid: $(this).data('dppec-eventid'), action: 'getMoreBookings', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
				function(data) {
					$('#pec-booking-list').append(data);
					var total = $btn.find('span').text() - 30;
					
					if(total <= 0) {
						$btn.hide();
					} else {
						$btn.find('span').text(total);
					}
				}
			);	

		});
		
	}
	
	if($(".dpProEventCalendar_ModalCalendar").length) {	

		$('.dpProEventCalendar_btn_getDate').click(function(event) {

			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				
				hideOverlay();
				$('#dpProEventCalendar_default_date').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').die('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getFromDate').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				
				hideOverlay();
				$('#pec_custom_shortcode_from').val($(this).data('dppec-date'));
				pec_updateShortcode();
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').die('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getEventDate').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				
				hideOverlay();
				$('#pec_date').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').die('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getEventEndDate').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				
				hideOverlay();
				$('#pec_end_date').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').die('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getDateRangeStart').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				
				hideOverlay();
				$('#dpProEventCalendar_date_range_start').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').die('click');
				
			});
		});
		
		$('.dpProEventCalendar_btn_getDateRangeEnd').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				
				hideOverlay();
				$('#dpProEventCalendar_date_range_end').val($(this).data('dppec-date'));
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').die('click');
				
			});
		});
		
		$('.btn_manage_special_dates').click(function(event) {
			var nonce = $(this).data('calendar-nonce');
			var calendar = $(this).data('calendar-id');
			$('.dp_pec_wrapper', '.dpProEventCalendar_ModalCalendar').hide();
			$('#dp_pec_id'+nonce, '.dpProEventCalendar_ModalCalendar').show();
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				if($(this).data('sp_date_active')) { return false; }
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').data('sp_date_active', false);
				$(this).data('sp_date_active', true);
				
				$('.dp_pec_content', '.dpProEventCalendar_ModalCalendar').css({'overflow': 'visible'});
				
				$('.dp_manage_special_dates', '.dpProEventCalendar_ModalCalendar').slideUp('fast').parent().css('z-index', 2);
				$('.dp_manage_special_dates', this).slideDown('fast').parent().css('z-index', 3);
				
			});
			
			$('.dp_pec_date:not(.disabled) select', '.dpProEventCalendar_ModalCalendar').live('change', function() 
			{
			   changeSpecialDate($(this), calendar);
			});
		});
		
		$('#btn_manage_special_dates').click(function(event) {
			
			showOverlay();
			
			$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').live('click', function(event) {
				if($(this).data('sp_date_active')) { return false; }
				$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').data('sp_date_active', false);
				$(this).data('sp_date_active', true);
				
				$('.dp_pec_content', '.dpProEventCalendar_ModalCalendar').css({'overflow': 'visible'});
				
				$('.dp_manage_special_dates', '.dpProEventCalendar_ModalCalendar').slideUp('fast').parent().css('z-index', 2);
				$('.dp_manage_special_dates', this).slideDown('fast').parent().css('z-index', 3);
				
			});
			
			$('.dp_pec_date:not(.disabled) select', '.dpProEventCalendar_ModalCalendar').live('change', function() 
			{
			   changeSpecialDate($(this));
			});
		});
		
	}
	
	function changeSpecialDate(obj, calendar) {
		if($(obj).val() == '') { 
			$(obj).parent().parent().css('background-color', '#fff');
		} else {
			obj_arr = $(obj).val().split(',');
			var color = obj_arr[1];
			var sp = obj_arr[0];
			$(obj).parent().parent().css('background-color', color);
		}
		
		$.post(ProEventCalendarAjax.ajaxurl, { date: $(obj).parent().parent().data('dppec-date'), sp : sp, calendar: calendar, action: 'setSpecialDates', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
			function(data) {

			}
		);	
	}
	
	function showOverlay() {
		if($(".dpProEventCalendar_Overlay").length) {
			$($overlay).fadeIn('fast');
		} else {
			$('body').append($overlay);
		}
		$(".dpProEventCalendar_ModalCalendar").css({ display: 'none', visibility: 'visible' }).fadeIn('fast');	
	}
	
	function hideOverlay() {
		$(".dpProEventCalendar_ModalCalendar").fadeOut('fast', function() { $(this).css({ display: 'block', visibility: 'hidden' }) } );	
		$(".dpProEventCalendar_Overlay").fadeOut('fast');
		
		$('.dp_manage_special_dates', '.dpProEventCalendar_ModalCalendar').slideUp('fast').parent().css('z-index', 2);
		$('.dp_pec_date:not(.disabled)', '.dpProEventCalendar_ModalCalendar').die('click');	
		$('.dp_pec_date:not(.disabled) select', '.dpProEventCalendar_ModalCalendar').die('change');	
	}
			
}); 

function pec_removeBooking(booking_id, parent_el) {
	jQuery(parent_el).closest('tr').css('opacity', .6);
	jQuery.post(ProEventCalendarAjax.ajaxurl, { booking_id: booking_id, action: 'removeBooking', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
		function(data) {
			jQuery(parent_el).closest('tr').remove();
		}
	);	
}
 