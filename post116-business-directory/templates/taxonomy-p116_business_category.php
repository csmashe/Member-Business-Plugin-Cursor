<?php
/**
 * Business Category Archive Template
 */

get_header(); ?>

<div class="post116-category-archive">
    <div class="container">
        <header class="post116-category-header">
            <h1 class="post116-category-title">
                <?php single_term_title(); ?>
            </h1>
            
            <?php if (term_description()) : ?>
                <div class="post116-category-description">
                    <?php echo term_description(); ?>
                </div>
            <?php endif; ?>
            
            <!-- Legal Disclaimer -->
            <div class="post116-disclaimer">
                <p><strong><?php _e('Disclaimer:', 'post116-business-directory'); ?></strong> 
                <?php _e('American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance.', 'post116-business-directory'); ?></p>
            </div>
        </header>
        
        <?php if (have_posts()) : ?>
            <div class="post116-businesses-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <div class="post116-business-card">
                        <div class="post116-business-header">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post116-business-logo">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post116-business-info">
                                <h2 class="post116-business-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                
                                <?php
                                $owners = get_post_meta(get_the_ID(), 'owners', true);
                                if (is_array($owners) && !empty($owners)) :
                                ?>
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
                                
                                <?php
                                $city = get_post_meta(get_the_ID(), 'city', true);
                                if ($city) :
                                ?>
                                    <div class="post116-business-location">
                                        <strong><?php _e('Location:', 'post116-business-directory'); ?></strong> <?php echo esc_html($city); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php
                                $phone = get_post_meta(get_the_ID(), 'business_phone', true);
                                if ($phone) :
                                ?>
                                    <div class="post116-business-phone">
                                        <strong><?php _e('Phone:', 'post116-business-directory'); ?></strong> 
                                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php
                                $veteran_owned = get_post_meta(get_the_ID(), 'veteran_owned', true) === '1';
                                $sons_owned = get_post_meta(get_the_ID(), 'sons_owned', true) === '1';
                                $auxiliary_owned = get_post_meta(get_the_ID(), 'auxiliary_owned', true) === '1';
                                
                                if ($veteran_owned || $sons_owned || $auxiliary_owned) :
                                ?>
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
                            <?php if (get_the_excerpt()) : ?>
                                <div class="post116-business-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php
                            $services = get_post_meta(get_the_ID(), 'services_offered', true);
                            if ($services) :
                            ?>
                                <div class="post116-business-services">
                                    <strong><?php _e('Services:', 'post116-business-directory'); ?></strong>
                                    <p><?php echo esc_html(wp_trim_words($services, 15)); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="post116-business-footer">
                            <a href="<?php the_permalink(); ?>" class="post116-view-details">
                                <?php _e('View Details', 'post116-business-directory'); ?>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php
            the_posts_pagination(array(
                'prev_text' => __('Previous', 'post116-business-directory'),
                'next_text' => __('Next', 'post116-business-directory'),
            ));
            ?>
            
        <?php else : ?>
            <div class="post116-no-results">
                <p><?php _e('No businesses found in this category.', 'post116-business-directory'); ?></p>
                <p><a href="<?php echo get_permalink(get_option('post116_bd_directory_page_id')); ?>">
                    <?php _e('View all businesses', 'post116-business-directory'); ?>
                </a></p>
            </div>
        <?php endif; ?>
        
        <!-- Back to Directory -->
        <div class="post116-back-to-directory">
            <a href="<?php echo get_permalink(get_option('post116_bd_directory_page_id')); ?>" class="post116-back-link">
                ← <?php _e('Back to Business Directory', 'post116-business-directory'); ?>
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>