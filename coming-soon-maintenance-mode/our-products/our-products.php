<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Process the AJAX request to install and update plugins.
add_action( 'wp_ajax_comisoma_plugin_install', 'comisoma_extras_install_plugin' );
add_action( 'wp_ajax_comisoma_plugin_update', 'comisoma_extras_update_plugin' );
add_action( 'wp_ajax_comisoma_plugin_activate', 'comisoma_extras_activate_plugin' );

// Process the AJAX request to install, update, and activate themes.
add_action( 'wp_ajax_comisoma_theme_install', 'comisoma_extras_install_theme' );
add_action( 'wp_ajax_comisoma_theme_activate', 'comisoma_extras_activate_theme' );
add_action( 'wp_ajax_comisoma_theme_update', 'comisoma_extras_update_theme' );

function comisoma_extras_install_plugin() {
	// Check user capabilities.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( 'You do not have permission to install plugins.' );
	}

	// Verify the nonce for install action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'comisoma-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the plugin slug.
	$comisoma_extplugin_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $comisoma_extplugin_slug ) ) {
		wp_send_json_error( 'Plugin slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	// Get plugin information.
	$comisoma_get_plugin_info = plugins_api( 'plugin_information', array( 'slug' => sanitize_key( $comisoma_extplugin_slug ) ) );
	// Create the plugin upgrader instance.
	$comisoma_upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

	// Install the plugin.
	$comisoma_result = $comisoma_upgrader->install( $comisoma_get_plugin_info->download_link );

	// Check the installation result.
	if ( is_wp_error( $comisoma_result ) ) {
		wp_send_json_error( 'Plugin installation failed.' );
	}

	// Send response - do NOT auto-activate, let user activate manually.
	wp_send_json_success( 'Plugin installed successfully.' );
}

function comisoma_extras_update_plugin() {
	// Check user capabilities.
	if ( ! current_user_can( 'update_plugins' ) ) {
		wp_send_json_error( 'You do not have permission to update plugins.' );
	}

	// Verify the nonce for update action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'comisoma-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the plugin slug.
	$comisoma_extplugin_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $comisoma_extplugin_slug ) ) {
		wp_send_json_error( 'Plugin slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	// Get plugin information.
	$comisoma_get_plugin_info = plugins_api( 'plugin_information', array( 'slug' => sanitize_key( $comisoma_extplugin_slug ) ) );
	// Create the plugin upgrader instance.
	$comisoma_upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

	// Update the plugin.
	$comisoma_result = $comisoma_upgrader->upgrade( $comisoma_get_plugin_info->download_link );

	// Check the update result.
	if ( is_wp_error( $comisoma_result ) ) {
		wp_send_json_error( 'Plugin update failed.' );
	}

	// Send response.
	wp_send_json_success( 'Plugin updated successfully.' );
}

function comisoma_extras_activate_plugin() {
	// Check user capabilities.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error( 'You do not have permission to activate plugins.' );
	}

	// Verify the nonce for activate action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'comisoma-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the plugin slug.
	$comisoma_plugin_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $comisoma_plugin_slug ) ) {
		wp_send_json_error( 'Plugin slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	// Activate the plugin.
	$comisoma_activate_result = activate_plugin( $comisoma_plugin_slug . '/' . $comisoma_plugin_slug . '.php' );

	// Check the activation result.
	if ( is_wp_error( $comisoma_activate_result ) ) {
		wp_send_json_error( 'Plugin activation failed.' );
	}

	// Send response.
	wp_send_json_success( 'Plugin activated successfully.' );
}

// Theme functions.
function comisoma_extras_install_theme() {
	// Check user capabilities.
	if ( ! current_user_can( 'install_themes' ) ) {
		wp_send_json_error( 'You do not have permission to install themes.' );
	}

	// Verify the nonce for install action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'comisoma-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the theme slug.
	$comisoma_exttheme_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $comisoma_exttheme_slug ) ) {
		wp_send_json_error( 'Theme slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/theme-install.php';

	// Get theme information.
	$comisoma_get_theme_info = themes_api( 'theme_information', array( 'slug' => sanitize_key( $comisoma_exttheme_slug ) ) );

	// Create the theme upgrader instance.
	$comisoma_upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'theme' ) ) );

	// Install the theme.
	$comisoma_result = $comisoma_upgrader->install( $comisoma_get_theme_info->download_link );

	// Check the installation result.
	if ( is_wp_error( $comisoma_result ) ) {
		wp_send_json_error( 'Theme installation failed.' );
	}

	// Send response.
	wp_send_json_success( 'Theme installed successfully.' );
}

function comisoma_extras_update_theme() {
	// Check user capabilities.
	if ( ! current_user_can( 'update_themes' ) ) {
		wp_send_json_error( 'You do not have permission to update themes.' );
	}

	// Verify the nonce for update action.
	if ( ! isset( $_POST['extnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['extnonce'] ) ), 'comisoma-extra-nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	// Retrieve the theme slug.
	$comisoma_theme_slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

	if ( empty( $comisoma_theme_slug ) ) {
		wp_send_json_error( 'Theme slug is required.' );
	}

	// Include the necessary files.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/theme-install.php';

	// Get theme information.
	$comisoma_get_theme_info = themes_api( 'theme_information', array( 'slug' => sanitize_key( $comisoma_theme_slug ) ) );

	// Create the theme upgrader instance.
	$comisoma_upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact( 'title', 'url', 'nonce', 'theme' ) ) );

	// Update the theme.
	$comisoma_result = $comisoma_upgrader->upgrade( $comisoma_get_theme_info->download_link );

	// Check the update result.
	if ( is_wp_error( $comisoma_result ) ) {
		wp_send_json_error( 'Theme update failed.' );
	}

	// Send response.
	wp_send_json_success( 'Theme updated successfully.' );
}
