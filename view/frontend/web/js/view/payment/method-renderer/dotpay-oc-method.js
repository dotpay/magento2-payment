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
        'ko',
        'jquery',
        'Dotpay_Payment/js/view/payment/method-renderer/dotpay-abstract-method'
    ],
    function (
        ko,
        $,
        Component
    ) {
        'use strict';
        var firstSelectMode = window.checkoutConfig.payment.dotpay_oc.cards.length !== 0 ? 'choose' : 'register';
        var cardLogo = ko.observable(firstSelectMode === 'choose'?window.checkoutConfig.payment.dotpay_oc.cards[0].logo:'');
        var dotpayComponent, selectedCard = window.checkoutConfig.payment.dotpay_oc.cards.length !== 0 ? window.checkoutConfig.payment.dotpay_oc.cards[0].id : -1;
        
        return Component.extend({
            defaults: {
                template: 'Dotpay_Payment/payment/oc-form'
            },
            methodName: 'dotpay_oc',
            initialize: function () {
                this._super();
                dotpayComponent = this;
            },
            getCreditCards: function() {
                return this.getConfigData().cards;
            },
            selectMode: ko.observable(firstSelectMode),
            availableCards: window.checkoutConfig.payment.dotpay_oc.cards.length !== 0,
            selectOneClickType: function(data, event) {
                dotpayComponent.correctFields(true);
                this.selectMode(event.target.value);
                return true;
            },
            isRegisterSelected: function() {
                return this.getCreditCards().length === 0;
            },
            selectCard: function(data, event) {
                selectedCard = event.target.value;
                var obj = $(event.target);
                var logo = jQuery('.dotpay-card-logo');
                logo.css('transform', 'rotateY(90deg)')
                .css('-webkit-transform', 'rotateY(90deg)');
                setTimeout(function(){
                    cardLogo(obj.find('option:selected').attr('data-logo'));
                    setTimeout(function(){
                        logo.css('transform', 'rotateY(0deg)')
                        .css('-webkit-transform', 'rotateY(0deg)');
                    }, 180);
                }, 800);
                return true;
            },
            activeCardLogo: cardLogo,
            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'selectedMode': this.selectMode(),
                        'selectedCard': selectedCard
                    }
                };
            }
        });
    }
);