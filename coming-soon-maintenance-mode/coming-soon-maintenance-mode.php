<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin Name:       Coming Soon Maintenance Mode
 * Plugin URI:        https://wpfrank.com/
 * Description:       One of the most recommended and crucial plugin to start your website projects.
 * Version:           1.1.5
 * Requires at least: 5.0
 * Requires PHP:      5.6
 * Author:            WP Frank
 * Author URI:        https://profiles.wordpress.org/farazfrank/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       coming-soon-maintenance-mode
 * Domain Path:       /languages

Coming Soon Maintenance Mode is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Coming Soon Maintenance Mode is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Coming Soon Maintenance Mode. If not, see https://wpfrank.com/.
 */
 
// COMISOMA default URLs and Paths
define( 'COMISOMA_URL', plugin_dir_url( __FILE__ ) );

// COMISOMA activation
function comisoma_activation() {
	// update current plugin version
	if ( is_admin() ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$comisoma_plugin_data = get_plugin_data( __FILE__ );

		if ( isset( $comisoma_plugin_data['Version'] ) ) {
			$comisoma_plugin_version = $comisoma_plugin_data['Version'];
			update_option( 'comisoma_current_version', $comisoma_plugin_version );
		}

		// Migrate old options if they exist
		$options_to_migrate = array(
			'csmm_current_version',
			'csmm_last_version',
			'csmm_settings',
			'csmm_templates',
			'csmm_content',
			'csmm_social_media'
		);

		foreach ( $options_to_migrate as $old_option ) {
			$old_value = get_option( $old_option );
			if ( $old_value !== false ) {
				$new_option = str_replace( 'csmm_', 'comisoma_', $old_option );
				if ( get_option( $new_option ) === false ) {
					update_option( $new_option, $old_value );
				}
			}
		}
	}
	
	// reset admin notice
	delete_user_meta(get_current_user_id(), 'dismissed_custom_notice');
}
register_activation_hook( __FILE__, 'comisoma_activation' );

// CSMM deactivation
function comisoma_deactivation() {
	// update last active plugin version
	$comisoma_last_version = get_option( 'comisoma_current_version' );
	if ( $comisoma_last_version !== '' ) {
		update_option( 'comisoma_last_version', $comisoma_last_version );
	}
	
	// reset admin notice
	delete_user_meta(get_current_user_id(), 'dismissed_custom_notice');
}
register_deactivation_hook( __FILE__, 'comisoma_deactivation' );

// comisoma uninstall
function comisoma_uninstall() {
}
register_uninstall_hook( __FILE__, 'comisoma_uninstall' );

// comisoma
function comisoma_menu_page() {
	// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_menu_page( __( 'Coming Soon Maintenance Mode', 'coming-soon-maintenance-mode' ), __( 'Coming Soon Maintenance Mode', 'coming-soon-maintenance-mode' ), 'manage_options', 'comisoma', 'comisoma_admin_page', 'dashicons-format-gallery', 81 );
	add_submenu_page( 'comisoma', 'Pro Features', 'Pro Features', 'manage_options', 'comisoma-pro', 'comisoma_pro_page');
	add_submenu_page( 'comisoma', 'More Products', 'More Products', 'manage_options', 'comisoma-more-products', 'comisoma_more_product');
}
add_action( 'admin_menu', 'comisoma_menu_page' );
add_action( 'admin_enqueue_scripts', 'comisoma_admin_scripts' );

// comisoma main page body
function comisoma_admin_page() {
	require 'admin/comisoma-admin.php';
}

// Pro page body
function comisoma_pro_page() {
	require 'admin/comisoma-pro.php';
}

// Our Other Plugins and Themes Page
function comisoma_more_product(){
	wp_enqueue_style( 'comisoma-bootstrap-admin-css' );
	wp_enqueue_style( 'cmss-product-css' );
	// Extras Page Template.
	include 'our-products/plugins-and-themes-api.php';
	include 'our-products/our-products.php';
}

