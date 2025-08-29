<?php
/**
 * Gutenberg Block Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Post116_BD_Gutenberg_Block {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_block'));
        add_shortcode('post116_directory', array($this, 'shortcode_handler'));
    }
    
    /**
     * Register Gutenberg block
     */
    public function register_block() {
        register_block_type('post116/directory', array(
            'attributes' => array(
                'showFlags' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'perPage' => array(
                    'type' => 'number',
                    'default' => 20,
                ),
                'placeholderText' => array(
                    'type' => 'string',
                    'default' => __('Search businesses, owners, or categories...', 'post116-business-directory'),
                ),
            ),
            'render_callback' => array($this, 'render_block'),
        ));
    }
    
    /**
     * Render the directory block
     */
    public function render_block($attributes) {
        $show_flags = $attributes['showFlags'] ?? true;
        $per_page = $attributes['perPage'] ?? 20;
        $placeholder_text = $attributes['placeholderText'] ?? __('Search businesses, owners, or categories...', 'post116-business-directory');
        
        // Get categories for filter dropdown
        $categories = get_terms(array(
            'taxonomy' => 'p116_business_category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ));
        
        // Get initial businesses
        $args = array(
            'post_type' => 'p116_business',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
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
        
        ob_start();
        ?>
        <div class="post116-directory" data-per-page="<?php echo esc_attr($per_page); ?>">
            <!-- Legal Disclaimer -->
            <div class="post116-disclaimer">
                <p><strong><?php _e('Disclaimer:', 'post116-business-directory'); ?></strong> 
                <?php _e('American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance.', 'post116-business-directory'); ?></p>
            </div>
            
            <!-- Search and Filters -->
            <div class="post116-search-filters">
                <div class="post116-search-bar">
                    <input type="text" 
                           id="post116-search-input" 
                           placeholder="<?php echo esc_attr($placeholder_text); ?>" 
                           class="post116-search-input" />
                    <button type="button" id="post116-search-clear" class="post116-search-clear" style="display: none;">
                        <?php _e('Clear', 'post116-business-directory'); ?>
                    </button>
                </div>
                
                <div class="post116-filters">
                    <select id="post116-category-filter" class="post116-filter">
                        <option value=""><?php _e('All Categories', 'post116-business-directory'); ?></option>
                        <?php if ($categories && !is_wp_error($categories)) : ?>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?php echo esc_attr($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    
                    <?php if ($show_flags) : ?>
                        <div class="post116-ownership-filters">
                            <label>
                                <input type="checkbox" id="post116-veteran-filter" class="post116-ownership-filter" value="veteran_owned" />
                                <?php _e('Veteran Owned', 'post116-business-directory'); ?>
                            </label>
                            <label>
                                <input type="checkbox" id="post116-sons-filter" class="post116-ownership-filter" value="sons_owned" />
                                <?php _e('Sons Owned', 'post116-business-directory'); ?>
                            </label>
                            <label>
                                <input type="checkbox" id="post116-auxiliary-filter" class="post116-ownership-filter" value="auxiliary_owned" />
                                <?php _e('Auxiliary Owned', 'post116-business-directory'); ?>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Loading indicator -->
            <div id="post116-loading" class="post116-loading" style="display: none;">
                <?php _e('Loading...', 'post116-business-directory'); ?>
            </div>
            
            <!-- Results -->
            <div id="post116-results" class="post116-results">
                <?php if ($query->have_posts()) : ?>
                    <div class="post116-businesses-grid">
                        <?php while ($query->have_posts()) : $query->the_post(); ?>
                            <?php $this->render_business_card(get_post()); ?>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if ($query->max_num_pages > 1) : ?>
                        <div class="post116-pagination">
                            <button type="button" id="post116-load-more" class="post116-load-more">
                                <?php _e('Load More', 'post116-business-directory'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="post116-no-results">
                        <p><?php _e('No businesses found.', 'post116-business-directory'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Shortcode handler
     */
    public function shortcode_handler($atts) {
        $atts = shortcode_atts(array(
            'show_flags' => 'true',
            'per_page' => '20',
            'placeholder' => __('Search businesses, owners, or categories...', 'post116-business-directory'),
        ), $atts);
        
        $attributes = array(
            'showFlags' => $atts['show_flags'] === 'true',
            'perPage' => intval($atts['per_page']),
            'placeholderText' => $atts['placeholder'],
        );
        
        return $this->render_block($attributes);
    }
    
    /**
     * Render business card
     */
    private function render_business_card($post) {
        $owners = get_post_meta($post->ID, 'owners', true);
        $categories = get_the_terms($post->ID, 'p116_business_category');
        $city = get_post_meta($post->ID, 'city', true);
        $phone = get_post_meta($post->ID, 'business_phone', true);
        $veteran_owned = get_post_meta($post->ID, 'veteran_owned', true) === '1';
        $sons_owned = get_post_meta($post->ID, 'sons_owned', true) === '1';
        $auxiliary_owned = get_post_meta($post->ID, 'auxiliary_owned', true) === '1';
        $services = get_post_meta($post->ID, 'services_offered', true);
        
        ?>
        <div class="post116-business-card" data-business-id="<?php echo $post->ID; ?>">
            <div class="post116-business-header">
                <?php if (has_post_thumbnail($post->ID)) : ?>
                    <div class="post116-business-logo">
                        <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="post116-business-info">
                    <h3 class="post116-business-title">
                        <a href="<?php echo get_permalink($post->ID); ?>">
                            <?php echo get_the_title($post->ID); ?>
                        </a>
                    </h3>
                    
                    <?php if ($categories && !is_wp_error($categories)) : ?>
                        <div class="post116-business-categories">
                            <?php foreach ($categories as $category) : ?>
                                <span class="post116-category-tag"><?php echo esc_html($category->name); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($veteran_owned || $sons_owned || $auxiliary_owned) : ?>
                        <div class="post116-ownership-flags">
                            <?php if ($veteran_owned) : ?>
                                <span class="post116-flag veteran"><?php _e('Veteran Owned', 'post116-business-directory'); ?></span>
                            <?php endif; ?>
                            <?php if ($sons_owned) : ?>
                                <span class="post116-flag sons"><?php _e('Sons Owned', 'post116-business-directory'); ?></span>
                            <?php endif; ?>
                            <?php if ($auxiliary_owned) : ?>
                                <span class="post116-flag auxiliary"><?php _e('Auxiliary Owned', 'post116-business-directory'); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="post116-business-details">
                <?php if (is_array($owners) && !empty($owners)) : ?>
                    <div class="post116-business-owners">
                        <strong><?php _e('Owners:', 'post116-business-directory'); ?></strong>
                        <?php
                        $owner_names = array();
                        foreach ($owners as $owner) {
                            if (!empty($owner['owner_name'])) {
                                $owner_names[] = esc_html($owner['owner_name']);
                            }
                        }
                        echo implode(', ', $owner_names);
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($city) : ?>
                    <div class="post116-business-location">
                        <strong><?php _e('Location:', 'post116-business-directory'); ?></strong> <?php echo esc_html($city); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($phone) : ?>
                    <div class="post116-business-phone">
                        <strong><?php _e('Phone:', 'post116-business-directory'); ?></strong> 
                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                    </div>
                <?php endif; ?>
                
                <?php if ($services) : ?>
                    <div class="post116-business-services">
                        <strong><?php _e('Services:', 'post116-business-directory'); ?></strong>
                        <p><?php echo esc_html(wp_trim_words($services, 15)); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="post116-business-footer">
                <a href="<?php echo get_permalink($post->ID); ?>" class="post116-view-details">
                    <?php _e('View Details', 'post116-business-directory'); ?>
                </a>
            </div>
        </div>
        <?php
    }
}