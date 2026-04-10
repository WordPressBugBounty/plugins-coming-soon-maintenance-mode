<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>



<div class="container-fluid">

	<div class="comisoma-extras-page shadow-lg bg-light h-100 p-3">
		<h1>Our Free Themes and Plugins</h1>
		<p>Thanks for choosing us. Please have a look at our various builds!</p>
	</div>

	<div class="comisoma-extras-title mt-3 mb-0">
		<h1>Plugins</h1>
	</div>
	<div class="row row-cols-1 row-cols-md-4 justify-content-center gap-2">
		<?php
		$comisoma_plugin_button      = '';
		$comisoma_frank_plugin_slugs = array( 
			'ultimate-portfolio', 'customizer-login-page', 'testimonial-maker', 
			'weather-effect', 'responsive-slider-gallery', 'team-builder-member-showcase',
			'modal-popup-box', 'wp-instagram-feed-awplife', 'abc-pricing-table',
			//'wp-flickr-gallery', 'new-album-gallery', 'blog-filter', 'event-monster', 'new-grid-gallery',
			//'new-image-gallery', 'media-slider', 'new-photo-gallery', 
			//  'slider-responsive-slideshow',
			// 'new-video-gallery', 'portfolio-filter-gallery',
		);

		// Required Info to Fetch Plugin Data.
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/theme-install.php';

		foreach ( $comisoma_frank_plugin_slugs as $comisoma_plugin_slug ) {
			// Define the arguments for the plugins_api function.
			$comisoma_args = array(
				'slug'   => $comisoma_plugin_slug,
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
			$comisoma_extras_info = plugins_api( 'plugin_information', $comisoma_args );

			// Proceed if plugin data is available.
			if ( ! is_wp_error( $comisoma_extras_info ) ) {
				$comisoma_extras_name              = $comisoma_extras_info->name;
				$comisoma_extras_rating            = $comisoma_extras_info->rating;
				$comisoma_extras_installs          = $comisoma_extras_info->active_installs;
				$comisoma_extras_last_updated      = $comisoma_extras_info->last_updated;
				$comisoma_extras_icons             = $comisoma_extras_info->icons;
				$comisoma_extras_author            = $comisoma_extras_info->author;
				$comisoma_extras_ratings           = $comisoma_extras_info->ratings;
				$comisoma_extras_downloaded        = $comisoma_extras_info->downloaded;
				$comisoma_extras_short_description = $comisoma_extras_info->short_description;

				// Fetch image source and name from icons.
				$comisoma_src = isset( $comisoma_extras_icons['2x'] ) ? $comisoma_extras_icons['2x'] : ( isset( $comisoma_extras_icons['1x'] ) ? $comisoma_extras_icons['1x'] : '' );
				$comisoma_alt = esc_html( $comisoma_extras_name ) . ( isset( $comisoma_extras_icons['2x'] ) ? ' 2x' : ( isset( $comisoma_extras_icons['1x'] ) ? ' 1x' : '' ) );

				// Iterate through the ratings and construct the star rating HTML.
				// Calculate the average rating.
				$comisoma_total_ratings = 0;
				$comisoma_total_count   = 0;

				foreach ( $comisoma_extras_ratings as $comisoma_rating => $comisoma_count ) {
					$comisoma_total_ratings += ( $comisoma_rating * $comisoma_count );
					$comisoma_total_count   += $comisoma_count;
				}

				$comisoma_average_rating = round( $comisoma_total_count > 0 ? $comisoma_total_ratings / $comisoma_total_count : 0 );

				// Determine the number of filled stars and half star based on the average rating.
				$comisoma_filled_stars = floor( $comisoma_average_rating );
				$comisoma_half_star    = round( $comisoma_average_rating - $comisoma_filled_stars, 1 ) >= 0.5;

				// Construct the star rating HTML.
				$comisoma_star_rating_html = '<div class="wporg-ratings" title="' . $comisoma_average_rating . ' out of 5 stars" style="color:#ffb900;">';

				// Filled stars.
				for ( $comisoma_i = 0; $comisoma_i < $comisoma_filled_stars; $comisoma_i++ ) {
					$comisoma_star_rating_html .= '<span class="dashicons dashicons-star-filled"></span>';
				}

				// Half star.
				if ( $comisoma_half_star ) {
					$comisoma_star_rating_html .= '<span class="dashicons dashicons-star-half"></span>';
				}

				// Empty stars.
				for ( $comisoma_i = 0; $comisoma_i < 5 - $comisoma_filled_stars - $comisoma_half_star; $comisoma_i++ ) {
					$comisoma_star_rating_html .= '<span class="dashicons dashicons-star-empty"></span>';
				}

				$comisoma_star_rating_html .= '</div>';
				// Finished Stars.

				if ( $comisoma_plugin_slug === 'slider-factory' ) {
					// Trim the name to 7 words.
					$comisoma_name_words  = explode( ' ', $comisoma_extras_name );
					$comisoma_extras_name = implode( ' ', array_slice( $comisoma_name_words, 0, 2 ) );
				}


				/** Install Update Button */
				// Set the plugin slug and other installation information.
				$comisoma_plugin_info = $comisoma_extras_info;

				// Check if the plugin information is available.
				if ( ! is_wp_error( $comisoma_plugin_info ) ) {
					// Check the installation status.
					$comisoma_plugin_install_status = install_plugin_install_status( $comisoma_plugin_info );

					// Generate installation link based on the installation status.
					switch ( $comisoma_plugin_install_status['status'] ) {
						case 'install':
							$comisoma_plugin_button = '<button href="#" class="btn btn-lg btn-lg btn-dark" onclick="comisomaAjaxRequest(\'' . esc_html( $comisoma_plugin_slug ) . '\', \'comisoma_plugin_install\', this)">Install Now</button>';
							break;
						case 'update_available':
							$comisoma_plugin_button = '<button href="#" class="btn btn-lg btn-dark" onclick="comisomaAjaxRequest(\'' . esc_html( $comisoma_plugin_slug ) . '\', \'comisoma_plugin_update\', this)">Update Now</button>';
							break;
						case 'latest_installed':
						case 'newer_installed':
							// Plugin is already installed and up to date.
							$comisoma_plugin_name = $comisoma_plugin_slug . '/' . $comisoma_plugin_slug . '.php';
							if ( is_plugin_active( $comisoma_plugin_name ) ) {
								$comisoma_plugin_button = '<button type="button" class="btn btn-lg btn-success" disabled>Installed/Activated</button>';
							} else {
								$comisoma_plugin_button = '<button href="#" class="btn btn-lg btn-dark" onclick="comisomaAjaxRequest(\'' . esc_html( $comisoma_plugin_slug ) . '\', \'comisoma_plugin_activate\', this)">Activate Now</button>';
							}
							break;
					}
				}
				/** End Install Update Button */
				?>
				<div class="col">
					<div class="card shadow-lg bg-light h-100">
					<img class="img-thumbnail rounded m-5 p-5" src="<?php echo esc_url( $comisoma_src ); ?>" alt="<?php echo esc_attr( $comisoma_alt ); ?>">
						<div class="card-body">
							<h5 class="card-title"><?php echo esc_html( $comisoma_extras_name ); ?></h5>
							<?php echo wp_kses_post( $comisoma_star_rating_html ); ?>
							<p class="card-text card-formating"><?php echo esc_html( $comisoma_extras_short_description ); ?></p>
							<p class="card-text card-formating"><b>Total Downloads :</b> <?php echo esc_html( number_format_i18n( $comisoma_extras_downloaded ) ); ?>+</p>
							<!--<p class="card-text card-formating"><b>Author :</b> <?php //echo wp_kses_post( $comisoma_extras_author, ); ?></p>-->
						</div>
						<?php
						$comisoma_allowed_tags = array(
							'button' => array(
								'href'    => array(),
								'onclick' => array(),
								'class'   => array(),
							),
						);
						?>
						<p class="card-text text-center mt-1"> <?php echo wp_kses( $comisoma_plugin_button, $comisoma_allowed_tags ); ?></p>
						<div class="card-footer">
							<small class="text-muted">Last Updated: <?php echo esc_html( $comisoma_extras_last_updated ); ?></small>
						</div>
					</div>
				</div>
				<?php
			} else {
				echo 'Failed to fetch plugin information for ' . esc_html( $comisoma_plugin_slug ) . '<br>';
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
		$comisoma_theme_button      = '';
		$comisoma_frank_theme_slugs = array( 
		'webenvo', 'metaverse', 'medihealth',
		'formula', 'formula-dark', 'formula-light',
		'medical-formula', 'business-campaign', 'cryptocurrency-exchange',
		//'nature-formula', 'home-interior', 'education-formula', 'dental-hospital', 
		//'hospital-health-care', 'awp-marketing-agency', 'aneeq', 'dental-hospital', 'awpbusinesspress', 
		//'bloglane', 'blush', 'business-campaign',  'timelineblog', 'coin-market', 
		//'cryptostore', 'newsstreet', 'business-blogs', 'daron'
		);
		// Fetch theme data.
		foreach ( $comisoma_frank_theme_slugs as $comisoma_theme_slug ) {
			$comisoma_thm_args = array(
				'slug'   => $comisoma_theme_slug,
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

			$comisoma_thm_extras_info = themes_api( 'theme_information', $comisoma_thm_args );

			// Proceed if theme data is available.
			if ( ! is_wp_error( $comisoma_thm_extras_info ) ) {
				$comisoma_thm_name           = $comisoma_thm_extras_info->name;
				$comisoma_thm_downloaded     = $comisoma_thm_extras_info->downloaded;
				$comisoma_thm_theme_url      = $comisoma_thm_extras_info->theme_url;
				$comisoma_thm_installs       = $comisoma_thm_extras_info->active_installs;
				$comisoma_thm_screenshot_url = $comisoma_thm_extras_info->screenshot_url;
				$comisoma_thm_last_updated   = $comisoma_thm_extras_info->last_updated;
				$comisoma_thm_num_ratings    = $comisoma_thm_extras_info->num_ratings;
				$comisoma_thm_rating         = $comisoma_thm_extras_info->rating;
				$comisoma_thm_ratings        = $comisoma_thm_extras_info->ratings;
				$comisoma_thm_version        = $comisoma_thm_extras_info->version;
				$comisoma_thm_preview_url    = $comisoma_thm_extras_info->preview_url;
				$comisoma_thm_author         = $comisoma_thm_extras_info->author;
				// var_dump( $comisoma_thm_preview_url  );
				if ( isset( $comisoma_thm_extras_info->parent ) ) {
					// Parent property exists.
					$comisoma_thm_parent = $comisoma_thm_extras_info->parent;
				}

				// Iterate through the ratings and construct the star rating HTML.
				// Calculate the average rating.
				$comisoma_total_ratings = 0;
				$comisoma_total_count   = 0;

				foreach ( $comisoma_thm_ratings as $comisoma_rating => $comisoma_count ) {
					$comisoma_total_ratings += ( $comisoma_rating * $comisoma_count );
					$comisoma_total_count   += $comisoma_count;
				}

				$comisoma_average_rating = $comisoma_total_count > 0 ? $comisoma_total_ratings / $comisoma_total_count : 0;

				// Determine the number of filled stars and half star based on the average rating.
				$comisoma_filled_stars = floor( $comisoma_average_rating );
				$comisoma_half_star    = round( $comisoma_average_rating - $comisoma_filled_stars, 1 ) >= 0.5;

				// Construct the star rating HTML.
				$comisoma_star_rating_html = '<div class="wporg-ratings" title="' . $comisoma_average_rating . ' out of 5 stars" style="color:#ffb900;">';

				// Filled stars.
				for ( $comisoma_i = 0; $comisoma_i < $comisoma_filled_stars; $comisoma_i++ ) {
					$comisoma_star_rating_html .= '<span class="dashicons dashicons-star-filled"></span>';
				}

				// Half star.
				if ( $comisoma_half_star ) {
					$comisoma_star_rating_html .= '<span class="dashicons dashicons-star-half"></span>';
				}

				// Empty stars.
				for ( $comisoma_i = 0; $comisoma_i < 5 - $comisoma_filled_stars - $comisoma_half_star; $comisoma_i++ ) {
					$comisoma_star_rating_html .= '<span class="dashicons dashicons-star-empty"></span>';
				}

				$comisoma_star_rating_html .= '</div>';
				// Finished Stars.

				// Get the theme information.
				$comisoma_theme_info = wp_get_theme( $comisoma_theme_slug );

				// Check if the theme directory exists.
				$comisoma_is_installed = is_dir( get_theme_root() . '/' . $comisoma_theme_slug );

				// Check if the theme information is available and the theme is installed.
				if ( $comisoma_theme_info->exists() && $comisoma_is_installed ) {
					$comisoma_current_theme   = wp_get_theme();
					$comisoma_is_active       = $comisoma_current_theme->get_stylesheet() === $comisoma_theme_info->get_stylesheet();
					$comisoma_current_version = $comisoma_theme_info->get( 'Version' );
					$comisoma_latest_version  = $comisoma_thm_version;

					// Generate buttons based on the installation, update, and activation status.
					if ( version_compare( $comisoma_current_version, $comisoma_latest_version, '<' ) ) {
						$comisoma_theme_button = '<button href="#" class="btn btn-dark" onclick="comisomaAjaxRequest(\'' . esc_html( $comisoma_theme_slug ) . '\', \'comisoma_theme_update\', this)">Update Now</button>';
					} elseif ( $comisoma_is_active ) {
						$comisoma_theme_button = '<button type="button" class="btn btn-success" disabled>Active</button>';
					} else {
						$comisoma_theme_button = '<a href="' . esc_url( admin_url( 'themes.php' ) ) . '" class="btn btn-dark">Activate Now</a>';
					}
				} else {
					$comisoma_theme_button = '<button href="#" class="btn btn-dark" onclick="comisomaAjaxRequest(\'' . esc_html( $comisoma_theme_slug ) . '\', \'comisoma_theme_install\', this)">Install Free</button>';
				}
				// Construct the theme card HTML.
				?>
				<div class="col">
					<div class="card shadow-lg bg-light h-100">
						<img class="card-img-top rounded" src="<?php echo esc_url( $comisoma_thm_screenshot_url ); ?>" alt="<?php echo esc_attr( $comisoma_thm_name ); ?>">
						<div class="card-body">
							<h5 class="card-title"><?php echo esc_html( $comisoma_thm_name ); ?></h5>
							<?php //if ( ! empty( $comisoma_thm_parent ) ) { ?>
								<!--<p class="card-text card-formating"><b>Child theme of : </b><?php echo esc_html( $comisoma_thm_parent['name'] ); ?></p>-->
							<?php //} ?>
							<?php echo wp_kses_post( $comisoma_star_rating_html ); ?>
							<p class="card-text card-formating"><b>Total Downloads : </b><?php echo esc_html( number_format_i18n( $comisoma_thm_downloaded ) ); ?>+</p>
							<!--<p class="card-text card-formating"><b>Author :</b> <a href="<?php echo esc_url( $comisoma_thm_author['profile'] ); ?>" ><?php echo esc_html( $comisoma_thm_author['display_name'], ); ?></a> </p>-->
							<!--<p class="card-text card-formating"><b>Author Web:</b> <a href="<?php echo esc_url( $comisoma_thm_author['author_url'] ); ?>" ><?php echo esc_html( $comisoma_thm_author['author_url'], ); ?></a> </p>-->
						</div>
							<?php
							$comisoma_thm_allowed_tags = array(
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
							<?php echo wp_kses( $comisoma_theme_button, $comisoma_thm_allowed_tags ); ?>
							<?php
							switch ( $comisoma_thm_name ) {
								case 'Webenvo':
									$comisoma_thm_demo_link = 'https://webenvo.com/premium-themes/webenvo-pro/';
									$comisoma_thm_buy_link  = 'https://webenvo.com/member/signup/webenvo-premium';
									break;
								case 'Formula':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'Formula Dark':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'Formula Light':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'Medical Formula':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								
								case 'Metaverse':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/formula-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/formula-pro';
									break;
								case 'MediHealth':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/medihealth-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/medihealth-premium';
									break;
								case 'Business Campaign':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/wpbusinesspress-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/wpbusinesspress-premium';
									break;
								case 'Cryptocurrency Exchange':
									$comisoma_thm_demo_link = 'https://awplife.com/wordpress-themes/crypto-premium/';
									$comisoma_thm_buy_link  = 'https://awplife.com/account/signup/crypto-premium';
									break;
								
							}
							?>
							<a href="<?php echo esc_url( $comisoma_thm_demo_link ); ?>" type="button" class="btn dm-btn-clr" target="_blank" >Pro Demo</a>
							<a href="<?php echo esc_url( $comisoma_thm_buy_link ); ?>" type="button" class="btn buy-btn-clr" target="_blank" >Buy Now</a>
						</p>
						<div class="card-footer">
							<small class="text-muted">Last Updated: <?php echo esc_html( $comisoma_thm_last_updated ); ?></small>
						</div>
					</div>
				</div>
				<?php
			} else {
				echo 'Failed to fetch theme information for ' . esc_html( $comisoma_theme_slug ) . '<br>';
			}
		}
			// var_dump( $comisoma_thm_extras_info );
		?>
	</div>
</div>
