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
 * @copyright PayPro S.A.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace Dotpay\Payment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Change status after placing the order.
 */
class ChangeStatusAfterPlace implements ObserverInterface
{
    /**
     * @var \Dotpay\Payment\Helper\Data\Configuration Helper prividing configuration data of Dotpay payment module
     */
    private $configHelper;

    /**
     * Initialize the observer.
     *
     * @param Dotpay\Payment\Helper\Data\Configuration $configHelper Configuration helper
     */
    public function __construct(\Dotpay\Payment\Helper\Data\Configuration $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * Change pending status of placed order according to Dotpay payment module configuration.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $methodCode = $order->getPayment()->getMethodInstance()->getCode();
        if (strpos($methodCode, 'dotpay') !== false) {
            $order->setStatus($this->configHelper->getStatusPending());
            $order->save();
        }
    }
}
