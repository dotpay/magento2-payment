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
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url'
    ],
    function (
        $,
        ko,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        customer,
        checkoutData,
        additionalValidators,
        url
    ) {
        'use strict';
        var abstractComponent;
        return Component.extend({
            methodName: null,
            correctFields: ko.observable(true),
            correctAgreements: ko.observable(true),
            initialize: function () {
                this._super();
                
            },
            getConfigData: function() {
                return window.checkoutConfig.payment[this.methodName];
            },
            getPaymentAcceptanceMarkSrc: function() {
                return this.getConfigData().logoUrl;
            },
            isLogoVisible: function() {
                return this.getConfigData().displayLogo;
            },
            getRedirectUrl: function () {
                return this.getConfigData().redirectUrl;
            },
            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
                var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (emailValidationResult && this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                    $.when(placeOrder).fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                }
                return false;
            },selectPaymentMethod: function () {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                abstractComponent = this;
                return true;
            },
            afterPlaceOrder: function () {
                window.location.replace(url.build(this.getRedirectUrl()));
            },
            getAgreements: function() {
                for(var i in this.getConfigData().agreements) {
                    this.getConfigData().agreements[i].parentObject = this;
                }
                return this.getConfigData().agreements;
            },
            validateAgreements: function() {
                var result = $('.dotpay-agreements input.'+abstractComponent.methodName).length === $('.dotpay-agreements input.'+abstractComponent.methodName+':checked').length;
                abstractComponent.correctAgreements(result);
            }
        });
    }
);
