<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version & Setup
$csmm_current_version = get_option( 'csmm_current_version', '1.3.0' );

// Defaults
$csmm_settings      = get_option( 'csmm_settings', array() );
$csmm_templates     = get_option( 'csmm_templates', array() );
$csmm_content       = get_option( 'csmm_content', array() );
$csmm_social_media   = get_option( 'csmm_social_media', array() );
$csmm_seo           = get_option( 'csmm_seo', array() );

// Backward compatibility check for upgrades from v1.2.0 if not triggered yet
$has_v120_legacy = false !== get_option( 'comisoma_settings' ) || false !== get_option( 'comisoma_content' );
if ( $has_v120_legacy && ! get_option( 'csmm_v120_migrated' ) ) {
	if ( class_exists( 'CSMM_Activator' ) ) {
		CSMM_Activator::migrate_v120_options( true );
		$csmm_settings     = get_option( 'csmm_settings', array() );
		$csmm_templates    = get_option( 'csmm_templates', array() );
		$csmm_content      = get_option( 'csmm_content', array() );
		$csmm_social_media  = get_option( 'csmm_social_media', array() );
	}
}

$csmm_website_mode   = isset( $csmm_settings['website_mode'] ) ? intval( $csmm_settings['website_mode'] ) : 3;
$csmm_free_templates = array( 1, 4, 8, 11, 15 );
$csmm_template_id    = isset( $csmm_templates['template_id'] ) ? intval( $csmm_templates['template_id'] ) : 1;
if ( ! in_array( $csmm_template_id, $csmm_free_templates, true ) ) {
	$csmm_template_id = 1;
}

// Allow live template preview override for admins
if ( isset( $_GET['template_preview'] ) && current_user_can( 'manage_options' ) ) {
	$preview_id = intval( $_GET['template_preview'] );
	if ( in_array( $preview_id, $csmm_free_templates, true ) ) {
		$csmm_template_id = $preview_id;
	}
}

// Logo Setup
$csmm_logo_type           = isset( $csmm_content['logo_type'] ) ? $csmm_content['logo_type'] : 'graphic';
$csmm_logo_enabled        = isset( $csmm_content['logo_enabled'] ) ? ( '1' === strval( $csmm_content['logo_enabled'] ) ) : ( 'disabled' !== $csmm_logo_type );
if ( ! $csmm_logo_enabled ) {
	$csmm_logo_type = 'disabled';
}
$csmm_logo_id             = ( ! $csmm_logo_enabled || 'disabled' === $csmm_logo_type ) ? '' : ( isset( $csmm_content['logo'] ) && '' !== $csmm_content['logo'] ? $csmm_content['logo'] : '1' );
$csmm_logo_text           = ( isset( $csmm_content['logo_text'] ) && '' !== $csmm_content['logo_text'] ) ? $csmm_content['logo_text'] : ( ( isset( $csmm_content['title'] ) && '' !== $csmm_content['title'] ) ? $csmm_content['title'] : get_bloginfo( 'name' ) );
$csmm_logo_link           = ( isset( $csmm_content['logo_link'] ) && '' !== $csmm_content['logo_link'] ) ? $csmm_content['logo_link'] : home_url( '/' );
$csmm_logo_height_enabled = ! empty( $csmm_content['logo_height_enabled'] );
$csmm_logo_height         = isset( $csmm_content['logo_height'] ) ? intval( $csmm_content['logo_height'] ) : 100;
$csmm_logo_alt            = 'coming-soon-logo';
$default_logo_file        = in_array( intval( $csmm_template_id ), array( 4, 8 ), true ) ? 'logo-b.png' : 'logo-w.png';
$csmm_logo_url            = array( CSMM_URL . 'templates/images/' . $default_logo_file );

if ( ! empty( $csmm_content['logo'] ) && is_numeric( $csmm_content['logo'] ) ) {
	$medium_src = wp_get_attachment_image_src( intval( $csmm_content['logo'] ), 'full', false );
	if ( $medium_src ) {
		$csmm_logo_url = $medium_src;
	}
}

// Content defaults
$csmm_title_enabled       = isset( $csmm_content['title_enabled'] ) ? ( '1' === strval( $csmm_content['title_enabled'] ) ) : true;
$csmm_title_font_size_enabled = ! empty( $csmm_content['title_font_size_enabled'] );
$csmm_title_font_size         = isset( $csmm_content['title_font_size'] ) ? intval( $csmm_content['title_font_size'] ) : 0;
$csmm_title_color             = isset( $csmm_content['title_color'] ) ? sanitize_text_field( $csmm_content['title_color'] ) : '';
$csmm_description_enabled = isset( $csmm_content['description_enabled'] ) ? ( '1' === strval( $csmm_content['description_enabled'] ) ) : true;
$csmm_description_font_size_enabled = ! empty( $csmm_content['description_font_size_enabled'] );
$csmm_description_font_size         = isset( $csmm_content['description_font_size'] ) ? intval( $csmm_content['description_font_size'] ) : 0;
$csmm_description_color             = isset( $csmm_content['description_color'] ) ? sanitize_text_field( $csmm_content['description_color'] ) : '';

$csmm_title           = ( ! $csmm_title_enabled ) ? '' : ( isset( $csmm_content['title'] ) && '' !== $csmm_content['title'] ? $csmm_content['title'] : __( 'Coming Soon', 'coming-soon-maintenance-mode' ) );
$csmm_description_raw = ( ! $csmm_description_enabled ) ? '' : ( isset( $csmm_content['description'] ) ? $csmm_content['description'] : __( 'Thank you for visiting our website! We are currently working on creating a new and exciting online experience for you. While we finish up the final touches, please sign up for our newsletter to receive exclusive updates and offers.', 'coming-soon-maintenance-mode' ) );
$csmm_desc_placeholder = '%%CSMM_DESC_TOKEN_' . md5( __FILE__ ) . '%%';
$csmm_description     = $csmm_desc_placeholder;
$csmm_countdown       = isset( $csmm_content['countdown'] ) ? $csmm_content['countdown'] : '1';
$csmm_countdown_title = isset( $csmm_content['countdown_title'] ) ? $csmm_content['countdown_title'] : __( 'Launching In...', 'coming-soon-maintenance-mode' );
$csmm_current_date    = date( 'Y-m-d' );
$csmm_countdown_date  = ( isset( $csmm_content['countdown_date'] ) && '' !== $csmm_content['countdown_date'] && strtotime( $csmm_content['countdown_date'] ) > time() ) ? $csmm_content['countdown_date'] : date( 'Y-m-d', strtotime( '+30 days' ) );
$csmm_countdown_time  = isset( $csmm_content['countdown_time'] ) ? $csmm_content['countdown_time'] : '10:00';
$csmm_countdown_override_enabled = ! empty( $csmm_content['countdown_override_enabled'] );
$csmm_countdown_digit_font_size  = isset( $csmm_content['countdown_digit_font_size'] ) ? intval( $csmm_content['countdown_digit_font_size'] ) : 0;
$csmm_countdown_digit_color      = isset( $csmm_content['countdown_digit_color'] ) ? sanitize_text_field( $csmm_content['countdown_digit_color'] ) : '';
$csmm_countdown_label_font_size  = isset( $csmm_content['countdown_label_font_size'] ) ? intval( $csmm_content['countdown_label_font_size'] ) : 0;
$csmm_countdown_label_color      = isset( $csmm_content['countdown_label_color'] ) ? sanitize_text_field( $csmm_content['countdown_label_color'] ) : '';
$csmm_countdown_box_bg           = isset( $csmm_content['countdown_box_bg'] ) ? sanitize_text_field( $csmm_content['countdown_box_bg'] ) : '';
$csmm_susbcriber_form            = '0'; // Email Lead Capture is a Pro feature, disabled in free edition
$csmm_video_url                  = isset( $csmm_content['video_url'] ) ? $csmm_content['video_url'] : 'https://player.vimeo.com/video/427528336?title=0&portrait=0&byline=0&autoplay=1&loop=1&muted=true';
$csmm_custom_css                 = ''; // Custom CSS Overrides is a Pro feature, disabled in free edition
$csmm_slide_ids       = isset( $csmm_content['slide_ids'] ) && is_array( $csmm_content['slide_ids'] ) ? $csmm_content['slide_ids'] : array();

