<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>



<div class="container-fluid">

	<div class="avantex-extras-page shadow-lg bg-light h-100 p-3">
		<h1>Our Free Themes and Plugins</h1>
		<p>Thanks for choosing us. Please have a look at our various builds!</p>
	</div>

	<div class="avantex-extras-title mt-3 mb-0">
		<h1>Plugins</h1>
	</div>
	<div class="row row-cols-1 row-cols-md-4 justify-content-center gap-2">
		<?php
		$csmm_plugin_button      = '';
		$csmm_frank_plugin_slugs = array( 
			'ultimate-portfolio', 'customizer-login-page', 'testimonial-maker', 
			'weather-effect', 'responsive-slider-gallery', 'team-builder-member-showcase',
			'modal-popup-box', 'wp-instagram-feed-awplife', 'abc-pricing-table',
			//'wp-flickr-gallery', 'new-album-gallery', 'blog-filter', 'event-monster', 'new-grid-gallery',
			//'new-image-gallery', 'media-slider', 'new-photo-gallery', 
			//  'slider-responsive-slideshow',
			// 'new-video-gallery', 'portfolio-filter-gallery',
		);

		// Required Info to Fetch Plugin Data.
		require ABSPATH . 'wp-admin/includes/plugin-install.php';
		require ABSPATH . 'wp-admin/includes/theme-install.php';

		foreach ( $csmm_frank_plugin_slugs as $csmm_plugin_slug ) {
			// Define the arguments for the plugins_api function.
			$csmm_args = array(
				'slug'   => $csmm_plugin_slug,
				'fields' => array(
					'name'              => true,
					'short_description' => true,
					'active_installs'   => true,
					'icons'             => true,
					'last_updated'      => true,
					'num_ratings'       => true,
					'rating'            => true,
					'ratings'           => true,
					'screenshots'       => true,
					'slug'              => true,
					'version'           => true,
					'versions'          => true,
					'downloaded'        => true,
				),
			);

			// Fetch plugin data using plugins_api.
			$csmm_extras_info = plugins_api( 'plugin_information', $csmm_args );

			// Proceed if plugin data is available.
			if ( ! is_wp_error( $csmm_extras_info ) ) {
				$csmm_extras_name              = $csmm_extras_info->name;
				$csmm_extras_rating            = $csmm_extras_info->rating;
				$csmm_extras_installs          = $csmm_extras_info->active_installs;
				$csmm_extras_last_updated      = $csmm_extras_info->last_updated;
				$csmm_extras_icons             = $csmm_extras_info->icons;
				$csmm_extras_author            = $csmm_extras_info->author;
				$csmm_extras_ratings           = $csmm_extras_info->ratings;
				$csmm_extras_downloaded        = $csmm_extras_info->downloaded;
				$csmm_extras_short_description = $csmm_extras_info->short_description;

				// Fetch image source and name from icons.
				$csmm_src = isset( $csmm_extras_icons['2x'] ) ? $csmm_extras_icons['2x'] : ( isset( $csmm_extras_icons['1x'] ) ? $csmm_extras_icons['1x'] : '' );
				$csmm_alt = esc_html( $csmm_extras_name ) . ( isset( $csmm_extras_icons['2x'] ) ? ' 2x' : ( isset( $csmm_extras_icons['1x'] ) ? ' 1x' : '' ) );

				// Iterate through the ratings and construct the star rating HTML.
				// Calculate the average rating.
				$csmm_total_ratings = 0;
				$csmm_total_count   = 0;

				foreach ( $csmm_extras_ratings as $csmm_rating => $csmm_count ) {
					$csmm_total_ratings += ( $csmm_rating * $csmm_count );
					$csmm_total_count   += $csmm_count;
				}

				$csmm_average_rating = round( $csmm_total_count > 0 ? $csmm_total_ratings / $csmm_total_count : 0 );

				// Determine the number of filled stars and half star based on the average rating.
				$csmm_filled_stars = floor( $csmm_average_rating );
				$csmm_half_star    = round( $csmm_average_rating - $csmm_filled_stars, 1 ) >= 0.5;

				// Construct the star rating HTML.
				$csmm_star_rating_html = '<div class="wporg-ratings" title="' . $csmm_average_rating . ' out of 5 stars" style="color:#ffb900;">';

				// Filled stars.
				for ( $csmm_i = 0; $csmm_i < $csmm_filled_stars; $csmm_i++ ) {
					$csmm_star_rating_html .= '<span class="dashicons dashicons-star-filled"></span>';
				}

				// Half star.
				if ( $csmm_half_star ) {
					$csmm_star_rating_html .= '<span class="dashicons dashicons-star-half"></span>';
				}

				// Empty stars.
				for ( $csmm_i = 0; $csmm_i < 5 - $csmm_filled_stars - $csmm_half_star; $csmm_i++ ) {
					$csmm_star_rating_html .= '<span class="dashicons dashicons-star-empty"></span>';
				}

				$csmm_star_rating_html .= '</div>';
				// Finished Stars.

				if ( $csmm_plugin_slug === 'slider-factory' ) {
					// Trim the name to 7 words.
					$csmm_name_words  = explode( ' ', $csmm_extras_name );
					$csmm_extras_name = implode( ' ', array_slice( $csmm_name_words, 0, 2 ) );
				}


				/** Install Update Button */
				// Set the plugin slug and other installation information.
				$csmm_plugin_info = $csmm_extras_info;

				// Check if the plugin information is available.
				if ( ! is_wp_error( $csmm_plugin_info ) ) {
					// Check the installation status.
					$csmm_plugin_install_status = install_plugin_install_status( $csmm_plugin_info );

					// Generate installation link based on the installation status.
					switch ( $csmm_plugin_install_status['status'] ) {
						case 'install':
							$csmm_plugin_button = '<button href="#" class="btn btn-lg btn-lg btn-dark" onclick="extrasAjaxRequest(\'' . esc_html( $csmm_plugin_slug ) . '\', \'extras_plugin_install\', this)">Install Now</button>';
							break;
						case 'update_available':
							$csmm_plugin_button = '<button href="#" class="btn btn-lg btn-dark" onclick="extrasAjaxRequest(\'' . esc_html( $csmm_plugin_slug ) . '\', \'extras_plugin_update\', this)">Update Now</button>';
							break;
						case 'latest_installed':
						case 'newer_installed':
							// Plugin is already installed and up to date.
							$csmm_plugin_name = $csmm_plugin_slug . '/' . $csmm_plugin_slug . '.php';
							if ( is_plugin_active( $csmm_plugin_name ) ) {
								$csmm_plugin_button = '<button type="button" class="btn btn-lg btn-success" disabled>Installed/Activated</button>';
							} else {
								$csmm_plugin_button = '<button href="#" class="btn btn-lg btn-dark" onclick="extrasAjaxRequest(\'' . esc_html( $csmm_plugin_slug ) . '\', \'extras_plugin_activate\', this)">Activate Now</button>';
							}
							break;
					}
				}
				/** End Install Update Button */
				?>
				<div class="col">
					<div class="card shadow-lg bg-light h-100">
					<img class="img-thumbnail rounded m-5 p-5" src="<?php echo esc_url( $csmm_src ); ?>" alt="<?php echo esc_attr( $csmm_alt ); ?>">
						<div class="card-body">
							<h5 class="card-title"><?php echo esc_html( $csmm_extras_name ); ?></h5>
							<?php echo wp_kses_post( $csmm_star_rating_html ); ?>
							<p class="card-text card-formating"><?php echo esc_html( $csmm_extras_short_description ); ?></p>
							<p class="card-text card-formating"><b>Total Downloads :</b> <?php echo esc_html( number_format_i18n( $csmm_extras_downloaded ) ); ?>+</p>
							<!--<p class="card-text card-formating"><b>Author :</b> <?php //echo wp_kses_post( $csmm_extras_author, ); ?></p>-->
						</div>
						<?php
						$csmm_allowed_tags = array(
							'button' => array(
								'href'    => array(),
								'onclick' => array(),
								'class'   => array(),
							),
						);
						?>
						<p class="card-text text-center mt-1"> <?php echo wp_kses( $csmm_plugin_button, $csmm_allowed_tags ); ?></p>
						<div class="card-footer">
							<small class="text-muted">Last Updated: <?php echo esc_html( $csmm_extras_last_updated ); ?></small>
						</div>
					</div>
				</div>
				<?php
			} else {
				echo 'Failed to fetch plugin information for ' . esc_html( $csmm_plugin_slug ) . '<br>';
			}
			?>

			<?php
		};
		?>
	</div>
	<br>
	<div class="avantex-extras-title mt-5 mb-0">
		<h1>Themes</h1>
	</div>
	<div class="row row-cols-1 row-cols-md-4 justify-content-center gap-2">
		<?php
		// Plugins Loop End.

		// Themes Start.
		$csmm_theme_button      = '';
		$csmm_frank_theme_slugs = array( 
		'webenvo', 'metaverse', 'medihealth',
		'formula', 'formula-dark', 'formula-light',
		'medical-formula', 'business-campaign', 'cryptocurrency-exchange',
		//'nature-formula', 'home-interior', 'education-formula', 'dental-hospital', 
		//'hospital-health-care', 'awp-marketing-agency', 'aneeq', 'dental-hospital', 'awpbusinesspress', 
		//'bloglane', 'blush', 'business-campaign',  'timelineblog', 'coin-market', 
		//'cryptostore', 'newsstreet', 'business-blogs', 'daron'
		);
		// Fetch theme data.
		foreach ( $csmm_frank_theme_slugs as $csmm_theme_slug ) {
			$csmm_thm_args = array(
				'slug'   => $csmm_theme_slug,
				'fields' => array(
					'name'            => true,
					'downloaded'      => true,
					'theme_url'       => true,
					'active_installs' => true,
					'screenshot_url'  => true,
					'last_updated'    => true,
					'num_ratings'     => true,
					'rating'          => true,
					'ratings'         => true,
					'version'         => true,
					'preview_url'     => true,
					'parent'          => true,
					'author'          => true,
					'sections'        => true,
				),
			);

			$csmm_thm_extras_info = themes_api( 'theme_information', $csmm_thm_args );

			// Proceed if theme data is available.
			if ( ! is_wp_error( $csmm_thm_extras_info ) ) {
				$csmm_thm_name           = $csmm_thm_extras_info->name;
				$csmm_thm_downloaded     = $csmm_thm_extras_info->downloaded;
				$csmm_thm_theme_url      = $csmm_thm_extras_info->theme_url;
				$csmm_thm_installs       = $csmm_thm_extras_info->active_installs;
				$csmm_thm_screenshot_url = $csmm_thm_extras_info->screenshot_url;
				$csmm_thm_last_updated   = $csmm_thm_extras_info->last_updated;
				$csmm_thm_num_ratings    = $csmm_thm_extras_info->num_ratings;
				$csmm_thm_rating         = $csmm_thm_extras_info->rating;
				$csmm_thm_ratings        = $csmm_thm_extras_info->ratings;
				$csmm_thm_version        = $csmm_thm_extras_info->version;
				$csmm_thm_preview_url    = $csmm_thm_extras_info->preview_url;
				$csmm_thm_author         = $csmm_thm_extras_info->author;
				// var_dump( $csmm_thm_preview_url  );
				if ( isset( $csmm_thm_extras_info->parent ) ) {
					// Parent property exists.
					$csmm_thm_parent = $csmm_thm_extras_info->parent;
				}

				// Iterate through the ratings and construct the star rating HTML.
				// Calculate the average rating.
				$csmm_total_ratings = 0;
				$csmm_total_count   = 0;

				foreach ( $csmm_thm_ratings as $csmm_rating => $csmm_count ) {
					$csmm_total_ratings += ( $csmm_rating * $csmm_count );
					$csmm_total_count   += $csmm_count;
				}

				$csmm_average_rating = $csmm_total_count > 0 ? $csmm_total_ratings / $csmm_total_count : 0;

				// Determine the number of filled stars and half star based on the average rating.
				$csmm_filled_stars = floor( $csmm_average_rating );
				$csmm_half_star    = round( $csmm_average_rating - $csmm_filled_stars, 1 ) >= 0.5;

				// Construct the star rating HTML.
				$csmm_star_rating_html = '<div class="wporg-ratings" title="' . $csmm_average_rating . ' out of 5 stars" style="color:#ffb900;">';

				// Filled stars.
				for ( $csmm_i = 0; $csmm_i < $csmm_filled_stars; $csmm_i++ ) {
					$csmm_star_rating_html .= '<span class="dashicons dashicons-star-filled"></span>';
				}

				// Half star.
				if ( $csmm_half_star ) {
					$csmm_star_rating_html .= '<span class="dashicons dashicons-star-half"></span>';
				}

				// Empty stars.
				for ( $csmm_i = 0; $csmm_i < 5 - $csmm_filled_stars - $csmm_half_star; $csmm_i++ ) {
					$csmm_star_rating_html .= '<span class="dashicons dashicons-star-empty"></span>';
				}

				$csmm_star_rating_html .= '</div>';
				// Finished Stars.

				// Get the theme information.
				$csmm_theme_info = wp_get_theme( $csmm_theme_slug );

				// Check if the theme directory exists.
				$csmm_is_installed = is_dir( get_theme_root() . '/' . $csmm_theme_slug );

				// Check if the theme information is available and the theme is installed.
				if ( $csmm_theme_info->exists() && $csmm_is_installed ) {
					$csmm_current_theme   = wp_get_theme();
					$csmm_is_active       = $csmm_current_theme->get_stylesheet() === $csmm_theme_info->get_stylesheet();
					$csmm_current_version = $csmm_theme_info->get( 'Version' );
					$csmm_latest_version  = $csmm_thm_version;

					// Generate buttons based on the installation, update, and activation status.
					if ( version_compare( $csmm_current_version, $csmm_latest_version, '<' ) ) {
						$csmm_theme_button = '<button href="#" class="btn btn-dark" onclick="extrasAjaxRequest(\'' . esc_html( $csmm_theme_slug ) . '\', \'extras_theme_update\', this)">Update Now</button>';
					} elseif ( $csmm_is_active ) {
						$csmm_theme_button = '<button type="button" class="btn btn-success" disabled>Active</button>';
					} else {
						$csmm_theme_button = '<a href="' . esc_url( admin_url( 'themes.php' ) ) . '" class="btn btn-dark">Activate Now</a>';
					}
				} else {
					$csmm_theme_button = '<button href="#" class="btn btn-dark" onclick="extrasAjaxRequest(\'' . esc_html( $csmm_theme_slug ) . '\', \'extras_theme_install\', this)">Install Free</button>';
				}
				// Construct the theme card HTML.
				?>
				<div class="col">
					<div class="card shadow-lg bg-light h-100">
						<img class="card-img-top rounded" src="<?php echo esc_url( $csmm_thm_screenshot_url ); ?>" alt="<?php echo esc_attr( $csmm_thm_name ); ?>">
						<div class="card-body">
							<h5 class="card-title"><?php echo esc_html( $csmm_thm_name ); ?></h5>
							<?php //if ( ! empty( $csmm_thm_parent ) ) { ?>
								<!--<p class="card-text card-formating"><b>Child theme of : </b><?php echo esc_html( $csmm_thm_parent['name'] ); ?></p>-->
							<?php //} ?>
							<?php echo wp_kses_post( $csmm_star_rating_html ); ?>
							<p class="card-text card-formating"><b>Total Downloads : </b><?php echo esc_html( number_format_i18n( $csmm_thm_downloaded ) ); ?>+</p>
							<!--<p class="card-text card-formating"><b>Author :</b> <a href="<?php echo esc_url( $csmm_thm_author['profile'] ); ?>" ><?php echo esc_html( $csmm_thm_author['display_name'], ); ?></a> </p>-->
							<!--<p class="card-text card-formating"><b>Author Web:</b> <a href="<?php echo esc_url( $csmm_thm_author['author_url'] ); ?>" ><?php echo esc_html( $csmm_thm_author['author_url'], ); ?></a> </p>-->
						</div>
							<?php
							$csmm_thm_allowed_tags = array(
								'a'      => array(
									'href'    => array(),
									'onclick' => array(),
									'class'   => array(),
									'type'    => array(),
								),

								'button' => array(
									'href'    => array(),
									'onclick' => array(),
									'class'   => array(),
									'type'    => array(),
								),
							);
							?>
						<p class="card-text text-center btn-formatting mt-1">
							<?php echo wp_kses( $csmm_theme_button, $csmm_thm_allowed_tags ); ?>
							<?php
							switch ( $csmm_thm_name ) {
								case 'Webenvo':
									$csmm_thm_demo_link = 'https://webenvo.com/premium-themes/webenvo-pro/';
									$csmm_thm_buy_link  = 'https://webenvo.com/member/signup/webenvo-premium';
									break;
								case 'Formula':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'Formula Dark':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'Formula Light':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'Medical Formula':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								
								case 'Metaverse':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'MediHealth':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/medihealth-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/medihealth-premium';
									break;
								case 'Business Campaign':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/wpbusinesspress-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/wpbusinesspress-premium';
									break;
								case 'Cryptocurrency Exchange':
									$csmm_thm_demo_link = 'https://awplife.com/wordpress-themes/crypto-premium/';
									$csmm_thm_buy_link  = 'https://awplife.com/account/signup/crypto-premium';
									break;
								
							}
							?>
							<a href="<?php echo esc_url( $csmm_thm_demo_link ); ?>" type="button" class="btn dm-btn-clr" target="_blank" >Pro Demo</a>
							<a href="<?php echo esc_url( $csmm_thm_buy_link ); ?>" type="button" class="btn buy-btn-clr" target="_blank" >Buy Now</a>
						</p>
						<div class="card-footer">
							<small class="text-muted">Last Updated: <?php echo esc_html( $csmm_thm_last_updated ); ?></small>
						</div>
					</div>
				</div>
				<?php
			} else {
				echo 'Failed to fetch theme information for ' . esc_html( $csmm_theme_slug ) . '<br>';
			}
		}
			// var_dump( $csmm_thm_extras_info );
		?>
	</div>
</div>