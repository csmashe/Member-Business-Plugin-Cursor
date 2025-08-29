<?php
/**
 * Plugin Name: Post 116 Business Directory
 * Plugin URI: https://alpost116nc.org
 * Description: A comprehensive business directory for American Legion Post 116 member businesses with search, categories, and owner management.
 * Version: 1.0.0
 * Author: American Legion Post 116
 * License: GPL v2 or later
 * Text Domain: post116-business-directory
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('POST116_BD_VERSION', '1.0.0');
define('POST116_BD_PLUGIN_FILE', __FILE__);
define('POST116_BD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POST116_BD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POST116_BD_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Post116_Business_Directory {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-post-type.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-taxonomy.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-meta-fields.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-admin.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-rest-api.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-gutenberg-block.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-template-loader.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-settings.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-capabilities.php';
        require_once POST116_BD_PLUGIN_DIR . 'includes/class-schema.php';
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('post116-business-directory', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize components
        Post116_BD_Post_Type::get_instance();
        Post116_BD_Taxonomy::get_instance();
        Post116_BD_Meta_Fields::get_instance();
        Post116_BD_Admin::get_instance();
        Post116_BD_REST_API::get_instance();
        Post116_BD_Gutenberg_Block::get_instance();
        Post116_BD_Template_Loader::get_instance();
        Post116_BD_Settings::get_instance();
        Post116_BD_Capabilities::get_instance();
        Post116_BD_Schema::get_instance();
    }
    
    /**
     * Enqueue public scripts and styles
     */
    public function enqueue_public_scripts() {
        wp_enqueue_script(
            'post116-bd-public',
            POST116_BD_PLUGIN_URL . 'public/js/public.js',
            array('jquery'),
            POST116_BD_VERSION,
            true
        );
        
        wp_enqueue_style(
            'post116-bd-public',
            POST116_BD_PLUGIN_URL . 'public/css/public.css',
            array(),
            POST116_BD_VERSION
        );
        
        // Localize script for AJAX
        wp_localize_script('post116-bd-public', 'post116_bd_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('p116/v1/'),
            'nonce' => wp_create_nonce('post116_bd_nonce'),
            'strings' => array(
                'search_placeholder' => __('Search businesses, owners, or categories...', 'post116-business-directory'),
                'no_results' => __('No businesses found.', 'post116-business-directory'),
                'loading' => __('Loading...', 'post116-business-directory'),
            )
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;
        
        if ('p116_business' === $post_type || strpos($hook, 'post116') !== false) {
            wp_enqueue_script(
                'post116-bd-admin',
                POST116_BD_PLUGIN_URL . 'public/js/admin.js',
                array('jquery', 'jquery-ui-sortable'),
                POST116_BD_VERSION,
                true
            );
            
            wp_enqueue_style(
                'post116-bd-admin',
                POST116_BD_PLUGIN_URL . 'public/css/admin.css',
                array(),
                POST116_BD_VERSION
            );
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Register post type and taxonomy
        Post116_BD_Post_Type::get_instance()->register_post_type();
        Post116_BD_Taxonomy::get_instance()->register_taxonomy();
        
        // Set up capabilities
        Post116_BD_Capabilities::get_instance()->add_capabilities();
        
        // Create directory page
        $this->create_directory_page();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Add meta indexes
        $this->add_meta_indexes();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create default directory page
     */
    private function create_directory_page() {
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
    
    /**
     * Add meta indexes for performance
     */
    private function add_meta_indexes() {
        global $wpdb;
        
        // Add indexes for search performance
        $wpdb->query("CREATE INDEX IF NOT EXISTS idx_owners_search ON {$wpdb->postmeta} (meta_key, meta_value(50)) WHERE meta_key = 'owners_search'");
        $wpdb->query("CREATE INDEX IF NOT EXISTS idx_city_search ON {$wpdb->postmeta} (meta_key, meta_value(50)) WHERE meta_key = 'city_search'");
    }
}

// Initialize the plugin
Post116_Business_Directory::get_instance();