// Graphic Background Settings (Free supports 'default', 'custom', 'solid')
$csmm_bg_type_raw            = isset( $csmm_content['bg_type'] ) ? $csmm_content['bg_type'] : 'default';
$csmm_bg_type                = in_array( $csmm_bg_type_raw, array( 'default', 'custom', 'solid' ), true ) ? $csmm_bg_type_raw : 'default';
$csmm_bg_custom_images       = isset( $csmm_content['bg_custom_images'] ) && is_array( $csmm_content['bg_custom_images'] ) ? $csmm_content['bg_custom_images'] : array();
$csmm_bg_image_size          = isset( $csmm_content['bg_image_size'] ) ? $csmm_content['bg_image_size'] : 'cover';
$csmm_bg_mobile_enabled      = ! empty( $csmm_content['bg_mobile_enabled'] );
$csmm_bg_mobile_image_url    = isset( $csmm_content['bg_mobile_image_url'] ) ? $csmm_content['bg_mobile_image_url'] : '';
$csmm_bg_solid_color         = isset( $csmm_content['bg_solid_color'] ) ? $csmm_content['bg_solid_color'] : '#1d1b1b';
$default_overlay             = in_array( $csmm_bg_type, array( 'custom', 'solid' ), true ) ? 'none' : 'solid';
$csmm_bg_overlay_type        = ( isset( $csmm_content['bg_overlay_type'] ) && '' !== $csmm_content['bg_overlay_type'] ) ? $csmm_content['bg_overlay_type'] : $default_overlay;
$csmm_bg_overlay_color       = isset( $csmm_content['bg_overlay_color'] ) ? $csmm_content['bg_overlay_color'] : '#000000';
$csmm_bg_overlay_opacity     = isset( $csmm_content['bg_overlay_opacity'] ) ? floatval( $csmm_content['bg_overlay_opacity'] ) : 0;
$csmm_bg_blur                = isset( $csmm_content['bg_blur'] ) ? intval( $csmm_content['bg_blur'] ) : 0;

// Launch timestamp string (e.g., 'October 25, 2026 10:00:00')
$csmm_launch_date = date( 'F d, Y', strtotime( $csmm_countdown_date ) );
$csmm_launch_time = date( 'H:i:s', strtotime( $csmm_countdown_time ) );
$csmm_launch_dt   = $csmm_launch_date . ' ' . $csmm_launch_time;

// Social Media Links (Free version: Facebook, Twitter / X, Instagram only)
$csmm_social_media_enabled = ! isset( $csmm_social_media['enabled'] ) || ! empty( $csmm_social_media['enabled'] );
if ( $csmm_social_media_enabled ) {
	$csmm_sm_facebook  = isset( $csmm_social_media['csmm_sm_facebook'] ) ? $csmm_social_media['csmm_sm_facebook'] : '#';
	$csmm_sm_twitter   = isset( $csmm_social_media['csmm_sm_twitter'] ) ? $csmm_social_media['csmm_sm_twitter'] : '#';
	$csmm_sm_instagram = isset( $csmm_social_media['csmm_sm_instagram'] ) ? $csmm_social_media['csmm_sm_instagram'] : '#';
} else {
	$csmm_sm_facebook  = '';
	$csmm_sm_twitter   = '';
	$csmm_sm_instagram = '';
}
$csmm_sm_youtube   = '';
$csmm_sm_linkedin  = '';
$csmm_sm_pinterest = '';
$csmm_sm_tumblr    = '';
$csmm_sm_snapchat  = '';
$csmm_sm_behance   = '';
$csmm_sm_dribbble  = '';
$csmm_sm_whatsapp  = '';
$csmm_sm_tiktok    = '';
$csmm_sm_qq        = '';

// Social Icon Styling & Overrides (Variables initialized safely to avoid undefined warnings)
$csmm_social_icon_size_enabled  = ! empty( $csmm_social_media['social_icon_size_enabled'] );
$csmm_social_icon_size          = isset( $csmm_social_media['social_icon_size'] ) ? intval( $csmm_social_media['social_icon_size'] ) : 0;
$csmm_social_icon_color_enabled = ! empty( $csmm_social_media['social_icon_color_enabled'] );
$csmm_social_icon_color         = isset( $csmm_social_media['social_icon_color'] ) ? sanitize_text_field( $csmm_social_media['social_icon_color'] ) : '';
$csmm_social_icon_hover_color   = isset( $csmm_social_media['social_icon_hover_color'] ) ? sanitize_text_field( $csmm_social_media['social_icon_hover_color'] ) : '';

// Subscriber Form UI Settings
$csmm_form_headline      = isset( $csmm_content['form_headline_text'] ) ? $csmm_content['form_headline_text'] : '';
$csmm_form_placeholder   = isset( $csmm_content['form_placeholder_text'] ) && '' !== $csmm_content['form_placeholder_text'] ? $csmm_content['form_placeholder_text'] : __( 'Email Address', 'coming-soon-maintenance-mode' );
$csmm_form_btn_text      = isset( $csmm_content['form_btn_text'] ) && '' !== $csmm_content['form_btn_text'] ? $csmm_content['form_btn_text'] : __( 'Notify Me', 'coming-soon-maintenance-mode' );
$csmm_form_input_bg      = isset( $csmm_content['form_input_bg'] ) && '' !== $csmm_content['form_input_bg'] ? $csmm_content['form_input_bg'] : 'rgba(0, 0, 0, 0.7)';
$csmm_form_input_color   = isset( $csmm_content['form_input_color'] ) && '' !== $csmm_content['form_input_color'] ? $csmm_content['form_input_color'] : '#ffffff';
$csmm_form_btn_bg        = isset( $csmm_content['form_btn_bg'] ) && '' !== $csmm_content['form_btn_bg'] ? $csmm_content['form_btn_bg'] : '#e11d48';
$csmm_form_btn_color     = isset( $csmm_content['form_btn_color'] ) && '' !== $csmm_content['form_btn_color'] ? $csmm_content['form_btn_color'] : '#ffffff';
$csmm_form_border_radius = isset( $csmm_content['form_border_radius'] ) ? intval( $csmm_content['form_border_radius'] ) : 0;

// Resolve template file safely
$template_file = CSMM_DIR . "templates/{$csmm_template_id}.php";
if ( ! file_exists( $template_file ) ) {
	$template_file = CSMM_DIR . 'templates/1.php';
}

// Render template with injected SEO & Social metadata
ob_start();
include $template_file;
$html = ob_get_clean();

// Remove particle mesh DOM element and scripts if non-default background is active
if ( in_array( $csmm_bg_type, array( 'pattern', 'solid', 'gradient', 'custom', 'video' ), true ) ) {
	$html = preg_replace( '/<div[^>]*id=["\']particles-js["\'][^>]*>\s*<\/div>/is', '', $html );
	$html = preg_replace( '/<script[^>]*src=["\'][^"\']*(?:particles|polygons)[^"\']*["\'][^>]*><\/script>\s*/is', '', $html );
}

