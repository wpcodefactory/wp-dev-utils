<?php
/**
 * Plugin Dependency Manager
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
		 * Setup args.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $setup_args = array();

		/**
		 * $failed_requirements.
		 *
		 * @version 1.0.0
		 *
		 * @var array
		 */
		protected $failed_requirements = array();

		/**
		 * $dependent_plugin_name.
		 *
		 * @version 1.0.0
		 *
		 * @var string
		 */
		protected $dependent_plugin_name = '';

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
				'file_path'        => '', // Dependent plugin file path.
				'plugin_dependency' => array(),
			) );

			// Plugin dependency.
			$args['plugin_dependency'] = Array_Utils::wp_parse_args_r( $args['plugin_dependency'], array(
				array(
					'plugin_path'   => '', // Path to the plugin file relative to the plugins directory. Ex:plugin-directory/plugin-file.php
					'plugin_name'   => '', // Plugin name
					'status'        => 'enabled', // enabled | disabled
					'error_message' => '<strong>{dependent_plugin_name}</strong> depends on <strong>{required_plugin_name}</strong> plugin <strong>{required_plugin_status}.</strong>',
					'show_notice'   => true
				)
			) );

			$this->setup_args = $args;
		}

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'admin_notices', array( $this, 'show_error_notices' ) );
		}

		/**
		 * Gets dependent plugin name.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return mixed
		 */
		function get_dependent_plugin_name() {
			if ( empty( $this->dependent_plugin_name ) ) {
				$plugin_data                 = get_plugin_data( $this->get_setup_args()['file_path'] );
				$this->dependent_plugin_name = $plugin_data['Name'];
			}

			return $this->dependent_plugin_name;
		}

		/**
		 * Show error notices.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function show_error_notices() {
			$failed_requirements = $this->get_failed_requirements();
			$html                = '';
			foreach ( $failed_requirements as $plugin ) {
				if ( $plugin['show_notice'] && $plugin['error_message'] ) {
					$array_from_to = array(
						'{dependent_plugin_name}'  => $this->get_dependent_plugin_name(),
						'{required_plugin_name}'   => $plugin['plugin_name'],
						'{required_plugin_status}' => $plugin['status']
					);
					$text          = str_replace( array_keys( $array_from_to ), $array_from_to, $plugin['error_message'] );
					$html          .= '<div class="notice notice-error"><p>';
					$html          .= $text;
					$html          .= '</p></div>';
				}
			}
			if ( ! empty( $html ) ) {
				echo $html;
			}
		}

		/**
		 * get_failed_requirements.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		function get_failed_requirements() {
			$required_plugins = $this->get_setup_args()['plugin_dependency'];
			if ( empty( $this->failed_requirements ) ) {
				$this->failed_requirements = array();
				foreach ( $required_plugins as $plugin ) {
					if ( empty( $plugin['plugin_path'] ) ) {
						continue;
					}
					if (
						( 'enabled' === $plugin['status'] && ! $this->is_plugin_active( $plugin['plugin_path'] ) )
						|| ( 'disabled' === $plugin['status'] && $this->is_plugin_active( $plugin['plugin_path'] ) )
					) {
						$this->failed_requirements[] = $plugin;
					}
				}
			}

			return $this->failed_requirements;
		}

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

		/**
		 * get_setup_args.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		public function get_setup_args() {
			return $this->setup_args;
		}

	}
}