<?php
/*
Plugin Name: Online Product Advisor
Description: Create & customize a product advisor engine to offer your customer tailored product suggestions.
Version: 1.0.1
Text Domain: wp-product-advisor
Domain Path: /languages
Author: Christian Westermann
Author URI: https://www.linkedin.com/in/christian-westermann-318414b8/
*/

if(! defined('ABSPATH')){
    die;
}

if ( ! function_exists( 'wppa' ) ) {
    // Create a helper function for easy SDK access.
    function wppa() {
        global $wppa;

        if ( ! isset( $wppa ) ) {
            // Activate multisite network integration.
            if ( ! defined( 'WP_FS__PRODUCT_3382_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_3382_MULTISITE', true );
            }

            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $wppa = fs_dynamic_init( array(
                'id'                  => '3382',
                'slug'                => 'online-product-advisor',
                'premium_slug'        => 'wp-kaufberater-pro-premium',
                'type'                => 'plugin',
                'public_key'          => 'pk_2ae16976d4f7fe64346aa186e57ec',
                'is_premium'          => true,
                'premium_suffix'      => 'Pro',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'opa_settings',
                ),
                // Set the SDK to work in a sandbox mode (for development & testing).
                // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
                'secret_key'          => 'sk_-KZsYXqFi%+^S*~w*y$93}Sjm34d<',
            ) );
        }

        return $wppa;
    }

    // Init Freemius.
    wppa();
    // Signal that SDK was initiated.
    do_action( 'wppa_loaded' );
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


function custom_plugin_setup() {
    load_plugin_textdomain('wp-product-advisor', false, dirname(plugin_basename(__FILE__)) . '/languages/');
} // end custom_theme_setup
add_action('after_setup_theme', 'custom_plugin_setup');


