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

namespace Dotpay\Payment\Controller\Oneclick;

use Dotpay\Payment\Controller\Dotpay;
use Dotpay\Payment\Api\Data\CreditCardInterface;

/**
 * Controller of removing saved credit cards
 */
class Remove extends Dotpay
{
    /**
     * @var \Magento\Framework\App\RequestInterface Request object
     */
    private $request;
    
    /**
     * @var \Dotpay\Payment\Model\CreditCardFactory Factory of credit card's model
     */
    private $ccCollectionFactory;
    
    /**
     * Initialize the controller
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dotpay\Payment\Helper\Url $urlHelper
     * @param \Dotpay\Payment\Model\Resource\CreditCard\CollectionFactory $ccCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        \Dotpay\Payment\Model\Resource\CreditCard\CollectionFactory $ccCollectionFactory
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $coreRegistry,
            $resultPageFactory,
            $urlHelper,
            $configHelper
        );
        $this->request = $context->getRequest();
        $this->ccCollectionFactory = $ccCollectionFactory;
    }
    
    /**
     * Execute action of the controller.
     */
    public function execute()
    {
        if($this->customerSession->isLoggedIn()) {
            $cardId = $this->request->getParam('cardId');
            $customerId = $this->customerSession->getCustomer()->getId();
            $cards = $this->ccCollectionFactory
                    ->create()
                    ->addFilter(CreditCardInterface::CUSTOMER_ID, $customerId)
                    ->addFilter(CreditCardInterface::CARD_ID, $cardId);
            if(!count($cards)) {
                die(__('Requested card doesn\'t exist.'));
            }
            $cards->getFirstItem()->delete();
            die('OK');
        } else {
            die(__('You have to be logged to delete your saved credit card.'));
        }
    }
}