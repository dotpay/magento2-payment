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

namespace Dotpay\Payment\Model\Adminhtml\System\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;


/**
 * Admin front end model of Shopname used in payment module configuration.
 */
class ShopName implements ArrayInterface
{

    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
       }


    /**
     * Return array list of available shopname for Dotpay module.
     *
     * @param bool $isMultiselect A flag if multi select functionality is available in frond end field
     *
     * @return array
     */
        
        public function toOptionArray() : array
        {

            $name_store_info = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $name_general = $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $name_customer_support = $this->scopeConfig->getValue('trans_email/ident_support/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


            return [
                    ['value' => 'storeinfo', 'label' => __('Name from: Store Information').' ['.$name_store_info.']'],
                    ['value' => 'general', 'label' => __('Name from: General Contact').' ['.$name_general.']'],
                    ['value' => 'customer', 'label' => __('Name from: Customer Support').' ['.$name_customer_support.']'],
                    ['value' => 'empty', 'label' => __("Name from: Settings from the Dotpay panel [don't send the name]")]
                 ];
        }

}
