<?php

namespace Dotpay\Payment\Controller\Adminhtml\Order;

use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Model\Configuration;
use Dotpay\Loader\Loader;
use Dotpay\Loader\Parser;
use Dotpay\Bootstrap;
use Dotpay\Tool\Curl;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;

abstract class RetryPayment extends \Magento\Backend\App\Action {
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
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Dotpay\Payment\Helper\Url Helper for generating urls used in the Dotpay payment plugin
     */
    protected $urlHelper;

    /**
     * @var \Dotpay\Model\Configuration SDK configuration object
     */
    protected $config;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Dotpay\Payment\Helper\Url                 $urlHelper
     * @param \Dotpay\Payment\Helper\Data\Configuration  $configHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper
    ) {
        $this->context = $context;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->objectManager = $context->getObjectManager();
        $this->orderRepository = $orderRepository;
        $this->urlHelper = $urlHelper;
        $this->configHelper = $configHelper;

        parent::__construct($context);

        Loader::load(
            new Parser(Bootstrap::getMainDir().'/di.xml')
        );

        $this->config = Configuration::createFromData($configHelper);
    }

    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initOrder($id)
    {
        try {
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        return $order;
    }
}