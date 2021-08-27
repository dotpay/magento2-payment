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
            getOtherChannels: function() {
                return this.getConfigData().channels
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
            },
            selectPaymentMethod: function () {

                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                abstractComponent = this;
                this.validateAgreements();

                return true;

            },
            afterPlaceOrder: function () {
                window.location.replace(url.build(this.getRedirectUrl()));
            },
            getAgreements: function() {
                for(var i in this.getConfigData().agreements) {
                    this.getConfigData().agreements[i].parentObject = this;
                }
				var agreements1 = this.getConfigData().agreements;

				//get web lang
                console.log('detect language:' + $("html").attr("lang"));
				
				// attention! this is a hack for languages not officially supported in api dotpay //
				
				//Dutch
				if ( $("html:lang(nl)").length > 0 ) {
					console.log('change agreements text to the Dutch (nl)');
					
					if (agreements1[0]['description_html'] && agreements1[0]['description_html'].length){
						agreements1[0]['description_html'] = 'Ik accepteer de <a title="\betalingsvoorschriften\" target=\"_blank\" href=\"https://ssl.dotpay.pl/test_payment/cloudfs2/magellan_media/regulamin_platnosci\">betalingsvoorschriften</a> PayPro TEST S.A..';		
					}
					if (agreements1[1]['description_html'] && agreements1[1]['description_html'].length){
						agreements1[1]['description_html'] = 'Ik erken dat om de betaling te verwerken, de beheerder van mijn persoonlijke gegevens PayPro S.A.. (KRS 0000347935), 60-327 Poznań (Polen), Kanclerska 15, +48126882600, <a href=\"mailto:bok@dotpay.pl\">bok@dotpay.pl</a>, zie <a title=\"regelgeving\" target=\"_blank\" href=\"https://ssl.dotpay.pl/test_payment/cloudfs2/magellan_media/rodo\">de volledige tekst van de informatieclausule</a>.';
					}
				}
				// Croatian
				if ( $("html:lang(hr)").length > 0 ) {
					console.log('change agreements text to the Croatian (hr)');
					
					if (agreements1[0]['description_html'] && agreements1[0]['description_html'].length){
						agreements1[0]['description_html'] = 'Ik accepteer de <a title="\betalingsvoorschriften\" target=\"_blank\" href=\"https://ssl.dotpay.pl/test_payment/cloudfs2/magellan_media/regulamin_platnosci\">betalingsvoorschriften</a> PayPro TEST S.A..';		
					}
					if (agreements1[1]['description_html'] && agreements1[1]['description_html'].length){
						agreements1[1]['description_html'] = 'Ik erken dat om de betaling te verwerken, de beheerder van mijn persoonlijke gegevens PayPro S.A.. (KRS 0000347935), 60-327 Poznań (Polen), Kanclerska 15, +48126882600, <a href=\"mailto:bok@dotpay.pl\">bok@dotpay.pl</a>, zie <a title=\"regelgeving\" target=\"_blank\" href=\"https://ssl.dotpay.pl/test_payment/cloudfs2/magellan_media/rodo\">de volledige tekst van de informatieclausule</a>.';
					}		

				}					
				// Bulgarian
				if ( $("html:lang(bg)").length > 0 ) {
					console.log('change agreements text to the Bulgarian (bg)');
					
					if (agreements1[0]['description_html'] && agreements1[0]['description_html'].length){
						agreements1[0]['description_html'] = 'Приемам <a title="\Правилника\" target=\"_blank\" href=\"https://ssl.dotpay.pl/test_payment/cloudfs2/magellan_media/regulamin_platnosci\">Правилника</a> за плащане на PayPro S.A..';		
					}
					if (agreements1[1]['description_html'] && agreements1[1]['description_html'].length){
						agreements1[1]['description_html'] = 'Приемам, че за реализиране на процеса на плащане, Администраторът на моите лични данни е PayPro S.A.. (KRS 0000347935), 60-327 Poznań (Полша), Kanclerska 15, +48126882600, <a href=\"mailto:bok@dotpay.pl\">bok@dotpay.pl</a>, виж пълния текст на <a title=\"регламенти\" target=\"_blank\" href=\"https://ssl.dotpay.pl/test_payment/cloudfs2/magellan_media/rodo\">информационната клауза</a>.';
					}		

				}
	
			   
			   	// add space before one click agreements
				if ($('input.dotpay_oc').siblings('br').length < 1){
					$('label input[name="oc-store-card"]').before("<br>");
				}

                if ($('label input[name="personal_data"]').siblings('br').length < 1) {
                    $('label input[name="personal_data"]').after("<br>");
                }
			   
				return agreements1;
            },
            validateAgreements: function() {
                var result = $('.dotpay-agreements input[type="checkbox"].'+abstractComponent.methodName).length === $('.dotpay-agreements input[type="checkbox"].'+abstractComponent.methodName+':checked').length;
                if(abstractComponent.methodName == "dotpay_blik")
                {
                    result = result && ($('#dotpay_blik_code').val().length === 6)
                }
                abstractComponent.correctAgreements(result);
            }
        });
    }
);