// 1. Process Social Media: Strip all social media elements when disabled
if ( ! $csmm_social_media_enabled ) {
	$html = preg_replace( '/<ul[^>]*class=["\'][^"\']*home-social[^"\']*["\'][^>]*>.*?<\/ul>/is', '', $html );
	$html = preg_replace( '/<div[^>]*class=["\'][^"\']*(?:social-links|social-icons|home-content__social|social-wrapper)[^"\']*["\'][^>]*>.*?<\/div>/is', '', $html );
}

// 2. Process Subscriber Form (Email Lead Capture) when Disabled
if ( '0' === strval( $csmm_susbcriber_form ) ) {
	$html = preg_replace( '/<div[^>]*class=["\'][^"\']*(?:home-content__subscribe|subscribe-wrapper|template-two-form)[^"\']*["\'][^>]*>.*?<\/div>/is', '', $html );
	$html = preg_replace( '/<form[^>]*id=["\'](?:subscribe-form|mc-form)["\'][^>]*>.*?<\/form>/is', '', $html );
}

// 3. Process Logo across all templates (Text, Graphic with height/link, or Disabled)
if ( ! $csmm_logo_enabled || 'disabled' === $csmm_logo_type ) {
	$html = preg_replace( '/<div class="home-logo">.*?<\/div>/is', '', $html );
	$html = preg_replace( '/<div class="[^"]*mb-6[^"]*">\s*<a[^>]*>\s*<img[^>]*>\s*<\/a>\s*<\/div>/is', '', $html );
} elseif ( 'text' === $csmm_logo_type ) {
	$logo_href = ! empty( $csmm_logo_link ) ? esc_url( $csmm_logo_link ) : esc_url( home_url( '/' ) );
	$text_logo_html = '<div class="home-logo csmm-text-logo"><a href="' . $logo_href . '" style="font-family: inherit; font-size: 2.2rem; font-weight: 800; color: #ffffff; text-decoration: none; display: inline-block; letter-spacing: -0.02em;">' . esc_html( $csmm_logo_text ) . '</a></div>';
	if ( preg_match( '/<div class="home-logo">.*?<\/div>/is', $html ) ) {
		$html = preg_replace( '/<div class="home-logo">.*?<\/div>/is', $text_logo_html, $html, 1 );
	} elseif ( preg_match( '/<div class="[^"]*mb-6[^"]*">\s*<a[^>]*>\s*<img[^>]*>\s*<\/a>\s*<\/div>/is', $html ) ) {
		$html = preg_replace( '/<div class="[^"]*mb-6[^"]*">\s*<a[^>]*>\s*<img[^>]*>\s*<\/a>\s*<\/div>/is', $text_logo_html, $html, 1 );
	}
} else {
	// Graphic logo: apply custom link or default to home_url('/')
	$logo_href = ! empty( $csmm_logo_link ) ? esc_url( $csmm_logo_link ) : esc_url( home_url( '/' ) );
	if ( preg_match( '/<div class="home-logo">\s*<a href="[^"]*">/i', $html ) ) {
		$html = preg_replace( '/<div class="home-logo">\s*<a href="[^"]*">/i', '<div class="home-logo"><a href="' . $logo_href . '">', $html, 1 );
	}
}

// 3. Process Description & Title visibility and full rich content / shortcode / embed rendering
if ( ! $csmm_title_enabled ) {
	$html = preg_replace( '/<h1\b[^>]*>.*?<\/h1>/is', '', $html );
}

// Replace <meta name="description" content="..."> with clean stripped plain text
$csmm_meta_desc_text = wp_strip_all_tags( stripslashes( $csmm_description_raw ) );
$html = str_replace( '<meta name="description" content="' . $csmm_desc_placeholder . '">', '<meta name="description" content="' . esc_attr( $csmm_meta_desc_text ) . '">', $html );
$html = preg_replace( '/<meta name="description" content="[^"]*%%CSMM_DESC_TOKEN_[^"]*">/i', '<meta name="description" content="' . esc_attr( $csmm_meta_desc_text ) . '">', $html );

if ( ! $csmm_description_enabled || empty( $csmm_description_raw ) ) {
	$html = preg_replace( '/<div class="csmm-description-content">.*?<\/div>/is', '', $html );
	$html = preg_replace( '/<div id="postcard-message-container"[^>]*>.*?<\/div>/is', '', $html );
	$html = preg_replace( '/<p[^>]*>\s*' . preg_quote( $csmm_desc_placeholder, '/' ) . '\s*<\/p>/is', '', $html );
	$html = str_replace( $csmm_desc_placeholder, '', $html );
	$html = preg_replace( '/(<h1>.*?<\/h1>\s*)<p\b[^>]*>.*?<\/p>/is', '$1', $html );
} else {
	// Full support for Shortcodes, WordPress Auto-Embeds, Custom HTML, and wpautop
	$processed_desc = stripslashes( $csmm_description_raw );
	global $wp_embed;
	if ( is_object( $wp_embed ) ) {
		$processed_desc = $wp_embed->autoembed( $processed_desc );
		$processed_desc = $wp_embed->run_shortcode( $processed_desc );
	}
	$processed_desc = do_shortcode( $processed_desc );
	$processed_desc = wpautop( $processed_desc );

	$desc_html = '<div class="csmm-description-content">' . $processed_desc . '</div>';

	// Replace template <p>...</p> placeholder or plain token with the rich description div
	$html = preg_replace( '/<p[^>]*>\s*' . preg_quote( $csmm_desc_placeholder, '/' ) . '\s*<\/p>/is', $desc_html, $html );
	$html = str_replace( $csmm_desc_placeholder, $desc_html, $html );
}

// 4. Process Form Custom Placeholder, Button Text & Headline Text
if ( ! empty( $csmm_form_placeholder ) ) {
	$html = preg_replace( '/placeholder="[^"]*"/i', 'placeholder="' . esc_attr( $csmm_form_placeholder ) . '"', $html, 1 );
}
if ( ! empty( $csmm_form_btn_text ) ) {
	$html = preg_replace( '/value="(Notify Me|Subscribe|Sign Up|Join Now)"/i', 'value="' . esc_attr( $csmm_form_btn_text ) . '"', $html, 1 );
}
if ( ! empty( $csmm_form_headline ) ) {
	$headline_escaped = esc_html( $csmm_form_headline );
	$html = preg_replace( '/(<p[^>]*class="[^"]*(?:prospectus-form-label|form-label)[^"]*"[^>]*>).*?(<\/p>)/is', '$1' . $headline_escaped . '$2', $html, 1 );
}

// 5. Construct Dynamic Background, Overlay & Form Styles
$dynamic_css = "\n<style id=\"csmm-dynamic-content-styles\">\n";

// Logo height constraint & visibility
if ( ! $csmm_logo_enabled || 'disabled' === $csmm_logo_type ) {
	$dynamic_css .= ".home-logo, .logo, .site-logo, .csmm-logo-wrap, .csmm-text-logo { display: none !important; }\n";
} elseif ( 'graphic' === $csmm_logo_type && $csmm_logo_height_enabled && $csmm_logo_height > 0 ) {
	$dynamic_css .= ".home-logo img { max-height: {$csmm_logo_height}px !important; height: auto !important; width: auto !important; }\n";
}

