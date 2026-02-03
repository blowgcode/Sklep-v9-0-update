/*******************************************************************************
/*******************************************************************************/

(function(window)
{
    "use strict";

    window.CHBSPaymentTpayFrontend = function() {};

    window.CHBSPaymentTpayFrontend.prototype.redirect = function(response)
    {
        if(!response || !response.payment_tpay_redirect_url)
            return;

        var duration = parseInt(response.payment_tpay_redirect_duration,10);
        if(isNaN(duration) || duration <= 0)
        {
            window.location.href = response.payment_tpay_redirect_url;
            return;
        }

        setTimeout(function()
        {
            window.location.href = response.payment_tpay_redirect_url;
        }, duration * 1000);
    };
})(window);
