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

namespace Dotpay\Payment\Controller;

use Dotpay\Model\Configuration;
use Dotpay\Loader\Loader;
use Dotpay\Loader\Parser;
use Dotpay\Bootstrap;

/**
 * Main Dotpay controller which provides common fuctionality.
 */
abstract class Dotpay extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Action\Context Application context
     */
    protected $context;

    /**
     * @var \Magento\Customer\Model\Session Customer session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session Checkout session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory Magento order factory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Locale\Resolver Locale resolver
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Registry Magento registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory Factory of result page
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface Object manager
     */
    protected $objectManager;

    /**
     * @var \Dotpay\Payment\Helper\Url Helper for generating urls used in the Dotpay payment plugin
     */
    protected $urlHelper;

    /**
     * @var Dotpay\Model\Configuration SDK configuration object
     */
    protected $config;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dotpay\Payment\Helper\Url                 $urlHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper
    ) {
        $this->context = $context;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->objectManager = $context->getObjectManager();
        $this->urlHelper = $urlHelper;

        parent::__construct($context);

        Loader::load(
            new Parser(Bootstrap::getMainDir().'/di.xml')
        );

        $this->config = Configuration::createFromData($configHelper);
    }

    /**
     * Return a Dotpay payment method's factory according to the given name.
     *
     * @param string $method Dotpay payment method used to pay for the order
     *
     * @return \Dotpay\Payment\Model\Factory\AbstractFactory
     */
    protected function getFactory($method)
    {
        switch ($method) {
            case 'dotpay_oc':
                return $this->objectManager->get('DotpayOcFactory');
            case 'dotpay_cc':
                return $this->objectManager->get('DotpayCcFactory');
            case 'dotpay_mp':
                return $this->objectManager->get('DotpayMpFactory');
            case 'dotpay_blik':
                return $this->objectManager->get('DotpayBlikFactory');
            case 'dotpay_paypal':
                return $this->objectManager->get('DotpayPaypalFactory');
            case 'dotpay_widget':
                return $this->objectManager->get('DotpayWidgetFactory');
            case 'dotpay_other':
                return $this->objectManager->get('DotpayOtherFactory');
            default:
                die(__('Unrecognized payment method. Please, try again.'));
        }
    }
}
