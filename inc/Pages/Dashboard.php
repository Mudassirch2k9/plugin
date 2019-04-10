<?php
/**
 * @package  OPAPlugin
 */
namespace OPA\Inc\Pages;

use OPA\Inc\Api\Callbacks\AdminCallbacks;
use OPA\Inc\Api\Callbacks\ManagerCallbacks;
use OPA\Inc\Api\SettingsApi;
use OPA\Inc\Base\BaseController;

/**
 *
 */
class Dashboard extends BaseController
{
    public $settings_api;

    private $adminCallbacks;

    private $managerCallbacks;

    private $pages = [];

    private $subpages = [];

    public function register()
    {
        $this->settings_api = new SettingsApi();

        $this->adminCallbacks = new AdminCallbacks();

        $this->managerCallbacks = new ManagerCallbacks();

        $this->setPages();
        // $this->setSubpages();
        $this->setSettings();
        $this->setSections();
        $this->setFields();

        // Little hack in order to get the menu items translated into german #gerLangHack
        $locale = get_locale();
        if ($locale == 'de_DE') {
            $this->settings_api->addPages($this->pages)->withSubPage('Übersicht')->register();
        } else {
            $this->settings_api->addPages($this->pages)->withSubPage('Dashboard..')->register();
        }
    }

    public function setPages()
    {

        // #gerLangHack
        $locale = get_locale();
        if ($locale == 'de_DE') {
            $this->pages = [
                [
                    'page_title' => 'WP Kaufberater Einstellungen',
                    'menu_title' => 'WPK Einstellungen',
                    'capability' => 'manage_options',
                    'menu_slug' => 'opa_settings',
                    'callback' => [$this->adminCallbacks, 'adminDashboard'],
                    'icon_url' => 'dashicons-admin-tools',
                    'position' => 9,
                ],
            ];
        } else {
            
            // #gerLangHack
            $locale = get_locale();
            if ($locale == 'de_DE') {
                $this->pages = [
                    [
                        'page_title' => 'WP Kaufberater Einstellungen',
                        'menu_title' => 'WPK Einstellungen', 'wp-product-advisor',
                        'capability' => 'manage_options',
                        'menu_slug' => 'opa_settings',
                        'callback' => [$this->adminCallbacks, 'adminDashboard'],
                        'icon_url' => 'dashicons-admin-tools',
                        'position' => 9,
                    ],
                ];  
            } else {
                $this->pages = [
                    [
                        'page_title' => 'WP Product Advisor Settings',
                        'menu_title' => 'WPPA Settings',
                        'capability' => 'manage_options',
                        'menu_slug' => 'opa_settings',
                        'callback' => [$this->adminCallbacks, 'adminDashboard'],
                        'icon_url' => 'dashicons-admin-tools',
                        'position' => 9,
                    ],
                ]; 
            }
        }

    }


    public function setSettings()
    {

        $args = [
            [
                'option_group' => $this->option_group_plugin_settings,
                'option_name' => $this->option_name_plugin,
                'callback' => [$this->managerCallbacks, 'checkboxSanitizer'],
            ]
        ];

        $this->settings_api->setSettings($args);

    }

    public function setSections()
    {

        // #gerLangHack
        $locale = get_locale();
        if ($locale == 'de_DE') {
            $args = [
                [
                    'id' => 'opa_admin_index',
                    'title' => 'WP Kaufberater Einstellungen',
                    'callback' => [$this->managerCallbacks, 'adminIndexSection'],
                    'page' => 'opa_settings',
                ],
            ];
        } else {
            $args = [
                [
                    'id' => 'opa_admin_index',
                    'title' => 'WPPA Settings',
                    'callback' => [$this->managerCallbacks, 'adminIndexSection'],
                    'page' => 'opa_settings',
                ],
            ];
        }

        $this->settings_api->setSections($args);
    }

    public function setFields()
    {
        $args = [];

        foreach ($this->manager as $key => $value) {
            $args[] = [

                'id' => $key,  //id decided for the field is settings option_name
                'title' => $value,
                'callback' => [$this->managerCallbacks, 'checkboxField'],
                'page' => 'opa_settings',
                'section' => 'opa_admin_index',
                'args' => [
                    'option_name'=> $this->option_name_plugin,
                    'label_for' => $key,
                    'classes' => 'ui-toggle'
                    ]   
                ];
        }

    //    var_dump($args);
        $this->settings_api->setFields($args);
       
    }
}
