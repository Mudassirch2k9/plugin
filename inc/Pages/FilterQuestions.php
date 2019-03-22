<?php

/**
 * @package  OPA Plugin
 */

namespace OPA\Inc\Pages;

use OPA\Inc\Api\Callbacks\AdminCallbacks;
use OPA\Inc\Api\Callbacks\FqCallbacks;
use OPA\Inc\Api\SettingsApi;
use OPA\Inc\Base\BaseController;

class FilterQuestions extends BaseController
{
    public $settings_api;

    public $subpages;

    public $fields;

    public $adminCallbacks;

    public $fq_callbacks;

    public function register()
    {
        if (!$this->activeModule('opa_filter_questions')) {
            return;
        }

        $this->settings_api = new SettingsApi();

        $this->adminCallbacks = new AdminCallbacks();

        $this->fq_callbacks = new FqCallbacks();

        $this->setSubpages();

        $this->setSettings();

        $this->setSection();

        $this->setFields();

        $this->settings_api->addSubpages($this->subpages)->register();

        //short code for product filter options
        add_shortcode('product_advisor', [$this->fq_callbacks, 'getFilterTemplate']);

        $this->registerAjaxActions();

    }

    //....Ajax Actions
    public function registerAjaxActions()
    {
        add_action('wp_ajax_load_ques_option_action', [$this, 'load_ques_option_action']);

        if ( is_admin() ) {
            add_action( 'wp_ajax_front_filter_action', [$this->fq_callbacks, 'frontFilterAction'] );
            add_action( 'wp_ajax_nopriv_front_filter_action',  [$this->fq_callbacks, 'frontFilterAction'] );
        }
    }

    public function load_ques_option_action()
    {
        
        global $wpdb; // this is how you get access to the database
        
        $submit_button = false;

        $pro_attr = strval($_POST['pro_attr']);

        $type = $this->fq_callbacks->getAttributeType( $pro_attr );

        if( $type )
        {
            $_POST['type'] = $type;
            $submit_button  = true;

        }else if ( isset($_POST['edit']))
        {
            $submit_button  = true;
        }

        settings_fields($this->option_group_filter_question);

        do_settings_sections($this->page_slug_filter_question);

        if($submit_button)
        {
            submit_button();
        }

        wp_die(); // this is required to terminate immediately and return a proper response
    }



    //....plugin settings pages & options

    //set subpage
    public function setSubpages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'opa_settings',
                'page_title' => 'Questions',
                'menu_title' => 'Filter Questions',
                'capability' => 'manage_options',
                'menu_slug' => $this->page_slug_filter_question,
                'callback' => [$this->adminCallbacks, 'adminFQ'],
            ],
            [
                'parent_slug' => 'opa_settings',
                'page_title' => 'Other Settings',
                'menu_title' => 'Other Settings',
                'capability' => 'manage_options',
                'menu_slug' => $this->page_slug_filter_question_other,
                'callback' => [$this->adminCallbacks, 'adminFqOther'],
            ],
        ];
    }

    //set settings
    public function setSettings()
    {
        $args = [
            [
                'option_group' => $this->option_group_filter_question,
                'option_name' => $this->option_name_questions,
                'callback' => [$this->fq_callbacks, 'fqSanitizer'],
            ],
            [
                'option_group' => $this->option_group_filter_question_other,
                'option_name' => $this->option_name_questions_other,
                'callback' => [$this->fq_callbacks, 'fqOtherSanitizer'],
            ],
        ];

        $this->settings_api->setSettings($args);
    }

    //set section
    public function setSection()
    {
        $args = [
            [
                'id' => 'opa_fq_index',
                'title' => 'Filter Question Manager',
                'callback' => [$this->fq_callbacks, 'fqSection'],
                'page' => $this->page_slug_filter_question,
            ],
            [
                'id' => 'opa_fq_other',
                'title' => 'Few Settings for Fillter Questions',
                'callback' => [$this->fq_callbacks, 'fqSectionOther'],
                'page' => $this->page_slug_filter_question_other,
            ],
        ];

        $this->settings_api->setSections($args);
    }

    //set fields

    public function setFields()
    {
        $args = [

            [
                'id' => 'pro_attr',
                'title' => 'Product Attribute',
                'callback' => [$this->fq_callbacks, 'settingsSelectField'],
                'page' => $this->page_slug_filter_question,
                'section' => 'opa_fq_index',
                'args' => [
                    'option_name' => $this->option_name_questions,
                    'label_for' => 'pro_attr',
                    'get_options' => [
                        [$this->fq_callbacks, 'getProductAttributesArray'],
                    ],
                    'disabledoption' => '-- Select an option --',
                ],
            ],
            [
                'id' => 'ques_body',
                'title' => 'Question Body',
                'callback' => [$this->fq_callbacks, 'settingsTextboxField'],
                'page' => $this->page_slug_filter_question,
                'section' => 'opa_fq_index',
                'args' => [
                    'option_name' => $this->option_name_questions,
                    'label_for' => 'ques_body',
                    'placeholder' => 'e.g. What is your max budget?',
                ],
            ],
            [
                'id' => 'options',
                'title' => 'Options',
                'callback' => [$this->fq_callbacks, 'settingsQuestionOptionsField'],
                'page' => $this->page_slug_filter_question,
                'section' => 'opa_fq_index',
                'args' => [
                    'option_name' => $this->option_name_questions,
                    'label_for' => 'options',
                ],
            ],
            [
                'id' => 'filter_page_title',
                'title' => 'Filter Page Title',
                'callback' => [$this->fq_callbacks, 'settingsOthertextboxField'],
                'page' => $this->page_slug_filter_question_other,
                'section' => 'opa_fq_other',
                'args' => [
                    'option_name' => $this->option_name_questions_other,
                    'label_for' => 'filter_page_title',
                ],
            ],
        ];

        //    var_dump($args);
        $this->settings_api->setFields($args);
    }

}
