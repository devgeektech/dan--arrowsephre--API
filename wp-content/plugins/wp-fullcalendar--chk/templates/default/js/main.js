(function ($) {
    "use strict";
	
	var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
	
    var fn = {

        // Launch Functions
        Launch: function () {
            fn.GoogleMaps();
            fn.Stellar();
            fn.Navigation();
            fn.Menu();
            fn.Apps();
        },



        // Google Maps
        GoogleMaps: function () {
			
			window.onload = function() {	
	
				var geocoder = new google.maps.Geocoder();
				
				geocoder.geocode({ 'address': venueAddress }, function (results, status) {
					
				  if (status == google.maps.GeocoderStatus.OK) {
					  
					  createGMAP(results[0].geometry.location);
					  
				  } else {
					  
					jQuery('.gmap_bg').remove();
					//alert('error: ' + status);
					
					return false;
					
					
				  }
				});
				
				function createGMAP(locationlatlng) {
				
				var lat = locationlatlng.lat().toString().substr(0, 12);
				var lng = locationlatlng.lng().toString().substr(0, 12);
					
				var myLatlng = new google.maps.LatLng(lat,lng);
				
				// Create an array of styles.
				var styles = [
				{
				  stylers: [
					{ hue: "#00ffe6" },
					{ saturation: -20 }
				  ]
				},{
				  featureType: "road",
				  elementType: "geometry",
				  stylers: [
					{ lightness: 100 },
					{ visibility: "simplified" }
				  ]
				},{
				  featureType: "poi.business",
				  elementType: "labels",
				  stylers: [
					{ visibility: "off" }
				  ]
				}
				];
			
				// Create a new StyledMapType object, passing it the array of styles,
				// as well as the name to be displayed on the map type control.
				var styledMap = new google.maps.StyledMapType(styles,
				{name: "Styled Map"});	
			
				var myOptions = {
					center: myLatlng,
					zoom: parseInt(14, 11),
					mapTypeControlOptions: {
						mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
					},
					disableDefaultUI: true,
					scrollwheel: false,
					disableDoubleClickZoom: true,
					draggable: false
				};
			
				var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
				
				//Associate the styled map with the MapTypeId and set it to display.
				map.mapTypes.set('map_style', styledMap);
				map.setMapTypeId('map_style');
				
				var marker = new google.maps.Marker({
					  position: myLatlng,
					  map: map
				  });
				  
				}
				
			}
			
        },



        // Stellar
        Stellar: function() {
            if(!(navigator.userAgent.match(/iPhone|iPad|iPod|Android|BlackBerry|IEMobile/i))) {
                $.stellar({
                    horizontalScrolling: false,
                    positionProperty: 'transform',
                    hideDistantElements: false
                });
            }
        },



        // One Page Navigation
        Navigation: function () {
            $('#navigation').onePageNav({
                currentClass: 'active',
                scrollSpeed: 1000,
                scrollOffset: 75,
                scrollThreshold: 0.2,
                easing: 'swing'
            });
        },



        // Menu
        Menu: function () {
            
			

        },

        // Apps
        Apps: function () {

            // Accordion
            $('.accordion').accordion();

            // Placeholders
            $('input, textarea').placeholder();
			
			$(document).scroll(function () {
		
				var position = $(this).scrollTop();
				
				if (!isMobile) {
					
					$(".header-caption .box").css({
						
						opacity : (1 - position / 800)
						
					});
					
					$("#return_btn").css({
						
						opacity : (0 + position / 800)
						
					});
					
				};
				
			});
        }

    };

    $(document).ready(function () {
        fn.Launch();
    });

})(jQuery);