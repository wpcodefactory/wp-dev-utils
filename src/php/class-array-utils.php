<?php
/**
 * Array utils
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Dev_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WP_Dev_Utils\Array_Utils' ) ) {

	class Array_Utils {

		/**
		 * Recursive wp_parse_args().
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return array
		 */
		static function wp_parse_args_r( &$a, $b ) {
			$a      = (array) $a;
			$b      = (array) $b;
			$result = $b;
			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = self::wp_parse_args_r( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}

			return $result;
		}
	}
}