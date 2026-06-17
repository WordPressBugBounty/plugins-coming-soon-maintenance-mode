<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// get current plugin version
$comisoma_current_version = get_option( 'comisoma_current_version' );
$comisoma_last_version = get_option( 'comisoma_last_version' );

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
$comisoma_logo_url[0] = "";
$comisoma_title = sanitize_text_field("Coming Soon");
$comisoma_description = sanitize_text_field("Thank you for visiting our website! We are currently working on creating a new and exciting online experience for you. While we finish up the final touches, please sign up for our newsletter to receive exclusive updates and offers.");
$comisoma_countdown = 1;
$comisoma_countdown_title = sanitize_text_field("Launching In...");
$comisoma_current_date = gmdate('Y-m-d');
$comisoma_launch_dt = gmdate('Y-m-d', strtotime($comisoma_current_date . ' +1 month'));
$comisoma_countdown_time = wp_date('H:i');
$comisoma_sm_facebook = $comisoma_sm_twitter = $comisoma_sm_instagram = "#";

//load CSMM settings
$comisoma_settings = get_option('comisoma_settings');
//print_r($comisoma_settings);
if(is_array($comisoma_settings)){
	if(array_key_exists('website_mode', $comisoma_settings)){ $comisoma_website_mode = $comisoma_settings['website_mode']; }
	if(array_key_exists('selected_posts', $comisoma_settings)){ $comisoma_posts = $comisoma_settings['selected_posts']; }
	if(array_key_exists('selected_pages', $comisoma_settings)){ $comisoma_pages = $comisoma_settings['selected_pages']; }
	if(array_key_exists('selected_other_pages', $comisoma_settings)){ $comisoma_other_pages = $comisoma_settings['selected_other_pages']; }
}
//load CSMM templates
$comisoma_templates = get_option('comisoma_templates');
if(is_array($comisoma_templates)){
	if(array_key_exists('template_id', $comisoma_templates)){ $comisoma_template_id = $comisoma_templates['template_id']; }
}
//load CSMM content
$comisoma_content = get_option('comisoma_content');
if(is_array($comisoma_content)){
	if(array_key_exists('logo', $comisoma_content)){ 
		$comisoma_logo_id = $comisoma_content['logo']; 
		$comisoma_logo_url = wp_get_attachment_image_src($comisoma_logo_id, 'medium', true); // attachment medium URL
	}
	if(array_key_exists('title', $comisoma_content)){ $comisoma_title = $comisoma_content['title']; }
	if(array_key_exists('description', $comisoma_content)){ $comisoma_description = $comisoma_content['description']; }
	if(array_key_exists('countdown', $comisoma_content)){ $comisoma_countdown = $comisoma_content['countdown']; }
	if(array_key_exists('countdown_title', $comisoma_content)){ $comisoma_countdown_title = $comisoma_content['countdown_title']; }
	if(array_key_exists('countdown_date', $comisoma_content)){ $comisoma_countdown_date = $comisoma_content['countdown_date']; }
	if(array_key_exists('countdown_time', $comisoma_content)){ $comisoma_countdown_time = $comisoma_content['countdown_time']; }
	if(array_key_exists('susbcriber_form', $comisoma_content)){ $comisoma_susbcriber_form = $comisoma_content['susbcriber_form']; }
	if(array_key_exists('video_url', $comisoma_content)){ $comisoma_video_url = $comisoma_content['video_url']; }
	if(array_key_exists('slide_ids', $comisoma_content)){ $comisoma_slide_ids = $comisoma_content['slide_ids']; }

	
	// launch date calculation
	//date_default_timezone_set(wp_timezone_string());
	$comisoma_launch_date = gmdate('F d, Y', strtotime($comisoma_countdown_date));
	$comisoma_launch_time = gmdate('H:i:s', strtotime($comisoma_countdown_time));
	$comisoma_launch_dt = $comisoma_launch_date." ".$comisoma_launch_time; // March 25, 2024 15:37:25
}

// load social media
$comisoma_social_media = get_option('comisoma_social_media');
if(is_array($comisoma_social_media)){
	if(array_key_exists('comisoma_sm_facebook', $comisoma_social_media)){ $comisoma_sm_facebook = $comisoma_social_media['comisoma_sm_facebook']; }
	if(array_key_exists('comisoma_sm_twitter', $comisoma_social_media)){ $comisoma_sm_twitter = $comisoma_social_media['comisoma_sm_twitter']; }
	if(array_key_exists('comisoma_sm_youtube', $comisoma_social_media)){ $comisoma_sm_youtube = $comisoma_social_media['comisoma_sm_youtube']; }
	if(array_key_exists('comisoma_sm_instagram', $comisoma_social_media)){ $comisoma_sm_instagram = $comisoma_social_media['comisoma_sm_instagram']; }
	if(array_key_exists('comisoma_sm_linkedin', $comisoma_social_media)){ $comisoma_sm_linkedin = $comisoma_social_media['comisoma_sm_linkedin']; }
	if(array_key_exists('comisoma_sm_pinterest', $comisoma_social_media)){ $comisoma_sm_pinterest = $comisoma_social_media['comisoma_sm_pinterest']; }
	if(array_key_exists('comisoma_sm_tumblr', $comisoma_social_media)){ $comisoma_sm_tumblr = $comisoma_social_media['comisoma_sm_tumblr']; }
	if(array_key_exists('comisoma_sm_snapchat', $comisoma_social_media)){ $comisoma_sm_snapchat = $comisoma_social_media['comisoma_sm_snapchat']; }
	if(array_key_exists('comisoma_sm_behance', $comisoma_social_media)){ $comisoma_sm_behance = $comisoma_social_media['comisoma_sm_behance']; }
	if(array_key_exists('comisoma_sm_dribbble', $comisoma_social_media)){ $comisoma_sm_dribbble = $comisoma_social_media['comisoma_sm_dribbble']; }
	if(array_key_exists('comisoma_sm_whatsapp', $comisoma_social_media)){ $comisoma_sm_whatsapp = $comisoma_social_media['comisoma_sm_whatsapp']; }
	if(array_key_exists('comisoma_sm_tiktok', $comisoma_social_media)){ $comisoma_sm_tiktok = $comisoma_social_media['comisoma_sm_tiktok']; }
	if(array_key_exists('comisoma_sm_qq', $comisoma_social_media)){ $comisoma_sm_qq = $comisoma_social_media['comisoma_sm_qq']; }
}
?>
<div class="m-3">


