/*
 * jQuery DP Pro Event Calendar v1.2.1
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: http://www.dpereyra.com
 * @Email: info@dpereyra.com
 *
 * Depends:
 * jquery.js
 */
  
(function ($) {
	function DPProEventCalendar(element, options) {
		this.calendar = $(element);
		this.eventDates = $('.dp_pec_date', this.calendar);
		
		/* Setting vars*/
		this.settings = $.extend({}, $.fn.dpProEventCalendar.defaults, options); 
		this.no_draggable = false,
		this.hasTouch = false,
		this.downEvent = "mousedown.rs",
		this.moveEvent = "mousemove.rs",
		this.upEvent = "mouseup.rs",
		this.cancelEvent = 'mouseup.rs',
		this.isDragging = false,
		this.successfullyDragged = false,
		this.view = "monthly",
		this.monthlyView = "calendar",
		this.type = 'calendar',
		this.defaultDate = 0,
		this.startTime = 0,
		this.startMouseX = 0,
		this.startMouseY = 0,
		this.currentDragPosition = 0,
		this.lastDragPosition = 0,
		this.accelerationX = 0,
		this.tx = 0;
		
		// Touch support
		if("ontouchstart" in window) {
					
			this.hasTouch = true;
			this.downEvent = "touchstart.rs";
			this.moveEvent = "touchmove.rs";
			this.upEvent = "touchend.rs";
			this.cancelEvent = 'touchcancel.rs';
		} 
		
		this.init();
	}
	
	DPProEventCalendar.prototype = {
		init : function(){
			var instance = this;
					
			instance.view = instance.settings.view;
			instance.defaultDate = instance.settings.defaultDate;
			
			if(instance.settings.type == 'compact') {
				instance.settings.skin = 'light';
			}

			$(instance.calendar).not('.dp_pec_compact_wrapper').addClass(instance.settings.skin);
			instance._makeResponsive();
			
			if($('.pec_upcoming_layout', instance.calendar).length) {

				var $grid = $('.pec_upcoming_layout', instance.calendar).isotope({
				  itemSelector: '.dp_pec_date_event_wrap'
				});

			}
			
			$(instance.calendar).on('click', '.prev_month', function(e) { instance._prevMonth(instance); });
			if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.prev_month', instance.calendar).hide();
			}
			
			$(instance.calendar).on('click', '.next_month', function(e) { instance._nextMonth(instance); });
			if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.next_month', instance.calendar).hide();
			}
			
			$('.prev_day', instance.calendar).click(function(e) { instance._prevDay(instance); });
			$('.next_day', instance.calendar).click(function(e) { instance._nextDay(instance); });
			
			$('.prev_week', instance.calendar).click(function(e) { instance._prevWeek(instance); });
			$('.next_week', instance.calendar).click(function(e) { instance._nextWeek(instance); });
						
			if(instance.settings.type == "add-event") {
				$('.dp_pec_new_event_wrapper select').selectric();
				$('.dp_pec_new_event_wrapper input.checkbox').iCheck({
					checkboxClass: 'icheckbox_flat',
					radioClass: 'iradio_flat',
					increaseArea: '20%' // optional
				});
			}
			
			if(instance.settings.type == "grid-upcoming") {
				$(instance.calendar).on('click', '.dp_pec_grid_text_wrap', function() {
					
					//$('.dp_pec_grid_link_image', $(this).closest('.dp_pec_grid_event')).attr('target', '_blank');
					$('.dp_pec_grid_link_image', $(this).closest('.dp_pec_grid_event'))[0].click();
					
				})	
			}
			
			$(instance.calendar).on('click', '.dp_pec_event_description_more', function(e) {
				$(this).closest('.dp_pec_event_description_short').hide();
				$(this).closest('.dp_pec_event_description').find('.dp_pec_event_description_full').show();
				
				if(typeof $grid != "undefined") {
					$grid.isotope('layout');
				}
			});
			
			/* touch support */
			if(instance.settings.draggable && instance.settings.type != "accordion" && instance.settings.type != "accordion-upcoming" && instance.settings.type != "add-event" && instance.settings.type != "bookings-user" && instance.settings.type != "past") {
				$('.dp_pec_content', instance.calendar).addClass('isDraggable');
				$('.dp_pec_content', instance.calendar).bind(instance.downEvent, function(e) { 	

					if(!instance.no_draggable) {
						instance.startDrag(e); 	
					} else if(!instance.hasTouch) {							
						e.preventDefault();
					}								
				});	
			}
			
			if(!instance.settings.isAdmin) {
				if(!$.proCalendar_isVersion('1.7')) {
					$(instance.calendar).on({
						mouseenter:
						   function(e)
						   {
							   
							   if(!$('.eventsPreviewDiv').length) {
									$('body').append($('<div />').addClass('eventsPreviewDiv'));
							   }
							  
							   $('.eventsPreviewDiv').removeClass('light dark').addClass(instance.settings.skin);
							   
								$('.eventsPreviewDiv').html($('.eventsPreview', $(this)).html());
								
								$(this).on({
									mouseenter:
										function(e)
										{
											$('.eventsPreviewDiv ul li').hide();
											$(".eventsPreviewDiv ul").find("li[data-dppec-event='" + $(e.target).data('dppec-event') + "']").addClass('dp_pec_preview_event').show();

											if($('.eventsPreviewDiv').html() != "") {
												$('.eventsPreviewDiv').show();
											}
										},
									mouseleave:
										function() 
										{
											$('.eventsPreviewDiv ul li').removeClass('dp_pec_preview_event').show();
											$('.eventsPreviewDiv').hide();
										}
								}, '.dp_daily_event:not(.dp_daily_event_show_more)');
								
								$(this).on({
									mouseenter:
										function(e)
										{
											$('.eventsPreviewDiv ul li').hide();
											$(".eventsPreviewDiv ul li:gt(2)").addClass('dp_pec_preview_event').show();

											if($('.eventsPreviewDiv').html() != "") {
												$('.eventsPreviewDiv').show();
											}
										},
									mouseleave:
										function() 
										{
											$('.eventsPreviewDiv ul li').removeClass('dp_pec_preview_event').show();
											$('.eventsPreviewDiv').hide();
										}
								}, '.dp_daily_event.dp_daily_event_show_more');
								
								
								if($('.eventsPreviewDiv').html() != "" && !$('.dp_daily_event', instance.calendar).length) {
									$('.eventsPreviewDiv').show();
								}
								
								$('.eventsPreviewDiv ul li').removeClass('dp_pec_preview_event').show();
								
						   },
						mouseleave:
						   function()
						   {
								$('.eventsPreviewDiv').html('').hide();
						   }
					   }, '.dp_pec_date:not(.disabled)'
					).bind('mousemove', function(e){
							
						if($('.eventsPreviewDiv').html() != "") {
							var body_pos = $("body").css('position');
							if(body_pos == "relative") {
								$("body").css('position', 'static');
							}
							$('.eventsPreviewDiv').removeClass('previewRight');
							
							var position = $(e.target).closest('.dp_pec_date').offset();
							var target_height = $(e.target).closest('.dp_pec_date').height();
							if(typeof position != "undefined") {
								$('.eventsPreviewDiv').css({
									left: position.left,
									top: position.top,
									marginTop: (target_height + 12) + "px",
									marginLeft: (position.left + $('.eventsPreviewDiv').outerWidth() >= $( window ).width() ? -($('.eventsPreviewDiv').outerWidth() - 30) + "px" : 0)
								});
							}
							
							if(position && position.left + $('.eventsPreviewDiv').outerWidth() >= $( window ).width()) {
								$('.eventsPreviewDiv').addClass('previewRight');
							}
						}
					});
					
				} else {
					$('.dp_pec_date:not(.disabled)', instance.calendar).live({
						mouseenter:
						   function(e)
						   {
							   if(!$('.eventsPreviewDiv').length) {
									$('body').append($('<div />').addClass('eventsPreviewDiv'));
							   }
							  
							   $('.eventsPreviewDiv').removeClass('light dark').addClass(instance.settings.skin);
							   
								$('.eventsPreviewDiv').html($('.eventsPreview', $(this)).html());
								
								if($('.eventsPreviewDiv').html() != "") {
									$('.eventsPreviewDiv').show();
								}
								
								if($('.eventsPreviewDiv').html() != "") {
									var body_pos = $("body").css('position');
									if(body_pos == "relative") {
										$("body").css('position', 'static');
									}
									$('.eventsPreviewDiv').removeClass('previewRight');
									
									var position = $(e.target).closest('.dp_pec_date').offset();
									var target_height = $(e.target).closest('.dp_pec_date').height();
									if(typeof position != "undefined") {
										$('.eventsPreviewDiv').css({
											left: position.left,
											top: position.top,
											marginTop: (target_height + 12) + "px",
											marginLeft: (position.left + $('.eventsPreviewDiv').outerWidth() >= $( window ).width() ? -($('.eventsPreviewDiv').outerWidth() - 30) + "px" : 0)
										});
									}
									
									if(position.left + $('.eventsPreviewDiv').outerWidth() >= $( window ).width()) {
										$('.eventsPreviewDiv').addClass('previewRight');
									}
								}
						   },
						mouseleave:
						   function()
						   {
								$('.eventsPreviewDiv').html('').hide();
						   }
					   }
					);
				}

				if(!$.proCalendar_isVersion('1.7')) {
					$(instance.calendar).on('mouseup', '.dp_pec_date:not(.disabled)', function(event) {
						if($(event.target).hasClass('dp_daily_event') && !$(event.target).hasClass('dp_daily_event_show_more')) { return; }
						
						if(instance.calendar.hasClass('dp_pec_daily')) { return; }
						if(instance.calendar.hasClass('dp_pec_weekly')) { return; }
						
						if(instance.settings.event_id != '' && $('.dp_pec_form_desc').length) {
							if( !$(this).find('.dp_book_event_radio').length ) {
								return;	
							}
							
							$('.dp_book_event_radio', instance.calendar).removeClass('dp_book_event_radio_checked');
							$(this).find('.dp_book_event_radio').addClass('dp_book_event_radio_checked');
							$('#pec_event_page_book_date', '.dpProEventCalendarModal').val($(this).data('dppec-date'));
							
							return;
						}
						
						if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && (event.which === 1 || event.which === 0)) {
							
							instance._removeElements();
							
							$.post(ProEventCalendarAjax.ajaxurl, { date: $(this).data('dppec-date'), calendar: instance.settings.calendar, category: $('select.pec_categories_list', instance.calendar).val(), event_id: instance.settings.event_id, author: instance.settings.author, action: 'getEvents', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
								function(data) {
	
									$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
									
									instance.eventDates = $('.dp_pec_date', instance.calendar);
									
									$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
								}
							);	
						}
		
					});
					
					$(instance.calendar).on('mouseup', '.dp_daily_event:not(.dp_daily_event_show_more)', function(event) {
						if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && (event.which === 1 || event.which === 0)) {
							
							if($(this).data('dppec-event-url') != "") {
								
								event.preventDefault();
								location.href = $(this).data('dppec-event-url');
								return false;
								
							} else {
								
								instance._removeElements();
							
								$.post(ProEventCalendarAjax.ajaxurl, { event: $(this).data('dppec-event'), calendar: instance.settings.calendar, date:$(this).data('dppec-date'),  action: 'getEvent', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
									function(data) {
		
										$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
										
										instance.eventDates = $('.dp_pec_date', instance.calendar);
										
										$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
									}
								);	

							}
						}
					});
					
				} else {
					$('.dp_pec_date:not(.disabled)', instance.calendar).live('mouseup', function(event) {
						
						if(instance.calendar.hasClass('dp_pec_daily')) { return; }
						
						if(instance.calendar.hasClass('dp_pec_weekly')) { return; }
						
						if(instance.settings.event_id != '' && $('.dp_pec_form_desc').length && $(this).find('.dp_count_events').length()) {
							if( !$(this).find('.dp_count_events').length ) {
								return;	
							}
							$('.dp_book_event_radio', instance.calendar).removeClass('dp_book_event_radio_checked');
							$(this).find('.dp_book_event_radio').addClass('dp_book_event_radio_checked');
							$('#pec_event_page_book_date', '.dpProEventCalendarModal').val($(this).data('dppec-date'));
							$('.pec_event_page_send_booking', '.dpProEventCalendarModal').prop('disabled', false).removeClass('dp_pec_disabled');
							
							return;
						}
						
						if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && (event.which === 1 || event.which === 0)) {
						
							$.post(ProEventCalendarAjax.ajaxurl, { date: $(this).data('dppec-date'), calendar: instance.settings.calendar, category: $('select.pec_categories_list', instance.calendar).val(), event_id: instance.settings.event_id, author: instance.settings.author, action: 'getEvents', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
								function(data) {
	
									$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
									
									instance.eventDates = $('.dp_pec_date', instance.calendar);
									
									$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
								}
							);	

						}
		
					});
					
					$('.dp_daily_event:not(.dp_daily_event_show_more)', instance.calendar).live('mouseup', function(event) {
						if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && (event.which === 1 || event.which === 0)) {
							
							if($(this).data('dppec-event-url') != "") {
								
								event.preventDefault();
								location.href = $(this).data('dppec-event-url');
								return false;
								
							} else {
							
								instance._removeElements();
								
								$.post(ProEventCalendarAjax.ajaxurl, { event: $(this).data('dppec-event'), date: $(this).data('dppec-date'), calendar: instance.settings.calendar, action: 'getEvent', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
									function(data) {
		
										$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
										
										instance.eventDates = $('.dp_pec_date', instance.calendar);
										
										$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
									}
								);	
							}
						}
					});
				}
			}
			
			if(!$.proCalendar_isVersion('1.7')) {
				$(instance.calendar).on('click', '.dp_pec_date_event_back', function(event) {
					event.preventDefault();
					instance._removeElements();
					
					instance._changeLayout();
				});
			} else {
				$('.dp_pec_date_event_back', instance.calendar).live('click', function(event) {
					event.preventDefault();
					instance._removeElements();
					
					instance._changeLayout();
				});
			}
			
			$(instance.calendar).on({
				'mouseenter': function(i) {

					$('.dp_pec_user_rate li a').addClass('is-off');
	
					for(var x = $(this).data('rate-val'); x > 0; x--) {
						$('.dp_pec_user_rate li a[data-rate-val="'+x+'"]', instance.calendar).removeClass('is-off').addClass('is-on');
					}

				},
				'mouseleave': function() {
					$('.dp_pec_user_rate li a', instance.calendar).removeClass('is-on');
					$('.dp_pec_user_rate li a', instance.calendar).removeClass('is-off');
				},
				'click': function() {
					
					$('.dp_pec_user_rate', instance.calendar).replaceWith($('<div>').addClass('dp_pec_loading').attr({ id: 'dp_pec_loading_rating' }));
					
					jQuery.post(ProEventCalendarAjax.ajaxurl, { 
							event_id: $(this).data('event-id'), 
							rate: $(this).data('rate-val'), 
							calendar: instance.settings.calendar,
							action: 'ProEventCalendar_RateEvent', 
							postEventsNonce : ProEventCalendarAjax.postEventsNonce 
						},
						function(data) {
							$('#dp_pec_loading_rating', instance.calendar).replaceWith(data);
						}
					);	
				}
			}, '.dp_pec_user_rate li a');
			
			$(document).on('click', '.dpProEventCalendar_close_modal_btn', function(e) {
			
				$('.dpProEventCalendarModal, .dpProEventCalendarOverlay').fadeOut('fast');
				
			});
			
			$('.dpProEventCalendar_subscribe', instance.calendar).click(function(e) {
				e.preventDefault();

				var mailform = '<h3>'+instance.settings.lang_subscribe_subtitle+'</h3><form>';
				mailform += '<div class="dpProEventCalendar_success">'+instance.settings.lang_txt_subscribe_thanks+'</div>';
				mailform += '<div class="dpProEventCalendar_error"><i class="fa fa-exclamation"></i>'+instance.settings.lang_fields_required+'</div>';
				mailform += '<div class="clear-article-share"></div>';
				mailform += '<input type="text" name="your_name" id="dpProEventCalendar_your_name" class="dpProEventCalendar_input dpProEventCalendar_from_name" placeholder="'+instance.settings.lang_your_name+'" />';
				mailform += '<input type="text" name="your_email" id="dpProEventCalendar_your_email" class="dpProEventCalendar_input dpProEventCalendar_from_email" placeholder="'+instance.settings.lang_your_email+'" />';
				if(instance.settings.recaptcha_enable && instance.settings.recaptcha_site_key != "") {
					mailform += '<div class="dp_pec_clear"></div><div id="pec_subscribe_captcha"></div>';
				}
				mailform += '<div class="dp_pec_clear"></div><input type="button" class="dpProEventCalendar_send dpProEventCalendar_action" name="" value="'+instance.settings.lang_subscribe+'" />';
				mailform += '<span class="dpProEventCalendar_sending_email"></span>';
				mailform += '</form>';
				
				if(!$('.dpProEventCalendarModal').length) {
					$('body').append(
						$('<div>').addClass('dpProEventCalendarModal').prepend(
							$('<h2>').text(instance.settings.lang_subscribe).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': 'javascript:void(0);' }).html('<i class="fa fa-times"></i>').click(function() { 
									$('.dpProEventCalendarModal, .dpProEventCalendarOverlay').fadeOut('fast')
								})
							)
						).append(
							$('<div>').addClass('dpProEventCalendar_mailform').html(mailform)
						).show()
					);
					
					$('.dpProEventCalendarOverlay').show();
					
					//dpShareLoadEvents();
				} else {
					
					$('.dpProEventCalendarModal .pec_book_select_date, .dpProEventCalendarModal .dpProEventCalendar_mailform').remove();
					
					$('.dpProEventCalendarModal').append(
						$('<div>').addClass('dpProEventCalendar_mailform').html(mailform)
					)

					$('.dpProEventCalendarModal, .dpProEventCalendarOverlay').removeAttr('style').show();

				}
				
				if(instance.settings.recaptcha_enable && instance.settings.recaptcha_site_key != "") {
					var pec_subscribe_captcha;
					pec_subscribe_captcha = grecaptcha.render('pec_subscribe_captcha', {
					  'sitekey' : instance.settings.recaptcha_site_key
					});
				}
				
				$('.dpProEventCalendar_send', '.dpProEventCalendarModal').click(function(e) {
					e.preventDefault();
					$(this).prop('disabled', true);
					$('.dpProEventCalendar_sending_email', '.dpProEventCalendarModal').css('display', 'inline-block');
					
					if( $('#dpProEventCalendar_your_name', '.dpProEventCalendarModal').val() != ""
						&& $('#dpProEventCalendar_your_email', '.dpProEventCalendarModal').val() != "") {
						
						var post_obj = {
							your_name: $('#dpProEventCalendar_your_name', '.dpProEventCalendarModal').val(), 
							your_email: $('#dpProEventCalendar_your_email', '.dpProEventCalendarModal').val(),
							calendar: instance.settings.calendar,
							action: 'ProEventCalendar_NewSubscriber', 
							postEventsNonce : ProEventCalendarAjax.postEventsNonce 
						}
						
						if(instance.settings.recaptcha_enable && instance.settings.recaptcha_site_key != "") {
							post_obj.grecaptcharesponse = grecaptcha.getResponse(pec_subscribe_captcha);
						}
						
						jQuery.post(ProEventCalendarAjax.ajaxurl, post_obj,
							function(data) {
								$('.dpProEventCalendar_send', '.dpProEventCalendarModal').prop('disabled', false);
								$('.dpProEventCalendar_sending_email', '.dpProEventCalendarModal').hide();
								
								$('.dpProEventCalendar_success, .dpProEventCalendar_error').hide();
								$('.dpProEventCalendar_success').css('display', 'inline-block');
								$('form', '.dpProEventCalendarModal')[0].reset();
							}
						);	
					} else {
						$(this).prop('disabled', false);
						$('.dpProEventCalendar_sending_email', '.dpProEventCalendarModal').hide();
						
						$('.dpProEventCalendar_success, .dpProEventCalendar_error').hide();
						$('.dpProEventCalendar_error').css('display', 'inline-block');
					}
				});
				
				$("input, textarea", '.dpProEventCalendarModal').placeholder();
				
				
			});
			
			$('.dp_pec_references', instance.calendar).click(function(e) {
				e.preventDefault();
				if(!$(this).hasClass('active')) {
					$(this).addClass('active');
					$('.dp_pec_references_div', instance.calendar).slideDown('fast');
				} else {
					$(this).removeClass('active');
					$('.dp_pec_references_div', instance.calendar).slideUp('fast');
				}
				
			});
			
			if(instance.monthlyView == "calendar") {
				var dppec_date = $('.dp_pec_content', instance.calendar).find(".dp_pec_date[data-dppec-date='" + instance.settings.defaultDateFormat + "']");
				
				if(typeof dppec_date.attr('style') == 'undefined' && instance.settings.show_current_date) {
					dppec_date.css('background-color', instance.settings.current_date_color).addClass('dp_pec_special_date');
				}
			}
			
			$('.dp_pec_view_all', instance.calendar).click(function(event) {
				event.preventDefault();

				if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && (event.which === 1 || event.which === 0)) {
					if(instance.monthlyView == "calendar") {
						$(this).addClass('active');
						instance.monthlyView = "list";
					} else {
						$(this).removeClass('active');
						instance.monthlyView = "calendar";
					}
					
					instance._changeMonth();
					
				}
			});
			
			if(instance.settings.selectric) {
				$('.dp_pec_layout select, .dp_pec_add_form select, .dp_pec_nav select', instance.calendar).selectric();
			}
						
			if(instance.view == "monthly-all-events" && instance.settings.type != "accordion" && instance.settings.type != "accordion-upcoming" && instance.settings.type != "add-event" && instance.settings.type != "list-author" && instance.settings.type != "grid" && instance.settings.type != "grid-upcoming" && instance.settings.type != "today-events" && instance.settings.type != "bookings-user" && instance.settings.type != "past") {
				$('.dp_pec_view_all', instance.calendar).addClass('active');
				instance.monthlyView = "list";
				
				instance._changeMonth();
			}

			$('.dp_pec_references_close', instance.calendar).click(function(e) {
				e.preventDefault();
				$('.dp_pec_references', instance.calendar).removeClass('active');
				$('.dp_pec_references_div', instance.calendar).slideUp('fast');
			});
			
			$('.dp_pec_search', instance.calendar).one('click', function(event) {
				$(this).val("");
			});
			
			if($('.dp_pec_accordion_event', instance.calendar).length) {
				$(instance.calendar).on('click', '.dp_pec_accordion_event', function(e) {

					if(!$(this).hasClass('visible')) {
						if(e.target.className != "dp_pec_date_event_close" && e.target.className != "fa fa-close") {
							$('.dp_pec_accordion_event').removeClass('visible');
							$(this).addClass('visible');

							if(typeof $grid != "undefined") {
								$grid.isotope('layout');
							}
						}
					} else {
						//$(this).removeClass('visible');
					}
				});
				
				$(instance.calendar).on('click', '.dp_pec_date_event_close', function(e) {
					
					$('.dp_pec_accordion_event', instance.calendar).removeClass('visible');
					
					if(typeof $grid != "undefined") {
						$grid.isotope('layout');
					}

				});
			}
			
			if($('.dp_pec_view_action', instance.calendar).length) {
				$('.dp_pec_view_action', instance.calendar).click(function(e) {
					e.preventDefault();
					$('.dp_pec_view_action', instance.calendar).removeClass('active');
					$(this).addClass('active');
					
					if(instance.view != $(this).data('pec-view')) {
						instance.view = $(this).data('pec-view');
						
						instance._changeLayout();
					}
				});
			}
			
			if($('.dp_pec_clear_end_date', instance.calendar).length) {
				$('.dp_pec_clear_end_date', instance.calendar).click(function(e) {
					e.preventDefault();
					$('.dp_pec_end_date_input', instance.calendar).val('');
				});
				
			}
			
			if($('.dp_pec_add_event', instance.calendar).length) {
				$('.dp_pec_add_event', instance.calendar).click(function(e) {
					e.preventDefault();
					$(this).hide();
					$('.dp_pec_cancel_event', instance.calendar).show();
					
					$('.dp_pec_add_form', instance.calendar).slideDown('fast');
					
					
					
				});
			}
			
			jQuery("#pec_map_address", instance.calendar).on('focus', function () {
				$('.map_lnlat_wrap', instance.calendar).show();
				
				jQuery("#pec_map_address", instance.calendar).off('focus');
				
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
				  jQuery("#map_lnlat", instance.calendar).val([
					latLng.lat(),
					latLng.lng()
				  ].join(", "));
				}
				
				function updateMarkerAddress(str) {
				  jQuery("#pec_map_address", instance.calendar).val(str);
				}
				
				function pec_map_initialize() {

				  var latLng = new google.maps.LatLng(0,0);
				  map = new google.maps.Map(jQuery("#pec_mapCanvas", instance.calendar)[0], {
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
				}
				
				var timeout;

				jQuery("#pec_map_address", instance.calendar).on('keyup', function () {
				  clearTimeout( timeout );
				  timeout = setTimeout(function() {
					  geocoder.geocode( { "address": jQuery("#pec_map_address", instance.calendar).val()}, function(results, status) {
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
				pec_map_initialize();
				//google.maps.event.addDomListener(window, "load", pec_map_initialize);
			});
			
			if($('.dp_pec_cancel_event', instance.calendar).length) {
				$('.dp_pec_cancel_event', instance.calendar).click(function(e) {
					e.preventDefault();
					$(this).hide();
					$('.dp_pec_add_event', instance.calendar).show();
					
					$('.dp_pec_add_form', instance.calendar).slideUp('fast');
					$('.dp_pec_notification_event_succesfull', instance.calendar).hide();
					
				});
			}
			
			if($('.event_image', instance.calendar).length) {

				$(instance.calendar).on('change', '.event_image', function() 
				{
					$('#event_image_lbl', $(this).parent()).val($(this).val().replace(/^.*[\\\/]/, ''));
				});

			}
			
			//if($('.pec_edit_event', instance.calendar).length) {
				$(instance.calendar).on('click', '.pec_edit_event', function(e) {
					
					if(!$('.dpProEventCalendarModalEditEvent').length) {
				
						$('body').append(
							$('<div>').addClass('dpProEventCalendarModalEditEvent dp_pec_new_event_wrapper').prepend(
								$('<h2>').text(instance.settings.lang_edit_event).append(
									$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': 'javascript:void(0);' }).html('<i class="fa fa-times"></i>').click(function() { 
										$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarOverlay').fadeOut('fast')
									})
								)
							).append(
								$('<div>').addClass('dpProEventCalendar_eventform').append($(this).next().children().clone(true))
							).show()
						);
						
						$('.dpProEventCalendarOverlay').show();
						
						$('.dpProEventCalendar_eventform').html($('.dpProEventCalendar_eventform').html().replace(/_pecremoveedit/g, ''));
						
						$('.dpProEventCalendarModalEditEvent').on('change', '.event_image', function() 
						{
							$('#event_image_lbl', $(this).parent()).val($(this).val().replace(/^.*[\\\/]/, ''));
						});
							
						submit_event_hook('.dpProEventCalendarModalEditEvent');
					} else {
						$('.dpProEventCalendar_eventform').html($(this).next().html().replace(/_pecremoveedit/g, ''));
						$('.dpProEventCalendarModalEditEvent').removeClass('dpProEventCalendarModalSmall');
						$('.dpProEventCalendarModalEditEvent h2').text(instance.settings.lang_edit_event);
						$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarOverlay').show();
					}
					
					$('.dpProEventCalendarModalEditEvent select').selectric();
					$('.dpProEventCalendarModalEditEvent input.checkbox').iCheck({
						checkboxClass: 'icheckbox_flat',
						radioClass: 'iradio_flat',
						increaseArea: '20%' // optional
					});
					
				});
			//}
			
			$(instance.calendar).on('click', '.pec_remove_event', function(e) {
					
				if(!$('.dpProEventCalendarModalEditEvent').length) {
			
					$('body').append(
						$('<div>').addClass('dpProEventCalendarModalEditEvent dpProEventCalendarModalSmall dp_pec_new_event_wrapper').prepend(
							$('<h2>').text(instance.settings.lang_remove_event).append(
								$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': 'javascript:void(0);' }).html('<i class="fa fa-times"></i>').click(function() { 
									$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarOverlay').fadeOut('fast')
								})
							)
						).append(
							$('<div>').addClass('dpProEventCalendar_eventform').append($(this).next().children().clone(true))
						).show()
					);
					
					$('.dpProEventCalendarOverlay').show();
					
				} else {
					$('.dpProEventCalendar_eventform').html($(this).next().html());
					$('.dpProEventCalendarModalEditEvent').addClass('dpProEventCalendarModalSmall');
					$('.dpProEventCalendarModalEditEvent h2').text(instance.settings.lang_remove_event);
					$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarOverlay').show();
				}
				
				$('.dpProEventCalendarModalEditEvent').on('click', '.dp_pec_remove_event', function(e) {
						e.preventDefault();
						$(this).addClass('dp_pec_disabled');
						var form = $(this).closest(".add_new_event_form");
						
						var origName = $(this).html();
						$(this).html(instance.settings.lang_sending);
						var me = this;
						var form = $(this).closest('form');
						var post_obj = {
							calendar: instance.settings.calendar, 
							action: 'removeEvent',
							postEventsNonce : ProEventCalendarAjax.postEventsNonce
						}
	
						$(this).closest(".add_new_event_form").ajaxForm({
							url: ProEventCalendarAjax.ajaxurl,
							data: post_obj,
							success:function(data){
								$(me).html(origName);
								location.reload();	

								$(me).removeClass('dp_pec_disabled');

							}
						}).submit();
					});		
				
			});
			
			function pec_createWindowNotification(text) {
				if(!$('.dpProEventCalendar_windowNotification').length) {
					$('body').append(
						$('<div>').addClass('dpProEventCalendar_windowNotification').text(text).show()
					);
				} else {
					$('.dpProEventCalendar_windowNotification').removeClass('fadeOutDown').text(text).show();
				}
				
				setTimeout(function() { $('.dpProEventCalendar_windowNotification').addClass('fadeOutDown'); }, 3000)
			}
			
			//if($('.dp_pec_submit_event', instance.calendar).length) {
				//$([instance.calendar, '.dpProEventCalendarModalEditEvent']).each(function() {
				function submit_event_hook(el) {
					$(el).on('click', '.dp_pec_submit_event', function(e) {
						e.preventDefault();
						if(typeof tinyMCE != "undefined") {
							tinyMCE.triggerSave();
						}
						
						$(this).addClass('dp_pec_disabled');
						var form = $(this).closest(".add_new_event_form");
						
						var origName = $(this).html();
						$(this).html(instance.settings.lang_sending);
						var me = this;
						var form = $(this).closest('form');
						var post_obj = {
							calendar: instance.settings.calendar, 
							action: 'submitEvent',
							postEventsNonce : ProEventCalendarAjax.postEventsNonce
						}
	
						if(instance.settings.type == "add-event") {
							$('.events_loading', form).show();
							form.fadeTo('fast', .5);
						}
						
						if($('.dp_pec_form_title', form).val() == "") {
							$(me).html(origName);
							$(me).removeClass('dp_pec_disabled');
							
							$('.dp_pec_form_title', form).addClass('dp_pec_validation_error');
							
							if(instance.settings.type == "add-event") {
								$('.events_loading', form).hide();
								form.fadeTo('fast', 1);
							}
							
							return;
						}
						/*
						$.ajax({
							type 	: 'POST',
							url  	: ProEventCalendarAjax.ajaxurl, 
							data	: $(form).serialize() + '&' + $.param(post_obj),
							success	: function(data, status) {
								$(me).html(origName);
								$(form)[0].reset();
								$('.dp_pec_form_title', instance.calendar).removeClass('dp_pec_validation_error');
								$(me).removeClass('dp_pec_disabled');
								$('.dp_pec_notification_event_succesfull', instance.calendar).show();
								if(instance.settings.type == "add-event") {
									$('.events_loading', instance.calendar).hide();
									form.fadeTo('fast', 1);
								}
	
							}
						});*/	
	
						$(this).closest(".add_new_event_form").ajaxForm({
							url: ProEventCalendarAjax.ajaxurl,
							data: post_obj,
							success:function(data){
								$(me).html(origName);
								if(!form.hasClass('edit_event_form')) {
									$(form)[0].reset();
								} else {
									location.reload();	
								}
								$('.dp_pec_form_title', form).removeClass('dp_pec_validation_error');
								$(me).removeClass('dp_pec_disabled');
								$('.dp_pec_notification_event_succesfull', form.parent()).show();
								if(instance.settings.type == "add-event") {
									$('.events_loading', form).hide();
									form.fadeTo('fast', 1);
								}
							}
						}).submit();
					});		
				}
				submit_event_hook(instance.calendar);
				//});
			//}
			
			$('.dp_pec_search_form', instance.calendar).submit(function() {
				if($(this).find('.dp_pec_search').val() != "" && !$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
					instance._removeElements();
					
					$.post(ProEventCalendarAjax.ajaxurl, { key: $(this).find('.dp_pec_search').val(), calendar: instance.settings.calendar, author: instance.settings.author, action: 'getSearchResults', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
						function(data) {
							
							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
							
							instance.eventDates = $('.dp_pec_date', instance.calendar);
							
							$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
						}
					);	
				}
				return false;
			});
			
			$('.dp_pec_icon_search', instance.calendar).click(function() {
				if($(this).parent().find('.dp_pec_content_search_input').val() != "" && !$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
					instance._removeElements();
					var results_lang = $(this).data('results_lang');
					$('.events_loading', instance.calendar).show();
					
					$.post(ProEventCalendarAjax.ajaxurl, { key: $(this).parent().find('.dp_pec_content_search_input').val(), type: 'accordion', calendar: instance.settings.calendar, author: instance.settings.author, action: 'getSearchResults', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
						function(data) {
							
							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).html(data);
							$('.actual_month', instance.calendar).text(results_lang);
							$('.return_layout', instance.calendar).show();
							$('.month_arrows', instance.calendar).hide();
							$('.events_loading', instance.calendar).hide();
							//.empty();
							
						}
					);	
				}
				return false;
			});
			
			$('.return_layout', instance.calendar).click(function() {
				$(this).hide();
				$('.month_arrows', instance.calendar).show();
				$('.dp_pec_content_search_input', instance.calendar).val('');
				
				instance._changeMonth();
			});
			
			$(instance.calendar).on('click', '.dpProEventCalendar_load_more', function() {
				
				var items = $(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head,.dp_pec_date_block_wrap):hidden').slice(0,$(this).data('pagination'));
				
				items.show();
				
				$(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head):visible:last').prevAll('.dp_pec_date_block_wrap').show();	
				
				/*$.each($(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head)'), function(index) {
					
					if($(this).is(':visible')) {
						$(this).prevAll('.dp_pec_date_block_wrap').show();	
					}
				});*/
				
				if($(this).data('total') <= $(this).parent().find('.dp_pec_isotope:not(.dp_pec_date_event_head,.dp_pec_date_block_wrap):visible').length) {
					$(this).hide();
				}
				
				if(typeof $grid != "undefined") {
					$grid.isotope('layout');
				}
				
			});
			
			$('.dp_pec_content_search_input', instance.calendar).keyup(function (e) {
				if (e.keyCode == 13) {
					// Do something
					$('.dp_pec_icon_search', instance.calendar).trigger('click');
				}
			});
			
			$('.pec_categories_list', instance.calendar).on('change', function() {
				/*if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' ) && $(this).val() != "") {
					instance._removeElements();
					
					$.post(ProEventCalendarAjax.ajaxurl, { key: $(this).val(), calendar: instance.settings.calendar, action: 'getCategoryResults', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
						function(data) {
							
							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
							$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');
							
							instance.eventDates = $('.dp_pec_date', instance.calendar);
							
							$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
						}
					);	
				} else {*/
					$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');
					instance._changeMonth();
				//}
				return false;
			});
			
			$('.dp_pec_nav select.pec_switch_year', instance.calendar).on('change', function() {
				$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');
				instance.settings.actualYear = $(this).val();
				instance._changeMonth();
				return false;
			});
			
			$('.dp_pec_nav select.pec_switch_month', instance.calendar).on('change', function() {
				$('.dp_pec_search_form', instance.calendar).find('.dp_pec_search').val('');

				var changed_month = $(this).val();
				if(changed_month.indexOf('-') !== -1) {
					var changed_month_split = changed_month.split('-');
					instance.settings.actualYear = parseInt(changed_month_split[1], 10);
					changed_month = changed_month_split[0];

				}

				for(i = 0; i < instance.settings.monthNames.length; i++) {
					if(instance.settings.monthNames[i] == changed_month) {
						instance.settings.actualMonth = i + 1;
					}
				}
				instance._changeMonth();
				return false;
			});
			
			if(!$.proCalendar_isVersion('1.7')) {
				$(instance.calendar).on('click', '.dp_pec_date_event_map', function(event) {
					event.preventDefault();
					$(this).closest('.dp_pec_date_event').find('.dp_pec_date_event_map_iframe').slideDown('fast');
				});
			} else {
				$('.dp_pec_date_event_map', instance.calendar).live('click', function(event) {
					event.preventDefault();
					$(this).closest('.dp_pec_date_event').find('.dp_pec_date_event_map_iframe').slideDown('fast');
				});
			}
			
			$(instance.calendar).on('change', 'select.pec_recurring_frequency', function() {
				
				instance._pec_update_frequency(this.value);
				
			});
		},
		
		_pec_update_frequency : function(val) {
			
			var instance = this;

			jQuery('.pec_daily_frequency', instance.calendar).hide();
			jQuery('.pec_weekly_frequency', instance.calendar).hide();
			jQuery('.pec_monthly_frequency', instance.calendar).hide();
			
			switch(val) {
				case "1":
					jQuery(".pec_daily_frequency", instance.calendar).show();
					jQuery(".pec_weekly_frequency", instance.calendar).hide();
					jQuery(".pec_monthly_frequency", instance.calendar).hide();
					break;	
				case "2":
					jQuery(".pec_daily_frequency", instance.calendar).hide();
					jQuery(".pec_weekly_frequency", instance.calendar).show();
					jQuery(".pec_monthly_frequency", instance.calendar).hide();
					break;	
				case "3":
					jQuery(".pec_daily_frequency", instance.calendar).hide();
					jQuery(".pec_weekly_frequency", instance.calendar).hide();
					jQuery(".pec_monthly_frequency", instance.calendar).show();
					break;	
				case "4":
					jQuery(".pec_daily_frequency", instance.calendar).hide();
					jQuery(".pec_weekly_frequency", instance.calendar).hide();
					jQuery(".pec_monthly_frequency", instance.calendar).hide();
					break;	
			}
		},
						
		_makeResponsive : function() {
			var instance = this;
			
			if(instance.calendar.width() < 500) {
				$(instance.calendar).addClass('dp_pec_400');

				$('.dp_pec_dayname span', instance.calendar).each(function(i) {
					$(this).html($(this).html().substr(0,3));
				});
				
				$('.prev_month strong', instance.calendar).hide();
				$('.next_month strong', instance.calendar).hide();
				$('.prev_day strong', instance.calendar).hide();
				$('.next_day strong', instance.calendar).hide();
				
			} else {
				$(instance.calendar).removeClass('dp_pec_400');

				$('.prev_month strong', instance.calendar).show();
				$('.next_month strong', instance.calendar).show();
				$('.prev_day strong', instance.calendar).show();
				$('.next_day strong', instance.calendar).show();
				
			}
		},
		_removeElements : function () {
			var instance = this;
			
			$('.dp_pec_date,.dp_pec_dayname,.dp_pec_isotope,.dpProEventCalendar_load_more, .dp_pec_responsive_weekly', instance.calendar).fadeOut(500);
			$('.dp_pec_monthly_row', instance.calendar).hide();
			$('.dp_pec_content', instance.calendar).addClass( 'dp_pec_content_loading' );
			$('.eventsPreviewDiv').html('').hide();
		},
		
		_prevMonth : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualMonth--;
				instance.settings.actualMonth = instance.settings.actualMonth == 0 ? 12 : (instance.settings.actualMonth);
				instance.settings.actualYear = instance.settings.actualMonth == 12 ? instance.settings.actualYear - 1 : instance.settings.actualYear;
				
				instance._changeMonth();
			}
		},
		
		_nextMonth : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualMonth++;
				instance.settings.actualMonth = instance.settings.actualMonth == 13 ? 1 : (instance.settings.actualMonth);
			
				instance.settings.actualYear = instance.settings.actualMonth == 1 ? instance.settings.actualYear + 1 : instance.settings.actualYear;
				
				instance._changeMonth();
			}
		},
		
		_prevDay : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay--;
				//instance.settings.actualDay = instance.settings.actualDay == 0 ? 12 : (instance.settings.actualDay);
				
				instance._changeDay();
			}
		},
		
		_nextDay : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay++;
				//instance.settings.actualDay = instance.settings.actualDay == 13 ? 1 : (instance.settings.actualDay);
	
				instance._changeDay();
			}
		},
		
		_prevWeek : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay -= 7;
				//instance.settings.actualDay = instance.settings.actualDay == 0 ? 12 : (instance.settings.actualDay);
				
				instance._changeWeek();
			}
		},
		
		_nextWeek : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualDay += 7;
				//instance.settings.actualDay = instance.settings.actualDay == 13 ? 1 : (instance.settings.actualDay);
	
				instance._changeWeek();
			}
		},
		
		_changeMonth : function () {
			var instance = this;
			
			//$('.dp_pec_content', instance.calendar).css({'overflow': 'hidden'});
			$('.dp_pec_nav_monthly', instance.calendar).show();
			$('.actual_month', instance.calendar).html( instance.settings.monthNames[(instance.settings.actualMonth - 1)] + ' ' + instance.settings.actualYear );
			if($('.dp_pec_nav select.pec_switch_month', instance.calendar).val().indexOf('-') !== -1) {

				$('.dp_pec_nav select.pec_switch_month', instance.calendar).val(instance.settings.monthNames[(instance.settings.actualMonth - 1)]+'-'+instance.settings.actualYear);

			} else {

				$('.dp_pec_nav select.pec_switch_month', instance.calendar).val(instance.settings.monthNames[(instance.settings.actualMonth - 1)]);

			}
			$('.dp_pec_nav select.pec_switch_year', instance.calendar).val(instance.settings.actualYear);
			$('.dp_pec_nav select', instance.calendar).selectric('refresh');
			
			instance._removeElements();
			
			if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.prev_month', instance.calendar).hide();
			} else {
				$('.prev_month', instance.calendar).show();
			}

			if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.next_month', instance.calendar).hide();
			} else {
				$('.next_month', instance.calendar).show();
			}
			
			var date_timestamp = Date.UTC(instance.settings.actualYear, (instance.settings.actualMonth - 1), 15) / 1000;
			
			if(instance.settings.type == "accordion") {
				$('.events_loading', instance.calendar).show();
				$.post(ProEventCalendarAjax.ajaxurl, { month: instance.settings.actualMonth, year: instance.settings.actualYear, calendar: instance.settings.calendar, category: instance.settings.category, event_id: instance.settings.event_id, author: instance.settings.author, action: 'getEventsMonthList', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
					function(data) {
						
						$('.events_loading', instance.calendar).hide();
						$('.dp_pec_content', instance.calendar).html(data);
						
						$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);

					}
				);	
			} else {
				if(instance.monthlyView == "calendar") {
					var start = new Date().getTime(); // note getTime()

					$.post(ProEventCalendarAjax.ajaxurl, { 
						date: date_timestamp, 
						calendar: instance.settings.calendar, 
						category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), 
						is_admin: instance.settings.isAdmin, 
						event_id: instance.settings.event_id, 
						author: instance.settings.author, 
						type: instance.settings.type, 
						action: 'getDate', 
						postEventsNonce : ProEventCalendarAjax.postEventsNonce 
					},
						function(data) {
							
							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
							
							var dppec_date = $('.dp_pec_content', instance.calendar).find(".dp_pec_date[data-dppec-date='" + instance.settings.defaultDateFormat + "']");
							
							if(typeof dppec_date.attr('style') == 'undefined' && instance.settings.show_current_date) {
								dppec_date.css('background-color', instance.settings.current_date_color).addClass('dp_pec_special_date');
							}

							$(instance.calendar).removeClass('dp_pec_daily');
							$(instance.calendar).removeClass('dp_pec_weekly');
							$(instance.calendar).addClass('dp_pec_'+instance.view);
		
							instance.eventDates = $('.dp_pec_date', instance.calendar);
							
							
							// Load time debug
					        //console.log( end - start );
							
							$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
							instance._makeResponsive();
						}
					);	
					
				} else {
				
					$.post(ProEventCalendarAjax.ajaxurl, { month: instance.settings.actualMonth, year: instance.settings.actualYear, calendar: instance.settings.calendar, category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), event_id: instance.settings.event_id, author: instance.settings.author, action: 'getEventsMonth', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
						function(data) {
		
							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
							$(instance.calendar).removeClass('dp_pec_daily');
							$(instance.calendar).removeClass('dp_pec_weekly');
							$(instance.calendar).addClass('dp_pec_'+instance.view);
							
							instance.eventDates = $('.dp_pec_date', instance.calendar);
							
							$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
							instance._makeResponsive();
						}
					);	
				
				}
			}
			
			
		},
		
		_changeDay : function () {
			var instance = this;
			
			$('.dp_pec_nav_daily', instance.calendar).show();
						
			//$('span.actual_month', instance.calendar).html( instance.settings.monthNames[(instance.settings.actualMonth - 1)] + ' ' + instance.settings.actualYear );

			instance._removeElements();
						
			var date_timestamp = Date.UTC(instance.settings.actualYear, (instance.settings.actualMonth - 1), (instance.settings.actualDay)) / 1000;

			$.post(ProEventCalendarAjax.ajaxurl, { date: date_timestamp, calendar: instance.settings.calendar, category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), is_admin: instance.settings.isAdmin, event_id: instance.settings.event_id, author: instance.settings.author, is_admin: instance.settings.isAdmin, action: 'getDaily', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
				function(data) {
					var newDate = data.substr(0, data.indexOf(">!]-->")).replace("<!--", "");
					$('span.actual_day', instance.calendar).html( newDate );
					
					$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
					$(instance.calendar).removeClass('dp_pec_monthly');
					$(instance.calendar).removeClass('dp_pec_weekly');
					$(instance.calendar).addClass('dp_pec_'+instance.view);

					instance.eventDates = $('.dp_pec_date', instance.calendar);
					
					$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
					instance._makeResponsive();
				}
			);
			
			
		},
		
		_changeWeek : function () {
			var instance = this;
			
			$('.dp_pec_nav_weekly', instance.calendar).show();
						
			//$('span.actual_month', instance.calendar).html( instance.settings.monthNames[(instance.settings.actualMonth - 1)] + ' ' + instance.settings.actualYear );

			instance._removeElements();
						
			var date_timestamp = Date.UTC(instance.settings.actualYear, (instance.settings.actualMonth - 1), (instance.settings.actualDay)) / 1000;

			$.post(ProEventCalendarAjax.ajaxurl, { date: date_timestamp, calendar: instance.settings.calendar, category: (instance.settings.category != "" ? instance.settings.category : $('select.pec_categories_list', instance.calendar).val()), is_admin: instance.settings.isAdmin, event_id: instance.settings.event_id, author: instance.settings.author, is_admin: instance.settings.isAdmin, action: 'getWeekly', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
				function(data) {
					var newDate = data.substr(0, data.indexOf(">!]-->")).replace("<!--", "");
					$('span.actual_week', instance.calendar).html( newDate );
					
					$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
					$(instance.calendar).removeClass('dp_pec_monthly');
					$(instance.calendar).removeClass('dp_pec_daily');
					$(instance.calendar).addClass('dp_pec_'+instance.view);

					instance.eventDates = $('.dp_pec_date', instance.calendar);
					
					$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
					instance._makeResponsive();
				}
			);
			
			
		},
		
		_changeLayout : function () {
			var instance = this;
			
			instance._removeElements();
			
			if(instance.settings.type != 'compact') {
				$('.dp_pec_nav', instance.calendar).hide();
			}
			
			if(instance.view == "monthly" || instance.view == "monthly-all-events") {
				instance._changeMonth();
			}
			
			if(instance.view == "daily") {
				instance._changeDay();
			}
			
			if(instance.view == "weekly") {
				instance._changeWeek();
			}
			
		},
		
		_str_pad: function (input, pad_length, pad_string, pad_type) {
			
			var half = '',
				pad_to_go;
		 
			var str_pad_repeater = function (s, len) {
				var collect = '',
					i;
		 
				while (collect.length < len) {
					collect += s;
				}
				collect = collect.substr(0, len);
		 
				return collect;
			};
		 
			input += '';
			pad_string = pad_string !== undefined ? pad_string : ' ';
		 
			if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') {
				pad_type = 'STR_PAD_RIGHT';
			}
			if ((pad_to_go = pad_length - input.length) > 0) {
				if (pad_type == 'STR_PAD_LEFT') {
					input = str_pad_repeater(pad_string, pad_to_go) + input;
				} else if (pad_type == 'STR_PAD_RIGHT') {
					input = input + str_pad_repeater(pad_string, pad_to_go);
				} else if (pad_type == 'STR_PAD_BOTH') {
					half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
					input = half + input + half;
					input = input.substr(0, pad_length);
				}
			}
		 
			return input;
		},
		
		// Start dragging
		startDrag:function(e) {
			var instance = this;
			
			if(!instance.isDragging) {					
				var point;
				if(instance.hasTouch) {
					//parsing touch event
					var currTouches = e.originalEvent.touches;
					if(currTouches && currTouches.length > 0) {
						point = currTouches[0];
						instance.fingerCount = currTouches.length;
					}					
					else {	
						return false;						
					}
				} else {
					point = e;		
					
					if (e.target) el = e.target;
					else if (e.srcElement) el = e.srcElement;

					if(el.toString() !== "[object HTMLEmbedElement]" && el.toString() !== "[object HTMLObjectElement]") {	
						e.preventDefault();						
					}
				}

				instance.isDragging = true;
				
				instance.direction = null;
				instance.fingerData = instance.createFingerData();
				
				// check the number of fingers is what we are looking for, or we are capturing pinches
				if (!instance.hasTouch || (instance.fingerCount === instance.settings.fingers || instance.settings.fingers === "all") || instance.hasPinches()) {
					// get the coordinates of the touch
					instance.fingerData[0].start.x = instance.fingerData[0].end.x = point.pageX;
					instance.fingerData[0].start.y = instance.fingerData[0].end.y = point.pageY;
					startTime = instance.getTimeStamp();
					
					if(instance.fingerCount==2) {
						//Keep track of the initial pinch distance, so we can calculate the diff later
						//Store second finger data as start
						instance.fingerData[1].start.x = instance.fingerData[1].end.x = e.originalEvent.touches[1].pageX;
						instance.fingerData[1].start.y = instance.fingerData[1].end.y = e.originalEvent.touches[1].pageY;
						
						//startTouchesDistance = endTouchesDistance = calculateTouchesDistance(fingerData[0].start, fingerData[1].start);
					}
					
					if (instance.settings.swipeStatus || instance.settings.pinchStatus) {
						//ret = triggerHandler(event, phase);
					}
				}
				else {
					//A touch with more or less than the fingers we are looking for, so cancel
					instance.releaseDrag();
					ret = false; // actualy cancel so we dont register event...
				}
				
				if(!$.proCalendar_isVersion('1.7')) {
					$(document).on(instance.moveEvent, function(e) { instance.moveDrag(e); })
						.on(instance.upEvent, function(e) { instance.releaseDrag(e); });
				} else {
					$(document).bind(instance.moveEvent, function(e) { instance.moveDrag(e); })
						.bind(instance.upEvent, function(e) { instance.releaseDrag(e); });
				}
				
				startPos = instance.tx = parseInt(instance.eventDates.css("left"), 10);	
				
				instance.successfullyDragged = false;
				instance.accelerationX = this.tx;
				instance.startTime = (e.timeStamp || new Date().getTime());
				instance.startMouseX = point.clientX;
				instance.startMouseY = point.clientY;
			}
			
			if(instance.hasTouch) {
				$('.dp_pec_content', instance.calendar).on(instance.cancelEvent, function(e) { instance.releaseDrag(e); });
			}
			
			return false;	
		},				
		moveDrag:function(e) {	
			var instance = this;
			
			var point;
			if(instance.hasTouch) {	
				if(instance.lockVerticalAxis) {
					return false;
				}	
				
				var touches = e.originalEvent.touches;
				// If touches more then one, so stop sliding and allow browser do default action
				
				if(touches.length > 1) {
					return false;
				}
				
				point = touches[0];	
				
				//e.preventDefault();				
			} else {
				point = e;
				//e.preventDefault();		
			}

			// Helps find last direction of drag move
			instance.lastDragPosition = instance.currentDragPosition;
			var distance = point.clientX - instance.startMouseX;
			if(instance.lastDragPosition != distance) {
				instance.currentDragPosition = distance;
			}

			if(distance != 0)
			{	

				if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {			
					if(distance > 0) {
						distance = Math.sqrt(distance) * 5;
					}			
				} else if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {		
					if(distance < 0) {
						distance = -Math.sqrt(-distance) * 5;
					}	
				}
				
				$('.dp_pec_content', instance.calendar).addClass('isDragging');
				instance.eventDates.css("left", distance);		
				
			}	
			
			var timeStamp = (e.timeStamp || new Date().getTime());
			if (timeStamp - instance.startTime > 350) {
				instance.startTime = timeStamp;
				instance.accelerationX = instance.tx + distance;						
			}
			
			if(!instance.checkedAxis) {
				
				var dir = true,
					diff = (Math.abs(point.pageX - instance.startMouseX) - Math.abs(point.pageY - instance.startMouseY) ) - (dir ? -7 : 7);

				if(diff > 7) {
					// hor movement
					if(dir) {
						e.preventDefault();
						instance.currMoveAxis = 'x';
					} else if(instance.hasTouch) {
						instance.completeGesture();
						return;
					} 
					instance.checkedAxis = true;
				} else if(diff < -7) {
					// ver movement
					if(!dir) {
						e.preventDefault();
						instance.currMoveAxis = 'y';
					} else if(instance.hasTouch) {
						instance.completeGesture();
						return;
					} 
					instance.checkedAxis = true;
				}
				return;
			}
			
			//Save the first finger data
			instance.fingerData[0].end.x = instance.hasTouch ? point.pageX : e.pageX;
			instance.fingerData[0].end.y = instance.hasTouch ? point.pageY : e.pageY;
			
			instance.direction = instance.calculateDirection(instance.fingerData[0].start, instance.fingerData[0].end);
			
			instance.validateDefaultEvent(instance.direction);
			
			return false;		
		},
		completeGesture: function() {
			var instance = this;
			instance.lockVerticalAxis = true;
			instance.releaseDrag();
		},
		releaseDrag:function(e) {
			var instance = this;
			
			if(instance.isDragging) {	
				var self = this;
				instance.isDragging = false;			
				instance.lockVerticalAxis = false;
				instance.checkedAxis = false;	
				$('.dp_pec_content', instance.calendar).removeClass('isDragging');
				
				var endPos = parseInt(instance.eventDates.css('left'), 10);

				$(document).unbind(instance.moveEvent).unbind(instance.upEvent);					

				if(endPos == instance._startPos) {						
					instance.successfullyDragged = false;
					return;
				} else {
					instance.successfullyDragged = true;
				}
				
				var dist = (instance.accelerationX - endPos);		
				var duration =  Math.max(40, (e.timeStamp || new Date().getTime()) - instance.startTime);
				// For nav speed calculation F=ma :)
				/*
				var v0 = Math.abs(dist) / duration;	
				
				
				var newDist = instance.eventDates.width() - Math.abs(startPos - endPos);
				var newDuration = Math.max((newDist * 1.08) / v0, 200);
				newDuration = Math.min(newDuration, 600);
				*/
				function returnToCurrent() {						
					/*
					newDist = Math.abs(startPos - endPos);
					newDuration = Math.max((newDist * 1.08) / v0, 200);
					newDuration = Math.min(newDuration, 500);
					*/

					$(instance.eventDates).animate(
						{'left': 0}, 
						'fast'
					);
				}
				
				// calculate move direction
				if((startPos - instance.settings.dragOffset) > endPos) {		

					if(instance.lastDragPosition < instance.currentDragPosition) {	
						returnToCurrent();
						return false;					
					}
					
					if(!(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin)) {
						if(instance.view == "monthly") {
							instance._nextMonth(instance);
						} else {
							instance._nextDay(instance);
						}
					} else {
						returnToCurrent();
					}
					
				} else if((startPos + instance.settings.dragOffset) < endPos) {	

					if(instance.lastDragPosition > instance.currentDragPosition) {
						returnToCurrent();
						return false;
					}
					
					if(!(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin)) {
						if(instance.view == "monthly") {
							instance._prevMonth(instance);
						} else {
							instance._prevDay(instance);
						}
						
					} else {
						returnToCurrent();
					}

				} else {
					returnToCurrent();
				}
			}

			return false;
		},
		
		/**
		* Checks direction of the swipe and the value allowPageScroll to see if we should allow or prevent the default behaviour from occurring.
		* This will essentially allow page scrolling or not when the user is swiping on a touchSwipe object.
		*/
		validateDefaultEvent : function(direction) {
			if (this.settings.allowPageScroll === "none" || this.hasPinches()) {
				e.preventDefault();
			} else {
				var auto = this.settings.allowPageScroll === true;

				switch (direction) {
					case "left":
						if ((true && auto) || (!auto && this.settings.allowPageScroll != "horizontal")) {
							event.preventDefault();
						}
						break;

					case "right":
						if ((true && auto) || (!auto && this.settings.allowPageScroll != "horizontal")) {
							event.preventDefault();
						}
						break;

					case "up":
						if ((false && auto) || (!auto && this.settings.allowPageScroll != "vertical")) {
							e.preventDefault();
						}
						break;

					case "down":
						if ((false && auto) || (!auto && this.settings.allowPageScroll != "vertical")) {
							e.preventDefault();
						}
						break;
				}
			}

		},
		
		/**
		 * Returns true if any Pinch events have been registered
		 */
		hasPinches : function() {
			return this.settings.pinchStatus || this.settings.pinchIn || this.settings.pinchOut;
		},
		
		createFingerData : function() {
			var fingerData=[];
			for (var i=0; i<=5; i++) {
				fingerData.push({
					start:{ x: 0, y: 0 },
					end:{ x: 0, y: 0 },
					delta:{ x: 0, y: 0 }
				});
			}
			
			return fingerData;
		},
		
		/**
		* Calcualte the angle of the swipe
		* @param finger A finger object containing start and end points
		*/
		caluculateAngle : function(startPoint, endPoint) {
			var x = startPoint.x - endPoint.x;
			var y = endPoint.y - startPoint.y;
			var r = Math.atan2(y, x); //radians
			var angle = Math.round(r * 180 / Math.PI); //degrees

			//ensure value is positive
			if (angle < 0) {
				angle = 360 - Math.abs(angle);
			}

			return angle;
		},
		
		/**
		* Calcualte the direction of the swipe
		* This will also call caluculateAngle to get the latest angle of swipe
		* @param finger A finger object containing start and end points
		*/
		calculateDirection : function(startPoint, endPoint ) {
			var angle = this.caluculateAngle(startPoint, endPoint);

			if ((angle <= 45) && (angle >= 0)) {
				return "left";
			} else if ((angle <= 360) && (angle >= 315)) {
				return "left";
			} else if ((angle >= 135) && (angle <= 225)) {
				return "right";
			} else if ((angle > 45) && (angle < 135)) {
				return "down";
			} else {
				return "up";
			}
		},
		
		/**
		* Returns a MS time stamp of the current time
		*/
		getTimeStamp : function() {
			var now = new Date();
			return now.getTime();
		}
	}
	
	$.fn.dpProEventCalendar = function(options){  

		var dpProEventCalendar;
		this.each(function(){
			
			dpProEventCalendar = new DPProEventCalendar($(this), options);
			
			$(this).data("dpProEventCalendar", dpProEventCalendar);
			
		});
		
		return this;

	}
	
  	/* Default Parameters and Events */
	$.fn.dpProEventCalendar.defaults = {  
		monthNames : new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
		actualMonth : '',
		actualYear : '',
		actualDay : '',
		defaultDate : '',
		lang_sending: 'Sending...',
		skin : 'light',
		view: 'monthly',
		type: 'calendar',
		selectric: true,
		lockVertical: true,
		calendar: null,
		show_current_date: true,
		dateRangeStart: null,
		dateRangeEnd: null,
		draggable: true,
		isAdmin: false,
		dragOffset: 50,
		recaptcha_enable : false,
		allowPageScroll: "vertical",
		fingers: 1
	};  
	
	$.fn.dpProEventCalendar.settings = {}
	
})(jQuery);

