<?php
/**
 * Template Name:demo
 *
 */
error_reporting(0);
get_header(); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script type="text/javascript">
			var gt3_ajaxurl = "http://localhost/dan1/wp-admin/admin-ajax.php";
		</script>
  
	<link rel='stylesheet' id='events-planner-stylesheet-main-css'  href='http://www.nancymerkling.com/wp-content/plugins/events-planner-pro/css/style.css?ver=4.3.1' type='text/css' media='all' />
<link rel='stylesheet' id='fullcalendar-stylesheet-css'  href='http://www.nancymerkling.com/wp-content/plugins/events-planner-pro/css/fullcalendar.css?ver=4.3.1' type='text/css' media='all' />
<link rel='stylesheet' id='small-calendar-css-css'  href='http://www.nancymerkling.com/wp-content/plugins/events-planner-pro/css/calendar/small-calendar.css?ver=4.3.1' type='text/css' media='all' />
<link rel='stylesheet' id='jquery-dataTables-css'  href='http://www.nancymerkling.com/wp-content/plugins/events-planner-pro/css/jquery.dataTables.css?ver=4.3.1' type='text/css' media='all' />
<link rel='stylesheet' id='course-calendar-css-css'  href='http://www.nancymerkling.com/wp-content/plugins/events-planner-pro/css/calendar/epl-course-cal.css?ver=4.3.1' type='text/css' media='all' />
<link rel='stylesheet' id='events-planner-stylesheet-css'  href='http://www.nancymerkling.com/wp-content/plugins/events-planner-pro/css/events-planner-style1.css?ver=4.3.1' type='text/css' media='all' />
<script type='text/javascript'>
/* <![CDATA[ */
var EPL = {"ajaxurl":"http:\/\/localhost/dan1/\/wp-admin\/admin-ajax.php","plugin_url":"http:\/\/www.nancymerkling.com\/wp-content\/plugins\/events-planner-pro\/","date_format":"mm\/dd\/yy","time_format":"g:i a","firstDay":"1","sc":"0","debug":"0","cart_added_btn_txt":"In the cart (View)"};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo site_url();?>/wp-content/themes/twentythirteen/js/events-planner.js'></script>

<script type='text/javascript' src='<?php echo site_url();?>/wp-content/themes/twentythirteen/js/fullcalendar.min.js'></script>

<div id='chk'></div>

<script type='text/javascript'>

    jQuery(document).ready(function($) {
	
$(".fc-select-my-month").change(function(){
       alert("HI");

});
        //TODO - remove php and use js vars

        $('#chk').fullCalendar({

            header: {
                left: '',
                center: 'select-my', //CAN'T USE THE title in header
             },

            selectMY: {
              
            },
            firstDay: EPL.firstDay,
            month:11,
            year:2015,
            theme: 0, //change
            editable: false,
            allDaySlot: false,
            allDayDefault: false,
            defaultView: "month",
            minTime: 7,
            
            
            events: [{"title":"<?php echo "test";?>","description":"\n\n<div class=\"fc_template1\">\n\n    <div class=\"event_title\"><?php echo "test";?><\/div>\n\n    <div class=\"event_details\">\n\n        <div class=\"event_date\">\n            Saturday, January 9, 2016        <\/div>\n\n\n                        <div class=\"event_time\">9:30 AM - 12:15 PM<\/div>\n        \n\n            <div style=\"\" class=\"event_description\">Set in her historic loft studio, Nancy Merkling demystifies shooting 'off-automatic' with her rare and insightful teaching style. Learning camera settings has never been simpler.  This 3- hour mini-workshop includes casual lecture, hands-on assistance and a field-trip-shoot adventure in the beautiful Starline Factory to apply your new skills and answer questions!  <\/div>\n    <\/div>\n\n<\/div>\n\n","term_list":"workshops","start_timestamp":"1452297600","start":"2016-01-09 09:30:00","end_timestamp":"1452297600","end":"2016-01-09 12:15:00","url":"http:\/\/www.nancymerkling.com\/register-online\/?page_id=356&epl_action=process_cart_action&cart_action=add&event_id=6&_rand=5666b115ee8b4&_date_id=bmgerG","edit_url":null,"backgroundColor":"#ffffff","borderColor":"#ffffff","textColor":"#6d8583","className":"","id":6,"sort_time":1452331800}],
            eventRender: function(event, element) {

                element.find('span.fc-event-title').html(element.find('span.fc-event-title').text());


            },
            eventMouseover:function( event, jsEvent, view ) {

                var title = event.title;
                var content = event.description;


            
                //ttp.fadeTo('10',0.9);

            },
            eventMouseout:function( event, jsEvent, view ) {


                $('#fc_tooltip').remove();


            },
            loading: function(bool) {
                if (bool) epl_loader('show');
                else epl_loader('hide');
            }

        });


    });


</script>

		
<?php get_sidebar(); ?>
<?php get_footer(); ?>