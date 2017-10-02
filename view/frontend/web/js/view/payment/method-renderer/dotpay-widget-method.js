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
 * @copyright Dotpay
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
define(
    [
        'jquery',
        'ko',
        'Dotpay_Payment/js/view/payment/method-renderer/dotpay-abstract-method'
    ],
    function (
        $,
        ko,
        Component
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
            if($('.dotpay-widget-container').length) {
                window.dotpayWidget.init(widgetConfig);
                return true;
            } else {
                ++counter;
                return false;
            }
        };
        
        return Component.extend({
            defaults: {
                template: 'Dotpay_Payment/payment/widget-form'
            },
            methodName: 'dotpay_widget',
            correctFields: ko.observable(false),
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
            }
        });
    }
);