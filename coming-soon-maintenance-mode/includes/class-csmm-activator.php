<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin activation & database migration.
 */
class CSMM_Activator {

	/**
	 * Run activation logic.
	 */
	public static function activate() {
		self::migrate_v120_options( true );
		self::update_version();
		self::create_tables();
		self::migrate_legacy_data();
		self::set_default_options();
	}

	/**
	 * Update version in options.
	 */
	public static function update_version() {
		update_option( 'csmm_current_version', CSMM_VERSION );
	}

	/**
	 * Create custom database tables.
	 */
	public static function create_tables() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'csmm_subscribers';
		$charset_collate = $wpdb->get_charset_collate();

		$suppress = $wpdb->suppress_errors( true );

		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`email` VARCHAR(191) NOT NULL,
			`ip_address` VARCHAR(45) NOT NULL DEFAULT '',
			`referer` VARCHAR(255) NOT NULL DEFAULT '',
			`created_at` DATETIME NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `email` (`email`)
		) {$charset_collate};";

		$wpdb->query( $sql );

		$wpdb->suppress_errors( $suppress );
	}

	/**
	 * Seamlessly migrate legacy subscriber emails from wp_options to custom table.
	 */
	public static function migrate_legacy_data() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'csmm_subscribers';

		// Check if legacy options exist
		$legacy_subscribers = get_option( 'cmss_subscriber_list' );

		if ( is_array( $legacy_subscribers ) && ! empty( $legacy_subscribers ) ) {
			// Flatten if nested
			$flat_emails = array();
			foreach ( $legacy_subscribers as $entry ) {
				if ( is_array( $entry ) && isset( $entry[0] ) ) {
					$email = sanitize_email( $entry[0] );
				} elseif ( is_string( $entry ) ) {
					$email = sanitize_email( $entry );
				} else {
					continue;
				}

				if ( is_email( $email ) ) {
					$flat_emails[] = strtolower( trim( $email ) );
				}
			}

			$flat_emails = array_unique( array_filter( $flat_emails ) );

			if ( ! empty( $flat_emails ) ) {
				$suppress = $wpdb->suppress_errors( true );
				foreach ( $flat_emails as $email ) {
					$wpdb->query(
						$wpdb->prepare(
							"INSERT IGNORE INTO `{$table_name}` (`email`, `ip_address`, `referer`, `created_at`) VALUES (%s, %s, %s, %s)",
							$email,
							'127.0.0.1',
							'legacy_migration',
							current_time( 'mysql' )
						)
					);
				}
				$wpdb->suppress_errors( $suppress );
			}

			update_option( 'csmm_legacy_migrated', true );
		}
	}

	/**
	 * Seamlessly migrate options from v1.2.0 (comisoma_*) to v1.3.0 (csmm_*).
	 *
	 * @param bool       $force Force migration even if already performed.
	 * @param array|null $data  Optional data payload (e.g. from JSON import).
	 */
	public static function migrate_v120_options( $force = false, $data = null ) {
		$is_import = is_array( $data ) && ! empty( $data );

		$old_settings  = $is_import && isset( $data['comisoma_settings'] ) ? $data['comisoma_settings'] : get_option( 'comisoma_settings' );
		$old_templates = $is_import && isset( $data['comisoma_templates'] ) ? $data['comisoma_templates'] : get_option( 'comisoma_templates' );
		$old_content   = $is_import && isset( $data['comisoma_content'] ) ? $data['comisoma_content'] : get_option( 'comisoma_content' );
		$old_sm        = $is_import && isset( $data['comisoma_social_media'] ) ? $data['comisoma_social_media'] : get_option( 'comisoma_social_media' );

		$has_v120 = ! empty( $old_settings ) || ! empty( $old_content ) || ! empty( $old_templates ) || ! empty( $old_sm );

		if ( ! $has_v120 ) {
			return;
		}

		// Prevent duplicate migration if already performed and not forced
		if ( ! $force && ! $is_import && get_option( 'csmm_v120_migrated' ) ) {
			return;
		}

		// 1. Settings (Website Mode & Target Pages)
		if ( is_array( $old_settings ) && ! empty( $old_settings ) ) {
			$new_settings = array(
				'website_mode'         => isset( $old_settings['website_mode'] ) ? intval( $old_settings['website_mode'] ) : 3,
				'selected_posts'       => isset( $old_settings['selected_posts'] ) && is_array( $old_settings['selected_posts'] ) ? array_map( 'intval', $old_settings['selected_posts'] ) : array(),
				'selected_pages'       => isset( $old_settings['selected_pages'] ) && is_array( $old_settings['selected_pages'] ) ? array_map( 'intval', $old_settings['selected_pages'] ) : array(),
				'selected_other_pages' => isset( $old_settings['selected_other_pages'] ) && is_array( $old_settings['selected_other_pages'] ) ? array_map( 'sanitize_text_field', $old_settings['selected_other_pages'] ) : array(),
			);
			update_option( 'csmm_settings', $new_settings );
		}

		// 2. Templates
		if ( is_array( $old_templates ) && ! empty( $old_templates ) ) {
			$template_id = isset( $old_templates['template_id'] ) ? intval( $old_templates['template_id'] ) : 1;
			// In Free v1.3.0, only templates 1, 4, 8, 11, 15 are supported. Fallback to 1 if unsupported.
			if ( ! in_array( $template_id, array( 1, 4, 8, 11, 15 ), true ) ) {
				$template_id = 1;
			}
			update_option( 'csmm_templates', array( 'template_id' => $template_id ) );
		}

		// 3. Content & Branding
		if ( is_array( $old_content ) && ! empty( $old_content ) ) {
			$slide_ids = array();
			if ( ! empty( $old_content['slide_ids'] ) ) {
				if ( is_string( $old_content['slide_ids'] ) ) {
					parse_str( urldecode_deep( $old_content['slide_ids'] ), $parsed );
					$slide_ids = isset( $parsed['csmm-slide-id'] ) && is_array( $parsed['csmm-slide-id'] ) ? array_map( 'intval', $parsed['csmm-slide-id'] ) : array();
				} elseif ( is_array( $old_content['slide_ids'] ) ) {
					$slide_ids = array_map( 'intval', $old_content['slide_ids'] );
				}
			}

			// Generate modern bg_custom_images for v1.3.0 background manager
			$bg_custom_images = array();
			foreach ( $slide_ids as $sid ) {
				if ( $sid > 0 ) {
					$src = wp_get_attachment_image_src( $sid, 'full', false );
					if ( $src ) {
						$bg_custom_images[] = array(
							'id'  => $sid,
							'url' => $src[0],
						);
					}
				}
			}

			$logo_val = isset( $old_content['logo'] ) ? sanitize_text_field( $old_content['logo'] ) : '';

			$new_content = array(
				'logo'                         => $logo_val,
				'logo_enabled'                 => ! empty( $logo_val ) ? '1' : '1',
				'logo_type'                    => 'graphic',
				'logo_text'                    => isset( $old_content['title'] ) && '' !== $old_content['title'] ? sanitize_text_field( $old_content['title'] ) : get_bloginfo( 'name' ),
				'logo_link'                    => home_url( '/' ),
				'logo_height_enabled'          => false,
				'logo_height'                  => 100,
				'title_enabled'                => '1',
				'title'                        => isset( $old_content['title'] ) && '' !== $old_content['title'] ? sanitize_text_field( $old_content['title'] ) : 'Coming Soon',
				'title_font_size_enabled'      => false,
				'title_font_size'              => 48,
				'title_color'                  => '',
				'description_enabled'          => '1',
				'description'                  => isset( $old_content['description'] ) ? wp_kses_post( $old_content['description'] ) : '',
				'description_font_size_enabled'=> false,
				'description_font_size'        => 18,
				'description_color'            => '',
				'countdown'                    => isset( $old_content['countdown'] ) ? strval( $old_content['countdown'] ) : '1',
				'countdown_title'              => isset( $old_content['countdown_title'] ) && '' !== $old_content['countdown_title'] ? sanitize_text_field( $old_content['countdown_title'] ) : 'Launching In...',
				'countdown_date'               => ( ! empty( $old_content['countdown_date'] ) && strtotime( $old_content['countdown_date'] ) > time() ) ? sanitize_text_field( $old_content['countdown_date'] ) : date( 'Y-m-d', strtotime( '+30 days' ) ),
				'countdown_time'               => isset( $old_content['countdown_time'] ) && ! empty( $old_content['countdown_time'] ) ? sanitize_text_field( $old_content['countdown_time'] ) : '10:00',
				'countdown_override_enabled'   => false,
				'countdown_digit_font_size'    => 48,
				'countdown_digit_color'        => '',
				'countdown_label_font_size'    => 14,
				'countdown_box_bg'             => '',
				'susbcriber_form'              => '0', // Lead capture is Pro-only in v1.3.0
				'form_headline_text'           => '',
				'form_placeholder_text'        => 'Email Address',
				'form_btn_text'                => 'Notify Me',
				'form_input_bg'                => 'rgba(0, 0, 0, 0.7)',
				'form_input_color'             => '#FFFFFF',
				'form_btn_bg'                  => '#e11d48',
				'form_btn_color'               => '#FFFFFF',
				'form_border_radius'           => 0,
				'video_url'                    => '',
				'slide_ids'                    => $slide_ids,
				'bg_type'                      => ! empty( $bg_custom_images ) ? 'custom' : 'default',
				'bg_custom_images'             => $bg_custom_images,
				'bg_image_size'                => 'cover',
				'bg_solid_color'               => '#0f172a',
				'custom_css'                   => '',
			);

			update_option( 'csmm_content', $new_content );
		}

		// 4. Social Media
		if ( is_array( $old_sm ) && ! empty( $old_sm ) ) {
			$fb = isset( $old_sm['comisoma_sm_facebook'] ) ? $old_sm['comisoma_sm_facebook'] : ( isset( $old_sm['csmm_sm_facebook'] ) ? $old_sm['csmm_sm_facebook'] : '#' );
			$tw = isset( $old_sm['comisoma_sm_twitter'] ) ? $old_sm['comisoma_sm_twitter'] : ( isset( $old_sm['csmm_sm_twitter'] ) ? $old_sm['csmm_sm_twitter'] : '#' );
			$ig = isset( $old_sm['comisoma_sm_instagram'] ) ? $old_sm['comisoma_sm_instagram'] : ( isset( $old_sm['csmm_sm_instagram'] ) ? $old_sm['csmm_sm_instagram'] : '#' );

			$new_sm = array(
				'enabled'           => true,
				'csmm_sm_facebook'  => esc_url_raw( $fb ),
				'csmm_sm_twitter'   => esc_url_raw( $tw ),
				'csmm_sm_instagram' => esc_url_raw( $ig ),
			);

			update_option( 'csmm_social_media', $new_sm );
		}

		// 5. Version preservation
		$old_v = $is_import && isset( $data['comisoma_current_version'] ) ? $data['comisoma_current_version'] : get_option( 'comisoma_current_version' );
		if ( ! empty( $old_v ) ) {
			update_option( 'csmm_last_version', $old_v );
		}

		// Mark migration completed
		update_option( 'csmm_v120_migrated', true );
	}

	/**
	 * Set default options on initial activation if not present.
	 */
	public static function set_default_options() {
		if ( false === get_option( 'csmm_settings' ) ) {
			update_option(
				'csmm_settings',
				array(
					'website_mode'         => 3, // 1 = Coming Soon, 2 = Maintenance, 3 = Live / Disabled
					'selected_posts'       => array(),
					'selected_pages'       => array(),
					'selected_other_pages' => array(),
				)
			);
		}

		if ( false === get_option( 'csmm_templates' ) ) {
			update_option(
				'csmm_templates',
				array(
					'template_id' => 1,
				)
			);
		}

		if ( false === get_option( 'csmm_content' ) ) {
			$current_date = date( 'Y-m-d' );
			$countdown_date = date( 'Y-m-d', strtotime( $current_date . ' +30 days' ) );

			update_option(
				'csmm_content',
				array(
					'logo'            => '1',
					'title'           => 'Coming Soon',
					'description'     => 'Thank you for visiting our website! We are currently working on creating a new and exciting online experience for you. We will be back soon with our brand new website!',
					'countdown'       => '1',
					'countdown_title' => 'Launching In...',
					'countdown_date'  => $countdown_date,
					'countdown_time'  => '10:00',
					'susbcriber_form'       => '0',
					'form_headline_text'    => '',
					'form_placeholder_text' => 'Email Address',
					'form_btn_text'         => 'Notify Me',
					'form_input_bg'         => 'rgba(0, 0, 0, 0.7)',
					'form_input_color'      => '#FFFFFF',
					'form_btn_bg'           => '#e11d48',
					'form_btn_color'        => '#FFFFFF',
					'form_border_radius'    => 0,
					'video_url'             => '',
					'slide_ids'             => array(),
					'custom_css'            => '',
				)
			);
		}

		if ( false === get_option( 'csmm_social_media' ) ) {
			update_option(
				'csmm_social_media',
				array(
					'csmm_sm_facebook'  => '#',
					'csmm_sm_twitter'   => '#',
					'csmm_sm_youtube'   => '',
					'csmm_sm_instagram' => '#',
					'csmm_sm_linkedin'  => '',
					'csmm_sm_pinterest' => '',
					'csmm_sm_tumblr'    => '',
					'csmm_sm_snapchat'  => '',
					'csmm_sm_behance'   => '',
					'csmm_sm_dribbble'  => '',
					'csmm_sm_whatsapp'  => '',
					'csmm_sm_tiktok'    => '',
					'csmm_sm_qq'        => '',
				)
			);
		}

		if ( false === get_option( 'csmm_seo' ) ) {
			update_option(
				'csmm_seo',
				array(
					'meta_title'          => '',
					'meta_description'    => '',
					'robots_meta'         => 'auto',
					'google_analytics_id' => '',
					'og_image_id'         => '',
				)
			);
		}
	}
}
