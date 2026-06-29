<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fetch plugin information from WordPress.org
if ( ! function_exists( 'plugins_api' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
}

$author_slug = 'awordpresslife';
$transient_key = 'ag_our_plugins_data';

// Force refresh the data to apply new categorization rules
if ( isset($_GET['refresh_plugins']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ag_refresh_plugins') ) {
    delete_transient( $transient_key );
}

$plugins = get_transient( $transient_key );

if ( false === $plugins ) {
    $api_args = array(
        'author'   => $author_slug,
        'per_page' => 100, // Fetch all plugins
        'fields'   => array(
            'icons'             => true,
            'banners'           => true,
            'active_installs'   => true,
            'short_description' => true,
            'rating'            => true,
            'num_ratings'       => true,
            'last_updated'      => true,
        ),
    );

    $response = plugins_api( 'query_plugins', $api_args );

    if ( ! is_wp_error( $response ) && isset( $response->plugins ) ) {
        $plugins = $response->plugins;
        // Cache for 12 hours
        set_transient( $transient_key, $plugins, 12 * HOUR_IN_SECONDS );
    } else {
        $plugins = array();
    }
}

// Filter and Categorize
$filtered_plugins = array();

// Define the desired manual position of plugins by slug
$manual_slug_order = array(
    'frank-website-seo-checker-and-audit',
    'frank-responsive-checker',
    'frank-dead-link-checker',
    'right-click-disable-or-ban',
    'frank-schema-markup-generator',
    'ultimate-portfolio',
    'hash-converter',
    'lead-generation-form',
    'online-job-board',
    'new-grid-gallery',
    'new-photo-gallery',
    'new-image-gallery',
    'portfolio-filter-gallery',
    'event-monster'
);

// List of keywords or slugs for "New Releases"
$new_keywords = array( 
    'Website SEO Checker',
    'Responsive Checker',
    'Dead Link Checker',
    'Schema Markup Generator',
    'Right Click Ban',
    'Ultimate Portfolio',
    'Universal Unit Converter',
    'Job Board',
    'Lead Generation Form'
);

// Fallback Slugs
$new_slugs = array(
    'frank-website-seo-checker-and-audit',
    'frank-responsive-checker',
    'dead-link-checker',
    'schema-markup-generator',
    'right-click-ban',
    'ultimate-portfolio',
    'universal-unit-converter',
    'lead-generation-form',
    'job-board-manager'
);

$technical_slugs = array( 
    'wp-life-companion', 
    'shortcode-generator', 
    'dead-link-checker', 
    'schema-markup-generator', 
    'right-click-ban',
    'frank-responsive-checker',
    'frank-website-seo-checker-and-audit'
);

// Technical Keywords
$technical_keywords = array(
    'Website SEO Checker',
    'Right Click Ban', 
    'Dead Link Checker', 
    'Schema Markup Generator', 
    'Shortcode Generator', 
    'Login Page Customizer',
    'Responsive Checker',
);

// Social Media Keywords
$social_keywords = array( 'social', 'share', 'facebook', 'instagram', 'twitter', 'tiktok', 'feed', 'icon', 'whatsapp', 'messenger', 'flickr', 'youtube', 'vimeo' );

// Marketing & Growth Keywords
$marketing_keywords = array( 'Event Monster', 'Testimonial', 'Pricing Table', 'Team Member', 'Coming Soon', 'Maintenance', 'Contact Form', 'Lead Generation' );

// Social Media Manual Names
$social_manual_names = array(
    'Animated Live Wall',
    'Album Gallery For Flickr',
    'Album Photostream Flickr Gallery',
    'Video Gallery YouTube Vimeo'
);

foreach ( $plugins as $plugin ) {
    $plugin = (array) $plugin;
    $slug = isset($plugin['slug']) ? $plugin['slug'] : '';
    $name = isset($plugin['name']) ? $plugin['name'] : '';
    
    // 1. Remove Companion plugins
    if ( stripos( $slug, 'companion' ) !== false ) {
        continue;
    }

    // 2. Assign Categories
    $categories = array( 'all' );
    
    // Popular if > 5,000 installs
    if ( isset($plugin['active_installs']) && (int) $plugin['active_installs'] >= 5000 ) {
        $categories[] = 'popular';
    }
    
    // New if slug matches or name contains keywords
    $is_new = false;
    if ( in_array( $slug, $new_slugs ) ) {
        $is_new = true;
    } else {
        foreach ( $new_keywords as $keyword ) {
            if ( stripos( $name, $keyword ) !== false ) {
                $is_new = true;
                break;
            }
        }
    }
    if ( $is_new ) {
        $categories[] = 'new';
    }
    
    // Technical check
    $is_technical = false;
    if ( in_array( $slug, $technical_slugs ) ) {
        $is_technical = true;
    } else {
        foreach ( $technical_keywords as $t_keyword ) {
            if ( stripos( $name, $t_keyword ) !== false ) {
                $is_technical = true;
                break;
            }
        }
    }
    if ( $is_technical ) {
        $categories[] = 'technical';
    }

    // Marketing & Growth check
    foreach ( $marketing_keywords as $m_keyword ) {
        if ( stripos( $name, $m_keyword ) !== false ) {
            $categories[] = 'marketing';
            break;
        }
    }

    // Social Media check (Exclude testimonials as requested)
    if ( stripos( $name, 'testimonial' ) === false && stripos( $slug, 'testimonial' ) === false ) {
        $is_social = false;
        
        // Check manual names
        foreach ( $social_manual_names as $s_name ) {
            if ( stripos( $name, $s_name ) !== false ) {
                $is_social = true;
                break;
            }
        }
        
        // Check keywords if not already matched
        if ( ! $is_social ) {
            foreach ( $social_keywords as $s_keyword ) {
                if ( stripos( $name, $s_keyword ) !== false || stripos( $slug, $s_keyword ) !== false ) {
                    $is_social = true;
                    break;
                }
            }
        }

        if ( $is_social ) {
            $categories[] = 'social';
        }
    }

    $plugin['ag_categories'] = $categories;
    $filtered_plugins[] = (object) $plugin;
}

// Sort by active installs descending (default for "All" tab on page load)
usort( $filtered_plugins, function ( $a, $b ) {
    $a = (array) $a;
    $b = (array) $b;
    $a_installs = isset($a['active_installs']) ? (int) $a['active_installs'] : 0;
    $b_installs = isset($b['active_installs']) ? (int) $b['active_installs'] : 0;
    return $b_installs - $a_installs;
} );

?>
<div class="wrap ag-our-plugins-wrap">
    <header class="ag-our-plugins-header">
        <div class="ag-header-content">
            <h1><?php esc_html_e( 'Our WordPress Ecosystem', 'coming-soon-maintenance-mode' ); ?></h1>
            <p><?php esc_html_e( 'Discover powerful tools designed to simplify your WordPress workflow. High-performance, reliable plugins built for modern websites.', 'coming-soon-maintenance-mode' ); ?></p>
        </div>
        <div class="ag-header-stats">
            <div class="ag-stat-item">
                <span class="ag-stat-value">500k+</span>
                <span class="ag-stat-label"><?php esc_html_e( 'Active Installs', 'coming-soon-maintenance-mode' ); ?></span>
            </div>
        </div>
    </header>

    <!-- Category Filters -->
    <nav class="ag-plugins-filters">
        <button class="ag-filter-btn active" data-filter="all"><?php esc_html_e( 'All Plugins', 'coming-soon-maintenance-mode' ); ?></button>
        <button class="ag-filter-btn" data-filter="new"><?php esc_html_e( 'New Releases', 'coming-soon-maintenance-mode' ); ?></button>
        <button class="ag-filter-btn" data-filter="popular"><?php esc_html_e( 'Most Popular', 'coming-soon-maintenance-mode' ); ?></button>
        <button class="ag-filter-btn" data-filter="marketing"><?php esc_html_e( 'Marketing & Growth', 'coming-soon-maintenance-mode' ); ?></button>
        <button class="ag-filter-btn" data-filter="social"><?php esc_html_e( 'Social Media', 'coming-soon-maintenance-mode' ); ?></button>
        <button class="ag-filter-btn" data-filter="technical"><?php esc_html_e( 'Technical Tools', 'coming-soon-maintenance-mode' ); ?></button>
        
        <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'refresh_plugins', '1' ), 'ag_refresh_plugins' ) ); ?>" class="ag-refresh-link" title="<?php esc_attr_e( 'Sync with WordPress.org', 'coming-soon-maintenance-mode' ); ?>">
            <span class="dashicons dashicons-update"></span>
        </a>
    </nav>

	<?php if ( empty( $filtered_plugins ) ) : ?>
        <div class="ag-error-wrap">
            <span class="dashicons dashicons-warning"></span>
            <h2><?php esc_html_e( 'Unable to fetch our plugins', 'coming-soon-maintenance-mode' ); ?></h2>
            <p><?php esc_html_e( 'We encountered an error connecting to WordPress.org. Please try again later.', 'coming-soon-maintenance-mode' ); ?></p>
            <a href="<?php echo esc_url( 'https://profiles.wordpress.org/awordpresslife/#content-plugins' ); ?>" target="_blank" class="ag-btn ag-btn-primary" style="margin-top: 20px;">
				<?php esc_html_e( 'Visit Our WordPress Profile', 'coming-soon-maintenance-mode' ); ?>
            </a>
        </div>
	<?php else : ?>
        <div class="ag-plugins-grid" id="ag-plugins-container">
			<?php foreach ( $filtered_plugins as $plugin ) :
                $plugin = (array) $plugin;
				$icons = isset($plugin['icons']) ? (array) $plugin['icons'] : array();
				$icon = ! empty( $icons['2x'] ) ? $icons['2x'] : ( ! empty( $icons['1x'] ) ? $icons['1x'] : '' );
				
                $banners = isset($plugin['banners']) ? (array) $plugin['banners'] : array();
				$banner = ! empty( $banners['high'] ) ? $banners['high'] : ( ! empty( $banners['low'] ) ? $banners['low'] : '' );
				
				if ( empty( $banner ) ) {
					$banner = 'https://s.w.org/plugins/geopattern-icon/' . $plugin['slug'] . '.svg';
				}

                $rating = isset($plugin['rating']) ? $plugin['rating'] : 0;
				$stars = ( $rating / 100 ) * 5;
                
                $active_installs = isset($plugin['active_installs']) ? $plugin['active_installs'] : 0;
				$install_count = $active_installs >= 1000 ? ( floor( $active_installs / 1000 ) . 'k+' ) : $active_installs;
				
				$is_installed = file_exists( WP_PLUGIN_DIR . '/' . $plugin['slug'] );
                
                // Categories for JS filtering
                $cat_classes = implode( ' ', array_map( function($c) { return 'cat-' . $c; }, $plugin['ag_categories'] ) );
                $plugin_slug = isset($plugin['slug']) ? $plugin['slug'] : '';
                $manual_index = array_search( $plugin_slug, $manual_slug_order );
                if ( $manual_index === false ) {
                    $manual_index = 999;
                }
				?>
                <div class="ag-plugin-card <?php echo esc_attr( $cat_classes ); ?>" data-installs="<?php echo (int) $active_installs; ?>" data-manual-order="<?php echo (int) $manual_index; ?>">
					<?php if ( $is_installed ) : ?>
                        <div class="ag-plugin-status"><?php esc_html_e( 'INSTALLED', 'coming-soon-maintenance-mode' ); ?></div>
					<?php endif; ?>

                    <div class="ag-plugin-banner">
                        <img src="<?php echo esc_url( $banner ); ?>" alt="<?php echo esc_attr( $plugin['name'] ); ?>">
                    </div>

                    <div class="ag-plugin-content">
                        <h2><?php echo esc_html( $plugin['name'] ); ?></h2>
                        <div class="ag-plugin-description">
							<?php echo esc_html( wp_trim_words( $plugin['short_description'], 18 ) ); ?>
                        </div>

                        <div class="ag-plugin-meta">
                            <div class="ag-plugin-meta-item" title="<?php echo esc_attr( $rating ); ?>%">
                                <span class="dashicons dashicons-star-filled"></span>
								<?php echo esc_html( number_format( $stars, 1 ) ); ?>
                            </div>
                            <div class="ag-plugin-meta-item">
                                <span class="dashicons dashicons-download"></span>
								<?php echo esc_html( $install_count ); ?> <?php esc_html_e( 'Installs', 'coming-soon-maintenance-mode' ); ?>
                            </div>
                        </div>

                        <div class="ag-plugin-actions">
                            <a href="<?php echo esc_url( 'https://wordpress.org/plugins/' . $plugin['slug'] . '/' ); ?>" target="_blank" class="ag-btn ag-btn-secondary">
								<?php esc_html_e( 'Details', 'coming-soon-maintenance-mode' ); ?>
                            </a>
                            <a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin['slug'] . '&TB_iframe=true&width=772&height=550' ) ); ?>" class="ag-btn ag-btn-primary thickbox">
								<?php esc_html_e( 'Install Now', 'coming-soon-maintenance-mode' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('.ag-filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        
        // Update active button
        $('.ag-filter-btn').removeClass('active');
        $(this).addClass('active');
        
        var $container = $('#ag-plugins-container');
        var $cards = $container.children('.ag-plugin-card');
        
        if (filter === 'all') {
            // Sort by active installs descending
            $cards.sort(function(a, b) {
                var valA = parseInt($(a).attr('data-installs')) || 0;
                var valB = parseInt($(b).attr('data-installs')) || 0;
                return valB - valA;
            });
            $container.append($cards);
            $('.ag-plugin-card').fadeIn(300);
        } else {
            // Sort by manual order ascending
            $cards.sort(function(a, b) {
                var valA = $(a).attr('data-manual-order');
                var valB = $(b).attr('data-manual-order');
                
                valA = (valA !== undefined && valA !== '') ? parseInt(valA) : 999;
                valB = (valB !== undefined && valB !== '') ? parseInt(valB) : 999;
                
                if (isNaN(valA)) { valA = 999; }
                if (isNaN(valB)) { valB = 999; }
                
                // If both are not in manual list (both 999), fall back to installs descending
                if (valA === 999 && valB === 999) {
                    var instA = parseInt($(a).attr('data-installs')) || 0;
                    var instB = parseInt($(b).attr('data-installs')) || 0;
                    return instB - instA;
                }
                return valA - valB;
            });
            $container.append($cards);
            $('.ag-plugin-card').hide();
            $('.cat-' + filter).fadeIn(300);
        }
    });
});
</script>
