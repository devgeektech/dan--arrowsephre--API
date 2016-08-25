<?php
error_reporting(0);

global $post_ID, $post;

setup_postdata($post);

$post_ID = get_the_ID();
$SITE_URL = plugin_dir_url( __FILE__ );

require_once(dirname(__FILE__).'/../../classes/base.class.php');

$calendar_id = get_post_meta($post_ID, 'pec_id_calendar');

if(!is_array($calendar_id)) {
	$calendar_id = array(0);	
}

$dpProEventCalendar_obj = new DpProEventCalendar( false, $calendar_id[0], null, null, '', '', $post_ID );

$event_dates = $dpProEventCalendar_obj->upcomingCalendarLayout( true, 20 );

if(!is_array($event_dates)) {
	$event_dates = array();
}

$booking_limit = get_post_meta($post_ID, 'pec_booking_limit', true);

?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>  
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title>Full Calender</title>
		<!--== CSS Files ==-->
		<link href="<?php echo $SITE_URL?>css/bootstrap.css" rel="stylesheet" media="screen">
		<link href="<?php echo $SITE_URL?>css/style.css" rel="stylesheet" media="screen">
		<link href="<?php echo $SITE_URL?>css/animate.min.css" rel="stylesheet" media="screen">
		<link href="<?php echo $SITE_URL?>../../css/font-awesome.css" rel="stylesheet" media="screen">
		<link href="<?php echo $SITE_URL?>css/flexslider.css" rel="stylesheet" media="screen">
		<link href="<?php echo $SITE_URL?>css/responsive.css" rel="stylesheet" media="screen">
        
        <link href="<?php echo $SITE_URL?>../../css/dpProEventCalendar.css" rel="stylesheet" media="screen">
        
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="" />
        <meta property="og:description" content="" />
        <meta property="og:url" content="" />
        <meta property="og:site_name" content="" />
        

		<!--== Google Fonts ==-->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet' type='text/css'>

		<script src="https://code.jquery.com/jquery-latest.min.js"></script>
		
        <script type='text/javascript'>
		/* <![CDATA[ */
		var ProEventCalendarAjax = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php?lang='.$_GET['lang'] )?>","postEventsNonce":""};
		/* ]]> */
		</script>

        <script src="<?php echo $SITE_URL?>../../js/jquery.dpProEventCalendar.js" type="text/javascript"></script>
        <script src="<?php echo $SITE_URL?>../../js/jquery.selectric.min.js" type="text/javascript"></script>
        <script src="<?php echo $SITE_URL?>../../js/jquery.placeholder.js" type="text/javascript"></script>
	</head>
	<body>
		<div id="home"></div>
		<header id="header" class="header" data-stellar-ratio="0.5">

			<!--== Caption ==-->
			<div class="header-caption" data-stellar-ratio="2">
				<div class="container">
					<div class="box">
						<h1><?php echo get_the_title()?></h1>
						
                        
					</div>
				</div>
			</div>

			<!--== Header Background ==-->
            <?php 
			$thumbnail_id = get_post_thumbnail_id($post->ID);
					
			$thumbnail_object = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
			$full_image_object = wp_get_attachment_image_src($thumbnail_id, 'full');
			
			?>
			<div id="header-background" class="header-background"<?php if($full_image_object[0] != "") {?> style="background-image: url('<?php echo $full_image_object[0]?>'); background-repeat: no-repeat; background-size: cover;"<?php }?>>
			</div>

		</header>

		<div class="container-fluid content">

			<!--===============================-->
			<!--== About ======================-->
			<!--===============================-->
			<section id="about">
						
                <div class="container">
                    
                    <div class="row">
    
                        <div class="col-sm-9 col-md-9">
                        
                            <?php echo wpautop(get_the_content())?>
                            
                        </div>
                        
                        <div class="col-sm-3 col-md-3">
                        	
                            <div class="event_details">
                            
                                <p>
                                    <i class="fa fa-clock-o"></i>
                                
                                    <?php echo date(get_option('time_format'), strtotime(get_post_meta($post_ID, 'pec_date', true)))?>
            
                                </p>
                            </div>
                            
                            <?php if(get_post_meta($post_ID, 'pec_phone', true) != "") {?>
                            <div class="event_details">
                                <p>
                                            
                                    <i class="fa fa-phone"></i>
                                    <?php echo get_post_meta($post_ID, 'pec_phone', true)?>
    
                                </p>
    
                            </div>    
                            <?php }?>
                        
                   		</div>
                   
                   </div>
                   
              </div>
              
		  </section>	  
          
          <section id="upcoming_dates_separator" class="color_separator">
						
                <div class="container"> 
                        
                   <div class="row">
    
                        <div class="col-sm-12 col-md-12">
                            	
                            <h2 class="main_title"><?php _e('Upcoming Dates', 'dpProEventCalendar')?></h2>
                            
                            <?php echo $dpProEventCalendar_obj->getBookingButton($post_ID)?>

                        </div>
                   
                   </div>
                   
              </div>
              
		  </section>	  
          
          <section id="upcoming_dates">
						
                <div class="container"> 
                        
                   <div class="row">
    
                        <div class="col-sm-12 col-md-12">
                        
                            <div class="dates_wrapper">
                            	
                                <div class="row">
									<?php 
                                    $counter = 0;
                                    $last_month = "";
                                    if(count($event_dates) > 0) {
                                        foreach($event_dates as $key) {
                                            
                                            $date = $key->date;
											$date_short = substr($key->date, 0, 10);
                                            
                                            $end_time = $key->end_time_hh.':'.$key->end_time_mm;	
                                            
											$booking_count = $dpProEventCalendar_obj->getBookingsCount($post_ID, $date_short);
											$booking_remain = true;
											$booked_date = false;
											
											if($dpProEventCalendar_obj->userHasBooking($date_short, $post_ID)) {
												$booked_date = true;
											}

											if($booking_limit > 0 && ($booking_limit - $booking_count) <= 0) {
												$booking_remain = false;
											}

                                            if($counter == 4) {
                                                
                                                echo '</div><div class="row">';
												$counter = 0;
                                                //break;
                                                    
                                            }
                                            
                                            $actual_month = date_i18n('F Y', strtotime($date));
                                            
                                            if($last_month != $actual_month) {
                                            
                                                ?>
                                                <div class="clear"></div>
                                                <div class="col-sm-12 col-md-12">
                                                	<h2><?php echo $actual_month?></h2>
                                                </div>
                                                <?php
                                                
                                                $last_month = $actual_month;
												$counter = 0;
                                                
                                            }
                                        ?>
                                        <div class="col-sm-3 col-md-3">
                                            
                                            <div class="calendar_date">
                                                
                                                
                                                <div class="cal_date"><?php echo date_i18n('d', strtotime($date))?></div>
                                                
                                                <?php if(get_post_meta($post_ID, 'pec_enable_booking', true)) {?>
            									
                                                
                                                <?php
                                                if($booked_date) {
													
													?>
                                                    <div class="cal_attendees">
                                                    <?php
                                                    echo $dpProEventCalendar_obj->translation['TXT_BOOK_ALREADY_BOOKED'];
													?>
                                                    </div>
                                                    <?php
													
                                                } else {
                                                    
                                                    if($booking_limit > 0) {
                                                    	
														?>
                                                        <div class="cal_attendees">
                                                        <?php
                                                        echo '<strong>'.($booking_limit - $booking_count).'</strong> '.$dpProEventCalendar_obj->translation['TXT_BOOK_TICKETS_REMAINING'];
														?>
                                                        </div>
                                                        <?php
                                                        
                                                    }
													
                                                }
												?>
                                                
                                                <!--<a href="javascript:void(0);" class="cal_book"><?php _e('Book Event', 'dpProEventCalendar')?></a>-->
            
                                                <?php }?>
                                                
                                            </div>
                                            
										</div>
                                        <?php 
                                            
                                            $counter++;
                                            
                                        }?>
                                        </div>
                                        <?php
                                    } else {
                                    
                                        ?>
                                        <p><?php _e('No Upcoming Dates For This Event.', 'dpProEventCalendar')?></p>
                                        <?php
                                        
                                    }?>
                                    
                                
                            </div>
                            
                            <div class="clear"></div>
                            
                            <?php if(!isset($_GET['iframe'])) {?>
                
                            <div class="circle_item">
                            
                                <div class="clear"></div>
                                
                                <a href="<?php echo home_url()?>" class="button" id="return_btn"><?php _e('Return to ', 'dpProEventCalendar')?><?php bloginfo('name')?></a>
                                
                            </div>
                            
                            <?php }?>
                            
                        </div>
                    
                    </div>
                
                </div>
                        
			</section>
			<!--==========-->
			
            <?php 
			if(get_post_meta($post_ID, 'pec_map', true)) {
			?>
			<!--===============================-->
			<!--== Location ===================-->
			<!--===============================-->
			<section id="location" class="bounceInUp" data-wow-duration="0.8s" data-wow-delay="0.1s">
				
                <div class="container">
                    
                    <div class="row">
    
                        <div class="col-sm-12 col-md-12">
                            <div class="map_details">
                                
                                <h2><i class="fa fa-map-marker"></i>Event Location</h2>
                                
                                <p>
            						
                                    <i class="fa fa-phone"></i>
                                    <?php echo get_post_meta($post_ID, 'pec_phone', true)?>
                                    
                                    <br>
                                    
                                    <i class="fa fa-home"></i>
                                    <?php echo get_post_meta($post_ID, 'pec_location', true)?>
            
                                </p>
            
                            </div>
						
                        </div>
                        
                    </div>
                    
                </div>
                
                <div class="map">
                    <div id="map-canvas"></div>
                </div>
			</section>
			<!--==========-->
			<?php }?>
		</div>


		<!--== Javascript Files ==-->
		<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script src="<?php echo $SITE_URL?>js/bootstrap.min.js"></script>
		<script src="<?php echo $SITE_URL?>js/SmoothScroll.js"></script>
		<script src="<?php echo $SITE_URL?>js/jquery.nav.js"></script>
		<script src="<?php echo $SITE_URL?>js/jquery.stellar.js"></script>
		<script src="<?php echo $SITE_URL?>js/jquery.flexslider-min.js"></script>
		<script src="<?php echo $SITE_URL?>js/jquery.placeholder.js"></script>
		<script src="<?php echo $SITE_URL?>js/jquery.accordion.js"></script>
    
        <script type="text/javascript">
	
		    var venueAddress = "<?php echo get_post_meta($post_ID, 'pec_map', true)?>";

		</script>
	
    	<script src="<?php echo $SITE_URL?>js/main.js"></script>
		<?php wp_footer()?>
	</body>
</html>