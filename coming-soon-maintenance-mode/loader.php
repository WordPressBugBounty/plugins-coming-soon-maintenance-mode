<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// get current plugin version
$comisoma_current_version = get_option( 'comisoma_current_version' );
$comisoma_last_version    = get_option( 'comisoma_last_version' );

//defaults
$comisoma_content = array();
$comisoma_templates = array();
$comisoma_settings = array();
$comisoma_posts = array();
$comisoma_pages = array();
$comisoma_other_pages = array();
$comisoma_website_mode = 3;
$comisoma_template_id = 1;
$comisoma_logo_id = "";
$comisoma_logo_alt = "coming-soon-logo";
$comisoma_logo_url[0] = esc_url( COMISOMA_URL.'templates/images/logo-w.png' );
$comisoma_title = sanitize_text_field("Coming Soon");
$comisoma_description = sanitize_text_field("Thank you for visiting our website! We are currently working on creating a new and exciting online experience for you. While we finish up the final touches, please sign up for our newsletter to receive exclusive updates and offers.");
$comisoma_countdown = 1;
$comisoma_countdown_title = sanitize_text_field("Launching In...");
$comisoma_current_date = gmdate('Y-m-d');
$comisoma_launch_dt = gmdate('Y-m-d', strtotime($comisoma_current_date . ' +30 days'));
$comisoma_countdown_date = gmdate('Y-m-d', strtotime($comisoma_current_date . ' +30 days'));
$comisoma_countdown_time = "10:00";
$comisoma_sm_facebook = $comisoma_sm_twitter = $comisoma_sm_instagram = "#";


//load CSMM settings
$comisoma_settings = get_option('comisoma_settings');
if(is_array($comisoma_settings)){
	if(array_key_exists('website_mode', $comisoma_settings)){ $comisoma_website_mode = $comisoma_settings['website_mode']; }
}
//load CSMM templates
$comisoma_templates = get_option('comisoma_templates');
if(is_array($comisoma_templates)){
	if(array_key_exists('template_id', $comisoma_templates)){ $comisoma_template_id = $comisoma_templates['template_id']; }
}
//load CSMM content
$comisoma_content = get_option('comisoma_content');
//print_r($comisoma_content);
if(is_array($comisoma_content)){
	if(array_key_exists('logo', $comisoma_content)){ 
		$comisoma_logo_id = $comisoma_content['logo']; 
		if($comisoma_logo_id)
			$comisoma_logo_url = wp_get_attachment_image_src($comisoma_logo_id, 'medium', false); // attachment medium URL
		else
			$comisoma_logo_url[0] = esc_url( COMISOMA_URL.'templates/images/logo-w.png' );
	}
	if(array_key_exists('title', $comisoma_content)){ $comisoma_title = $comisoma_content['title']; }
	if(array_key_exists('description', $comisoma_content)){ $comisoma_description = $comisoma_content['description']; }
	if(array_key_exists('countdown', $comisoma_content)){ $comisoma_countdown = $comisoma_content['countdown']; }
	if(array_key_exists('countdown_title', $comisoma_content)){ $comisoma_countdown_title = $comisoma_content['countdown_title']; }
	if(array_key_exists('countdown_date', $comisoma_content)){ $comisoma_countdown_date = $comisoma_content['countdown_date']; }
	if(array_key_exists('countdown_time', $comisoma_content)){ $comisoma_countdown_time = $comisoma_content['countdown_time']; }
	
	// launch date calculation
	$comisoma_launch_date = gmdate('F d, Y', strtotime($comisoma_countdown_date));
	$comisoma_launch_time = gmdate('H:i:s', strtotime($comisoma_countdown_time));
	$comisoma_launch_dt = $comisoma_launch_date." ".$comisoma_launch_time; // March 25, 2024 15:37:25
}

// load social media
$comisoma_social_media = get_option('comisoma_social_media');
if(is_array($comisoma_social_media)){
	if(array_key_exists('comisoma_sm_facebook', $comisoma_social_media)){ $comisoma_sm_facebook = $comisoma_social_media['comisoma_sm_facebook']; }
	if(array_key_exists('comisoma_sm_twitter', $comisoma_social_media)){ $comisoma_sm_twitter = $comisoma_social_media['comisoma_sm_twitter']; }
	if(array_key_exists('comisoma_sm_instagram', $comisoma_social_media)){ $comisoma_sm_instagram = $comisoma_social_media['comisoma_sm_instagram']; }
}

// Enqueue template assets function
function comisoma_enqueue_template_assets( $template_id ) {
	// Base styles
	wp_enqueue_style( 'comisoma-base', COMISOMA_URL . 'templates/css/base.css', array(), '1.1.0' );
	wp_enqueue_style( 'comisoma-vendor', COMISOMA_URL . 'templates/css/vendor.css', array(), '1.1.0' );
	wp_enqueue_style( 'comisoma-main', COMISOMA_URL . 'templates/css/main.css', array(), '1.1.0' );
	wp_enqueue_style( 'comisoma-fontawesome', COMISOMA_URL . 'admin/assets/fontawesome-free-6.2.1-web/css/all.min.css', array(), '6.2.1' );
	
	// Template specific styles
	wp_enqueue_style( 'comisoma-template-' . $template_id, COMISOMA_URL . 'templates/css/' . $template_id . '.css', array(), '1.1.0' );
	
	// Footer scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'comisoma-plugins', COMISOMA_URL . 'templates/js/plugins.js', array( 'jquery' ), '1.1.0', true );
	
	// Template specific scripts
	if ( $template_id == 1 ) {
		wp_enqueue_script( 'comisoma-particles', COMISOMA_URL . 'templates/js/particles.min.js', array( 'jquery' ), '1.1.0', true );
		wp_enqueue_script( 'comisoma-polygons', COMISOMA_URL . 'templates/js/polygons.js', array( 'jquery' ), '1.1.0', true );
	}

	global $comisoma_countdown, $comisoma_launch_dt;

	$comisoma_inline_script = '
		jQuery( document ).ready(function() {
			var doc = document.documentElement;
			doc.setAttribute("data-useragent", navigator.userAgent);

	';

	if ( $comisoma_countdown == 1 ) {
		$comisoma_inline_script .= '
			var ComisomaFinalCountdown = function() {
				var finalDate =  new Date("' . esc_js( $comisoma_launch_dt ) . '").getTime();
				jQuery(".home-content__clock").countdown(finalDate)
				.on("update.countdown", function(event) {
					var str = \'<div class="time days">%D <span>D</span></div>\' +
							\'<div class="time hours">%H <span>H</span></div>\' +
							\'<div class="time minutes">%M <span>M</span></div>\' +
							\'<div class="time seconds">%S <span>S</span></div>\';
					jQuery(this).html(event.strftime(str));
				})
				.on("finish.countdown", function(event) {
					jQuery( ".home-content__counter" ).fadeOut( "slow" );
				});
			};
			(function ssInit() {
				ComisomaFinalCountdown();
			})();
		';
	}

	$comisoma_inline_script .= '});';
	wp_add_inline_script( 'comisoma-plugins', $comisoma_inline_script );
}

// Enqueue assets for this template
comisoma_enqueue_template_assets( $comisoma_template_id );

//print_r($cmss_subscriber_list);
$comisoma_file = plugin_dir_path( __FILE__ )."templates/$comisoma_template_id.php";
include($comisoma_file);
