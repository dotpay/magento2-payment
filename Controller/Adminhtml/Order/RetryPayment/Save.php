<?php

namespace Dotpay\Payment\Controller\Adminhtml\Order\RetryPayment;

use Dotpay\Model\PaymentLink;
use Dotpay\Model\Seller;
use Dotpay\Payment\Controller\Adminhtml\Order\RetryPayment;
use Dotpay\Payment\Model\OrderRetryPayment;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Model\Configuration;
use Dotpay\Loader\Loader;
use Dotpay\Loader\Parser;
use Dotpay\Bootstrap;
use Dotpay\Tool\Curl;

class Save extends RetryPayment {

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Dotpay_Payment::order_retryPayment';



    /**
     * CURRENT: calculate CHK for the PID. - only for 'api_version = next'
     *
     * @param $token                  with transaction parameters (pid)
     * @param string $pin             Seller pin to sign the control sum
     *
     * @return string
     */
    
    
    ## function: counts the checksum from the defined array of all parameters

    public function generateCHKlink($token,$DotpayPin)
    {
            $PIDArray = array('api_version' => 'next', 'pid' => $token);
            ksort($PIDArray);
            $paramList = implode(';', array_keys($PIDArray));
            $PIDArray['paramsList'] = $paramList;
            ksort($PIDArray);
            $json = json_encode($PIDArray, JSON_UNESCAPED_SLASHES);
            
         return hash_hmac('sha256', $json, $DotpayPin, false);
       
    }

    /**
     * @return void
     */
    public function execute()
    {

        

        try {
            $curl = new Curl();
            $order = $this->_initOrder($this->getRequest()->getParam('order_id'));
            $sellerResource = new SellerResource($this->config, $curl);
            $paymentLink = PaymentLink::createFromData(new \Dotpay\Payment\Helper\Data\PaymentLink($order));
            $paymentLink->setUrl($this->urlHelper->getBackUrl());
            $paymentLink->setUrlc($this->urlHelper->getNotificationUrl());
            $locale = strstr($this->_localeResolver->getLocale(), "_", true);
            $paymentLink->setLanguage($locale);
            $seller = Seller::createFromConfiguration($this->config);
            $link = $sellerResource->getNewPaymentLink($seller, $paymentLink);
            $chk = $this->generateCHKlink($link['token'],$this->config->getPin());
            
            $url = $link['payment_url'] . "&api_version=next&chk=" . $chk;
            $this->_objectManager->create(OrderRetryPayment::class)
                ->setOrderId($order->getEntityId())
                ->setUrl($url)
                ->setToken($link['token'])
                ->save();
            $this->messageManager->addSuccessMessage(__('Payment link generated successfully'));
        } catch(\Exception $e) {
            $this->messageManager->addErrorMessage(__('Payment link could not be created: ' . $e->getMessage()));
        }

        $path = 'sales/order/view';
        $pathParams = ['order_id' => $order->getEntityId()];
        return $this->resultRedirectFactory->create()->setPath($path, $pathParams);
    }

    protected function _isAllowed()
    {
        return true;
    }
}