// Title toggle & font size / color override (Supported across all 36 templates)
if ( ! $csmm_title_enabled ) {
	$dynamic_css .= "h1, .home-content__text h1, .home-content h1, .title, .title-font, .reveal-text, .hero-title, .section-title, .main-title, .highlight, h1 span.highlight, h1.title-font, .banner-text h1, .display-1, .display-2 { display: none !important; }\n";
} elseif ( $csmm_title_font_size_enabled ) {
	$title_rules = array();
	if ( $csmm_title_font_size > 0 ) {
		$title_rules[] = "font-size: {$csmm_title_font_size}px !important";
		$title_rules[] = "line-height: 1.2 !important";
	}
	if ( ! empty( $csmm_title_color ) ) {
		$title_rules[] = "color: {$csmm_title_color} !important";
		$title_rules[] = "-webkit-text-fill-color: {$csmm_title_color} !important";
		$title_rules[] = "background-image: none !important";
		$title_rules[] = "background: none !important";
	}
	if ( ! empty( $title_rules ) ) {
		$title_rule_str = implode( '; ', $title_rules );
		$dynamic_css .= "h1, .home-content__text h1, .home-content h1, .title, .title-font, .reveal-text, .hero-title, .section-title, .main-title, .highlight, h1 span.highlight, h1.title-font, .banner-text h1, .display-1, .display-2 { {$title_rule_str}; }\n";
	}
}

// Description toggle & font size / color override (Supported across all 36 templates)
if ( ! $csmm_description_enabled ) {
	$dynamic_css .= ".csmm-description-content, .home-content__text p, .home-content p, #postcard-message-container, #postcard-message, .description, .hero-desc, .section-desc, .sub-title, .content p, .lead { display: none !important; }\n";
} elseif ( $csmm_description_font_size_enabled ) {
	$desc_rules = array();
	if ( $csmm_description_font_size > 0 ) {
		$desc_rules[] = "font-size: {$csmm_description_font_size}px !important";
		$desc_rules[] = "line-height: 1.6 !important";
	}
	if ( ! empty( $csmm_description_color ) ) {
		$desc_rules[] = "color: {$csmm_description_color} !important";
	}
	if ( ! empty( $desc_rules ) ) {
		$desc_rule_str = implode( '; ', $desc_rules );
		$dynamic_css .= ".csmm-description-content, .csmm-description-content *, .csmm-description-content p, .home-content__text p, .home-content p, #postcard-message-container, #postcard-message, .description, .hero-desc, .section-desc, .sub-title, .content p, .lead { {$desc_rule_str}; }\n";
	}
}

// Social Icon Styling & Overrides (Size, Color, Hover Color)
if ( ! $csmm_social_media_enabled ) {
	$dynamic_css .= ".home-social, ul.home-social, .social-links, .social-icons, .home-content__social, #social-media, .social-media-container, .social-wrapper, .social, .s-footer .social-list { display: none !important; opacity: 0 !important; visibility: hidden !important; height: 0 !important; margin: 0 !important; padding: 0 !important; pointer-events: none !important; }\n";
} else {
	if ( ! empty( $csmm_social_icon_size_enabled ) && ! empty( $csmm_social_icon_size ) && $csmm_social_icon_size > 0 ) {
		$dynamic_css .= ".home-social, .social-links, .social-icons, .social-media, .social, .s-footer .social-list { gap: 16px !important; }\n";
		$dynamic_css .= ".home-social i, .home-social a i, .home-social li a i, .home-social svg, .social-links i, .social-links a i, .social-icons i, .social-icons a i, .social i, .social a i, .s-footer .social-list i, .s-footer .social-list a i { font-size: {$csmm_social_icon_size}px !important; width: auto !important; height: auto !important; line-height: 1 !important; }\n";
		$dynamic_css .= ".home-social a, .home-social li a, .social-links a, .social-icons a, .social a { font-size: {$csmm_social_icon_size}px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; }\n";
	}

	if ( ! empty( $csmm_social_icon_color_enabled ) ) {
		if ( ! empty( $csmm_social_icon_color ) ) {
			$dynamic_css .= ".home-social a, .home-social li a, .home-social i, .home-social a i, .home-social li a i, .home-social span, .home-social svg, .social-links a, .social-links i, .social-links a i, .social-icons a, .social-icons i, .social-icons a i, .social a, .social i, .social a i, .s-footer .social-list a, .s-footer .social-list i, .s-footer .social-list a i, .social-icon, a.social-icon { color: {$csmm_social_icon_color} !important; fill: {$csmm_social_icon_color} !important; -webkit-text-fill-color: {$csmm_social_icon_color} !important; }\n";
		}
		if ( ! empty( $csmm_social_icon_hover_color ) ) {
			$dynamic_css .= ".home-social a:hover, .home-social li:hover a, .home-social li a:hover, .home-social a:hover i, .home-social li a:hover i, .home-social li:hover i, .home-social li:hover span, .home-social a:hover svg, .social-links a:hover, .social-links a:hover i, .social-icons a:hover, .social-icons a:hover i, .social a:hover, .social a:hover i, .s-footer .social-list a:hover, .s-footer .social-list a:hover i, .social-icon:hover, a.social-icon:hover { color: {$csmm_social_icon_hover_color} !important; fill: {$csmm_social_icon_hover_color} !important; -webkit-text-fill-color: {$csmm_social_icon_hover_color} !important; }\n";
		}
	}
}

// Countdown & Subscriber Form toggles & styling
if ( '0' === strval( $csmm_countdown ) ) {
	$dynamic_css .= ".home-content__counter, .home-content__clock, #countdown, .countdown, .countdown-container, .countdown-box, .counter-box { display: none !important; }\n";
} elseif ( $csmm_countdown_override_enabled ) {
	// Digits / Numbers
	$digit_rules = array();
	if ( $csmm_countdown_digit_font_size > 0 ) {
		$digit_rules[] = "font-size: {$csmm_countdown_digit_font_size}px !important";
		$digit_rules[] = "line-height: 1.1 !important";
	}
	if ( ! empty( $csmm_countdown_digit_color ) ) {
		$digit_rules[] = "color: {$csmm_countdown_digit_color} !important";
		$digit_rules[] = "-webkit-text-fill-color: {$csmm_countdown_digit_color} !important";
		$digit_rules[] = "background-image: none !important";
		$digit_rules[] = "background: none !important";
	}
	if ( ! empty( $digit_rules ) ) {
		$digit_rule_str = implode( '; ', $digit_rules );
		$dynamic_css .= ".home-content__clock .time, .home-content__clock, .countdown-number, .countdown-amount, .counter-number, .countdown-box .text-3xl, .countdown-box .text-4xl, .countdown-box .text-5xl, .countdown-box .font-bold, #days, #hours, #minutes, #seconds, .time.days, .time.hours, .time.minutes, .time.seconds { {$digit_rule_str}; }\n";
	}

	// Labels (Days, Hours, Mins, Secs)
	$label_rules = array();
	if ( $csmm_countdown_label_font_size > 0 ) {
		$label_rules[] = "font-size: {$csmm_countdown_label_font_size}px !important";
	}
	if ( ! empty( $csmm_countdown_label_color ) ) {
		$label_rules[] = "color: {$csmm_countdown_label_color} !important";
	}
	if ( ! empty( $label_rules ) ) {
		$label_rule_str = implode( '; ', $label_rules );
		$dynamic_css .= ".home-content__clock .time span, .countdown-label, .counter-label, .time-text, .subtext, .countdown-text, .timer-label, .countdown-period { {$label_rule_str}; }\n";
	}

	// Box backgrounds
	if ( ! empty( $csmm_countdown_box_bg ) ) {
		$dynamic_css .= ".countdown-box, .counter-box, .time-box, .timer-box, .count-box, .counter-item, #countdown > div { background: {$csmm_countdown_box_bg} !important; background-color: {$csmm_countdown_box_bg} !important; }\n";
	}
}
if ( '0' === strval( $csmm_susbcriber_form ) ) {
	$dynamic_css .= ".home-content__subscribe, #mc-form, #subscribe-form, .subscribe-form, .subscribe-wrapper, .subscribe-box, .template-two-form, .prospectus-content #subscribe-form, .prospectus-form-label, .party-form, .notify-wrapper, .newsletter-form, .subscription-form, .form-container, .main-card #subscribe-form, .split-layout #subscribe-form, .content-side #subscribe-form, .postcard #subscribe-form, .template-one-form, .form-wrapper, form.subscribe-form { display: none !important; opacity: 0 !important; visibility: hidden !important; height: 0 !important; margin: 0 !important; padding: 0 !important; pointer-events: none !important; }\n";
}