// CSMM load admin scripts (CSS/JS) only on plugin pages
function comisoma_admin_scripts() {
	if ( current_user_can( 'manage_options' ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not required for checking page parameter, no form data is processed
		if ( isset( $_GET['page'] ) ) {
			// load plugin required CSS and JS only on plugin pages
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not required for checking page parameter
			$sf_current_page_slug = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			// Check if we are on any of the plugin's pages
			if ( strpos( $sf_current_page_slug, 'comisoma' ) !== false || $sf_current_page_slug === 'comisoma-pro' ) {
				
				// Enqueue core admin CSS
				wp_enqueue_style( 'comisoma-bootstrap-admin-css', plugin_dir_url( __FILE__ ) . 'admin/assets/bootstrap-5.2.3-dist/css/bootstrap.css', array(), '5.2.3' );
				wp_enqueue_style( 'comisoma-fontawesome-admin-css', plugin_dir_url( __FILE__ ) . 'admin/assets/fontawesome-free-6.2.1-web/css/all.css', array(), '6.2.1' );
				
				if ( strpos( $sf_current_page_slug, 'comisoma' ) !== false ) {
					// core admin assets for the main plugin page
					wp_enqueue_script('media-upload');
					wp_enqueue_media();
					wp_enqueue_script( 'comisoma-uploader-js', plugins_url( 'admin/assets/js/comisoma-uploader.js', __FILE__ ), array('jquery'), '1.0.0', true );
				wp_localize_script(
					'comisoma-uploader-js',
					'ComisomaUploaderAjax',
					array(
						'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
						'logoNonce' => wp_create_nonce( 'comisoma-logo-nonce' ),
					)
				);

				// CSS
				wp_enqueue_style( 'comisoma-admin-style-css', plugin_dir_url( __FILE__ ) . 'admin/assets/css/style.css', array(), '1.1.5' );
				wp_enqueue_style( 'comisoma-bootstrap-admin-css', plugin_dir_url( __FILE__ ) . 'admin/assets/bootstrap-5.2.3-dist/css/bootstrap.css', array(), '5.2.3' );
				wp_enqueue_style( 'comisoma-fontawesome-admin-css', plugin_dir_url( __FILE__ ) . 'admin/assets/fontawesome-free-6.2.1-web/css/all.css', array(), '6.2.1' );

				// JS
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_script( 'jquery-effects-shake', '', array( 'jquery', 'jquery-ui-core', 'jquery-effects-core' ), '1.0.0', true );
				wp_enqueue_script( 'comisoma-bootstrap-bundle-js', plugin_dir_url( __FILE__ ) . 'admin/assets/bootstrap-5.2.3-dist/js/bootstrap.bundle.js', array( 'jquery' ), '5.2.3', true );

				// Admin page main JS (extracted from inline script)
				wp_enqueue_script( 'comisoma-admin-js', plugin_dir_url( __FILE__ ) . 'admin/assets/js/comisoma-admin.js', array( 'jquery', 'jquery-effects-shake' ), '1.1.5', true );
				wp_localize_script(
					'comisoma-admin-js',
					'ComisomaAdmin',
					array(
						'ajaxUrl' => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'comisoma-save' ),
					)
				);
				}

				if ( $sf_current_page_slug === 'comisoma-pro' ) {
					// Custom CSS for the new modernized pro page
					wp_enqueue_style( 'comisoma-pro-page-css', plugin_dir_url( __FILE__ ) . 'admin/assets/css/comisoma-pro.css', array('comisoma-bootstrap-admin-css'), '1.0.0' );
				}
				
				// product page assets
				wp_register_style( 'cmss-product-css', plugin_dir_url( __FILE__ ) . 'our-products/products.css', array(), '1.0' );
				wp_register_script( 'comisoma-product-js', plugin_dir_url( __FILE__ ) . 'our-products/products.js', array( 'jquery' ), '1.0', true );
				wp_enqueue_script( 'comisoma-product-js' );
				wp_localize_script(
					'comisoma-product-js',
					'ComisomaExtrasAjax',
					array(
						'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
						'extnonce' => wp_create_nonce( 'comisoma-extra-nonce' ),
					)
				);
			}
		}
	} // current_user_can end
}
add_action( 'admin_enqueue_scripts', 'comisoma_admin_scripts' );

