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

namespace Dotpay\Payment\Helper\Data;

use Dotpay\Provider\PaymentProviderInterface;

/**
 * Provider of payment data.
 */
class Payment implements PaymentProviderInterface
{
    /**
     * @var \Magento\Sales\Model\Order/null Magento order object
     */
    private $order = null;

    /**
     * @var \Dotpay\Model\Seller/null SDK seller object
     */
    private $seller = null;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Sales\Model\Order $order Magento order object
     */
    public function __construct(
        \Magento\Sales\Model\Order $order
    ) {
        $this->order = $order;
    }

    /**
     * Load a proper seller to the provider.
     *
     * @param \Dotpay\Model\Seller $seller  SDK seller object
     * @param int                  $orderId Order if
     */
    public function load(\Dotpay\Model\Seller $seller, $orderId)
    {
        $this->seller = $seller;
        $this->order = $this->order->load($orderId);
    }

    /**
     * Return an id of the order.
     *
     * @return int
     */
    public function getId()
    {
        return $this->order->getId();
    }

    /**
     * Return an amount of the order.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->order->getGrandTotal();
    }

    /**
     * Return a currency code of the order.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->order->getOrderCurrencyCode();
    }

    /**
     * Return a description of the payment.
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Order ID: %1', $this->order->getRealOrderId());
    }

    /**
     * Return a Seller model for the payment.
     *
     * @return Seller
     */
    public function getSeller()
    {
        return $this->seller;
    }
}