// Subscriber Form Input & Button Styling (Absolute Overlay Style for Templates 1-16)
$dynamic_css .= ".home-content__subscribe, #mc-form, .home-content__form { max-width: 540px !important; width: 100% !important; height: 54px !important; min-height: 54px !important; max-height: 54px !important; margin-top: 20px !important; margin-bottom: 28px !important; position: relative !important; }\n";
$dynamic_css .= ".home-content__subscribe input[type=\"email\"], #mc-form input[type=\"email\"] { background: {$csmm_form_input_bg} !important; background-color: {$csmm_form_input_bg} !important; color: {$csmm_form_input_color} !important; border-top-left-radius: {$csmm_form_border_radius}px !important; border-bottom-left-radius: {$csmm_form_border_radius}px !important; border-top-right-radius: 0px !important; border-bottom-right-radius: 0px !important; border: none !important; padding-right: 180px !important; padding-left: 20px !important; box-sizing: border-box !important; width: 100% !important; height: 54px !important; min-height: 54px !important; max-height: 54px !important; line-height: 54px !important; margin: 0 !important; margin-bottom: 0 !important; }\n";
$dynamic_css .= ".home-content__subscribe input[type=\"email\"]::placeholder, #mc-form input[type=\"email\"]::placeholder, input#csmm-email::placeholder, .subscribe-input::placeholder { color: {$csmm_form_input_color} !important; opacity: 0.85 !important; }\n";
$dynamic_css .= ".home-content__subscribe input[type=\"submit\"], #mc-form input[type=\"submit\"], .home-content__subscribe button, #mc-form button { background: {$csmm_form_btn_bg} !important; background-color: {$csmm_form_btn_bg} !important; color: {$csmm_form_btn_color} !important; border-top-right-radius: {$csmm_form_border_radius}px !important; border-bottom-right-radius: {$csmm_form_border_radius}px !important; border-top-left-radius: 0px !important; border-bottom-left-radius: 0px !important; border-color: {$csmm_form_btn_bg} !important; border: none !important; height: 54px !important; min-height: 54px !important; max-height: 54px !important; line-height: 54px !important; padding: 0 28px !important; top: 0 !important; right: 0 !important; margin: 0 !important; position: absolute !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; }\n";
$dynamic_css .= ".home-content__subscribe input:disabled, .home-content__subscribe button:disabled, #mc-form input:disabled, #mc-form button:disabled, form.subscribe-form input:disabled, form.subscribe-form button:disabled, #subscribe-form input:disabled, #subscribe-form button:disabled, .subscribe-input:disabled, .subscribe-btn:disabled { opacity: 0.7 !important; cursor: not-allowed !important; pointer-events: none !important; }\n";
$dynamic_css .= ".home-content__subscribe label.subscribe-message, #mc-form label.subscribe-message, #mc-form label { position: absolute !important; top: 62px !important; left: 0 !important; right: 0 !important; margin-top: 0 !important; margin-bottom: 0 !important; }\n";

// Subscriber Form (Side-by-Side Flex Style for Templates 2, 17-36)
$dynamic_css .= "#subscribe-form, .subscribe-form, .postcard #subscribe-form, .split-layout #subscribe-form, .main-card #subscribe-form, .main-content #subscribe-form, .prospectus-content #subscribe-form, .content-side #subscribe-form, .template-two-form #subscribe-form { position: relative !important; max-width: 520px !important; width: 100% !important; min-height: 48px !important; display: flex !important; flex-direction: row !important; align-items: stretch !important; box-sizing: border-box !important; }\n";
$dynamic_css .= "#subscribe-form input[type=\"email\"], #subscribe-form input#csmm-email, #subscribe-form .subscribe-input, .subscribe-form input[type=\"email\"], .subscribe-form input#csmm-email, .subscribe-form .subscribe-input { background: {$csmm_form_input_bg} !important; background-color: {$csmm_form_input_bg} !important; color: {$csmm_form_input_color} !important; padding-left: 18px !important; padding-right: 18px !important; width: auto !important; flex: 1 1 auto !important; min-width: 0 !important; height: 50px !important; min-height: 50px !important; max-height: 50px !important; line-height: 50px !important; margin: 0 !important; border: none !important; box-sizing: border-box !important; border-top-left-radius: {$csmm_form_border_radius}px !important; border-bottom-left-radius: {$csmm_form_border_radius}px !important; border-top-right-radius: 0px !important; border-bottom-right-radius: 0px !important; }\n";
$dynamic_css .= "#subscribe-form button, #subscribe-form input[type=\"submit\"], #subscribe-form .subscribe-btn, .subscribe-form button, .subscribe-form input[type=\"submit\"], .subscribe-form .subscribe-btn { background: {$csmm_form_btn_bg} !important; background-color: {$csmm_form_btn_bg} !important; color: {$csmm_form_btn_color} !important; position: static !important; height: 50px !important; min-height: 50px !important; max-height: 50px !important; line-height: 50px !important; margin: 0 !important; padding: 0 24px !important; white-space: nowrap !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; border: none !important; box-sizing: border-box !important; border-top-right-radius: {$csmm_form_border_radius}px !important; border-bottom-right-radius: {$csmm_form_border_radius}px !important; border-top-left-radius: 0px !important; border-bottom-left-radius: 0px !important; }\n";

