<?php
	/*
		Plugin Name: FindUsAt
		Plugin URI: http://andrewkaser.com/#
		Description: Location of stores where your items can be found, and easily provide directions at the click of a button
		Author: Kaser
		Version: 0.1
		Author URI: andrewkaser.com
	*/

	$googlemaps_api_key = 'AIzaSyAbbaCFnkLFkAKUX8pmXBdnZtkjO206VSg';


	add_action( 'admin_menu', 'findusat_menu' );
	add_action( 'wp_enqueue_scripts', 'findusat_adding_scripts' );
	add_action( 'admin_enqueue_scripts', 'findusat_adding_admin_scripts' );

	function findusat_menu()
	{
		add_submenu_page('options-general.php', 'Find Us At', 'Find Us At', 'manage_options', 'findusat', 'findusat_options' );
	}

	function findusat_options()
	{
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Find Us At - Settings</h1>
			<p>Please configure the plugin to operate how you'd like it to. If you have any suggestions please shoot them over to kaser@cssboss.com</p>
		</div>
		<?php
	}

	/*
	 * register the custom post type
	 */
	function findusat_locations_init() {
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
	 * save the meta
	 */
	function save_findusat_meta( $post_id, $post )
	{
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// set default values incase user didn't set them.
		$findusat_meta['address_line_1'] = isset($_POST['address_line_1']) ? $_POST['address_line_1'] : 'none';
		$findusat_meta['address_line_2'] = isset($_POST['address_line_2']) ? $_POST['address_line_2'] : 'nope';
		$findusat_meta['x_coordinate'] = isset($_POST['x_coordinate']) ? $_POST['x_coordinate'] : 'nota';
		$findusat_meta['y_coordinate'] = isset($_POST['y_coordinate']) ? $_POST['y_coordinate'] : 'zip';

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
		wp_register_style( 'findusat_style', plugins_url( 'assets/css/findusat.css', __FILE__) );
		wp_enqueue_style( 'findusat_style' );
		wp_register_script( 'findusat_script', plugins_url('assets/js/findusat.js', __FILE__), array('jquery'), '1.1', true );
		wp_enqueue_script( 'findusat_script' );
	}

	/*
	 * load styles and js for wp-admin page
	 */
	function findusat_adding_admin_scripts()
	{
		global $googlemaps_api_key;

		wp_register_style( 'findusat_admin_style', plugins_url( 'assets/css/admin_findusat.css', __FILE__) );
		wp_enqueue_style( 'findusat_admin_style' );

		wp_register_script( 'findusat_google_maps_api', 'https://maps.googleapis.com/maps/api/js?key='.$googlemaps_api_key, '1', true );
		wp_enqueue_script( 'findusat_google_maps_api' );

		wp_register_script( 'findusat_admin_script', plugins_url('assets/js/admin_findusat.js', __FILE__), array('jquery', 'findusat_google_maps_api'), '1.1', true );
		wp_enqueue_script( 'findusat_admin_script' );
	}
	add_action( 'wp_ajax_getCoordinates', 'getCoordinates' );

	function getCoordinates()
	{
		$address = str_replace(" ", "+", $_POST['address']);
		
		$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";

		$response = file_get_contents($url);
		
		$json = json_decode($response,TRUE); //generate array object from the response from the web

		$lat_lng = array(
			'lat' => $json['results'][0]['geometry']['location']['lat'],
			'lng' => $json['results'][0]['geometry']['location']['lng']
		);

		echo $json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng'];
	}
?>