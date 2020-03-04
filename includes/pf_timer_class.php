<?php

class PFTimer {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		$pf_timer_post_lbls = apply_filters( 'pf_timer_post_labels',
		                                     array(
			                                     'name'               => __( 'PF Times', PF_TIMER_SLUG ),
			                                     'singular_name'      => __( 'WP Countdown Timer', PF_TIMER_SLUG ),
			                                     'add_new'            => __( 'Add Timer', PF_TIMER_SLUG ),
			                                     'add_new_item'       => __( 'Add New Timer', PF_TIMER_SLUG ),
			                                     'edit_item'          => __( 'Edit Timer', PF_TIMER_SLUG ),
			                                     'new_item'           => __( 'New Timer', PF_TIMER_SLUG ),
			                                     'view_item'          => __( 'View Timer', PF_TIMER_SLUG ),
			                                     'search_items'       => __( 'Search Timer', PF_TIMER_SLUG ),
			                                     'not_found'          => __( 'No Timer Found', PF_TIMER_SLUG ),
			                                     'not_found_in_trash' => __( 'No Timer Found in Trash', PF_TIMER_SLUG ),
			                                     'parent_item_colon'  => '',
			                                     'menu_name'          => __( 'PF Timer', PF_TIMER_SLUG )
		                                     ) );

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
		// Register pf_timer post type
		register_post_type( PF_TIMER_POST_TYPE, $pf_timer_args );

		add_action( 'admin_init',
			function () {
				remove_post_type_support( PF_TIMER_POST_TYPE, 'editor' );
			},
			        99 );

		add_shortcode( 'pf-timer', array( $this, 'pf_timer_shortcode' ) );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_pf_timer_admin_page' ) );

			add_action( 'plugin_action_links_' . PF_TIMER_PLUGIN_BASENAME, array( $this, 'pf_timer__action_links' ) );

			add_action( 'add_meta_boxes', array( $this, 'pf_timer_post_sett_metabox' ) );

			add_action( 'save_post', array( $this, 'pf_timer_save_metabox_value' ) );

			// Action to add style in backend
			add_action( 'admin_enqueue_scripts', array( $this, 'pf_timer_admin_style' ) );