if ( 17 === intval( $csmm_template_id ) ) {
	$dynamic_css .= "html, body { min-height: 100vh !important; height: 100vh !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 !important; padding: 1.5rem !important; box-sizing: border-box !important; overflow: hidden !important; }\n";
	$dynamic_css .= ".prospectus-card { max-width: 880px !important; width: 100% !important; min-height: 480px !important; max-height: 520px !important; height: 500px !important; margin: auto !important; display: grid !important; grid-template-columns: 1.15fr 0.85fr !important; background-color: #FFFFFF !important; border-radius: 12px !important; overflow: hidden !important; box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.25) !important; position: relative !important; z-index: 2 !important; }\n";
	$dynamic_css .= ".prospectus-content { display: flex !important; flex-direction: column !important; justify-content: center !important; padding: 2.5rem 2.25rem !important; box-sizing: border-box !important; height: 100% !important; }\n";
	$dynamic_css .= ".prospectus-card .image-container { height: 100% !important; width: 100% !important; display: block !important; position: relative !important; overflow: hidden !important; clip-path: polygon(0 0, 100% 0, 100% 88%, 0% 100%) !important; }\n";
	$dynamic_css .= ".prospectus-card .image-container img { width: 100% !important; height: 100% !important; object-fit: cover !important; display: block !important; }\n";
	$dynamic_css .= ".prospectus-content #subscribe-form, #subscribe-form { display: flex !important; flex-direction: row !important; align-items: stretch !important; height: 44px !important; min-height: 44px !important; max-height: 44px !important; max-width: 380px !important; width: 100% !important; margin: 0 !important; }\n";
	$dynamic_css .= ".prospectus-content #subscribe-form input, .prospectus-content #subscribe-form input[type=\"email\"], .prospectus-content #subscribe-form input#csmm-email, .prospectus-content #subscribe-form .subscribe-input, #subscribe-form input[type=\"email\"], input#csmm-email { background: #F7FAFC !important; background-color: #F7FAFC !important; border: 1px solid #E2E8F0 !important; border-right: none !important; color: #2D3748 !important; border-radius: 4px 0 0 4px !important; border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; height: 44px !important; min-height: 44px !important; max-height: 44px !important; line-height: 44px !important; padding: 0 14px !important; font-size: 13px !important; box-sizing: border-box !important; flex: 1 1 auto !important; margin: 0 !important; }\n";
	$dynamic_css .= ".prospectus-content #subscribe-form button, .prospectus-content #subscribe-form button[type=\"submit\"], .prospectus-content #subscribe-form .subscribe-btn, #subscribe-form button, button[type=\"submit\"].subscribe-btn { background: #1A202C !important; background-color: #1A202C !important; color: #FFFFFF !important; border: 1px solid #1A202C !important; border-radius: 0 4px 4px 0 !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; height: 44px !important; min-height: 44px !important; max-height: 44px !important; line-height: 44px !important; padding: 0 18px !important; font-size: 12px !important; font-weight: 600 !important; letter-spacing: 0.5px !important; white-space: nowrap !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; margin: 0 !important; cursor: pointer !important; }\n";
}

// Responsive Layout & Long Description Scroll Support (Prevents cutting off subscriber form)
$dynamic_css .= "html, body { min-height: 100% !important; height: auto !important; overflow-x: hidden !important; overflow-y: auto !important; }\n";
$dynamic_css .= ".s-home, section.s-home, #home, .template-one, .template-two, .split-layout, .container-fluid, .wrapper { min-height: 100vh !important; height: auto !important; }\n";
$dynamic_css .= ".main-card, .prospectus-card { min-height: auto !important; height: auto !important; }\n";
$dynamic_css .= ".home-content { min-height: 100vh !important; height: auto !important; box-sizing: border-box !important; padding-bottom: 5rem !important; }\n";
$dynamic_css .= ".home-content__main { padding-top: clamp(2.5rem, 6vh, 8rem) !important; padding-bottom: 2.5rem !important; position: relative !important; }\n";
$dynamic_css .= ".home-content__text { overflow: visible !important; height: auto !important; }\n";
$dynamic_css .= ".home-content__text p, .home-content p, .template-two-desc, .template-two-content .csmm-description-content, .template-two .csmm-description-content, .home-content__text > p { word-wrap: break-word !important; overflow-wrap: break-word !important; word-break: break-word !important; line-height: 1.65 !important; margin-bottom: 24px !important; }\n";
$dynamic_css .= ".template-one, main.template-one, .s-home.template-one, .s-home--particles.template-one { display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; min-height: 100vh !important; height: auto !important; padding: 40px 20px !important; box-sizing: border-box !important; }\n";
$dynamic_css .= ".template-one .home-content { display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; width: 100% !important; height: auto !important; padding: 0 !important; margin: auto !important; }\n";
$dynamic_css .= ".template-one .home-content__main { padding-top: 0 !important; margin: 0 auto !important; width: 100% !important; max-width: 960px !important; }\n";
$dynamic_css .= ".template-two, main.template-two, .s-home--static.template-two { display: flex !important; align-items: center !important; justify-content: center !important; min-height: 100vh !important; height: auto !important; padding: 40px 20px !important; box-sizing: border-box !important; }\n";
$dynamic_css .= ".template-two .home-content { display: flex !important; align-items: center !important; justify-content: center !important; width: 100% !important; height: auto !important; padding: 0 !important; margin: auto !important; }\n";
$dynamic_css .= ".template-two .home-content__main { width: 100% !important; max-width: 980px !important; margin: 0 auto !important; padding: 0 !important; }\n";
$dynamic_css .= ".template-two .frame-ipad { margin: 0 auto !important; max-width: 900px !important; width: 100% !important; box-sizing: border-box !important; padding: 3rem 2rem 2.5rem 2rem !important; }\n";
$dynamic_css .= ".template-two .home-logo { margin-top: 1.5rem !important; margin-bottom: 1.5rem !important; }\n";
$dynamic_css .= ".template-two .home-logo a.logo-dark { height: auto !important; min-height: auto !important; }\n";
$dynamic_css .= ".template-two-desc, .template-two-content .csmm-description-content, .template-two .csmm-description-content { margin-bottom: 2.5rem !important; max-width: 680px !important; }\n";
$dynamic_css .= ".template-two-countdown, .template-two-content .template-two-countdown { margin-top: 2.2rem !important; margin-bottom: 2.5rem !important; }\n";
$dynamic_css .= ".template-two-countdown .countdown-title { margin-bottom: 1.25rem !important; }\n";
$dynamic_css .= ".template-two-form, .template-two-content .template-two-form { margin-top: 2.2rem !important; }\n";

// Template Defaults: ONLY Templates 4 and 8 default to Black (#000000), Templates 1, 11, and 15 default to White (#ffffff)
if ( in_array( intval( $csmm_template_id ), array( 4, 8 ), true ) ) {
	if ( empty( $csmm_title_font_size_enabled ) || empty( $csmm_title_color ) ) {
		$dynamic_css .= "h1, .home-content h1, .home-content__text h1 { color: #000000 !important; -webkit-text-fill-color: #000000 !important; }\n";
	}
	if ( empty( $csmm_description_font_size_enabled ) || empty( $csmm_description_color ) ) {
		$dynamic_css .= ".home-content__text p, .home-content p, .csmm-description-content, .csmm-description-content p { color: #000000 !important; }\n";
	}
} else {
	if ( empty( $csmm_title_font_size_enabled ) || empty( $csmm_title_color ) ) {
		if ( 1 === intval( $csmm_template_id ) ) {
			$dynamic_css .= ".home-content h1, .home-content__text h1 { color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; }\n";
		} elseif ( 11 === intval( $csmm_template_id ) ) {
			$dynamic_css .= ".home-content h1 { -webkit-text-stroke-color: #ffffff !important; color: transparent !important; }\n";
		} elseif ( 15 === intval( $csmm_template_id ) ) {
			$dynamic_css .= ".home-content h1 { -webkit-text-stroke-color: #ffffff !important; }\n";
		}
	}
	if ( empty( $csmm_description_font_size_enabled ) || empty( $csmm_description_color ) ) {
		$dynamic_css .= ".home-content__text p, .home-content p, .home-content__main p, .csmm-description-content, .csmm-description-content p { color: #ffffff !important; }\n";
	}
}

