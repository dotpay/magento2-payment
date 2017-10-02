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

namespace Dotpay\Payment\Controller\Payment;

use Dotpay\Payment\Controller\Dotpay;
use Dotpay\Resource\RegisterOrder;
use Dotpay\Tool\Curl;

/**
 * Controller for preparing form with details of payment.
 */
class Preparing extends Dotpay
{
    /**
     * @var \Dotpay\Payment\Model\Instruction Object of payment instruction model
     */
    private $instructionModel;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dotpay\Payment\Helper\Url                 $urlHelper
     * @param \Dotpay\Payment\Helper\Data\Configuration  $configHelper
     * @param \Dotpay\Payment\Model\Instruction          $instructionModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        \Dotpay\Payment\Model\Instruction $instructionModel
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
        $this->instructionModel = $instructionModel;
    }

    /**
     * Execute action of the controller.
     */
    public function execute()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if ($order === null || $order->getId() === null) {
            die(__('Order is not found'));
        }

        $payment = $order->getPayment();
        if ($payment === null || $payment->getId() === null) {
            die(__('Payment is not found'));
        }

        $factory = $this->getFactory($payment->getMethod());
        $channel = $factory->getChannel($payment->getAdditionalInformation());

        if ($channel->canHaveInstruction()) {
            return $this->createPaymentWithInstruction($channel);
        } else {
            $this->coreRegistry->register('formData', $channel->getHiddenForm());
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->renderLayout();
        }
    }

    /**
     * Create new payment with instruction based on data contained in the given payment channel.
     *
     * @param \Dotpay\Channel\Channel $channel Payment channel used for the payment
     *
     * @return
     */
    private function createPaymentWithInstruction(\Dotpay\Channel\Channel $channel)
    {
        $registerOrder = new RegisterOrder(
            $this->config,
            new Curl()
        );
        $instruction = $registerOrder->create($channel)->getInstruction();
        $dbInstruction = $this->instructionModel->load($instruction->getOrderId(), 'order_id');

        $dbInstruction->setOrderId($instruction->getOrderId())
                      ->setNumber($instruction->getNumber())
                      ->setBankAccount($instruction->getBankAccount())
                      ->setChannel($instruction->getChannel())
                      ->setHash($instruction->getHash())
                      ->setAmount($instruction->getAmount())
                      ->setCurrency($instruction->getCurrency());
        $dbInstruction->save();

        $resultRedirect = $this->context->getResultRedirectFactory()->create();
        $resultRedirect->setPath($this->urlHelper->getInstructionUrl().'?orderId='.$instruction->getOrderId());

        return $resultRedirect;
    }
}
