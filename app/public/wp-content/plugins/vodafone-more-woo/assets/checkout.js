(function ($) {
    'use strict';

    $(document).ready(function () {

        // Apply coupon
        $(document).on('click', '#vmw-apply-btn', function () {
            var code = $('#vmw_coupon_code').val().trim();
            if (!code) return;

            var $btn = $(this);
            $btn.prop('disabled', true).text(vmwData.i18n.applying);
            $('#vmw-coupon-message').html('');

            $.ajax({
                url: vmwData.ajax_url,
                method: 'POST',
                data: {
                    action: 'vmw_apply_coupon',
                    nonce:  vmwData.nonce,
                    coupon: code,
                },
                success: function (response) {
                    if (response.success) {
                        // Replace the input with success message + remove button
                        $('#vmw-coupon-inner').html(
                            '<div class="vmw-applied">' +
                            '<p class="vmw-success">✅ ' + response.data.message + '</p>' +
                            '<button type="button" id="vmw-remove-btn" class="button">' +
                            'Remove' +
                            '</button>' +
                            '</div>'
                        );
                        $('body').trigger('update_checkout');
                    } else {
                        $('#vmw-coupon-message').html(
                            '<p class="vmw-error">❌ ' + response.data.message + '</p>'
                        );
                        $btn.prop('disabled', false).text('Apply');
                    }
                },
                error: function () {
                    $('#vmw-coupon-message').html('<p class="vmw-error">❌ Connection error. Please try again.</p>');
                    $btn.prop('disabled', false).text('Apply');
                }
            });
        });

        // Remove coupon
        $(document).on('click', '#vmw-remove-btn', function () {
            var $btn = $(this);
            $btn.prop('disabled', true).text(vmwData.i18n.removing);

            $.ajax({
                url: vmwData.ajax_url,
                method: 'POST',
                data: {
                    action: 'vmw_remove_coupon',
                    nonce:  vmwData.nonce,
                },
                success: function () {
                    $('#vmw-coupon-inner').html(
                        '<p>Have a Vodafone More loyalty coupon? Enter it below.</p>' +
                        '<div class="vmw-input-row">' +
                        '<input type="text" id="vmw_coupon_code" placeholder="8-digit coupon code" maxlength="8" class="input-text">' +
                        '<button type="button" id="vmw-apply-btn" class="button alt">Apply</button>' +
                        '</div>' +
                        '<div id="vmw-coupon-message"></div>'
                    );
                    $('body').trigger('update_checkout');
                }
            });
        });

        // Allow Enter key on input
        $(document).on('keydown', '#vmw_coupon_code', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#vmw-apply-btn').trigger('click');
            }
        });

    });

})(jQuery);