// Graphic Background Types - Complete replacement of template background when non-default (Free supports custom & solid)
if ( in_array( $csmm_bg_type, array( 'solid', 'custom' ), true ) ) {
	$dynamic_css .= ".s-home::before, .s-home::after, .s-home--static::before, .s-home--particles::before, .s-home .overlay, .s-home .gradient-overlay, .home-overlay, .grid-overlay, .s-home .grid-overlay, .home-slider, .home-slider-img, .home-slider-img::before, .parallax-mirror, .parallax-slider, .PhotoZoom_iframe__LeuQM, .PhotoZoom_image__iR_Ia, video.PhotoZoom_iframe__LeuQM, #vjs_video_3_html5_api, .video-background, #main-video, #gameCanvas, #gameOver, .blueprint-bg, .blueprint-element, .background-shapes, .background-shapes .shape, .overlay, #network-canvas, #particles-canvas, .image-background, .background-pattern, .background-elements, .hills, .sun, .cloud { display: none !important; opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; background-image: none !important; background: none !important; }\n";
	$dynamic_css .= "#particles-js, .home-particles, .particles-js-canvas-el, #particles-js canvas { display: none !important; opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }\n";
	if ( 'solid' === $csmm_bg_type ) {
		$dynamic_css .= ".image-side { display: none !important; opacity: 0 !important; visibility: hidden !important; animation: none !important; }\n";
		$dynamic_css .= ".split-layout { display: flex !important; align-items: center !important; justify-content: center !important; min-height: 100vh !important; }\n";
		$dynamic_css .= ".split-layout .content-side { max-width: 620px !important; width: 100% !important; margin: 2rem auto !important; border-radius: 1.25rem !important; position: relative !important; z-index: 2 !important; }\n";
	}
	$dynamic_css .= ".background-image, .background-gradient { display: none !important; opacity: 0 !important; visibility: hidden !important; animation: none !important; }\n";
	$dynamic_css .= ".home-content, .s-home .row, .home-content__main, .main-content, main, .prospectus-card, .main-card, .content-side { position: relative !important; z-index: 2 !important; }\n";
}

$video_bg_html = '';

if ( 'solid' === $csmm_bg_type ) {
	$dynamic_css .= "html { background-image: none !important; }\n";
	$dynamic_css .= "body, .s-home, main.s-home, section.s-home, #home, .template-one, #particles-js, .home-particles, #bg, .bg-image, .bg-container { background: {$csmm_bg_solid_color} !important; background-color: {$csmm_bg_solid_color} !important; background-image: none !important; }\n";
} elseif ( 'custom' === $csmm_bg_type ) {
	$custom_img_url = '';
	if ( ! empty( $csmm_bg_custom_images ) && ! empty( $csmm_bg_custom_images[0]['url'] ) ) {
		$custom_img_url = $csmm_bg_custom_images[0]['url'];
	} elseif ( ! empty( $csmm_content['slides'] ) && ! empty( $csmm_content['slides'][0]['url'] ) ) {
		$custom_img_url = $csmm_content['slides'][0]['url'];
	}
	if ( ! empty( $custom_img_url ) ) {
		$custom_bg_url = esc_url( $custom_img_url );
		$bg_size_val = 'cover';
		if ( 'contain' === $csmm_bg_image_size ) {
			$bg_size_val = 'contain';
		} elseif ( 'auto' === $csmm_bg_image_size ) {
			$bg_size_val = 'auto';
		} elseif ( 'fill' === $csmm_bg_image_size || 'stretch' === $csmm_bg_image_size ) {
			$bg_size_val = '100% 100%';
		}
		$dynamic_css .= "html { background-image: none !important; background: transparent !important; }\n";
		$dynamic_css .= "body, .s-home, main.s-home, section.s-home, #home, .template-one, #particles-js, .home-particles, #bg, .bg-image { background-image: url('{$custom_bg_url}') !important; background-size: {$bg_size_val} !important; background-position: center center !important; background-repeat: no-repeat !important; background-attachment: fixed !important; }\n";
	}
}

// Mobile Background Override
if ( $csmm_bg_mobile_enabled && ! empty( $csmm_bg_mobile_image_url ) ) {
	$mob_url = esc_url( $csmm_bg_mobile_image_url );
	$dynamic_css .= "@media (max-width: 768px) { body, .s-home, main.s-home, #particles-js, .home-particles, #bg, .bg-image { background-image: url('{$mob_url}') !important; background-size: cover !important; background-position: center center !important; } }\n";
}

// Background Blur
if ( $csmm_bg_blur > 0 && 'default' !== $csmm_bg_type ) {
	$dynamic_css .= ".home-content { backdrop-filter: blur({$csmm_bg_blur}px); -webkit-backdrop-filter: blur({$csmm_bg_blur}px); }\n";
}

// Custom CSS user block
if ( ! empty( $csmm_custom_css ) ) {
	$dynamic_css .= $csmm_custom_css . "\n";
}

$dynamic_css .= "</style>\n";

// Dynamic Overlay HTML if enabled
$overlay_html = '';
if ( 'default' !== $csmm_bg_type && 'none' !== $csmm_bg_overlay_type && $csmm_bg_overlay_opacity > 0 ) {
	$overlay_html = '<div class="csmm-dynamic-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: ' . esc_attr( $csmm_bg_overlay_color ) . '; opacity: ' . floatval( $csmm_bg_overlay_opacity ) . '; pointer-events: none; z-index: 1;"></div>' . "\n";
}

// Combine video background and overlay
if ( ! empty( $video_bg_html ) ) {
	$overlay_html = $video_bg_html . $overlay_html;
}

// 6. Floating Toast Notification & AJAX Subscriber Script
$subscribe_api_url = esc_url_raw( rest_url( 'csmm/v1/subscribe' ) );
$toast_and_ajax_html = '
<!-- CSMM Floating Toast Notification -->
<div id="csmm-floating-toast" style="position: fixed; bottom: 28px; right: 28px; z-index: 999999; display: flex; align-items: center; gap: 14px; background: #0f172a; color: #ffffff; padding: 14px 22px; border-radius: 12px; box-shadow: 0 12px 36px rgba(0,0,0,0.45); border: 1px solid rgba(255,255,255,0.15); font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; opacity: 0; transform: translateY(24px) scale(0.95); transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); pointer-events: none; max-width: 90vw;">
  <div id="csmm-toast-icon" style="width: 26px; height: 26px; border-radius: 50%; background: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 800; flex-shrink: 0;">✓</div>
  <div id="csmm-toast-msg" style="line-height: 1.4;">' . esc_html__( 'Thank you! You have been successfully subscribed.', 'coming-soon-maintenance-mode' ) . '</div>
</div>

