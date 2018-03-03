<?php

namespace Dotpay\Payment\Model\Config\Backend;

class Value extends \Magento\Framework\App\Config\Value
{
    /**
     * Return value from the given configuration path
     *
     * @param string $configPath Configuration path
     *
     * @return mixed
     */
    protected function getConfigData($configPath)
    {
        return $this->_config->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the Dotpay payment is enabled on shop site
     *
     * @return boolean
     */
    protected function isEnabled() {
        return (bool) $this->getConfigData('payment/dotpay_main/active');
    }
}