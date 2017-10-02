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

use Dotpay\Provider\ConfigurationProviderInterface;

/**
 * Provider of Dotpay module's configuration data.
 */
class Configuration implements ConfigurationProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface Magento scope config
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface Magento store manager
     */
    private $storeManager;

    /**
     * Name of Dotpay payment module.
     */
    const PLUGIN_ID = DOTPAY_MODNAME;

    /**
     * @var string Scope of configuration
     */
    private $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getPluginId()
    {
        return self::PLUGIN_ID;
    }

    public function getEnable()
    {
        return $this->getData('payment/dotpay_main/active');
    }

    public function getId()
    {
        return $this->getData('payment/dotpay_main/id');
    }

    public function getPin()
    {
        return $this->getData('payment/dotpay_main/pin');
    }

    public function getTestMode()
    {
        return $this->getData('payment/dotpay_main/test');
    }

    public function getUsername()
    {
        return $this->getData('payment/dotpay_main/username');
    }

    public function getPassword()
    {
        return $this->getData('payment/dotpay_main/password');
    }

    public function getInstructionVisible()
    {
        return $this->getData('payment/dotpay_main/instruction');
    }

    public function isDisplayedLogo()
    {
        return $this->getData('payment/dotpay_main/display_logo');
    }

    public function getRedirectUrl()
    {
        return $this->getData('payment/dotpay_main/redirect_url');
    }

    public function getBackUrl()
    {
        return $this->getData('payment/dotpay_main/back_url');
    }

    public function getStatusUrl()
    {
        return $this->getData('payment/dotpay_main/status_url');
    }

    public function getConfirmUrl()
    {
        return $this->getData('payment/dotpay_main/confirm_url');
    }

    public function getOcManageUrl()
    {
        return $this->getData('payment/dotpay_main/oc_manage_url');
    }

    public function getOcRemoveUrl()
    {
        return $this->getData('payment/dotpay_main/oc_remove_url');
    }

    public function getStatusPending()
    {
        return $this->getData('payment/dotpay_main/order_status');
    }

    public function getStatusComplete()
    {
        return $this->getData('payment/dotpay_main/status_complete');
    }

    public function getStatusCanceled()
    {
        return $this->getData('payment/dotpay_main/status_canceled');
    }

    public function getOcVisible()
    {
        return $this->getData('payment/dotpay_oc/active');
    }

    public function getCcVisible()
    {
        return $this->getData('payment/dotpay_cc/active');
    }

    public function getFccVisible()
    {
        return $this->getData('payment/dotpay_cc/fcc_active');
    }

    public function getFccId()
    {
        return $this->getData('payment/dotpay_cc/fcc_id');
    }

    public function getFccPin()
    {
        return $this->getData('payment/dotpay_cc/fcc_pin');
    }

    public function getFccCurrencies()
    {
        return $this->getData('payment/dotpay_cc/fcc_currencies');
    }

    public function getMpVisible()
    {
        return $this->getData('payment/dotpay_mp/active');
    }

    public function getBlikVisible()
    {
        return $this->getData('payment/dotpay_blik/active');
    }

    public function getPaypalVisible()
    {
        return $this->getData('payment/dotpay_paypal/active');
    }

    public function getWidgetVisible()
    {
        return $this->getData('payment/dotpay_widget/widget');
    }

    public function getWidgetCurrencies()
    {
        return $this->getData('payment/dotpay_widget/disable_currencies');
    }

    /**
     * Check if refunds requesting is enabled from a shop system.
     *
     * @return bool
     */
    public function getRefundsEnable()
    {
        return false;
    }

    public function getRenew()
    {
        return false;
    }

    public function getRenewDays()
    {
        return 0;
    }

    public function getSurcharge()
    {
        return false;
    }

    public function getSurchargeAmount()
    {
        return 0;
    }

    public function getSurchargePercent()
    {
        return 0;
    }

    public function getShopName()
    {
        return $this->storeManager->getStore()->getName();
    }

    public function getMultimerchant()
    {
        return false;
    }

    public function getApi()
    {
        return 'dev';
    }

    public function getShowShortcut()
    {
        return $this->getData('payment/dotpay_main/show_shortcut');
    }

    private function getData($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            $this->storeScope
        );
    }
}
