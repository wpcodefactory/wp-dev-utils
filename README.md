# WP Dev Utils 

A collection of useful codes and utilities for WordPress plugin development by WPFactory.

## Installation
Set your `composer.json` like this:

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
```

## WP_Plugin_Base class
A class designed to provide useful and convenient features for your plugin, so you no longer have to create them from scratch with every new project.

For now, this is what it offers:
- Convenient way for setting the plugin version, with a customized meta that will be responsible for detecting when the plugin updates.
- Localization parameters, such as, localization initialization hook, localization domain and relative path.
- Plugin dependency options.
- Dynamic class loading.
- HPOS compatibility parameter.
- A cacheable get_option() feature.
- A Singleton class.

## Documentation
* [WP_Plugin_Base](https://github.com/wpcodefactory/wp-dev-utils/wiki/WP_Plugin_Base)
* [Class_Factory](https://github.com/wpcodefactory/wp-dev-utils/wiki/Class_Factory)
