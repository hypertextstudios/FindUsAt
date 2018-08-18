<?php
	/*
		Plugin Name: FindUsAt
		Plugin URI: http://andrewkaser.com/#
		Description: Location of stores where your items can be found, and easily provide directions at the click of a button
		Author: Kaser
		Version: 1.0
		Author URI: andrewkaser.com
	*/

	require_once( 'include/findusat_admin_settings.php' );

	add_shortcode( 'findusat', 'findusat_shortcode' );

	add_action( 'admin_menu', 'findusat_menu' );
	add_action( 'wp_enqueue_scripts', 'findusat_adding_scripts' );
	add_action( 'admin_enqueue_scripts', 'findusat_adding_admin_scripts' );

	add_action( 'wp_ajax_get_coordinates_for_shortcode', 'get_coordinates_for_shortcode' );
	add_action( 'wp_ajax_nopriv_get_coordinates_for_shortcode', 'get_coordinates_for_shortcode' );

	/*
	 * register findusat shortcode to display map
	 */
	function findusat_shortcode( $atts )
	{
		$a = shortcode_atts( array(
			'width' => '400',
			'height' => '350',
		), $atts );

		echo '<div id="findusat_map" style="width:' . $a['width'] . '; height:' . $a['height'] . ';"></div>';

		$args = array(
			'post_type' => 'location',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );

		echo '<ul id="findusat_locations">';
		// The Loop
		if ( $the_query->have_posts() )
		{
			while ( $the_query->have_posts() )
			{
				$the_query->the_post();

				// get x/y latitude
				$address_line_1 = get_post_meta( get_the_ID(), 'address_line_1', true );
				$address_line_2 = get_post_meta( get_the_ID(), 'address_line_2', true );
				$x_coordinate = get_post_meta( get_the_ID(), 'x_coordinate', true );
				$y_coordinate = get_post_meta( get_the_ID(), 'y_coordinate', true );
				
				$location_name = get_the_title();
				$map_link = "https://google.com/maps/dir/".$x_coordinate.",".$y_coordinate;
				echo '<li>
					<ul>
						<li><h2><a href="'.$map_link.'">'.$location_name.'</a></h2></li>
						<li><a href="'.$map_link.'">'.$address_line_1.'</a></li>
						<li><a href="'.$map_link.'">'.$address_line_2.'</a></li>
					</ul>
				</li>';
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		} else {
			// no posts found
		}
		echo '</ul>';
	}

	/*
	 * register the custom post type
	 */
	function findusat_locations_init()
	{
		$args = array(
			'public' => true,
			'label' => 'Locations'
		);
		register_post_type( 'location', $args );
	}
	add_action( 'init', 'findusat_locations_init' );

	/*
	 * Add meta
	 */
	function location_address_metaboxes( $post )
	{
		add_meta_box( 'address_meta', 'Location Address', 'location_address_meta_box', 'location', 'side', 'high' );
	}
	add_action( 'add_meta_boxes', 'location_address_metaboxes' );

	/*
	 * output the content of location metabox
	 */
	function location_address_meta_box()
	{
		$address_line_1 = get_post_meta( get_the_ID(), 'address_line_1', true );
		$address_line_2 = get_post_meta( get_the_ID(), 'address_line_2', true );
		$x_coordinate = get_post_meta( get_the_ID(), 'x_coordinate', true );
		$y_coordinate = get_post_meta( get_the_ID(), 'y_coordinate', true );
		?>

		<ul>
			<li><input type="text" name="address_line_1" placeholder="Adress Line 1" class="address_line_1" value="<?php echo $address_line_1; ?>" /></li>
			<li><input type="text" name="address_line_2" placeholder="Adress Line 2" class="address_line_2" value="<?php echo $address_line_2; ?>" /></li>
			<li><input type="submit" class="submit_address" value="Generate Coordinates"/></li>
			<li><input type="text" name="x_coordinate" placeholder="X Coordinate" id="x_coordinate" value="<?php echo $x_coordinate; ?>" /></li>
			<li><input type="text" name="y_coordinate" placeholder="Y Coordinate" id="y_coordinate" value="<?php echo $y_coordinate; ?>" /></li>
			<a href="" id="mapsLink">Map</a>
			<div id="map"></div>
		</ul>
		<?php
	}

	/*
	 * save the meta data when the page is saved or published
	 */
	function save_findusat_meta( $post_id, $post )
	{
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// set default values incase user didn't set them.
		$findusat_meta['address_line_1'] = isset($_POST['address_line_1']) ? $_POST['address_line_1'] : '';
		$findusat_meta['address_line_2'] = isset($_POST['address_line_2']) ? $_POST['address_line_2'] : '';
		$findusat_meta['x_coordinate'] = isset($_POST['x_coordinate']) ? $_POST['x_coordinate'] : '';
		$findusat_meta['y_coordinate'] = isset($_POST['y_coordinate']) ? $_POST['y_coordinate'] : '';

		foreach ( $findusat_meta as $key => $value )
		{
			if ( $post->post_type == 'revision' ) return;

			$value = implode( ',', (array)$value );

			if ( get_post_meta( $post->ID, $key, FALSE ) )
			{
				update_post_meta( $post->ID, $key, $value );
			} else {
				add_post_meta( $post->ID, $key, $value );
			}
			if ( !$value ) delete_post_meta( $post->ID, $key );
		}
	}
	add_action( 'save_post', 'save_findusat_meta', 1, 2 );

	/*
	 * load styles and JS for front end of website.
	 */
	function findusat_adding_scripts()
	{
		$findusat_options = get_option ( 'findusat_global_options' );
		$googlemaps_api_key = isset ( $findusat_options['findusat_googlemaps_api_key'] ) ? $findusat_options['findusat_googlemaps_api_key'] : $options;

		wp_register_style( 'findusat_style', plugins_url( 'assets/css/findusat.css', __FILE__) );
		wp_enqueue_style( 'findusat_style' );

		wp_register_script( 'findusat_google_maps_api', 'https://maps.googleapis.com/maps/api/js?key='.$googlemaps_api_key, '1', true );
		wp_enqueue_script( 'findusat_google_maps_api' );
		wp_register_script( 'findusat_script', plugins_url('assets/js/findusat.js', __FILE__), array('jquery', 'findusat_google_maps_api'), '1.1', true );
		
		wp_localize_script( 'findusat_script', 'fua_coords', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'findusat_script' );
	}

	/*
	 * load styles and js for wp-admin page
	 */
	function findusat_adding_admin_scripts()
	{
		$findusat_options = get_option ( 'findusat_global_options' );
		$googlemaps_api_key = isset ( $findusat_options['findusat_googlemaps_api_key'] ) ? $findusat_options['findusat_googlemaps_api_key'] : $options;

		wp_register_style( 'findusat_admin_style', plugins_url( 'assets/css/admin_findusat.css', __FILE__) );
		wp_enqueue_style( 'findusat_admin_style' );

		wp_register_script( 'findusat_google_maps_api', 'https://maps.googleapis.com/maps/api/js?key='.$googlemaps_api_key, '1', true );
		wp_enqueue_script( 'findusat_google_maps_api' );

		wp_register_script( 'findusat_admin_script', plugins_url('assets/js/admin_findusat.js', __FILE__), array('jquery', 'findusat_google_maps_api'), '1.1', true );
		wp_enqueue_script( 'findusat_admin_script' );
	}
	add_action( 'wp_ajax_getCoordinates', 'getCoordinates' );


	/*
	 * returns a single set of coordinates from a provided address 
	 */
	function getCoordinates()
	{
		$address = str_replace( " ", "+", $_POST['address'] );
		
		$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";

		$response = file_get_contents( $url );
		
		$json = json_decode( $response, TRUE ); //generate array object from the response from the web

		$lat_lng = array(
			'lat' => $json['results'][0]['geometry']['location']['lat'],
			'lng' => $json['results'][0]['geometry']['location']['lng']
		);
		
		echo $json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng'];
		die();
	}

	/*
	 * Build an array of all coordinates when FindUsAt shortcode is called
	 */
	function get_coordinates_for_shortcode()
	{
		$coordinates = array();
		$c = 0;

		$args = array(
			'post_type' => 'location',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() )
		{
			while ( $the_query->have_posts() )
			{
				$the_query->the_post();

				// get x/y latitude
				$address_line_1 = get_post_meta( get_the_ID(), 'address_line_1', true );
				$address_line_2 = get_post_meta( get_the_ID(), 'address_line_2', true );
				$x_coordinate = get_post_meta( get_the_ID(), 'x_coordinate', true );
				$y_coordinate = get_post_meta( get_the_ID(), 'y_coordinate', true );
				$infowindow_data = get_the_content();
				$location_name = get_the_title();
				$coord_combo = array( $location_name, $x_coordinate , $y_coordinate, $infowindow_data );

				$coordinates[$c] = $coord_combo;
				$c++;
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		} else {
			// no posts found
		}
		$coordinates = json_encode($coordinates);
		echo $coordinates;
		die();
	}
?>