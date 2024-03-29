jQuery( document ).ready( function()
{
	
	// check to see if the map needs to be loaded
	if ( jQuery('#findusat_map').length )
	{
		// display the map for all locations
		function view_initMap(coords_response)
		{
			var coords = JSON.parse(coords_response);
			var bounds  = new google.maps.LatLngBounds();
			var locationCount = coords.length;

			map = new google.maps.Map(document.getElementById('findusat_map'), {
				zoom: 15,
				center: { lat: 42.2741366, lng: -85.6671883 }
			});


			jQuery('#location_count').html(locationCount);
			
			for ( i = 0; i < locationCount; i++ )
			{

				var title = coords[i][0];
				var title = title.replace(/&amp;/g, '&');
				var title = title.replace(/&#038;/g, '&');
				var lat = parseFloat(coords[i][1]);
				var lng = parseFloat(coords[i][2]);
				var infowindow = new google.maps.InfoWindow({});

				var myLatLng = {lat: lat, lng: lng};

				var marker = new google.maps.Marker({
					map: map,
					position: myLatLng,
					title: ''+title+''
				});

				google.maps.event.addListener(marker, 'click', (function (marker, i)
				{
					return function ()
					{
						infowindow.setContent(coords[i][3]);
						infowindow.open(map, marker);
					}
				})(marker, i));

				loc = new google.maps.LatLng(lat, lng);
				bounds.extend(loc);
			}

			map.fitBounds(bounds);
			map.panToBounds(bounds);
		}

		// get array of location data and pass them to view_initMap()
		jQuery.ajax({
			url: fua_coords.ajax_url,
			type: 'post',
			data: {
				action: 'get_coordinates_for_shortcode',
			},
			success: function( coord_array )
			{
				//var coords_response = jQuery.parseJSON(coord_array);
				view_initMap(coord_array);
				return false;
			}
		});
	}
});