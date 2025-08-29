<?php
/**
 * Template Loader Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Template_Loader {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('template_include', array($this, 'template_loader'));
        add_action('wp_head', array($this, 'add_json_ld_schema'));
    }
    
    /**
     * Template loader
     */
    public function template_loader($template) {
        if (is_singular('p116_business')) {
            $custom_template = $this->locate_template('single-p116_business.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        if (is_tax('p116_business_category')) {
            $custom_template = $this->locate_template('taxonomy-p116_business_category.php');
            if ($custom_template) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Locate template file
     */
    private function locate_template($template_name) {
        // Check theme directory first
        $theme_template = locate_template(array(
            'post116-business-directory/' . $template_name,
            $template_name
        ));
        
        if ($theme_template) {
            return $theme_template;
        }
        
        // Check plugin templates directory
        $plugin_template = POST116_BD_PLUGIN_DIR . 'templates/' . $template_name;
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
        
        return false;
    }
    
    /**
     * Add JSON-LD schema to single business pages
     */
    public function add_json_ld_schema() {
        if (!is_singular('p116_business')) {
            return;
        }
        
        $post = get_post();
        $owners = get_post_meta($post->ID, 'owners', true);
        $business_phone = get_post_meta($post->ID, 'business_phone', true);
        $business_email = get_post_meta($post->ID, 'business_email', true);
        $website_url = get_post_meta($post->ID, 'website_url', true);
        $address1 = get_post_meta($post->ID, 'address1', true);
        $address2 = get_post_meta($post->ID, 'address2', true);
        $city = get_post_meta($post->ID, 'city', true);
        $state = get_post_meta($post->ID, 'state', true);
        $postal_code = get_post_meta($post->ID, 'postal_code', true);
        $links = get_post_meta($post->ID, 'links', true);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => get_the_title($post->ID),
            'description' => $post->post_excerpt ?: wp_trim_words($post->post_content, 30),
            'url' => get_permalink($post->ID),
        );
        
        // Add image
        if (has_post_thumbnail($post->ID)) {
            $schema['image'] = get_the_post_thumbnail_url($post->ID, 'large');
        }
        
        // Add contact information
        if ($business_phone) {
            $schema['telephone'] = $business_phone;
        }
        
        if ($business_email) {
            $schema['email'] = $business_email;
        }
        
        if ($website_url) {
            $schema['url'] = $website_url;
        }
        
        // Add address
        if ($city) {
            $address = array(
                '@type' => 'PostalAddress',
                'addressLocality' => $city,
            );
            
            if ($address1) {
                $address['streetAddress'] = $address1;
                if ($address2) {
                    $address['streetAddress'] .= ', ' . $address2;
                }
            }
            
            if ($state) {
                $address['addressRegion'] = $state;
            }
            
            if ($postal_code) {
                $address['postalCode'] = $postal_code;
            }
            
            $schema['address'] = $address;
        }
        
        // Add sameAs links
        if (is_array($links) && !empty($links)) {
            $same_as = array();
            foreach ($links as $link) {
                if (!empty($link['link_url'])) {
                    $same_as[] = $link['link_url'];
                }
            }
            if (!empty($same_as)) {
                $schema['sameAs'] = $same_as;
            }
        }
        
        // Add owner information
        if (is_array($owners) && !empty($owners)) {
            $founders = array();
            foreach ($owners as $owner) {
                if (!empty($owner['owner_name'])) {
                    $founder = array(
                        '@type' => 'Person',
                        'name' => $owner['owner_name'],
                    );
                    
                    if (!empty($owner['owner_role'])) {
                        $founder['jobTitle'] = $owner['owner_role'];
                    }
                    
                    if (!empty($owner['owner_email'])) {
                        $founder['email'] = $owner['owner_email'];
                    }
                    
                    if (!empty($owner['owner_phone'])) {
                        $founder['telephone'] = $owner['owner_phone'];
                    }
                    
                    $founders[] = $founder;
                }
            }
            
            if (!empty($founders)) {
                $schema['founder'] = count($founders) === 1 ? $founders[0] : $founders;
            }
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}