			// Action to add script in backend
			add_action( 'admin_enqueue_scripts', array( $this, 'pf_timer_admin_script' ) );
		} else {
			// Action to add style at front side
			add_action( 'init', array( $this, 'pf_timer_front_style' ) );

			// Action to add script at front side
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
		register_activation_hook( PF_TIMER_BASE_FILE, array( $this, 'pf_timer_install' ) );

		/**
		 * Deactivation Hook
		 *
		 * Register plugin deactivation hook.
		 *
		 * @package PF Timer
		 * @since 1.0.0
		 */
		register_deactivation_hook( PF_TIMER_BASE_FILE, array( $this, 'pf_timer_uninstall' ) );
	}

	/**
	 * Plugin Setup (On Activation)
	 *
	 * Does the initial setup,
	 * stest default values for the plugin options.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	public function pf_timer_install() {
		// IMP need to flush rules for custom registered post type
		flush_rewrite_rules();
	}

	/**
	 * Plugin Setup (On Deactivation)
	 *
	 * Delete plugin options.
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */

	public function pf_timer_uninstall() {
		// IMP need to flush rules for custom registered post type
		flush_rewrite_rules();
	}

	public function pf_timer__action_links( $links ) {
		$link  = '<a href="' . esc_url( admin_url( '/edit.php?post_type=pf_timer' ) ) . '">';
		$links = array_merge( array( $link . __( 'Settings', 'textdomain' ) . '</a>' ), $links );

		return $links;
	}

	/**
	 * Function to add style at front side
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	function pf_timer_front_style() {
		// Registring default style
		wp_register_style( 'pf-front-style', PF_TIMER_URL . 'assets/css/pf-front-style.css', null, PF_TIMER_VERSION );
		wp_enqueue_style( 'pf-front-style' );
	}

	/**
	 * Function to add script at front side
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	function pf_timer_front_script() {
		// Registring default script
		wp_register_script( 'pf-front-script-js',
		                    PF_TIMER_URL . 'assets/js/pf-front-script.js',
		                    array( 'jquery' ),
		                    PF_TIMER_VERSION,
		                    true );
		wp_enqueue_script( 'pf-front-script-js' );
	}

	/**
	 * Enqueue admin styles
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	function pf_timer_admin_style( $hook ) {
		global $post_type;

		// If page is plugin setting page then enqueue script
		if ( $post_type == PF_TIMER_POST_TYPE ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_register_style( 'pf-ui-timepicker-addon',
			                   PF_TIMER_URL . 'assets/css/pf-ui-timepicker-addon.css',
			                   null,
			                   PF_TIMER_VERSION );
			wp_enqueue_style( 'pf-ui-timepicker-addon' );

			// Registring default style
			wp_register_style( 'pf-style', PF_TIMER_URL . 'assets/css/pf-style.css', null, PF_TIMER_VERSION );
			wp_enqueue_style( 'pf-style' );
		}
	}

	/**
	 * Enqueue admin script
	 *
	 * @package PF Timer Ultimate
	 * @since 1.0.0
	 */
	function pf_timer_admin_script( $hook ) {
		global $post_type;

		// If page is plugin setting page then enqueue script
		if ( $post_type == PF_TIMER_POST_TYPE ) {
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_register_script( 'pf-ui-timepicker-addon-js',
			                    PF_TIMER_URL . 'assets/js/pf-ui-timepicker-addon.js',
			                    array( 'jquery' ),
			                    PF_TIMER_VERSION,
			                    true );
			wp_enqueue_script( 'pf-ui-timepicker-addon-js' );


			// Registring default script
			wp_register_script( 'pf-script-js',
			                    PF_TIMER_URL . 'assets/js/pf-script.js',
			                    array( 'jquery' ),
			                    PF_TIMER_VERSION,
			                    true );
			wp_enqueue_script( 'pf-script-js' );
		}
	}


	/**
	 * Add options page
	 */
	public function add_pf_timer_admin_page() {
		$pf_timer_post_lbls = apply_filters( 'pf_timer_post_labels',
		                                     array(
			                                     'name'               => __( 'PF Times', PF_TIMER_SLUG ),
			                                     'singular_name'      => __( 'WP Countdown Timer', PF_TIMER_SLUG ),
			                                     'add_new'            => __( 'Add Timer', PF_TIMER_SLUG ),
			                                     'add_new_item'       => __( 'Add New Timer', PF_TIMER_SLUG ),
			                                     'edit_item'          => __( 'Edit Timer', PF_TIMER_SLUG ),
			                                     'new_item'           => __( 'New Timer', PF_TIMER_SLUG ),
			                                     'view_item'          => __( 'View Timer', PF_TIMER_SLUG ),
			                                     'search_items'       => __( 'Search Timer', PF_TIMER_SLUG ),
			                                     'not_found'          => __( 'No Timer Found', PF_TIMER_SLUG ),
			                                     'not_found_in_trash' => __( 'No Timer Found in Trash', PF_TIMER_SLUG ),
			                                     'parent_item_colon'  => '',
			                                     'menu_name'          => __( 'PF Timer', PF_TIMER_SLUG )
		                                     ) );

		$pf_timer_args = array(
			'labels'          => $pf_timer_post_lbls,
			'public'          => false,
			'show_ui'         => true,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'menu_icon'       => 'dashicons-clock',
			//'supports'				=> apply_filters('wpcdt_timer_post_supports', array('title')),
		);

		// Register pf_timer post type
		register_post_type( PF_TIMER_POST_TYPE, $pf_timer_args );
	}

	public function pf_timer_admin_list_page() {
		$this->options = get_option( 'pf_timer_option_name' );
		print_r( $this->options );
		//foreach()
	}


	function pf_timer_post_sett_metabox() {
		add_meta_box( 'pf-timer-post-sett',
		              __( 'PF Timer Settings - Settings', PF_TIMER_POST_TYPE ),
		              array( $this, 'pf_timer_post_sett_mb_content' ),
		              PF_TIMER_POST_TYPE,
		              'normal',
		              'high' );
	}

	function pf_timer_post_sett_mb_content() {
		include_once( PF_TIMER_DIR . '/includes/pf-timer-admin-create-metabox.php' );
	}


	function pf_timer_save_metabox_value( $post_id ) {
		update_post_meta( $post_id,
		                  'pf_timer_subtitle',
		                  isset( $_POST['pf_timer_subtitle'] ) ? $_POST['pf_timer_subtitle'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_expiry_date',
		                  isset( $_POST['pf_timer_expiry_date'] ) ? $_POST['pf_timer_expiry_date'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_timezone',
		                  isset( $_POST['pf_timer_timezone'] ) ? $_POST['pf_timer_timezone'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_redirect_url',
		                  isset( $_POST['pf_timer_redirect_url'] ) ? $_POST['pf_timer_redirect_url'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_border_radius',
		                  isset( $_POST['pf_timer_border_radius'] ) ? $_POST['pf_timer_border_radius'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_bg_color',
		                  isset( $_POST['pf_timer_bg_color'] ) ? $_POST['pf_timer_bg_color'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_font_color',
		                  isset( $_POST['pf_timer_font_color'] ) ? $_POST['pf_timer_font_color'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_font_size',
		                  isset( $_POST['pf_timer_font_size'] ) ? $_POST['pf_timer_font_size'] : '' );
		update_post_meta( $post_id,
		                  'pf_timer_days_label',
		                  isset( $_POST['pf_timer_days_label'] ) ? $_POST['pf_timer_days_label'] : 'Days' );
		update_post_meta( $post_id,
		                  'pf_timer_days_label_show',
		                  isset( $_POST['pf_timer_days_label_show'] ) ? $_POST['pf_timer_days_label_show'] : '0' );
		update_post_meta( $post_id,
		                  'pf_timer_hours_label',
		                  isset( $_POST['pf_timer_hours_label'] ) ? $_POST['pf_timer_hours_label'] : 'Hours' );
		update_post_meta( $post_id,
		                  'pf_timer_hours_label_show',
		                  isset( $_POST['pf_timer_hours_label_show'] ) ? $_POST['pf_timer_hours_label_show'] : '0' );
		update_post_meta( $post_id,
		                  'pf_timer_minutes_label',
		                  isset( $_POST['pf_timer_minutes_label'] ) ? $_POST['pf_timer_minutes_label'] : 'Minutes' );
		update_post_meta( $post_id,
		                  'pf_timer_minutes_label_show',
		                  isset( $_POST['pf_timer_minutes_label_show'] ) ? $_POST['pf_timer_minutes_label_show'] : '0' );
		update_post_meta( $post_id,
		                  'pf_timer_seconds_label',
		                  isset( $_POST['pf_timer_seconds_label'] ) ? $_POST['pf_timer_seconds_label'] : 'Seconds' );
		update_post_meta( $post_id,
		                  'pf_timer_seconds_label_show',
		                  isset( $_POST['pf_timer_seconds_label_show'] ) ? $_POST['pf_timer_seconds_label_show'] : '0' );
	}

	public function pf_timer_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			                         'id' => '',
		                         ),
		                         $atts,
		                         'pf-timer' ) );

		$id                    = ! empty( $id ) ? $id : '';
		$pf_timer_expiry_date  = get_post_meta( $id, 'pf_timer_expiry_date', true );
		$pf_timer_timezone     = get_post_meta( $id, 'pf_timer_timezone', true );
		$pf_timer_redirect_url = get_post_meta( $id, 'pf_timer_redirect_url', true );

		$pf_timer_days_label       = get_post_meta( $id, 'pf_timer_days_label', true );
		$pf_timer_days_label_show  = get_post_meta( $id, 'pf_timer_days_label_show', true );
		$pf_timer_days_label_class = ( $pf_timer_days_label_show == 0 ) ? 'hide' : '';

		$pf_timer_hours_label       = get_post_meta( $id, 'pf_timer_hours_label', true );
		$pf_timer_hours_label_show  = get_post_meta( $id, 'pf_timer_hours_label_show', true );
		$pf_timer_hours_label_class = ( $pf_timer_hours_label_show == 0 ) ? 'hide' : '';

		$pf_timer_minutes_label       = get_post_meta( $id, 'pf_timer_minutes_label', true );
		$pf_timer_minutes_label_show  = get_post_meta( $id, 'pf_timer_minutes_label_show', true );
		$pf_timer_minutes_label_class = ( $pf_timer_minutes_label_show == 0 ) ? 'hide' : '';

		$pf_timer_seconds_label       = get_post_meta( $id, 'pf_timer_seconds_label', true );
		$pf_timer_seconds_label_show  = get_post_meta( $id, 'pf_timer_seconds_label_show', true );
		$pf_timer_seconds_label_class = ( $pf_timer_seconds_label_show == 0 ) ? 'hide' : '';

		$pf_timer_bg_color      = get_post_meta( $id, 'pf_timer_bg_color', true );
		$pf_timer_font_color    = get_post_meta( $id, 'pf_timer_font_color', true );
		$pf_timer_font_size     = get_post_meta( $id, 'pf_timer_font_size', true );
		$pf_timer_border_radius = get_post_meta( $id, 'pf_timer_border_radius', true );

		$pf_timer_box_width  = ( $pf_timer_font_size * 2 ) + 40;
		$pf_timer_ul_width   = $pf_timer_box_width * 4 + ( 6 * 4 ) + 10;
		$pf_timer_box_height = ( $pf_timer_font_size * 2 ) + 40;

		$pf_timer_labelfont_size = intval($pf_timer_font_size * 0.40);

		$tz_obj    = new DateTimeZone( $pf_timer_timezone );
		$today     = new DateTime( "now", $tz_obj );
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
            <p>'.$pf_timer_days_label.'</p>
        </li>
        <li class="' . $pf_timer_hours_label_class . '">
            <div class="flap">
                <span class="hours">0</span>
            </div>
            <p>'.$pf_timer_hours_label.'</p>
        </li>
        <li class="' . $pf_timer_minutes_label_class . '">
            <div class="flap">
                <span class="minutes">0</span>
            </div>
            <p>'.$pf_timer_minutes_label.'</p>
        </li>
        <li class="' . $pf_timer_seconds_label_class . '">
            <div class="flap">
                <span class="seconds">0</span>
            </div>
            <p>'.$pf_timer_seconds_label.'</p>
        </li>
    </ul>
</div>
<div class="clearfix"></div>';

		return $timer;
	}
}
