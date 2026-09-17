<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Admin Manager for Coming Soon Maintenance Mode Pro.
 */
class CSMM_Admin
{

	/**
	 * Init admin hooks.
	 */
	public function init()
	{
		add_action('admin_menu', array($this, 'register_menu'));
		add_action('admin_print_scripts', array($this, 'print_admin_early_scripts'), 1);
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
	}

	/**
	 * Print early scripts in head for scroll restoration and safe fallbacks.
	 */
	public function print_admin_early_scripts()
	{
		$screen = get_current_screen();
		if (!$screen || strpos($screen->id, 'wpfrank-csmm') === false) {
			return;
		}
		?>
		<script>
			window.wp = window.wp || {};
			if ('scrollRestoration' in history) {
				history.scrollRestoration = 'manual';
			}
		</script>
		<?php
	}

	/**
	 * Register admin menu.
	 */
	public function register_menu()
	{
		add_menu_page(
			__('Coming Soon', 'coming-soon-maintenance-mode'),
			__('Coming Soon', 'coming-soon-maintenance-mode'),
			'manage_options',
			'wpfrank-csmm',
			array($this, 'render_admin_app'),
			'dashicons-clock',
			30
		);
	}

	public function enqueue_assets($hook_suffix = '')
	{
		$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
		// Enqueue on our plugin page
		if ($page !== 'wpfrank-csmm' && (empty($hook_suffix) || strpos($hook_suffix, 'wpfrank-csmm') === false)) {
			return;
		}

		// WordPress media library uploader support
		wp_enqueue_media();

		$dist_path = CSMM_DIR . 'admin/assets/dist/';
		$dist_url = CSMM_URL . 'admin/assets/dist/';

		// Check manifest or built files
		$js_file = $dist_path . 'index.js';
		$css_file = $dist_path . 'index.css';

		if (file_exists($css_file)) {
			$css_ver = CSMM_VERSION . '.' . filemtime($css_file);
			wp_enqueue_style(
				'csmm-react-app-css',
				$dist_url . 'index.css',
				array(),
				$css_ver
			);
		}

		// Enqueue WordPress core utility scripts
		wp_enqueue_script('wp-hooks');
		wp_enqueue_script('wp-util');

		if (file_exists($js_file)) {
			$js_ver = CSMM_VERSION . '.' . filemtime($js_file);
			wp_enqueue_script(
				'csmm-react-app-js',
				$dist_url . 'index.js',
				array('jquery', 'wp-hooks', 'wp-util', 'media-editor', 'media-views'),
				$js_ver,
				true
			);

			$rest_api = new CSMM_REST_API();
			$settings_res = $rest_api->get_settings();
			$initial_settings = is_a($settings_res, 'WP_REST_Response') ? $settings_res->get_data() : $settings_res;

			$templates_res = $rest_api->get_templates();
			$templates = is_a($templates_res, 'WP_REST_Response') ? $templates_res->get_data() : $templates_res;

			$target_res = $rest_api->get_target_items();
			$target_items = is_a($target_res, 'WP_REST_Response') ? $target_res->get_data() : $target_res;

			// Pass context & REST configuration to React
			wp_localize_script(
				'csmm-react-app-js',
				'csmmData',
				array(
					'restUrl' => esc_url_raw(rest_url('csmm/v1/')),
					'nonce' => wp_create_nonce('wp_rest'),
					'exportUrl' => esc_url(admin_url('admin-post.php?action=csmm_export_subscribers')),
					'siteUrl' => home_url('/'),
					'siteTitle' => get_bloginfo('name'),
					'previewUrl' => add_query_arg('csmm', 'true', home_url('/')),
					'pluginUrl' => CSMM_URL,
					'version' => CSMM_VERSION,
					'user' => array(
						'name' => wp_get_current_user()->display_name,
						'can_manage' => current_user_can('manage_options'),
					),
					'initialSettings' => $initial_settings,
					'templates' => $templates,
					'targetItems' => $target_items,
				)
			);
		}
	}

	/**
	 * Render React root container.
	 */
	public function render_admin_app()
	{
		?>
		<style>
			/* Ensure no unwanted page horizontal scrollbars while preserving sticky headers */
			.toplevel_page_wpfrank-csmm html,
			.toplevel_page_wpfrank-csmm body {
				overflow-x: clip !important;
			}

			.toplevel_page_wpfrank-csmm #wpcontent,
			.toplevel_page_wpfrank-csmm #wpbody,
			.toplevel_page_wpfrank-csmm #wpbody-content {
				overflow: visible !important;
			}

			.toplevel_page_wpfrank-csmm #wpcontent {
				padding-left: 0 !important;
				padding-right: 0 !important;
			}

			.toplevel_page_wpfrank-csmm #wpbody-content {
				padding-bottom: 0 !important;
			}

			.toplevel_page_wpfrank-csmm #wpfooter {
				display: none !important;
			}

			@keyframes csmm-spin {
				to {
					transform: rotate(360deg);
				}
			}
		</style>
		<div id="csmm-react-root"
			style="margin: 0; padding: 0; width: 100%; box-sizing: border-box; overflow-x: clip; background: transparent;">
			<script>
				(function () {
					var mode = localStorage.getItem('csmm_theme_mode') || 'light';
					if (mode === 'dark') {
						document.write('<style>#csmm-preloader-bg { background-color: #0b0f19 !important; } #csmm-preloader-card { background: #1e293b !important; border-color: #334155 !important; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4) !important; } #csmm-preloader-title { color: #f8fafc !important; } #csmm-preloader-desc { color: #94a3b8 !important; } #csmm-preloader-spinner { border-color: #334155 !important; border-top-color: #3b82f6 !important; }</style>');
					}
				})();
			</script>
			<div id="csmm-preloader-bg"
				style="display: flex; align-items: center; justify-content: center; height: calc(100vh - 32px); min-height: 480px; width: 100%; padding: 20px; box-sizing: border-box; overflow: hidden; background-color: #ececec; transition: background-color 0.2s ease;">
				<div id="csmm-preloader-card"
					style="background: #ffffff; padding: 36px 44px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.06); text-align: center; max-width: 420px; width: 90%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
					<div
						style="width: 56px; height: 56px; margin: 0 auto 20px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1d4ed8); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 16px rgba(37, 99, 235, 0.25);">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2"
							stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"></circle>
							<polyline points="12 6 12 12 16 14"></polyline>
						</svg>
					</div>
					<h3 id="csmm-preloader-title"
						style="margin: 0 0 8px 0; color: #0f172a; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em;">
						<?php esc_html_e('Coming Soon Studio', 'coming-soon-maintenance-mode'); ?>
					</h3>
					<p id="csmm-preloader-desc" style="margin: 0 0 24px 0; color: #64748b; font-size: 0.875rem;">
						<?php esc_html_e('Loading workspace settings & templates...', 'coming-soon-maintenance-mode'); ?>
					</p>
					<div id="csmm-preloader-spinner"
						style="width: 32px; height: 32px; margin: 0 auto; border: 3px solid #e2e8f0; border-top-color: #2563eb; border-radius: 50%; animation: csmm-spin 0.8s linear infinite;">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
