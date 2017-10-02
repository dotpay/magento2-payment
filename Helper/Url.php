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

namespace Dotpay\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Dotpay\Payment\Model\Method\AbstractAdapter;
use Dotpay\Model\Configuration;

class Url extends AbstractHelper
{
    /**
     * @var \Dotpay\Payment\Model\Method\AbstractAdapter Abstract payment method adapter
     */
    private $adapter;

    /**
     * @var \Dotpay\Model\Configuration SDK configuration of the payment module
     */
    private $config;

    /**
     * Initialize the helper.
     *
     * @param Context         $context
     * @param AbstractAdapter $adapter
     */
    public function __construct(Context $context, AbstractAdapter $adapter)
    {
        parent::__construct($context);
        $this->adapter = $adapter;
        $this->config = new Configuration(DOTPAY_MODNAME);
        $this->config->setTestMode($this->adapter->isTestMode());
    }

    /**
     * Return url to Dotpay payment service.
     *
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->config->getPaymentUrl();
    }

    /**
     * Return url to Dotpay seller API.
     *
     * @return string
     */
    public function getSellerUrl()
    {
        return $this->config->getPaymentUrl();
    }

    /**
     * Return an url according to the given custom part.
     *
     * @param string $customUrl Last personalized part of requested url
     *
     * @return string
     */
    public function getUrl($customUrl)
    {
        return $this->_urlBuilder->getUrl(
            $customUrl, [
                '_secure' => $this->_getRequest()->isSecure(),
            ]
        );
    }

    /**
     * Return url to the "back" page.
     *
     * @param string $customUrl Custom part of the url pointing at "back" page
     *
     * @return string
     */
    public function getBackUrl($customUrl = null)
    {
        if ($customUrl) {
            return $this->getUrl($customUrl);
        } else {
            return $this->getUrl($this->adapter->getBackUrl());
        }
    }

    /**
     * Return url to the page of payment notifications.
     *
     * @param string $customUrl Custom part of the url pointing at page of payment notifications
     *
     * @return string
     */
    public function getNotificationUrl($customUrl = null)
    {
        if ($customUrl) {
            return $this->getUrl($customUrl);
        } else {
            return $this->getUrl($this->adapter->getConfirmUrl());
        }
    }

    /**
     * Return url to the place where status of payment is checked.
     *
     * @param string $customUrl Custom part of the url pointing at place where status of payment is checked
     *
     * @return string
     */
    public function getStatusUrl($customUrl = null)
    {
        if ($customUrl) {
            return $this->getUrl($customUrl);
        } else {
            return $this->getUrl($this->adapter->getStatusUrl());
        }
    }

    /**
     * Return url to the page of payment preparing.
     *
     * @param string $customUrl Custom part of the url pointing at page of payment preparing
     *
     * @return string
     */
    public function getPreparingUrl($customUrl = null)
    {
        if ($customUrl) {
            return $this->getUrl($customUrl);
        } else {
            return $this->getUrl($this->adapter->getRedirectUrl());
        }
    }

    /**
     * Return url to the page where instruction of completing payment is displayed.
     *
     * @param string $customUrl Custom part of the url pointing at page where instruction of completing payment is displayed
     *
     * @return string
     */
    public function getInstructionUrl($customUrl = null)
    {
        if ($customUrl) {
            return $this->getUrl($customUrl);
        } else {
            return $this->getUrl($this->adapter->getInstructionUrl());
        }
    }

    /**
     * Return url to the page with managing of saved credit cards.
     *
     * @param string $customUrl Custom part of the url pointing at page with managing of saved credit cards
     *
     * @return string
     */
    public function getOcManageUrl($customUrl = null)
    {
        if ($customUrl) {
            return $this->getUrl($customUrl);
        } else {
            return $this->getUrl($this->adapter->getOcManageUrl());
        }
    }

    /**
     * Return url to the place where saved credit card are removed.
     *
     * @param string $customUrl Custom part of the url pointing at place where saved credit card are removed
     *
     * @return string
     */
    public function getOcRemoveUrl($customUrl = null)
    {
        if ($customUrl) {
            return $this->getUrl($customUrl);
        } else {
            return $this->getUrl($this->adapter->getOcRemoveUrl());
        }
    }
}
