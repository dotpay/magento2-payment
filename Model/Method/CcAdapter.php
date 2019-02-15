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
 * Adapter for payment method using credit cards.
 */
class CcAdapter extends AbstractAdapter
{
    /**
     * Return an information if the card channel using One Click is enabled.
     *
     * @param int/null $storeId Id of the store
     *
     * @return bool
     */
    public function isActive($storeId = null)
    {
        $visibility = $this->scopeConfig->getValue(
            'payment/dotpay_cc/visibility',
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
     * Return an information if the Fcc channel is available.
     *
     * @param int/null $storeId Id of the store
     *
     * @return bool
     */
    public function isFccActive($storeId = null)
    {
        return $this->getConfigData('fcc_active', $storeId);
    }

    /**
     * Return seller id for the Fcc channel.
     *
     * @param int/null $storeId Id of the store
     *
     * @return int
     */
    public function getFccId($storeId = null)
    {
        return $this->getConfigData('fcc_id', $storeId);
    }

    /**
     * Return seller pin for the Fcc channel.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getFccPin($storeId = null)
    {
        return $this->getConfigData('fcc_pin', $storeId);
    }

    /**
     * Return a string with list of currencies for which the "credit card channel for foreign currencies" is available.
     * Every currency code is separated by comma.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getFccCurrencies($storeId = null)
    {
        return $this->getConfigData('fcc_currencies', $storeId);
    }

    /**
     * Return SDK configuration object with general information including credit cards settings.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        $config = parent::getConfiguration();
        $config->setCcVisible($this->isActive())
               ->setFccVisible($this->isFccActive())
               ->setFccId($this->getFccId())
               ->setFccPin($this->getFccPin())
               ->setFccCurrencies($this->getFccCurrencies());

        return $config;
    }
}