// upload logo callback
function comisoma_logo_li_callback() {
	// Verify nonce for AJAX request
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'comisoma-logo-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}
	if ( isset($_POST['attachment_id']) ) {
		//defaults
		$comisoma_logo_url = "";
		$comisoma_logo_id = sanitize_text_field( wp_unslash ( $_POST['attachment_id'] ) );
		$comisoma_logo_url = wp_get_attachment_image_src($comisoma_logo_id, 'medium', true); // attachment medium URL
		?>
		<li class="col-md-4 comisoma-logo-<?php echo esc_attr($comisoma_logo_id); ?>" data-position="<?php echo esc_attr($comisoma_logo_id); ?>">
			<input type="hidden" class="form-control comisoma-logo-id" id="comisoma-logo-id" name="comisoma-logo-id" value="<?php echo esc_attr($comisoma_logo_id); ?>">
			<img src="<?php echo esc_url($comisoma_logo_url[0]); ?>" class="img-thumbnail mt-3 bg-light">
			<div class="d-grid gap-2">
				<button type="button" id="comisoma-remove-logo" onclick="comisoma_save('remove-logo', <?php echo esc_attr($comisoma_logo_id); ?>);" class="btn btn-danger btn-block"><i class="fa-solid fa-trash"></i> <?php esc_html_e( 'Remove Logo', 'coming-soon-maintenance-mode' ); ?></button>
			</div>
		</li>
		<?php
		wp_die();
	}
}
add_action( 'wp_ajax_comisoma_logo', 'comisoma_logo_li_callback' );

// custom admin notice start
function comisoma_admin_notice() {
	$dismissed = get_user_meta(get_current_user_id(), 'dismissed_custom_notice', true);
	if (!$dismissed) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not required for checking page parameter, no form data is processed
		if (isset($_GET['page']) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === 'comisoma') {
			$image_url = plugin_dir_url(__FILE__) . 'admin/assets/img/portfolio-wordpress.webp'; // Replace with your image URL
			echo '<div class="notice is-dismissible awp-notice-custom">
			<a href="https://wpfrank.com/wordpress-plugins/ultimate-portfolio-pro/" target="_blank"><img width="1690px" src="' . esc_url($image_url) . '"></a>
			</div>';
		}
	}
}
add_action('admin_notices', 'comisoma_admin_notice');

function comisoma_admin_notice_script() {
    // Enqueue notice CSS.
    wp_enqueue_style( 'comisoma-notice-css', plugin_dir_url( __FILE__ ) . 'admin/assets/css/comisoma-notice.css', array(), '1.1.5' );

    // Enqueue notice JS.
    wp_enqueue_script( 'comisoma-notice-js', plugin_dir_url( __FILE__ ) . 'admin/assets/js/comisoma-notice.js', array( 'jquery' ), '1.1.5', true );
    wp_localize_script(
        'comisoma-notice-js',
        'ComisomaNotice',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'comisoma_dismiss_notice_nonce' ),
        )
    );
}
add_action('admin_enqueue_scripts', 'comisoma_admin_notice_script');
function comisoma_dismiss_notice() {
    // Check the nonce
    check_ajax_referer('comisoma_dismiss_notice_nonce', 'security');
    // Update user meta to mark the notice as dismissed
    update_user_meta(get_current_user_id(), 'dismissed_custom_notice', '1');
    wp_send_json_success();
}
add_action('wp_ajax_comisoma_dismiss_notice', 'comisoma_dismiss_notice');
// custom admin notice end

