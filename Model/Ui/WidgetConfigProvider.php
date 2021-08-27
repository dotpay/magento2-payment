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
 * Config provider for UI side of method using Dotpay widget.
 */
class WidgetConfigProvider extends AbstractConfigProvider
{
    /**
     * Code of Dotpay widget payment method.
     */
    const CODE = 'dotpay_widget';

    /**
     * Return a configuration of Dotpay widget method.
     *
     * @return array
     */
    public function getConfig()
    {
        $baseConfig = parent::getConfig();
        $config = [
            'payment' => [
                self::CODE => [
                    'logoUrl' => $this->getPaymentMethodLogo('dotpay'),
                    'widgetConfig' => $this->getWidgetConfiguration(),
                ],
            ],
        ];
        $config['payment'][self::CODE] = array_merge($config['payment'][self::CODE], $baseConfig);

        return $config;
    }

    /**
     * Return a configuration used by Dotpay widget.
     *
     * @return array
     */
    private function getWidgetConfiguration()
    {
        $request = self::$agreements->getRequest();
        $amount1 = $request->getAmount();

        if($amount1 != '' && $amount1 > 0 ){
				$amount = $amount1;		
		}else{
			    $amount = '307.77';	 //fix for empty request to the dotpay api 
		}

        $widgetConfig = [
            'payment' => [
                'sellerId' => $request->getSellerId(),
                'amount' => $amount,
                'currency' => $request->getCurrency(),
                'lang' => $request->getLanguage(),
            ],
            'request' => [
                'test' => $this->getPaymentAdapter()->isTestMode(),
                'hiddenChannels' => $this->configuration->getEnabledChannels(),
                'widgetVisible' => $this->configuration->getWidgetVisible(),
            ],
        ];

        return $widgetConfig;
    }
}
