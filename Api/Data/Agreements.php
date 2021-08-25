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

namespace Dotpay\Payment\Api\Data;

use Dotpay\Exception\Resource\ApiException;
use Dotpay\Exception\Resource\HttpException;
use Dotpay\Resource\Payment;
use Dotpay\Resource\Channel\Request;
use Dotpay\Model\Configuration;
use Dotpay\Tool\Curl;

/**
 * The class allows to get agreements list from Dotpay served based on set of payment params.
 */
class Agreements
{
    /**
     * @var Dotpay\Resource\Channel\Request Request object
     */
    private $request;

    /**
     * @var array/null Agreements list
     */
    private $agreementsData = null;

    /**
     * Initialize object using povided payment data.
     *
     * @param int    $sellerId Seller id
     * @param bool   $testMode Flag if test mode is activated
     * @param float  $amount   Amount of the request
     * @param string $currency Currency code of the request
     * @param string $language Language used by the customer
     */
    public function __construct($sellerId, $testMode, $amount, $currency, $language)
    {
        $this->request = Request::getFromData($sellerId, $testMode, $amount, $currency, $language);
    }

    /**
     * Return array of agreements.
     *
     * @return array
     */
    public function get()
    {
        try {
            if ($this->agreementsData === null) {
                $config = new Configuration(DOTPAY_MODNAME);
                $config->setTestMode($this->request->isTestMode());
                $paymentApi = new Payment($config, new Curl());
                $infoStructure = $paymentApi->getChannelListForRequest($this->request);
                $this->agreementsData = $infoStructure->getUniversalAgreements();
            }
        }
        catch (ApiException $e)
        {
            return [];
        }
        catch (HttpException $e)
        {
            return [];
        }
        return $this->agreementsData;
    }

    /**
     * Return request object.
     *
     * @return Dotpay\Resource\Channel\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
