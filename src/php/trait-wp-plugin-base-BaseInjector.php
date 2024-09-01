<?php
/**
 * Singleton.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Plugin_Base;

trait WP_Plugin_Base_Injector {

	/**
	 * $wpf_plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var
	 */
	protected $wpf_plugin;

	/**
	 * get_wpf_plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_wpf_plugin() {
		return $this->wpf_plugin;
	}

	/**
	 * set_wpf_plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param   mixed  $wpf_plugin
	 */
	public function set_wpf_plugin( $wpf_plugin ) {
		$this->wpf_plugin = $wpf_plugin;
	}


}