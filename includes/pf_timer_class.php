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
		add_action( 'admin_menu', array( $this, 'add_pf_timer_admin_page' ) );

		add_action( 'plugin_action_links_' . PF_TIMER_PLUGIN_BASENAME, array( $this, 'pf_timer__action_links' ) );

		add_action( 'add_meta_boxes', array( $this, 'pf_timer_post_sett_metabox' ) );

		add_action( 'save_post', array( $this, 'pf_timer_save_metabox_value' ) );
		// Action to add style at front side
		add_action( 'wp_enqueue_scripts', array( $this, 'pf_timer_front_style' ) );

		// Action to add script at front side
		add_action( 'wp_enqueue_scripts', array( $this, 'pf_timer_front_script' ) );

		// Action to add style in backend
		add_action( 'admin_enqueue_scripts', array( $this, 'pf_timer_admin_style' ) );

		// Action to add script in backend
		add_action( 'admin_enqueue_scripts', array( $this, 'pf_timer_admin_script' ) );

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
		$links = array_merge( array(
			                      '<a href="' . esc_url( admin_url( '/edit.php?post_type=pf_timer' ) ) . '">' . __( 'Settings',
			                                                                                                        'textdomain' ) . '</a>'
		                      ),
		                      $links );

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
		wp_register_style( 'pf-style', PF_TIMER_URL . 'assets/css/pf-style.css', null, PF_TIMER_VERSION );
		wp_enqueue_style( 'pf-style' );
	}

	/**
	 * Function to add script at front side
	 *
	 * @package PF Timer
	 * @since 1.0.0
	 */
	function pf_timer_front_script() {
		// Registring default script
		wp_register_script( 'pf-script-js',
		                    PF_TIMER_URL . 'assets/js/pf_script.js',
		                    array( 'jquery' ),
		                    PF_TIMER_VERSION,
		                    true );
		wp_enqueue_script( 'pf-script-js' );
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
	}
}
