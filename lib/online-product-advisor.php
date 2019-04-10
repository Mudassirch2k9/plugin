<?php
/*
Plugin Name: Online Product Advisor
Description: Create & customize a product advisor engine to offer your customer tailored product suggestions.
Version: 1.0.1
Author: Christian Westermann
Author URI: https://www.linkedin.com/in/christian-westermann-318414b8/
*/

if(! defined('ABSPATH')){
    die;
}

if(file_exists(dirname(__FILE__).'/vendor/autoload.php'))
{
    require_once dirname(__FILE__).'/vendor/autoload.php';
}


function activate_OPA_plugin()
{
    OPA\Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_OPA_plugin');


function deactivate_OPA_plugin()
{
    OPA\Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_OPA_plugin');




if(class_exists('OPA\\Inc\\Init'))
{
    OPA\Inc\Init::register_services();
}