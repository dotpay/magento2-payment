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

namespace Dotpay\Payment\Model\Method;

/**
 * Adapter for payment method using Dotpay widget.
 */
class WidgetAdapter extends AbstractAdapter
{
    /**
     * Return an information if the Dotpay widget channel is enabled.
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->isMainActive($storeId);// && $this->getVisible();
    }

    /**
     * @inheritdoc
     */
    public function canUseForCurrency($currencyCode)
    {
        if($this->getDisableCurrencies()) {
            $currencies = explode(",", $this->getDisableCurrencies());
            if(count($currencies) > 0 && in_array($currencyCode, $currencies))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Return an information if Dtpay widget is visible.
     * If not then customer can choose the payment channel in Dotpay site.
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function getVisible($storeId = null)
    {
        return (bool) $this->getConfigData('widget', $storeId);
    }

    /**
     * Return string containing a list of currencies. When one of those is used, widget will not be displayed.
     * Currency codes are separated by comma.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getDisableCurrencies($storeId = null)
    {
        return $this->getConfigData('disable_currencies', $storeId);
    }

    /**
     * Return SDK configuration object with general information including widget settings.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        $config = parent::getConfiguration();
        $config->setWidgetVisible($this->getVisible())
               ->setWidgetCurrencies($this->getDisableCurrencies());

        return $config;
    }
}
