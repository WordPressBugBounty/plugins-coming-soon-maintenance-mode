/**
 * @comisoma logo uploader v1.0.0 - MIT License
 */
jQuery(
	function(jQuery) {
		var file_frame,
		COMISOMA = {
			ul: '',
			init: function() {
				this.ul = jQuery( '#comisoma-logo' );
				this.ul.sortable(
					{
						placeholder: '',
						revert: true,
					}
				);

				/**
				 * Add Image Callback Function
				 */
				jQuery( '#comisoma-upload-logo' ).on(
					'click',
					function(event) {
						event.preventDefault();
						if (file_frame) {
							file_frame.open();
							return;
						}
						file_frame = wp.media.frames.file_frame = wp.media(
							{
								multiple: true
							}
						);

						file_frame.on(
							'select',
							function() {
								var images = file_frame.state().get( 'selection' ).toJSON(),
									length = images.length;
								for (var i = 0; i < length; i++) {
									COMISOMA.get_thumbnail( images[i]['id'] );
								}
							}
						);
						file_frame.open();
					}
				);

				/**
				 * Remove Image Callback Function
				 */
				/* this.ul.on('click', '#comisoma-remove-image', function() {
				if (confirm('Are sure to delete this images?')) {
					jQuery(this).parent().fadeOut(700, function() {
						jQuery(this).remove();
					});
				}
				return false;
				}); */

				/**
				 * Remove All Images Callback Function
				 */
				jQuery( '#comisoma-remove-all-image' ).on(
					'click',
					function() {
						if (confirm( 'Are sure to delete all images?' )) {
							COMISOMA.ul.empty();
						}
						return false;
					}
				);

			},
			get_thumbnail: function(id, cb) {
				cb = cb || function() {
				};
				var data = {
					action: 'comisoma_logo',
					attachment_id: id,
					nonce: ComisomaUploaderAjax.logoNonce,
				};
				jQuery.post(
					ComisomaUploaderAjax.ajaxUrl,
					data,
					function(response) {
						COMISOMA.ul.empty();
						COMISOMA.ul.append( response );
						cb();
						// BindMultiSelect();
					}
				);
			}
		};
		COMISOMA.init();
	}
);

/**
 * @comisoma slides v1.0.0 - MIT License
 */
jQuery(
	function(jQuery) {
		var file_frame,
		COMISOMA_Slides = {
			ul: '',
			init: function() {
				this.ul = jQuery( '#comisoma-slides' );
				this.ul.sortable(
					{
						placeholder: '',
						revert: true,
					}
				);

				/**
				 * Add Image Callback Function
				 */
				jQuery( '#comisoma-upload-slide' ).on(
					'click',
					function(event) {
						event.preventDefault();
						if (file_frame) {
							file_frame.open();
							return;
						}
						file_frame = wp.media.frames.file_frame = wp.media(
							{
								multiple: true
							}
						);

						file_frame.on(
							'select',
							function() {
								var images = file_frame.state().get( 'selection' ).toJSON(),
									length = images.length;
								for (var i = 0; i < length; i++) {
									COMISOMA_Slides.get_thumbnail( images[i]['id'] );
								}
							}
						);
						file_frame.open();
					}
				);

				/**
				 * Remove Image Callback Function
				 */
				/* this.ul.on('click', '#comisoma-remove-slides', function() {
				if (confirm('Are sure to delete this images?')) {
					jQuery(this).parent().fadeOut(700, function() {
						jQuery(this).remove();
					});
				}
				return false;
				}); */

				/**
				 * Remove All Images Callback Function
				 */
				jQuery( '#comisoma-remove-all-slides' ).on(
					'click',
					function() {
						if (confirm( 'Are sure to delete all slides?' )) {
							COMISOMA_Slides.ul.empty();
						}
						return false;
					}
				);

			},
			get_thumbnail: function(id, cb) {
				cb = cb || function() {
				};
				var data = {
					action: 'comisoma_slide',
					attachment_id: id,
				};
				jQuery.post(
					ajaxurl,
					data,
					function(response) {
						COMISOMA_Slides.ul.append( response );
						cb();
						// BindMultiSelect();
					}
				);
			}
		};
		COMISOMA_Slides.init();
	}
);

