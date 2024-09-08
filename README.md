# WP Dev Utils 

A collection of useful codes and utilities for WordPress plugin development by WPFactory

## WP_Plugin_Base Class
A class that will give your plugin many useful and convenient features that you are tired of creating from scratch every time you create a new plugin.

For now, this is what it offers:
- Convenient way for setting the plugin version, with a customized meta that will be responsible for detecting when the plugin updates.
- Localization parameters, such as, localization initialization hook, localization domain and relative path
- Plugin dependency options.
- A cacheable get_option() feature.
- A Singleton class.

### How to use it?
Extend the `WP_Plugin_Base` class from your main plugin class.
