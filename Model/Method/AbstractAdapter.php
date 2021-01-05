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

namespace Dotpay\Payment\Model\Method;

use Dotpay\Model\Refund;
use Dotpay\Resource\Seller;
use Dotpay\Tool\Curl;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Adapter as PaymentAdapter;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Gateway\Command\CommandManagerInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Payment\Gateway\Validator\ValidatorPoolInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Dotpay\Model\Configuration;

/**
 * Abstract adapter of all Dotpay payment methods.
 */
class AbstractAdapter extends PaymentAdapter
{
    /**
     * @var \Magento\Payment\Gateway\Config\ValueHandlerPoolInterface Value handler pool
     */
    private $valueHandlerPool;

    /**
     * @var \Magento\Payment\Gateway\Data\PaymentDataObjectFactory Factory of payment data object
     */
    private $paymentDataObjectFactory;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface Module list
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface Scope config
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session Session of the current customer
     */
    protected $customerSession;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Initialize the abstract adapter.
     *
     * @param ManagerInterface          $eventManager
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param PaymentDataObjectFactory  $paymentDataObjectFactory
     * @param string                    $code
     * @param string                      $formBlockType
     * @param string                      $infoBlockType
     * @param ModuleListInterface       $moduleList
     * @param ScopeConfigInterface      $scopeConfig
     * @param Session                   $customerSession
     * @param CommandPoolInterface      $commandPool
     * @param ValidatorPoolInterface    $validatorPool
     * @param CommandManagerInterface   $commandExecutor
     */
    public function __construct(
        ManagerInterface $eventManager,
        ValueHandlerPoolInterface $valueHandlerPool,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        $code,
        $formBlockType,
        $infoBlockType,
        ModuleListInterface $moduleList,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        CommandPoolInterface $commandPool = null,
        ValidatorPoolInterface $validatorPool = null,
        CommandManagerInterface $commandExecutor = null
    ) {
        $this->valueHandlerPool = $valueHandlerPool;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->moduleList = $moduleList;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;

        parent::__construct(
            $eventManager,
            $valueHandlerPool,
            $paymentDataObjectFactory,
            $code,
            $formBlockType,
            $infoBlockType,
            $commandPool,
            $validatorPool,
            $commandExecutor
        );
    }

    /**
     * Return an answer if Dotpay module is active.
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function isMainActive($storeId = null)
    {
        return (bool) $this->getConfiguredMainValue('active', $storeId);
    }

    /**
     * Overwritten method in children taking into account if the module is enabled.
     * Only then specific payment channel can be available.
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->isMainActive($storeId) && parent::isActive($storeId);
    }

    /**
     * Return id of Dotpay seller.
     *
     * @param int/null $storeId Id of the store
     *
     * @return int
     */
    public function getSellerId($storeId = null)
    {
        return $this->getConfiguredMainValue('id', $storeId);
    }

    /**
     * Return pin of Dotpay seller.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getSellerPin($storeId = null)
    {
        return $this->getConfiguredMainValue('pin', $storeId);
    }

    /**
     * Return username of seller from the Dotpay panel.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getSellerUsername($storeId = null)
    {
        return $this->getConfiguredMainValue('username', $storeId);
    }

    /**
     * Return password of seller from the Dotpay panel.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getSellerPassword($storeId = null)
    {
        return $this->getConfiguredMainValue('password', $storeId);
    }

    /**
     * Return an information if test mode is enabled.
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function isTestMode($storeId = null)
    {
        return (bool) $this->getConfiguredMainValue('test', $storeId);
    }


    /**
     * Return an information where the name of the seller is to be retrieved from
     * 
     * available: 
     * storeinfo: Store Information
     * general: General Contact
     * customer: Customer Support
     * empty: Settings from the Dotpay panel [don't send the name]
     * 
     * @return string
     */
    public function storeNameFrom($storeId = null)
    {
        return (string) $this->getConfiguredMainValue('shop_name', $storeId);
    }


    /**
     * Return an information where the email of the seller is to be retrieved from
     * 
     * available: 
     * storeinfo: Store Information
     * general: General Contact
     * customer: Customer Support
     * empty: Settings from the Dotpay panel [don't send the email]
     * 
     * @return string
     */
    public function storeEmailFrom($storeId = null)
    {
        return (string) $this->getConfiguredMainValue('shop_email', $storeId);
    }


    /**
     * Return an information if instruction of completing payments is displayed in the shop site.
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function isInstructionAvailable($storeId = null)
    {
        return (bool) $this->getConfiguredMainValue('instruction', $storeId);
    }

    /**
     * Return an information if Control field with additional information
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function getControlDefault($storeId = null)
    {
        return (bool) $this->getConfiguredMainValue('control_type', $storeId);
    }

  

    /**
     * Return an information if payment channel logo is displayed in checkout page.
     *
     * @param int/null $storeId Id of the store
     *
     * @return boolean
     */
    public function isLogoDisplayed($storeId = null)
    {
        return (bool) $this->getConfiguredMainValue('display_logo', $storeId);
    }

