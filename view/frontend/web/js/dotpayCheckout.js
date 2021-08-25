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
define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'jquery/ui',
    'mage/mage'
], function ($, confirm) {
    'use strict';

    $.widget('mage.dotpayCheckout', {
        options: {
            originalForm:
                'form:not(#product_addtocart_form_from_popup):has(input[name="product"][value=%1])',
            productId: 'input[type="hidden"][name="product"]',
            ppCheckoutSelector: '[data-role=pp-checkout-url]',
            ppCheckoutInput: '<input type="hidden" data-role="pp-checkout-url" name="return_url" value=""/>'
        },

        /**
         * Initialize store credit events
         * @private
         */
        _create: function () {
            this.element.on('click', '[data-action="checkout-form-submit"]', $.proxy(function (e) {
                var $target = $(e.target),
                    returnUrl = $target.data('checkout-url'),
                    productId = $target.closest('form').find(this.options.productId).val(),
                    originalForm = this.options.originalForm.replace('%1', productId),
                    self = this;

                e.preventDefault();

                if (this.options.confirmUrl && this.options.confirmMessage) {
                    confirm({
                        content: this.options.confirmMessage,
                        actions: {

                            /**
                             * Confirmation handler
                             *
                             */
                            confirm: function () {
                                returnUrl = self.options.confirmUrl;
                                self._redirect(returnUrl, originalForm);
                            },

                            /**
                             * Cancel confirmation handler
                             *
                             */
                            cancel: function () {
                                self._redirect(returnUrl);
                            }
                        }
                    });

                    return false;
                }

                this._redirect(returnUrl, originalForm);

            }, this));
        },

        /**
         * Redirect to certain url, with optional form
         * @param {String} returnUrl
         * @param {HTMLElement} originalForm
         *
         */
        _redirect: function (returnUrl, originalForm) {
            var $form,
                ppCheckoutInput;

            if (this.options.isCatalogProduct && originalForm) {
                // find the form from which the button was clicked
                $form = originalForm ? $(originalForm) : $($(this.options.shortcutContainerClass).closest('form'));

                ppCheckoutInput = $form.find(this.options.ppCheckoutSelector)[0];

                if (!ppCheckoutInput) {
                    ppCheckoutInput = $(this.options.ppCheckoutInput);
                    ppCheckoutInput.appendTo($form);
                }
                $(ppCheckoutInput).val(returnUrl);

                $form.submit();
            } else {
                $.mage.redirect(returnUrl);
            }
        }
    });

    return $.mage.dotpayCheckout;
});
