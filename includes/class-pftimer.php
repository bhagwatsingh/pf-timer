<?php
/**
 * PFTimer main class
 *
 * @category Class
 * @package PFTimer
 * @since 1.0.0
 * @subpackage PFTimer/includes
 * @author Team Profit-Funnels
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Class main
 *
 * @since    1.0.0
 */
class PFTimer {
	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var options
	 */
	private $options;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_post_type' ) );
		add_action(
			'admin_init',
			function () {
				remove_post_type_support( 'pf-timer', 'editor' );
			},
			99
		);

		add_shortcode( 'pf-timer', array( $this, 'pf_timer_short_code' ) );

		if ( is_admin() ) {

			add_action( 'admin_menu', array( $this, 'add_pf_timer_admin_page' ) );
			// Add Short Column to post list.
			add_filter( 'manage_pf-timer_posts_columns', array( $this, 'pf_timer_columns' ) );
			add_action( 'manage_pf-timer_posts_custom_column', array( $this, 'pf_timer_column' ), 10, 2 );

			// Add Action links to plugins list.
			add_action( 'plugin_action_links_' . PF_TIMER_PLUGIN_PLUGIN_BASENAME, array(
				$this,
				'pf_timer__action_links'
			) );

			// Add metaboxes for timer settings.
			add_action( 'add_meta_boxes', array( $this, 'pf_timer_post_sett_metabox' ) );

			// Save custom post data.
			add_action( 'save_post', array( $this, 'pf_timer_save_metabox_value' ) );

			// Action to add style in backend.
			add_action( 'admin_enqueue_scripts', array( $this, 'pf_timer_admin_style' ) );

			// Action to add script in backend.
			add_action( 'admin_enqueue_scripts', array( $this, 'pf_timer_admin_script' ) );
		} else {
			// Action to add style at front side.
			add_action( 'init', array( $this, 'pf_timer_front_style' ) );

			// Action to add script at front side.
			add_action( 'wp_enqueue_scripts', array( $this, 'pf_timer_front_script' ) );
		}

		/**
		 * Activation Hook
		 *
		 * Register plugin activation hook.
		 *
		 * @package PF Timer
		 * @since 1.0.0
		 */
		register_activation_hook( PF_TIMER_PLUGIN_BASE_FILE, array( $this, 'pf_timer_install' ) );

