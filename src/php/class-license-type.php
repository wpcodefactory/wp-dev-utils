<?php
/**
 * License type
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Dev_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WP_Dev_Utils\License_Type' ) ) {

	class License_Type {

		/**
		 * Setup args.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $setup_args = array();

		/**
		 * setup.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $args
		 *
		 * @return void
		 */
		function setup( $args = null ) {
			$args = wp_parse_args( $args, array(
				'file_path' => '',
			) );

			$this->setup_args = $args;
		}

		/**
		 * Detects license type by folder.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param   array  $folder_checks         An array where the key is the license type, and the value is an array of folder paths to check.
		 *                                        Example:
		 *                                        array(
		 *                                           'pro'     => array('src/php/pro', 'includes/pro'),
		 *                                           'premium' => array('src/php/premium')
		 *                                        )
		 * @param          $default_license_type
		 *
		 * @return int|mixed|string
		 */
		function detect_license_type_by_folder( $folder_checks, $default_license_type = 'free' ) {
			foreach ( $folder_checks as $license_type => $folders ) {
				foreach ( $folders as $folder ) {
					if ( file_exists( plugin_dir_path( $this->setup_args['file_path'] ) . '/' . untrailingslashit( $folder ) ) ) {
						return $license_type;
					}
				}
			}

			return $default_license_type;
		}
	}
}