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

namespace Dotpay\Payment\Model\Factory;

use Dotpay\Channel\Oc;
use Dotpay\Payment\Api\Data\CreditCardInterface;

/**
 * Factory which produces SDK channel for credit cards using One Click mode.
 */
class OcFactory extends AbstractFactory
{
    /**
     * @var \Dotpay\Payment\Model\CreditCardFactory Factory to build models of credit cards
     */
    private $cardFactory;

    /**
     * Initialize the factory.
     *
     * @param \Magento\Customer\Model\Session              $customerSession
     * @param \Magento\Checkout\Model\Session              $checkoutSession
     * @param \Dotpay\Payment\Helper\Locale                $localeHelper
     * @param \Dotpay\Payment\Helper\Url                   $urlHelper
     * @param \Dotpay\Payment\Model\Method\AbstractAdapter $paymentAdapter
     * @param \Dotpay\Payment\Model\CreditCardFactory      $cardFactory
     */
    public function __construct(\Magento\Customer\Model\Session $customerSession,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Dotpay\Payment\Helper\Locale $localeHelper,
                                \Dotpay\Payment\Helper\Url $urlHelper,
                                \Dotpay\Payment\Model\Method\AbstractAdapter $paymentAdapter,
                                \Dotpay\Payment\Model\CreditCardFactory $cardFactory
                                ) {
        $this->cardFactory = $cardFactory;
        parent::__construct(
            $customerSession,
            $checkoutSession,
            $localeHelper,
            $urlHelper,
            $paymentAdapter
        );
    }

    /**
     * Return SDK One Clikc credit card channel filled by all necessary data.
     *
     * @param array $additionalInformation Additional information about payment
     *
     * @return Oc
     */
    public function getChannel($additionalInformation)
    {
        $channel = new Oc($this->paymentAdapter->getConfiguration(),
                          $this->getTransaction(),
                          $this->getPaymentResource(),
                          $this->getSellerResource());
        $channel->setSeller($this->getSeller());
        $cardModel = $this->cardFactory->create();
        if ($additionalInformation['selectedMode'] === 'register') {
            
            $existingCards = $cardModel->getCollection()
                                      ->addFilter(CreditCardInterface::CUSTOMER_ID, $this->customerSession->getCustomer()->getId())
                                      ->addFilter(CreditCardInterface::ORDER_ID, $this->checkoutSession->getLastRealOrder()->getId());
            if (!count($existingCards)) {
                $cardModel->setCustomer($this->customerSession->getCustomer())
                          ->setOrder($this->checkoutSession->getLastRealOrder());
                $cardModel->save();
            } else {
                $cardModel = $existingCards->getFirstItem();
            }
            $channel->setCard($cardModel->getSdkObject());
        } else {
            $dbCard = $cardModel->load($additionalInformation['selectedCard']);
            if ($dbCard !== null) {
                $channel->setCard($dbCard->getSdkObject());
            }
        }

        return $channel;
    }
}
