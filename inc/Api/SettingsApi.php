<?php
/**
 * @package  OPAPlugin
 */

namespace OPA\Inc\Api;

class SettingsApi
{
    private $admin_pages = [];

    private $admin_subpages = [];

    private $settings = [];

    private $sections = [];

    private $fields = [];

    public function register()
    {
        if (!empty($this->admin_pages) || !empty($this->admin_subpages)) {

            add_action('admin_menu', array($this, 'addAdminMenu'));
        }

        if(!empty($this->settings)){
            add_action('admin_init',[$this, 'registerCustomFields']);
        }
    }

    //..............Methods to register Pages and Sub Pages 

    public function addPages(array $pages)
    {
        $this->admin_pages = $pages;
        return $this;
    }

    public function addSubpages(array $pages)
    {
        $this->admin_subpages = array_merge($this->admin_subpages, $pages);
        return $this;
    }

    public function withSubpage(string $title = null)
    {
        if (empty($this->admin_pages)) {
            return $this;
        }
        $admin_page = $this->admin_pages[0];

        $subpages = [
            [
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => ($title) ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback'],
            ],
        ];

        $this->admin_subpages = $subpages;

        return $this;

    }

    public function addAdminMenu()
    {
        foreach ($this->admin_pages as $page) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'],
                $page['callback'], $page['icon_url'], $page['position']);
        }

        foreach ($this->admin_subpages as $page) {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'],
                $page['callback']);
        }
    }

    //..............Methods to register Custom fields section

    //set method for settings
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    //set method for sections
    public function setSections(array $sections)
    {
        $this->sections = $sections;
        return $this;
    }

    //set method for fields
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }


    public function registerCustomFields()
    {
        //Register setting
        foreach ($this->settings as $setting) {
            register_setting($setting['option_group'], $setting['option_name'],
                (isset($setting['callback'])) ? $setting['callback'] : '');

        }
        //add section in setting

        foreach ($this->sections as $section) {
            add_settings_section($section['id'], $section['title'],
                (isset($section['callback'])) ? $section['callback'] : '', $section['page']);
        }

        //add fields in section
        foreach ($this->fields as $field) {
            
            add_settings_field($field['id'], $field['title'],
                (isset($field['callback'])) ? $field['callback'] : '', $field['page'], $field['section'],
                (isset($field['args'])) ? $field['args'] : '');
                
                // echo '<pre>' . var_export($field, true) . '</pre>';;
        }
           
    }

}
