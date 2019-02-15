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

namespace Dotpay\Payment\Api\Data;

/**
 * Interface of instruction model.
 */
interface InstructionInterface
{
    /**
     * Column name of instruction id in database.
     */
    const INSTRUCTION_ID = 'entity_id';

    /**
     * Column name of order id in database.
     */
    const ORDER_ID = 'order_id';

    /**
     * Column name of payment number in database.
     */
    const NUMBER = 'number';

    /**
     * Column name of recipient's bank account in database.
     */
    const BANK_ACCOUNT = 'bank_account';

    /**
     * Column name of used payment channel id in database.
     */
    const CHANNEL = 'channel';

    /**
     * Column name of instruction's hash in database.
     */
    const HASH = 'hash';

    /**
     * Column name of payment amount in database.
     */
    const AMOUNT = 'amount';

    /**
     * Column name of payment currency in database.
     */
    const CURRENCY = 'currency';

    /**
     * Column name of payment number in database.
     */
    const TITLE = 'title';

    /**
     * Return id of instruction.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id of instruction.
     *
     * @param int $id id of credit card
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Return object of order associated with the instruction.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder();

    /**
     * Set object of order associated with the instruction.
     *
     * @param \Magento\Sales\Model\Order $order Order object
     *
     * @return $this
     */
    public function setOrder(\Magento\Sales\Model\Order $order);

    /**
     * Return number of payment.
     *
     * @return string
     */
    public function getNumber();

    /**
     * Set number of payment.
     *
     * @param string $number Payment number
     *
     * @return $this
     */
    public function setNumber($number);

    /**
     * Return number of bank account if it's set.
     *
     * @return string/null
     */
    public function getBankAccount();

    /**
     * Set number of bank account.
     *
     * @param string $bankAccount Bank account number
     *
     * @return $this
     */
    public function setBankAccount($bankAccount);

    /**
     * Return id of channel used to pay for the order.
     *
     * @return int
     */
    public function getChannel();

    /**
     * Set id of channel used to pay for the order.
     *
     * @param int $channel Channel id
     *
     * @return int
     */
    public function setChannel($channel);

    /**
     * Return hash of the instruction.
     *
     * @return string
     */
    public function getHash();

    /**
     * Set hash of the instruction.
     *
     * @param string $hash Hash of the instruction
     */
    public function setHash($hash);

    /**
     * Return amount of money for payment related to the instruction.
     *
     * @return float
     */
    public function getAmount();

    /**
     * Set amount of money for payment related to the instruction.
     *
     * @param float $amount Amount of money
     *
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Return currency used in payment related to the instruction.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency used in payment related to the instruction.
     *
     * @param string $currency Currency code
     *
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * Return title of the instruction.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title of the instruction.
     *
     * @param string $hash Title of the instruction
     */
    public function setTitle($title);
}
