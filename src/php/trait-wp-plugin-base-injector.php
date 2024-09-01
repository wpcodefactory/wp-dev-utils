<?php
/**
 * WP Plugin Base Injector.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Plugin_Base;

if ( trait_exists( 'WPFactory\WP_Plugin_Base\WP_Plugin_Base_Injector' ) ) {

	trait WP_Plugin_Base_Injector {

		/**
		 * WP Plugin Base class.
		 *
		 * @since 1.0.0
		 *
		 * @var
		 */
		protected $wp_plugin_base;

		/**
		 * get_wpf_plugin.
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public function get_wp_plugin_base() {
			return $this->wp_plugin_base;
		}

		/**
		 * Sets WP Plugin Base class.
		 *
		 * @since 1.0.0
		 *
		 * @param   mixed  $wp_plugin_base
		 */
		public function set_wp_plugin_base( $wp_plugin_base ) {
			$this->wp_plugin_base = $wp_plugin_base;
		}

	}
}