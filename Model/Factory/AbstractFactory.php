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

namespace Dotpay\Payment\Model\Factory;

use Dotpay\Model\CustomerAdditionalData;
use Dotpay\Model\Seller;
use Dotpay\Model\Customer;
use Dotpay\Model\Payment;
use Dotpay\Model\Transaction;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Tool\Curl;
use Dotpay\Tool\AmountFormatter;

/**
 * Abstract factory which produces necessary SDK objects including channel object.
 */
abstract class AbstractFactory
{
    /**
     * @var \Magento\Customer\Model\Session Customer session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session Checkout session
     */
    protected $checkoutSession;

    /**
     * @var \Dotpay\Payment\Helper\Url Helper of supporting management of locale used by customer
     */
    protected $localeHeper;

    /**
     * @var \Dotpay\Payment\Helper\Url Helper for generating urls used in the Dotpay payment plugin
     */
    protected $urlHelper;

    /**
     * @var \Dotpay\Payment\Model\Method\AbstractAdapter Adapter of concrete payment method which extends the abstract one
     */
    protected $paymentAdapter;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory Order collection factory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface Scope config
     */
    protected $scopeConfig;

    /**
     * Initialize the factory.
     *
     * @param \Magento\Customer\Model\Session                            $customerSession
     * @param \Magento\Checkout\Model\Session                            $checkoutSession
     * @param \Dotpay\Payment\Helper\Locale                              $localeHelper
     * @param \Dotpay\Payment\Helper\Url                                 $urlHelper
     * @param \Dotpay\Payment\Model\Method\AbstractAdapter               $paymentAdapter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface         $scopeConfig
     */
    public function __construct(\Magento\Customer\Model\Session $customerSession,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Dotpay\Payment\Helper\Locale $localeHelper,
                                \Dotpay\Payment\Helper\Url $urlHelper,
                                \Dotpay\Payment\Model\Method\AbstractAdapter $paymentAdapter,
                                \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->localeHeper = $localeHelper;
        $this->urlHelper = $urlHelper;
        $this->paymentAdapter = $paymentAdapter;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return id of the current customer.
     *
     * @return int
     */
    protected function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * Return first name of customer.
     *
     * @return string
     */
    protected function getFirstname()
    {
        $formatter = new \Dotpay\Tool\StringFormatter\Name();
        return $formatter->format($this->checkoutSession->getLastRealOrder()->getBillingAddress()->getFirstname());
    }

    /**
     * Return last name of customer.
     *
     * @return string
     */
    protected function getLastname()
    {
        $formatter = new \Dotpay\Tool\StringFormatter\Name();
        return $formatter->format($this->checkoutSession->getLastRealOrder()->getBillingAddress()->getLastname());
    }

    /**
     * Return email of customer.
     *
     * @return string
     */
    protected function getEmail()
    {
        return $this->checkoutSession->getLastRealOrder()->getBillingAddress()->getEmail();
    }

    /**
     * Return street from address of customer.
     *
     * @return string
     */
    protected function getStreet()
    {
        $streetOriginal = $this->checkoutSession->getLastRealOrder()->getBillingAddress()->getStreet();
        $streetData = is_array($streetOriginal) ? implode(' ', $streetOriginal) : $streetOriginal;

        return $streetData;
    }

    /**
     * Return post code from address of customer.
     *
     * @return string
     */
    protected function getPostcode()
    {
        return $this->checkoutSession->getLastRealOrder()->getBillingAddress()->getPostcode();
    }

    /**
     * Return city from address of customer.
     *
     * @return string
     */
    protected function getCity()
    {
        $formatter = new \Dotpay\Tool\StringFormatter\Name();
        return $formatter->format($this->checkoutSession->getLastRealOrder()->getBillingAddress()->getCity());
    }

    /**
     * Return country from address of customer.
     *
     * @return string
     */
    protected function getCountry()
    {
        return strtoupper($this->checkoutSession->getLastRealOrder()->getBillingAddress()->getCountryId());
    }

    /**
     * Return street from shipping address of customer.
     *
     * @return string
     */
    protected function getShippingStreet()
    {
        $streetOriginal = $this->checkoutSession->getLastRealOrder()->getShippingAddress()->getStreet();
        $streetData = is_array($streetOriginal) ? implode(' ', $streetOriginal) : $streetOriginal;

        return $streetData;
    }

    /**
     * Return post code from address of customer.
     *
     * @return string
     */
    protected function getShippingPostcode()
    {
        return $this->checkoutSession->getLastRealOrder()->getShippingAddress()->getPostcode();
    }

    /**
     * Return city from address of customer.
     *
     * @return string
     */
    protected function getShippingCity()
    {
        $formatter = new \Dotpay\Tool\StringFormatter\Name();
        return $formatter->format($this->checkoutSession->getLastRealOrder()->getShippingAddress()->getCity());
    }

    /**
     * Return country from address of customer.
     *
     * @return string
     */
    protected function getShippingCountry()
    {
        return strtoupper($this->checkoutSession->getLastRealOrder()->getShippingAddress()->getCountryId());
    }

    /**
     * Return date of user's registration
     *
     * @return \DateTime
     */
    protected function getRegisteredSince()
    {
        $customer = $this->customerSession->getCustomer();
        if($customer->getId())
        {
            return new \DateTime("@".$customer->getCreatedAtTimestamp());
        }
        return null;
    }

    /**
     * Return number of orders
     *
     * @return int
     */
    protected function getOrderCount()
    {
        $customer = $this->customerSession->getCustomer();
        if($customer->getId())
        {
            $orders = $this->orderCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customer->getId()
            )->setOrder(
                'created_at',
                'desc'
            );
            return count($orders);
        }
        return 0;
    }

