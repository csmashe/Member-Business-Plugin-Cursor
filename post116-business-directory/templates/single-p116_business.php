<?php
/**
 * Single Business Template
 */

get_header(); ?>

<div class="post116-single-business">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post116-business-article'); ?>>
                
                <!-- Legal Disclaimer -->
                <div class="post116-disclaimer">
                    <p><strong><?php _e('Disclaimer:', 'post116-business-directory'); ?></strong> 
                    <?php _e('American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance.', 'post116-business-directory'); ?></p>
                </div>
                
                <header class="post116-business-header">
                    <div class="post116-business-title-section">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post116-business-logo">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post116-business-title-info">
                            <h1 class="post116-business-title"><?php the_title(); ?></h1>
                            
                            <?php
                            $categories = get_the_terms(get_the_ID(), 'p116_business_category');
                            if ($categories && !is_wp_error($categories)) :
                            ?>
                                <div class="post116-business-categories">
                                    <?php foreach ($categories as $category) : ?>
                                        <span class="post116-category-tag">
                                            <a href="<?php echo get_term_link($category); ?>">
                                                <?php echo esc_html($category->name); ?>
                                            </a>
                                        </span>
                                    <?php endforeach; ?>
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
                </header>
                
                <div class="post116-business-content">
                    <div class="post116-business-main">
                        
                        <!-- Business Description -->
                        <?php if (get_the_content()) : ?>
                            <section class="post116-business-description">
                                <h2><?php _e('About This Business', 'post116-business-directory'); ?></h2>
                                <div class="post116-business-content-text">
                                    <?php the_content(); ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Services Offered -->
                        <?php
                        $services = get_post_meta(get_the_ID(), 'services_offered', true);
                        if ($services) :
                        ?>
                            <section class="post116-business-services">
                                <h2><?php _e('Services Offered', 'post116-business-directory'); ?></h2>
                                <div class="post116-services-list">
                                    <?php
                                    $services_list = explode("\n", $services);
                                    echo '<ul>';
                                    foreach ($services_list as $service) {
                                        $service = trim($service);
                                        if ($service) {
                                            echo '<li>' . esc_html($service) . '</li>';
                                        }
                                    }
                                    echo '</ul>';
                                    ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                    </div>
                    
                    <aside class="post116-business-sidebar">
                        
                        <!-- Business Owners -->
                        <?php
                        $owners = get_post_meta(get_the_ID(), 'owners', true);
                        if (is_array($owners) && !empty($owners)) :
                        ?>
                            <section class="post116-business-owners">
                                <h3><?php _e('Business Owners', 'post116-business-directory'); ?></h3>
                                <div class="post116-owners-list">
                                    <?php foreach ($owners as $owner) : ?>
                                        <?php if (!empty($owner['owner_name'])) : ?>
                                            <div class="post116-owner">
                                                <h4><?php echo esc_html($owner['owner_name']); ?></h4>
                                                <?php if (!empty($owner['owner_role'])) : ?>
                                                    <p class="post116-owner-role"><?php echo esc_html($owner['owner_role']); ?></p>
                                                <?php endif; ?>
                                                
                                                <div class="post116-owner-contact">
                                                    <?php if (!empty($owner['owner_email'])) : ?>
                                                        <p><strong><?php _e('Email:', 'post116-business-directory'); ?></strong> 
                                                        <a href="mailto:<?php echo esc_attr($owner['owner_email']); ?>">
                                                            <?php echo esc_html($owner['owner_email']); ?>
                                                        </a></p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($owner['owner_phone'])) : ?>
                                                        <p><strong><?php _e('Phone:', 'post116-business-directory'); ?></strong> 
                                                        <a href="tel:<?php echo esc_attr($owner['owner_phone']); ?>">
                                                            <?php echo esc_html($owner['owner_phone']); ?>
                                                        </a></p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($owner['owner_website'])) : ?>
                                                        <p><strong><?php _e('Website:', 'post116-business-directory'); ?></strong> 
                                                        <a href="<?php echo esc_url($owner['owner_website']); ?>" target="_blank" rel="noopener">
                                                            <?php echo esc_html($owner['owner_website']); ?>
                                                        </a></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Contact Information -->
                        <section class="post116-business-contact">
                            <h3><?php _e('Contact Information', 'post116-business-directory'); ?></h3>
                            <div class="post116-contact-details">
                                <?php
                                $business_phone = get_post_meta(get_the_ID(), 'business_phone', true);
                                $business_email = get_post_meta(get_the_ID(), 'business_email', true);
                                $website_url = get_post_meta(get_the_ID(), 'website_url', true);
                                
                                if ($business_phone) :
                                ?>
                                    <p><strong><?php _e('Phone:', 'post116-business-directory'); ?></strong> 
                                    <a href="tel:<?php echo esc_attr($business_phone); ?>">
                                        <?php echo esc_html($business_phone); ?>
                                    </a></p>
                                <?php endif; ?>
                                
                                <?php if ($business_email) : ?>
                                    <p><strong><?php _e('Email:', 'post116-business-directory'); ?></strong> 
                                    <a href="mailto:<?php echo esc_attr($business_email); ?>">
                                        <?php echo esc_html($business_email); ?>
                                    </a></p>
                                <?php endif; ?>
                                
                                <?php if ($website_url) : ?>
                                    <p><strong><?php _e('Website:', 'post116-business-directory'); ?></strong> 
                                    <a href="<?php echo esc_url($website_url); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html($website_url); ?>
                                    </a></p>
                                <?php endif; ?>
                            </div>
                        </section>
                        
                        <!-- Address -->
                        <?php
                        $address1 = get_post_meta(get_the_ID(), 'address1', true);
                        $address2 = get_post_meta(get_the_ID(), 'address2', true);
                        $city = get_post_meta(get_the_ID(), 'city', true);
                        $state = get_post_meta(get_the_ID(), 'state', true);
                        $postal_code = get_post_meta(get_the_ID(), 'postal_code', true);
                        
                        if ($city) :
                        ?>
                            <section class="post116-business-address">
                                <h3><?php _e('Location', 'post116-business-directory'); ?></h3>
                                <div class="post116-address-details">
                                    <address>
                                        <?php if ($address1) : ?>
                                            <?php echo esc_html($address1); ?><br>
                                        <?php endif; ?>
                                        <?php if ($address2) : ?>
                                            <?php echo esc_html($address2); ?><br>
                                        <?php endif; ?>
                                        <?php echo esc_html($city); ?>
                                        <?php if ($state) : ?>
                                            , <?php echo esc_html($state); ?>
                                        <?php endif; ?>
                                        <?php if ($postal_code) : ?>
                                            <?php echo esc_html($postal_code); ?>
                                        <?php endif; ?>
                                    </address>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Additional Links -->
                        <?php
                        $links = get_post_meta(get_the_ID(), 'links', true);
                        if (is_array($links) && !empty($links)) :
                        ?>
                            <section class="post116-business-links">
                                <h3><?php _e('Additional Links', 'post116-business-directory'); ?></h3>
                                <div class="post116-links-list">
                                    <?php foreach ($links as $link) : ?>
                                        <?php if (!empty($link['link_label']) && !empty($link['link_url'])) : ?>
                                            <p>
                                                <a href="<?php echo esc_url($link['link_url']); ?>" target="_blank" rel="noopener">
                                                    <?php echo esc_html($link['link_label']); ?>
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                    </aside>
                </div>
                
                <!-- Back to Directory -->
                <div class="post116-back-to-directory">
                    <a href="<?php echo get_permalink(get_option('post116_bd_directory_page_id')); ?>" class="post116-back-link">
                        ← <?php _e('Back to Business Directory', 'post116-business-directory'); ?>
                    </a>
                </div>
                
            </article>
        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>