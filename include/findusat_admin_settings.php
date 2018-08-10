<?php

/*
 * Add link to options page into the Settings menu
 */
function findusat_menu()
{
	add_submenu_page('options-general.php', 'Find Us At', 'Find Us At', 'manage_options', 'findusat', 'findusat_options' );
}

?>