    /**
     * Return url to the page of payment preparing.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getRedirectUrl($storeId = null)
    {
        return $this->getConfiguredMainValue('redirect_url', $storeId);
    }

    /**
     * Return url to the page where instruction of completing payment is displayed.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getInstructionUrl($storeId = null)
    {
        return $this->getConfiguredMainValue('instruction_url', $storeId);
    }

    /**
     * Return url to the "back" page.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getBackUrl($storeId = null)
    {
        return $this->getConfiguredMainValue('back_url', $storeId);
    }

    /**
     * Return url to the place where status of payment is checked.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getStatusUrl($storeId = null)
    {
        return $this->getConfiguredMainValue('status_url', $storeId);
    }

    /**
     * Return url to the page of payment notifications.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getConfirmUrl($storeId = null)
    {
        return $this->getConfiguredMainValue('confirm_url', $storeId);
    }

    /**
     * Return url to the page with managing of saved credit cards.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getOcManageUrl($storeId = null)
    {
        return $this->getConfiguredMainValue('oc_manage_url', $storeId);
    }

    /**
     * Return url to the place where saved credit card are removed.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getOcRemoveUrl($storeId = null)
    {
        return $this->getConfiguredMainValue('oc_remove_url', $storeId);
    }

    /**
     * Return new refund status.
     *
     * @param int/null $storeId Id of the store
     *
     * @return string
     */
    public function getStatusRefundNew($storeId = null)
    {
        return $this->getConfiguredMainValue('status_refund_new', $storeId);
    }

    /**
     * Unifies configured value handling logic.
     *
     * @param string $field
     * @param null   $storeId
     *
     * @return mixed
     */
    private function getConfiguredMainValue($field, $storeId = null)
    {
        $handler = $this->valueHandlerPool->get('main');
        $subject = [
            'field' => $field,
        ];

        if ($this->getInfoInstance()) {
            $subject['payment'] = $this->paymentDataObjectFactory->create($this->getInfoInstance());
        }

        return $handler->handle($subject, $storeId ?: $this->getStore());
    }

    /**
     * Return number of installed Dotpay module version.
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return (string) $this->moduleList->getOne('Dotpay_Payment')['setup_version'];
    }

    /**
     * Return a name of the shop set in configuration.
     *
     * @return string
     */
    protected function getShopName()
    {   
        $From = $this->storeNameFrom();

        if($From == 'storeinfo'){ // for Store Information
            $ShopName = $this->scopeConfig->getValue('general/store_information/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }else if($From == 'general'){ // for General Contact Scope
            $ShopName = $this->scopeConfig->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }else if($From == 'customer'){ // for Customer Support
            $ShopName = $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE); 
        }else{
            $ShopName = "";
        }
        $Shop_name = trim(preg_replace('/[^\p{L}0-9\s\"\/\\:\.\$\+!#\^\?\-_@]/u','',$ShopName));

        if(strlen($Shop_name) > 300) {
            $Shop_name = substr($Shop_name,0,290)." _";
        }  
        
        return  $Shop_name;

    }

    
    /**
     * Return a email of the shop set in configuration.
     *
     * @return string
     */
    protected function getShopEmail() 
    {

        $From = $this->storeEmailFrom();

        if($From == 'general'){ // for General Contact Scope
            $ShopEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }else if($From == 'customer'){ // for Customer Support
            $ShopEmail = $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE); 
        }else{
            $ShopEmail = "";
        }

        $Shop_email = trim(preg_replace('/[^\p{L}0-9\s\"\/\\:\.\$\+!#\^\?\-_@]/u','',$ShopEmail));

       if(strlen($Shop_email) > 100) {
            $Shop_email = "";
        }  

        return $Shop_email;
    }

    /**
     * Return SDK configuration object with general information.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        $config = new Configuration(DOTPAY_MODNAME);
        $config->setEnable($this->isActive())
               ->setId($this->getSellerId())
               ->setPin($this->getSellerPin())
               ->setUsername($this->getSellerUsername())
               ->setPassword($this->getSellerPassword())
               ->setTestMode($this->isTestMode())
               ->setInstructionVisible($this->isInstructionAvailable())
               ->setControlDefault($this->getControlDefault())
               ->setShopName($this->getShopName())
               ->setStoreName($this->storeNameFrom())
               ->setStoreName($this->storeEmailFrom())
               ->setShopEmail($this->getShopEmail());

        return $config;
    }

    public function canRefund()
    {
        return true;
    }

    public function canRefundPartialPerInvoice()
    {
        return true;
    }

    public function refund(InfoInterface $payment, $amount)
    {

        parent::refund($payment, $amount);

        $seller = new Seller($this->getConfiguration(), new Curl());
        $order = $payment->getOrder();
        $refund = new Refund(
            $payment->getLastTransId(),
            $amount,
            $order->getEntityId(),
            _("Refund for order no ") . $order->getRealOrderId()
        );
        $seller->makeRefund($refund);

        return $this;
    }
}