// save CSMM start
function comisoma_save() {
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'comisoma-save' ) ) {
			// verified action
			//print_r($_POST);
			$tab = isset( $_POST['tab'] ) ? sanitize_text_field( wp_unslash ( $_POST['tab'] ) ) : 'more';
			
			// settings data save start
			if($tab == 'settings'){
				$comisoma_selected_posts = array();
				$comisoma_selected_pages = array();
				$comisoma_selected_other_pages = array();
				$comisoma_website_mode = isset( $_POST['website_mode'] ) ? sanitize_text_field( wp_unslash ( $_POST['website_mode'] ) ) : 3;
				
				if( isset ( $_POST['selected_posts'] ) && is_array( $_POST['selected_posts'] ) ) {
					$comisoma_selected_posts = array_map( 'sanitize_text_field', wp_unslash( $_POST['selected_posts'] ) );
				}
				if( isset ( $_POST['selected_pages'] ) && is_array( $_POST['selected_pages'] ) ) {
					$comisoma_selected_pages = array_map( 'sanitize_text_field', wp_unslash( $_POST['selected_pages'] ) );
				}
				if( isset ( $_POST['selected_other_pages'] ) && is_array( $_POST['selected_other_pages'] ) ) {
					$comisoma_selected_other_pages = array_map( 'sanitize_text_field', wp_unslash( $_POST['selected_other_pages'] ) );
				}
				
				$comisoma_settings_array = array(
					'website_mode' => $comisoma_website_mode,
					'selected_posts' => $comisoma_selected_posts,
					'selected_pages' => $comisoma_selected_pages,
					'selected_other_pages' => $comisoma_selected_other_pages,
				);
				// unset key if no posts / pages selected
				if(count($comisoma_selected_posts) <= 0) {
					unset($comisoma_settings_array['selected_posts']);
				}
				if(count($comisoma_selected_pages) <= 0) {
					unset($comisoma_settings_array['selected_pages']);
				}
				if(count($comisoma_selected_other_pages) <= 0) {
					unset($comisoma_settings_array['selected_other_pages']);
				}
				update_option('comisoma_settings', $comisoma_settings_array);
			}
			// settings data save end
			
			// templates data save start
			if($tab == 'templates'){
				$comisoma_template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( wp_unslash ( $_POST['template_id'] ) ) : 1;
				update_option('comisoma_templates', array('template_id' => $comisoma_template_id));
			}
			// templates data save end
			
			// content data save start
			if($tab == 'content'){
				$comisoma_logo = "";
				if(isset($_POST['logo']))
					$comisoma_logo = sanitize_text_field(wp_unslash($_POST['logo']));
				$comisoma_title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash ( $_POST['title'] ) ) : '';
				$comisoma_description = isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash ( $_POST['description'] ) ) : '';
				$comisoma_countdown = isset( $_POST['countdown'] ) ? sanitize_text_field( wp_unslash ( $_POST['countdown'] ) ) : '';
				$comisoma_countdown_title = isset( $_POST['countdown_title'] ) ? sanitize_text_field( wp_unslash ( $_POST['countdown_title'] ) ) : '';
				$comisoma_countdown_date = isset( $_POST['countdown_date'] ) ? sanitize_text_field( wp_unslash ( $_POST['countdown_date'] ) ) : '';
				$comisoma_countdown_time = isset( $_POST['countdown_time'] ) ? sanitize_text_field( wp_unslash ( $_POST['countdown_time'] ) ) : '';
				
				$comisoma_content_array = array(
					'logo' => $comisoma_logo,
					'title' => $comisoma_title,
					'description' => $comisoma_description,
					'countdown' => $comisoma_countdown,
					'countdown_title' => $comisoma_countdown_title,
					'countdown_date' => $comisoma_countdown_date,
					'countdown_time' => $comisoma_countdown_time,
				);
				update_option('comisoma_content', $comisoma_content_array);
			}
			// content data save end
			
			// social media data save start
			if($tab == 'social-media'){
				$comisoma_social_media_array = array(
					'comisoma_sm_facebook' => isset( $_POST['comisoma_sm_facebook'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_facebook'] ) ) : '',
					'comisoma_sm_twitter' => isset( $_POST['comisoma_sm_twitter'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_twitter'] ) ) : '',
					'comisoma_sm_youtube' => isset( $_POST['comisoma_sm_youtube'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_youtube'] ) ) : '',
					'comisoma_sm_instagram' => isset( $_POST['comisoma_sm_instagram'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_instagram'] ) ) : '',
					'comisoma_sm_linkedin' => isset( $_POST['comisoma_sm_linkedin'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_linkedin'] ) ) : '',
					'comisoma_sm_pinterest' => isset( $_POST['comisoma_sm_pinterest'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_pinterest'] ) ) : '',
					'comisoma_sm_tumblr' => isset( $_POST['comisoma_sm_tumblr'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_tumblr'] ) ) : '',
					'comisoma_sm_snapchat' => isset( $_POST['comisoma_sm_snapchat'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_snapchat'] ) ) : '',
					'comisoma_sm_behance' => isset( $_POST['comisoma_sm_behance'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_behance'] ) ) : '',
					'comisoma_sm_dribbble' => isset( $_POST['comisoma_sm_dribbble'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_dribbble'] ) ) : '',
					'comisoma_sm_whatsapp' => isset( $_POST['comisoma_sm_whatsapp'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_whatsapp'] ) ) : '',
					'comisoma_sm_tiktok' => isset( $_POST['comisoma_sm_tiktok'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_tiktok'] ) ) : '',
					'comisoma_sm_qq' => isset( $_POST['comisoma_sm_qq'] ) ? sanitize_text_field( wp_unslash ( $_POST['comisoma_sm_qq'] ) ) : '',
				);
				update_option('comisoma_social_media', $comisoma_social_media_array);
			}
			// social media data save end
			
			// more data save start
			if($tab == 'more'){
				$comisoma_more_array = array(
				);
				update_option('comisoma_more', $comisoma_more_array);
			}
			// more data save end
			
			wp_die(); // this is required to terminate immediately and return a proper response
		} else {
			echo esc_html_e( 'Nonce not verified action.', 'coming-soon-maintenance-mode' );
			die;
		}
	}
}
add_action( 'wp_ajax_comisoma_save', 'comisoma_save' );
// Removed wp_ajax_nopriv_comisoma_save — save handler requires manage_options and must not be exposed to unauthenticated users.
// save CSMM end

// register CSMM frontend scripts start
function comisoma_frontend_scripts() {
	wp_enqueue_script( 'jquery' );
	//template 1
}
add_action( 'wp_enqueue_scripts', 'comisoma_frontend_scripts' );
// register CSMM frontend scripts end

// output CSMM start
$comisoma_website_mode = 3;
$comisoma_current_date = gmdate('Y-m-d');
$comisoma_launch_dt = gmdate('Y-m-d', strtotime($comisoma_current_date . ' +30 days'));
//load CSMM content
$comisoma_content = array();
$comisoma_settings = array();
$comisoma_content = get_option('comisoma_content');
if(is_array($comisoma_content)){
	
	if(array_key_exists('countdown', $comisoma_content)){ $comisoma_countdown = $comisoma_content['countdown']; }
	if(array_key_exists('countdown_date', $comisoma_content)){ $comisoma_countdown_date = $comisoma_content['countdown_date']; }
	if(array_key_exists('countdown_time', $comisoma_content)){ $comisoma_countdown_time = $comisoma_content['countdown_time']; }
	
	// launch date calculation
	$comisoma_launch_dt = gmdate( 'F d, Y H:i:s', strtotime("$comisoma_countdown_date $comisoma_countdown_time")); // March 25, 2024 15:37:25
	$comisoma_today_date = current_datetime()->format('F d, Y H:i:s'); // get time accordingly to WordPress time zone settings
}

$comisoma_settings = get_option('comisoma_settings');
if(is_array($comisoma_settings)){
	if(array_key_exists('website_mode', $comisoma_settings)){ $comisoma_website_mode = $comisoma_settings['website_mode']; }
}

// - coming soon mode start
if($comisoma_website_mode == 1) {
	function comisoma_website_mode(){
		// check user logged in
		if (!is_user_logged_in()) {
			include('loader.php');
			exit();
		} else {
		}
	}
	add_action( 'template_redirect', 'comisoma_website_mode' );
}
// - coming soon mode end

// - maintenance soon mode start
if($comisoma_website_mode == 2) {
	function comisoma_website_mode(){
		// chekc user logged in
		if (!is_user_logged_in()) {
			
			global $post;
			$comisoma_post_id = "";
			$comisoma_post_type = "";
			$comisoma_flag = false;
			$comisoma_queried_object = get_queried_object();
			$comisoma_posts = array();
			$comisoma_pages = array();
			$comisoma_other_pages = array();
			if(isset($comisoma_queried_object->ID)) {
				$comisoma_post_id = $comisoma_queried_object->ID;
				$comisoma_post_type = $comisoma_queried_object->post_type;
			}
			
			$comisoma_settings = get_option('comisoma_settings');
			if(is_array($comisoma_settings)){
				if(array_key_exists('website_mode', $comisoma_settings)){ $comisoma_website_mode = $comisoma_settings['website_mode']; }
				if(array_key_exists('selected_posts', $comisoma_settings)){ $comisoma_posts = $comisoma_settings['selected_posts']; }
				if(array_key_exists('selected_pages', $comisoma_settings)){ $comisoma_pages = $comisoma_settings['selected_pages']; }
				if(array_key_exists('selected_other_pages', $comisoma_settings)){ $comisoma_other_pages = $comisoma_settings['selected_other_pages']; }
			}
			
			// enable maintenance mode on posts
			if($comisoma_post_type == "post" || is_single() ) {
				if(in_array( $comisoma_post_id, $comisoma_posts)) {
					$comisoma_flag = true;
				}
			}
			
			// enable maintenance mode on pages - is_page
			if($comisoma_post_type == "page" || is_page() ) {
				if(in_array( $comisoma_post_id, $comisoma_pages)) {
					$comisoma_flag = true;
				}
			}
			
			// font page
			if(is_front_page()){
				if(in_array( 'front', $comisoma_other_pages)) {
					$comisoma_flag = true;
				}
			}
			
			// home page
			if(is_home()) {
				if(in_array( 'home', $comisoma_other_pages)) {
					$comisoma_flag = true;
				}
			}
			
			// category
			if(is_category()) {
				if(in_array( 'category', $comisoma_other_pages)) {
					$comisoma_flag = true;
				}
			}
			
			// tag
			if(is_tag()) {
				if(in_array( 'tag', $comisoma_other_pages)) {
					$comisoma_flag = true;
				}
			}
			
			// search
			if(is_search()) {
				if(in_array( 'search', $comisoma_other_pages)) {
					$comisoma_flag = true;
				}
			}
			
			if($comisoma_flag) {
				include('loader.php');
				exit();
			}
			
		} else {
		}
	}
	add_action( 'template_redirect', 'comisoma_website_mode' );
}
// - maintenance soon mode end

// output CSMM end

// live preview CSMM start
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not required for preview parameter check, safe read-only operation
if( isset( $_GET['csmm'] ) && sanitize_text_field( wp_unslash( $_GET['csmm'] ) ) === 'true' ){
	function comisoma_website_mode_preview(){
		// chekc user logged in
		include('loader.php');
		exit();
	}
	add_action( 'template_redirect', 'comisoma_website_mode_preview' );
}
// output CSMM end

// restrict rest API for maintenance mode start
function comisoma_restrict_rest_api_for_maintenance_mode($result, $server, $request) {
    // Check if the maintenance mode is enabled in your plugin's settings
    $comisoma_website_mode = 3; // default mode live
    $comisoma_settings = get_option('comisoma_settings');
	if(is_array($comisoma_settings)){
		if(array_key_exists('website_mode', $comisoma_settings)){ $comisoma_website_mode = $comisoma_settings['website_mode']; }
	}

    // Restrict access to posts and pages for unauthenticated users if maintenance mode is enabled
    if ($comisoma_website_mode && !is_user_logged_in()) {
        // Check if the request is for posts or pages
        if (strpos($request->get_route(), '/wp/v2/posts') !== false || strpos($request->get_route(), '/wp/v2/pages') !== false) {
            return new WP_Error('rest_forbidden', esc_html__('The site is in maintenance mode.', 'coming-soon-maintenance-mode'), array('status' => rest_authorization_required_code()));
        }
    }

    return $result;
}
add_filter('rest_pre_dispatch', 'comisoma_restrict_rest_api_for_maintenance_mode', 10, 3);
// restrict rest API for maintenance mode end
?>
