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
use Dotpay\Model\Configuration;

/**
 * Admin front end model of show or hide channel for specific conditions used in payment module configuration.
 */
class Visibility implements ArrayInterface
{
    const ALWAYS = 1;
    const LOGGED_IN = 2;
    const NOT_LOGGED_IN = 3;


    /**
     * Return array list list of specific conditions for Dotpay module.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ALWAYS, 'label' => __('Always show')],
            ['value' => self::LOGGED_IN, 'label' => __('Show only for logged in user')],
            ['value' => self::NOT_LOGGED_IN, 'label' => __('Show only for guest user')]

        ];
    }
}