		/**
		 * Deactivation Hook
		 *
		 * Register plugin deactivation hook.
		 *
		 * @package PF Timer
		 * @since 1.0.0
		 */
		register_deactivation_hook( PF_TIMER_PLUGIN_BASE_FILE, array( $this, 'pf_timer_uninstall' ) );
	}

	/**
	 * Create Custom Post Type
	 *
	 * @since    1.0.0
	 */
	public function create_post_type() {
		// Create pf_timer post labels.
		$pf_timer_post_lbls = apply_filters(
			'pf_timer_post_labels',
			array(
				'name'               => __( 'PF Timer', 'pf-timer' ),
				'singular_name'      => __( 'WP Countdown Timer', 'pf-timer' ),
				'add_new'            => __( 'Add PF Timer', 'pf-timer' ),
				'add_new_item'       => __( 'Add New PF Timer', 'pf-timer' ),
				'edit_item'          => __( 'Edit PF Timer', 'pf-timer' ),
				'new_item'           => __( 'New PF Timer', 'pf-timer' ),
				'view_item'          => __( 'View PF Timer', 'pf-timer' ),
				'search_items'       => __( 'Search PF Timer', 'pf-timer' ),
				'not_found'          => __( 'No PF Timer Found', 'pf-timer' ),
				'not_found_in_trash' => __( 'No PF Timer Found in Trash', 'pf-timer' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'PF Timer', 'pf-timer' ),
			)
		);

		$pf_timer_args = array(
			'labels'          => $pf_timer_post_lbls,
			'public'          => false,
			'show_ui'         => true,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'menu_icon'       => 'dashicons-clock',
			'supports'        => array( 'title' ),
		);

		// Register pf_timer post type.
		register_post_type( 'pf-timer', $pf_timer_args );
	}

	/**
	 * Plugin Setup (On Activation)
	 *
	 * Does the initial setup.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_install() {
		$this->create_post_type();
		// IMP need to flush rules for custom registered post type.
		flush_rewrite_rules();
	}

	/**
	 * Plugin Setup (On Deactivation)
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_uninstall() {
		// IMP need to flush rules for custom registered post type.
		flush_rewrite_rules();
	}

	/**
	 * Function to add action link at plugin list.
	 *
	 * @param string $links action links.
	 *
	 * @return string $links action links.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer__action_links( $links = '' ) {
		$link  = '<a href="' . esc_url( admin_url( '/edit.php?post_type=pf-timer' ) ) . '">';
		$links = array_merge( array( $link . __( 'Settings', 'textdomain' ) . '</a>' ), $links );

		return $links;
	}

	/**
	 * Function to add style at front side.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_front_style() {
		// Registring default style.
		wp_register_style( 'pf-front-style', PF_TIMER_PLUGIN_URL . 'assets/css/pf-front-style.css', null, PF_TIMER_PLUGIN_VERSION );
		wp_enqueue_style( 'pf-front-style' );
	}

	/**
	 * Function to add script at front side.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_front_script() {
		// Registring default script.
		wp_register_script(
			'pf-front-script-js',
			PF_TIMER_PLUGIN_URL . 'assets/js/pf-front-script.js',
			array( 'jquery' ),
			PF_TIMER_PLUGIN_VERSION,
			true
		);
		wp_enqueue_script( 'pf-front-script-js' );
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook hook action.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_admin_style( $hook ) {
		global $post_type;
		// If page is plugin setting page then enqueue script.
		if ( 'pf-timer' === $post_type ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_register_style(
				'pf-ui-timepicker-addon',
				PF_TIMER_PLUGIN_URL . 'assets/css/pf-ui-timepicker-addon.css',
				null,
				PF_TIMER_PLUGIN_VERSION
			);
			wp_enqueue_style( 'pf-ui-timepicker-addon' );

			// Registring default style.
			wp_register_style( 'pf-style', PF_TIMER_PLUGIN_URL . 'assets/css/pf-style.css', null, PF_TIMER_PLUGIN_VERSION );
			wp_enqueue_style( 'pf-style' );
		}
	}

	/**
	 * Enqueue admin script.
	 *
	 * @param string $hook hook action.
	 *
	 * @package PF Timer Ultimate
	 * @since 1.0.0
	 */
	public function pf_timer_admin_script( $hook ) {
		global $post_type;

		// If page is plugin setting page then enqueue script.
		if ( 'pf-timer' === $post_type ) {
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_register_script(
				'pf-ui-timepicker-addon-js',
				PF_TIMER_PLUGIN_URL . 'assets/js/pf-ui-timepicker-addon.js',
				array( 'jquery' ),
				PF_TIMER_PLUGIN_VERSION,
				true
			);
			wp_enqueue_script( 'pf-ui-timepicker-addon-js' );

			// Registring default script.
			wp_register_script(
				'pf-script-js',
				PF_TIMER_PLUGIN_URL . 'assets/js/pf-script.js',
				array( 'jquery' ),
				PF_TIMER_PLUGIN_VERSION,
				true
			);
			wp_enqueue_script( 'pf-script-js' );
		}
	}

	/**
	 * Function to Add options page.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function add_pf_timer_admin_page() {
		$pf_timer_post_lbls = apply_filters(
			'pf_timer_post_labels',
			array(
				'name'               => __( 'PF Timer', 'pf-timer' ),
				'singular_name'      => __( 'PF  Timer', 'pf-timer' ),
				'add_new'            => __( 'Add PF Timer', 'pf-timer' ),
				'add_new_item'       => __( 'Add New PF Timer', 'pf-timer' ),
				'edit_item'          => __( 'Edit PF Timer', 'pf-timer' ),
				'new_item'           => __( 'New PF Timer', 'pf-timer' ),
				'view_item'          => __( 'View PF Timer', 'pf-timer' ),
				'search_items'       => __( 'Search PF Timer', 'pf-timer' ),
				'not_found'          => __( 'No PF Timer Found', 'pf-timer' ),
				'not_found_in_trash' => __( 'No PF Timer Found in Trash', 'pf-timer' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'PF Timer', 'pf-timer' ),
			)
		);

		$pf_timer_args = array(
			'labels'          => $pf_timer_post_lbls,
			'public'          => false,
			'show_ui'         => true,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'menu_icon'       => 'dashicons-clock',
		);

		// Register pf_timer post type.
		register_post_type( 'pf-timer', $pf_timer_args );
	}

	/**
	 * Function to Add Column in Admin post list.
	 *
	 * @param string $columns custom post list columns.
	 *
	 * @return string $columns custom post list columns.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_columns( $columns ) {
		$columns = array(
			'cb'        => $columns['cb'],
			'title'     => __( 'Title' ),
			'shortcode' => __( 'Shortcode', 'pf-timer' ),
		);

		return $columns;
	}

	/**
	 * Function to Add Column description in Admin post list.
	 *
	 * @param string $column Post List Column for ShortCode.
	 * @param integer $post_id Post Id.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_column( $column, $post_id ) {
		// shortcode column.
		if ( 'shortcode' === $column ) {
			echo '[pf-timer id="' . sanitize_key( $post_id ) . '"]';
		}
	}


	/**
	 * Function to set metabox.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_post_sett_metabox() {
		add_meta_box(
			'pf-timer-post-sett',
			__( 'PF Timer Settings', 'pf-timer' ),
			array( $this, 'pf_timer_post_sett_mb_content' ),
			'pf-timer',
			'normal',
			'high'
		);
	}

	/**
	 * Function to add metabox template
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_post_sett_mb_content() {
		include_once PF_TIMER_PLUGIN_DIR . '/includes/pf-timer-admin-create-metabox.php';
	}

	/**
	 * Function to set metabox values
	 *
	 * @param integer $post_id Post Id.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_save_metabox_value( $post_id ) {
		update_post_meta(
			$post_id,
			'pf_timer_subtitle',
			isset( $_POST['pf_timer_subtitle'] ) ? sanitize_text_field( $_POST['pf_timer_subtitle'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_expiry_date',
			isset( $_POST['pf_timer_expiry_date'] ) ? sanitize_text_field( $_POST['pf_timer_expiry_date'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_timezone',
			isset( $_POST['pf_timer_timezone'] ) ? sanitize_text_field( $_POST['pf_timer_timezone'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_redirect_url',
			isset( $_POST['pf_timer_redirect_url'] ) ? sanitize_text_field( $_POST['pf_timer_redirect_url'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_border_radius',
			isset( $_POST['pf_timer_border_radius'] ) ? sanitize_text_field( $_POST['pf_timer_border_radius'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_bg_color',
			isset( $_POST['pf_timer_bg_color'] ) ? sanitize_text_field( $_POST['pf_timer_bg_color'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_font_color',
			isset( $_POST['pf_timer_font_color'] ) ? sanitize_text_field( $_POST['pf_timer_font_color'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_font_size',
			isset( $_POST['pf_timer_font_size'] ) ? sanitize_text_field( $_POST['pf_timer_font_size'] ) : ''
		);
		update_post_meta(
			$post_id,
			'pf_timer_days_label',
			isset( $_POST['pf_timer_days_label'] ) ? sanitize_text_field( $_POST['pf_timer_days_label'] ) : 'Days'
		);
		update_post_meta(
			$post_id,
			'pf_timer_days_label_show',
			isset( $_POST['pf_timer_days_label_show'] ) ? sanitize_text_field( $_POST['pf_timer_days_label_show'] ) : '0'
		);
		update_post_meta(
			$post_id,
			'pf_timer_hours_label',
			isset( $_POST['pf_timer_hours_label'] ) ? sanitize_text_field( $_POST['pf_timer_hours_label'] ) : 'Hours'
		);
		update_post_meta(
			$post_id,
			'pf_timer_hours_label_show',
			isset( $_POST['pf_timer_hours_label_show'] ) ?  sanitize_text_field( $_POST['pf_timer_hours_label_show'] )  : '0'
		);
		update_post_meta(
			$post_id,
			'pf_timer_minutes_label',
			isset( $_POST['pf_timer_minutes_label'] ) ? sanitize_text_field( $_POST['pf_timer_minutes_label'] )  : 'Minutes'
		);
		update_post_meta(
			$post_id,
			'pf_timer_minutes_label_show',
			isset( $_POST['pf_timer_minutes_label_show'] ) ?  sanitize_text_field( $_POST['pf_timer_minutes_label_show'] )  : '0'
		);
		update_post_meta(
			$post_id,
			'pf_timer_seconds_label',
			isset( $_POST['pf_timer_seconds_label'] ) ?  sanitize_text_field( $_POST['pf_timer_seconds_label'] )  : 'Seconds'
		);
		update_post_meta(
			$post_id,
			'pf_timer_seconds_label_show',
			isset( $_POST['pf_timer_seconds_label_show'] ) ?  sanitize_text_field( $_POST['pf_timer_seconds_label_show'] )  : '0'
		);
	}

	/**
	 * Function to create dynamic timer for pf_timer.
	 *
	 * @param array $attributes Attributes for the ShortCode.
	 * @param string $content Post content.
	 *
	 * @return string $timer Complete Timer HTML.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_short_code( $attributes, $content = null ) {
		$args = shortcode_atts(
			array(
				'id' => '',
			),
			$attributes,
			'pf-timer'
		);

		$id                    = ! empty( $args['id'] ) ? $args['id'] : '';
		$pf_timer_expiry_date  = get_post_meta( $id, 'pf_timer_expiry_date', true );
		$pf_timer_timezone     = get_post_meta( $id, 'pf_timer_timezone', true );
		$pf_timer_redirect_url = get_post_meta( $id, 'pf_timer_redirect_url', true );

		$pf_timer_days_label       = get_post_meta( $id, 'pf_timer_days_label', true );
		$pf_timer_days_label_show  = intval( get_post_meta( $id, 'pf_timer_days_label_show', true ) );
		$pf_timer_days_label_class = ( 0 === $pf_timer_days_label_show ) ? 'hide' : '';

		$pf_timer_hours_label       = get_post_meta( $id, 'pf_timer_hours_label', true );
		$pf_timer_hours_label_show  = intval( get_post_meta( $id, 'pf_timer_hours_label_show', true ) );
		$pf_timer_hours_label_class = ( 0 === $pf_timer_hours_label_show ) ? 'hide' : '';

		$pf_timer_minutes_label       = get_post_meta( $id, 'pf_timer_minutes_label', true );
		$pf_timer_minutes_label_show  = intval( get_post_meta( $id, 'pf_timer_minutes_label_show', true ) );
		$pf_timer_minutes_label_class = ( 0 === $pf_timer_minutes_label_show ) ? 'hide' : '';

		$pf_timer_seconds_label       = get_post_meta( $id, 'pf_timer_seconds_label', true );
		$pf_timer_seconds_label_show  = intval( get_post_meta( $id, 'pf_timer_seconds_label_show', true ) );
		$pf_timer_seconds_label_class = ( 0 === $pf_timer_seconds_label_show ) ? 'hide' : '';

		$pf_timer_bg_color      = get_post_meta( $id, 'pf_timer_bg_color', true );
		$pf_timer_font_color    = get_post_meta( $id, 'pf_timer_font_color', true );
		$pf_timer_font_size     = get_post_meta( $id, 'pf_timer_font_size', true );
		$pf_timer_border_radius = get_post_meta( $id, 'pf_timer_border_radius', true );

		$pf_timer_box_width  = ( $pf_timer_font_size * 2 ) + 40;
		$pf_timer_ul_width   = $pf_timer_box_width * 4 + ( 6 * 4 ) + 10;
		$pf_timer_box_height = ( $pf_timer_font_size * 2 ) + 40;

		$pf_timer_labelfont_size = intval( $pf_timer_font_size * 0.50 );

		$tz_obj    = new DateTimeZone( $pf_timer_timezone );
		$today     = new DateTime( 'now', $tz_obj );
		$starttime = strtotime( $today->format( 'Y-m-d H:i:s' ) );
		$endtime   = strtotime( $pf_timer_expiry_date );
		$remaining = $endtime - $starttime;
		$timer     = '<style>.pf-timer-container ul {width: ' . $pf_timer_ul_width . 'px !important;} #pf-timer-' . $id . ' ul li .flap { background-color: ' . $pf_timer_bg_color . ' !important; border-radius: ' . $pf_timer_border_radius . 'px !important;width: ' . $pf_timer_box_width . 'px !important;height: ' . $pf_timer_box_height . 'px !important; } #pf-timer-' . $id . ' ul li .flap span {color: ' . $pf_timer_font_color . ' !important;font-size: ' . $pf_timer_font_size . 'px !important;line-height: ' . $pf_timer_font_size . 'px !important;} #pf-timer-' . $id . ' ul p {color: ' . $pf_timer_bg_color . ' !important;font-size: ' . $pf_timer_labelfont_size . 'px !important;line-height: ' . $pf_timer_labelfont_size . 'px !important;padding: ' . $pf_timer_labelfont_size . 'px 0 !important;}</style>';
		$timer     .= '<div id="pf-timer-' . $id . '" class="pf-timer-container" data-timestamp="' . $remaining . '" data-redirect="' . $pf_timer_redirect_url . '">
    <ul>
        <li class="' . $pf_timer_days_label_class . '">
            <div class="flap">
                <span class="days">0</span>
            </div>
            <p>' . $pf_timer_days_label . '</p>
        </li>
        <li class="' . $pf_timer_hours_label_class . '">
            <div class="flap">
                <span class="hours">0</span>
            </div>
            <p>' . $pf_timer_hours_label . '</p>
        </li>
        <li class="' . $pf_timer_minutes_label_class . '">
            <div class="flap">
                <span class="minutes">0</span>
            </div>
            <p>' . $pf_timer_minutes_label . '</p>
        </li>
        <li class="' . $pf_timer_seconds_label_class . '">
            <div class="flap">
                <span class="seconds">0</span>
            </div>
            <p>' . $pf_timer_seconds_label . '</p>
        </li>
    </ul>
</div>
<div class="clearfix"></div>';

		return $timer;
	}
}
