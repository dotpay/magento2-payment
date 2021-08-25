/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@dotpay.pl so we can send you a copy immediately.
 *
 * @author    Dotpay Team <tech@dotpay.pl>
 * @copyright PayPro S.A.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
define(
    [
        'jquery',
        'ko',
        'Dotpay_Payment/js/view/payment/method-renderer/dotpay-abstract-method',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
    ],
    function (
        $,
        ko,
        Component,
        selectPaymentMethodAction,
        checkoutData,
    ) {
        'use strict';
        var selectedChannel, dotpayComponent;

        var widgetConfig = window.checkoutConfig.payment.dotpay_widget.widgetConfig;
        widgetConfig.event = {
            'onChoose': function(e) {
                selectedChannel = e.channel.id;
                dotpayComponent.correctFields(true);
            }
        };
        var counter = 0;
        var load = function() {
            if($('.dotpay-other-channel').length && !$('.dotpay-widget-container').length) {
                window.dotpayWidget.init(widgetConfig);
                return true;
            } else {
                ++counter;
                return false;
            }
        };

        return Component.extend({
            defaults: {
                template: 'Dotpay_Payment/payment/other-form'
            },
            methodName: 'dotpay_other',
            selectPaymentMethod: function (data, event) {
                selectedChannel = parseInt($(event.target).attr("data-other-channel"));
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                $('#other_channel_'+selectedChannel).addClass("_active");

                return true;

            },
            initialize: function () {
                this._super();
                var interval;
                interval = setInterval(function(){
                    if(load() === true || counter > 10) {//timeout
                        clearInterval(interval);
                    }
                }, 300);
                dotpayComponent = this;
            },
            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'channel': selectedChannel
                    }
                };
            },
            // getAgreements: function() {
            //     for(var i in this.getConfigData().agreements) {
            //         this.getConfigData().agreements[i].parentObject = this;
            //     }
            //     return this.getConfigData().agreements;
            // },
            validateAgreements: function() {
                var result = $('.dotpay-agreements input[type="checkbox"].dotpay_other_'+selectedChannel).length === $('.dotpay-agreements input[type="checkbox"].dotpay_other_'+selectedChannel+':checked').length;
                //console.log(selectedChannel);
                this.correctAgreements(result);
            }
        });
    }
);
