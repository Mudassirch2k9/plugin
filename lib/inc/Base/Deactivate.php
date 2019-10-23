<?php
/**
 * @package  OPAPlugin
 */
namespace OPA\Inc\base;

class Deactivate{
    public static function deactivate(){
        flush_rewrite_rules();
    }
}

