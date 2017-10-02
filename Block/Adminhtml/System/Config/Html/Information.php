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

namespace Dotpay\Payment\Block\Adminhtml\System\Config\Html;

use Dotpay\Model\Configuration;
use Dotpay\Resource\Github;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Tool\Curl;

/**
 * Block of information displayed in admin configuration of Dotpay payments.
 */
class Information extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Magento\Framework\Module\ResourceInterface Module resource
     */
    private $moduleResource;

    /**
     * @var \Dotpay\Model\Configuration SDK configuration object
     */
    private $config;

    /**
     * @var \Dotpay\Resource\Github\Version/null SDK Github version of payment module
     */
    private static $ghVersion = null;

    /**
     * @var \Dotpay\Resource\Payment/null SDK payment resource
     */
    private static $paymentResource = null;

    /**
     * @var \Dotpay\Resource\Seller/null SDK seller resource
     */
    private static $sellerResource = null;

    /**
     * @var boolean/null Flag if seller account data is correct
     */
    private static $testAccountData = null;

    /**
     * @var string Location of template file
     */
    protected $_template = 'Dotpay_Payment::system/config/information.phtml';

    /**
     * Initialization of block.
     *
     * @param \Magento\Backend\Block\Template\Context     $context
     * @param \Magento\Framework\Module\ResourceInterface $moduleResource
     * @param \Dotpay\Payment\Helper\Data\Configuration   $configHelper
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleResource = $moduleResource;
        $this->config = Configuration::createFromData($configHelper);
    }

    /**
     * Render fieldset html.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Rendered element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->toHtml();
    }

    /**
     * Check if register information can be displayed. This is done only when test mode is set on.
     *
     * @return bool
     */
    public function isRegisterDisplayed()
    {
        return $this->config->getTestMode();
    }

    /**
     * Return version number of installed Dotpay module.
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->moduleResource->getDbVersion('DOTPAY_NAMESPACE');
    }

    /**
     * Return version of the payment module from Github.
     *
     * @return \Dotpay\Resource\Github\Version
     */
    public function getAvailableVersion()
    {
        if (self::$ghVersion === null) {
            $ghResource = new Github($this->config, new Curl());
            self::$ghVersion = $ghResource->getLatestProjectVersion('dotpay', DOTPAY_MODNAME);
        }

        return self::$ghVersion;
    }

    /**
     * Return an information if newer version is available on Github.
     *
     * @return bool
     */
    public function isNewerVersion()
    {
        return $this->getAvailableVersion()->isNewAvailable($this->getModuleVersion());
    }

    /**
     * Check if seller id from configuration is correct.
     *
     * @return bool
     */
    public function checkId()
    {
        if (!$this->config->getEnable()) {
            return true;
        }

        return $this->getPaymentResource()->checkSeller($this->config->getId());
    }

    /**
     * Check if seller id for foreign currencies from configuration is correct.
     *
     * @return bool
     */
    public function checkFccId()
    {
        if (!$this->config->getEnable() || !($this->config->getCcVisible() && $this->config->getFccVisible())) {
            return true;
        }

        return $this->getPaymentResource()->checkSeller($this->config->getFccId());
    }

    /**
     * Check if seller account data from configuration is correct.
     *
     * @return bool
     */
    public function checkAccountData()
    {
        if (!($this->config->getEnable() && $this->config->isGoodApiData())) {
            return true;
        }
        if (self::$testAccountData === null) {
            self::$testAccountData = $this->getSellerResource()->isAccountRight();
        }

        return self::$testAccountData;
    }

    /**
     * Check if seller pin from configuration is correct.
     *
     * @return bool
     */
    public function checkPin()
    {
        if (!($this->config->getEnable() && $this->config->isGoodApiData())) {
            return true;
        }
        if (!($this->checkId() && $this->checkAccountData())) {
            return true;
        }

        return $this->getSellerResource()->checkPin();
    }

    /**
     * Check if seller pin for foreign currencies from configuration is correct.
     *
     * @return bool
     */
    public function checkFccPin()
    {
        if (!($this->config->getEnable() && $this->config->getCcVisible() && $this->config->getFccVisible() && $this->config->isGoodApiData())) {
            return true;
        }
        if (!($this->checkFccId() && $this->checkAccountData())) {
            return true;
        }

        return $this->getSellerResource()->checkFccPin();
    }

    /**
     * Return SDK's payment resource.
     *
     * @return \Dotpay\Resource\Payment
     */
    private function getPaymentResource()
    {
        if (self::$paymentResource === null) {
            self::$paymentResource = new PaymentResource($this->config, new Curl());
        }

        return self::$paymentResource;
    }

    /**
     * Return SDK's seller resource.
     *
     * @return \Dotpay\Resource\Seller
     */
    private function getSellerResource()
    {
        if (self::$sellerResource === null) {
            self::$sellerResource = new SellerResource($this->config, new Curl());
        }

        return self::$sellerResource;
    }
}
