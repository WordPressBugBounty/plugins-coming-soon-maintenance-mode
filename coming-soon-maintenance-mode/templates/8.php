<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <title><?php echo esc_html($comisoma_title); ?></title>
    <meta name="description" content="<?php echo esc_html($comisoma_description); ?>">
    <meta name="author" content="">
    <!-- mobile specific metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicons
    ================================================== -->
   <link rel="shortcut icon" href="<?php echo esc_url( get_site_icon_url() ); ?>" type="image/x-icon">
	<link rel="icon" href="<?php echo esc_url( get_site_icon_url() ); ?>" type="image/x-icon">
    <?php wp_head(); ?>
</head>
<body>
    <!-- home
    ================================================== -->
    <main class="s-home s-home--static template-two">

        <div class="home-content">

            <div class="row home-content__main text-center">
				<div class="frame-ipad">
					
					<div class="col-six">
					</div>
					<div class="col-eight text-wrap">

						<?php if($comisoma_logo_id) { ?>
						<div class="home-logo">
							<a class="logo-dark" href="<?php echo esc_url( get_site_url() ); ?>">
								<img src="<?php echo esc_url( $comisoma_logo_url[0] ); ?>" alt="<?php echo esc_attr( $comisoma_logo_alt ); ?>">
							</a>
						</div>
						<?php } ?>

						<div class="home-content__text">

							<h1><?php if($comisoma_title != "") { echo esc_html( $comisoma_title ); } ?></h1>
							<p><?php if($comisoma_description != "") { echo esc_textarea( stripslashes( $comisoma_description ) ); } ?></p>

							<?php if($comisoma_countdown == 1) { ?>
							<div class="home-content__counter">
								<div class="home-content__clock">
									<div class="time days">
										325
										<span>D</span>
									</div>
									<div class="time hours">
										09
										<span>H</span>
									</div>
									<div class="time minutes">
										54
										<span>M</span>
									</div>
									<div class="time seconds">
										30
										<span>S</span>
									</div>
								</div>  <!-- end home-content__clock -->
							</div>  <!-- end home-content__counter -->
							<?php } ?>
						</div>  <!-- end home-content__text -->

						<ul class="home-social">
							<?php if(empty($comisoma_sm_facebook) == false) { ?>
							<li><a href="<?php echo esc_url($comisoma_sm_facebook); ?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
							<?php } ?>
							<?php if(empty($comisoma_sm_twitter) == false) { ?>
							<li><a href="<?php echo esc_url($comisoma_sm_twitter); ?>" target="_blank"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a></li>
							<?php } ?>
							<?php if(empty($comisoma_sm_instagram) == false) { ?>
							<li><a href="<?php echo esc_url($comisoma_sm_instagram); ?>" target="_blank"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a></li>
							<?php } ?>
						</ul> <!-- end home-social -->
					</div>

				</div>  <!-- frame ipad -->
            </div>  <!-- end home-content__main -->

        </div> <!-- end home-content -->

    </main> <!-- end s-home -->

    <!-- Java Script
    ================================================== -->
    <?php wp_footer(); ?>
</body>
</html>
