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

use Dotpay\Model\Payer;
use Dotpay\Provider\PaymentLinkProviderInterface;

/**
 * Provider of payment link data.
 */
class PaymentLink implements PaymentLinkProviderInterface
{

    /**
     * @var \Magento\Sales\Model\Order $order Magento order object
     */
    private $order = null;

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
     * @inheritDoc
     */
    public function getType()
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return round($this->order->getGrandTotal(), 2);
    }

    /**
     * @inheritDoc
     */
    public function getCurrency()
    {
        return $this->order->getOrderCurrencyCode();
    }

    /**
     * @inheritDoc
     */
    public function getControl()
    {
        return $this->order->getEntityId();
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return __('Order ID: %1', $this->order->getRealOrderId())."/".$this->getControl()."/".__('RE');
    }

    /**
     * @inheritDoc
     */
    public function getPayer()
    {
        return new Customer($this->order);
    }

    /**
     * @inheritDoc
     */
    public function getIgnoreLastPaymentChannel()
    {
        return 1;
    }

}