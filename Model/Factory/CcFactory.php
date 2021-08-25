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

namespace Dotpay\Payment\Model\Factory;

use Dotpay\Model\Seller;
use Dotpay\Channel\Fcc;
use Dotpay\Channel\Cc;

/**
 * Factory which produces SDK channel for credit cards.
 */
class CcFactory extends AbstractFactory
{
    /**
     * Return proper SDK seller object depended on normal or FCC method.
     *
     * @return Seller
     */
    public function getSeller()
    {
        if ($this->isFccAvailable()) {
            return Seller::createFccFromConfiguration($this->paymentAdapter->getConfiguration());
        } else {
            return parent::getSeller();
        }
    }

    /**
     * Return SDK Credit Card/Cc for foreign currencies channel filled by all necessary data.
     *
     * @param array $additionalInformation Additional information about payment
     *
     * @return Cc/Fcc
     */
    public function getChannel($additionalInformation)
    {
        if ($this->isFccAvailable()) {
            $channel = new Fcc($this->paymentAdapter->getConfiguration(),
                          $this->getTransaction(),
                          $this->getPaymentResource(),
                          $this->getSellerResource());
        } else {
            $channel = new Cc($this->paymentAdapter->getConfiguration(),
                          $this->getTransaction(),
                          $this->getPaymentResource(),
                          $this->getSellerResource());
        }
        $channel->setSeller($this->getSeller());

        return $channel;
    }

    /**
     * Check if the method "credit cards for foreign currencies" is available to use.
     *
     * @return bool
     */
    protected function isFccAvailable()
    {
        $config = $this->paymentAdapter->getConfiguration();

        return $config->isFccEnable() && $config->isCurrencyForFcc($this->getCurrency());
    }
}
