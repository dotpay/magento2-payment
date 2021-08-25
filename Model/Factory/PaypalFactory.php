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

use Dotpay\Channel\Paypal;

/**
 * Factory which produces SDK PayPal channel.
 */
class PaypalFactory extends AbstractFactory
{
    /**
     * Return SDK PayPal channel filled by all necessary data.
     *
     * @param array $additionalInformation Additional information about payment
     *
     * @return Paypal
     */
    public function getChannel($additionalInformation)
    {
        $channel = new Paypal($this->paymentAdapter->getConfiguration(),
                          $this->getTransaction(),
                          $this->getPaymentResource(),
                          $this->getSellerResource());
        $channel->setSeller($this->getSeller());

        return $channel;
    }
}
