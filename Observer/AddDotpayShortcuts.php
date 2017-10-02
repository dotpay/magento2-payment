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

namespace Dotpay\Payment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

/**
 * Add shortcut of Dotpay payment to cart container.
 */
class AddDotpayShortcuts implements ObserverInterface
{
    /**
     * @var Dotpay\Payment\Helper\Data\Configuration Helper which provides configuration data of Dotpay payment module
     */
    protected $configHelper;

    /**
     * Initialize the observer.
     *
     * @param Dotpay\Payment\Helper\Data\Configuration $configHelper Configuration helper
     */
    public function __construct(
        \Dotpay\Payment\Helper\Data\Configuration $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * Add Dotpay shortcut buttons.
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        if ($this->configHelper->getEnable() && $this->configHelper->getShowShortcut()) {
            $shortcutButtons = $observer->getEvent()->getContainer();

            $params = [
            ];

            $shortcut = $shortcutButtons->getLayout()->createBlock(
                'Dotpay\Payment\Block\Shortcut',
                '',
                $params
            );
            $shortcut->setIsInCatalogProduct(
                $observer->getEvent()->getIsCatalogProduct()
            )->setShowOrPosition(
                $observer->getEvent()->getOrPosition()
            );
            $shortcutButtons->addShortcut($shortcut);
        }
    }
}
