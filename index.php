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
	define( 'PF_TIMER_VERSION', '1.0.0' ); // Version of plugin
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

if ( is_admin() ) {
	$pf_timer = new PFTimer();
}

$pf_timer_post_lbls = apply_filters( 'pf_timer_post_labels', array(
	'name'                 	=> __('PF Times', PF_TIMER_SLUG),
	'singular_name'        	=> __('WP Countdown Timer', PF_TIMER_SLUG),
	'add_new'              	=> __('Add Timer',PF_TIMER_SLUG),
	'add_new_item'         	=> __('Add New Timer', PF_TIMER_SLUG),
	'edit_item'            	=> __('Edit Timer', PF_TIMER_SLUG),
	'new_item'             	=> __('New Timer', PF_TIMER_SLUG),
	'view_item'            	=> __('View Timer', PF_TIMER_SLUG),
	'search_items'         	=> __('Search Timer', PF_TIMER_SLUG),
	'not_found'            	=> __('No Timer Found', PF_TIMER_SLUG),
	'not_found_in_trash'   	=> __('No Timer Found in Trash', PF_TIMER_SLUG),
	'parent_item_colon'    	=> '',
	'menu_name'           	=> __('PF Timer', PF_TIMER_SLUG)
));

$pf_timer_args = array(
	'labels'				=> $pf_timer_post_lbls,
	'public'				=> false,
	'show_ui'				=> true,
	'query_var'				=> false,
	'rewrite'				=> false,
	'capability_type'		=> 'post',
	'hierarchical'			=> false,
	'menu_icon'				=> 'dashicons-clock',
	'supports' => array( 'title'),
);

// Register pf_timer post type
register_post_type( PF_TIMER_POST_TYPE,  $pf_timer_args );

add_action( 'admin_init', function() {
	remove_post_type_support( PF_TIMER_POST_TYPE, 'editor' );

}, 99);


