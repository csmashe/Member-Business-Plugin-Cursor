<?php
/**
 * Installation Script for Post 116 Business Directory Plugin
 * 
 * This script helps with plugin installation and setup.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Install {
    
    public static function install() {
        // Register post type and taxonomy
        Post116_BD_Post_Type::get_instance()->register_post_type();
        Post116_BD_Taxonomy::get_instance()->register_taxonomy();
        
        // Set up capabilities
        Post116_BD_Capabilities::get_instance()->add_capabilities();
        
        // Create directory page
        self::create_directory_page();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Add meta indexes
        self::add_meta_indexes();
        
        // Set default options
        self::set_default_options();
        
        // Create sample data if requested
        if (isset($_GET['create_sample_data']) && current_user_can('manage_options')) {
            self::create_sample_data();
        }
    }
    
    private static function create_directory_page() {
        $page_id = get_option('post116_bd_directory_page_id');
        
        if (!$page_id || !get_post($page_id)) {
            $page_data = array(
                'post_title' => 'Business Directory',
                'post_content' => '<!-- wp:post116/directory -->',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'directory',
            );
            
            $page_id = wp_insert_post($page_data);
            
            if ($page_id && !is_wp_error($page_id)) {
                update_option('post116_bd_directory_page_id', $page_id);
            }
        }
    }
    
    private static function add_meta_indexes() {
        global $wpdb;
        
        // Add indexes for search performance
        $wpdb->query("CREATE INDEX IF NOT EXISTS idx_owners_search ON {$wpdb->postmeta} (meta_key, meta_value(50)) WHERE meta_key = 'owners_search'");
        $wpdb->query("CREATE INDEX IF NOT EXISTS idx_city_search ON {$wpdb->postmeta} (meta_key, meta_value(50)) WHERE meta_key = 'city_search'");
    }
    
    private static function set_default_options() {
        $default_options = array(
            'directory_page_id' => get_option('post116_bd_directory_page_id'),
            'show_ownership_flags' => true,
            'enable_map_view' => false,
            'businesses_per_page' => 20,
            'custom_css' => ''
        );
        
        update_option('post116_bd_options', $default_options);
    }
    
    private static function create_sample_data() {
        // Include the test script
        if (file_exists(__DIR__ . '/test-plugin.php')) {
            include_once __DIR__ . '/test-plugin.php';
            Post116_BD_Test::run_tests();
        }
    }
}

// Run installation if this file is accessed directly
if (isset($_GET['install']) && current_user_can('manage_options')) {
    Post116_BD_Install::install();
    echo "<h2>Plugin Installation Complete!</h2>";
    echo "<p><a href='" . admin_url('edit.php?post_type=p116_business') . "'>Go to Business Directory</a></p>";
    echo "<p><a href='" . admin_url('edit.php?post_type=p116_business&page=post116-bd-settings') . "'>Configure Settings</a></p>";
    echo "<p><a href='?install=1&create_sample_data=1'>Create Sample Data</a></p>";
}