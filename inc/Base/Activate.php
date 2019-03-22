<?php
/**
 * @package  OPAPlugin
 */

namespace OPA\Inc\base;

use OPA\Inc\Base\BaseController;

class Activate{
    
    public static $baseController;

    public static function activate(){
        flush_rewrite_rules();

        self::$baseController = new BaseController();
        
        self::setDefaultOptionData(self::$baseController->option_name_plugin);
        self::setDefaultOptionData(self::$baseController->option_name_questions);

        $default_product_attributes = [ 
            "opa_brand" => [
                "name"  => "Brand",
                "type"  => "text",
                "placeholder" => "e.g. Apple",
                "id"    => 'opa_brand'
            ],
            "opa_model" => [
                "name"  => "Model",
                "type"  => "text",
                "placeholder" => "e.g. iPhone XS",
                "id"    => 'opa_model'
            ],
            "opa_external_link" => [
                "name"  => "External Link",
                "type"  => "text",
                "placeholder" => "e.g. https://www.amazzon.com/.....",
                "id"    => 'opa_external_link'
            ]
        ];

        self::setDefaultOptionData(self::$baseController->option_name_product_attributes , $default_product_attributes);

        $default_product_attributes = [ 
            "filter_page_title" => "Our top suggestions for you:"
        ];

        self::setDefaultOptionData(self::$baseController->page_slug_filter_question_other , $default_product_attributes);

        
    }
    public static function setDefaultOptionData( String $option_name, Array $default = [])
    {
        if(!get_option($option_name) ){
            
            update_option($option_name, $default );
        }
    }
}

