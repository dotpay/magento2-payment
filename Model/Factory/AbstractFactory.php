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
     * Initialize the factory.
     *
     * @param \Magento\Customer\Model\Session              $customerSession
     * @param \Magento\Checkout\Model\Session              $checkoutSession
     * @param \Dotpay\Payment\Helper\Locale                $localeHelper
     * @param \Dotpay\Payment\Helper\Url                   $urlHelper
     * @param \Dotpay\Payment\Model\Method\AbstractAdapter $paymentAdapter
     */
    public function __construct(\Magento\Customer\Model\Session $customerSession,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Dotpay\Payment\Helper\Locale $localeHelper,
                                \Dotpay\Payment\Helper\Url $urlHelper,
                                \Dotpay\Payment\Model\Method\AbstractAdapter $paymentAdapter)
    {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->localeHeper = $localeHelper;
        $this->urlHelper = $urlHelper;
        $this->paymentAdapter = $paymentAdapter;
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
        return $this->checkoutSession->getLastRealOrder()->getBillingAddress()->getFirstname();
    }

    /**
     * Return last name of customer.
     *
     * @return string
     */
    protected function getLastname()
    {
        return $this->checkoutSession->getLastRealOrder()->getBillingAddress()->getLastname();
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
        return $this->checkoutSession->getLastRealOrder()->getBillingAddress()->getCity();
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
                    );

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
