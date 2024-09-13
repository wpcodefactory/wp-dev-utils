<?php
/**
 * License type manager
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Dev_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WP_Dev_Utils\License_Type_Manager' ) ) {

	class License_Type_Manager {

		/**
		 * Setup args.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $setup_args = array();

		/**
		 * License type.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $license_type = null;

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
			if ( $this->license_type !== null ) {
				return $this->license_type;
			}

			foreach ( $folder_checks as $license_type => $folders ) {
				foreach ( $folders as $folder ) {
					if ( file_exists( plugin_dir_path( $this->setup_args['file_path'] ) . '/' . untrailingslashit( $folder ) ) ) {
						$this->license_type = $license_type;
						return $license_type;
					}
				}
			}

			$this->license_type = $default_license_type;
			return $default_license_type;
		}
	}
}