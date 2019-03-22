<?php

/**
 * @package  OPA Plugin
 */

namespace OPA\Inc\Pages;

use OPA\Inc\Api\Callbacks\AdminCallbacks;
use OPA\Inc\Api\Callbacks\CptCallbacks;
use OPA\Inc\Api\SettingsApi;
use OPA\Inc\Base\BaseController;

class CustomPostTypeController extends BaseController
{
    public $settings_api;

    public $subpages;

    public $fields;

    public $adminCallbacks;

    public $cptCallbacks;

    public $custom_post_types = [];
    
    public $metaBoxes=[];

    public function register()
    {
        if (!$this->activeModule('product_manager')) {
            return;
        }

        $this->settings_api = new SettingsApi();

        $this->adminCallbacks = new AdminCallbacks();

        $this->cptCallbacks = new cptCallbacks();

        $this->setSubpages();

        $this->setSettings();

        $this->setSection();

        $this->setFields();

        $this->settings_api->addSubpages($this->subpages)->register();

        $this->addPostType();

        if (!empty($this->custom_post_types)) {
            add_action('init', [$this, 'registerPostTypes']);
        }

        //metaboxes
        $this->addMetaBox();

        if (!empty($this->metaBoxes))
        {
            add_action('edit_form_after_title', [$this, 'registerCustomMetaBoxContext']);
            add_action('add_meta_boxes', [$this, 'registerMetaBoxes']);
            add_action('save_post', [$this, 'saveMetaBoxesData']);
        }
    }

    //..............Methods to register Custom fields section

    //register custom context to dsiplay it after title
    public function registerCustomMetaBoxContext($post)
    {
        do_meta_boxes(null, 'custom_metabox_holder', $post);
    }

    //add metabox data
    public function addMetaBox()
    {
        $product_attr_fields = [];

        $attributes = (get_option($this->option_name_product_attributes)) ?: [];

        foreach ($attributes as $attr) {
            $id         =   (isset($attr['id'])) ? $attr['id']: "";
            $title       =   (isset($attr['name'])) ? $attr['name']: "";
            $type       =   (isset($attr['type'])) ? $attr['type']: "";
            $placeholder =  (isset($attr['placeholder'])) ? $attr['placeholder']: null;

            $product_attr_fields[] = [
                'id' => $id,
                'title' => $title,
                'type' => $type,
                'classes' => '',
                'placeholder' => $placeholder
            ];
        }

        $this->metaBoxes = [
            [
                'id' => 'product_spec',
                'title' => 'Product Specification',
                'callback' => [ $this->cptCallbacks,'createMetaFields'],
                'screen' => 'product',
                'context' => 'custom_metabox_holder',
                'priority' => 'high',
                'args' => [
                    'fields' => $product_attr_fields
                ],
            ],
            
        ];
    }
    
    public function registerMetaBoxes()
    {
        
        foreach ($this->metaBoxes as $metaBox) {

            add_meta_box($metaBox['id'], $metaBox['title'], $metaBox['callback'], $metaBox['screen'],
                $metaBox['context'], $metaBox['priority'], $metaBox['args']);
        }
        
    }

    public function saveMetaBoxesData($post_id)
    {
        
        // Checks save status
        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = (isset($_POST['prfx_nonce']) && wp_verify_nonce($_POST['prfx_nonce'], basename(__FILE__))) ? 'true' : 'false';

        // Exits script depending on save status
        if ($is_autosave || $is_revision || !$is_valid_nonce) {
            return;
        }
        
        // Checks for input and sanitizes/saves if needed
        foreach ($this->metaBoxes as $metaBox) {

            foreach ($metaBox['args']['fields'] as $field) {

                if (isset($_POST[$field['id']])) {
                    $value =  sanitize_text_field($_POST[$field['id']]);
                    
                    update_post_meta( $post_id, $field['id'], $value );

                    // when saving text type
                }
            }
        }

        
    }


    //....plugin settings pages & options

