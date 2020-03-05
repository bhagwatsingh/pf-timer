<?php
/**
 * PF Timer Plugin
 *
 * @package PFTimer
 * @copyright Copyright (C) 2020, Profit-Funnels.In
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: PF Timer Plugin
 * Version: 1.0.0
 * Plugin URI: https://plugins.profit-funnels.in/
 * Description: PF Countdown Timer Plugin
 * Author:      Team Profit-Funnels
 * Author URI:  https://Profit-Funnels.In
 * Text Domain: pf-timer
 * Domain Path: /languages/
 * License:     GPL v3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Plugin constants
 *
 * @package pf-timer
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '' ); // don't load directly.
}

if ( ! defined( 'PF_TIMER_PLUGIN_VERSION' ) ) {
	define( 'PF_TIMER_PLUGIN_VERSION', '1.0.0' ); // // Current version of plugin.
}
if ( ! defined( 'PF_TIMER_PLUGIN_BASE_FILE' ) ) {
	define( 'PF_TIMER_PLUGIN_BASE_FILE', __FILE__ ); // Current plugin file with path.
}
if ( ! defined( 'PF_TIMER_PLUGIN_DIR' ) ) {
	define( 'PF_TIMER_PLUGIN_DIR', dirname( PF_TIMER_PLUGIN_BASE_FILE ) ); // Current plugin dir path.
}

if ( ! defined( 'PF_TIMER_PLUGIN_URL' ) ) {
	define( 'PF_TIMER_PLUGIN_URL', plugin_dir_url( PF_TIMER_PLUGIN_BASE_FILE ) ); // Current plugin dir url.
}
if ( ! defined( 'PF_TIMER_PLUGIN_PLUGIN_BASENAME' ) ) {
	define( 'PF_TIMER_PLUGIN_PLUGIN_BASENAME', plugin_basename( PF_TIMER_PLUGIN_BASE_FILE ) ); // plugin file with current plugin directory.
}

require_once PF_TIMER_PLUGIN_DIR . '/includes/class-pftimer.php';
$pf_timer = new PFTimer();
