<?php

/*
 * Add link to options page into the Settings menu
 */
function findusat_menu()
{
	add_submenu_page(
		'options-general.php',
		'Find Us At',
		'Find Us At',
		'manage_options',
		'findusat',
		'findusat_options'
	);
}

/*
 * output the settings page for users to configure FindUsAt
 */
function findusat_options()
{
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div><h1 class="wp-heading-inline">Find Us At - Settings</h1>
		<p>Please configure the plugin to operate how you'd like it to. If you have any suggestions please shoot them over to kaser@cssboss.com</p>
		<form method="post" action="options.php">
			<?php

				settings_fields( 'findusat' );
				do_settings_sections( 'findusat' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/*
 * set up Sections, Settings, and Fields
 */

function findusat_settings_api_init()
{
	add_settings_section(
		'findusat_global_options',
		'Global Options',
		'findusat_global_settings_func',
		'findusat'
	);

	add_settings_field(
		'findusat_googlemaps_api_key',
		'Google Maps API Key',
		'findusat_googlemaps_api_key_setting_func',
		'findusat',
		'findusat_global_options'
	);

	register_setting(
		'findusat',
		'findusat_googlemaps_api_key'
	);
}

add_action( 'admin_init', 'findusat_settings_api_init' );

function findusat_googlemaps_api_key_setting_func()
{
	$options = get_option ( 'findusat_googlemaps_api_key' );
	$value = isset ( $options ['findusat_global_options'] ) ? $options ['findusat_global_options'] : $options;
	echo '<input type="text" placeholder="Please Insert Your Google Maps API Key" name="findusat_googlemaps_api_key" value="' . $options . '">';
}

function findusat_global_settings_func()
{
	echo " Please fill out the options below!";
}
?>