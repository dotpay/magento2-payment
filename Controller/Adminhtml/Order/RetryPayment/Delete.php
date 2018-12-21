<?php

namespace Dotpay\Payment\Controller\Adminhtml\Order\RetryPayment;

use Dotpay\Model\Seller;
use Dotpay\Payment\Controller\Adminhtml\Order\RetryPayment;
use Dotpay\Payment\Model\OrderRetryPayment;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Tool\Curl;

class Delete extends RetryPayment {

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Dotpay_Payment::order_retryPayment';

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $orderId = 0;
        try {
            $curl = new Curl();

            $sellerResource = new SellerResource($this->config, $curl);

            $links = $this->_objectManager->get(OrderRetryPayment::class)->getCollection()->addFieldToFilter('entity_id', array('eq' => $id))->load();
            if($links->getSize() == 1)
            {
                $link = $links->fetchItem();
                $seller = Seller::createFromConfiguration($this->config);
                $ret = $sellerResource->deletePaymentLink($seller, $link->getToken());
                $orderId = $link->getOrderId();
                $link->delete();
                $this->messageManager->addSuccessMessage(__('Payment link deleted successfully'));
            }

        } catch(\Exception $e) {
            $this->messageManager->addErrorMessage(__('Payment link could not be deleted'));
        }
        $path = 'sales/order/view';
        $pathParams = ['order_id' => $orderId];
        return $this->resultRedirectFactory->create()->setPath($path, $pathParams);

    }
}