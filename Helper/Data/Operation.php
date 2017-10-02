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

use Dotpay\Provider\OperationProviderInterface;

/**
 * Provider of operation data when payment is confirming by Dotpay.
 */
class Operation implements OperationProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface Magento request object
     */
    private $request;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Framework\App\RequestInterface $request Magento request object
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Return an account id of a seller.
     *
     * @return int|null
     */
    public function getAccountId()
    {
        return $this->request->getParam('id');
    }

    /**
     * Return a number of the operation.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->request->getParam('operation_number');
    }

    /**
     * Return an identifier of a type of the operation.
     *
     * @return string
     */
    public function getType()
    {
        return $this->request->getParam('operation_type');
    }

    /**
     * Return a status identifier of the operation.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->request->getParam('operation_status');
    }

    /**
     * Return a transaction amount.
     *
     * @return float|null
     */
    public function getAmount()
    {
        return $this->request->getParam('operation_amount');
    }

    /**
     * Return a code of a transaction currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->request->getParam('operation_currency');
    }

    /**
     * Return a withdrawal amount.
     *
     * @return float|null
     */
    public function getWithdrawalAmount()
    {
        return $this->request->getParam('operation_withdrawal_amount');
    }

    /**
     * Return an amount of a Dotpay commission.
     * It's presented as a negative amount.
     *
     * @return float|null
     */
    public function getCommissionAmount()
    {
        return $this->request->getParam('operation_commission_amount');
    }

    /**
     * Return a flag if operation is marked as completed in Seller panel.
     *
     * @return bool
     */
    public function getCompleted()
    {
        return $this->request->getParam('is_completed');
    }

    /**
     * Return an original amount which was sent from a shop.
     *
     * @return float|null
     */
    public function getOriginalAmount()
    {
        return $this->request->getParam('operation_original_amount');
    }

    /**
     * Return a code of an original currency which was sent from a shop.
     *
     * @return string
     */
    public function getOriginalCurrency()
    {
        return $this->request->getParam('operation_original_currency');
    }

    /**
     * Return a DateTime object with date and a time of the last change status of the operation.
     *
     * @return DateTime|null
     */
    public function getDateTime()
    {
        return new \DateTime($this->request->getParam('operation_datetime'));
    }

    /**
     * Return a number of an operation which is related to the operation.
     *
     * @return string
     */
    public function getRelatedNumber()
    {
        return $this->request->getParam('operation_related_number');
    }

    /**
     * Return a value which was given during making a payment.
     *
     * @return mixed
     */
    public function getControl()
    {
        return $this->request->getParam('control');
    }

    /**
     * Return a description of the operation.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->request->getParam('description');
    }

    /**
     * Return an email of payer.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->request->getParam('email');
    }
}
