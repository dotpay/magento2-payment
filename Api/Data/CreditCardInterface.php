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
 * @copyright PayPro S.A.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace Dotpay\Payment\Api\Data;

/**
 * Interface of credit card model.
 */
interface CreditCardInterface
{
    /**
     * Column name of credit card id in database.
     */
    const CARD_ID = 'entity_id';

    /**
     * Column name of order id in database.
     */
    const ORDER_ID = 'order_id';

    /**
     * Column name of customer id in database.
     */
    const CUSTOMER_ID = 'customer_id';

    /**
     * Column name of brand id in database.
     */
    const BRAND_ID = 'brand_id';

    /**
     * Column name of credit card masked number in database.
     */
    const MASK = 'mask';

    /**
     * Column name of customer hash of credit card in database.
     */
    const CUSTOMER_HASH = 'customer_hash';

    /**
     * Column name of credit card id given from Dotpay in database.
     */
    const CC_ID = 'card_id';

    /**
     * Column name of register date in database.
     */
    const REGISTER_DATE = 'register_date';

    /**
     * Return id of credit card.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id of credit card.
     *
     * @param int $id id of credit card
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Return object of the first order paid using the credit card.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder();

    /**
     * Set object of the first order paid using the credit card.
     *
     * @param \Magento\Sales\Model\Order $order Order object
     *
     * @return $this
     */
    public function setOrder(\Magento\Sales\Model\Order $order);

    /**
     * Return object of customer who is owner of the credit card.
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer();

    /**
     * Set object of customer who is owner of the credit card.
     *
     * @param \Magento\Customer\Model\Customer $customer Cusomer object
     *
     * @return $this
     */
    public function setCustomer(\Magento\Customer\Model\Customer $customer);

    /**
     * Return object of brand of the credit card.
     *
     * @return \Dotpay\Payment\Model\CardBrand
     */
    public function getBrand();

    /**
     * Set object of brand of the credit card.
     *
     * @param \Dotpay\Payment\Model\CardBrand $brand Card brand object
     *
     * @return $this
     */
    public function setBrand(\Dotpay\Payment\Model\CardBrand $brand);

    /**
     * Return masked number.
     *
     * @return string
     */
    public function getMask();

    /**
     * Set masked number.
     *
     * @param string $mask Masked number
     *
     * @return $this
     */
    public function setMask($mask);

    /**
     * Return customer hash of credit card.
     *
     * @return string
     */
    public function getCustomerHash();

    /**
     * Set customer hash of credit card.
     *
     * @param string $hash Customer hash
     *
     * @return $this
     */
    public function setCustomerHash($hash);

    /**
     * Return card id given from Dotpay.
     *
     * @return string
     */
    public function getCardId();

    /**
     * Set card id given from Dotpay.
     *
     * @param type $ccId Card id given from Dotpay
     *
     * @return $this
     */
    public function setCardId($ccId);

    /**
     * Return date of credit card registration.
     *
     * @return \DateTime
     */
    public function getRegisterDate();

    /**
     * Set date of credit card registration.
     *
     * @param \DateTime $registerDate Date of credit card registration
     *
     * @return $this
     */
    public function setRegisterDate(\DateTime $registerDate);
}
