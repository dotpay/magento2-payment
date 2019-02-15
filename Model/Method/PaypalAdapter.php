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

namespace Dotpay\Payment\Model\Method;

use Dotpay\Payment\Model\Adminhtml\System\Config\Visibility;

/**
 * Adapter for payment method using PayPal
 */
class PaypalAdapter extends AbstractAdapter
{

    /**
     * Return an information if the PayPal is enabled.
     *
     * @param int/null $storeId Id of the store
     *
     * @return bool
     */
    public function isActive($storeId = null)
    {
        $visibility = $this->scopeConfig->getValue(
            'payment/dotpay_paypal/visibility',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($visibility)
        {
            switch($visibility)
            {
                case Visibility::ALWAYS:
                    return parent::isActive($storeId);
                case Visibility::LOGGED_IN:
                    return parent::isActive($storeId) && $this->customerSession->isLoggedIn();
                case Visibility::NOT_LOGGED_IN:
                    return parent::isActive($storeId) && !$this->customerSession->isLoggedIn();
            }
        }
        return parent::isActive($storeId);
    }

    /**
     * Return SDK configuration object with general information including Paypal settings.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        $config = parent::getConfiguration();
        $config->setPaypalVisible($this->isActive());
        return $config;
    }
}
