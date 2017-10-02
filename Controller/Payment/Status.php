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
use Dotpay\Processor\Status as StatusProcessor;

/**
 * Controller of checking payment status in background.
 */
class Status extends Dotpay
{
    /**
     * @var \Magento\Framework\App\RequestInterface Request object
     */
    private $request;

    /**
     * @var \Dotpay\Payment\Helper\Data\Configuration Configuration data provider
     */
    private $configHelper;

    /**
     * @var \Magento\Sales\Model\Order Order model
     */
    private $orderModel;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory Factory of Magento JSON result
     */
    private $resultJsonFactory;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory
     * @param \Dotpay\Payment\Helper\Url                       $urlHelper
     * @param \Magento\Sales\Model\Order                       $orderModel
     * @param \Dotpay\Payment\Helper\Data\Configuration        $configHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Magento\Sales\Model\Order $orderModel,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
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
        $this->orderModel = $orderModel;
        $this->configHelper = $configHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute action of the controller.
     *
     * @return mixed
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $status = new StatusProcessor();
            $order = $this->orderModel->load($this->request->getParam('orderId'));
            if ($order->getId() === null) {
                $status->codeNotExist();

                return $this->returnData($status);
            }
            $status->setStatus($order->getStatusLabel());
            switch ($order->getStatus()) {
                case $this->configHelper->getStatusPending():
                    $status->codePending();
                    break;
                case $this->configHelper->getStatusComplete():
                    $status->codeSuccess();
                     $this->messageManager->addSuccess(__('Payment has been finished successfully.'));
                    break;
                case $this->configHelper->getStatusCanceled():
                    $status->codeError();
                    break;
                default:
                    $status->codeOtherStatus();
            }

            return $this->returnData($status);
        } else {
            die('Please call AJAX request');
        }
    }

    /**
     * Return Magento JSON result which contains data from SDK status processor.
     *
     * @param \Dotpay\Processor\Status $status Object of SDK status processor
     *
     * @return mixed
     */
    private function returnData(\Dotpay\Processor\Status $status)
    {
        $result = $this->resultJsonFactory->create();
        $result->setHeader('Content-Type', 'application/json; charset=utf-8', true);

        return $result->setData($status->getData());
    }
}
