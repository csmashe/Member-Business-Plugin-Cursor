<?php
/**
 * Custom Taxonomy Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Taxonomy {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_taxonomy'));
    }
    
    /**
     * Register the p116_business_category taxonomy
     */
    public function register_taxonomy() {
        $labels = array(
            'name' => __('Business Categories', 'post116-business-directory'),
            'singular_name' => __('Business Category', 'post116-business-directory'),
            'menu_name' => __('Categories', 'post116-business-directory'),
            'all_items' => __('All Categories', 'post116-business-directory'),
            'parent_item' => __('Parent Category', 'post116-business-directory'),
            'parent_item_colon' => __('Parent Category:', 'post116-business-directory'),
            'new_item_name' => __('New Category Name', 'post116-business-directory'),
            'add_new_item' => __('Add New Category', 'post116-business-directory'),
            'edit_item' => __('Edit Category', 'post116-business-directory'),
            'update_item' => __('Update Category', 'post116-business-directory'),
            'view_item' => __('View Category', 'post116-business-directory'),
            'separate_items_with_commas' => __('Separate categories with commas', 'post116-business-directory'),
            'add_or_remove_items' => __('Add or remove categories', 'post116-business-directory'),
            'choose_from_most_used' => __('Choose from the most used', 'post116-business-directory'),
            'popular_items' => __('Popular Categories', 'post116-business-directory'),
            'search_items' => __('Search Categories', 'post116-business-directory'),
            'not_found' => __('Not Found', 'post116-business-directory'),
            'no_terms' => __('No categories', 'post116-business-directory'),
            'items_list' => __('Categories list', 'post116-business-directory'),
            'items_list_navigation' => __('Categories list navigation', 'post116-business-directory'),
        );
        
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'rest_base' => 'business-categories',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
            'rewrite' => array(
                'slug' => 'directory/category',
                'with_front' => false,
            ),
        );
        
        register_taxonomy('p116_business_category', array('p116_business'), $args);
    }
}