    //set subpage
    public function setSubpages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'opa_settings',
                'page_title' => 'Product Manger',
                'menu_title' => 'Product Manager',
                'capability' => 'manage_options',
                'menu_slug' => $this->page_slug_product_manager,
                'callback' => [$this->adminCallbacks, 'adminCPT'],
            ],
        ];
    }

    //set settings
    public function setSettings()
    {
        $args = [
            [
                'option_group' => $this->option_group_product_manager,
                'option_name' => $this->option_name_product_attributes,
                'callback' => [$this->cptCallbacks, 'cptSanitizer'],
            ],
        ];

        $this->settings_api->setSettings($args);
    }

    //set section
    public function setSection()
    {
        $args = [
            [
                'id' => 'opa_cpt_index',
                'title' => 'Product Attribute Form',
                'callback' => [$this->cptCallbacks, 'cptSection'],
                'page' => $this->page_slug_product_manager,
            ],
        ];

        $this->settings_api->setSections($args);
    }

    //set fields
    public function setFields()
    {
        $args =
            [
                
                [
                    'id' => 'name',
                    'title' => 'Name',
                    'callback' => [$this->cptCallbacks, 'settingsTextboxField'],
                    'page' => $this->page_slug_product_manager,
                    'section' => 'opa_cpt_index',
                    'args' => [
                        'option_name' => $this->option_name_product_attributes,
                        'label_for' => 'name',
                        'placeholder' => 'eg. Price',
                    ],
                ],
            [
                'id' => 'type',
                'title' => 'Type',
                'callback' => [$this->cptCallbacks, 'settingsSelectField'],
                'page' => $this->page_slug_product_manager,
                'section' => 'opa_cpt_index',
                'args' => [
                    'option_name' => $this->option_name_product_attributes,
                    'label_for' => 'type',
                    'disabled_option' => ' select one',
                    'options' => [
                        'text' => 'Text',
                        'number' => 'Number',
                        'checkbox' => 'Checkbox',
                    ]
                ],
            ],
            [
                'id' => 'placeholder',
                'title' => 'Placeholder',
                'callback' => [$this->cptCallbacks, 'settingsTextboxField'],
                'page' => $this->page_slug_product_manager,
                'section' => 'opa_cpt_index',
                'args' => [
                    'option_name' => $this->option_name_product_attributes,
                    'label_for' => 'placeholder',
                    'placeholder' => 'e.g. 100.00',
                ],
            ]
        ];

        //    var_dump($args);
        $this->settings_api->setFields($args);
    }

    //.... post type section

    public function addPostType()
    {
        $options = [
            [
                'post_type' => 'product',
                'plural_name' => 'Products',
                'singular_name' => 'Product',
                'public' => true,
                'has_archive' => true,
                'taxonomies' => ['category', 'post_tag']
            ],
        ];

        foreach ($options as $option) {

            $this->custom_post_types[] = [
                'post_type' => $option['post_type'],
                'name' => $option['plural_name'],
                'singular_name' => $option['singular_name'],
                'menu_name' => $option['plural_name'],
                'name_admin_bar' => $option['singular_name'],
                'archives' => $option['singular_name'] . ' Archives',
                'attributes' => $option['singular_name'] . ' Attributes',
                'parent_item_colon' => 'Parent ' . $option['singular_name'],
                'all_items' => 'All ' . $option['plural_name'],
                'add_new_item' => 'Add New ' . $option['singular_name'],
                'add_new' => 'Add New',
                'new_item' => 'New ' . $option['singular_name'],
                'edit_item' => 'Edit ' . $option['singular_name'],
                'update_item' => 'Update ' . $option['singular_name'],
                'view_item' => 'View ' . $option['singular_name'],
                'view_items' => 'View ' . $option['plural_name'],
                'search_items' => 'Search ' . $option['plural_name'],
                'not_found' => 'No ' . $option['singular_name'] . ' Found',
                'not_found_in_trash' => 'No ' . $option['singular_name'] . ' Found in Trash',
                'featured_image' => 'Featured Image',
                'set_featured_image' => 'Set Featured Image',
                'remove_featured_image' => 'Remove Featured Image',
                'use_featured_image' => 'Use Featured Image',
                'insert_into_item' => 'Insert into ' . $option['singular_name'],
                'uploaded_to_this_item' => 'Upload to this ' . $option['singular_name'],
                'items_list' => $option['plural_name'] . ' List',
                'items_list_navigation' => $option['plural_name'] . ' List Navigation',
                'filter_items_list' => 'Filter' . $option['plural_name'] . ' List',
                'label' => $option['singular_name'],
                'description' => $option['plural_name'] . ' Custom Post Type',
                'supports' => ['title', 'editor', 'thumbnail'],
                'taxonomies' => isset($option['taxonomies']) ? $option['taxonomies']: [],
                'hierarchical' => false,
                'public' => isset($option['public']) ?: false,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => isset($option['has_archive']) ?: false,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'capability_type' => 'post',
            ];
        }
    }

    public function registerPostTypes()
    {
        foreach ($this->custom_post_types as $post_type) {
            register_post_type($post_type['post_type'],
                [
                    'labels' => [
                        'name' => $post_type['name'],
                        'singular_name' => $post_type['singular_name'],
                        'menu_name' => $post_type['menu_name'],
                        'name_admin_bar' => $post_type['name_admin_bar'],
                        'archives' => $post_type['archives'],
                        'attributes' => $post_type['attributes'],
                        'parent_item_colon' => $post_type['parent_item_colon'],
                        'all_items' => $post_type['all_items'],
                        'add_new_item' => $post_type['add_new_item'],
                        'add_new' => $post_type['add_new'],
                        'new_item' => $post_type['new_item'],
                        'edit_item' => $post_type['edit_item'],
                        'update_item' => $post_type['update_item'],
                        'view_item' => $post_type['view_item'],
                        'view_items' => $post_type['view_items'],
                        'search_items' => $post_type['search_items'],
                        'not_found' => $post_type['not_found'],
                        'not_found_in_trash' => $post_type['not_found_in_trash'],
                        'featured_image' => $post_type['featured_image'],
                        'set_featured_image' => $post_type['set_featured_image'],
                        'remove_featured_image' => $post_type['remove_featured_image'],
                        'use_featured_image' => $post_type['use_featured_image'],
                        'insert_into_item' => $post_type['insert_into_item'],
                        'uploaded_to_this_item' => $post_type['uploaded_to_this_item'],
                        'items_list' => $post_type['items_list'],
                        'items_list_navigation' => $post_type['items_list_navigation'],
                        'filter_items_list' => $post_type['filter_items_list'],
                    ],
                    'label' => $post_type['label'],
                    'description' => $post_type['description'],
                    'supports' => $post_type['supports'],
                    'taxonomies' => $post_type['taxonomies'],
                    'hierarchical' => $post_type['hierarchical'],
                    'public' => $post_type['public'],
                    'show_ui' => $post_type['show_ui'],
                    'show_in_menu' => $post_type['show_in_menu'],
                    'menu_position' => $post_type['menu_position'],
                    'show_in_admin_bar' => $post_type['show_in_admin_bar'],
                    'show_in_nav_menus' => $post_type['show_in_nav_menus'],
                    'can_export' => $post_type['can_export'],
                    'has_archive' => $post_type['has_archive'],
                    'exclude_from_search' => $post_type['exclude_from_search'],
                    'publicly_queryable' => $post_type['publicly_queryable'],
                    'capability_type' => $post_type['capability_type'],
                ]
            );
        }
    }

}
