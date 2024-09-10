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
		 * Plugin Dependency Manager.
		 *
		 * @since 1.0.0
		 *
		 * @var Plugin_Dependency_Manager
		 */
		protected $plugin_dependency_manager;

		/**
		 * Database.
		 *
		 * @since 1.0.0
		 *
		 * @var Database_Manager
		 */
		public $db;

		/**
		 * Class Factory.
		 *
		 * @since 1.0.0
		 *
		 * @var Class_Factory
		 */
		public $class_factory;

		/**
		 * events.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $events = array(
			'plugin_activation',
			'plugin_deactivation',
			'plugin_update',
		);

		/**
		 * Initialized.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var bool
		 */
		protected $initialized = false;

		/**
		 * Pre Initialized.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var bool
		 */
		protected $pre_initialized = false;

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
				'file_path'          => '',
				'hpos_compatibility' => 'ignore', // compatible | incompatible | ignore.
				'use_db_manager'     => true,
				'versioning'         => array(),
				'localization'       => array(),
				'plugin_dependency'  => array(),
				'class_factory'      => array(),
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

			// Class loading.
			$args['class_factory'] = wp_parse_args( $args['class_factory'], array(
				'base_namespace'      => '',
				'priority_namespaces' => array(),
			) );

			// Plugin dependency.
			$args['plugin_dependency'] = Array_Utils::wp_parse_args_r( $args['plugin_dependency'], array(
				array(
					'plugin_path'   => '', // Path to the plugin file relative to the plugins directory. Ex:plugin-directory/plugin-file.php.
					'plugin_name'   => '',
					'plugin_status' => 'enabled', // enabled | disabled.
					'error_notice'  => '<strong>{dependent_plugin_name}</strong> depends on <strong>{required_plugin_name}</strong> plugin <strong>{required_plugin_status}</strong>.',
					'error_actions' => array() // Possible values: show_error_notice, disable_dependent_plugin.
				)
			) );

			$this->setup_args = $args;

			// Pre initializes.
			$this->pre_init();
		}

		/**
		 * get_plugin_dependency_manager.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Plugin_Dependency_Manager
		 */
		function get_plugin_dependency_manager() {
			if ( is_null( $this->plugin_dependency_manager ) ) {
				// Setup args.
				$args = $this->get_setup_args();

				// Dependency manager.
				$this->plugin_dependency_manager = new Plugin_Dependency_Manager();
				$this->plugin_dependency_manager->setup( $args );
				$this->plugin_dependency_manager->init();
			}

			return $this->plugin_dependency_manager;
		}

		/**
		 * requirements_passed.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return bool
		 */
		function plugin_requirements_passed() {
			$dependency_manager = $this->get_plugin_dependency_manager();
			if ( ! empty( $dependency_manager->get_failed_requirements() ) ) {
				return false;
			}

			return true;
		}

		/**
		 * HPOS compatibility.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function declare_hpos_compatibility() {
			$setup_args         = $this->get_setup_args();
			$hpos_compatibility = $setup_args['hpos_compatibility'];
			if ( 'ignore' !== $hpos_compatibility ) {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->get_plugin_basename(), 'compatible' === $hpos_compatibility );
				}
			}
		}

		/**
		 * Pre Initializes the class.
		 *
		 * Will always be called, and before the init() method, even with false === plugin_requirements_passed().
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return false|void
		 */
		function pre_init() {
			// Makes sure the pre init method only calls once.
			if ( $this->pre_initialized ) {
				return false;
			}
			$this->pre_initialized = true;

			// HPOS compatibility.
			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
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
			// Makes sure the init method only calls once.
			if ( $this->initialized ) {
				return false;
			}
			$this->initialized = true;

			// Gets plugin setup args.
			$args = $this->get_setup_args();

			// Class loading.
			$this->initialize_class_loading();

			// Do not init if plugin requirements didn't pass.
			if ( ! $this->plugin_requirements_passed() ) {
				return false;
			}

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
		 * Initializes class loading.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function initialize_class_loading() {
			$setup_args          = $this->get_setup_args();
			$class_loading       = $setup_args['class_factory'] ?? '';
			$base_namespace      = $class_loading['base_namespace'] ?? '';
			$priority_namespaces = $class_loading['priority_namespaces'] ?? array();
			$this->class_factory = new Class_Factory( $base_namespace, $priority_namespaces );
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
			$file       = $this->get_plugin_basename();

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
			$setup_args = $this->get_setup_args();
			$versioning = $setup_args['versioning'] ?? '';
			$version    = $versioning['version'] ?? '';
			$meta_key   = $versioning['version_meta'] ?? '';
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
				load_plugin_textdomain( $domain, false, dirname( $this->get_plugin_basename() ) . '/' . untrailingslashit( $relative_path ) . '/' );
			}
		}

		/**
		 * Adds action links.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param   array  $action_links  Expected an array with nested arrays. Example: array( array( 'link' => '', 'target' =>'', 'label'=>'' ) )
		 *
		 * @return void
		 */
		function add_action_links( $action_links = array() ) {
			add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), function ( $links ) use ( $action_links ) {
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

		/**
		 * get_plugin_version.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_plugin_version() {
			$setup_args = $this->get_setup_args();
			$versioning = $setup_args['versioning'] ?? '';
			$version    = $versioning['version'] ?? '';
			return $version;
		}

		/**
		 * get_file_path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_plugin_file_path() {
			$setup_args = $this->get_setup_args();
			return $setup_args['file_path'];
		}

		/**
		 * get_basename.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_plugin_basename() {
			$file_path = $this->get_plugin_file_path();
			return plugin_basename( $file_path );
		}

	}
}