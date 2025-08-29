<?php
/**
 * Meta Fields Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Meta_Fields {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_fields'));
        add_action('wp_ajax_post116_bd_reorder_owners', array($this, 'ajax_reorder_owners'));
        add_action('wp_ajax_post116_bd_reorder_links', array($this, 'ajax_reorder_links'));
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'post116_bd_owners',
            __('Business Owners', 'post116-business-directory'),
            array($this, 'owners_meta_box'),
            'p116_business',
            'normal',
            'high'
        );
        
        add_meta_box(
            'post116_bd_contact',
            __('Contact Information', 'post116-business-directory'),
            array($this, 'contact_meta_box'),
            'p116_business',
            'normal',
            'high'
        );
        
        add_meta_box(
            'post116_bd_address',
            __('Address', 'post116-business-directory'),
            array($this, 'address_meta_box'),
            'p116_business',
            'normal',
            'high'
        );
        
        add_meta_box(
            'post116_bd_ownership',
            __('Ownership Flags', 'post116-business-directory'),
            array($this, 'ownership_meta_box'),
            'p116_business',
            'side',
            'high'
        );
        
        add_meta_box(
            'post116_bd_links',
            __('Additional Links', 'post116-business-directory'),
            array($this, 'links_meta_box'),
            'p116_business',
            'normal',
            'default'
        );
        
        add_meta_box(
            'post116_bd_services',
            __('Services Offered', 'post116-business-directory'),
            array($this, 'services_meta_box'),
            'p116_business',
            'normal',
            'default'
        );
        
        add_meta_box(
            'post116_bd_display',
            __('Display Options', 'post116-business-directory'),
            array($this, 'display_meta_box'),
            'p116_business',
            'side',
            'default'
        );
    }
    
    /**
     * Owners meta box
     */
    public function owners_meta_box($post) {
        wp_nonce_field('post116_bd_meta_nonce', 'post116_bd_meta_nonce');
        
        $owners = get_post_meta($post->ID, 'owners', true);
        if (!is_array($owners)) {
            $owners = array();
        }
        
        echo '<div id="post116-owners-container">';
        echo '<p class="description">' . __('Add business owners with their contact information.', 'post116-business-directory') . '</p>';
        
        if (empty($owners)) {
            $owners = array(array(
                'owner_name' => '',
                'owner_role' => '',
                'owner_email' => '',
                'owner_phone' => '',
                'owner_website' => ''
            ));
        }
        
        foreach ($owners as $index => $owner) {
            $this->render_owner_fields($index, $owner);
        }
        
        echo '<button type="button" class="button" id="add-owner">' . __('Add Owner', 'post116-business-directory') . '</button>';
        echo '</div>';
    }
    
    /**
     * Render owner fields
     */
    private function render_owner_fields($index, $owner = array()) {
        $owner = wp_parse_args($owner, array(
            'owner_name' => '',
            'owner_role' => '',
            'owner_email' => '',
            'owner_phone' => '',
            'owner_website' => ''
        ));
        
        echo '<div class="post116-owner-group" data-index="' . esc_attr($index) . '">';
        echo '<div class="owner-header">';
        echo '<h4>' . sprintf(__('Owner %d', 'post116-business-directory'), $index + 1) . '</h4>';
        echo '<button type="button" class="button-link remove-owner">' . __('Remove', 'post116-business-directory') . '</button>';
        echo '</div>';
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="owner_name_' . $index . '">' . __('Name', 'post116-business-directory') . ' *</label></th>';
        echo '<td><input type="text" id="owner_name_' . $index . '" name="owners[' . $index . '][owner_name]" value="' . esc_attr($owner['owner_name']) . '" class="regular-text" required /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_role_' . $index . '">' . __('Role', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="owner_role_' . $index . '" name="owners[' . $index . '][owner_role]" value="' . esc_attr($owner['owner_role']) . '" class="regular-text" placeholder="' . __('e.g., Owner, Manager, Partner', 'post116-business-directory') . '" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_email_' . $index . '">' . __('Email', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="email" id="owner_email_' . $index . '" name="owners[' . $index . '][owner_email]" value="' . esc_attr($owner['owner_email']) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_phone_' . $index . '">' . __('Phone', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="tel" id="owner_phone_' . $index . '" name="owners[' . $index . '][owner_phone]" value="' . esc_attr($owner['owner_phone']) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="owner_website_' . $index . '">' . __('Website', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="url" id="owner_website_' . $index . '" name="owners[' . $index . '][owner_website]" value="' . esc_attr($owner['owner_website']) . '" class="regular-text" placeholder="https://" /></td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
    }
    
    /**
     * Contact meta box
     */
    public function contact_meta_box($post) {
        $business_phone = get_post_meta($post->ID, 'business_phone', true);
        $business_email = get_post_meta($post->ID, 'business_email', true);
        $website_url = get_post_meta($post->ID, 'website_url', true);
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="business_phone">' . __('Business Phone', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="tel" id="business_phone" name="business_phone" value="' . esc_attr($business_phone) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="business_email">' . __('Business Email', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="email" id="business_email" name="business_email" value="' . esc_attr($business_email) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="website_url">' . __('Website URL', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="url" id="website_url" name="website_url" value="' . esc_attr($website_url) . '" class="regular-text" placeholder="https://" /></td>';
        echo '</tr>';
        echo '</table>';
    }
    
    /**
     * Address meta box
     */
    public function address_meta_box($post) {
        $address1 = get_post_meta($post->ID, 'address1', true);
        $address2 = get_post_meta($post->ID, 'address2', true);
        $city = get_post_meta($post->ID, 'city', true);
        $state = get_post_meta($post->ID, 'state', true);
        $postal_code = get_post_meta($post->ID, 'postal_code', true);
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="address1">' . __('Address Line 1', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="address1" name="address1" value="' . esc_attr($address1) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="address2">' . __('Address Line 2', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="address2" name="address2" value="' . esc_attr($address2) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="city">' . __('City', 'post116-business-directory') . ' *</label></th>';
        echo '<td><input type="text" id="city" name="city" value="' . esc_attr($city) . '" class="regular-text" required /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="state">' . __('State', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="state" name="state" value="' . esc_attr($state) . '" class="regular-text" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="postal_code">' . __('Postal Code', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="postal_code" name="postal_code" value="' . esc_attr($postal_code) . '" class="regular-text" /></td>';
        echo '</tr>';
        echo '</table>';
    }
    
    /**
     * Ownership meta box
     */
    public function ownership_meta_box($post) {
        $veteran_owned = get_post_meta($post->ID, 'veteran_owned', true);
        $sons_owned = get_post_meta($post->ID, 'sons_owned', true);
        $auxiliary_owned = get_post_meta($post->ID, 'auxiliary_owned', true);
        
        echo '<p>';
        echo '<label><input type="checkbox" name="veteran_owned" value="1" ' . checked($veteran_owned, '1', false) . ' /> ';
        echo __('Veteran Owned', 'post116-business-directory') . '</label><br>';
        echo '<label><input type="checkbox" name="sons_owned" value="1" ' . checked($sons_owned, '1', false) . ' /> ';
        echo __('Sons of American Legion Owned', 'post116-business-directory') . '</label><br>';
        echo '<label><input type="checkbox" name="auxiliary_owned" value="1" ' . checked($auxiliary_owned, '1', false) . ' /> ';
        echo __('Auxiliary Owned', 'post116-business-directory') . '</label>';
        echo '</p>';
    }
    
    /**
     * Links meta box
     */
    public function links_meta_box($post) {
        $links = get_post_meta($post->ID, 'links', true);
        if (!is_array($links)) {
            $links = array();
        }
        
        echo '<div id="post116-links-container">';
        echo '<p class="description">' . __('Add additional links (social media, directories, etc.)', 'post116-business-directory') . '</p>';
        
        if (empty($links)) {
            $links = array(array(
                'link_label' => '',
                'link_url' => ''
            ));
        }
        
        foreach ($links as $index => $link) {
            $this->render_link_fields($index, $link);
        }
        
        echo '<button type="button" class="button" id="add-link">' . __('Add Link', 'post116-business-directory') . '</button>';
        echo '</div>';
    }
    
    /**
     * Render link fields
     */
    private function render_link_fields($index, $link = array()) {
        $link = wp_parse_args($link, array(
            'link_label' => '',
            'link_url' => ''
        ));
        
        echo '<div class="post116-link-group" data-index="' . esc_attr($index) . '">';
        echo '<div class="link-header">';
        echo '<button type="button" class="button-link remove-link">' . __('Remove', 'post116-business-directory') . '</button>';
        echo '</div>';
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="link_label_' . $index . '">' . __('Label', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="text" id="link_label_' . $index . '" name="links[' . $index . '][link_label]" value="' . esc_attr($link['link_label']) . '" class="regular-text" placeholder="' . __('e.g., Facebook, LinkedIn', 'post116-business-directory') . '" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="link_url_' . $index . '">' . __('URL', 'post116-business-directory') . '</label></th>';
        echo '<td><input type="url" id="link_url_' . $index . '" name="links[' . $index . '][link_url]" value="' . esc_attr($link['link_url']) . '" class="regular-text" placeholder="https://" /></td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
    }
    
    /**
     * Services meta box
     */
    public function services_meta_box($post) {
        $services = get_post_meta($post->ID, 'services_offered', true);
        
        echo '<p class="description">' . __('Brief list of services offered (one per line)', 'post116-business-directory') . '</p>';
        echo '<textarea id="services_offered" name="services_offered" rows="5" cols="50" class="large-text">' . esc_textarea($services) . '</textarea>';
    }
    
    /**
     * Display meta box
     */
    public function display_meta_box($post) {
        $show_in_directory = get_post_meta($post->ID, 'show_in_directory', true);
        if ($show_in_directory === '') {
            $show_in_directory = '1'; // Default to true
        }
        
        echo '<p>';
        echo '<label><input type="checkbox" name="show_in_directory" value="1" ' . checked($show_in_directory, '1', false) . ' /> ';
        echo __('Show in directory', 'post116-business-directory') . '</label>';
        echo '</p>';
    }
    
    /**
     * Save meta fields
     */
    public function save_meta_fields($post_id) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check nonce
        if (!isset($_POST['post116_bd_meta_nonce']) || !wp_verify_nonce($_POST['post116_bd_meta_nonce'], 'post116_bd_meta_nonce')) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_business', $post_id)) {
            return;
        }
        
        // Save owners
        if (isset($_POST['owners']) && is_array($_POST['owners'])) {
            $owners = array();
            $owners_search = array();
            
            foreach ($_POST['owners'] as $owner) {
                if (!empty($owner['owner_name'])) {
                    $owner_data = array(
                        'owner_name' => sanitize_text_field($owner['owner_name']),
                        'owner_role' => sanitize_text_field($owner['owner_role']),
                        'owner_email' => sanitize_email($owner['owner_email']),
                        'owner_phone' => sanitize_text_field($owner['owner_phone']),
                        'owner_website' => esc_url_raw($owner['owner_website'])
                    );
                    $owners[] = $owner_data;
                    $owners_search[] = strtolower($owner_data['owner_name']);
                }
            }
            
            update_post_meta($post_id, 'owners', $owners);
            update_post_meta($post_id, 'owners_search', implode(' ', $owners_search));
        }
        
        // Save contact information
        $contact_fields = array('business_phone', 'business_email', 'website_url');
        foreach ($contact_fields as $field) {
            if (isset($_POST[$field])) {
                $value = '';
                switch ($field) {
                    case 'business_phone':
                        $value = sanitize_text_field($_POST[$field]);
                        break;
                    case 'business_email':
                        $value = sanitize_email($_POST[$field]);
                        break;
                    case 'website_url':
                        $value = esc_url_raw($_POST[$field]);
                        break;
                }
                update_post_meta($post_id, $field, $value);
            }
        }
        
        // Save address
        $address_fields = array('address1', 'address2', 'city', 'state', 'postal_code');
        foreach ($address_fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $field, $value);
                
                // Create city search field
                if ($field === 'city') {
                    update_post_meta($post_id, 'city_search', strtolower($value));
                }
            }
        }
        
        // Save ownership flags
        $ownership_fields = array('veteran_owned', 'sons_owned', 'auxiliary_owned');
        foreach ($ownership_fields as $field) {
            $value = isset($_POST[$field]) ? '1' : '0';
            update_post_meta($post_id, $field, $value);
        }
        
        // Save links
        if (isset($_POST['links']) && is_array($_POST['links'])) {
            $links = array();
            
            foreach ($_POST['links'] as $link) {
                if (!empty($link['link_label']) && !empty($link['link_url'])) {
                    $links[] = array(
                        'link_label' => sanitize_text_field($link['link_label']),
                        'link_url' => esc_url_raw($link['link_url'])
                    );
                }
            }
            
            update_post_meta($post_id, 'links', $links);
        }
        
        // Save services
        if (isset($_POST['services_offered'])) {
            update_post_meta($post_id, 'services_offered', sanitize_textarea_field($_POST['services_offered']));
        }
        
        // Save display options
        $show_in_directory = isset($_POST['show_in_directory']) ? '1' : '0';
        update_post_meta($post_id, 'show_in_directory', $show_in_directory);
    }
}