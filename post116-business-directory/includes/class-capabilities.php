<?php
/**
 * Capabilities Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Capabilities {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'add_capabilities'));
    }
    
    /**
     * Add capabilities to all roles
     */
    public function add_capabilities() {
        // Define capabilities
        $capabilities = array(
            'edit_business',
            'edit_businesses',
            'edit_others_businesses',
            'publish_businesses',
            'read_private_businesses',
            'delete_business',
            'delete_businesses',
            'delete_private_businesses',
            'delete_published_businesses',
            'delete_others_businesses',
            'edit_published_businesses',
            'manage_business_categories',
            'edit_business_categories',
            'delete_business_categories',
            'assign_business_categories'
        );
        
        // Get all roles
        $roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->add_cap($cap);
                }
            }
        }
    }
    
    /**
     * Remove capabilities (for deactivation)
     */
    public function remove_capabilities() {
        $capabilities = array(
            'edit_business',
            'edit_businesses',
            'edit_others_businesses',
            'publish_businesses',
            'read_private_businesses',
            'delete_business',
            'delete_businesses',
            'delete_private_businesses',
            'delete_published_businesses',
            'delete_others_businesses',
            'edit_published_businesses',
            'manage_business_categories',
            'edit_business_categories',
            'delete_business_categories',
            'assign_business_categories'
        );
        
        $roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                foreach ($capabilities as $cap) {
                    $role->remove_cap($cap);
                }
            }
        }
    }
}