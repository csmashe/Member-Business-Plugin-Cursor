<?php
/**
 * REST API Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_REST_API {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('p116/v1', '/search', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_businesses'),
            'permission_callback' => '__return_true',
            'args' => array(
                'search' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'category' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'veteran_owned' => array(
                    'required' => false,
                    'type' => 'boolean',
                ),
                'sons_owned' => array(
                    'required' => false,
                    'type' => 'boolean',
                ),
                'auxiliary_owned' => array(
                    'required' => false,
                    'type' => 'boolean',
                ),
                'city' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'per_page' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 20,
                ),
                'page' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 1,
                ),
            ),
        ));
        
        register_rest_route('p116/v1', '/autocomplete', array(
            'methods' => 'GET',
            'callback' => array($this, 'autocomplete'),
            'permission_callback' => '__return_true',
            'args' => array(
                'search' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'type' => array(
                    'required' => false,
                    'type' => 'string',
                    'enum' => array('business', 'owner', 'category'),
                    'default' => 'business',
                ),
                'limit' => array(
                    'required' => false,
                    'type' => 'integer',
                    'default' => 10,
                ),
            ),
        ));
    }
    
    /**
     * Search businesses
     */
    public function search_businesses($request) {
        $search = $request->get_param('search');
        $category = $request->get_param('category');
        $veteran_owned = $request->get_param('veteran_owned');
        $sons_owned = $request->get_param('sons_owned');
        $auxiliary_owned = $request->get_param('auxiliary_owned');
        $city = $request->get_param('city');
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        
        $args = array(
            'post_type' => 'p116_business',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'show_in_directory',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'tax_query' => array(),
        );
        
        // Add search
        if (!empty($search)) {
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key' => 'owners_search',
                    'value' => $search,
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'city_search',
                    'value' => $search,
                    'compare' => 'LIKE'
                )
            );
            
            // Also search in post title and content
            $args['s'] = $search;
        }
        
        // Add category filter
        if (!empty($category)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'p116_business_category',
                'field' => 'slug',
                'terms' => $category,
            );
        }
        
        // Add ownership filters
        if ($veteran_owned) {
            $args['meta_query'][] = array(
                'key' => 'veteran_owned',
                'value' => '1',
                'compare' => '='
            );
        }
        
        if ($sons_owned) {
            $args['meta_query'][] = array(
                'key' => 'sons_owned',
                'value' => '1',
                'compare' => '='
            );
        }
        
        if ($auxiliary_owned) {
            $args['meta_query'][] = array(
                'key' => 'auxiliary_owned',
                'value' => '1',
                'compare' => '='
            );
        }
        
        // Add city filter
        if (!empty($city)) {
            $args['meta_query'][] = array(
                'key' => 'city',
                'value' => $city,
                'compare' => '='
            );
        }
        
        // Set order by title
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
        
        $query = new WP_Query($args);
        
        $businesses = array();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $businesses[] = $this->format_business_data(get_post());
            }
        }
        wp_reset_postdata();
        
        return new WP_REST_Response(array(
            'businesses' => $businesses,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'current_page' => $page,
        ), 200);
    }
    
    /**
     * Autocomplete endpoint
     */
    public function autocomplete($request) {
        $search = $request->get_param('search');
        $type = $request->get_param('type');
        $limit = $request->get_param('limit');
        
        $results = array();
        
        switch ($type) {
            case 'business':
                $results = $this->autocomplete_businesses($search, $limit);
                break;
            case 'owner':
                $results = $this->autocomplete_owners($search, $limit);
                break;
            case 'category':
                $results = $this->autocomplete_categories($search, $limit);
                break;
        }
        
        return new WP_REST_Response($results, 200);
    }
    
    /**
     * Autocomplete businesses
     */
    private function autocomplete_businesses($search, $limit) {
        $args = array(
            'post_type' => 'p116_business',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            's' => $search,
            'meta_query' => array(
                array(
                    'key' => 'show_in_directory',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'orderby' => 'title',
            'order' => 'ASC',
        );
        
        $query = new WP_Query($args);
        $results = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = get_post();
                $results[] = array(
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'url' => get_permalink($post->ID),
                    'city' => get_post_meta($post->ID, 'city', true),
                );
            }
        }
        wp_reset_postdata();
        
        return $results;
    }
    
    /**
     * Autocomplete owners
     */
    private function autocomplete_owners($search, $limit) {
        global $wpdb;
        
        $search_lower = strtolower($search);
        
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT DISTINCT meta_value as owner_name
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'owners_search'
            AND LOWER(meta_value) LIKE %s
            AND post_id IN (
                SELECT ID FROM {$wpdb->posts}
                WHERE post_type = 'p116_business'
                AND post_status = 'publish'
                AND ID IN (
                    SELECT post_id FROM {$wpdb->postmeta}
                    WHERE meta_key = 'show_in_directory'
                    AND meta_value = '1'
                )
            )
            LIMIT %d
        ", '%' . $wpdb->esc_like($search_lower) . '%', $limit));
        
        $formatted_results = array();
        foreach ($results as $result) {
            $owners = explode(' ', $result->owner_name);
            foreach ($owners as $owner) {
                if (stripos($owner, $search_lower) !== false) {
                    $formatted_results[] = array(
                        'name' => $owner,
                        'type' => 'owner'
                    );
                }
            }
        }
        
        return array_slice($formatted_results, 0, $limit);
    }
    
    /**
     * Autocomplete categories
     */
    private function autocomplete_categories($search, $limit) {
        $terms = get_terms(array(
            'taxonomy' => 'p116_business_category',
            'name__like' => $search,
            'number' => $limit,
            'hide_empty' => true,
        ));
        
        $results = array();
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $results[] = array(
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'type' => 'category'
                );
            }
        }
        
        return $results;
    }
    
    /**
     * Format business data for API response
     */
    private function format_business_data($post) {
        $owners = get_post_meta($post->ID, 'owners', true);
        $categories = get_the_terms($post->ID, 'p116_business_category');
        
        return array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'content' => $post->post_content,
            'url' => get_permalink($post->ID),
            'logo' => get_the_post_thumbnail_url($post->ID, 'medium'),
            'owners' => is_array($owners) ? $owners : array(),
            'categories' => $categories && !is_wp_error($categories) ? $categories : array(),
            'contact' => array(
                'phone' => get_post_meta($post->ID, 'business_phone', true),
                'email' => get_post_meta($post->ID, 'business_email', true),
                'website' => get_post_meta($post->ID, 'website_url', true),
            ),
            'address' => array(
                'address1' => get_post_meta($post->ID, 'address1', true),
                'address2' => get_post_meta($post->ID, 'address2', true),
                'city' => get_post_meta($post->ID, 'city', true),
                'state' => get_post_meta($post->ID, 'state', true),
                'postal_code' => get_post_meta($post->ID, 'postal_code', true),
            ),
            'ownership' => array(
                'veteran_owned' => get_post_meta($post->ID, 'veteran_owned', true) === '1',
                'sons_owned' => get_post_meta($post->ID, 'sons_owned', true) === '1',
                'auxiliary_owned' => get_post_meta($post->ID, 'auxiliary_owned', true) === '1',
            ),
            'links' => get_post_meta($post->ID, 'links', true) ?: array(),
            'services' => get_post_meta($post->ID, 'services_offered', true),
        );
    }
}