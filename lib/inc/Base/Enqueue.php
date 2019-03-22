<?php

/**
 * @package  OPA Plugin
 */

namespace OPA\Inc\Base;
use \OPA\Inc\Base\BaseController;
class Enqueue extends BaseController{
    
    public function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_enqueue']);
    }

    function admin_enqueue()
    {
         // Load the datepicker script (pre-registered in WordPress).
         wp_enqueue_script( 'jquery-ui-datepicker' );

         // You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
         wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
         wp_enqueue_style( 'jquery-ui' );
 
         // Select2 searchable dropdown
         wp_enqueue_style( 'select2_css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css' );
         wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', ['jquery'], '1.0', true );

         
        // enqueue admin scripts and style
        wp_enqueue_style('mypluginstyle',$this->plugin_url.'assets/opa-admin_style.css' );
        
        wp_enqueue_script('mypluginscript',$this->plugin_url.'assets/opa-admin_script.js' );

        // select 2
        wp_enqueue_style('select2','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css');
       
        wp_enqueue_script('select2','https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js' );
      
 
 
    }

    function frontend_enqueue()
    {
        // enqueue frontend scripts and style
        wp_enqueue_style('mypluginstyle',$this->plugin_url.'assets/opa-style.css');
        // wp_enqueue_script('mypluginstyle',$this->plugin_url.'assets/opa-script.js');
        
        // load bootstrap if not loaded
        if (!wp_style_is('bootstrap')) {
            wp_register_style('bootstrap', $this->plugin_url.'lib/bootstrap.min.css');
            wp_enqueue_style('bootstrap');
        }
            
        if (!wp_script_is('bootstrap')) {
            wp_register_script('bootstrap', $this->plugin_url.'lib/bootstrap.min.js', ['jquery'], '1.0', true );
            wp_enqueue_script('bootstrap');
        }

        if (!wp_style_is('font-awesome')) {
                wp_enqueue_style('font-awesome', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css'); 
        }
        
    }
    
}

