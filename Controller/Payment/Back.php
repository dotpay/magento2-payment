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

/**
 * Controller of displaying the page after the payment.
 */
class Back extends Dotpay
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
            if ($orderId == null) {
                throw new \RuntimeException(__('The payment has not been found. Please contact to the seller.'));
            }
            $pageData['order_id'] = $orderId;
            $pageData['order_id_show'] = $this->checkoutSession->getLastRealOrder()->getRealOrderId()."/".$orderId;
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
}
