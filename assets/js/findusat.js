jQuery( document ).ready( function()
{
	function view_initMap(coords_response)
	{
		var coords = JSON.parse(coords_response);

		map = new google.maps.Map(document.getElementById('findusat_map'), {
			zoom: 8,
			center: { lat: 42.2741366, lng: -85.6671883 }
		});

		for ( i = 0; i < coords.length; i++ )
		{
			var title = coords[i][0];
			var lat = parseFloat(coords[i][1]);
			var lng = parseFloat(coords[i][2]);

			var myLatLng = {lat: lat, lng: lng};

			var marker = new google.maps.Marker({
				map: map,
				position: myLatLng,
				title: title
			});
		}
	}

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
});