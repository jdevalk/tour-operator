<?php
/*
 * Plugin Name: Tour Operator Plugin
 * Plugin URI: https://www.lsdev.biz/product/tour-operator-plugin/
 * Description: The Tour Operator plugin core contains the Accommodation, Destination and Tour post types. Use these core post types to build day-by-day tour itineraries that map out of the progress of each tour through the various accommodations and destinations that are stayed at along the way.
 * Tags: tour operator, tour operators, tour, tours, tour itinerary, tour itineraries, accommodation, accommodation listings, destinations, regions, tourism, lsx
 * Author: LightSpeed
 * Version: 1.0.3
 * Author URI: https://www.lsdev.biz/
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: tour-operator
 * Domain Path: /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('LSX_TO_PATH',  plugin_dir_path( __FILE__ ) );
define('LSX_TO_CORE',  __FILE__ );
define('LSX_TO_URL',  plugin_dir_url( __FILE__ ) );
define('LSX_TO_VER',  '1.0.3' );

register_activation_hook( LSX_TO_CORE, array( 'Tour_Operator', 'register_activation_hook' ) );

require_once( LSX_TO_PATH . 'module.php' );
