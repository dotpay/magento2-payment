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
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'dotpay_oc',
                component: 'Dotpay_Payment/js/view/payment/method-renderer/dotpay-oc-method'
            },
            {
                type: 'dotpay_cc',
                component: 'Dotpay_Payment/js/view/payment/method-renderer/dotpay-cc-method'
            },
            {
                type: 'dotpay_mp',
                component: 'Dotpay_Payment/js/view/payment/method-renderer/dotpay-mp-method'
            },
            {
                type: 'dotpay_blik',
                component: 'Dotpay_Payment/js/view/payment/method-renderer/dotpay-blik-method'
            },
            {
                type: 'dotpay_paypal',
                component: 'Dotpay_Payment/js/view/payment/method-renderer/dotpay-paypal-method'
            },
            {
                type: 'dotpay_other',
                component: 'Dotpay_Payment/js/view/payment/method-renderer/dotpay-other-method'
            },
            {
                type: 'dotpay_widget',
                component: 'Dotpay_Payment/js/view/payment/method-renderer/dotpay-widget-method'
            }
        );
        return Component.extend({});
    }
);