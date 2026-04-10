function comisoma_save(tab, id){
	
	// website-mode-top tab data post start
	if(tab == 'website-mode-top'){
		var website_mode = jQuery('input[name="website-mode-top"]:checked').val();
		var selected_posts = [];
		var selected_pages = [];
		var selected_other_pages = [];
		jQuery.ajax({
			type: 'POST',
			url: ComisomaAdmin.ajaxUrl,
			data: {
				'action': 'comisoma_save', //this is the name of the AJAX method called in WordPress
				'tab': 'settings',
				'website_mode': website_mode,
				'selected_posts': selected_posts,
				'selected_pages': selected_pages,
				'selected_other_pages': selected_other_pages,
				'nonce': ComisomaAdmin.nonce,
			}, 
			success: function (response) {
				console.log('website-mode-top-saved');
			},
			error: function () {
				//alert("error");
			}
		});
	}
	// website-mode-top tab data post end
	
	// settings tab data post start
	if(tab == 'settings'){
		jQuery('button#comisoma-save-setings').addClass('d-none');
		jQuery('div#comisoma-setings-saving').removeClass('d-none');
		var website_mode = jQuery('input[name="website-mode"]:checked').val();
		var selected_posts = [];
		jQuery("input:checkbox[name=comisoma_posts]:checked").each(function(){
			selected_posts.push(jQuery(this).val());
		});
		var selected_pages = [];
		jQuery("input:checkbox[name=comisoma_pages]:checked").each(function(){
			selected_pages.push(jQuery(this).val());
		});
		var selected_other_pages = [];
		jQuery("input:checkbox[name=comisoma_other_pages]:checked").each(function(){
			selected_other_pages.push(jQuery(this).val());
		});
		jQuery.ajax({
			type: 'POST',
			url: ComisomaAdmin.ajaxUrl,
			data: {
				'action': 'comisoma_save', //this is the name of the AJAX method called in WordPress
				'tab': tab,
				'website_mode': website_mode,
				'selected_posts': selected_posts,
				'selected_pages': selected_pages,
				'selected_other_pages': selected_other_pages,
				'nonce': ComisomaAdmin.nonce,
			}, 
			success: function (response) {
				jQuery(function() {
					setTimeout(function() {
						// hide processing icon and show button
						jQuery('div#comisoma-setings-saving').addClass('d-none');
						jQuery('button#comisoma-save-setings').removeClass('d-none');
						// shake live preview button
						jQuery( "#comisoma-live-preview" ).effect("shake");
					}, 500);
				});
			},
			error: function () {
				//alert("error");
			}
		});
	}
	// settings tab data post end
	
	// templates tab data post start
	if(tab == 'templates'){
		var template_id = jQuery("#" + id).val();
		jQuery("#nav-templates .cmss-btn-template").removeClass("activated btn-primary").addClass("activate btn-outline-primary").text("Activate");
		jQuery('button#' + id).removeClass('btn-outline-primary activate').addClass('btn-primary activated').text('Activated');
		jQuery.ajax({
			type: 'POST',
			url: ComisomaAdmin.ajaxUrl,
			data: {
				'action': 'comisoma_save', //this is the name of the AJAX method called in WordPress
				'tab': tab,
				'template_id': template_id,
				'nonce': ComisomaAdmin.nonce,
			}, 
			success: function (response) {
				// shake live preview button
				jQuery( "#comisoma-live-preview" ).effect("shake");
			},
			error: function () {
				//alert("error");
			}
		});
	}
	// templates tab data post end
	
	// content tab data post start
	if(tab == 'content'){
		jQuery('button#comisoma-save-content').addClass('d-none');
		jQuery('div#comisoma-content-saving').removeClass('d-none');
		var logo = jQuery("#comisoma-logo-id").val();
		var title = jQuery("#comisoma-title").val();
		var description = jQuery("#comisoma-description").val();
		var countdown = jQuery('input[name="comisoma-countdown"]:checked').val();
		var countdown_title = jQuery("#comisoma-countdown-title").val();
		var countdown_date = jQuery("#comisoma-countdown-date").val();
		var countdown_time = jQuery("#comisoma-countdown-time").val();
		jQuery.ajax({
			type: 'POST',
			url: ComisomaAdmin.ajaxUrl,
			data: {
				'action': 'comisoma_save', //this is the name of the AJAX method called in WordPress
				'tab': tab,
				'logo': logo,
				'title': title,
				'description': description,
				'countdown': countdown,
				'countdown_title': countdown_title,
				'countdown_date': countdown_date,
				'countdown_time': countdown_time,
				'nonce': ComisomaAdmin.nonce,
			}, 
			success: function (response) {
				jQuery(function() {
					setTimeout(function() {
						// hide processing icon and show button
						jQuery('div#comisoma-content-saving').addClass('d-none');
						jQuery('button#comisoma-save-content').removeClass('d-none');
						// shake live preview button
						jQuery( "#comisoma-live-preview" ).effect("shake");
					}, 500);
				});
			},
			error: function () {
				//alert("error");
			}
		});
	}
	// content tab data post end
	
	// social media tab data post start
	if(tab == 'social-media'){
		jQuery('button#comisoma-save-sm').addClass('d-none');
		jQuery('div#comisoma-sm-saving').removeClass('d-none');
		var comisoma_sm_facebook = jQuery("#comisoma-sm-facebook").val();
		var comisoma_sm_twitter = jQuery("#comisoma-sm-twitter").val();
		var comisoma_sm_instagram = jQuery("#comisoma-sm-instagram").val();
		var comisoma_sm_youtube = jQuery("#comisoma-sm-youtube").val();
		var comisoma_sm_linkedin = jQuery("#comisoma-sm-linkedin").val();
		var comisoma_sm_pinterest = jQuery("#comisoma-sm-pinterest").val();
		var comisoma_sm_tumblr = jQuery("#comisoma-sm-tumblr").val();
		var comisoma_sm_snapchat = jQuery("#comisoma-sm-snapchat").val();
		var comisoma_sm_behance = jQuery("#comisoma-sm-behance").val();
		var comisoma_sm_dribbble = jQuery("#comisoma-sm-dribbble").val();
		var comisoma_sm_whatsapp = jQuery("#comisoma-sm-whatsapp").val();
		var comisoma_sm_tiktok = jQuery("#comisoma-sm-tiktok").val();
		var comisoma_sm_qq = jQuery("#comisoma-sm-qq").val();
		jQuery.ajax({
			type: 'POST',
			url: ComisomaAdmin.ajaxUrl,
			data: {
				'action': 'comisoma_save', //this is the name of the AJAX method called in WordPress
				'tab': tab,
				'comisoma_sm_facebook': comisoma_sm_facebook,
				'comisoma_sm_twitter': comisoma_sm_twitter,
				'comisoma_sm_instagram': comisoma_sm_instagram,
				'comisoma_sm_youtube': comisoma_sm_youtube,
				'comisoma_sm_linkedin': comisoma_sm_linkedin,
				'comisoma_sm_pinterest': comisoma_sm_pinterest,
				'comisoma_sm_tumblr': comisoma_sm_tumblr,
				'comisoma_sm_snapchat': comisoma_sm_snapchat,
				'comisoma_sm_behance': comisoma_sm_behance,
				'comisoma_sm_dribbble': comisoma_sm_dribbble,
				'comisoma_sm_whatsapp': comisoma_sm_whatsapp,
				'comisoma_sm_tiktok': comisoma_sm_tiktok,
				'comisoma_sm_qq': comisoma_sm_qq,
				'nonce': ComisomaAdmin.nonce,
			}, 
			success: function (response) {
				jQuery(function() {
					setTimeout(function() {
						// hide processing icon and show button
						jQuery('div#comisoma-sm-saving').addClass('d-none');
						jQuery('button#comisoma-save-sm').removeClass('d-none');
						// shake live preview button
						jQuery( "#comisoma-live-preview" ).effect("shake");
					}, 500);
				});
			},
			error: function () {
				//alert("error");
			}
		});
	}
	// social media tab data post end
	
	// remove logo start
	if(tab == 'remove-logo'){
		jQuery("li.comisoma-logo-" + id).fadeOut(700, function() {
			jQuery("li.comisoma-logo-" + id).remove();
		});
	}
	// remove logo end
	
}

