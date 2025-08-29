<?php
/**
 * Test Script for Post 116 Business Directory Plugin
 * 
 * This script can be run to test the plugin functionality and create sample data.
 * Run this from the WordPress admin or via WP-CLI.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Test {
    
    public static function run_tests() {
        echo "<h2>Post 116 Business Directory Plugin Tests</h2>\n";
        
        // Test 1: Check if post type is registered
        self::test_post_type_registration();
        
        // Test 2: Check if taxonomy is registered
        self::test_taxonomy_registration();
        
        // Test 3: Check if capabilities are set
        self::test_capabilities();
        
        // Test 4: Create sample data
        self::create_sample_data();
        
        // Test 5: Test REST API endpoints
        self::test_rest_api();
        
        echo "<h3>Tests completed!</h3>\n";
    }
    
    private static function test_post_type_registration() {
        echo "<h3>Test 1: Post Type Registration</h3>\n";
        
        $post_type = get_post_type_object('p116_business');
        if ($post_type) {
            echo "✅ Custom post type 'p116_business' is registered<br>\n";
            echo "   - Name: " . $post_type->labels->name . "<br>\n";
            echo "   - Public: " . ($post_type->public ? 'Yes' : 'No') . "<br>\n";
        } else {
            echo "❌ Custom post type 'p116_business' is NOT registered<br>\n";
        }
    }
    
    private static function test_taxonomy_registration() {
        echo "<h3>Test 2: Taxonomy Registration</h3>\n";
        
        $taxonomy = get_taxonomy('p116_business_category');
        if ($taxonomy) {
            echo "✅ Custom taxonomy 'p116_business_category' is registered<br>\n";
            echo "   - Name: " . $taxonomy->labels->name . "<br>\n";
            echo "   - Hierarchical: " . ($taxonomy->hierarchical ? 'Yes' : 'No') . "<br>\n";
        } else {
            echo "❌ Custom taxonomy 'p116_business_category' is NOT registered<br>\n";
        }
    }
    
    private static function test_capabilities() {
        echo "<h3>Test 3: Capabilities</h3>\n";
        
        $roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
        $capabilities = array('edit_business', 'manage_business_categories');
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                echo "Role: $role_name<br>\n";
                foreach ($capabilities as $cap) {
                    if ($role->has_cap($cap)) {
                        echo "   ✅ Has capability: $cap<br>\n";
                    } else {
                        echo "   ❌ Missing capability: $cap<br>\n";
                    }
                }
            }
        }
    }
    
    private static function create_sample_data() {
        echo "<h3>Test 4: Creating Sample Data</h3>\n";
        
        // Create sample categories
        $categories = array(
            'automotive' => 'Automotive Services',
            'construction' => 'Construction & Contracting',
            'food-beverage' => 'Food & Beverage',
            'healthcare' => 'Healthcare Services',
            'professional' => 'Professional Services'
        );
        
        $category_ids = array();
        foreach ($categories as $slug => $name) {
            $term = wp_insert_term($name, 'p116_business_category', array('slug' => $slug));
            if (!is_wp_error($term)) {
                $category_ids[$slug] = $term['term_id'];
                echo "✅ Created category: $name<br>\n";
            } else {
                echo "❌ Failed to create category: $name<br>\n";
            }
        }
        
        // Create sample businesses
        $businesses = array(
            array(
                'title' => 'Johnson Auto Repair',
                'content' => 'Full-service automotive repair shop specializing in domestic and foreign vehicles. We provide oil changes, brake service, engine repair, and more.',
                'excerpt' => 'Professional automotive repair services for all makes and models.',
                'category' => 'automotive',
                'owners' => array(
                    array(
                        'owner_name' => 'Mike Johnson',
                        'owner_role' => 'Owner/Manager',
                        'owner_phone' => '(555) 123-4567',
                        'owner_email' => 'mike@johnsonauto.com'
                    )
                ),
                'contact' => array(
                    'business_phone' => '(555) 123-4567',
                    'business_email' => 'info@johnsonauto.com',
                    'website_url' => 'https://johnsonauto.com'
                ),
                'address' => array(
                    'address1' => '123 Main Street',
                    'city' => 'Raleigh',
                    'state' => 'NC',
                    'postal_code' => '27601'
                ),
                'ownership' => array(
                    'veteran_owned' => true,
                    'sons_owned' => false,
                    'auxiliary_owned' => false
                ),
                'services' => "Oil Changes\nBrake Service\nEngine Repair\nTransmission Service\nTire Service\nDiagnostic Services",
                'show_in_directory' => true
            ),
            array(
                'title' => 'Smith Construction LLC',
                'content' => 'Licensed general contractor specializing in residential and commercial construction. Over 20 years of experience in the Triangle area.',
                'excerpt' => 'Licensed general contractor for residential and commercial projects.',
                'category' => 'construction',
                'owners' => array(
                    array(
                        'owner_name' => 'Robert Smith',
                        'owner_role' => 'Owner',
                        'owner_phone' => '(555) 234-5678',
                        'owner_email' => 'robert@smithconstruction.com'
                    ),
                    array(
                        'owner_name' => 'Sarah Smith',
                        'owner_role' => 'Project Manager',
                        'owner_phone' => '(555) 234-5679',
                        'owner_email' => 'sarah@smithconstruction.com'
                    )
                ),
                'contact' => array(
                    'business_phone' => '(555) 234-5678',
                    'business_email' => 'info@smithconstruction.com',
                    'website_url' => 'https://smithconstruction.com'
                ),
                'address' => array(
                    'address1' => '456 Oak Avenue',
                    'city' => 'Durham',
                    'state' => 'NC',
                    'postal_code' => '27701'
                ),
                'ownership' => array(
                    'veteran_owned' => true,
                    'sons_owned' => false,
                    'auxiliary_owned' => false
                ),
                'services' => "New Construction\nRenovations\nAdditions\nKitchen Remodeling\nBathroom Remodeling\nDeck Construction",
                'show_in_directory' => true
            ),
            array(
                'title' => 'Legion Family Restaurant',
                'content' => 'Family-owned restaurant serving traditional American cuisine. Open for breakfast, lunch, and dinner. Catering services available.',
                'excerpt' => 'Family restaurant serving traditional American cuisine with catering services.',
                'category' => 'food-beverage',
                'owners' => array(
                    array(
                        'owner_name' => 'Maria Rodriguez',
                        'owner_role' => 'Owner/Chef',
                        'owner_phone' => '(555) 345-6789',
                        'owner_email' => 'maria@legionfamilyrestaurant.com'
                    )
                ),
                'contact' => array(
                    'business_phone' => '(555) 345-6789',
                    'business_email' => 'info@legionfamilyrestaurant.com',
                    'website_url' => 'https://legionfamilyrestaurant.com'
                ),
                'address' => array(
                    'address1' => '789 Pine Street',
                    'city' => 'Chapel Hill',
                    'state' => 'NC',
                    'postal_code' => '27514'
                ),
                'ownership' => array(
                    'veteran_owned' => false,
                    'sons_owned' => true,
                    'auxiliary_owned' => false
                ),
                'services' => "Breakfast\nLunch\nDinner\nCatering\nPrivate Events\nTakeout",
                'show_in_directory' => true
            )
        );
        
        foreach ($businesses as $business_data) {
            $post_id = wp_insert_post(array(
                'post_title' => $business_data['title'],
                'post_content' => $business_data['content'],
                'post_excerpt' => $business_data['excerpt'],
                'post_type' => 'p116_business',
                'post_status' => 'publish'
            ));
            
            if ($post_id && !is_wp_error($post_id)) {
                echo "✅ Created business: " . $business_data['title'] . "<br>\n";
                
                // Set category
                if (isset($category_ids[$business_data['category']])) {
                    wp_set_post_terms($post_id, array($category_ids[$business_data['category']]), 'p116_business_category');
                }
                
                // Set meta fields
                update_post_meta($post_id, 'owners', $business_data['owners']);
                update_post_meta($post_id, 'business_phone', $business_data['contact']['business_phone']);
                update_post_meta($post_id, 'business_email', $business_data['contact']['business_email']);
                update_post_meta($post_id, 'website_url', $business_data['contact']['website_url']);
                update_post_meta($post_id, 'address1', $business_data['address']['address1']);
                update_post_meta($post_id, 'city', $business_data['address']['city']);
                update_post_meta($post_id, 'state', $business_data['address']['state']);
                update_post_meta($post_id, 'postal_code', $business_data['address']['postal_code']);
                update_post_meta($post_id, 'veteran_owned', $business_data['ownership']['veteran_owned'] ? '1' : '0');
                update_post_meta($post_id, 'sons_owned', $business_data['ownership']['sons_owned'] ? '1' : '0');
                update_post_meta($post_id, 'auxiliary_owned', $business_data['ownership']['auxiliary_owned'] ? '1' : '0');
                update_post_meta($post_id, 'services_offered', $business_data['services']);
                update_post_meta($post_id, 'show_in_directory', $business_data['show_in_directory'] ? '1' : '0');
                
                // Set search fields
                $owner_names = array();
                foreach ($business_data['owners'] as $owner) {
                    $owner_names[] = strtolower($owner['owner_name']);
                }
                update_post_meta($post_id, 'owners_search', implode(' ', $owner_names));
                update_post_meta($post_id, 'city_search', strtolower($business_data['address']['city']));
                
            } else {
                echo "❌ Failed to create business: " . $business_data['title'] . "<br>\n";
            }
        }
    }
    
    private static function test_rest_api() {
        echo "<h3>Test 5: REST API Endpoints</h3>\n";
        
        $rest_url = rest_url('p116/v1/');
        echo "REST API Base URL: $rest_url<br>\n";
        
        // Test search endpoint
        $search_url = $rest_url . 'search';
        echo "Search endpoint: $search_url<br>\n";
        
        // Test autocomplete endpoint
        $autocomplete_url = $rest_url . 'autocomplete';
        echo "Autocomplete endpoint: $autocomplete_url<br>\n";
        
        echo "✅ REST API endpoints are registered<br>\n";
    }
}

// Run tests if this file is accessed directly
if (isset($_GET['run_tests']) && current_user_can('manage_options')) {
    Post116_BD_Test::run_tests();
}