<script id="csmm-ajax-subscribe-script">
(function() {
  function showCsmmToast(msg, isError) {
    var toast = document.getElementById("csmm-floating-toast");
    var toastMsg = document.getElementById("csmm-toast-msg");
    var toastIcon = document.getElementById("csmm-toast-icon");
    if (!toast || !toastMsg) return;
    
    toastMsg.textContent = msg || (isError ? "Subscription failed. Please try again." : "Thank you! You have been successfully subscribed.");
    if (isError) {
      toastIcon.style.background = "#ef4444";
      toastIcon.textContent = "✕";
    } else {
      toastIcon.style.background = "#10b981";
      toastIcon.textContent = "✓";
    }
    
    toast.style.opacity = "1";
    toast.style.transform = "translateY(0) scale(1)";
    toast.style.pointerEvents = "auto";
    
    if (window._csmmToastTimer) clearTimeout(window._csmmToastTimer);
    window._csmmToastTimer = setTimeout(function() {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(24px) scale(0.95)";
      toast.style.pointerEvents = "none";
    }, 5000);
  }

  function initCsmmAjaxForms() {
    var forms = document.querySelectorAll("#mc-form, form.group, .home-content__subscribe form, form.subscribe-form");
    if (!forms.length) {
      forms = document.querySelectorAll("form");
    }

    forms.forEach(function(form) {
      if (form.getAttribute("data-csmm-bound")) return;
      form.setAttribute("data-csmm-bound", "true");

      var emailInput = form.querySelector("input[type=\'email\'], input#csmm-email, input[name=\'csmm-email\']");
      if (!emailInput) return;

      form.addEventListener("submit", function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (form.getAttribute("data-csmm-submitting") === "true") {
          return false;
        }

        var emailVal = (emailInput.value || "").trim();
        if (!emailVal || !emailVal.includes("@")) {
          showCsmmToast("Please enter a valid email address.", true);
          return false;
        }

        var submitBtn = form.querySelector("input[type=\'submit\'], button[type=\'submit\'], input[name=\'subscribe\'], button");
        var origBtnText = submitBtn ? (submitBtn.value || submitBtn.textContent) : "";

        // Lock form & disable all inputs and buttons
        form.setAttribute("data-csmm-submitting", "true");
        var formControls = form.querySelectorAll("input, button, select, textarea");
        formControls.forEach(function(el) {
          el.disabled = true;
          el.setAttribute("disabled", "disabled");
          el.style.pointerEvents = "none";
          el.style.cursor = "not-allowed";
        });

        if (emailInput) {
          emailInput.style.opacity = "0.7";
        }

        if (submitBtn) {
          submitBtn.style.opacity = "0.75";
          if (submitBtn.tagName === "INPUT") submitBtn.value = "Subscribing...";
          else submitBtn.textContent = "Subscribing...";
        }

        fetch("' . $subscribe_api_url . '", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            email: emailVal,
            referer: window.location.href
          })
        })
        .then(function(res) {
          return res.json().then(function(data) {
            return { ok: res.ok, data: data };
          });
        })
        .then(function(result) {
          if (result.ok && result.data && result.data.success) {
            showCsmmToast(result.data.message || "Thank you! You have been successfully subscribed.", false);
            emailInput.value = "";
          } else {
            var err = (result.data && result.data.message) ? result.data.message : "You are already subscribed or could not subscribe.";
            showCsmmToast(err, true);
          }
        })
        .catch(function(err) {
          showCsmmToast("Subscription request failed. Please check your connection.", true);
        })
        .finally(function() {
          form.removeAttribute("data-csmm-submitting");
          var formControls = form.querySelectorAll("input, button, select, textarea");
          formControls.forEach(function(el) {
            el.disabled = false;
            el.removeAttribute("disabled");
            el.style.pointerEvents = "";
            el.style.cursor = "";
          });

          if (emailInput) {
            emailInput.style.opacity = "";
          }

          if (submitBtn) {
            submitBtn.style.opacity = "";
            if (submitBtn.tagName === "INPUT") submitBtn.value = origBtnText;
            else submitBtn.textContent = origBtnText;
          }
        });

        return false;
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCsmmAjaxForms);
  } else {
    initCsmmAjaxForms();
  }
})();
</script>
';

// Universal Pure Vanilla JS Real-Time Countdown Engine for all 36 Templates
if ( '1' === strval( $csmm_countdown ) ) {
	$toast_and_ajax_html .= '
<!-- CSMM Universal Pure Vanilla JS Real-Time Countdown Engine -->
<script id="csmm-universal-countdown-engine">
(function() {
  var countdownDateStr = ' . json_encode( $csmm_countdown_date ) . ';
  var countdownTimeStr = ' . json_encode( $csmm_countdown_time ) . ';
  if (!countdownDateStr) return;

  var targetTimestamp = 0;
  var dParts = countdownDateStr.split("-");
  var tParts = (countdownTimeStr || "00:00").split(":");
  if (dParts.length === 3) {
    var targetDate = new Date(
      parseInt(dParts[0], 10),
      parseInt(dParts[1], 10) - 1,
      parseInt(dParts[2], 10),
      parseInt(tParts[0] || 0, 10),
      parseInt(tParts[1] || 0, 10),
      0
    );
    targetTimestamp = targetDate.getTime();
  }
  if (!targetTimestamp || targetTimestamp <= Date.now()) {
    targetTimestamp = Date.now() + (30 * 24 * 60 * 60 * 1000);
  }

  function pad(n) {
    return n < 10 ? "0" + n : String(n);
  }

  function tick() {
    var now = Date.now();
    var diff = targetTimestamp - now;

    var days = 0, hours = 0, minutes = 0, seconds = 0;
    var isFinished = false;

    if (diff > 0) {
      days = Math.floor(diff / (1000 * 60 * 60 * 24));
      hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      seconds = Math.floor((diff % (1000 * 60)) / 1000);
    } else {
      isFinished = true;
    }

    var strDays = pad(days);
    var strHours = pad(hours);
    var strMins = pad(minutes);
    var strSecs = pad(seconds);

    // 1. Update elements with specific IDs or classes (Templates 2, 17-36)
    var elDays = document.getElementById("days");
    var elHours = document.getElementById("hours");
    var elMins = document.getElementById("minutes");
    var elSecs = document.getElementById("seconds");

    function updateText(el, val) {
      if (el && el.textContent !== val) {
        el.textContent = val;
      }
    }

    updateText(elDays, strDays);
    updateText(elHours, strHours);
    updateText(elMins, strMins);
    updateText(elSecs, strSecs);

    // 2. Update .home-content__clock (Templates 1-16)
    var clocks = document.querySelectorAll(".home-content__clock");
    clocks.forEach(function(clock) {
      var dEl = clock.querySelector(".time.days, .days");
      var hEl = clock.querySelector(".time.hours, .hours");
      var mEl = clock.querySelector(".time.minutes, .minutes");
      var sEl = clock.querySelector(".time.seconds, .seconds");

      function setClockUnit(el, val, defaultLabel) {
        if (!el) return;
        var span = el.querySelector("span");
        var lbl = (span && span.textContent) ? span.textContent.trim() : defaultLabel;
        var newHtml = val + " <span>" + lbl + "</span>";
        if (el.innerHTML !== newHtml) {
          el.innerHTML = newHtml;
        }
      }

      setClockUnit(dEl, strDays, "D");
      setClockUnit(hEl, strHours, "H");
      setClockUnit(mEl, strMins, "M");
      setClockUnit(sEl, strSecs, "S");
    });

    if (isFinished) {
      if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
      }
    }
  }

  var timerInterval = null;
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function() {
      tick();
      if (!timerInterval) timerInterval = setInterval(tick, 1000);
    });
  } else {
    tick();
    if (!timerInterval) timerInterval = setInterval(tick, 1000);
  }
})();
</script>';
}

// 7. Inject Dynamic CSS, Overlay, Toast and SEO Meta into output HTML
if ( preg_match( '/<\/head>/i', $html ) ) {
	$html = preg_replace( '/<\/head>/i', $dynamic_css . '</head>', $html, 1 );
}
if ( ! empty( $overlay_html ) && preg_match( '/<body[^>]*>/i', $html ) ) {
	$html = preg_replace( '/(<body[^>]*>)/i', '$1' . "\n" . $overlay_html, $html, 1 );
}
if ( preg_match( '/<\/body>/i', $html ) ) {
	$html = preg_replace( '/<\/body>/i', $toast_and_ajax_html . '</body>', $html, 1 );
} else {
	$html .= $toast_and_ajax_html;
}

// 8. Inject SEO & Social meta tags into <head>
ob_start();
CSMM_SEO::render_meta_tags( $csmm_content, $csmm_settings, $csmm_seo );
$seo_meta = ob_get_clean();

if ( preg_match( '/<head[^>]*>/i', $html ) ) {
	$html = preg_replace( '/(<head[^>]*>)/i', '$1' . "\n" . $seo_meta, $html, 1 );
}

echo $html;