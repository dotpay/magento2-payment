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

namespace Dotpay\Payment\Controller\Payment;

use Dotpay\Payment\Controller\Dotpay;
use Dotpay\Resource\Channel\Request;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Model\Configuration;
use Dotpay\Model\Instruction as SdkInstruction;

/**
 * Controller of displaying instruction of completing payment.
 */
class Instruction extends Dotpay
{
    /**
     * @var \Magento\Framework\App\RequestInterface Request object
     */
    protected $request;

    /**
     * @var \Dotpay\Payment\Helper\Url Helper of supporting management of locale used by customer
     */
    protected $localeHelper;

    /**
     * @var Dotpay\Model\Configuration SDK configuration object
     */
    protected $config;

    /**
     * @var \Dotpay\Payment\Model\Instruction Model of payment instruction
     */
    private $instructionModel;

    /**
     * @var \Magento\Sales\Model\Order Model of order
     */
    private $orderModel;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dotpay\Payment\Helper\Url                 $urlHelper
     * @param \Dotpay\Payment\Model\Instruction          $instructionModel
     * @param \Magento\Sales\Model\Order                 $orderModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Dotpay\Payment\Helper\Locale $localeHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        \Dotpay\Payment\Model\Instruction $instructionModel,
        \Magento\Sales\Model\Order $orderModel
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $coreRegistry,
            $resultPageFactory,
            $urlHelper,
            $configHelper
        );
        $this->request = $context->getRequest();
        $this->localeHelper = $localeHelper;
        $this->config = Configuration::createFromData($configHelper);
        $this->instructionModel = $instructionModel;
        $this->orderModel = $orderModel;
    }

    /**
     * Execute action of the controller.
     */
    public function execute()
    {
        $pageData = [];
        $dbInstruction = $this->instructionModel->load($this->request->getParam('orderId'), 'order_id');
        if ($dbInstruction->getId() !== null && $this->checkVisitor($dbInstruction)){
            $instruction = SdkInstruction::createFromData($dbInstruction);
            $pageData['instruction'] = $instruction;
            if ($instruction->getIsCash()) {
                $pageData['target_url'] = $instruction->getPdfUrl($this->config);
            } else {
                $pageData['target_url'] = $instruction->getBankPage($this->config);
            }
            $channelInfo = $this->getChannelInfo(
                $instruction->getAmount(),
                $instruction->getCurrency(),
                $instruction->getChannel()
            );
            $pageData['channel_logo'] = $channelInfo->getLogo();
        } else {
            $pageData['error_message'] = __('Instruction of payment completion for the order doesn\'t exist');
        }
        $this->coreRegistry->register('data', $pageData);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * Return an object with information about the payment channel.
     *
     * @param float  $amount    Amount of money
     * @param string $currency  Currency code
     * @param int    $channelId Channel id
     *
     * @return \Dotpay\Resource\Channel\OneChannel
     */
    private function getChannelInfo($amount, $currency, $channelId)
    {
        $request = Request::getFromData(
                $this->config->getId(),
                $this->config->getTestMode(),
                $amount,
                $currency,
                $this->localeHelper->getLanguage()
            );
        $paymentResource = new PaymentResource($this->config, new \Dotpay\Tool\Curl());
        $information = $paymentResource->getChannelListForRequest($request);

        return $information->getChannelInfo($channelId);
    }

    /**
     * Check if the visitor has access to a specific instruction
     *
     * @param \Dotpay\Payment\Model\Instruction  $dbInstruction    Instruction object from database
     *
     * @return bool
     */
    private function checkVisitor($dbInstruction)
    {
        $cookie = $this->objectManager->get('Dotpay\Payment\Cookie\Instruction');
        $order = $this->orderModel->load($dbInstruction->getOrderId());
        $hash = $cookie->get($order->getEntityId());

        if($hash && $hash == $dbInstruction->getHash())
        {
            return true;
        }

        if($this->customerSession->getCustomer() && $order->getCustomerId() && $this->customerSession->getCustomer()->getEntityId() == $order->getCustomerId())
        {
            return true;
        }

        return false;
    }
}
