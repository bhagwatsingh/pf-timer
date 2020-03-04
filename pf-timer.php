<?php
/**
 * Plugin Name: PF Timer Plugin
 * Plugin URI: https://plugins.profit-funnels.in/
 * Description: Countdown Timer Plugin
 * Version: 1.0
 * Author: Team Profit-Funnels
 * Author URI: https://profit-funnels.in
 */

if ( ! defined( 'PF_TIMER_VERSION' ) ) {
	define( 'PF_TIMER_VERSION', '1.0.1' ); // Version of plugin
}
if ( ! defined( 'PF_TIMER_DIR' ) ) {
	define( 'PF_TIMER_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if ( ! defined( 'PF_TIMER_BASE_FILE' ) ) {
	define( 'PF_TIMER_BASE_FILE', __FILE__ ); // Plugin dir
}
if ( ! defined( 'PF_TIMER_URL' ) ) {
	define( 'PF_TIMER_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if ( ! defined( 'PF_TIMER_PLUGIN_BASENAME' ) ) {
	define( 'PF_TIMER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // plugin base name
}
if ( ! defined( 'PF_TIMER_POST_TYPE' ) ) {
	define( 'PF_TIMER_POST_TYPE', 'pf_timer' ); // Plugin post type
}

if ( ! defined( 'PF_TIMER_SLUG' ) ) {
	define( 'PF_TIMER_SLUG', PF_TIMER_POST_TYPE ); // Plugin post type
}
if ( ! defined( 'PF_TIMER_META_PREFIX' ) ) {
	define( 'PF_TIMER_META_PREFIX', '_pftimer_' ); // Plugin metabox prefix
}



require_once( PF_TIMER_DIR . '/includes/pf_timer_class.php' );

$pf_timer = new PFTimer();
