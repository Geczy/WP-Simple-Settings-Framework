WP Simple Settings Framework
================================

A minimalistic framework for Wordpress Settings API.

Quick start
------------

* [Download the latest release](https://github.com/Geczy/WP-Simple-Settings-Framework/zipball/master) (zip)

* Or, clone the repo, `git clone git://github.com/Geczy/WP-Simple-Settings-Framework.git`

Usage
------------
```php
<?php
add_action( 'init', 'sf_load_settings' );
function sf_load_settings() {
	require 'classes/sf-class-settings.php';
	$settings_framework = new \Geczy\WPSettingsFramework\SF_Settings_API($id = 'my_plugin_name', $title = 'My Plugin Title', $menu = 'plugins.php');
}
```

Features
------------

### Input types

* Text
* Number
* Textarea
* Checkbox
* Radio
* Select
* WP Pages

### Helpers

Update or add a new option

```php
<?php
$settings_framework->update_option('your_option', 'new_value');
```

Get an existing option's value

```php
<?php
$settings_framework->get_option('your_option');
```

Bug tracker
-----------

Have a bug? Please create an issue here on GitHub!

https://github.com/Geczy/WP-Simple-Settings-Framework/issues/

Copyright and License
---------------------

Copyright 2012 Matthew Gates

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
* Neither the names of the copyright holders nor the names of the contributors may be used to endorse or promote products derived from this software without specific prior written permission.

http://www.opensource.org/licenses/bsd-license.php
