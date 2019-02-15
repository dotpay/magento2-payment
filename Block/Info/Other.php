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

namespace Dotpay\Payment\Block\Info;

/**
 * Block of information about payment method (by Dotpay other channel) displayed in order preview.
 */
class Other extends \Magento\Payment\Block\Info
{
    /**
     * @var string Path to template file
     */
    protected $_template = 'Dotpay_Payment::info/info.phtml';

    /**
     * Return a message about used payment method.
     *
     * @return string
     */
    public function getMessage()
    {
        return __('Dotpay payment channels');
    }
}
