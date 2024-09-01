<?php
/**
 * WPF Plugin Database class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPF_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPF_Plugin\Database' ) ) {

	class Database {

		/**
		 * WPF Plugin Injector.
		 *
		 * @since 1.0.0
		 */
		use WPF_Plugin_Injector;

		/**
		 * Options.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $options = array();

		/**
		 * get_option.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return false|mixed|null
		 */
		function get_option( $option, $default_value = false, $get_value_from_cache = true ) {
			if (
				! isset( $this->options[ $option ] ) ||
				! $get_value_from_cache
			) {
				$this->options[ $option ] = get_option( $option, $default_value );
			}

			return $this->options[ $option ];
		}

	}


}