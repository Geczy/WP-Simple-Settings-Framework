<?php
/*
Plugin Name: WP Simple Settings Framework
Plugin URI: http://www.wpaffiliatedemon.com
Description: Build an affiliate e-shop or an affiliate e-shop aggregator with campaigns from all greek affiliate agencies.
Version: 1.6.3
Author: Skapator, Alex Itsios
Author URI: http://skapator.com
*/

add_action( 'init', 'sf_load_settings' );
function sf_load_settings() {
    require 'classes/sf-class-settings.php';
    $settings_framework = new SF_Settings_API($id = 'my_plugin_name', $title = 'My Plugin Title', $menu = 'plugins.php', __FILE__);
    $settings_framework->load_options(plugin_dir_path( __FILE__ ).'sf-options.php');
}