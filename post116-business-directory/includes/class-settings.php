<?php
/**
 * Settings Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Settings {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_head', array($this, 'output_custom_css'));
    }
    
    /**
     * Add settings page to admin menu
     */
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=p116_business',
            __('Directory Settings', 'post116-business-directory'),
            __('Settings', 'post116-business-directory'),
            'manage_options',
            'post116-bd-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('post116_bd_settings', 'post116_bd_options', array($this, 'sanitize_options'));
        
        add_settings_section(
            'post116_bd_general',
            __('General Settings', 'post116-business-directory'),
            array($this, 'general_section_callback'),
            'post116-bd-settings'
        );
        
        add_settings_field(
            'directory_page_id',
            __('Directory Page', 'post116-business-directory'),
            array($this, 'directory_page_field'),
            'post116-bd-settings',
            'post116_bd_general'
        );
        
        add_settings_field(
            'show_ownership_flags',
            __('Show Ownership Flags', 'post116-business-directory'),
            array($this, 'show_ownership_flags_field'),
            'post116-bd-settings',
            'post116_bd_general'
        );
        
        add_settings_field(
            'enable_map_view',
            __('Enable Map View', 'post116-business-directory'),
            array($this, 'enable_map_view_field'),
            'post116-bd-settings',
            'post116_bd_general'
        );
        
        add_settings_field(
            'businesses_per_page',
            __('Businesses Per Page', 'post116-business-directory'),
            array($this, 'businesses_per_page_field'),
            'post116-bd-settings',
            'post116_bd_general'
        );
        
        add_settings_section(
            'post116_bd_display',
            __('Display Settings', 'post116-business-directory'),
            array($this, 'display_section_callback'),
            'post116-bd-settings'
        );
        
        add_settings_field(
            'custom_css',
            __('Custom CSS', 'post116-business-directory'),
            array($this, 'custom_css_field'),
            'post116-bd-settings',
            'post116_bd_display'
        );
    }
    
    /**
     * Settings page HTML
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Business Directory Settings', 'post116-business-directory'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('post116_bd_settings');
                do_settings_sections('post116-bd-settings');
                submit_button();
                ?>
            </form>
            
            <div class="post116-settings-info">
                <h2><?php _e('Shortcode Usage', 'post116-business-directory'); ?></h2>
                <p><?php _e('You can display the business directory on any page using the following shortcode:', 'post116-business-directory'); ?></p>
                <code>[post116_directory]</code>
                
                <h3><?php _e('Shortcode Attributes', 'post116-business-directory'); ?></h3>
                <ul>
                    <li><code>show_flags</code> - <?php _e('Show ownership flags (true/false, default: true)', 'post116-business-directory'); ?></li>
                    <li><code>per_page</code> - <?php _e('Number of businesses per page (default: 20)', 'post116-business-directory'); ?></li>
                    <li><code>placeholder</code> - <?php _e('Search placeholder text', 'post116-business-directory'); ?></li>
                </ul>
                
                <h3><?php _e('Example', 'post116-business-directory'); ?></h3>
                <code>[post116_directory show_flags="true" per_page="10" placeholder="Search our member businesses..."]</code>
            </div>
        </div>
        <?php
    }
    
    /**
     * General section callback
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure general settings for the business directory.', 'post116-business-directory') . '</p>';
    }
    
    /**
     * Display section callback
     */
    public function display_section_callback() {
        echo '<p>' . __('Customize the appearance of the business directory.', 'post116-business-directory') . '</p>';
    }
    
    /**
     * Directory page field
     */
    public function directory_page_field() {
        $options = get_option('post116_bd_options', array());
        $directory_page_id = isset($options['directory_page_id']) ? $options['directory_page_id'] : get_option('post116_bd_directory_page_id');
        
        $pages = get_pages(array(
            'post_status' => 'publish',
            'sort_column' => 'post_title',
            'sort_order' => 'ASC'
        ));
        
        echo '<select name="post116_bd_options[directory_page_id]">';
        echo '<option value="">' . __('Select a page...', 'post116-business-directory') . '</option>';
        
        foreach ($pages as $page) {
            $selected = selected($directory_page_id, $page->ID, false);
            echo '<option value="' . $page->ID . '"' . $selected . '>' . esc_html($page->post_title) . '</option>';
        }
        
        echo '</select>';
        echo '<p class="description">' . __('Select the page that contains the business directory.', 'post116-business-directory') . '</p>';
    }
    
    /**
     * Show ownership flags field
     */
    public function show_ownership_flags_field() {
        $options = get_option('post116_bd_options', array());
        $show_flags = isset($options['show_ownership_flags']) ? $options['show_ownership_flags'] : true;
        
        echo '<label>';
        echo '<input type="checkbox" name="post116_bd_options[show_ownership_flags]" value="1" ' . checked($show_flags, true, false) . ' /> ';
        echo __('Show ownership flags (Veteran Owned, Sons Owned, Auxiliary Owned) in the directory', 'post116-business-directory');
        echo '</label>';
    }
    
    /**
     * Enable map view field
     */
    public function enable_map_view_field() {
        $options = get_option('post116_bd_options', array());
        $enable_map = isset($options['enable_map_view']) ? $options['enable_map_view'] : false;
        
        echo '<label>';
        echo '<input type="checkbox" name="post116_bd_options[enable_map_view]" value="1" ' . checked($enable_map, true, false) . ' /> ';
        echo __('Enable map view for businesses with addresses (Phase 2 feature)', 'post116-business-directory');
        echo '</label>';
    }
    
    /**
     * Businesses per page field
     */
    public function businesses_per_page_field() {
        $options = get_option('post116_bd_options', array());
        $per_page = isset($options['businesses_per_page']) ? $options['businesses_per_page'] : 20;
        
        echo '<input type="number" name="post116_bd_options[businesses_per_page]" value="' . esc_attr($per_page) . '" min="1" max="100" />';
        echo '<p class="description">' . __('Number of businesses to display per page (1-100)', 'post116-business-directory') . '</p>';
    }
    
    /**
     * Custom CSS field
     */
    public function custom_css_field() {
        $options = get_option('post116_bd_options', array());
        $custom_css = isset($options['custom_css']) ? $options['custom_css'] : '';
        
        echo '<textarea name="post116_bd_options[custom_css]" rows="10" cols="50" class="large-text code">' . esc_textarea($custom_css) . '</textarea>';
        echo '<p class="description">' . __('Add custom CSS to style the business directory. This will be added to the page head.', 'post116-business-directory') . '</p>';
    }
    
    /**
     * Sanitize options
     */
    public function sanitize_options($input) {
        $sanitized = array();
        
        if (isset($input['directory_page_id'])) {
            $sanitized['directory_page_id'] = absint($input['directory_page_id']);
        }
        
        if (isset($input['show_ownership_flags'])) {
            $sanitized['show_ownership_flags'] = true;
        } else {
            $sanitized['show_ownership_flags'] = false;
        }
        
        if (isset($input['enable_map_view'])) {
            $sanitized['enable_map_view'] = true;
        } else {
            $sanitized['enable_map_view'] = false;
        }
        
        if (isset($input['businesses_per_page'])) {
            $per_page = absint($input['businesses_per_page']);
            $sanitized['businesses_per_page'] = max(1, min(100, $per_page));
        } else {
            $sanitized['businesses_per_page'] = 20;
        }
        
        if (isset($input['custom_css'])) {
            $sanitized['custom_css'] = wp_strip_all_tags($input['custom_css']);
        }
        
        return $sanitized;
    }
    
    /**
     * Output custom CSS
     */
    public function output_custom_css() {
        $options = get_option('post116_bd_options', array());
        $custom_css = isset($options['custom_css']) ? $options['custom_css'] : '';
        
        if (!empty($custom_css)) {
            echo '<style type="text/css">' . "\n";
            echo $custom_css . "\n";
            echo '</style>' . "\n";
        }
    }
}