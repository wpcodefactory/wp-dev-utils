<?php
/**
 * WPF Plugin.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Dev_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WP_Dev_Utils\WP_Plugin_Base' ) ) {

	/**
	 * WPF_Plugin.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	abstract class WP_Plugin_Base {

		/**
		 * Singleton Trait.
		 *
		 * @since 1.0.0
		 */
		use Singleton;

		/**
		 * Setup args.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $setup_args = array();

		/**
		 * Database.
		 *
		 * @since 1.0.0
		 *
		 * @var Database_Manager
		 */
		public $db;

		/**
		 * events.
		 *
		 * @since 1.0.0
		 *
		 * @var string[]
		 */
		protected $events = array(
			'plugin_activation',
			'plugin_deactivation',
			'plugin_update',
		);

		/**
		 * Setups the class.
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
				'file_path'         => '',
				'use_db_manager'    => true,
				'versioning'        => array(),
				'localization'      => array(),
				'action_links'      => array(
					//array( 'label' => 'Test', 'link' => 'http://test.com', 'target' => '_self' ),
					//array( 'label' => 'Test', 'link' => 'http://test.com', 'target' => '_blank' ),
				),
			) );

			// Localization.
			$args['localization'] = wp_parse_args( $args['localization'], array(
				'action_hook'   => 'init',
				'domain'        => '',
				'relative_path' => 'langs',
			) );

			// Versioning.
			$args['versioning'] = wp_parse_args( $args['versioning'], array(
				'version'      => '1.0.0',
				'version_meta' => '',
			) );

			$this->setup_args = $args;
		}

		/**
		 * Initializes the class.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			$args = $this->get_setup_args();

			// Database class.
			if ( $args['use_db_manager'] ) {
				$db = new Database_Manager();
				$db->set_wp_plugin_base( $this );
				$this->set_db_manager( $db );
			}

			// Action links.
			if ( ! empty( $args['action_links'] ) ) {
				$this->add_action_links( $args['action_links'] );
			}

			// Localization.
			$this->add_action( $args['localization']['action_hook'], array( $this, 'localize' ) );

			// Version checking.
			add_action( 'admin_init', array( $this, 'version_checking' ) );

			// Handles plugin activation and deactivation.
			$this->handle_activation_deactivation();
		}

		/**
		 * Sets db manager.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param   Database_Manager  $db
		 */
		public function set_db_manager( Database_Manager $db ) {
			$this->db = $db;
		}

		/**
		 * handle_activation_deactivation.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function handle_activation_deactivation() {
			$setup_args = $this->get_setup_args();
			$file       = plugin_basename( $setup_args['file_path'] );

			// Activation.
			if ( 'activate_' . $file === current_filter() ) {
				$this->trigger_event( 'plugin_activation' );
			}

			// Deactivation.
			if ( 'deactivate_' . $file === current_filter() ) {
				$this->trigger_event( 'plugin_deactivation' );
			}
		}

		/**
		 * __call.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $name
		 * @param $arguments
		 *
		 * @return void
		 */
		public function __call( $name, $arguments ) {
			if (
				! method_exists( $this, $name )	&&
				! in_array( preg_replace( '/^on_/', '', $name ), $this->events )
			) {
				trigger_error( "Method '$name' does not exist or is not allowed.", E_USER_ERROR );
			}
		}

		/**
		 * trigger_event.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $event
		 * @param $params
		 *
		 * @return void
		 */
		function trigger_event( $event, $params = null ) {
			call_user_func_array( array( $this, "on_{$event}" ), array( $params ) );
		}

		/**
		 * version_checking.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function version_checking() {
			$setup_args     = $this->get_setup_args();
			$versioning     = $setup_args['versioning'] ?? '';
			$version = $versioning['version'] ?? '';
			$meta_key       = $versioning['version_meta'] ?? '';
			if ( ! empty( $meta_key ) ) {
				$old_version = $this->db->get_option( $meta_key, '' );
			}
			if ( ! empty( $meta_key ) && $old_version !== $version ) {
				update_option( $meta_key, sanitize_text_field( $version ), false );
				$this->trigger_event( 'plugin_update', array(
					'old_version' => $old_version,
					'new_version' => $version
				) );
			}
		}

		/**
		 * Localizes the plugin.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		public function localize() {
			$setup_args        = $this->get_setup_args();
			$localization_data = $setup_args['localization'] ?? '';
			$domain            = $localization_data['domain'] ?? '';
			$relative_path     = $localization_data['relative_path'] ?? '';
			if ( ! empty( $domain ) && ! empty( $relative_path ) ) {
				load_plugin_textdomain( $domain, false, dirname( plugin_basename( $setup_args['file_path'] ) ) . '/' . untrailingslashit( $relative_path ) . '/' );
			}
		}

		/**
		 * Adds action links.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $action_links
		 *
		 * @return void
		 */
		function add_action_links( $action_links ) {
			$setup_args = $this->get_setup_args();
			add_filter( 'plugin_action_links_' . plugin_basename( $setup_args['file_path'] ), function ( $links ) use ( $action_links ) {
				$custom_links = array();
				foreach ( $action_links as $link_info ) {
					$link           = $link_info['link'] ?? '';
					$target         = $link_info['target'] ?? '';
					$label          = $link_info['label'] ?? '';
					$custom_links[] = sprintf( '<a href="%s" target="%s">%s</a>', esc_url( $link ), sanitize_text_field( $target ), sanitize_text_field( $label ) );
				}
				$links = array_merge( $custom_links, $links );

				return $links;
			} );
		}

		/**
		 * Runs the add_action() callback if the hook_name is the current_filter.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $hook_name
		 * @param $callback
		 * @param $priority
		 * @param $accepted_args
		 *
		 * @return void
		 */
		function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
			if ( $hook_name === current_filter() ) {
				$callback();
			} else {
				add_action( $hook_name, $callback, $priority, $accepted_args );
			}
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

		/**
		 * gets_events.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		public function get_events() {
			return $this->events;
		}

	}
}