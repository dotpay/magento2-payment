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

use Dotpay\Model\Payer;
use Dotpay\Provider\PaymentLinkProviderInterface;

/**
 * Provider of payment link data.
 */
class PaymentLink implements PaymentLinkProviderInterface
{

    /**
     * @var \Magento\Sales\Model\Order $order Magento order object
     */
    private $order = null;

    /**
     * Initialize the provider.
     *
     * @param \Magento\Sales\Model\Order $order Magento order object
     */
    public function __construct(
        \Magento\Sales\Model\Order $order
    ) {
        $this->order = $order;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return round($this->order->getGrandTotal(), 2);
    }

    /**
     * @inheritDoc
     */
    public function getCurrency()
    {
        return $this->order->getOrderCurrencyCode();
    }


    /**
     * Parsing domain from a URL
     */
    public function getHost($url) { 
        $parseUrl = parse_url(trim($url)); 
        if(isset($parseUrl['host']))
        {
            $host = $parseUrl['host'];
        }
        else
        {
             $path = explode('/', $parseUrl['path']);
             $host = $path[0];
        }
        return trim($host); 
     }


    /**
     * @inheritDoc
     */
    public function getControl($full = null)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->MagentoUrl =  $this->getHost($storeManager->getStore()->getBaseUrl());

        $newControl = 'tr_id:#'.$this->order->getEntityId().'|domain:'.$this->MagentoUrl.'|Magento DP module: v'.\Dotpay\Channel\Channel::DOTPAY_PLUGIN_VERSION.'|Link';

       if($full != null){
            $control = $newControl;
        }else{
            $control = $this->order->getEntityId();
        }
       return $control;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return __('Order ID: %1', $this->order->getRealOrderId())."/".$this->getControl()."/".__('RE');
    }
    


    /**
     * @inheritDoc
     */
    public function getOrderIDforURL()
    {
        $idcontrol1 = base64_encode('#'.$this->getControl().'#'.$this->order->getRealOrderId().'#'.time()); 
        
        // simple trick to obstruct direct decoding this string from url:
        $idcontrol1  = str_replace('=','',$idcontrol1); 
        $rand = sha1(rand());
        $idcontrol2 = substr($idcontrol1, 0, 8).substr($rand, 13, 6).substr($idcontrol1, 8, strlen($idcontrol1));
        $idcontrol = "Ma:".substr($rand, 4, 3).$idcontrol2.substr(sha1(rand()), 10, 4).":RE";

        return $idcontrol;
    }


    
    /**
     * @inheritDoc
     */
    public function getPayer()
    {
        return new Customer($this->order);
    }

    /**
     * @inheritDoc
     */
    public function getIgnoreLastPaymentChannel()
    {
        return 1;
    }

}