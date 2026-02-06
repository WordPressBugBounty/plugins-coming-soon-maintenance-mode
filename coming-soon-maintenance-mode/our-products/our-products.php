<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Process the AJAX request to install and activate plugins.
add_action( 'wp_ajax_extras_plugin_install', 'csmm_extras_install_plugin' );
add_action( 'wp_ajax_extras_plugin_update', 'csmm_extras_update_plugin' );
add_action( 'wp_ajax_extras_plugin_activate', 'csmm_extras_activate_plugin' );

// Process the AJAX request to install, update, and activate themes.
add_action( 'wp_ajax_extras_theme_install', 'csmm_extras_install_theme' );
add_action( 'wp_ajax_extras_theme_activate', 'csmm_extras_activate_theme' );
add_action( 'wp_ajax_extras_theme_update', 'csmm_extras_update_theme' );

function csmm_extras_install_plugin() {
	// Verify the nonce for install action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'csmm-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the plugin slug.
	$csmm_extplugin_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $csmm_extplugin_slug ) ) {
		wp_send_json_error( 'Plugin slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	// Get plugin information.
	$csmm_get_plugin_info = plugins_api( 'plugin_information', array( 'slug' => sanitize_key( $csmm_extplugin_slug ) ) );
	// Create the plugin upgrader instance.
	$csmm_upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

	// Install the plugin.
	$csmm_result = $csmm_upgrader->install( $csmm_get_plugin_info->download_link );

	// Check the installation result.
	if ( is_wp_error( $csmm_result ) ) {
		wp_send_json_error( 'Plugin installation failed.' );
	}

	// Send response.
	csmm_extras_activate_plugin();
	wp_send_json_success( 'Plugin installed successfully.' );
}

function csmm_extras_update_plugin() {
	// Verify the nonce for update action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'csmm-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the plugin slug.
	$csmm_extplugin_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $csmm_extplugin_slug ) ) {
		wp_send_json_error( 'Plugin slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	// Get plugin information.
	$csmm_get_plugin_info = plugins_api( 'plugin_information', array( 'slug' => sanitize_key( $csmm_extplugin_slug ) ) );
	// Create the plugin upgrader instance.
	$csmm_upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

	// Update the plugin.
	$csmm_result = $csmm_upgrader->upgrade( $csmm_get_plugin_info->download_link );

	// Check the update result.
	if ( is_wp_error( $csmm_result ) ) {
		wp_send_json_error( 'Plugin update failed.' );
	}

	// Send response.
	wp_send_json_success( 'Plugin updated successfully.' );
}

function csmm_extras_activate_plugin() {
	// Verify the nonce for activate action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'csmm-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the plugin slug.
	$csmm_plugin_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $csmm_plugin_slug ) ) {
		wp_send_json_error( 'Plugin slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	// Activate the plugin.
	$csmm_activate_result = activate_plugin( $csmm_plugin_slug . '/' . $csmm_plugin_slug . '.php' );

	// Check the activation result.
	if ( is_wp_error( $csmm_activate_result ) ) {
		wp_send_json_error( 'Plugin activation failed.' );
	}

	// Send response.
	wp_send_json_success( 'Plugin activated successfully.' );
}

// Theme functions.
function csmm_extras_install_theme() {
	// Verify the nonce for install action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'csmm-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the theme slug.
	$csmm_exttheme_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $csmm_exttheme_slug ) ) {
		wp_send_json_error( 'Theme slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/theme-install.php';

	// Get theme information.
	$csmm_get_theme_info = themes_api( 'theme_information', array( 'slug' => sanitize_key( $csmm_exttheme_slug ) ) );

	// Create the theme upgrader instance.
	$csmm_upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'theme' ) ) );

	// Install the theme.
	$csmm_result = $csmm_upgrader->install( $csmm_get_theme_info->download_link );

	// Check the installation result.
	if ( is_wp_error( $csmm_result ) ) {
		wp_send_json_error( 'Theme installation failed.' );
	}

	// Send response.
	wp_send_json_success( 'Theme installed successfully.' );
}

function csmm_extras_update_theme() {
	// Verify the nonce for update action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'csmm-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the theme slug.
	$csmm_theme_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $csmm_theme_slug ) ) {
		wp_send_json_error( 'Theme slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/theme-install.php';

	// Get theme information.
	$csmm_get_theme_info = themes_api( 'theme_information', array( 'slug' => sanitize_key( $csmm_theme_slug ) ) );

	// Create the theme upgrader instance.
	$csmm_upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'theme' ) ) );

	// Update the theme.
	$csmm_result = $csmm_upgrader->upgrade( $csmm_get_theme_info->download_link );

	// Check the update result.
	if ( is_wp_error( $csmm_result ) ) {
		wp_send_json_error( 'Theme update failed.' );
	}

	// Send response.
	wp_send_json_success( 'Theme updated successfully.' );
}
