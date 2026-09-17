<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles frontend intercepts, HTTP status codes, and template rendering.
 */
class CSMM_Frontend {

	/**
	 * Init frontend hooks.
	 */
	public function init() {
		add_action( 'template_redirect', array( $this, 'handle_intercept' ), 1 );
	}

	/**
	 * Main intercept logic.
	 */
	public function handle_intercept() {
		// 1. Live Preview Query Parameter
		if ( isset( $_GET['csmm'] ) && 'true' === $_GET['csmm'] ) {
			if ( current_user_can( 'manage_options' ) ) {
				$this->render_template();
				exit;
			}
		}

		// Don't intercept logged in admins or feeds/REST
		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// Don't block wp-login or admin pages
		if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true ) ) {
			return;
		}

		$has_v120    = false !== get_option( 'comisoma_settings' ) || false !== get_option( 'comisoma_content' );
		$is_migrated = get_option( 'csmm_v120_migrated' );
		if ( $has_v120 && ! $is_migrated ) {
			if ( class_exists( 'CSMM_Activator' ) ) {
				CSMM_Activator::migrate_v120_options( true );
			}
		}
		$settings     = get_option( 'csmm_settings', array() );
		$website_mode = isset( $settings['website_mode'] ) ? intval( $settings['website_mode'] ) : 3;

		// Mode 3 = Live (Disabled)
		if ( 3 === $website_mode ) {
			return;
		}

		// Mode 1 = Coming Soon (Site-wide)
		if ( 1 === $website_mode ) {
			if ( ! is_user_logged_in() ) {
				status_header( 200 );
				$this->render_template();
				exit;
			}
		}

		// Mode 2 = Maintenance Mode (Selective targeting & 503 status)
		if ( 2 === $website_mode ) {
			if ( ! is_user_logged_in() ) {
				if ( $this->is_maintenance_matched( $settings ) ) {
					// Send HTTP 503 for search engines
					status_header( 503 );
					header( 'Retry-After: 3600' );
					$this->render_template();
					exit;
				}
			}
		}
	}

	/**
	 * Check if current page matches maintenance mode targeting.
	 *
	 * @param array $settings
	 * @return bool
	 */
	private function is_maintenance_matched( $settings ) {
		$posts       = isset( $settings['selected_posts'] ) && is_array( $settings['selected_posts'] ) ? $settings['selected_posts'] : array();
		$pages       = isset( $settings['selected_pages'] ) && is_array( $settings['selected_pages'] ) ? $settings['selected_pages'] : array();
		$other_pages = isset( $settings['selected_other_pages'] ) && is_array( $settings['selected_other_pages'] ) ? $settings['selected_other_pages'] : array();

		// If no selective rules set, apply site-wide
		if ( empty( $posts ) && empty( $pages ) && empty( $other_pages ) ) {
			return true;
		}

		$queried = get_queried_object();
		$post_id = isset( $queried->ID ) ? $queried->ID : 0;

		// Single posts
		if ( ( is_single() || ( isset( $queried->post_type ) && 'post' === $queried->post_type ) ) && in_array( $post_id, $posts ) ) {
			return true;
		}

		// Pages
		if ( ( is_page() || ( isset( $queried->post_type ) && 'page' === $queried->post_type ) ) && in_array( $post_id, $pages ) ) {
			return true;
		}

		// Front page
		if ( is_front_page() && in_array( 'front', $other_pages, true ) ) {
			return true;
		}

		// Blog index / Home
		if ( is_home() && in_array( 'home', $other_pages, true ) ) {
			return true;
		}

		// Category archive
		if ( is_category() && in_array( 'category', $other_pages, true ) ) {
			return true;
		}

		// Tag archive
		if ( is_tag() && in_array( 'tag', $other_pages, true ) ) {
			return true;
		}

		// Search results
		if ( is_search() && in_array( 'search', $other_pages, true ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Load template loader file.
	 */
	private function render_template() {
		require_once CSMM_DIR . 'loader.php';
	}
}
