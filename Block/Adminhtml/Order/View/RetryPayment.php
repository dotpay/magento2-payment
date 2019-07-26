<?php

namespace Dotpay\Payment\Block\Adminhtml\Order\View;

use Dotpay\Payment\Api\Data\OrderInterface;
use Dotpay\Payment\Model\OrderRetryPayment;
use Dotpay\Payment\Model\OrderRetryPaymentFactory;

class RetryPayment extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var \Dotpay\Payment\Helper\Data\Configuration Provider of Dotpay module configuration data
     */
    private $configHelper;

    /**
     * @var \Dotpay\Payment\Model\OrderRetryPaymentFactory
     */
    protected $_orderRetryPaymentFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param OrderRetryPaymentFactory $newsFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Dotpay\Payment\Helper\Data\Configuration $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        OrderRetryPaymentFactory $orderRetryPaymentFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->_orderRetryPaymentFactory = $orderRetryPaymentFactory;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    public function getRetryPayments()
    {
        $collection = $this->_orderRetryPaymentFactory->create()->getCollection()->addFieldToFilter('order_id', $this->getOrder()->getEntityId());
        return $collection;
    }

    public function getSaveUrl()
    {
        return $this->getUrl("dotpay/order_retryPayment/save", ["order_id" => $this->getOrder()->getEntityId()]);
    }

    public function getDeleteUrl($id)
    {
        return $this->getUrl("dotpay/order_retryPayment/delete", ["id" => $id]);
    }

    public function canRetry()
    {
        $status = $this->getOrder()->getStatus();
        if(
            $status === $this->configHelper->getStatusPending()
            && count($this->getRetryPayments()) < 1
        )
            return true;

        return false;
    }

    public function canDelete()
    {
        $status = $this->getOrder()->getStatus();
        if(
            $status === $this->configHelper->getStatusPending()
        )
            return true;

        return false;
    }

    public function canShowBlock()
    {
        $status = $this->getOrder()->getStatus();
        if(in_array($status, [$this->configHelper->getStatusPending(), $this->configHelper->getStatusCanceled(), $this->configHelper->getStatusComplete(), $this->configHelper->getStatusDuplicated()]))
            return true;

        $statuses = $this->getOrder()->getAllStatusHistory();
        foreach($statuses as $status)
        {
            if(in_array($status->getStatus(), [$this->configHelper->getStatusPending(), $this->configHelper->getStatusCanceled(), $this->configHelper->getStatusComplete(), $this->configHelper->getStatusDuplicated()]))
                return true;
        }
        return false;
    }
}