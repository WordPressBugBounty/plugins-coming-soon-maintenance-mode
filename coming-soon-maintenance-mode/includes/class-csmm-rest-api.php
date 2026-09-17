<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress REST API Controller for CSMM.
 */
class CSMM_REST_API {

	const NAMESPACE = 'csmm/v1';

	/**
	 * Register REST routes.
	 */
	public function register_routes() {
		// GET & POST Settings
		register_rest_route(
			self::NAMESPACE,
			'/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'admin_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => array( $this, 'admin_permissions_check' ),
				),
			)
		);

		// Templates List
		register_rest_route(
			self::NAMESPACE,
			'/templates',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_templates' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
			)
		);

		// Posts and Pages list for selective targeting
		register_rest_route(
			self::NAMESPACE,
			'/target-items',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_target_items' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
			)
		);

		// Import Plugin Settings
		register_rest_route(
			self::NAMESPACE,
			'/import-settings',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'import_settings' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
			)
		);

		// Factory Reset Settings
		register_rest_route(
			self::NAMESPACE,
			'/reset-settings',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'reset_settings' ),
				'permission_callback' => array( $this, 'admin_permissions_check' ),
			)
		);
	}

	/**
	 * Permission check for admin management.
	 */
	public function admin_permissions_check() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get full settings data.
	 */
	public function get_settings() {
		$has_v120    = false !== get_option( 'comisoma_settings' ) || false !== get_option( 'comisoma_content' );
		$is_migrated = get_option( 'csmm_v120_migrated' );
		if ( $has_v120 && ! $is_migrated ) {
			if ( class_exists( 'CSMM_Activator' ) ) {
				CSMM_Activator::migrate_v120_options( true );
			}
		}
		$settings = get_option( 'csmm_settings', array() );

		$templates    = get_option( 'csmm_templates', array() );
		$content      = get_option( 'csmm_content', array() );
		$social_media = get_option( 'csmm_social_media', array() );
		$seo          = get_option( 'csmm_seo', array() );
		$integrations = get_option( 'csmm_integrations', array() );

		// Defaults
		$website_mode = isset( $settings['website_mode'] ) ? intval( $settings['website_mode'] ) : 3;
		$template_id  = isset( $templates['template_id'] ) ? intval( $templates['template_id'] ) : 1;
		if ( ! in_array( $template_id, array( 1, 4, 8, 11, 15 ), true ) ) {
			$template_id = 1;
		}

		$logo_id  = isset( $content['logo'] ) && '' !== $content['logo'] ? $content['logo'] : '';
		$logo_url = '';
		if ( ! empty( $logo_id ) && is_numeric( $logo_id ) ) {
			$img_src = wp_get_attachment_image_src( intval( $logo_id ), 'medium', false );
			if ( $img_src ) {
				$logo_url = $img_src[0];
			}
		}

		if ( empty( $logo_url ) ) {
			$default_logo = in_array( intval( $template_id ), array( 4, 8 ), true ) ? 'logo-b.png' : 'logo-w.png';
			$logo_url     = CSMM_URL . 'templates/images/' . $default_logo;
		}

		// Parse slides
		$slides_data = array();
		if ( ! empty( $content['slide_ids'] ) ) {
			$slide_ids = $content['slide_ids'];
			if ( is_string( $slide_ids ) ) {
				parse_str( urldecode_deep( $slide_ids ), $parsed );
				$slide_ids = isset( $parsed['csmm-slide-id'] ) ? $parsed['csmm-slide-id'] : array();
			}
			if ( is_array( $slide_ids ) ) {
				foreach ( $slide_ids as $sid ) {
					$sid = intval( $sid );
					if ( $sid > 0 ) {
						$src = wp_get_attachment_image_src( $sid, 'medium', true );
						if ( $src ) {
							$slides_data[] = array(
								'id'  => $sid,
								'url' => $src[0],
							);
						}
					}
				}
			}
		}

		$current_date   = date( 'Y-m-d' );
		$countdown_date = ( ! empty( $content['countdown_date'] ) && strtotime( $content['countdown_date'] ) > time() ) ? $content['countdown_date'] : date( 'Y-m-d', strtotime( $current_date . ' +30 days' ) );

		$response = array(
			'website_mode'         => $website_mode,
			'selected_posts'       => isset( $settings['selected_posts'] ) && is_array( $settings['selected_posts'] ) ? array_map( 'intval', $settings['selected_posts'] ) : array(),
			'selected_pages'       => isset( $settings['selected_pages'] ) && is_array( $settings['selected_pages'] ) ? array_map( 'intval', $settings['selected_pages'] ) : array(),
			'selected_other_pages' => isset( $settings['selected_other_pages'] ) && is_array( $settings['selected_other_pages'] ) ? $settings['selected_other_pages'] : array(),
			'template_id'          => $template_id,
			'logo'                 => $logo_id,
			'logo_url'             => $logo_url,
			'logo_enabled'         => isset( $content['logo_enabled'] ) ? strval( $content['logo_enabled'] ) : ( ( isset( $content['logo_type'] ) && $content['logo_type'] === 'disabled' ) ? '0' : '1' ),
			'logo_type'            => isset( $content['logo_type'] ) ? $content['logo_type'] : 'graphic',
			'logo_text'            => ( isset( $content['logo_text'] ) && '' !== $content['logo_text'] ) ? $content['logo_text'] : ( ( isset( $content['title'] ) && '' !== $content['title'] ) ? $content['title'] : get_bloginfo( 'name' ) ),
			'logo_link'            => ( isset( $content['logo_link'] ) && '' !== $content['logo_link'] ) ? $content['logo_link'] : home_url( '/' ),
			'logo_height_enabled'  => ! empty( $content['logo_height_enabled'] ),
			'logo_height'          => isset( $content['logo_height'] ) ? intval( $content['logo_height'] ) : 100,
			'title_enabled'                => isset( $content['title_enabled'] ) ? strval( $content['title_enabled'] ) : '1',
			'title'                        => isset( $content['title'] ) ? $content['title'] : 'Coming Soon',
			'title_font_size_enabled'      => ! empty( $content['title_font_size_enabled'] ),
			'title_font_size'              => isset( $content['title_font_size'] ) ? intval( $content['title_font_size'] ) : 48,
			'title_color'                  => isset( $content['title_color'] ) ? $content['title_color'] : '',
			'description_enabled'          => isset( $content['description_enabled'] ) ? strval( $content['description_enabled'] ) : '1',
			'description'                  => isset( $content['description'] ) ? $content['description'] : '',
			'description_font_size_enabled'=> ! empty( $content['description_font_size_enabled'] ),
			'description_font_size'        => isset( $content['description_font_size'] ) ? intval( $content['description_font_size'] ) : 18,
			'description_color'            => isset( $content['description_color'] ) ? $content['description_color'] : '',
			'countdown'                    => isset( $content['countdown'] ) ? strval( $content['countdown'] ) : '1',
			'countdown_title'              => isset( $content['countdown_title'] ) ? $content['countdown_title'] : 'Launching In...',
			'countdown_date'               => $countdown_date,
			'countdown_time'               => isset( $content['countdown_time'] ) ? $content['countdown_time'] : '10:00',
			'countdown_override_enabled'   => ! empty( $content['countdown_override_enabled'] ),
			'countdown_digit_font_size'    => isset( $content['countdown_digit_font_size'] ) ? intval( $content['countdown_digit_font_size'] ) : 48,
			'countdown_digit_color'        => isset( $content['countdown_digit_color'] ) ? $content['countdown_digit_color'] : '',
			'countdown_label_font_size'    => isset( $content['countdown_label_font_size'] ) ? intval( $content['countdown_label_font_size'] ) : 14,
			'countdown_label_color'        => isset( $content['countdown_label_color'] ) ? $content['countdown_label_color'] : '',
			'countdown_box_bg'             => isset( $content['countdown_box_bg'] ) ? $content['countdown_box_bg'] : '',
			'susbcriber_form'              => '0', // Email Lead Capture is a Pro feature (disabled in Free)
			'form_headline_text'           => isset( $content['form_headline_text'] ) ? $content['form_headline_text'] : '',
			'form_placeholder_text'        => isset( $content['form_placeholder_text'] ) ? $content['form_placeholder_text'] : 'Email Address',
			'form_btn_text'                => isset( $content['form_btn_text'] ) ? $content['form_btn_text'] : 'Notify Me',
			'form_input_bg'                => isset( $content['form_input_bg'] ) ? $content['form_input_bg'] : 'rgba(0, 0, 0, 0.7)',
			'form_input_color'             => isset( $content['form_input_color'] ) && '' !== $content['form_input_color'] ? $content['form_input_color'] : '#FFFFFF',
			'form_btn_bg'                  => isset( $content['form_btn_bg'] ) && '' !== $content['form_btn_bg'] ? $content['form_btn_bg'] : '#e11d48',
			'form_btn_color'               => isset( $content['form_btn_color'] ) && '' !== $content['form_btn_color'] ? $content['form_btn_color'] : '#FFFFFF',
			'form_border_radius'   => isset( $content['form_border_radius'] ) ? intval( $content['form_border_radius'] ) : 0,
			'video_url'            => isset( $content['video_url'] ) ? $content['video_url'] : '',
			'custom_css'           => '', // Custom CSS Overrides is a Pro feature (disabled in Free)
			'slides'               => $slides_data,
			'bg_type'              => isset( $content['bg_type'] ) && in_array( $content['bg_type'], array( 'default', 'custom', 'solid' ), true ) ? $content['bg_type'] : 'default',
			'bg_custom_images'     => isset( $content['bg_custom_images'] ) && is_array( $content['bg_custom_images'] ) ? $content['bg_custom_images'] : ( ! empty( $slides_data ) ? $slides_data : array() ),
			'bg_image_size'        => isset( $content['bg_image_size'] ) ? $content['bg_image_size'] : 'cover',
			'bg_slideshow_images'  => isset( $content['bg_slideshow_images'] ) && is_array( $content['bg_slideshow_images'] ) ? $content['bg_slideshow_images'] : ( ! empty( $slides_data ) ? $slides_data : array() ),
			'bg_slideshow_speed'   => isset( $content['bg_slideshow_speed'] ) ? intval( $content['bg_slideshow_speed'] ) : 5,
			'bg_slideshow_animation' => isset( $content['bg_slideshow_animation'] ) ? $content['bg_slideshow_animation'] : 'fade',
			'bg_slideshow_scale'   => isset( $content['bg_slideshow_scale'] ) ? $content['bg_slideshow_scale'] : 'cover',
			'bg_mobile_enabled'    => ! empty( $content['bg_mobile_enabled'] ),
			'bg_mobile_image_url'  => isset( $content['bg_mobile_image_url'] ) ? $content['bg_mobile_image_url'] : '',
			'bg_video_source'      => isset( $content['bg_video_source'] ) ? $content['bg_video_source'] : 'youtube',
			'bg_video_url'         => isset( $content['bg_video_url'] ) ? $content['bg_video_url'] : ( isset( $content['video_url'] ) ? $content['video_url'] : 'https://www.youtube.com/watch?v=KLuTLF3x9sA' ),
			'bg_video_youtube_url' => isset( $content['bg_video_youtube_url'] ) && ! empty( $content['bg_video_youtube_url'] ) && false !== strpos( $content['bg_video_youtube_url'], 'youtu' )
				? $content['bg_video_youtube_url']
				: ( ( ! isset( $content['bg_video_source'] ) || 'youtube' === $content['bg_video_source'] ) && ! empty( $content['bg_video_url'] ) && false !== strpos( $content['bg_video_url'], 'youtu' )
					? $content['bg_video_url']
					: ( isset( $content['video_url'] ) && ! empty( $content['video_url'] ) && false !== strpos( $content['video_url'], 'youtu' )
						? $content['video_url']
						: 'https://www.youtube.com/watch?v=KLuTLF3x9sA' ) ),
			'bg_video_vimeo_url'   => isset( $content['bg_video_vimeo_url'] ) && ! empty( $content['bg_video_vimeo_url'] ) && false !== strpos( $content['bg_video_vimeo_url'], 'vimeo' )
				? $content['bg_video_vimeo_url']
				: ( isset( $content['bg_video_source'] ) && 'vimeo' === $content['bg_video_source'] && ! empty( $content['bg_video_url'] ) && false !== strpos( $content['bg_video_url'], 'vimeo' )
					? $content['bg_video_url']
					: 'https://vimeo.com/1178283333' ),
			'bg_video_mp4_url'     => isset( $content['bg_video_mp4_url'] ) && ! empty( $content['bg_video_mp4_url'] ) && false !== strpos( $content['bg_video_mp4_url'], '.mp4' )
				? $content['bg_video_mp4_url']
				: ( isset( $content['bg_video_source'] ) && in_array( $content['bg_video_source'], array( 'file', 'mp4' ), true ) && ! empty( $content['bg_video_url'] ) && false !== strpos( $content['bg_video_url'], '.mp4' )
					? $content['bg_video_url']
					: 'https://wpfrank.com/wp-content/uploads/2026/09/coming-soon-maintenance-mode-pro-default-video.mp4' ),
			'bg_video_poster_url'  => isset( $content['bg_video_poster_url'] ) ? $content['bg_video_poster_url'] : '',
			'bg_video_loop'        => ! isset( $content['bg_video_loop'] ) || ! empty( $content['bg_video_loop'] ),
			'bg_pattern'                => isset( $content['bg_pattern'] ) ? $content['bg_pattern'] : 'lines',
			'bg_custom_pattern_url'     => isset( $content['bg_custom_pattern_url'] ) ? $content['bg_custom_pattern_url'] : '',
			'bg_custom_pattern_size'    => isset( $content['bg_custom_pattern_size'] ) ? intval( $content['bg_custom_pattern_size'] ) : 60,
			'bg_custom_pattern_repeat'  => isset( $content['bg_custom_pattern_repeat'] ) ? $content['bg_custom_pattern_repeat'] : 'repeat',
			'bg_custom_pattern_bg'      => isset( $content['bg_custom_pattern_bg'] ) ? $content['bg_custom_pattern_bg'] : '#0b1120',
			'bg_solid_color'            => isset( $content['bg_solid_color'] ) ? $content['bg_solid_color'] : '#1d1b1b',
			'bg_gradient_type'     => isset( $content['bg_gradient_type'] ) ? $content['bg_gradient_type'] : 'linear',
			'bg_gradient_color1'   => isset( $content['bg_gradient_color1'] ) ? $content['bg_gradient_color1'] : '#1e3a8a',
			'bg_gradient_color2'   => isset( $content['bg_gradient_color2'] ) ? $content['bg_gradient_color2'] : '#0f172a',
			'bg_gradient_angle'    => isset( $content['bg_gradient_angle'] ) ? intval( $content['bg_gradient_angle'] ) : 135,
			'bg_overlay_type'      => isset( $content['bg_overlay_type'] ) ? $content['bg_overlay_type'] : 'solid',
			'bg_overlay_color'     => isset( $content['bg_overlay_color'] ) ? $content['bg_overlay_color'] : '#000000',
			'bg_overlay_opacity'   => isset( $content['bg_overlay_opacity'] ) ? floatval( $content['bg_overlay_opacity'] ) : 0,
			'bg_blur'              => isset( $content['bg_blur'] ) ? intval( $content['bg_blur'] ) : 0,
			'social_media'         => array(
				'enabled'   => ! isset( $social_media['enabled'] ) || ! empty( $social_media['enabled'] ),
				'facebook'  => isset( $social_media['csmm_sm_facebook'] ) ? $social_media['csmm_sm_facebook'] : '#',
				'twitter'   => isset( $social_media['csmm_sm_twitter'] ) ? $social_media['csmm_sm_twitter'] : '#',
				'instagram' => isset( $social_media['csmm_sm_instagram'] ) ? $social_media['csmm_sm_instagram'] : '#',
			),
			'seo'                  => array(
				'meta_title'          => isset( $seo['meta_title'] ) ? $seo['meta_title'] : '',
				'meta_description'    => isset( $seo['meta_description'] ) ? $seo['meta_description'] : '',
				'robots_meta'         => isset( $seo['robots_meta'] ) ? $seo['robots_meta'] : 'auto',
				'google_analytics_id' => isset( $seo['google_analytics_id'] ) ? $seo['google_analytics_id'] : '',
				'og_image_id'         => isset( $seo['og_image_id'] ) ? $seo['og_image_id'] : '',
			),
			'integrations'         => array(
				'mailchimp_enabled'      => ! empty( $integrations['mailchimp_enabled'] ),
				'mailchimp_api_key'      => isset( $integrations['mailchimp_api_key'] ) ? $integrations['mailchimp_api_key'] : '',
				'mailchimp_list_id'      => isset( $integrations['mailchimp_list_id'] ) ? $integrations['mailchimp_list_id'] : '',
				'brevo_enabled'          => ! empty( $integrations['brevo_enabled'] ),
				'brevo_api_key'          => isset( $integrations['brevo_api_key'] ) ? $integrations['brevo_api_key'] : '',
				'brevo_list_id'          => isset( $integrations['brevo_list_id'] ) ? $integrations['brevo_list_id'] : '',
				'mailerlite_enabled'     => ! empty( $integrations['mailerlite_enabled'] ),
				'mailerlite_api_key'     => isset( $integrations['mailerlite_api_key'] ) ? $integrations['mailerlite_api_key'] : '',
				'mailerlite_group_id'    => isset( $integrations['mailerlite_group_id'] ) ? $integrations['mailerlite_group_id'] : '',
				'webhook_enabled'        => ! empty( $integrations['webhook_enabled'] ),
				'webhook_url'            => isset( $integrations['webhook_url'] ) ? $integrations['webhook_url'] : '',
				'smtp_enabled'           => ! empty( $integrations['smtp_enabled'] ),
				'smtp_host'              => isset( $integrations['smtp_host'] ) ? $integrations['smtp_host'] : '',
				'smtp_port'              => isset( $integrations['smtp_port'] ) ? $integrations['smtp_port'] : '587',
				'smtp_encryption'        => isset( $integrations['smtp_encryption'] ) ? $integrations['smtp_encryption'] : 'tls',
				'smtp_username'          => isset( $integrations['smtp_username'] ) ? $integrations['smtp_username'] : '',
				'smtp_password'          => isset( $integrations['smtp_password'] ) ? $integrations['smtp_password'] : '',
				'smtp_from_email'        => isset( $integrations['smtp_from_email'] ) ? $integrations['smtp_from_email'] : get_bloginfo( 'admin_email' ),
				'smtp_from_name'         => isset( $integrations['smtp_from_name'] ) ? $integrations['smtp_from_name'] : get_bloginfo( 'name' ),
				'admin_email_enabled'    => ! empty( $integrations['admin_email_enabled'] ),
				'admin_email_recipient'  => isset( $integrations['admin_email_recipient'] ) ? $integrations['admin_email_recipient'] : get_bloginfo( 'admin_email' ),
				'admin_email_subject'    => isset( $integrations['admin_email_subject'] ) ? $integrations['admin_email_subject'] : 'New Subscriber Lead Captured on {site_name} 🎉',
				'admin_email_body'       => isset( $integrations['admin_email_body'] ) ? $integrations['admin_email_body'] : "<h2>New Subscriber Lead!</h2>\n<p>A new visitor has subscribed to your Coming Soon newsletter:</p>\n<p><strong>Email:</strong> {subscriber_email}<br><strong>IP Address:</strong> {ip_address}<br><strong>Date:</strong> {date}</p>",
				'welcome_email_enabled'  => ! empty( $integrations['welcome_email_enabled'] ),
				'welcome_email_subject'  => isset( $integrations['welcome_email_subject'] ) ? $integrations['welcome_email_subject'] : 'Thank you for subscribing to {site_name}! 🚀',
				'welcome_email_body'     => isset( $integrations['welcome_email_body'] ) ? $integrations['welcome_email_body'] : "<h2>Welcome to {site_name}!</h2>\n<p>Hi there,</p>\n<p>Thank you for subscribing to our newsletter! We are currently working hard behind the scenes to launch our brand new website.</p>\n<p>You'll be the very first to know when we go live on <strong>{launch_date}</strong>!</p>\n<p>Best regards,<br>The {site_name} Team</p>",
				'launch_email_enabled'   => ! empty( $integrations['launch_email_enabled'] ),
				'launch_email_subject'   => isset( $integrations['launch_email_subject'] ) ? $integrations['launch_email_subject'] : 'We are officially LIVE! 🚀 Welcome to {site_name}',
				'launch_email_body'      => isset( $integrations['launch_email_body'] ) ? $integrations['launch_email_body'] : "<h2>We Are Officially Live! 🎉</h2>\n<p>Hi there,</p>\n<p>The wait is finally over! We have officially launched our brand new website, and you are the first to know.</p>\n<p>Discover our latest features, products, and exclusive offers right now.</p>\n<p style=\"text-align: center; margin: 30px 0;\"><a href=\"{site_url}\" style=\"background-color: #2563eb; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: 700; display: inline-block;\">Start Exploring Now 🚀</a></p>\n<p>Thank you for being part of our early journey!</p>\n<p>Best regards,<br>The {site_name} Team</p>",
				'email_header_title'     => isset( $integrations['email_header_title'] ) ? $integrations['email_header_title'] : '{site_name}',
				'email_header_bg'        => isset( $integrations['email_header_bg'] ) ? $integrations['email_header_bg'] : '#2563eb',
				'email_header_color'     => isset( $integrations['email_header_color'] ) ? $integrations['email_header_color'] : '#ffffff',
				'email_bg_color'         => isset( $integrations['email_bg_color'] ) ? $integrations['email_bg_color'] : '#f8fafc',
				'email_card_bg'          => isset( $integrations['email_card_bg'] ) ? $integrations['email_card_bg'] : '#ffffff',
				'email_text_color'       => isset( $integrations['email_text_color'] ) ? $integrations['email_text_color'] : '#1e293b',
				'email_footer_text'      => isset( $integrations['email_footer_text'] ) ? $integrations['email_footer_text'] : '',
				'email_footer_bg'        => isset( $integrations['email_footer_bg'] ) ? $integrations['email_footer_bg'] : '#f1f5f9',
				'email_footer_color'     => isset( $integrations['email_footer_color'] ) ? $integrations['email_footer_color'] : '#64748b',
			),
			'preview_url'          => add_query_arg( 'csmm', 'true', home_url( '/' ) ),
			'site_url'             => home_url(),
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Save settings handler.
	 *
	 * @param WP_REST_Request $request
	 */
	public function save_settings( $request ) {
		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_params();
		}

		// 1. Settings (Website mode & targeting)
		if ( isset( $params['website_mode'] ) ) {
			$website_mode         = intval( $params['website_mode'] );
			$selected_posts       = isset( $params['selected_posts'] ) && is_array( $params['selected_posts'] ) ? array_map( 'intval', $params['selected_posts'] ) : array();
			$selected_pages       = isset( $params['selected_pages'] ) && is_array( $params['selected_pages'] ) ? array_map( 'intval', $params['selected_pages'] ) : array();
			$selected_other_pages = isset( $params['selected_other_pages'] ) && is_array( $params['selected_other_pages'] ) ? array_map( 'sanitize_text_field', $params['selected_other_pages'] ) : array();

			$settings_array = array(
				'website_mode'         => $website_mode,
				'selected_posts'       => $selected_posts,
				'selected_pages'       => $selected_pages,
				'selected_other_pages' => $selected_other_pages,
			);
			update_option( 'csmm_settings', $settings_array );
		}

		// 2. Template
		if ( isset( $params['template_id'] ) ) {
			$template_id = intval( $params['template_id'] );
			if ( ! in_array( $template_id, array( 1, 4, 8, 11, 15 ), true ) ) {
				$template_id = 1;
			}
			update_option( 'csmm_templates', array( 'template_id' => $template_id ) );
		}

		// 3. Content
		$content_array = get_option( 'csmm_content', array() );
		if ( isset( $params['title_enabled'] ) ) {
			$content_array['title_enabled'] = sanitize_text_field( $params['title_enabled'] );
		}
		if ( isset( $params['title'] ) ) {
			$content_array['title'] = sanitize_text_field( $params['title'] );
		}
		if ( isset( $params['title_font_size_enabled'] ) ) {
			$content_array['title_font_size_enabled'] = ! empty( $params['title_font_size_enabled'] );
		}
		if ( isset( $params['title_font_size'] ) ) {
			$content_array['title_font_size'] = intval( $params['title_font_size'] );
		}
		if ( isset( $params['title_color'] ) ) {
			$content_array['title_color'] = sanitize_text_field( $params['title_color'] );
		}
		if ( isset( $params['description_enabled'] ) ) {
			$content_array['description_enabled'] = sanitize_text_field( $params['description_enabled'] );
		}
		if ( isset( $params['description'] ) ) {
			if ( current_user_can( 'unfiltered_html' ) ) {
				$content_array['description'] = $params['description'];
			} else {
				$content_array['description'] = wp_kses_post( $params['description'] );
			}
		}
		if ( isset( $params['description_font_size_enabled'] ) ) {
			$content_array['description_font_size_enabled'] = ! empty( $params['description_font_size_enabled'] );
		}
		if ( isset( $params['description_font_size'] ) ) {
			$content_array['description_font_size'] = intval( $params['description_font_size'] );
		}
		if ( isset( $params['description_color'] ) ) {
			$content_array['description_color'] = sanitize_text_field( $params['description_color'] );
		}
		if ( isset( $params['logo_enabled'] ) ) {
			$content_array['logo_enabled'] = sanitize_text_field( $params['logo_enabled'] );
		}
		if ( isset( $params['logo'] ) ) {
			$content_array['logo'] = sanitize_text_field( $params['logo'] );
		}
		if ( isset( $params['logo_type'] ) ) {
			$content_array['logo_type'] = sanitize_text_field( $params['logo_type'] );
		}
		if ( isset( $params['logo_text'] ) ) {
			$content_array['logo_text'] = sanitize_text_field( $params['logo_text'] );
		}
		if ( isset( $params['logo_link'] ) ) {
			$content_array['logo_link'] = esc_url_raw( $params['logo_link'] );
		}
		if ( isset( $params['logo_height_enabled'] ) ) {
			$content_array['logo_height_enabled'] = ! empty( $params['logo_height_enabled'] );
		}
		if ( isset( $params['logo_height'] ) ) {
			$content_array['logo_height'] = intval( $params['logo_height'] );
		}
		if ( isset( $params['countdown'] ) ) {
			$content_array['countdown'] = sanitize_text_field( $params['countdown'] );
		}
		if ( isset( $params['countdown_title'] ) ) {
			$content_array['countdown_title'] = sanitize_text_field( $params['countdown_title'] );
		}
		if ( isset( $params['countdown_date'] ) ) {
			$content_array['countdown_date'] = sanitize_text_field( $params['countdown_date'] );
		}
		if ( isset( $params['countdown_time'] ) ) {
			$content_array['countdown_time'] = sanitize_text_field( $params['countdown_time'] );
		}
		if ( isset( $params['countdown_override_enabled'] ) ) {
			$content_array['countdown_override_enabled'] = ! empty( $params['countdown_override_enabled'] );
		}
		if ( isset( $params['countdown_digit_font_size'] ) ) {
			$content_array['countdown_digit_font_size'] = intval( $params['countdown_digit_font_size'] );
		}
		if ( isset( $params['countdown_digit_color'] ) ) {
			$content_array['countdown_digit_color'] = sanitize_text_field( $params['countdown_digit_color'] );
		}
		if ( isset( $params['countdown_label_font_size'] ) ) {
			$content_array['countdown_label_font_size'] = intval( $params['countdown_label_font_size'] );
		}
		if ( isset( $params['countdown_label_color'] ) ) {
			$content_array['countdown_label_color'] = sanitize_text_field( $params['countdown_label_color'] );
		}
		if ( isset( $params['countdown_box_bg'] ) ) {
			$content_array['countdown_box_bg'] = sanitize_text_field( $params['countdown_box_bg'] );
		}
		if ( isset( $params['susbcriber_form'] ) ) {
			$content_array['susbcriber_form'] = '0'; // Email Lead Capture is a Pro feature
		}
		if ( isset( $params['form_headline_text'] ) ) {
			$content_array['form_headline_text'] = sanitize_text_field( $params['form_headline_text'] );
		}
		if ( isset( $params['form_placeholder_text'] ) ) {
			$content_array['form_placeholder_text'] = sanitize_text_field( $params['form_placeholder_text'] );
		}
		if ( isset( $params['form_btn_text'] ) ) {
			$content_array['form_btn_text'] = sanitize_text_field( $params['form_btn_text'] );
		}
		if ( isset( $params['form_input_bg'] ) ) {
			$content_array['form_input_bg'] = sanitize_text_field( $params['form_input_bg'] );
		}
		if ( isset( $params['form_input_color'] ) ) {
			$content_array['form_input_color'] = sanitize_text_field( $params['form_input_color'] );
		}
		if ( isset( $params['form_btn_bg'] ) ) {
			$content_array['form_btn_bg'] = sanitize_text_field( $params['form_btn_bg'] );
		}
		if ( isset( $params['form_btn_color'] ) ) {
			$content_array['form_btn_color'] = sanitize_text_field( $params['form_btn_color'] );
		}
		if ( isset( $params['form_border_radius'] ) ) {
			$content_array['form_border_radius'] = intval( $params['form_border_radius'] );
		}
		if ( isset( $params['video_url'] ) ) {
			$content_array['video_url'] = esc_url_raw( $params['video_url'] );
		}
		if ( isset( $params['custom_css'] ) ) {
			$content_array['custom_css'] = ''; // Custom CSS is a Pro feature
		}
		if (isset($params['slide_ids'])) {
			$slide_ids = is_array($params['slide_ids']) ? array_map('intval', $params['slide_ids']) : array();
			$content_array['slide_ids'] = $slide_ids;
		}

		// Background Settings (Free supports 'default', 'custom', 'solid')
		if ( isset( $params['bg_type'] ) ) {
			$bg_type = sanitize_text_field( $params['bg_type'] );
			$content_array['bg_type'] = in_array( $bg_type, array( 'default', 'custom', 'solid' ), true ) ? $bg_type : 'default';
		}
		if (isset($params['bg_custom_images']) && is_array($params['bg_custom_images'])) {
			$sanitized_bg_imgs = array();
			foreach ($params['bg_custom_images'] as $img) {
				if (is_array($img) && !empty($img['url'])) {
					$sanitized_bg_imgs[] = array(
						'id'  => isset($img['id']) ? intval($img['id']) : 0,
						'url' => esc_url_raw($img['url']),
					);
				}
			}
			$content_array['bg_custom_images'] = $sanitized_bg_imgs;
			// Sync with slide_ids if available
			$content_array['slide_ids'] = array_column($sanitized_bg_imgs, 'id');
		}
		if (isset($params['bg_image_size'])) {
			$content_array['bg_image_size'] = sanitize_text_field($params['bg_image_size']);
		}
		if ( isset( $params['bg_slideshow_images'] ) && is_array( $params['bg_slideshow_images'] ) ) {
			$sanitized_slideshow = array();
			foreach ( $params['bg_slideshow_images'] as $img ) {
				if ( isset( $img['url'] ) ) {
					$sanitized_slideshow[] = array(
						'id'  => isset( $img['id'] ) ? intval( $img['id'] ) : 0,
						'url' => esc_url_raw( $img['url'] ),
					);
				}
			}
			$content_array['bg_slideshow_images'] = $sanitized_slideshow;
		}
		if ( isset( $params['bg_slideshow_speed'] ) ) {
			$content_array['bg_slideshow_speed'] = max( 2, intval( $params['bg_slideshow_speed'] ) );
		}
		if ( isset( $params['bg_slideshow_animation'] ) ) {
			$content_array['bg_slideshow_animation'] = sanitize_text_field( $params['bg_slideshow_animation'] );
		}
		if ( isset( $params['bg_slideshow_scale'] ) ) {
			$content_array['bg_slideshow_scale'] = sanitize_text_field( $params['bg_slideshow_scale'] );
		}
		if (isset($params['bg_mobile_enabled'])) {
			$content_array['bg_mobile_enabled'] = !empty($params['bg_mobile_enabled']);
		}
		if (isset($params['bg_mobile_image_url'])) {
			$content_array['bg_mobile_image_url'] = esc_url_raw($params['bg_mobile_image_url']);
		}
		if (isset($params['bg_video_source'])) {
			$content_array['bg_video_source'] = sanitize_text_field($params['bg_video_source']);
		}
		if (isset($params['bg_video_youtube_url'])) {
			$content_array['bg_video_youtube_url'] = esc_url_raw($params['bg_video_youtube_url']);
		}
		if (isset($params['bg_video_vimeo_url'])) {
			$content_array['bg_video_vimeo_url'] = esc_url_raw($params['bg_video_vimeo_url']);
		}
		if (isset($params['bg_video_mp4_url'])) {
			$content_array['bg_video_mp4_url'] = esc_url_raw($params['bg_video_mp4_url']);
		}
		if (isset($params['bg_video_url'])) {
			$content_array['bg_video_url'] = esc_url_raw($params['bg_video_url']);
			$content_array['video_url'] = esc_url_raw($params['bg_video_url']);
		}
		if (isset($params['bg_video_loop'])) {
			$content_array['bg_video_loop'] = !empty($params['bg_video_loop']);
		}
		if (isset($params['bg_video_poster_url'])) {
			$content_array['bg_video_poster_url'] = esc_url_raw($params['bg_video_poster_url']);
		}
		if (isset($params['bg_pattern'])) {
			$content_array['bg_pattern'] = sanitize_text_field($params['bg_pattern']);
		}
		if (isset($params['bg_custom_pattern_url'])) {
			$content_array['bg_custom_pattern_url'] = esc_url_raw($params['bg_custom_pattern_url']);
		}
		if (isset($params['bg_custom_pattern_size'])) {
			$content_array['bg_custom_pattern_size'] = intval($params['bg_custom_pattern_size']);
		}
		if (isset($params['bg_custom_pattern_repeat'])) {
			$content_array['bg_custom_pattern_repeat'] = sanitize_text_field($params['bg_custom_pattern_repeat']);
		}
		if (isset($params['bg_custom_pattern_bg'])) {
			$content_array['bg_custom_pattern_bg'] = sanitize_text_field($params['bg_custom_pattern_bg']);
		}
		if (isset($params['bg_solid_color'])) {
			$content_array['bg_solid_color'] = sanitize_hex_color($params['bg_solid_color']) ? sanitize_hex_color($params['bg_solid_color']) : sanitize_text_field($params['bg_solid_color']);
		}
		if (isset($params['bg_gradient_type'])) {
			$content_array['bg_gradient_type'] = sanitize_text_field($params['bg_gradient_type']);
		}
		if (isset($params['bg_gradient_color1'])) {
			$content_array['bg_gradient_color1'] = sanitize_text_field($params['bg_gradient_color1']);
		}
		if (isset($params['bg_gradient_color2'])) {
			$content_array['bg_gradient_color2'] = sanitize_text_field($params['bg_gradient_color2']);
		}
		if (isset($params['bg_gradient_angle'])) {
			$content_array['bg_gradient_angle'] = intval($params['bg_gradient_angle']);
		}
		if (isset($params['bg_overlay_type'])) {
			$content_array['bg_overlay_type'] = sanitize_text_field($params['bg_overlay_type']);
		}
		if (isset($params['bg_overlay_color'])) {
			$content_array['bg_overlay_color'] = sanitize_text_field($params['bg_overlay_color']);
		}
		if (isset($params['bg_overlay_opacity'])) {
			$content_array['bg_overlay_opacity'] = floatval($params['bg_overlay_opacity']);
		}
		if (isset($params['bg_blur'])) {
			$content_array['bg_blur'] = intval($params['bg_blur']);
		}

		update_option('csmm_content', $content_array);

		// 4. Social Media (Free version: Facebook, Twitter / X, Instagram only)
		if ( isset( $params['social_media'] ) && is_array( $params['social_media'] ) ) {
			$sm = $params['social_media'];
			$social_array = array(
				'enabled'           => ! isset( $sm['enabled'] ) || ! empty( $sm['enabled'] ),
				'csmm_sm_facebook'  => isset( $sm['facebook'] ) ? esc_url_raw( $sm['facebook'] ) : '',
				'csmm_sm_twitter'   => isset( $sm['twitter'] ) ? esc_url_raw( $sm['twitter'] ) : '',
				'csmm_sm_instagram' => isset( $sm['instagram'] ) ? esc_url_raw( $sm['instagram'] ) : '',
			);

			update_option( 'csmm_social_media', $social_array );
		}

		// 5. SEO Settings
		if ( isset( $params['seo'] ) && is_array( $params['seo'] ) ) {
			$seo_input = $params['seo'];
			$seo_array = array(
				'meta_title'          => isset( $seo_input['meta_title'] ) ? sanitize_text_field( $seo_input['meta_title'] ) : '',
				'meta_description'    => isset( $seo_input['meta_description'] ) ? sanitize_textarea_field( $seo_input['meta_description'] ) : '',
				'robots_meta'         => isset( $seo_input['robots_meta'] ) ? sanitize_text_field( $seo_input['robots_meta'] ) : 'auto',
				'google_analytics_id' => isset( $seo_input['google_analytics_id'] ) ? sanitize_text_field( $seo_input['google_analytics_id'] ) : '',
				'og_image_id'         => isset( $seo_input['og_image_id'] ) ? sanitize_text_field( $seo_input['og_image_id'] ) : '',
			);
			update_option( 'csmm_seo', $seo_array );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Settings updated successfully!', 'coming-soon-maintenance-mode' ),
			)
		);
	}

	/**
	 * Get list of all available templates.
	 */
	public function get_templates() {
		$templates = array();

		$titles = array(
			1 => 'Minimal Clean Countdown', 2 => 'Dark Modern Agency', 3 => 'Creative Studio Glow',
			4 => 'Geometric Tech Blue', 5 => 'Modern Business Launch', 6 => 'Minimal White Launch',
			7 => 'Cyber Neon Wave', 8 => 'Abstract Vibrant Gradient', 9 => 'Corporate Clean Slate',
			10 => 'Startup Countdown Box', 11 => 'Elegant Luxury Gold', 12 => 'Modern Split Layout',
			13 => 'Sunset Vibrant Purple', 14 => 'Deep Space Starlight', 15 => 'Aurora Borealis Glow',
			16 => 'Alien Shooter', 17 => 'Academy & Courses', 18 => 'Beauty & Spa Salon',
			19 => 'Celebration & Events', 20 => 'Construction & Architecture', 21 => 'Heavy Construction Pro',
			22 => 'Education & University', 23 => 'Festivals & Concerts', 24 => 'Fashion & Boutique',
			25 => 'Food & Restaurant', 26 => 'Future & AI Technology', 27 => 'Gaming & Esports',
			28 => 'Green & Eco Energy', 29 => 'Gym & Fitness Center', 30 => 'Health & Medical Clinic',
			31 => 'Kids & Kindergarten', 32 => 'Podcast & Audio Show', 33 => 'Creative Portfolio',
			34 => 'Real Estate & Properties', 35 => 'Shopping & E-Commerce', 36 => 'Travel & Adventure',
		);

		$img_names = array(
			1 => '1.webp', 2 => '2.webp', 3 => '3.webp', 4 => '4.webp', 5 => '5.webp', 6 => '6.webp',
			7 => '7.webp', 8 => '8.webp', 9 => '9.webp', 10 => '10.webp', 11 => '11.webp', 12 => '12.webp',
			13 => '13.webp', 14 => '14.webp', 15 => '15.webp', 16 => '16.webp', 17 => '17-academy.webp',
			18 => '18-beauty.webp', 19 => '19-Celebrate.webp', 20 => '20-construction.webp', 21 => '21-construction2.webp',
			22 => '22-education.webp', 23 => '23-event.webp', 24 => '24-fashion.webp', 25 => '25-food.webp',
			26 => '26-future.webp', 27 => '27-gaming.webp', 28 => '28-green.webp', 29 => '29-gym.webp',
			30 => '30-health.webp', 31 => '31-kids.webp', 32 => '32-podcast.webp', 33 => '33-portfolio.webp',
			34 => '34-realestate.webp', 35 => '35-shopping.webp', 36 => '36-travel.webp',
		);

		$free_template_ids = array( 1, 4, 8, 11, 15 );

		for ( $i = 1; $i <= 36; $i++ ) {
			$title    = isset( $titles[ $i ] ) ? $titles[ $i ] : "Template #{$i}";
			$img_file = isset( $img_names[ $i ] ) ? $img_names[ $i ] : "{$i}.webp";
			$thumb    = CSMM_URL . "admin/assets/img/{$img_file}";
			$is_free  = in_array( $i, $free_template_ids, true );

			$templates[] = array(
				'id'          => $i,
				'title'       => $title,
				'thumbnail'   => $thumb,
				'is_free'     => $is_free,
				'pro_url'     => 'https://wpfrank.com/wordpress-plugins/coming-soon-maintenance-mode-pro/',
				'preview_url' => $is_free ? add_query_arg(
					array(
						'csmm'             => 'true',
						'template_preview' => $i,
					),
					home_url( '/' )
				) : 'https://wpfrank.com/wordpress-plugins/coming-soon-maintenance-mode-pro/',
			);
		}

		return rest_ensure_response( $templates );
	}

	/**
	 * Get posts and pages for selective targeting.
	 */
	public function get_target_items() {
		$posts_data = array();
		$pages_data = array();

		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 150,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $p ) {
			$posts_data[] = array(
				'id'    => $p->ID,
				'title' => $p->post_title ? $p->post_title : "(no title #{$p->ID})",
			);
		}

		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 150,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $pages as $pg ) {
			$pages_data[] = array(
				'id'    => $pg->ID,
				'title' => $pg->post_title ? $pg->post_title : "(no title #{$pg->ID})",
			);
		}

		return rest_ensure_response(
			array(
				'posts' => $posts_data,
				'pages' => $pages_data,
			)
		);
	}

	/**
	 * Import plugin settings from JSON payload.
	 */
	public function import_settings( $request ) {
		$params = $request->get_json_params();
		if ( empty( $params ) || ! is_array( $params ) ) {
			return new WP_Error( 'invalid_data', __( 'Invalid or empty settings data provided for import.', 'coming-soon-maintenance-mode' ), array( 'status' => 400 ) );
		}

		// Check if payload is from legacy v1.2.0 export
		if ( isset( $params['comisoma_settings'] ) || isset( $params['comisoma_content'] ) || isset( $params['comisoma_templates'] ) || isset( $params['comisoma_social_media'] ) ) {
			CSMM_Activator::migrate_v120_options( true, $params );

			$updated      = $this->get_settings();
			$updated_data = is_a( $updated, 'WP_REST_Response' ) ? $updated->get_data() : $updated;

			return rest_ensure_response(
				array(
					'success' => true,
					'message' => __( 'Legacy v1.2.0 settings imported and migrated successfully!', 'coming-soon-maintenance-mode' ),
					'data'    => $updated_data,
				)
			);
		}

		// Handle payload if wrapped inside "settings" key from export file
		$import_data = isset( $params['settings'] ) && is_array( $params['settings'] ) ? $params['settings'] : $params;

		// Sanitize import payload for Free version
		unset( $import_data['integrations'] );
		unset( $import_data['custom_css'] );
		unset( $import_data['video_url'] );
		unset( $import_data['bg_slideshow_images'] );
		unset( $import_data['bg_video_url'] );
		unset( $import_data['bg_pattern'] );
		unset( $import_data['bg_gradient_type'] );

		if ( isset( $import_data['template_id'] ) ) {
			$tid = intval( $import_data['template_id'] );
			if ( ! in_array( $tid, array( 1, 4, 8, 11, 15 ), true ) ) {
				$import_data['template_id'] = 1;
			}
		}

		// Save imported settings through save_settings logic
		$save_req = new WP_REST_Request( 'POST', '/' . self::NAMESPACE . '/settings' );
		$save_req->set_body_params( $import_data );
		$save_res = $this->save_settings( $save_req );

		if ( is_wp_error( $save_res ) ) {
			return $save_res;
		}

		$updated = $this->get_settings();
		$updated_data = is_a( $updated, 'WP_REST_Response' ) ? $updated->get_data() : $updated;

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Plugin settings imported successfully!', 'coming-soon-maintenance-mode' ),
				'data'    => $updated_data,
			)
		);
	}

	/**
	 * Factory reset all plugin settings to default installation values.
	 */
	public function reset_settings( $request ) {
		delete_option( 'csmm_settings' );
		delete_option( 'csmm_templates' );
		delete_option( 'csmm_content' );
		delete_option( 'csmm_social_media' );
		delete_option( 'csmm_advanced' );
		delete_option( 'csmm_seo' );
		delete_option( 'csmm_integrations' );

		CSMM_Activator::set_default_options();

		$fresh = $this->get_settings();
		$fresh_data = is_a( $fresh, 'WP_REST_Response' ) ? $fresh->get_data() : $fresh;

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'All plugin settings have been reset to factory defaults.', 'coming-soon-maintenance-mode' ),
				'data'    => $fresh_data,
			)
		);
	}
}
