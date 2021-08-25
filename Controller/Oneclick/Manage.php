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
 * Controller of managing saved credit cards.
 */
class Manage extends Dotpay
{
    /**
     * @var \Dotpay\Payment\Helper\Data\Configuration Provider of Dotpay module configuration data
     */
    private $configHelper;

    /**
     * @var \Dotpay\Payment\Model\Resource\CreditCard\CollectionFactory Factory of credit cards collection
     */
    private $ccCollectionFactory;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Framework\App\Action\Context                       $context
     * @param \Magento\Customer\Model\Session                             $customerSession
     * @param \Magento\Checkout\Model\Session                             $checkoutSession
     * @param \Magento\Framework\Registry                                 $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory                  $resultPageFactory
     * @param \Dotpay\Payment\Helper\Url                                  $urlHelper
     * @param \Dotpay\Payment\Helper\Data\Configuration                   $configHelper
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
        $this->configHelper = $configHelper;
        $this->ccCollectionFactory = $ccCollectionFactory;
    }

    /**
     * Execute action of the controller.
     *
     * @return mixed
     */
    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
            $savedCards = $this->ccCollectionFactory
                    ->create()
                    ->addFilter(CreditCardInterface::CUSTOMER_ID, $customerId);


            // not so elegant hack to remove cards with incomplete info added with an error
            foreach($savedCards as $card)
            {
                if($card->getMask() === null)
                {
                    $card->delete();
                }
            }

            $this->coreRegistry->register('data', [
                'cards' => $savedCards,
                'onRemoveMessage' => __('Do you want to deregister a saved card'),
                'onDoneMessage' => __('The card was deregistered from the shop'),
                'onFailureMessage' => __('An error occurred while deregistering the card'),
                'removeUrl' => $this->urlHelper->getOcRemoveUrl($this->configHelper->getOcRemoveUrl()),
            ]);
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->renderLayout();
        } else {
            $this->messageManager->addError(__('You have to be logged to see your saved credit cards.'));

            return $this->resultRedirectFactory->create()->setPath('/', ['_current' => true]);
        }
    }
}
