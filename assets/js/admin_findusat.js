jQuery(function($)
{
	// get coordinates of the address provided
	jQuery('.submit_address').on('click',function()
	{
		var address_line_1 = $('.address_line_1').val().replace(' ','+');
		var address_line_2 = $('.address_line_2').val().replace(' ','+');
		var address = address_line_1.replace(' ','+') + address_line_2.replace(' ','+');

		$.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				action : 'getCoordinates',
				address : address
			},
			success : function( response ) {
				var lat_lng = response.split(',');
				$('#x_coordinate').val(lat_lng[0]);
				$('#y_coordinate').val(lat_lng[1]);
				$('#mapsLink').attr("href","https://www.google.com/maps/@"+lat_lng[0]+","+lat_lng[1]+",12z");
				ajax_initMap();
			}
		});
		return false;
	});

	// Check if coordinates exist, if they do populate the map
	if ( $('#x_coordinate').val() !== '' && $('#y_coordinate').val() !== '' )
	{
		ajax_initMap();
	}

	// display the map with a marker
	function ajax_initMap()
	{
		var lat = parseFloat( $('#x_coordinate').val() );
		var lng = parseFloat( $('#y_coordinate').val() );
		var myLatLng = { lat: lat, lng: lng };
		
		map = new google.maps.Map(document.getElementById('map'), {
			zoom: 4,
			center: myLatLng
		});

		var marker = new google.maps.Marker({
			position: myLatLng,	
			map: map
		});
	}
});