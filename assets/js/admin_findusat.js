jQuery(function($) {
  $('.submit_address').on('click',function()
  {
  	var address_line_1 = $('.address_line_1').val();
  	var address_line_2 = $('.address_line_2').val();
  	var address = address_line_1.replace(' ','+') + address_line_2.replace(' ','+');
  	// get coordinates

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
        console.log('lat_lng has been returned');
        $('#mapsLink').attr("href","https://www.google.com/maps/@"+lat_lng[0]+","+lat_lng[1]+",12z");
 var map;
        function initMap() {
         

          var myLatLng = {lat: lat_lng[0], lng: lat_lng[1]};

          map = new google.maps.Map(document.getElementById('map'), {
            zoom: 4,
            center: myLatLng
          });

          var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'Hello World!'
          });
        }
      }
    });
  	return false;
  });
});