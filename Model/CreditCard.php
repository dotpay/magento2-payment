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

namespace Dotpay\Payment\Model;

use Magento\Framework\Model\AbstractModel;
use Dotpay\Payment\Api\Data\CreditCardInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Dotpay\Model\CreditCard as SdkCreditCard;

/**
 * Model of single credit card saved in database and used for One Click method.
 */
class CreditCard extends AbstractModel implements CreditCardInterface, IdentityInterface
{
    /**
     * Identifier of cache tag.
     */
    const CACHE_TAG = 'dotpay_credit_card';

    /**
     * @var \Magento\Sales\Model\Order Magento order model
     */
    private $order;

    /**
     * @var \Magento\Customer\Model\Customer Magento customer model
     */
    private $customer;

    /**
     * @var \Dotpay\Payment\Model\CardBrand Card brand model
     */
    private $cardBrand;

    /**
     * Initialize the model.
     *
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Sales\Model\Order                              $order
     * @param \Magento\Customer\Model\Customer                        $customer
     * @param \Dotpay\Payment\Model\CardBrand                         $cardBrand
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,
        \Magento\Customer\Model\Customer $customer,
        \Dotpay\Payment\Model\CardBrand $cardBrand,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->cardBrand = $cardBrand;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Pseudoconstructor for binding model with its resource.
     */
    protected function _construct()
    {
        $this->_init('Dotpay\Payment\Model\Resource\CreditCard');
    }

    /**
     * Return list of identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Return id of credit card.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::CARD_ID);
    }

    /**
     * Set id of credit card.
     *
     * @param int $id id of credit card
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::CARD_ID, $id);
    }

    /**
     * Return object of the first order paid using the credit card.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $orderId = $this->getData(self::ORDER_ID);

        return $this->order->load($orderId);
    }

    /**
     * Set object of the first order paid using the credit card.
     *
     * @param \Magento\Sales\Model\Order $order Order object
     *
     * @return $this
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {
        return $this->setData(self::ORDER_ID, $order->getId());
    }

    /**
     * Return object of customer who is owner of the credit card.
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        $customerId = $this->getData(self::CUSTOMER_ID);

        return $this->customer->load($customerId);
    }

    /**
     * Set object of customer who is owner of the credit card.
     *
     * @param \Magento\Customer\Model\Customer $customer Cusomer object
     *
     * @return $this
     */
    public function setCustomer(\Magento\Customer\Model\Customer $customer)
    {
        return $this->setData(self::CUSTOMER_ID, $customer->getId());
    }

    /**
     * Return object of brand of the credit card.
     *
     * @return \Dotpay\Payment\Model\CardBrand
     */
    public function getBrand()
    {
        $brandId = $this->getData(self::BRAND_ID);
        if ($brandId != null) {
            return $this->cardBrand->load($brandId);
        } else {
            return null;
        }
    }

    /**
     * Set object of brand of the credit card.
     *
     * @param \Dotpay\Payment\Model\CardBrand $brand Card brand object
     *
     * @return $this
     */
    public function setBrand(\Dotpay\Payment\Model\CardBrand $brand)
    {
        return $this->setData(self::BRAND_ID, $brand->getId());
    }

    /**
     * Return masked number.
     *
     * @return string
     */
    public function getMask()
    {
        return $this->getData(self::MASK);
    }

    /**
     * Set masked number.
     *
     * @param string $mask Masked number
     *
     * @return $this
     */
    public function setMask($mask)
    {
        return $this->setData(self::MASK, $mask);
    }

    /**
     * Return customer hash of credit card.
     *
     * @return string
     */
    public function getCustomerHash()
    {
        return $this->getData(self::CUSTOMER_HASH);
    }

    /**
     * Set customer hash of credit card.
     *
     * @param string $hash Customer hash
     *
     * @return $this
     */
    public function setCustomerHash($hash)
    {
        return $this->setData(self::CUSTOMER_HASH, $hash);
    }

    /**
     * Return card id given from Dotpay.
     *
     * @return string
     */
    public function getCardId()
    {
        return $this->getData(self::CC_ID);
    }

    /**
     * Set card id given from Dotpay.
     *
     * @param type $ccId Card id given from Dotpay
     *
     * @return $this
     */
    public function setCardId($ccId)
    {
        return $this->setData(self::CC_ID, $ccId);
    }

    /**
     * Return date of credit card registration.
     *
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->getData(self::REGISTER_DATE);
    }

    /**
     * Set date of credit card registration.
     *
     * @param \DateTime $registerDate Date of credit card registration
     *
     * @return $this
     */
    public function setRegisterDate(\DateTime $registerDate)
    {
        return $this->setData(self::REGISTER_DATE, $registerDate->format('Y-m-d H:i:s'));
    }

    /**
     * Return an information if the saved card has complete data and can be used for payments.
     *
     * @return bool
     */
    public function isReadyToUse()
    {
        return $this->getMask() !== null &&
               $this->getCardId() !== null &&
               $this->getBrand() !== null;
    }

    /**
     * Return SDK model of saved credit card.
     *
     * @return SdkCreditCard
     */
    public function getSdkObject()
    {
        $object = new SdkCreditCard($this->getId(), $this->getCustomer()->getId());
        $object->setOrderId($this->getOrder()->getId())
               ->setUserId($this->getCustomer()->getId())
               ->setCustomerHash($this->getCustomerHash());
        if ($this->getMask() !== null) {
            $object->setMask($this->getMask());
        }
        if ($this->getBrand() !== null) {
            $object->setBrand($this->getBrand()->getSdkObject());
        }
        if ($this->getCardId() !== null) {
            $object->setCardId($this->getCardId());
        }
        if ($this->getRegisterDate() !== null) {
            $object->setRegisterDate(new \DateTime($this->getRegisterDate()));
        }

        return $object;
    }
}
