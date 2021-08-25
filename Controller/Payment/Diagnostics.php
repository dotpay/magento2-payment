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

use Dotpay\Processor\Diagnostics as SdkDiagnostics;
use Dotpay\Tool\Curl;
use Dotpay\Resource\Payment as PaymentResource;
use Dotpay\Resource\Seller as SellerResource;
use Dotpay\Payment\Controller\Dotpay;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Controller to display diagnostic info for Dotpay at /dotpay/payment/diagnostics/.
 */
class Diagnostics extends Dotpay implements CsrfAwareActionInterface
{

    /**
     * @var \Dotpay\Payment\Helper\Data\Configuration Provider of Dotpay module configuration data
     */
    private $configHelper;

    /**
     * @var \Dotpay\Payment\Helper\Module Module resource
     */
    private $moduleList;

    /**
     * Initialize the controller.
     *
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\Order                 $orderModel
     * @param \Magento\Customer\Model\Customer           $customerModel
     * @param \Dotpay\Payment\Helper\Url                 $urlHelper
     * @param \Dotpay\Payment\Helper\Data\Configuration  $configHelper
     * @param \Dotpay\Payment\Helper\Module              $moduleList
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Customer\Model\Customer $customerModel,
        \Dotpay\Payment\Helper\Url $urlHelper,
        \Dotpay\Payment\Helper\Data\Configuration $configHelper,
        \Dotpay\Payment\Helper\Module $moduleList
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
        $this->configHelper = $configHelper;
        $this->moduleList = $moduleList;
    }

    /**
     * Execute action of the controller.
     */
    public function execute()
    {
        $curl = new Curl();
        $paymentResource = new PaymentResource($this->config, $curl);
        $sellerResource = new SellerResource($this->config, $curl);
        $diagnostics = new SdkDiagnostics($this->config, $paymentResource, $sellerResource, $this->getModuleVersion());

        if ($diagnostics->execute()) {
            die('OK');
        } else {
            throw new NotFoundException(__("Page not found"));
        }
    }

    /**
     * Break the program and display the given message.
     *
     * @param string $message Messsage to display
     */
    private function breakExecution($message)
    {
        die(__($message));
    }

    /**
     * Return version number of installed Dotpay module.
     *
     * @return string/null
     */
    private function getModuleVersion()
    {
        $moduleInfo = $this->moduleList->getInfo(DOTPAY_NAMESPACE);
        if($moduleInfo !== null) {
            return "magento2-payment v. " . $moduleInfo['version'];
        }
        return null;
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

}
