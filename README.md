# WP Dev Utils 

A collection of useful codes and utilities for WordPress plugin development by WPFactory.

## WP_Plugin_Base Class
A class that will give your plugin some useful and convenient features that you are tired of creating from scratch every time you create a new plugin.

For now, this is what it offers:
- Convenient way for setting the plugin version, with a customized meta that will be responsible for detecting when the plugin updates.
- Localization parameters, such as, localization initialization hook, localization domain and relative path
- Plugin dependency options.
- A cacheable get_option() feature.
- A Singleton class.

### How to use it?
Extend the `WP_Plugin_Base` class from your main plugin class, override the `init()` method and put your code there. Example:

```php
<?php
namespace My_Plugin_Namespace;

use WPFactory\WP_Dev_Utils\WP_Plugin_Base;

if ( ! class_exists( 'My_Plugin_Namespace\Plugin' ) ) {

	class Plugin extends WP_Plugin_Base {
		
		public function init() {
			parent::init();
			// Put your code here.
		}

	}

}
```

Get the plugin with the `get_instance()` static method, run the `setup()` and then the `init()` method. Example:

```php
namespace My_Plugin_Namespace;

// Initializes the plugin.
function initialize_plugin(){
	// Loads Composer.
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
	
	// Gets the plugin.
	$plugin = \My_Plugin_Namespace\Plugin::get_instance();
	
	// Setups the plugin.
	$plugin->setup();
	
	// Initializes the plugin.
	if ( $plugin->plugin_requirements_passed() ) {
		$plugin->init();
	}
}

// Initializes the plugin on specific WordPress Hooks.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\initialize_plugin' );
register_activation_hook( __FILE__, __NAMESPACE__ . '\\initialize_plugin' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\initialize_plugin' );
```

### Installation
Set your composer.json like this:

```json
{
  "repositories": [    
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wp-dev-utils"
    }
  ],
  "require": {   
    "wpfactory/wp-dev-utils": "dev-main"
  },
  "config": {
    "preferred-install": "dist"
  }
}
