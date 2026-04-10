jQuery(document).ready(function ($) {
    jQuery(document).on('click', '.comisoma-notice-custom .notice-dismiss', function (e) {
        e.preventDefault();
        var notice = jQuery(this).closest('.comisoma-notice-custom');
        jQuery.ajax({
            type: "POST",
            url: ComisomaNotice.ajaxUrl,
            data: {
                action: "comisoma_dismiss_notice",
                security: ComisomaNotice.nonce,
            },
            success: function (response) {
                notice.fadeOut(200);
            }
        });
    });
});
