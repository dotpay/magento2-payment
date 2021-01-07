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

use Dotpay\Processor\Back as BackProcessor;
use Dotpay\Payment\Controller\Dotpay;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

/**
 * Controller of displaying the page after the payment.
 */
class Back extends Dotpay implements CsrfAwareActionInterface
{
    /**
     * Execute action of the controller.
     */
 
 
     
     public function execute()
    {
        $pageData = [];
        $backProcessor = new BackProcessor($this->_request->getParam('error_code'));



        try {
            $backProcessor->execute();
            
            $orderId = $this->checkoutSession->getLastRealOrder()->getId();
            $RealOrderId = $this->checkoutSession->getLastRealOrder()->getRealOrderId();

            // for payment link
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  
            $request = $objectManager->get('Magento\Framework\App\Request\Http');  
            $get_status_back = $request->getParam('status');
            $get_order_id_back_tmp = $request->getParam('DpOrderId');
            
             $get_order_id_back1 = explode(':',$get_order_id_back_tmp);
   
            if (isset($get_order_id_back1[1])) {
                $get_order_id_back0 = $get_order_id_back1[1];
            }else{
                $get_order_id_back0 = null;
            }
           //remove unnecessary stuff masking real data
            $get_order_id_back = substr($get_order_id_back0, 3, 8).substr($get_order_id_back0, 17, 26).'==';


            // get id order from back link (schema: '#160#000000160#1608539078')    
            $oderid_decode1 = base64_decode($get_order_id_back);

            $oderid_decode= explode('#',$oderid_decode1);
   
            if (isset($oderid_decode[1])) {
                $oderid_back = (int)$oderid_decode[1];
            }else{
                $oderid_back = null;
            }
            
            if (isset($oderid_decode[2])) {
                $oderid_back_nr = (string)$oderid_decode[2];
            }else{
                $oderid_back_nr = null;
            }
            

            if ($orderId == null && $oderid_back == null ) 
            {
                    $pageData['order_id_back'] = null;
                    $pageData['order_id_back_nr'] = null;
                    $pageData['order_id'] = null;
                    $orderIdN = null;

                throw new \RuntimeException(__('The payment has not been found. Please contact to the seller.'));

            }elseif ($orderId == null && $oderid_back != null && $get_status_back != null) {

                    $pageData['order_id_back'] = $oderid_back;
                    $pageData['order_id_back_nr'] = $oderid_back_nr;
                    $pageData['order_id'] = $oderid_back;
                    $orderIdN = $oderid_back;


            }else{
                    $pageData['order_id'] = $orderId;
                    $pageData['order_id_back'] = null;
                    $pageData['order_id_back_nr'] = null;
                    $orderIdN = $orderId;
            }
            

            if($RealOrderId != null){
                $pageData['order_id_show'] = $RealOrderId."/".$orderIdN;
            }else{
                $pageData['order_id_show'] = $orderIdN."/".$oderid_back_nr;
            }
            
            $pageData['target_url'] = $this->urlHelper->getStatusUrl();

            if ($this->customerSession->isLoggedIn()) {
                $pageData['redirect_url'] = $this->urlHelper->getUrl('customer/account');
            } else {
                $pageData['redirect_url'] = $this->urlHelper->getUrl('checkout/onepage/success');
            }
        } catch (\RuntimeException $e) {
            $pageData['error_message'] = $e->getMessage();
        }
        $this->coreRegistry->register('data', $pageData);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
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
