<?php
/**
 * Uninstall plugin
 *
 * @package PFTimer
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// delete/trash custom posts type if defined in plugin.
