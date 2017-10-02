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

namespace Dotpay\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Dotpay\Payment\Api\Data\Agreements;
use Dotpay\Locale\Adapter\Csv;
use Dotpay\Model\Configuration;

/**
 * Abstract config provider with general functionality for UI side of other Dotpay payment methods.
 */
abstract class AbstractConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\View\Asset\Repository Asset repository
     */
    private $assetRepository;

    /**
     * @var \Dotpay\Payment\Model\Method\AbstractAdapter Adapter of concrete payment method
     */
    private $paymentAdapter;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface Store manager
     */
    private $storeManager;

    /**
     * @var \Magento\Checkout\Model\Cart Object of Magento cart
     */
    protected $cart;

    /**
     * @var \Dotpay\Payment\Helper\Locale Helper providing data about locality
     */
    protected $localeHelper;

    /**
     * @var \Dotpay\Payment\Helper\Data\Configuration Provider of Dotpay module's configuration data
     */
    protected $configuration;

    /**
     * @var \Dotpay\Payment\Api\Data\Agreements Object of service providing agreements data from Dotpay server according to payment details
     */
    protected static $agreements = null;

    /**
     * Initialize the abstract configuration provider for UI part of Dotpay payment methods.
     *
     * @param \Magento\Framework\View\Asset\Repository     $assetRepository
     * @param \Dotpay\Payment\Model\Method\AbstractAdapter $adapter
     * @param \Magento\Store\Model\StoreManagerInterface   $storeManager
     * @param \Magento\Checkout\Model\Cart                 $cart
     * @param \Dotpay\Payment\Helper\Locale                $localeHelper
     * @param \Dotpay\Payment\Helper\Data\Configuration    $configuration
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Dotpay\Payment\Model\Method\AbstractAdapter $adapter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Cart $cart,
        \Dotpay\Payment\Helper\Locale $localeHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configuration
    ) {
        $this->assetRepository = $assetRepository;
        $this->paymentAdapter = $adapter;
        $this->storeManager = $storeManager;
        $this->cart = $cart;
        $this->localeHelper = $localeHelper;
        $this->configuration = Configuration::createFromData($configuration);
        Csv::setDefaultLocale($this->localeHelper->getLocale());
    }

    /**
     * Return a configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        $baseConfig = [
            'displayLogo' => $this->isLogoDisplayed(),
            'agreements' => $this->getAgreements(),
            'redirectUrl' => $this->paymentAdapter->getRedirectUrl(),
        ];

        return $baseConfig;
    }

    /**
     * Return a path to logo of concrete payment method.
     *
     * @param string $logoCode Code of concrete logo
     *
     * @return string
     */
    final protected function getPaymentMethodLogo($logoCode)
    {
        return $this->assetRepository->getUrlWithParams('Dotpay_Payment::images/'.$logoCode.'.png', []);
    }

    /**
     * Return an information if payment method's logo can be displayed.
     *
     * @return bool
     */
    final protected function isLogoDisplayed()
    {
        return $this->paymentAdapter->isLogoDisplayed($this->storeManager->getStore()->getId());
    }

    /**
     * Return an adapter of concrete payment method.
     *
     * @return AbstractAdapter
     */
    final protected function getPaymentAdapter()
    {
        return $this->paymentAdapter;
    }

    /**
     * Return an object which helps to get agreement data from Dotpay server.
     *
     * @return Agreements
     */
    protected function getAgreements()
    {
        $agreements = [];
        if (self::$agreements === null) {
            $cartData = $this->cart->getQuote()->getData();
            self::$agreements = new Agreements($this->getPaymentAdapter()->getSellerId(),
                                               $this->getPaymentAdapter()->isTestMode(),
                                               $cartData['grand_total'],
                                               $cartData['quote_currency_code'],
                                               $this->localeHelper->getLanguage());
        }
        foreach (self::$agreements->get() as $agreement) {
            $agreements[] = $agreement->getData();
        }

        return $agreements;
    }
}