jQuery(document).ready(function(){
	// select all posts
	jQuery("#check-all-posts").click(function(){
		jQuery(".comisoma-post-list").prop('checked', jQuery(this).prop('checked'));
	});
	
	// select all pages
	jQuery("#check-all-pages").click(function(){
		jQuery(".comisoma-page-list").prop('checked', jQuery(this).prop('checked'));
	});
	// select all other pages
	jQuery("#check-all-other-pages").click(function(){
		jQuery(".comisoma-other-page-list").prop('checked', jQuery(this).prop('checked'));
	});
	
	//get active tab id start
	jQuery('button').click(function() {
		if(this.id == "nav-settings-tab") {
			jQuery("div.website-mode-top").addClass('d-none');
		}
		if(this.id == "nav-templates-tab" || this.id == "nav-content-tab" || this.id == "nav-social-media-tab") {
			jQuery("div.website-mode-top").removeClass('d-none');
		}
	});
	//get active tab id end
	
	//set website mode on top start
	// at top
	jQuery('input[name="website-mode-top"]').click(function() {
		var website_mode = jQuery('input[name="website-mode-top"]:checked').val();
		if(website_mode == 1) {
			jQuery("#website-mode1").prop('checked', true);
			jQuery("#maintenance-mode-options").fadeOut( "slow", function() {});
		}
		if(website_mode == 2) {
			jQuery("#website-mode2").prop('checked', true);
			jQuery("#maintenance-mode-options").fadeIn( "slow", function(){ });
		}
		if(website_mode == 3) {
			jQuery("#website-mode3").prop('checked', true);
			jQuery("#maintenance-mode-options").fadeOut( "slow", function() {});
		}
	});
	
	// in tab
	jQuery('input[name="website-mode"]').click(function() {
		var website_mode = jQuery('input[name="website-mode"]:checked').val();
		if(website_mode == 1) {
			jQuery("#website-mode-top1").prop('checked', true);
		}
		if(website_mode == 2) {
			jQuery("#website-mode-top2").prop('checked', true);
		}
		if(website_mode == 3) {
			jQuery("#website-mode-top3").prop('checked', true);
		}
		//hide and show maintenance-mode-options settings
		if(website_mode == 2){
			jQuery("#maintenance-mode-options").fadeIn( "slow", function() {});
		} else {
			jQuery("#maintenance-mode-options").fadeOut( "slow", function() {});
		}
	});
	//set website mode on top end

});
