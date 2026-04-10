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
    <style>
        body { background: #111 !important; }
        .PhotoZoom_iframe__LeuQM { height: 100vh !important; }
    </style>
    <?php wp_head(); ?>

</head>

<body>

    <!-- home
    ================================================== -->
    <div class="PhotoZoom_iframe__LeuQM" style="background-image: url('<?php echo esc_url( COMISOMA_URL . 'templates/images/temp-15-fg.webp' ); ?>'); background-size: cover; background-position: center; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: 1;"></div>

    <section id="home" class="s-home page-hero target-section" data-parallax="scroll" data-image-src="<?php echo esc_url( COMISOMA_URL . 'templates/images/temp-15-fg.webp' ); ?>" data-natural-width="3000" data-natural-height="2000" data-position-y="center" style="z-index: 2; background: transparent;">


        <div class="home-content">


           
                <div class="home-logo">
					<?php if ($comisoma_logo_id) { ?>
						<a href="<?php echo esc_url( get_site_url() ); ?>">
							<img src="<?php echo esc_url($comisoma_logo_url[0]);
										?>" alt="<?php echo esc_attr($comisoma_logo_alt);
													?>">
						</a>
					 <?php } ?>
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
					</ul> <!-- end home-social --> <!-- end home-social -->
                </div>
           

            <div class="row home-content__main">


                <?php if ($comisoma_countdown == 1) { ?>
                    <div class="home-content__counter">
                        <?php if ($comisoma_countdown_title != "") { ?>
                            <h3 class="comisoma-countdown-title"><?php echo esc_html($comisoma_countdown_title); ?></h3>
                        <?php } ?>
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
                        </div> <!-- end home-content__clock -->
                    </div> <!-- end home-content__counter -->
                <?php } ?>

                <h1 class="csm-ticker"><?php if ($comisoma_title != "") {
                                            echo esc_html($comisoma_title);
                                        } ?></h1>
                




            </div> <!-- end home-content__main -->

            <div class="home-content__scroll">
            </div>

        </div> <!-- end home-content -->



    </section>

  

     <!-- Java Script
    ================================================== -->

    <?php wp_footer(); ?>
</body>
</html>