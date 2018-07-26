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
use Dotpay\Payment\Api\Data\CreditCardInterface;

/**
 * Delete all credit cards saved for deleting customer.
 */
class DeleteCustomer implements ObserverInterface
{
    /**
     * @var \Dotpay\Payment\Model\CreditCard Model of saved credit card
     */
    protected $ccModel;

    /**
     * Initialize the observer.
     *
     * @param \Dotpay\Payment\Model\CreditCard $ccModel
     */
    public function __construct(
        \Dotpay\Payment\Model\CreditCard $ccModel
    ) {
        $this->ccModel = $ccModel;
    }

    /**
     * Remove saved cards of deleted user.
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $customerId = $observer->getEvent()->getCustomer()->getCustomerId();
        $cards = $this->ccModel->getCollection()
                                ->addFilter(CreditCardInterface::CUSTOMER_ID, $customerId);
        if(count($cards) > 0)
        {
            foreach($cards as $card)
            {
                $card->delete();
            }
        }
    }
}
