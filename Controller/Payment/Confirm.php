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

use Dotpay\Action\UpdateCcInfo;
use Dotpay\Action\MakePaymentOrRefund;
use Dotpay\Model\CreditCard;
use Dotpay\Model\Operation;
use Dotpay\Model\Notification;
use Dotpay\Model\Payment;
use Dotpay\Payment\Api\Data\OrderInterface;
use Dotpay\Processor\Confirmation;
use Dotpay\Tool\Curl;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Payment\Controller\Dotpay;
use Dotpay\Payment\Api\Data\CreditCardInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Controller of payment confirmation by Dotpay server.
 */
class Confirm extends Dotpay implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Sales\Model\Order Magento order model
     */
    private $orderModel;

    /**
     * @var \Magento\Customer\Model\Customer Magento customer model
     */
    private $customerModel;

    /**
     * @var \Dotpay\Payment\Helper\Data\Configuration Provider of Dotpay module configuration data
     */
    private $configHelper;

    /**
     * @var \Dotpay\Payment\Model\CardBrand Mode of saved credit card
     */
    private $ccModel;

    /**
     * @var \Dotpay\Payment\Model\CardBrand Model of credit card's brand
     */
    private $ccBrandModel;

    /**
     * @var \Dotpay\Payment\Helper\Data\Notification Data provider for SDK notification object
     */
    private $notification;

    /**
     * @var \Dotpay\Payment\Helper\Data\Payment Data provider for SDK payment object
     */
    private $payment;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\Order                 $orderModel
     * @param \Magento\Customer\Model\Customer           $customerModel
     * @param \Dotpay\Payment\Helper\Url                 $urlHelper
     * @param \Dotpay\Payment\Helper\Data\Configuration  $configHelper
     * @param \Dotpay\Payment\Model\CreditCard           $ccModel
     * @param \Dotpay\Payment\Model\CardBrand            $ccBrandModel
     * @param \Dotpay\Payment\Helper\Data\Notification   $notificationModel
     * @param \Dotpay\Payment\Helper\Data\Payment        $paymentModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Customer\Model\Customer $customerModel,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        \Dotpay\Payment\Model\CreditCard $ccModel,
        \Dotpay\Payment\Model\CardBrand $ccBrandModel,
        \Dotpay\Payment\Helper\Data\Notification $notificationModel,
        \Dotpay\Payment\Helper\Data\Payment $paymentModel
    ) {
        if(!$notificationModel->getOperation())
        {
            throw new NotFoundException(__("Page not found"));
        }
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $coreRegistry,
            $resultPageFactory,
            $urlHelper,
            $configHelper
        );
        $this->orderModel = $orderModel;
        $this->customerModel = $customerModel;
        $this->configHelper = $configHelper;
        $this->ccModel = $ccModel;
        $this->ccBrandModel = $ccBrandModel;
        $this->notification = Notification::createFromData($notificationModel);
        $paymentModel->load(
            $this->config->getSeller($this->notification->getOperation()->getAccountId()),
            $this->notification->getOperation()->getOrderId()
        );
        $this->payment = Payment::createFromData($paymentModel);
    }

    /**
     * Execute action of the controller.
     */
    public function execute()
    {
        $curl = new Curl();
        $paymentResource = new PaymentResource($this->config, $curl);
        $sellerResource = new SellerResource($this->config, $curl);
        $confirmation = new Confirmation($this->config, $paymentResource, $sellerResource);

        $confirmation->setUpdateCcAction(new UpdateCcInfo([$this, 'updateCcFn']));
        $confirmation->setMakePaymentAction(new MakePaymentOrRefund([$this, 'makePaymentFn']));
        $confirmation->setMakeRefundAction(new MakePaymentOrRefund([$this, 'makeRefundFn']));

        if ($confirmation->execute($this->payment, $this->notification)) {
            die('OK');
        } else {
            die('ERROR WITH CONFIRMATION');
        }
    }


    /**
     * Update credit card in database using information from Dotpay server about details of used CC.
     *
     * @param CreditCard|null $cc Credit card object with saved data of CC used for payment
     */
    public function updateCcFn(CreditCard $cc = null)
    {
        if($cc) {
            $dbCard = $this->loadCardFromDb($cc->getOrderId());

            if ($dbCard && $dbCard->getId() !== null && !$dbCard->isReadyToUse()) {
                $dbCard->setMask($cc->getMask());
                $dbCard->setCardId($cc->getCardId());
                $dbCcBrand = $this->ccBrandModel->load($cc->getBrand()->getName(), 'name');
                if ($dbCcBrand->getId() === null) {
                    $dbCcBrand->setName($cc->getBrand()->getName());
                    $dbCcBrand->setLogo($cc->getBrand()->getImage());
                    $dbCcBrand->save();
                }
                $dbCard->setBrand($dbCcBrand);
                $dbCard->save();
            }
        }
        else
        {   
            $control = $this->notification->getOperation()->getControl();
            $dbCard = $this->loadCardFromDb($control);

            if($dbCard && !$dbCard->getMask())
            {
                $dbCard->delete();
            }
        }
    }


    /**
     * Get all comments of operation (only payment completed). 
     * The information is used as another comment for the order in the event of duplicate payments
     *
     * @param CountCompletedTr| CompletedTr 
     * return eq: "2" for CountCompletedTr or "M9929-18619","M9999-33765" for CompletedTr
     */

    public function GetCommentsFromOrder($order,$get=null){

                $orderComments = $order->getAllStatusHistory();
                $orderComment1 = [];

                foreach ((array)$orderComments as $comment) {
                    if( $comment->getData('status') == 'dotpay_complete') {  //get all comments only for payments completed

                        $body1 = $comment->getData('comment');
                        preg_match_all("/\sM\d{4,5}\-\d{4,5}/", $body1, $matches);
                        $matches1 = str_replace(" ","",$matches[0]);
                        $body2 = array_unique($matches1);
                        $orderComment1[] = $body2;
                    }                  
                }

                $CountCompletedTr = count($orderComment1);

              if($CountCompletedTr > 0){
                    $CompletedTr = json_encode($orderComment1);
                    $CompletedTr = str_replace("[","",$CompletedTr);
                    $CompletedTr = str_replace("]","",$CompletedTr);
                    $CompletedTr = str_replace(",,",",",$CompletedTr);
                }else{
                    $CompletedTr = '';
                }

                if($get == 'count'){
                    $view = $CountCompletedTr;
                }else{
                    $view = $CompletedTr;
                }
                unset($orderComments);
               
                return $view;
    }



    /**
     * Make payment based on provided data of operation.
     *
     * @param Operation $operation Data with processed operation
     *
     * @return bool
     */

    public function makePaymentFn(Operation $operation)
    {
        $order = $this->orderModel->load($operation->getControl());

        if ($order === null) {
            $this->breakExecution('Order is not found');
        }

        $payment = $order->getPayment();
        if ($payment === null) {
            $this->breakExecution('Payment is not found');
        }

       $lastStatus = $order->getStatus();

       $payment->setTransactionId($operation->getNumber());
       $transaction = $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT, null, false);
       $transaction->setParentTxnId(null);
       
        if ($operation->getStatus() === Operation::STATUS_NEW) {  //payment created
            
            //$transaction->setIsClosed(0);
            $order->addStatusToHistory($this->configHelper->getStatusPending(), __('Payment has started. Payment number from Dotpay:').' '.$operation->getNumber().' ('.__('payment channel no:').' '.$this->notification->getChannelId().')', true);
        }



        // if last status: Pending (dotpay_pending)
        if($lastStatus === $this->configHelper->getStatusPending() ) 
        {

                if ($operation->getStatus() === Operation::STATUS_COMPLETE) {  //if payment complete
                    
                    $message = __('The payment is confirmed. Payment number from Dotpay:').' '.$operation->getNumber().' ('.__('payment channel no:').' '.$this->notification->getChannelId().')';
                    $transaction->setIsClosed(1);
                    $order->addStatusToHistory($this->configHelper->getStatusComplete(), $message, true);
                    if ($this->configHelper->getInvoiceOnConfirm() && $order->canInvoice()) {
                        $invoice = $order->prepareInvoice();
                        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                        $invoice->register();
                        $dbTransaction = $this->objectManager->create('Magento\Framework\DB\Transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());
                        $dbTransaction->save();
                    }
                } elseif ($operation->getStatus() === Operation::STATUS_REJECTED) {    //if payment rejected
                  
                    $message = __('The payment is rejected. Payment number from Dotpay:').' '.$operation->getNumber().' ('.__('payment channel no:').' '.$this->notification->getChannelId().')';
                    $transaction->setIsClosed(1);
                    $order->addStatusToHistory($this->configHelper->getStatusCanceled(), $message, true);
                }
                $transaction->setAdditionalInformation('info', serialize($operation));
                $transaction->save();
                $payment->save();
                $order->save();

                return true;

       }

        else {

            // if last status: completed and get status: completed
            if($lastStatus === $this->configHelper->getStatusComplete() && $operation->getStatus() === Operation::STATUS_COMPLETE) 
            {
                $order->addStatusToHistory($this->configHelper->getStatusComplete(), __('The payment is confirmed. Payment number from Dotpay:').' '.$operation->getNumber().' ('.__('payment channel no:').' '.$this->notification->getChannelId().')', false);
                $order->addStatusToHistory($this->configHelper->getStatusDuplicated(), __('The payment has been confirmed twice - check for possible duplicated payment').' ('.$operation->getNumber().') - '. __('in total:').' '.(substr_count($this->GetCommentsFromOrder($order),'-') +1).' '. __('separate payments ! Payments made before:').' '.$this->GetCommentsFromOrder($order), false);
                $order->save();
            }

             // if last status: completed and get status: rejected
            elseif($lastStatus === $this->configHelper->getStatusComplete() && $operation->getStatus() === Operation::STATUS_REJECTED) 
            {
               // $order->addStatusToHistory($this->configHelper->getStatusComplete(), __('Another attempt to pay for an order already paid for - this time unsuccessful. The status of this order has not been changed.').' ('.$operation->getNumber().' /'.__('payment channel no:').' '.$this->notification->getChannelId().'/)', false);
                
                $order->addCommentToStatusHistory(__('Another attempt to pay for an order already paid for - this time unsuccessful. The status of this order has not been changed.').' ('.$operation->getNumber().' ,'.__('payment channel no:').' '.$this->notification->getChannelId().')', $status = false, $isVisibleOnFront = false);

                $order->save();

            }
          
            // if last status: duplicated and get status: completed
            elseif($lastStatus === $this->configHelper->getStatusDuplicated() && $operation->getStatus() === Operation::STATUS_COMPLETE) {
                $order->addStatusToHistory($this->configHelper->getStatusComplete(), __('The payment is confirmed. Payment number from Dotpay:').' '.$operation->getNumber().' ('.__('payment channel no:').' '.$this->notification->getChannelId().')', false);
                $order->addStatusToHistory($this->configHelper->getStatusDuplicated(), __('The payment has been confirmed more then once - check for possible duplicated payment').' ('.$operation->getNumber().') - '. __('in total:').' '.(substr_count($this->GetCommentsFromOrder($order),'-') +1).' '. __('separate payments ! Payments made before:').' '.$this->GetCommentsFromOrder($order), false);
                $order->save();
            }
           

            // if last status: canceled and get status: completed
            elseif ($lastStatus === $this->configHelper->getStatusCanceled() && $operation->getStatus() === Operation::STATUS_COMPLETE) 
            {
                //payment complete
                $message = __('The payment is confirmed. Payment number from Dotpay:').' '.$operation->getNumber().' ('.__('payment channel no:').' '.$this->notification->getChannelId().')';
                $transaction->setIsClosed(1);
                $order->addStatusToHistory($this->configHelper->getStatusComplete(), $message, true);
                if ($this->configHelper->getInvoiceOnConfirm() && $order->canInvoice()) {
                    $invoice = $order->prepareInvoice();
                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                    $invoice->register();
                    $dbTransaction = $this->objectManager->create('Magento\Framework\DB\Transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $dbTransaction->save();
                }
            }

            // if last status: canceled and get status: rejected
            elseif($lastStatus === $this->configHelper->getStatusCanceled() && $operation->getStatus() === Operation::STATUS_REJECTED) 
            {
                $order->addStatusToHistory($this->configHelper->getStatusCanceled(), __('The payment has been confirmed twice - another failed payment attempt.').' ('.$operation->getNumber().' /'.__('payment channel no:').' '.$this->notification->getChannelId().'/)', false);
                $order->save();
            }

            elseif($operation->getStatus() === Operation::STATUS_NEW) 
            {
                
                $order->addStatusToHistory($this->configHelper->getStatusPending(), __('Another payment attempt initiated. Payment number from Dotpay:').' ('.$operation->getNumber().' /'.__('payment channel no:').' '.$this->notification->getChannelId().'/)', false);
               // $order->save();
            }

            else {
                return true;
            }
            return true;
        }

    }
    

    /**
     * Make refund based on provided data of operation.
     *
     * @param Operation $operation Data with processed operation
     *
     * @return bool
     */
    public function makeRefundFn(Operation $operation)
    {
        
        $order = $this->orderModel->load($operation->getControl());
        
        if ($order === null) {
            $this->breakExecution('Order is not found');
        }

        $lastStatus = $order->getStatus();
        if (in_array(
                $lastStatus,
                [
                    $this->configHelper->getStatusRefunded(),
                    $this->configHelper->getStatusRefundFailed()
                ]
            ) === true
        ) {
            return true;
        }

        $payment = $order->getPayment();
        if ($payment === null) {
            $this->breakExecution('Payment is not found');
        }

        if ($operation->getStatus() === Operation::STATUS_NEW) {
            $order->addStatusToHistory($this->configHelper->getStatusRefundNew(), __('The refund is created. Refund number from Dotpay:').' '.$operation->getNumber(), true);
        }
        $payment->setTransactionId($operation->getNumber());
        $transaction = $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND, null, false);

        $transaction->setTxnId($operation->getNumber());

        if ($operation->getStatus() === Operation::STATUS_COMPLETE) {
            $message = __('The refund is confirmed. Refund number from Dotpay:').' '.$operation->getNumber();
            $order->addStatusToHistory($this->configHelper->getStatusRefunded(), $message, true);

        } elseif ($operation->getStatus() === Operation::STATUS_REJECTED) {
            //payment rejected
            $message = __('The refund is rejected. Refund number from Dotpay:').' '.$operation->getNumber();
            $order->addStatusToHistory($this->configHelper->getStatusRefundFailed(), $message, true);
        }
        $transaction->setAdditionalInformation('info', serialize($operation));
        $transaction->save();
        $payment->save();
        $order->save();

        return true;

    }

    /**
     * Create invoice in shop for the given order.
     *
     * @param \Magento\Sales\Model\Order $order Magento order object
     */
    private function createInvoice(\Magento\Sales\Model\Order $order)
    {
        try {
            if ($order->canInvoice()) {
                $invoice = $order->prepareInvoice();
                if ($invoice->getTotalQty()) {
                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                    $invoice->register();
                    $transactionSave = $this->objectManager->create('Magento\Framework\DB\Transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $transactionSave->save();
                }
            }
        } catch (\Exception $e) {
            error_log(__METHOD__.' '.$e->getMessage());
        }
    }

    /**
     * Return credit card from database associated with concrete order. If the card doesn't exist null value is returned.
     *
     * @param int $orderId Order id
     *
     * @return \Dotpay\Payment\Model\CreditCard/null
     */
    private function loadCardFromDb($orderId)
    {
        $dbOrder = $this->orderModel->load($orderId);
        if ($dbOrder !== null && $dbOrder->getCustomerId() !== null) {
            $dbCustomer = $this->customerModel->load($dbOrder->getCustomerId());
            if ($dbCustomer !== null) {
                $dbCard = $this->ccModel->getCollection()
                                        ->addFilter(CreditCardInterface::CUSTOMER_ID, $dbCustomer->getId())
                                        ->addFilter(CreditCardInterface::ORDER_ID, $dbOrder->getId())
                                        ->getFirstItem();

                return $dbCard;
            }
        }

        return null;
    }

    /**
     * Break the program and display the given message.
     *
     * @param string $message Messsage to display
     */
    private function breakExecution($message)
    {
        die(__($message));
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

}
