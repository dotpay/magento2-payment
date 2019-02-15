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

use Dotpay\Exception\BadParameter\AccountDataException;
use Dotpay\Exception\BadParameter\FccIdException;
use Dotpay\Exception\BadParameter\FccPinException;
use Dotpay\Model\Configuration;
use Dotpay\Resource\Github;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Tool\Curl;
use Dotpay\Exception\BadParameter\IdException;
use Dotpay\Exception\BadParameter\PinException;

/**
 * Block of information displayed in admin configuration of Dotpay payments.
 */
class Information extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Dotpay\Payment\Helper\Module Module resource
     */
    private $moduleList;

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
     * @param \Dotpay\Payment\Helper\Module    $moduleList
     * @param \Dotpay\Payment\Helper\Data\Configuration   $configHelper
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Dotpay\Payment\Helper\Module $moduleList,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleList = $moduleList;
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
        $this->checkId();
        $this->checkFccId();
        $this->checkPin();
        $this->checkFccPin();
        $this->checkAccountData();

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
     * @return string/null
     */
    public function getModuleVersion()
    {
        $moduleInfo = $this->moduleList->getInfo(DOTPAY_NAMESPACE);
        if($moduleInfo !== null) {
            return $moduleInfo['version'];
        }
        return null;
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
        if(!$this->config->isActivated()) {
            return false;
        }
        return $this->getAvailableVersion()->isNewAvailable($this->getModuleVersion());
    }

    /**
     * Check if payment module is activated
     *
     * @return boolean
     */
    public function isActivated()
    {
        return $this->config->isActivated();
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
        if(!$this->getPaymentResource()->checkSeller($this->config->getId()))
        {
            $this->config->addError(new IdException());
        }
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

        if(!$this->getPaymentResource()->checkSeller($this->config->getFccId()))
        {
            $this->config->addError(new FccIdException());
        }
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

        if(!self::$testAccountData)
        {
            $this->config->addError(new AccountDataException());
        }
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

        if(!$this->getSellerResource()->checkPin())
        {
            $this->config->addError(new PinException());
        }
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

        if($this->getSellerResource()->checkFccPin())
        {
            $this->config->addError(new FccPinException());
        }
    }

    /**
     * Check if seller id from configuration is correct.
     *
     * @return array|bool
     */
    public function checkErrors()
    {
        if (!$this->config->getErrors()) {
            return false;
        }

        return $this->config->getErrors();
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
