<?php
/**
 * Custom Post Type Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Post_Type {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'));
    }
    
    /**
     * Register the p116_business custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name' => __('Businesses', 'post116-business-directory'),
            'singular_name' => __('Business', 'post116-business-directory'),
            'menu_name' => __('Business Directory', 'post116-business-directory'),
            'name_admin_bar' => __('Business', 'post116-business-directory'),
            'archives' => __('Business Archives', 'post116-business-directory'),
            'attributes' => __('Business Attributes', 'post116-business-directory'),
            'parent_item_colon' => __('Parent Business:', 'post116-business-directory'),
            'all_items' => __('All Businesses', 'post116-business-directory'),
            'add_new_item' => __('Add New Business', 'post116-business-directory'),
            'add_new' => __('Add New', 'post116-business-directory'),
            'new_item' => __('New Business', 'post116-business-directory'),
            'edit_item' => __('Edit Business', 'post116-business-directory'),
            'update_item' => __('Update Business', 'post116-business-directory'),
            'view_item' => __('View Business', 'post116-business-directory'),
            'view_items' => __('View Businesses', 'post116-business-directory'),
            'search_items' => __('Search Businesses', 'post116-business-directory'),
            'not_found' => __('Not found', 'post116-business-directory'),
            'not_found_in_trash' => __('Not found in Trash', 'post116-business-directory'),
            'featured_image' => __('Business Logo', 'post116-business-directory'),
            'set_featured_image' => __('Set business logo', 'post116-business-directory'),
            'remove_featured_image' => __('Remove business logo', 'post116-business-directory'),
            'use_featured_image' => __('Use as business logo', 'post116-business-directory'),
            'insert_into_item' => __('Insert into business', 'post116-business-directory'),
            'uploaded_to_this_item' => __('Uploaded to this business', 'post116-business-directory'),
            'items_list' => __('Businesses list', 'post116-business-directory'),
            'items_list_navigation' => __('Businesses list navigation', 'post116-business-directory'),
            'filter_items_list' => __('Filter businesses list', 'post116-business-directory'),
        );
        
        $args = array(
            'label' => __('Business', 'post116-business-directory'),
            'description' => __('Member businesses and services directory', 'post116-business-directory'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
            'taxonomies' => array('p116_business_category'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-store',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'business',
            'map_meta_cap' => true,
            'show_in_rest' => true,
            'rest_base' => 'businesses',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite' => array(
                'slug' => 'directory',
                'with_front' => false,
            ),
        );
        
        register_post_type('p116_business', $args);
    }
}