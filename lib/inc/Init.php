<?php

/**
 * @package  OPA Plugin
 */

namespace OPA\Inc;

final class Init
{
    /**
     * Store all classes inside
     * @return array full list of classes
     */
    public static function get_services()
    {
        return [
            Pages\Dashboard::class,
            Base\SettingsLink::class,
            Base\Enqueue::class,
            Pages\CustomPostTypeController::class,
            Pages\FilterQuestions::class,
            // Base\GalleryController::class,
        ];
    }
    /**
     * Loop through the class, initialize them,
     * and call register function if exists
     * @return
     */

    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize Class
     * @param class $class     class from the service array
     * @return class instance  new instance of class
     *
     */
    private static function instantiate($class)
    {
        $service = new $class();
        return $service;
    }

}
