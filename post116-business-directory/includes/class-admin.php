<?php
/**
 * Admin Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('manage_p116_business_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_p116_business_posts_custom_column', array($this, 'populate_admin_columns'), 10, 2);
        add_filter('manage_edit-p116_business_sortable_columns', array($this, 'make_columns_sortable'));
        add_action('pre_get_posts', array($this, 'handle_column_sorting'));
        add_action('restrict_manage_posts', array($this, 'add_admin_filters'));
        add_filter('parse_query', array($this, 'handle_admin_filters'));
    }
    
    /**
     * Add custom columns to admin list table
     */
    public function add_admin_columns($columns) {
        // Remove date column and add custom ones
        unset($columns['date']);
        
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            // Insert custom columns after title
            if ($key === 'title') {
                $new_columns['categories'] = __('Categories', 'post116-business-directory');
                $new_columns['owners'] = __('Owners', 'post116-business-directory');
                $new_columns['city'] = __('City', 'post116-business-directory');
                $new_columns['phone'] = __('Phone', 'post116-business-directory');
                $new_columns['ownership_flags'] = __('Ownership', 'post116-business-directory');
            }
        }
        
        // Add date back at the end
        $new_columns['date'] = __('Date', 'post116-business-directory');
        
        return $new_columns;
    }
    
    /**
     * Populate custom columns
     */
    public function populate_admin_columns($column, $post_id) {
        switch ($column) {
            case 'categories':
                $terms = get_the_terms($post_id, 'p116_business_category');
                if ($terms && !is_wp_error($terms)) {
                    $term_names = array();
                    foreach ($terms as $term) {
                        $term_names[] = $term->name;
                    }
                    echo implode(', ', $term_names);
                } else {
                    echo '—';
                }
                break;
                
            case 'owners':
                $owners = get_post_meta($post_id, 'owners', true);
                if (is_array($owners) && !empty($owners)) {
                    $owner_names = array();
                    $count = 0;
                    foreach ($owners as $owner) {
                        if (!empty($owner['owner_name'])) {
                            $owner_names[] = esc_html($owner['owner_name']);
                            $count++;
                            if ($count >= 2) break; // Show only first two
                        }
                    }
                    echo implode(', ', $owner_names);
                    if (count($owners) > 2) {
                        echo ' <span class="description">(+' . (count($owners) - 2) . ' more)</span>';
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'city':
                $city = get_post_meta($post_id, 'city', true);
                echo $city ? esc_html($city) : '—';
                break;
                
            case 'phone':
                $phone = get_post_meta($post_id, 'business_phone', true);
                echo $phone ? esc_html($phone) : '—';
                break;
                
            case 'ownership_flags':
                $flags = array();
                if (get_post_meta($post_id, 'veteran_owned', true) === '1') {
                    $flags[] = '<span class="post116-flag veteran">' . __('Veteran', 'post116-business-directory') . '</span>';
                }
                if (get_post_meta($post_id, 'sons_owned', true) === '1') {
                    $flags[] = '<span class="post116-flag sons">' . __('Sons', 'post116-business-directory') . '</span>';
                }
                if (get_post_meta($post_id, 'auxiliary_owned', true) === '1') {
                    $flags[] = '<span class="post116-flag auxiliary">' . __('Auxiliary', 'post116-business-directory') . '</span>';
                }
                
                if (!empty($flags)) {
                    echo implode(' ', $flags);
                } else {
                    echo '—';
                }
                break;
        }
    }
    
    /**
     * Make columns sortable
     */
    public function make_columns_sortable($columns) {
        $columns['city'] = 'city';
        $columns['ownership_flags'] = 'ownership_flags';
        return $columns;
    }
    
    /**
     * Handle column sorting
     */
    public function handle_column_sorting($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        $orderby = $query->get('orderby');
        
        switch ($orderby) {
            case 'city':
                $query->set('meta_key', 'city');
                $query->set('orderby', 'meta_value');
                break;
                
            case 'ownership_flags':
                $query->set('meta_key', 'veteran_owned');
                $query->set('orderby', 'meta_value');
                break;
        }
    }
    
    /**
     * Add admin filters
     */
    public function add_admin_filters() {
        global $typenow;
        
        if ($typenow === 'p116_business') {
            // Category filter
            $taxonomy = 'p116_business_category';
            $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
            wp_dropdown_categories(array(
                'show_option_all' => __('All Categories', 'post116-business-directory'),
                'taxonomy' => $taxonomy,
                'name' => $taxonomy,
                'value_field' => 'slug',
                'selected' => $selected,
                'show_count' => true,
                'hide_empty' => false,
            ));
            
            // Ownership flags filter
            $ownership_filter = isset($_GET['ownership_filter']) ? $_GET['ownership_filter'] : '';
            echo '<select name="ownership_filter">';
            echo '<option value="">' . __('All Ownership Types', 'post116-business-directory') . '</option>';
            echo '<option value="veteran"' . selected($ownership_filter, 'veteran', false) . '>' . __('Veteran Owned', 'post116-business-directory') . '</option>';
            echo '<option value="sons"' . selected($ownership_filter, 'sons', false) . '>' . __('Sons Owned', 'post116-business-directory') . '</option>';
            echo '<option value="auxiliary"' . selected($ownership_filter, 'auxiliary', false) . '>' . __('Auxiliary Owned', 'post116-business-directory') . '</option>';
            echo '</select>';
            
            // Show in directory filter
            $show_filter = isset($_GET['show_in_directory']) ? $_GET['show_in_directory'] : '';
            echo '<select name="show_in_directory">';
            echo '<option value="">' . __('All Businesses', 'post116-business-directory') . '</option>';
            echo '<option value="1"' . selected($show_filter, '1', false) . '>' . __('Shown in Directory', 'post116-business-directory') . '</option>';
            echo '<option value="0"' . selected($show_filter, '0', false) . '>' . __('Hidden from Directory', 'post116-business-directory') . '</option>';
            echo '</select>';
        }
    }
    
    /**
     * Handle admin filters
     */
    public function handle_admin_filters($query) {
        global $pagenow, $typenow;
        
        if ($pagenow === 'edit.php' && $typenow === 'p116_business') {
            $meta_query = $query->get('meta_query');
            if (!is_array($meta_query)) {
                $meta_query = array();
            }
            
            // Ownership filter
            if (isset($_GET['ownership_filter']) && !empty($_GET['ownership_filter'])) {
                $ownership_type = sanitize_text_field($_GET['ownership_filter']);
                $meta_query[] = array(
                    'key' => $ownership_type . '_owned',
                    'value' => '1',
                    'compare' => '='
                );
            }
            
            // Show in directory filter
            if (isset($_GET['show_in_directory']) && $_GET['show_in_directory'] !== '') {
                $show_value = sanitize_text_field($_GET['show_in_directory']);
                $meta_query[] = array(
                    'key' => 'show_in_directory',
                    'value' => $show_value,
                    'compare' => '='
                );
            }
            
            if (!empty($meta_query)) {
                $query->set('meta_query', $meta_query);
            }
        }
    }
}