/* onShowProCalendar custom event */
 (function($){
  $.fn.extend({ 
    onShowProCalendar: function(callback, unbind){
      return this.each(function(){
        var obj = this;
        var bindopt = (unbind==undefined)?true:unbind; 
        if($.isFunction(callback)){
          if($(this).is(':hidden')){
            var checkVis = function(){
              if($(obj).is(':visible')){
                callback.call();
                if(bindopt){
                  $('body').unbind('click keyup keydown', checkVis);
                }
              }                         
            }
            $('body').bind('click keyup keydown', checkVis);
          }
          else{
            callback.call();
          }
        }
      });
    }
  });
})(jQuery);

(function($) {
/**
 * Used for version test cases.
 *
 * @param {string} left A string containing the version that will become
 *        the left hand operand.
 * @param {string} oper The comparison operator to test against. By
 *        default, the "==" operator will be used.
 * @param {string} right A string containing the version that will
 *        become the right hand operand. By default, the current jQuery
 *        version will be used.
 *
 * @return {boolean} Returns the evaluation of the expression, either
 *         true or false.
 */
	$.proCalendar_isVersion = function(version1, version2){
		if ('undefined' === typeof version1) {
		  throw new Error("$.versioncompare needs at least one parameter.");
		}
		version2 = version2 || $.fn.jquery;
		if (version1 == version2) {
		  return 0;
		}
		var v1 = normalize(version1);
		var v2 = normalize(version2);
		var len = Math.max(v1.length, v2.length);
		for (var i = 0; i < len; i++) {
		  v1[i] = v1[i] || 0;
		  v2[i] = v2[i] || 0;
		  if (v1[i] == v2[i]) {
			continue;
		  }
		  return v1[i] > v2[i] ? 1 : 0;
		}
		return 0;
	};
	function normalize(version){
	return $.map(version.split('.'), function(value){
	  return parseInt(value, 10);
	});

	}
	
	$(document).ready(function() {
	
		if(!$('.dpProEventCalendarOverlay').length) {
	
			$('body').append(
				$('<div>').addClass('dpProEventCalendarOverlay').click(function() { 
					$('.dpProEventCalendarModalEditEvent, .dpProEventCalendarModal, .dpProEventCalendarOverlay').fadeOut('fast')
				})
			);
	
		}
		
		$(document).on('click', '.pec_event_page_book', function(e) {
			
			var $cloned = $(this).next().clone(true);
			
			if(!$('.dpProEventCalendarModal').length) {
				$('body').append(
					$('<div>').addClass('dpProEventCalendarModal').prepend(
						$('<h2>').text($(this).find('strong').text()).append(
							$('<a>').addClass('dpProEventCalendarClose').attr({ 'href': 'javascript:void(0);' }).html('<i class="fa fa-times"></i>').click(function() { 
								$('.dpProEventCalendarModal, .dpProEventCalendarOverlay').fadeOut('fast')
							})
						)
					).show()
				);
				
				$('.dpProEventCalendarOverlay').show();
				
				$("input, textarea", '.dpProEventCalendarModal').placeholder();
				
				//dpShareLoadEvents();
			} else {
				$('.dpProEventCalendarModal .pec_book_select_date, .dpProEventCalendarModal .dpProEventCalendar_mailform').remove();
				
				$('.dpProEventCalendarModal, .dpProEventCalendarOverlay').removeAttr('style').show();
			}
			
			$('.dpProEventCalendarModal').addClass('dpProEventCalendarModal_Preload');
		
			$('.dpProEventCalendarModal').append(
				$cloned.css({ 'position' : 'relative', 'left' : 'auto', 'display': 'block'})
			);
			
			$('.dpProEventCalendarModal').removeClass('dpProEventCalendarModal_Preload');
			$('#pec_event_page_book_date', '.dpProEventCalendarModal').trigger('change');
			$('select', '.dpProEventCalendarModal').selectric();
			
		});
		
		
		$(document).on('click', '.pec_event_page_send_booking', function(e) {
			var instance = this;

			if($('#pec_event_page_book_name', '.dpProEventCalendarModal').length) {
				
				$('#pec_event_page_book_name, #pec_event_page_book_email', '.dpProEventCalendarModal').css({ 'border-color' : 'inherit' });
				
				if($('#pec_event_page_book_name', '.dpProEventCalendarModal').val() == '') {
					$('#pec_event_page_book_name', '.dpProEventCalendarModal').css({ 'border-color' : '#e14d43' });
					
					return false;
				}
				
				var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

				if($('#pec_event_page_book_email', '.dpProEventCalendarModal').val() == '' || !re.test($('#pec_event_page_book_email', '.dpProEventCalendarModal').val())) {
					$('#pec_event_page_book_email', '.dpProEventCalendarModal').css({ 'border-color' : '#e14d43' });
					
					return false;
				}
				
			}
			
			if($('#pec_event_page_book_phone', '.dpProEventCalendarModal').length) {
				
				$('#pec_event_page_book_phone', '.dpProEventCalendarModal').css({ 'border-color' : 'inherit' });
				
				if($('#pec_event_page_book_phone', '.dpProEventCalendarModal').val() == '') {
					$('#pec_event_page_book_phone', '.dpProEventCalendarModal').css({ 'border-color' : '#e14d43' });
					
					return false;
				}
				
			}
			
			if($('#pec_event_page_book_terms_conditions', '.dpProEventCalendarModal').length) {
				
				if($('#pec_event_page_book_terms_conditions', '.dpProEventCalendarModal').is( ":checked" ) == false) {

					$('#pec_event_page_book_terms_conditions', '.dpProEventCalendarModal').focus();
					
					return false;
				}

			}
			var extra_fields = {};
			if($('.pec_event_page_book_extra_fields', '.dpProEventCalendarModal').length) {

				$('.pec_event_page_book_extra_fields', '.dpProEventCalendarModal').each(function( index ) {
				  extra_fields[$(this).attr('name')] = $(this).val();
				});

			}
			var $btn_booking = $(this);
			$btn_booking.prop('disabled', true);
			$btn_booking.css('opacity', .6);
			var event_id = $('#pec_event_page_book_event_id', '.dpProEventCalendarModal').val();
			var quantity = $('#pec_event_page_book_quantity', '.dpProEventCalendarModal').val();
			$.post(ProEventCalendarAjax.ajaxurl, 
				{ 
					event_date: $('#pec_event_page_book_date', '.dpProEventCalendarModal').val(), 
					event_id: event_id, 
					calendar: $('#pec_event_page_book_calendar', '.dpProEventCalendarModal').val(), 
					comment: $('#pec_event_page_book_comment', '.dpProEventCalendarModal').val(), 
					quantity: quantity, 
					name: ($('#pec_event_page_book_name', '.dpProEventCalendarModal').length ? $('#pec_event_page_book_name', '.dpProEventCalendarModal').val() : ''), 
					email: ($('#pec_event_page_book_email', '.dpProEventCalendarModal').length ? $('#pec_event_page_book_email', '.dpProEventCalendarModal').val() : ''), 
					phone: ($('#pec_event_page_book_phone', '.dpProEventCalendarModal').length ? $('#pec_event_page_book_phone', '.dpProEventCalendarModal').val() : ''), 
					extra_fields: extra_fields,
					return_url: window.location.href,
					action: 'bookEvent', 
					postEventsNonce : ProEventCalendarAjax.postEventsNonce 
				},
				function(data) {
					data = jQuery.parseJSON(data);
					
					$('#pec_event_page_book_comment', '.dpProEventCalendarModal').val('');
					$btn_booking.prop('disabled', false);	
					$btn_booking.css('opacity', 1);
					
					if(data.gateway_screen != "") {
						
						$('.pec_book_select_date', '.dpProEventCalendarModal').html(data.gateway_screen);
						
						$('.pec_gateway_form select', '.dpProEventCalendarModal').selectric();
						
					} else {

						$('.pec_book_select_date', '.dpProEventCalendarModal').html(data.notification);
						//$('.dp_pec_attendees_counter_'+ event_id +' span', instance.calendar).text( parseInt($('.dp_pec_attendees_counter_'+ event_id +' span', instance.calendar).text(), 10) + parseInt(quantity, 10) );
					}
				}
			);	
			
		});
		
	});
})(jQuery);