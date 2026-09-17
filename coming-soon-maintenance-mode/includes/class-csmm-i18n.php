<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Internationalization (i18n) for CSMM.
 */
class CSMM_i18n {

	/**
	 * Load plugin textdomain.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'coming-soon-maintenance-mode',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