<div class="contain">
	<div class="row" style="--bs-gutter-x: 0rem;">
		<div class="col-md-6 p-2">
			<h3 class="float-start"><?php esc_html_e( 'Coming Soon Maintenance Mode', 'coming-soon-maintenance-mode' ); ?>
			<small>
			<?php
			if ( $comisoma_current_version != '' ) {
				echo esc_html( 'v' );
				echo esc_html($comisoma_current_version);
			}
			?>
			</small>
			</h3>
		</div>
		<div class="col-md-6 p-2 sticky">
			<div class="website-mode-top btn-group d-none" role="group" aria-label="Basic radio toggle button group">
				<input onclick="return comisoma_save('website-mode-top', '');" type="radio" class="btn-check" name="website-mode-top" id="website-mode-top1" value="1" autocomplete="off" <?php if($comisoma_website_mode == 1 ) echo esc_attr("checked"); ?>>
				<label class="btn btn-lg btn-outline-danger" for="website-mode-top1"><?php esc_html_e( 'Coming Soon', 'coming-soon-maintenance-mode' ); ?></label>
				<input onclick="return comisoma_save('website-mode-top', '');" type="radio" class="btn-check" name="website-mode-top" id="website-mode-top2" value="2" autocomplete="off" <?php if($comisoma_website_mode == 2 ) echo esc_attr("checked"); ?>>
				<label class="btn btn-lg btn-outline-danger" for="website-mode-top2"><?php esc_html_e( 'Maintenance', 'coming-soon-maintenance-mode' ); ?></label>
				<input onclick="return comisoma_save('website-mode-top', '');" type="radio" class="btn-check" name="website-mode-top" id="website-mode-top3" value="3" autocomplete="off" <?php if($comisoma_website_mode == 3 ) echo esc_attr("checked"); ?>>
				<label class="btn btn-lg btn-outline-danger" for="website-mode-top3"><?php esc_html_e( 'Live', 'coming-soon-maintenance-mode' ); ?></label>
			</div>
			<a id="comisoma-live-preview" href="<?php echo esc_url( get_site_url().'/?preview=true&csmm=true' ); ?>" target="framename" class="btn btn-lg btn-outline-warning float-end"><strong><?php esc_html_e( 'Check Live Preview', 'coming-soon-maintenance-mode' ); ?></strong></a>
		</div>
		<div class="col-md-12 p-2 border">
			<div class="col-md-12 p-2">
				<nav>
					<div class="nav nav-tabs" id="nav-tab" role="tablist">
						<button class="nav-link active" id="nav-settings-tab" data-bs-toggle="tab" data-bs-target="#nav-settings" type="button" role="tab" aria-controls="nav-settings" aria-selected="true"><?php esc_html_e( 'Website Mode', 'coming-soon-maintenance-mode' ); ?></button>
						<button class="nav-link" id="nav-templates-tab" data-bs-toggle="tab" data-bs-target="#nav-templates" type="button" role="tab" aria-controls="nav-templates" aria-selected="false"><?php esc_html_e( 'Templates', 'coming-soon-maintenance-mode' ); ?></button>
						<button class="nav-link" id="nav-content-tab" data-bs-toggle="tab" data-bs-target="#nav-content" type="button" role="tab" aria-controls="nav-content" aria-selected="false"><?php esc_html_e( 'Settings', 'coming-soon-maintenance-mode' ); ?></button>
						<button class="nav-link" id="nav-social-media-tab" data-bs-toggle="tab" data-bs-target="#nav-social-media" type="button" role="tab" aria-controls="nav-social-media" aria-selected="false"><?php esc_html_e( 'Social Media', 'coming-soon-maintenance-mode' ); ?></button>
						<button class="nav-link" id="nav-tools-tab" data-bs-toggle="tab" data-bs-target="#nav-tools" type="button" role="tab" aria-controls="nav-tools" aria-selected="false"><?php esc_html_e( 'Tools', 'coming-soon-maintenance-mode' ); ?></button>
						<button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#nav-docs" type="button" role="tab" aria-controls="nav-docs" aria-selected="false"><?php esc_html_e( 'Docs', 'coming-soon-maintenance-mode' ); ?></button>
					</div>
				</nav>
				<div class="tab-content" id="nav-tabContent">
					
					<div class="tab-pane fade show active" id="nav-settings" role="tabpanel" aria-labelledby="nav-settings-tab" tabindex="0">
						<div class="row">
							<div class="col-md-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Website Mode', 'coming-soon-maintenance-mode' ); ?></h5>
								<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
									<input type="radio" class="btn-check" name="website-mode" id="website-mode1" value="1" autocomplete="off" <?php if($comisoma_website_mode == 1 ) echo esc_attr("checked"); ?>>
									<label class="btn btn-lg btn-outline-secondary" for="website-mode1"><?php esc_html_e( 'Coming Soon', 'coming-soon-maintenance-mode' ); ?></label>
									<input type="radio" class="btn-check" name="website-mode" id="website-mode2" value="2" autocomplete="off" <?php if($comisoma_website_mode == 2 ) echo esc_attr("checked"); ?>>
									<label class="btn btn-lg btn-outline-secondary" for="website-mode2"><?php esc_html_e( 'Maintenance', 'coming-soon-maintenance-mode' ); ?></label>
									<input type="radio" class="btn-check" name="website-mode" id="website-mode3" value="3" autocomplete="off" <?php if($comisoma_website_mode == 3 ) echo esc_attr("checked"); ?>>
									<label class="btn btn-lg btn-outline-secondary" for="website-mode3"><?php esc_html_e( 'Live', 'coming-soon-maintenance-mode' ); ?></label>
								</div>
								<br><br>
								<div>
									<h5 class="mt-3"><?php esc_html_e( 'Video Tutorial - Check How Plugin Works', 'coming-soon-maintenance-mode' ); ?></h5>
									<iframe width="100%" height="415" src="https://www.youtube.com/embed/HYVdcnvPi08" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
								</div>
							</div>
							<div class="col-md-6 p-2 mt-3 border">
								<h5 class=""><?php esc_html_e( 'Website Mode Tips', 'coming-soon-maintenance-mode' ); ?> <i class="fa-solid fa-circle-info m-1"></i></h5>
								<div>
									<div id="comisoma-website-mode-info" class="form-text"><strong><?php esc_html_e( 'What are the website modes?', 'coming-soon-maintenance-mode' ); ?></strong></div>
									<div id="comisoma-website-mode-info" class="form-text">
										<?php esc_html_e( 'Website mode defines the status of the website, such as whether it is available to access for visitors or not.', 'coming-soon-maintenance-mode' ); ?>
									</div>
									<div id="comisoma-website-mode-info" class="form-text">
										<strong><?php esc_html_e( 'Coming Soon', 'coming-soon-maintenance-mode' ); ?></strong> - <?php esc_html_e( 'this mode is used to inform visitors that a website is under construction or coming soon. Expect an update from the administrator.', 'coming-soon-maintenance-mode' ); ?>
									</div>
									<div id="comisoma-website-mode-info" class="form-text">
										<strong><?php esc_html_e( 'Maintenance', 'coming-soon-maintenance-mode' ); ?></strong> - <?php esc_html_e( 'this mode is a way for website administrators to temporarily disable access to the particular part of website while it is being updated or undergoing maintenance.', 'coming-soon-maintenance-mode' ); ?>
									</div>
									<div id="comisoma-website-mode-info" class="form-text">
										<strong><?php esc_html_e( 'Live', 'coming-soon-maintenance-mode' ); ?></strong> - <?php esc_html_e( 'this mode means that the website is accessible and available for anyone to visit on the internet.', 'coming-soon-maintenance-mode' ); ?>
									</div>
									<div id="comisoma-website-mode-info" class="form-text">
										<span class="badge text-bg-danger"><?php esc_html_e( 'Important Note', 'coming-soon-maintenance-mode' ); ?></span><br>
										<strong><?php esc_html_e( 'Check Live Preview', 'coming-soon-maintenance-mode' ); ?></strong> <?php esc_html_e( 'button is used to check the website as a visitor only for admin.', 'coming-soon-maintenance-mode' ); ?>
										<?php esc_html_e( 'Coming Soon and Maintenance Mode will not work for admin.', 'coming-soon-maintenance-mode' ); ?>
									</div>
								</div>
							</div>
							<div id="maintenance-mode-options" class="row" style="<?php if($comisoma_website_mode != 2 ) echo esc_attr("display:none"); ?>">
								<div class="col-md-6 p-2 mt-3 border bg-light">
									<h5 class=""><?php esc_html_e( 'Maintenance Mode Options', 'coming-soon-maintenance-mode' ); ?></h5>
								</div>
								<div class="col-md-6 p-2 mt-3 border">
									<h5 class=""><?php esc_html_e( 'Maintenance Mode Options Tips', 'coming-soon-maintenance-mode' ); ?> <i class="fa-solid fa-circle-info m-1"></i></h5>
									<div id="comisoma-maintenance-mode-info-1" class="form-text">
										<?php esc_html_e( 'If you need to perform updates on a specific part of your website, you can use the option below to display a maintenance message to visitors on specific posts and pages. All other parts of your website will remain accessible during this time.', 'coming-soon-maintenance-mode' ); ?>
									</div>
								</div>
								<div class="col-md-4 p-2 border bg-light">
									<h6 class=""><?php esc_html_e( 'Select Posts', 'coming-soon-maintenance-mode' ); ?></h6>
									<div class="">
										<?php $posts = get_posts(); ?>
										<div>
										<input type="checkbox" id="check-all-posts" name="comisoma_posts" value="-1" <?php if(in_array( -1, $comisoma_posts)) echo esc_attr("checked"); ?> />&nbsp;&nbsp;<strong><?php esc_html_e('Select All', 'coming-soon-maintenance-mode' ); ?></strong>
										</div>
										<?php foreach ( $posts as $post ) { ?>
										<div>
											<input type="checkbox" class="comisoma-post-list" name="comisoma_posts" value="<?php echo esc_attr($post->ID); ?>" <?php if(in_array( $post->ID, $comisoma_posts)) echo esc_attr("checked"); ?> />&nbsp;&nbsp;<?php echo esc_html($post->post_title); ?>
										</div>
										<?php } ?>
									</div>
								</div>
								<div class="col-md-4 p-2 border bg-light">
									<h6 class=""><?php esc_html_e( 'Select Pages', 'coming-soon-maintenance-mode' ); ?></h6>
									<div class="">
										<?php $pages = get_pages(); ?>
										<div>
											<input type="checkbox" id="check-all-pages" name="comisoma_pages" value="-1" <?php if(in_array( -1, $comisoma_pages)) echo esc_attr("checked"); ?> />&nbsp;&nbsp;<strong><?php esc_html_e('Select All', 'coming-soon-maintenance-mode'); ?></strong>
										</div>
										<?php foreach ( $pages as $page ) { ?>
										<div>
											<input type="checkbox" class="comisoma-page-list" name="comisoma_pages" value="<?php echo esc_attr( $page->ID ); ?>" <?php if ( in_array( $page->ID, $comisoma_pages ) ) echo 'checked'; ?> />&nbsp;&nbsp;<?php echo esc_html( $page->post_title ); ?>										</div>
										<?php } ?>
									</div>
								</div>
								<div class="col-md-4 p-2 border bg-light">
									<h6 class=""><?php esc_html_e( 'Select Other Pages', 'coming-soon-maintenance-mode' ); ?></h6>
									<div class="">
										<div>
										<input type="checkbox" id="check-all-other-pages" name="comisoma_other_pages" value="-1" <?php if(in_array( -1, $comisoma_other_pages)) echo esc_attr("checked"); ?> />&nbsp;&nbsp;<strong><?php esc_html_e('Select All', 'coming-soon-maintenance-mode'); ?></strong>
										</div>
										<div>
										<input type="checkbox" class="comisoma-other-page-list" name="comisoma_other_pages" value="front" <?php if(in_array( "front", $comisoma_other_pages)) echo esc_attr("checked"); ?> />&nbsp;&nbsp; <a href="https://developer.wordpress.org/reference/functions/is_front_page/" target="_blank"><?php esc_html_e('Front Page', 'coming-soon-maintenance-mode'); ?></a>
										</div>
										<div>
										<input type="checkbox" class="comisoma-other-page-list" name="comisoma_other_pages" value="home" <?php if(in_array( "home", $comisoma_other_pages)) echo esc_attr("checked"); ?> />&nbsp;&nbsp; <a href="https://developer.wordpress.org/reference/functions/is_home/" target="_blank"><?php esc_html_e('Home Page', 'coming-soon-maintenance-mode'); ?></a>
										</div>
										<div>
										<input type="checkbox" class="comisoma-other-page-list" name="comisoma_other_pages" value="category" <?php if(in_array( "category", $comisoma_other_pages)) echo esc_attr("checked"); ?> />&nbsp;&nbsp; <a href="https://developer.wordpress.org/reference/functions/is_category/" target="_blank"><?php esc_html_e('Category Page', 'coming-soon-maintenance-mode'); ?></a>
										</div>
										<div>
										<input type="checkbox" class="comisoma-other-page-list" name="comisoma_other_pages" value="tag" <?php if(in_array( "tag", $comisoma_other_pages)) echo esc_attr("checked"); ?> />&nbsp;&nbsp; <a href="https://developer.wordpress.org/reference/functions/is_tag/" target="_blank"><?php esc_html_e('Tag Page', 'coming-soon-maintenance-mode'); ?></a>
										</div>
										<div>
										<input type="checkbox" class="comisoma-other-page-list" name="comisoma_other_pages" value="search" <?php if(in_array( "search", $comisoma_other_pages)) echo esc_attr("checked"); ?> />&nbsp;&nbsp; <a href="https://developer.wordpress.org/reference/functions/is_search/" target="_blank"><?php esc_html_e('Search Page', 'coming-soon-maintenance-mode'); ?></a>
										</div>
									</div>
								</div>
							</div>
						
							<div class="col-md-12 p-2 mt-3">
								<div id="comisoma-setings-saving" class="col-md-12 p-2 mt-3 spinner-grow text-dark d-none" role="status">
									<span class="visually-hidden"></span>
								</div>
							</div>
							<div class="col-md-12 p-2 mt-3">
								<button type="button" id="comisoma-save-setings" name="comisoma-save-setings" class="btn btn-lg btn-outline-secondary" onclick="return comisoma_save('settings', '')"><i class="fa-regular fa-floppy-disk"></i> <?php esc_html_e( 'Save', 'coming-soon-maintenance-mode' ); ?></button>
							</div>
						</div><!-- settings tab row end-->
					</div><!-- settings tab content end-->
					
					<!-- templates tab content start-->
					<div class="tab-pane fade" id="nav-templates" role="tabpanel" aria-labelledby="nav-templates-tab" tabindex="0">
						<div class="col-md-12 p-2 mt-3">
							<h5 class=""><?php esc_html_e( 'Select Template', 'coming-soon-maintenance-mode' ); ?></h5>
							<div id="comisoma-templates-info-1" class="form-text"><?php esc_html_e( 'Click on activate button under the template for selection.', 'coming-soon-maintenance-mode' ); ?></div>
						</div>
						<div class="row">
							<div class="col-md-6 p-2">
								<div class="p-4">
									<img src="<?php echo esc_url( COMISOMA_URL.'admin/assets/img/1.webp'); ?>" class="w-100 h-100 rounded-4">
								</div>
								<div class="p-2 text-center">
									<button type="button" class="btn btn-lg btn-secondary" disabled><?php esc_html_e( 'Template 1', 'coming-soon-maintenance-mode' ); ?></button>
									<button type="button" class="btn btn-lg <?php if($comisoma_template_id == 1) echo esc_attr("btn-primary activated"); else echo esc_attr("btn-outline-primary activate"); ?> cmss-btn-template" id="comisoma-t1" value="1" onclick="return comisoma_save('templates', this.id);"><?php if($comisoma_template_id == 1) echo esc_attr("Activated"); else echo esc_attr("Activate"); ?></button>
								</div>
							</div>
							<div class="col-md-6 p-2">
								<div class="p-4">
									<img src="<?php echo esc_url( COMISOMA_URL.'admin/assets/img/4.webp'); ?>" class="w-100 h-100 rounded-4 border">
								</div>
								<div class="p-2 text-center">
									<button type="button" class="btn btn-lg btn-secondary" disabled><?php esc_html_e( 'Template 2', 'coming-soon-maintenance-mode' ); ?></button>
									<button type="button" class="btn btn-lg <?php if($comisoma_template_id == 4) echo esc_attr("btn-primary activated"); else echo esc_attr("btn-outline-primary activate"); ?> cmss-btn-template" id="comisoma-t4" value="4" onclick="return comisoma_save('templates', this.id);"><?php if($comisoma_template_id == 4) echo esc_attr("Activated"); else echo esc_attr("Activate"); ?></button>
								</div>
							</div>
							<div class="col-md-6 p-2">
								<div class="p-4">
									<img src="<?php echo esc_url( COMISOMA_URL.'admin/assets/img/8.webp'); ?>" class="w-100 h-100 rounded-4">
								</div>
								<div class="p-2 text-center">
									<button type="button" class="btn btn-lg btn-secondary" disabled><?php esc_html_e( 'Template 3', 'coming-soon-maintenance-mode' ); ?></button>
									<button type="button" class="btn btn-lg <?php if($comisoma_template_id == 8) echo esc_attr("btn-primary activated"); else echo esc_attr("btn-outline-primary activate"); ?> cmss-btn-template" id="comisoma-t8" value="8" onclick="return comisoma_save('templates', this.id);"><?php if($comisoma_template_id == 8) echo esc_attr("Activated"); else echo esc_attr("Activate"); ?></button>
								</div>
							</div>
							<div class="col-md-6 p-2">
								<div class="p-4">
									<img src="<?php echo esc_url( COMISOMA_URL.'admin/assets/img/11.webp'); ?>" class="w-100 h-100 rounded-4">
								</div>
								<div class="p-2 text-center">
									<button type="button" class="btn btn-lg btn-secondary" disabled><?php esc_html_e( 'Template 4', 'coming-soon-maintenance-mode' ); ?></button>
									<button type="button" class="btn btn-lg <?php if($comisoma_template_id == 11) echo esc_attr("btn-primary activated"); else echo esc_attr("btn-outline-primary activate"); ?> cmss-btn-template" id="comisoma-t11" value="11" onclick="return comisoma_save('templates', this.id);"><?php if($comisoma_template_id == 11) echo esc_attr("Activated"); else echo esc_attr("Activate"); ?></button>
								</div>
							</div>
							<div class="col-md-6 p-2">
								<div class="p-4">
									<img src="<?php echo esc_url( COMISOMA_URL.'admin/assets/img/15.webp'); ?>" class="w-100 h-100 rounded-4">
								</div>
								<div class="p-2 text-center">
									<button type="button" class="btn btn-lg btn-secondary" disabled><?php esc_html_e( 'Template 5', 'coming-soon-maintenance-mode' ); ?></button>
									<button type="button" class="btn btn-lg <?php if($comisoma_template_id == 15) echo esc_attr("btn-primary activated"); else echo esc_attr("btn-outline-primary activate"); ?> cmss-btn-template" id="comisoma-t15" value="15" onclick="return comisoma_save('templates', this.id);"><?php if($comisoma_template_id == 15) echo esc_attr("Activated"); else echo esc_attr("Activate"); ?></button>
								</div>
							</div>
							<div class="col-md-12 p-2">
								<div class="d-grid gap-2">
									<a class="btn btn-lg btn-danger" target="_blank" href="https://wpfrank.com/demo/coming-soon-maintenance-mode-pro">Check More 35 Pro Templates</a>
									<a class="btn btn-lg btn-info" target="_blank" href="https://wordpress.org/plugins/coming-soon-maintenance-mode/">Rate <i class="fa-solid fa-star"></i> and Share Feedback <i class="fa-solid fa-comment-dots"></i> on <i class="fa-brands fa-wordpress"></i> if you like our plugin.</a>
								</div>
							</div>
						</div>
					</div><!-- templates tab content end-->
					
					<div class="tab-pane fade p-2" id="nav-content" role="tabpanel" aria-labelledby="nav-content-tab" tabindex="0">
						<div class="row">
							<div class="col-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Logo', 'coming-soon-maintenance-mode' ); ?></h5>
								<div>
									<ul id="comisoma-logo">
										<?php if($comisoma_logo_id) { ?>
										<li class="col-md-4 comisoma-logo-<?php echo esc_attr($comisoma_logo_id); ?>" data-position="<?php echo esc_attr($comisoma_logo_id); ?>">
											<input type="hidden" class="form-control comisoma-logo-id" id="comisoma-logo-id" name="comisoma-logo-id" value="<?php echo esc_attr($comisoma_logo_id); ?>">
											<img src="<?php echo esc_url($comisoma_logo_url[0]); ?>" class="img-thumbnail mt-3 bg-light">
											<div class="d-grid gap-2">
												<button type="button" id="comisoma-remove-logo" onclick="comisoma_save('remove-logo', <?php echo esc_attr($comisoma_logo_id); ?>);" class="btn btn-danger btn-block"><i class="fa-solid fa-trash"></i> <?php esc_html_e( 'Remove Logo', 'coming-soon-maintenance-mode' ); ?></button>
											</div>
										</li>
										<?php } ?>
									</ul>
									<div>
										<button type="button" id="comisoma-upload-logo" class="btn btn-secondary"><i class="fa-solid fa-upload"></i> <?php esc_html_e( 'Upload Logo', 'coming-soon-maintenance-mode' ); ?></button>
										<!--<button type="button" id="comisoma-remove-logo" class="btn btn-secondary mt-3" onclick="comisoma_save('remove-logo', '');"><i class="fa-solid fa-trash"></i> <?php esc_html_e( 'Remove Logo', 'coming-soon-maintenance-mode' ); ?></button>-->
									</div>
								</div>
							</div>
							<div class="col-6 p-2 mt-3 border">
								<h5 class=""><?php esc_html_e( 'Logo Tips', 'coming-soon-maintenance-mode' ); ?> <i class="fa-solid fa-circle-info m-1"></i></h5>
								<div>
									<div id="comisoma-upload-logo-info" class="form-text"><?php esc_html_e( 'Upload your logo for your website.', 'coming-soon-maintenance-mode' ); ?></div>
									<div id="comisoma-upload-logo-info" class="form-text"><?php esc_html_e( 'The recommended logo size is 300x170px.', 'coming-soon-maintenance-mode' ); ?></div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Title', 'coming-soon-maintenance-mode' ); ?></h5>
								<div>
									<input type="text" class="form-control" id="comisoma-title" name="comisoma-title" value="<?php echo esc_attr($comisoma_title); ?>">
								</div>
							</div>
							<div class="col-md-6 p-2 mt-3 border">
								<h5 class=""><?php esc_html_e( 'Title Tips', 'coming-soon-maintenance-mode' ); ?> <i class="fa-solid fa-circle-info m-1"></i></h5>
								<div>
									<div id="comisoma-title-info-1" class="form-text"><?php esc_html_e( 'Write a title for the website.', 'coming-soon-maintenance-mode' ); ?></div>
									<div id="comisoma-title-info-2" class="form-text"><?php esc_html_e( 'Leave it blank if you dont want to display it.', 'coming-soon-maintenance-mode' ); ?></div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Description', 'coming-soon-maintenance-mode' ); ?></h5>
								<div>
									<textarea id="comisoma-description" name="comisoma-description" class="form-control" rows="3" style="height:101px;"><?php echo esc_textarea(stripslashes($comisoma_description)); ?></textarea>
								</div>
							</div>
							<div class="col-md-6 p-2 mt-3 border">
								<h5 class=""><?php esc_html_e( 'Description Tips', 'coming-soon-maintenance-mode' ); ?> <i class="fa-solid fa-circle-info m-1"></i></h5>
								<div>
									<div id="comisoma-description-info" class="form-text"><?php esc_html_e( 'Write a brief description for the website.', 'coming-soon-maintenance-mode' ); ?></div>
									<div id="comisoma-title-info" class="form-text"><?php esc_html_e( 'Leave it blank if you dont want to display it.', 'coming-soon-maintenance-mode' ); ?></div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Countdown', 'coming-soon-maintenance-mode' ); ?></h5>
								<div>
									<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
										<input type="radio" class="btn-check" name="comisoma-countdown" id="comisoma-countdown1" value="1" autocomplete="off" <?php if($comisoma_countdown == 1 ) echo esc_attr("checked"); ?>>
										<label class="btn btn-outline-secondary" for="comisoma-countdown1"><?php esc_html_e( 'Yes', 'coming-soon-maintenance-mode' ); ?></label>
										<input type="radio" class="btn-check" name="comisoma-countdown" id="comisoma-countdown2" value="2" autocomplete="off" <?php if($comisoma_countdown == 2 ) echo esc_attr("checked"); ?>>
										<label class="btn btn-outline-secondary" for="comisoma-countdown2"><?php esc_html_e( 'No', 'coming-soon-maintenance-mode' ); ?></label>
									</div>
								</div>
								<div class="col-md-6 mt-3">
									<h6><?php esc_html_e( 'Timezone', 'coming-soon-maintenance-mode' ); ?></h6>
									<a href="options-general.php#timezone_string" target="_blank" id="comisoma-timezone" class="btn btn-secondary"><?php esc_html_e( 'set timezone', 'coming-soon-maintenance-mode' ); ?></a>
								</div>
								<div class="col-md-6 mt-3">
									<h6><?php esc_html_e( 'Countdown Heading', 'coming-soon-maintenance-mode' ); ?></h6>
									<input type="text" class="form-control" id="comisoma-countdown-title" name="comisoma-countdown-title" value="<?php echo esc_attr($comisoma_countdown_title); ?>">
								</div>
								<div class="col-md-6 mt-3">
									<h6><?php esc_html_e( 'Date', 'coming-soon-maintenance-mode' ); ?></h6>
									<input type="date" class="form-control" id="comisoma-countdown-date" name="comisoma-countdown-date" value="<?php echo esc_attr($comisoma_countdown_date); ?>">
								</div>
								<div class="col-md-6 mt-3">
									<h6><?php esc_html_e( 'Time', 'coming-soon-maintenance-mode' ); ?></h6>
									<input type="time" class="form-control" id="comisoma-countdown-time" name="comisoma-countdown-time" value="<?php echo esc_attr($comisoma_countdown_time); ?>">
								</div>
							</div>
							<div class="col-md-6 p-2 mt-3 border">
								<h5 class=""><?php esc_html_e( 'Countdown Tips', 'coming-soon-maintenance-mode' ); ?> <i class="fa-solid fa-circle-info m-1"></i></h5>
								<div>
									<div id="comisoma-countdown-info" class="form-text"><?php esc_html_e( 'Set a website launch countdown.', 'coming-soon-maintenance-mode' ); ?></div>
									<div id="comisoma-countdown-timezone-info" class="form-text"><?php esc_html_e( 'Timezone setting used to calculate website launch.', 'coming-soon-maintenance-mode' ); ?></div>
									<div id="comisoma-countdown-title-info" class="form-text"><?php esc_html_e( 'Write a countdown heading which will appear above the countdown.', 'coming-soon-maintenance-mode' ); ?></div>
									<div id="comisoma-countdown-dt-info-1" class="form-text"><?php esc_html_e( 'Set a date and time for the website launch.', 'coming-soon-maintenance-mode' ); ?></div>
									<div id="comisoma-countdown-dt-info-2" class="form-text"><?php esc_html_e( 'Do not select a past date.', 'coming-soon-maintenance-mode' ); ?></div>
									<div class="form-text"><span class="badge text-bg-danger"><?php esc_html_e( 'Important Note', 'coming-soon-maintenance-mode' ); ?></span><br><?php esc_html_e( 'Automatic website launch on the countdown end is available only in the Pro version.', 'coming-soon-maintenance-mode' ); ?></div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div id="comisoma-content-saving" class="col-md-12 p-2 mt-3 spinner-grow text-dark d-none" role="status">
								<span class="visually-hidden"></span>
							</div>
							<div class="col-md-12 p-2 mt-3">
								<button type="button" id="comisoma-save-content" name="comisoma-save-content" class="btn btn-lg btn-outline-secondary" onclick="return comisoma_save('content', '')"><i class="fa-regular fa-floppy-disk"></i> <?php esc_html_e( 'Save', 'coming-soon-maintenance-mode' ); ?></button>
							</div>
						</div>
					</div><!-- content tab content end-->
					
					<div class="tab-pane fade" id="nav-social-media" role="tabpanel" aria-labelledby="nav-social-media-tab" tabindex="0">
						<div class="row">
							<div class="col-6 p-2 mt-3 border bg-light">
								<div id="comisoma-website-mode-info" class="form-text">
									<?php esc_html_e( 'Add your social media account URLs in given social media section to display on website.', 'coming-soon-maintenance-mode' ); ?>
									<br><?php esc_html_e( 'You can keep it blank if you dont want to display.', 'coming-soon-maintenance-mode' ); ?>
								</div>
								<div>
									<div class="input-group p-2 mt-3">
										<span class="input-group-text" id="basic-addon1"><i class="fa-brands fa-facebook-f"></i></span>
										<input type="text" class="form-control" id="comisoma-sm-facebook" name="comisoma-sm-facebook" value="<?php echo esc_attr($comisoma_sm_facebook); ?>" placeholder="facebook profile / page / group url">
									</div>
								</div>
								<div>
									<div class="input-group p-2 mt-3">
										<span class="input-group-text" id="basic-addon2"><i class="fa-brands fa-twitter"></i></span>
										<input type="text" class="form-control" id="comisoma-sm-twitter" name="comisoma-sm-twitter" value="<?php echo esc_attr($comisoma_sm_twitter); ?>" placeholder="twitter profile url">
									</div>
								</div>
								<div>
									<div class="input-group p-2 mt-3">
										<span class="input-group-text" id="basic-addon4"><i class="fa-brands fa-instagram"></i></span>
										<input type="text" class="form-control" id="comisoma-sm-instagram" name="comisoma-sm-instagram" value="<?php echo esc_attr($comisoma_sm_instagram); ?>" placeholder="instagram profile / brand page url">
									</div>
								</div>

								<div id="comisoma-sm-saving" class="col-md-12 p-2 mt-3 spinner-grow text-dark d-none" role="status">
									<span class="visually-hidden"></span>
								</div>
								<div class="col-md-12 p-2 mt-3">
									<button type="button" id="comisoma-save-sm" name="comisoma-save-sm" class="btn btn-lg btn-outline-secondary" onclick="return comisoma_save('social-media', '')"><i class="fa-regular fa-floppy-disk"></i> <?php esc_html_e( 'Save', 'coming-soon-maintenance-mode' ); ?></button>
								</div>
							</div>
						</div>
					</div><!-- social media tab content end-->
					
					<div class="tab-pane fade" id="nav-docs" role="tabpanel" aria-labelledby="nav-docs-tab" tabindex="0">
						<div class="row">
							<div class="col-6 p-2 mt-3 border">
								<div id="comisoma-website-mode-info" class="form-text">
									<h5 class=""><?php esc_html_e( 'Have a problem with the plugin?', 'coming-soon-maintenance-mode' ); ?></h5>
									<?php esc_html_e( 'Raise a support ticket and we will respond you ASAP.', 'coming-soon-maintenance-mode' ); ?>
									<br><br><a href="https://wordpress.org/support/plugin/coming-soon-maintenance-mode/" target="_blank" class="btn btn-lg btn-outline-primary"><i class="fa-solid fa-ticket"></i> <?php esc_html_e( 'Create Ticket', 'coming-soon-maintenance-mode' ); ?></a>
								</div>
								<br>
								<div id="comisoma-website-mode-info" class="form-text">
									<h5 class=""><?php esc_html_e( 'Visit & Subscribe Our YouTube Channel for More Tutorial Videos', 'coming-soon-maintenance-mode' ); ?></h5>
									<?php esc_html_e( 'In upcoming plugin updates you will get all video docs on our YouTube official website.', 'coming-soon-maintenance-mode' ); ?>
									<br><br><a href="https://www.youtube.com/channel/UCqbxQzbTEE2p3o33fKB5NIQ/" target="_blank" id="comisoma-subscribe" name="comisoma-subscribe" class="btn btn-lg btn-outline-danger"><i class="fa-brands fa-youtube"></i> <?php esc_html_e( 'Subscribe', 'coming-soon-maintenance-mode' ); ?></a>
								</div>
								<br>
								<div id="comisoma-website-mode-info" class="form-text">
									<h5 class=""><?php esc_html_e( 'Upgrade To Pro', 'coming-soon-maintenance-mode' ); ?></h5>
									<?php esc_html_e( 'Upgrade the plugin to the Pro version to get more templates and features.', 'coming-soon-maintenance-mode' ); ?>
									<br><br><a href="https://wpfrank.com/wordpress-plugins/coming-soon-maintenance-mode-pro/" target="_blank" id="comisoma-subscribe" name="comisoma-subscribe" class="btn btn-lg btn-danger"><i class="fa-solid fa-cart-shopping"></i> <?php esc_html_e( 'Get Pro Plugin', 'coming-soon-maintenance-mode' ); ?></a>
								</div>
							</div>
						</div>
					</div><!-- social media tab content end-->
					
					<div class="tab-pane fade" id="nav-tools" role="tabpanel" aria-labelledby="nav-tools-tab" tabindex="0">
						<div class="row">
							
							<!-- Export Settings Section -->
							<div class="col-md-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Export Settings', 'coming-soon-maintenance-mode' ); ?></h5>
								<div id="comisoma-export-info" class="form-text mb-3">
									<?php esc_html_e( 'Generate a JSON string of your current plugin settings to copy to another website.', 'coming-soon-maintenance-mode' ); ?>
								</div>
								<div>
									<button type="button" id="comisoma-export-settings-btn" class="btn btn-outline-primary mb-3" onclick="return comisoma_export();"><i class="fa-solid fa-file-export"></i> <?php esc_html_e( 'Generate Export', 'coming-soon-maintenance-mode' ); ?></button>
								</div>
								<div>
									<textarea id="comisoma-export-data" class="form-control" rows="5" readonly placeholder="<?php esc_html_e( 'Export data will appear here...', 'coming-soon-maintenance-mode' ); ?>"></textarea>
								</div>
							</div>

							<!-- Import Settings Section -->
							<div class="col-md-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Import Settings', 'coming-soon-maintenance-mode' ); ?></h5>
								<div id="comisoma-import-info" class="form-text mb-3">
									<?php esc_html_e( 'Paste a JSON string of exported settings to overwrite this sites settings.', 'coming-soon-maintenance-mode' ); ?>
								</div>
								<div>
									<textarea id="comisoma-import-data" class="form-control mb-3" rows="5" placeholder="<?php esc_html_e( 'Paste export data here...', 'coming-soon-maintenance-mode' ); ?>"></textarea>
								</div>
								<div>
									<button type="button" id="comisoma-import-settings-btn" class="btn btn-outline-success" onclick="return comisoma_save('import', '');"><i class="fa-solid fa-file-import"></i> <?php esc_html_e( 'Import Settings', 'coming-soon-maintenance-mode' ); ?></button>
								</div>
							</div>

							<!-- Reset Settings Section -->
							<div class="col-md-6 p-2 mt-3 border bg-light">
								<h5 class=""><?php esc_html_e( 'Reset Plugin Settings', 'coming-soon-maintenance-mode' ); ?></h5>
								<div id="comisoma-reset-info" class="form-text mb-3">
									<span class="badge text-bg-danger"><?php esc_html_e( 'Warning', 'coming-soon-maintenance-mode' ); ?></span><br>
									<?php esc_html_e( 'This will permanently delete all your plugin configurations, template selections, and social media data, reverting the plugin to its default fresh state.', 'coming-soon-maintenance-mode' ); ?>
								</div>
								<div>
									<button type="button" id="comisoma-reset-settings-btn" class="btn btn-danger" onclick="return comisoma_save('reset', '');"><i class="fa-solid fa-rotate-left"></i> <?php esc_html_e( 'Factory Reset Plugin', 'coming-soon-maintenance-mode' ); ?></button>
								</div>
							</div>

						</div>
					</div><!-- tools tab content end-->
					
					<div class="tab-pane fade" id="nav-docs" role="tabpanel" aria-labelledby="nav-docs-tab" tabindex="0">
					</div><!-- docs tab content end-->
				</div>
			</div>
		</div><!-- left section end-->
	</div><!-- end row-->
</div><!-- end container-->
</div>
