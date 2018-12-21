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

namespace Dotpay\Payment\Helper\Data;

use Dotpay\Provider\CustomerProviderInterface;

/**
 * Provider of customer data.
 */
class Customer implements CustomerProviderInterface
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Sales\Model\Order $order Magento order object
     */
    public function __construct(
        \Magento\Sales\Model\Order $order
    ) {
        $this->order = $order;
        $this->customer = $order->getCustomer();
    }

    /**
     * Return an id of the customer in a shop.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->customer->getId();
    }

    /**
     * Return an email address of the payer.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->order->getCustomerEmail();
    }

    /**
     * Return a first name of the payer.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->order->getBillingAddress()->getFirstname();
    }

    /**
     * Return a last name of the payer.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->order->getBillingAddress()->getLastname();
    }

    /**
     * Return a street name of the customer.
     *
     * @return string
     */
    public function getStreet()
    {
        $streetOriginal = $this->order->getBillingAddress()->getStreet();
        $streetData = is_array($streetOriginal) ? implode(' ', $streetOriginal) : $streetOriginal;

        return $streetData;
    }

    /**
     * Return a building number of the customer.
     *
     * @return string/null
     */
    public function getBuildingNumber()
    {
        return null;
    }

    /**
     * Return a post code of the customer.
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->order->getBillingAddress()->getPostcode();
    }

    /**
     * Return a city of the customer.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->order->getBillingAddress()->getCity();
    }

    /**
     * Return a country of the customer.
     *
     * @return string
     */
    public function getCountry()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $country = $objectManager->create('\Magento\Directory\Model\Country')->load($this->order->getBillingAddress()->getCountryId())->getData('iso3_code');
        return $country;
    }

    /**
     * Return a phone number of the customer.
     *
     * @return string
     */
    public function getPhone()
    {
        //return "";
        return $this->order->getShippingAddress()->getTelephone();
    }

    /**
     * Return a language used by the customer.
     *
     * @return string
     */
    public function getLanguage()
    {
        return 'pl';
    }

    /**
     * Check if address details are available
     *
     * @return boolean
     */
    public function isAddressAvailable()
    {
        //return false;
        return $this->getStreet() !== ''
        && $this->getPostCode() !== ''
        && $this->getCity() !== ''
        && $this->getCountry() !== ''
        && $this->getPhone() !== '';
    }
}
