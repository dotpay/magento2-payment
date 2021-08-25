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
use Dotpay\Model\Customer;

/**
 * Admin front end model of languages used in payment module configuration.
 */
class Language implements ArrayInterface
{
    /**
     * Return array list of available languages for Dotpay module.
     *
     * @param bool $isMultiselect A flag if multi select functionality is available in frond end field
     *
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $options = [];
        if ($isMultiselect) {
            $options[] = ['value' => '', 'label' => __('--Please Select--')];
        }
        foreach (Customer::$LANGUAGES as $language) {
            $options[] = ['value' => $language, 'label' => $language];
        }

        return $options;
    }
}
