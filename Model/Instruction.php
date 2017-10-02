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
use Dotpay\Payment\Api\Data\InstructionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Dotpay\Provider\InstructionProviderInterface;

/**
 * Model of instruction of completing payments
 */
class Instruction extends AbstractModel implements InstructionProviderInterface, InstructionInterface, IdentityInterface
{
    /**
     * Identifier of cache tag.
     */
    const CACHE_TAG = 'dotpay_instruction';

    /**
     * @var \Magento\Sales\Model\Order Magento order model
     */
    private $order;

    /**
     * Initialize the model.
     *
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Sales\Model\Order                              $order
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->order = $order;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Pseudoconstructor for binding model with its resource.
     */
    protected function _construct()
    {
        $this->_init('Dotpay\Payment\Model\Resource\Instruction');
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
     * Return id of instruction.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::INSTRUCTION_ID);
    }

    /**
     * Set id of instruction.
     *
     * @param int $id id of credit card
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::INSTRUCTION_ID, $id);
    }

    /**
     * Return object of order associated with the instruction.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $orderId = $this->getData(self::ORDER_ID);

        return $this->order->load($orderId);
    }

    /**
     * Set object of order associated with the instruction.
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
     * Return id of order.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    /**
     * Set the given order id.
     *
     * @param int $orderId Order id
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Return number of payment.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->getData(self::NUMBER);
    }

    /**
     * Set number of payment.
     *
     * @param string $number Payment number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        return $this->setData(self::NUMBER, $number);
    }

    /**
     * Return number of bank account if it's set.
     *
     * @return string/null
     */
    public function getBankAccount()
    {
        return $this->getData(self::BANK_ACCOUNT);
    }

    /**
     * Set number of bank account.
     *
     * @param string $bankAccount Bank account number
     *
     * @return $this
     */
    public function setBankAccount($bankAccount)
    {
        return $this->setData(self::BANK_ACCOUNT, $bankAccount);
    }

    /**
     * Return id of channel used to pay for the order.
     *
     * @return int
     */
    public function getChannel()
    {
        return $this->getData(self::CHANNEL);
    }

    /**
     * Set id of channel used to pay for the order.
     *
     * @param int $channel Channel id
     *
     * @return int
     */
    public function setChannel($channel)
    {
        return $this->setData(self::CHANNEL, $channel);
    }

    /**
     * Return hash of the instruction.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->getData(self::HASH);
    }

    /**
     * Set hash of the instruction.
     *
     * @param string $hash Hash of the instruction
     */
    public function setHash($hash)
    {
        return $this->setData(self::HASH, $hash);
    }

    /**
     * Return amount of money for payment related to the instruction.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * Set amount of money for payment related to the instruction.
     *
     * @param float $amount Amount of money
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Return currency used in payment related to the instruction.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * Set currency used in payment related to the instruction.
     *
     * @param string $currency Currency code
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }
}
