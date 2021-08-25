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

namespace Dotpay\Payment\Model\Ui;

/**
 * Config provider for UI side of credit card method.
 */
class CcConfigProvider extends AbstractConfigProvider
{
    /**
     * Code of credit card payment method.
     */
    const CODE = 'dotpay_cc';

    /**
     * Return a configuration of credit card payment method.
     *
     * @return array
     */
    public function getConfig()
    {
        $baseConfig = parent::getConfig();
        $config = [
            'payment' => [
                self::CODE => [
                    'logoUrl' => $this->getPaymentMethodLogo('cards'),
                ],
            ],
        ];
        $config['payment'][self::CODE] = array_merge($config['payment'][self::CODE], $baseConfig);

        return $config;
    }
}
