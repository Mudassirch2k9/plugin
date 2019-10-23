<?php

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;

class GalleryController extends BaseController
{
    public $settings_api;

    public $adminCallbacks;

    public $subpages;

    public function register()
    {
        if(!$this->activeModule('gallery_manager')) return;

        $this->settings_api = new SettingsApi();

        $this->adminCallbacks = new AdminCallBacks();

        $this->setSubpages();

        $this->settings_api->addSubpages($this->subpages)->register();
    }

    public function setSubpages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'mft_settings',
                'page_title' => 'Gallery Manager',
                'menu_title' => 'Gallery Manager',
                'capability' => 'manage_options',
                'menu_slug' => 'mft_gallery',
                'callback' => [$this->adminCallbacks, 'adminGallery'],
            ],
        ];
    }
}
