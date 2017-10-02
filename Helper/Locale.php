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

namespace Dotpay\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;

/**
 * Helper providing data about locality.
 */
class Locale extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Locale\Resolver Magento locale rresolver
     */
    private $localeResolver;

    /**
     * Initialize the helper.
     *
     * @param Context  $context
     * @param Resolver $localeResolver
     */
    public function __construct(Context $context, Resolver $localeResolver)
    {
        parent::__construct($context);
        $this->localeResolver = $localeResolver;
    }

    /**
     * Return locale string used in shop.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Returl language used in shop.
     *
     * @return string
     */
    public function getLanguage()
    {
        return strtolower(\Locale::getPrimaryLanguage($this->getLocale()));
    }
}