    /**
     * Return delivary type
     *
     * @return int
     */
    protected function getDeliveryType()
    {
        $mapping = json_decode(
            $this->scopeConfig->getValue(
                'payment/dotpay_main/shipping_mapping',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            true
        );

        $code = $this->checkoutSession->getLastRealOrder()->getShippingMethod(true)->getData('method');

        return isset($mapping[$code]) ? $mapping[$code] : null;

    }

    /**
     * Return phone number of customer.
     *
     * @return string
     */
    protected function getPhone()
    {
        return $this->checkoutSession->getLastRealOrder()->getBillingAddress()->getTelephone();
    }

    /**
     * Return language of customer.
     *
     * @return string
     */
    protected function getLanguage()
    {
        return $this->localeHeper->getLanguage();
    }

    /**
     * Return id of last placed order.
     *
     * @return int
     */
    protected function getOrderId()
    {
        return $this->checkoutSession->getLastRealOrder()->getEntityId();
    }

    /**
     * Return amount of money of last placed order.
     *
     * @return float
     */
    protected function getAmount()
    {
        return AmountFormatter::format(
            $this->checkoutSession->getLastRealOrder()->getGrandTotal(),
            $this->getCurrency()
        );
    }

    /**
     * Return currency of last placed order.
     *
     * @return string
     */
    protected function getCurrency()
    {
        return $this->checkoutSession->getLastRealOrder()->getOrderCurrencyCode();
    }

    /**
     * Return description of last placed order.
     *
     * @return string
     */
    protected function getDescription()
    {
        $AllOrderIds = $this->checkoutSession->getLastRealOrder()->getRealOrderId().'/'.$this->checkoutSession->getLastRealOrder()->getEntityId();
        return __('Order ID: %1', $AllOrderIds);
        // return __('Order ID: %1', $this->checkoutSession->getLastRealOrder()->getRealOrderId());
    }

    /**
     * Return SDK seller object.
     *
     * @return Seller
     */
    public function getSeller()
    {
        return Seller::createFromConfiguration($this->paymentAdapter->getConfiguration());
    }

    /**
     * Return SDK customer object.
     *
     * @return Customer
     */
    public function getCustomer()
    {
        $customer = new Customer($this->getEmail(),
                                 $this->getFirstname(),
                                 $this->getLastname());
        $customer->setId($this->getCustomerId())
                 ->setStreet($this->getStreet())
                 ->setPostCode($this->getPostcode())
                 ->setCity($this->getCity())
                 ->setCountry($this->getCountry())
                 ->setPhone($this->getPhone())
                 ->setLanguage($this->getLanguage());


        return  $customer;
    }

    /**
     * Return SDK payment object.
     *
     * @return Payment
     */
    public function getPayment()
    {
        return new Payment($this->getSeller(),
                               $this->getAmount(),
                               $this->getCurrency(),
                               $this->getDescription(),
                               $this->getOrderId());
    }

    /**
     * Return SDK customer additional data object
     *
     * @return CustomerAdditionalData
     */
    public function getCustomerAdditionalData()
    {
        $customerAdditionaData = new CustomerAdditionalData(
            $this->getEmail(),
            $this->getFirstname(),
            $this->getLastname()
        );

        $customerAdditionaData
            ->setStreet($this->getShippingStreet())
            ->setPostCode($this->getShippingPostcode())
            ->setCity($this->getShippingCity())
            ->setCountry($this->getShippingCountry())
            ->setPhone($this->getPhone())
            ->setRegisteredSince($this->getRegisteredSince())
            ->setOrderCount($this->getOrderCount())
            ->setDeliveryType($this->getDeliveryType());

        return $customerAdditionaData;
    }

    /**
     * Return SDK transaction object.
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        $transaction = new Transaction($this->getCustomer(), $this->getPayment());
        $transaction->setBackUrl(
                        $this->urlHelper->getBackUrl($this->paymentAdapter->getBackUrl())
                    )->setConfirmUrl(
                        $this->urlHelper->getNotificationUrl($this->paymentAdapter->getConfirmUrl())
                    )->setCustomerAdditionalData($this->getCustomerAdditionalData());

        return $transaction;
    }

    /**
     * Return SDK payment resource manager.
     *
     * @return PaymentResource
     */
    public function getPaymentResource()
    {
        return new PaymentResource($this->paymentAdapter->getConfiguration(), new Curl());
    }

    /**
     * Return SDK seller resource manager.
     *
     * @return SellerResource
     */
    public function getSellerResource()
    {
        return new SellerResource($this->paymentAdapter->getConfiguration(), new Curl());
    }

    /**
     * Return proper SDK channel object filled by all necessary data.
     *
     * @param array $additionalInformation Additional information about payment
     *
     * @return Channel
     */
    abstract public function getChannel($additionalInformation);
}
