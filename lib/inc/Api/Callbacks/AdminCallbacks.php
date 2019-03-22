<?php
/**
 * @package  OPAPlugin
 */

 namespace OPA\Inc\Api\Callbacks;

 use \OPA\Inc\Base\BaseController;

class AdminCallbacks extends BaseController
{

    public function adminDashboard()
    {
        return require_once("$this->plugin_path/templates/admin.php");
    }
    
    public function adminCPT()
    {
        return require_once("$this->plugin_path/templates/product_manager.php");
    }
    
    public function adminFQ()
    {
        return require_once("$this->plugin_path/templates/filter_questions.php");
    }
    
    public function adminFqOther()
    {
        return require_once("$this->plugin_path/templates/filter_questions_other.php");
    }
    
    
}