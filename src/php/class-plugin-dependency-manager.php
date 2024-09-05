<?php
/**
 * Plugin Checker
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Dev_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WP_Dev_Utils\Plugin_Dependency_Manager' ) ) {

	class Plugin_Dependency_Manager {
		/**
		 * is_plugin_active.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function is_plugin_active( $plugin ) {
			return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
				(
					in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) )
					|| ( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
				)
			);
		}

		
	}
}