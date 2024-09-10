<?php
/**
 * Class Factory.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WP_Dev_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WP_Dev_Utils\Class_Factory' ) ) {

	class Class_Factory {

		/**
		 * Base namespace.
		 *
		 * @since   1.0.0
		 *
		 * @var string
		 */
		private $base_namespace;

		/**
		 * Priority namespaces.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		private $priority_namespaces = [];

		/**
		 * Constructor.
		 *
		 * @param          $baseNamespace
		 * @param   array  $priorityNamespaces
		 */
		public function __construct( $baseNamespace, array $priorityNamespaces = array() ) {
			$this->base_namespace      = trim( $baseNamespace, '\\' ); // Remove trailing slashes
			$this->priority_namespaces = array_map( function ( $namespace ) {
				return trim( $namespace, '\\' );
			}, $priorityNamespaces );
		}

		/**
		 * Create.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $className
		 * @param ...$params
		 *
		 * @throws \ReflectionException
		 * @return mixed|object|null
		 */
		public function create( $className, ...$params ) {
			// Resolve the full class name to load
			$classToLoad = $this->resolve_class_name( $className );
			if ( class_exists( $classToLoad ) ) {
				// Use reflection to instantiate the class with parameters
				$reflection = new \ReflectionClass( $classToLoad );

				return $reflection->newInstanceArgs( $params );
			} else {
				throw new \Exception( "Class $classToLoad does not exist." );
			}
		}

		/**
		 * Call static class.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $class_name
		 * @param $method_name
		 * @param ...$params
		 *
		 * @throws \Exception
		 * @return mixed
		 */
		public function call_static_method( $class_name, $method_name, ...$params ) {
			// Resolve the full class name to load.
			$class_to_load = $this->resolve_class_name( $class_name );

			if ( class_exists( $class_to_load ) ) {
				if ( method_exists( $class_to_load, $method_name ) ) {
					return call_user_func_array( [ $class_to_load, $method_name ], $params );
				} else {
					throw new \Exception( "Method $method_name does not exist in class $class_to_load." );
				}
			} else {
				throw new \Exception( "Class $class_to_load does not exist." );
			}
		}

		/**
		 * resolve_class_name.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $class_name
		 *
		 * @throws \Exception
		 * @return array|mixed|string|string[]
		 */
		private function resolve_class_name( $class_name ) {
			$load_possibilities = array();
			if ( $this->is_full_namespace( $class_name ) ) {
				foreach ( $this->priority_namespaces as $priority_namespace ) {
					$possibility          = str_replace( $this->base_namespace, $priority_namespace, $class_name );
					$load_possibilities[] = $possibility;
				}
				$load_possibilities[] = $class_name;
			} else {
				foreach ( $this->priority_namespaces as $priority_namespace ) {
					$priority_class       = $priority_namespace . '\\' . $class_name;
					$load_possibilities[] = $priority_class;
				}
				$load_possibilities[] = $this->base_namespace . '\\' . $class_name;
			}

			foreach ( $load_possibilities as $possible_class_name ) {
				if ( class_exists( $possible_class_name ) ) {
					return $possible_class_name;
				}
			}

			throw new \Exception( "Class $class_name does not exist." );
		}

		/**
		 * is_full_namespace.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $class_name
		 *
		 * @return bool
		 */
		private function is_full_namespace( $class_name ) {
			return strpos( $class_name, $this->base_namespace . '\\' ) === 0;
		}


	}
}