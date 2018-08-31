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

use Dotpay\Model\Operation;
use Dotpay\Provider\NotificationProviderInterface;

/**
 * Provider of notification when payment is confirming by Dotpay.
 */
class Notification implements NotificationProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface Magento request object
     */
    private $request;

    /**
     * @var \Dotpay\Payment\Helper\Data\Operation Payment data provider
     */
    private $operation;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Framework\App\RequestInterface $request   Magento request object
     * @param \Dotpay\Payment\Helper\Data\Operation   $operation Payment data provider
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Dotpay\Payment\Helper\Data\Operation $operation
    ) {
        $this->request = $request;
        $this->operation = Operation::createFromData($operation);
    }

    /**
     * Return an Operation object with details of operation which relates the notification.
     *
     * @return \Dotpay\Payment\Helper\Data\Operation
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Return an email of a seller.
     *
     * @return string
     */
    public function getShopEmail()
    {
        return $this->request->getParam('p_email');
    }

    /**
     * Return a name of a shop.
     *
     * @return string
     */
    public function getShopName()
    {
        return $this->request->getParam('p_info');
    }

    /**
     * Return an id of used payment channel.
     *
     * @return int
     */
    public function getChannelId()
    {
        return $this->request->getParam('channel');
    }

    /**
     * Return a codename of a country of the payment instrument from which payment was made.
     *
     * @return string
     */
    public function getChannelCountry()
    {
        return $this->request->getParam('channel_country');
    }

    /**
     * Return a codename of a country resulting from IP address from which the payment was made.
     *
     * @return string
     */
    public function getIpCountry()
    {
        return $this->request->getParam('geoip_country');
    }

    /**
     * Return a checksum of a Dotpay notification.
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->request->getParam('signature');
    }
}
