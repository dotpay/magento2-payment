<?php
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

namespace Dotpay\Payment\Model\Config\Source\Order;

/**
 * Model of list of all statuses including pending payment states.
 */
class ShippingMapping implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var array $_shippingMethods A list of store's available shipping methods
     */

    const DOTPAY_METHODS = [
        'COURIER' => "Kurier",
        'POCZTA_POLSKA' => "Poczta Polska",
        'PICKUP_POINT' => "Dostawa do punktu (np. UPSAccess point, DHL Parcel Shop",
        'PACZKOMAT' => "Paczkomat",
        'PACZKA_W_RUCHU' => "Paczka w Ruchu",
        'PICKUP_SHOP' => "Odbiór w sklepie (click&collect)"
    ];


    /**
     * @return array
     */
    public function toOptionArray()
    {

        $options = [['value' => '', 'label' => __('-- Please Select --')]];

        return $options;
    }
}
