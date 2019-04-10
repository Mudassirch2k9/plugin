<?php
/**
 * @package  OPAPlugin
 */

namespace OPA\Inc\Base;

class BaseController{

    public $plugin_path;
    public $plugin_url;
    public $plugin;
    public $manager;

    public $option_name_plugin;
    public $option_group_plugin_settings;

    public $page_slug_product_manager;
    public $option_group_product_manager;
    public $option_name_product_attributes;

    public $page_slug_filter_question;
    public $page_slug_filter_question_other;
    public $option_group_filter_question;
    public $option_group_filter_question_other;
    public $option_name_questions;
    public $option_name_questions_other;

    function __construct(){
        $this->plugin_path = plugin_dir_path( dirname( __FILE__,2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__,2));
        $this->plugin = plugin_basename(dirname(__FILE__,3)).'/online-product-advisor.php';

        $this->manager = [
            'product_manager' => __( 'Product Manager', 'wp-product-advisor' ),
            'opa_filter_questions' => __( 'Filter Questions', 'wp-product-advisor' )
        ];

        
        //...Plugin attributes

        $this->option_name_plugin = 'opa_plugin';

        $this->option_group_plugin_settings = 'opa_plugin_settings';

        
        //...product Manager attributes

        $this->page_slug_product_manager = 'product_manager';

        $this->option_group_product_manager = 'opa_cpt_settings_group';

        $this->option_name_product_attributes = 'opa_cpt_product_attribute';
        

        
        //...filter Question attributes

        $this->page_slug_filter_question = 'filter_question';

        $this->page_slug_filter_question_other = 'filter_question_other';

        $this->option_group_filter_question = 'opa_fq_settings_group';

        $this->option_group_filter_question_other = 'opa_fq_settings_group_other';

        $this->option_name_questions = 'opa_questions';

        $this->option_name_questions_other = 'opa_questions_other';

        //...session attribute

        $this->session_attr_filter_data = 'opa_filter_data';
        

    }

    public function activeModule(string $key)
    {
        $option = get_option($this->option_name_plugin);
        
        return (isset($option[ $key]))?$option[ $key] : false;

    }

    // public function startSession()
    // {
        
    // }

    public function getFilterSessionData(){
        if( isset($_SESSION['opa_filter_data'] ) ){

            $_SESSION['opa_filter_data'] = ['hi ...'];
        }

        return $_SESSION['opa_filter_data'];
    }
    
}