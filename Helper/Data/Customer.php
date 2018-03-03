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
     * @var \Dotpay\Payment\Helper\Locale Locale helper providing data about locality
     */
    protected $localeHeper;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Dotpay\Payment\Helper\Locale   $localeHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Dotpay\Payment\Helper\Locale $localeHelper
    ) {
        $this->customer = $customerSession->getCustomer();
        $this->localeHeper = $localeHelper;
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
        $this->customer->getEmail();
    }

    /**
     * Return a first name of the payer.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->customer->getFirstname();
    }

    /**
     * Return a last name of the payer.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->customer->getLastname();
    }

    /**
     * Return a street name of the customer.
     *
     * @return string
     */
    public function getStreet()
    {
        $streetOriginal = $this->customer->getDefaultBillingAddress()->getStreet();
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
        return $this->customer->getDefaultBillingAddress()->getPostcode();
    }

    /**
     * Return a city of the customer.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->customer->getDefaultBillingAddress()->getCity();
    }

    /**
     * Return a country of the customer.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->customer->getDefaultBillingAddress()->getCountryId();
    }

    /**
     * Return a phone number of the customer.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->customer->getDefaultBillingAddress()->getTelephone();
    }

    /**
     * Return a language used by the customer.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->localeHeper->getLanguage();
    }

    /**
     * Check if address details are available
     *
     * @return boolean
     */
    public function isAddressAvailable()
    {
        return $this->getStreet() !== ''
        && $this->postCode !== ''
        && $this->city !== ''
        && $this->country !== ''
        && $this->phone !== '';
    }
}
