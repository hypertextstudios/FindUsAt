=== Find Us At ===
Contributors: hypertextstudios, kaser
Donate link: http://hypertext-studios.com/
Tags: locations, store location, where to find us, brick & mortar, dealerships, dealers, find us at, we're in these stores, list the stores that your product is in
Requires at least: 4.9
Tested up to: 4.9
Stable tag: 1.2.1
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Quickly display a map of the locations that your product is in or were your stores are located.

== Description ==

Create a google map with different location markers and information associated with it to help drive your customers to the store to pick up your product!

Just insert the address and name of each location and save each, and insert the [findusat] shortcode on any page or widget area! 

== Installation ==

Thanks for checking out FindUsAt! Set up is super simple and only takes a few minutes:

1. Upload the plugin files to the `/wp-content/plugins/findusat` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->FindUsAt screen to configure the plugin
1. copy & paste [findusat] into a page to display a map of all your locations.
1. copy & paste [findusat_locations] into a page to display a list of all your locations.


== Frequently Asked Questions ==

= Do I Need A Google Maps API Key? =

Yes, you can get started here: https://developers.google.com/maps/documentation/javascript/get-api-key.

= How many locations can I have? =

You can have as many as you want!

= How are you so awesome? =

I just am!

== Screenshots ==

1. Convert the address of your location to coordinates for google maps
2. Display the map with your locations by use of a shimple shortcode

== Changelog ==

= 1.2.1 =
* Fixed [findusat_locations] shortcode to actually output the data, sorry about that.

= 1.2 =
* Code changes for handling output
* Updated some styles
* Added correct information about the author
* Added check for map element before running javascript
* split the map and the list of locations into two shortcodes

= 1.1 =
* Auto zoom and pan map to fit all markers
* Added list of locations under map
* Added infoWindows for each location with the_content()
* Fixed empty values for x & y coordinates

= 1.0 =
* MVP version-
* Allows user to set Google Maps API Key.
* Set up Custom Post Type 'locations' 
* Metabox to convert address to coordinates
* Shortcode to display map with all locations
* Width and Height attributes for shortcode

== Upgrade Notice ==

= 1.2.1 =
Fixed an error that prevents shortcode from outputting data!

= 1.2 =
Fixed some errors and made it better :)

= 1.1 =
Lots of new google maps features included and some bug fixes!

= 1.0 =
This is the MVP, baby! It's all up hill from here.
