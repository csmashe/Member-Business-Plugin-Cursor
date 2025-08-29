<?php
/**
 * Schema Class for JSON-LD
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Schema {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Schema is handled in the template loader class
    }
    
    /**
     * Generate LocalBusiness schema for a business
     */
    public function generate_business_schema($post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'p116_business') {
            return null;
        }
        
        $owners = get_post_meta($post_id, 'owners', true);
        $business_phone = get_post_meta($post_id, 'business_phone', true);
        $business_email = get_post_meta($post_id, 'business_email', true);
        $website_url = get_post_meta($post_id, 'website_url', true);
        $address1 = get_post_meta($post_id, 'address1', true);
        $address2 = get_post_meta($post_id, 'address2', true);
        $city = get_post_meta($post_id, 'city', true);
        $state = get_post_meta($post_id, 'state', true);
        $postal_code = get_post_meta($post_id, 'postal_code', true);
        $links = get_post_meta($post_id, 'links', true);
        $services = get_post_meta($post_id, 'services_offered', true);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => get_the_title($post_id),
            'description' => $post->post_excerpt ?: wp_trim_words($post->post_content, 30),
            'url' => get_permalink($post_id),
        );
        
        // Add image
        if (has_post_thumbnail($post_id)) {
            $schema['image'] = get_the_post_thumbnail_url($post_id, 'large');
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
        
        // Add services
        if ($services) {
            $services_list = explode("\n", $services);
            $clean_services = array();
            foreach ($services_list as $service) {
                $service = trim($service);
                if ($service) {
                    $clean_services[] = $service;
                }
            }
            if (!empty($clean_services)) {
                $schema['makesOffer'] = array();
                foreach ($clean_services as $service) {
                    $schema['makesOffer'][] = array(
                        '@type' => 'Offer',
                        'itemOffered' => array(
                            '@type' => 'Service',
                            'name' => $service
                        )
                    );
                }
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
        
        // Add business hours if available (placeholder for future enhancement)
        $schema['openingHours'] = 'Mo-Fr 09:00-17:00'; // Default placeholder
        
        return $schema;
    }
    
    /**
     * Output schema JSON-LD
     */
    public function output_schema($post_id) {
        $schema = $this->generate_business_schema($post_id);
        if ($schema) {
            echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
        }
    }
}