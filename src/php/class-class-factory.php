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
		private $baseNamespace;
		private $priorityNamespaces = [];

		public function __construct( $baseNamespace, array $priorityNamespaces = [] ) {
			$this->baseNamespace      = trim( $baseNamespace, '\\' ); // Remove any trailing slashes
			$this->priorityNamespaces = array_map( function ( $namespace ) {
				return trim( $namespace, '\\' );
			}, $priorityNamespaces );
		}

		public function create( $className, ...$params ) {
			$classToLoad = $this->resolveClassName( $className );

			if ( class_exists( $classToLoad ) ) {
				// Use reflection to instantiate the class with parameters
				$reflection = new \ReflectionClass( $classToLoad );

				return $reflection->newInstanceArgs( $params );
			} else {
				throw new \Exception( "Class $classToLoad does not exist." );
			}
		}

		public function createStatic( $className, $methodName, ...$params ) {
			$classToLoad = $this->resolveClassName( $className );

			if ( class_exists( $classToLoad ) ) {
				if ( method_exists( $classToLoad, $methodName ) ) {
					return call_user_func_array( [ $classToLoad, $methodName ], $params );
				} else {
					throw new \Exception( "Method $methodName does not exist in class $classToLoad." );
				}
			} else {
				throw new \Exception( "Class $classToLoad does not exist." );
			}
		}

		// This method resolves the class name by checking priority namespaces first
		private function resolveClassName( $className ) {
			// Check priority namespaces first
			foreach ( $this->priorityNamespaces as $priorityNamespace ) {
				$priorityClass = $priorityNamespace . '\\' . $className;
				if ( class_exists( $priorityClass ) ) {
					return $priorityClass;
				}
			}

			// If no priority class exists, return the base namespace class
			return $this->baseNamespace . '\\' . $className;
		}
	}
}