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

use Dotpay\Channel\Oc;
use Dotpay\Payment\Api\Data\CreditCardInterface;

/**
 * Config provider for UI side of credit cards using One Click method.
 */
class OcConfigProvider extends AbstractConfigProvider
{
    /**
     * Code of credit cards using One Click payment method.
     */
    const CODE = 'dotpay_oc';

    /**
     * @var \Dotpay\Payment\Model\CreditCardFactory Factory of saved credit cards' models
     */
    private $ccCollectionFactory;

    /**
     * Initialize the configuration provider for UI part of One Click payment method.
     *
     * @param \Magento\Framework\View\Asset\Repository                    $assetRepository
     * @param \Dotpay\Payment\Model\Method\AbstractAdapter                $adapter
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager
     * @param \Magento\Checkout\Model\Cart                                $cart
     * @param \Dotpay\Payment\Helper\Locale                               $localeHelper
     * @param \Dotpay\Payment\Model\Resource\CreditCard\CollectionFactory $ccCollectionFactory
     * @param \Dotpay\Payment\Helper\Data\Configuration                   $configuration
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Dotpay\Payment\Model\Method\AbstractAdapter $adapter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Cart $cart,
        \Dotpay\Payment\Helper\Locale $localeHelper,
        \Dotpay\Payment\Model\Resource\CreditCard\CollectionFactory $ccCollectionFactory,
        \Dotpay\Payment\Helper\Data\Configuration $configuration
    ) {
        $this->ccCollectionFactory = $ccCollectionFactory;
        parent::__construct(
            $assetRepository,
            $adapter,
            $storeManager,
            $cart,
            $localeHelper,
            $configuration
        );
    }

    /**
     * Return a configuration of One Click payment method.
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
                    'cards' => $this->getSavedCards(),
                ],
            ],
        ];
        $config['payment'][self::CODE] = array_merge($config['payment'][self::CODE], $baseConfig);

        return $config;
    }

    /**
     * Return list of saved credit cards for current customer.
     *
     * @return array
     */
    private function getSavedCards()
    {
        $customerId = $this->cart->getCustomerSession()->getCustomer()->getId();
        $savedDbCards = $this->ccCollectionFactory
                ->create()
                ->addFilter(CreditCardInterface::CUSTOMER_ID, $customerId);
        $cards = [];
        foreach ($savedDbCards as $card) {
            if (($brand = $card->getBrand()) !== null) {
                $cards[] = [
                    'id' => $card->getId(),
                    'mask' => $card->getMask(),
                    'brand_name' => $brand->getName(),
                    'logo' => $brand->getLogo(),
                ];
            }
        }

        return $cards;
    }

    /**
     * Return a list of all agreements containing the specific one for One Click.
     *
     * @return array
     */
    protected function getAgreements()
    {
        $agreements = parent::getAgreements();
        foreach (Oc::getSpecialAgreements() as $agreement) {
            $agreements[] = $agreement->getData();
        }

        return $agreements;
    }
}
