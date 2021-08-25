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

namespace Dotpay\Payment\Helper\Data;

use Dotpay\Model\SdkCreditCard;
use Dotpay\Model\CardBrand;
use Dotpay\Provider\CreditCardProviderInterface;

/**
 * Provider of notification when payment is confirming by Dotpay.
 */
class CreditCard implements CreditCardProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface Magento request object
     */
    private $request;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Framework\App\RequestInterface $request   Magento request object
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Return a CardBrand object with details of a credit card brand.
     *
     * @return \Dotpay\Model\CardBrand
     */
    public function getBrand()
    {
        return new CardBrand($this->request->getParam('credit_card_brand_code'), null, $this->request->getParam('credit_card_brand_codename'));
    }

    /**
     * Return id of credit card issuer.
     *
     * @return string
     */
    public function getIssuerId()
    {
        return $this->request->getParam('credit_card_issuer_identification_number');
    }

    /**
     * Return a masked number of the credt card.
     *
     * @return string
     */
    public function getMask()
    {
        return $this->request->getParam('credit_card_masked_number');
    }

    /**
     * Return a unique identifier of the credit card.
     *
     * @return int
     */
    public function getUniqueId()
    {
        return $this->request->getParam('credit_card_unique_identifier');
    }

    /**
     * Return an identificator of credit card which is assigned by Dotpay system.
     *
     * @return string
     */
    public function getCardId()
    {
        return $this->request->getParam('credit_card_id');
    }

    /**
     * Return credit card's expiration year
     *
     * @return string
     */
    public function getExpirationYear()
    {
        return $this->request->getParam('credit_card_expiration_year');
    }

    /**
     * Return credit card's expiration month
     *
     * @return string
     */
    public function getExpirationMonth()
    {
        return $this->request->getParam('credit_card_expiration_month');
    }

}
